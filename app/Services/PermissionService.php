<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PermissionService
{
    /**
     * Check if user has permission
     */
    public static function can($permission)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // System admin has all permissions
        if ($user->hasRole('system-admin')) {
            return true;
        }

        $raw = trim((string) $permission);
        $normalized = strtolower($raw);
        $normalized = preg_replace('/\s+/', '-', $normalized);
        $normalized = str_replace('_', '-', $normalized);

        // Check user's specific permission
        return $user->hasPermission($raw) || $user->hasPermission($normalized);
    }

    /**
     * Check if user can perform action on branch data
     */
    public static function canAccessBranch($branchId = null)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // System admin can access all branches
        if ($user->hasRole('system-admin')) {
            return true;
        }

        // Branch admin and tutors can only access their branch
        if ($user->hasRole(['branch-admin', 'tutor'])) {
            $userBranchId = session('user_branch_id');
            
            if ($branchId === null) {
                return $userBranchId !== null;
            }

            return $userBranchId == $branchId;
        }

        return false;
    }

    /**
     * Check if user can create in their branch
     */
    public static function canCreate($permission)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // System admin can create anywhere
        if ($user->hasRole('system-admin')) {
            return true;
        }

        // Branch admin can create in their branch
        if ($user->hasRole('branch-admin')) {
            return $user->hasPermission($permission) && session('user_branch_id') !== null;
        }

        return false;
    }

    /**
     * Check if user can update specific record
     */
    public static function canUpdate($permission, $model)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // System admin can update anything
        if ($user->hasRole('system-admin')) {
            return true;
        }

        // Branch admin can update in their branch
        if ($user->hasRole('branch-admin')) {
            if (!$user->hasPermission($permission)) {
                return false;
            }

            $userBranchId = session('user_branch_id');
            
            // Check if model has branch_id
            if (isset($model->branch_id)) {
                return $model->branch_id == $userBranchId;
            }

            return true;
        }

        return false;
    }

    /**
     * Check if user can delete specific record
     */
    public static function canDelete($permission, $model)
    {
        return self::canUpdate($permission, $model);
    }

    /**
     * Check if user can view specific record
     */
    public static function canView($permission, $model = null)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // System admin can view anything
        if ($user->hasRole('system-admin')) {
            return true;
        }

        // Branch admin and tutors can view their branch data
        if ($user->hasRole(['branch-admin', 'tutor'])) {
            if (!$user->hasPermission($permission)) {
                return false;
            }

            if ($model === null) {
                return true;
            }

            $userBranchId = session('user_branch_id');
            
            // Check if model has branch_id
            if (isset($model->branch_id)) {
                return $model->branch_id == $userBranchId;
            }

            return true;
        }

        // Students can view their own data
        if ($user->hasRole('student')) {
            if ($model === null) {
                return $user->hasPermission($permission);
            }

            if (isset($model->user_id)) {
                return $model->user_id == $user->id;
            }

            return false;
        }

        return false;
    }

    /**
     * Get user's accessible branch IDs
     */
    public static function getAccessibleBranchIds()
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // System admin can access all branches
        if ($user->hasRole('system-admin')) {
            return \App\Models\Branch::pluck('id')->toArray();
        }

        // Branch admin and tutors can access only their branch
        if ($user->hasRole(['branch-admin', 'tutor'])) {
            $branchId = session('user_branch_id');
            return $branchId ? [$branchId] : [];
        }

        return [];
    }

    /**
     * Check if user is system admin
     */
    public static function isSystemAdmin()
    {
        $user = Auth::user();
        return $user && $user->hasRole('system-admin');
    }

    /**
     * Check if user is branch admin
     */
    public static function isBranchAdmin()
    {
        $user = Auth::user();
        return $user && $user->hasRole('branch-admin');
    }

    /**
     * Check if user is tutor
     */
    public static function isTutor()
    {
        $user = Auth::user();
        return $user && $user->hasRole('tutor');
    }

    /**
     * Check if user is student
     */
    public static function isStudent()
    {
        $user = Auth::user();
        return $user && $user->hasRole('student');
    }
}
