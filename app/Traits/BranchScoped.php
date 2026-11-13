<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait BranchScoped
 *
 * Provides multi-tenant data isolation and role-based query scoping across the application.
 *
 * @package App\Traits
 * @author Renatus Bernardo <bernadorenatus32@gmail.com>
 *
 * ARCHITECTURAL OVERVIEW FOR FUTURE DEVELOPERS:
 * 1. Global Scope Application:
 *    When this trait is attached to an Eloquent model, it registers a global query scope named 'branch'.
 *    Every Eloquent query (e.g. Model::all(), Model::where(), relations) automatically has branch filters appended.
 *
 * 2. Role-Based Visibility Rules:
 *    - 'system-admin': Bypasses all scoping. Has unrestricted national/global visibility across all branches.
 *    - 'branch-admin' & 'tutor': Constrained strictly to records belonging to their active branch (via session 'user_branch_id').
 *    - 'student': Constrained strictly to records belonging to their own user account (via user_id or applicant.user_id).
 *
 * 3. Relationship Traversal Hierarchy:
 *    If the model table has a direct 'branch_id', that column is filtered directly.
 *    Otherwise, the scope traverses relationships in order:
 *      applicant_id -> applicant.branch_id
 *      enrollment_id -> enrollment.applicant.branch_id
 *      invoice_id -> invoice.applicant.branch_id
 *      venue_id -> venue.branch_id
 *
 * 4. How to Extend / Add to New Models:
 *    Simply add `use \App\Traits\BranchScoped;` to any Eloquent model that represents branch-segregated data.
 *    Ensure the model defines the necessary relationship (e.g. applicant(), enrollment(), or branch_id).
 *
 * 5. Bypassing Scope in Background Jobs / CLI:
 *    To query all branches intentionally (e.g., in scheduled cron tasks, reports, or console commands):
 *    Use `Model::withoutGlobalScope('branch')->get();`
 */
trait BranchScoped
{
    /**
     * Boot the trait and apply the global branch/user data isolation scope.
     *
     * @return void
     */
    protected static function bootBranchScoped()
    {
        // Automatically apply branch scope when querying
        static::addGlobalScope('branch', function (Builder $builder) {
            $user = Auth::user();

            if (!$user) {
                return;
            }

            // System admin, Director, and Marketing Officer have cross-branch visibility
            if ($user->hasRole(['system-admin', 'director', 'marketing-officer'])) {
                return;
            }

            $branchId = session('user_branch_id');

            // Branch admin, training coordinator, and tutors see only their branch data
            if ($user->hasRole(['branch-admin', 'training-coordinator', 'tutor'])) {
                if (!$branchId) {
                    return;
                }

                $table = (new static)->getTable();

                // Direct branch_id column
                if (self::hasColumn($table, 'branch_id')) {
                    $builder->where("{$table}.branch_id", $branchId);
                    return;
                }

                // Filter through applicant relationship
                if (self::hasColumn($table, 'applicant_id')) {
                    $builder->whereHas('applicant', function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    });
                    return;
                }

                // Filter through enrollment relationship
                if (self::hasColumn($table, 'enrollment_id')) {
                    $builder->whereHas('enrollment.applicant', function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    });
                    return;
                }

                // Filter through invoice relationship
                if (self::hasColumn($table, 'invoice_id')) {
                    $builder->whereHas('invoice.applicant', function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    });
                    return;
                }

                // Filter through venue relationship
                if (self::hasColumn($table, 'venue_id')) {
                    $builder->whereHas('venue', function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    });
                    return;
                }
            }

            // Students see only their own data
            if ($user->hasRole('student')) {
                $table = (new static)->getTable();

                // Direct user_id column
                if (self::hasColumn($table, 'user_id')) {
                    $builder->where("{$table}.user_id", $user->id);
                    return;
                }

                // Filter through applicant relationship
                if (self::hasColumn($table, 'applicant_id')) {
                    $builder->whereHas('applicant', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                    return;
                }

                // Filter through enrollment relationship
                if (self::hasColumn($table, 'enrollment_id')) {
                    $builder->whereHas('enrollment.applicant', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                    return;
                }

                // Filter through invoice relationship
                if (self::hasColumn($table, 'invoice_id')) {
                    $builder->whereHas('invoice.applicant', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                    return;
                }
            }
        });
    }

    /**
     * Check if table has column
     */
    private static function hasColumn($table, $column)
    {
        try {
            return \Schema::hasColumn($table, $column);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Scope a query to a specific branch
     */
    public function scopeForBranch(Builder $query, $branchId)
    {
        $table = $this->getTable();

        if (self::hasColumn($table, 'branch_id')) {
            return $query->where('branch_id', $branchId);
        }

        if (self::hasColumn($table, 'applicant_id')) {
            return $query->whereHas('applicant', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return $query;
    }

    /**
     * Scope a query to user's branch
     */
    public function scopeForUserBranch(Builder $query)
    {
        $branchId = session('user_branch_id');
        
        if ($branchId) {
            return $this->scopeForBranch($query, $branchId);
        }

        return $query;
    }

    /**
     * Check if user can access this model instance
     */
    public function canAccess()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // System admin can access everything
        if ($user->hasRole('system-admin')) {
            return true;
        }

        $branchId = session('user_branch_id');

        // Branch admin and tutors can access their branch data
        if ($user->hasRole(['branch-admin', 'tutor'])) {
            // Direct branch_id
            if (isset($this->branch_id)) {
                return $this->branch_id == $branchId;
            }

            // Through applicant
            if (isset($this->applicant_id) && $this->applicant) {
                return $this->applicant->branch_id == $branchId;
            }

            // Through enrollment
            if (isset($this->enrollment_id) && $this->enrollment && $this->enrollment->applicant) {
                return $this->enrollment->applicant->branch_id == $branchId;
            }

            // Through invoice
            if (isset($this->invoice_id) && $this->invoice && $this->invoice->applicant) {
                return $this->invoice->applicant->branch_id == $branchId;
            }

            // Through venue
            if (isset($this->venue_id) && $this->venue) {
                return $this->venue->branch_id == $branchId;
            }

            return false;
        }

        // Students can access only their own data
        if ($user->hasRole('student')) {
            // Direct user_id
            if (isset($this->user_id)) {
                return $this->user_id == $user->id;
            }

            // Through applicant
            if (isset($this->applicant_id) && $this->applicant) {
                return $this->applicant->user_id == $user->id;
            }

            // Through enrollment
            if (isset($this->enrollment_id) && $this->enrollment && $this->enrollment->applicant) {
                return $this->enrollment->applicant->user_id == $user->id;
            }

            // Through invoice
            if (isset($this->invoice_id) && $this->invoice && $this->invoice->applicant) {
                return $this->invoice->applicant->user_id == $user->id;
            }

            return false;
        }

        return false;
    }

    /**
     * Get the branch ID for this model instance
     */
    public function getBranchId()
    {
        // Direct branch_id
        if (isset($this->branch_id)) {
            return $this->branch_id;
        }

        // Through applicant
        if (isset($this->applicant_id) && $this->applicant) {
            return $this->applicant->branch_id;
        }

        // Through enrollment
        if (isset($this->enrollment_id) && $this->enrollment && $this->enrollment->applicant) {
            return $this->enrollment->applicant->branch_id;
        }

        // Through invoice
        if (isset($this->invoice_id) && $this->invoice && $this->invoice->applicant) {
            return $this->invoice->applicant->branch_id;
        }

        // Through venue
        if (isset($this->venue_id) && $this->venue) {
            return $this->venue->branch_id;
        }

        return null;
    }
}
