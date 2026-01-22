<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations - Add reporting fields to existing tables
     */
    public function up(): void
    {
        // ========================================
        // 1. APPLICANTS TABLE
        // ========================================
        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'registration_date')) {
                $table->date('registration_date')->nullable()->after('created_at');
            }
            if (!Schema::hasColumn('applicants', 'status')) {
                $table->string('status', 50)->default('active')->after('education_level');
            }
        });

        // Update registration_date from created_at
        DB::table('applicants')
            ->whereNull('registration_date')
            ->whereNotNull('created_at')
            ->update(['registration_date' => DB::raw('DATE(created_at)')]);

        // Add index
        if (!$this->indexExists('applicants', 'applicants_branch_status_index')) {
            Schema::table('applicants', function (Blueprint $table) {
                $table->index(['branch_id', 'status'], 'applicants_branch_status_index');
            });
        }

        // ========================================
        // 2. ENROLLMENTS TABLE
        // ========================================
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'enrollment_status')) {
                $table->string('enrollment_status', 50)->default('active')->after('end_date');
            }
            if (!Schema::hasColumn('enrollments', 'completion_date')) {
                $table->date('completion_date')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('enrollments', 'attendance_percentage')) {
                $table->decimal('attendance_percentage', 5, 2)->default(0.00)->after('enrollment_status');
            }
        });

        // Add index
        if (!$this->indexExists('enrollments', 'enrollments_course_venue_index')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->index(['course_id', 'venue_id'], 'enrollments_course_venue_index');
            });
        }

        // ========================================
        // 3. INVOICES TABLE
        // ========================================
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0.00)->after('balance');
            }
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('status');
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('date');
            }
        });

        // Calculate total_amount for existing records
        DB::table('invoices')
            ->where(function ($query) {
                $query->where('total_amount', 0)
                      ->orWhereNull('total_amount');
            })
            ->update([
                'total_amount' => DB::raw('COALESCE(balance, 0) + COALESCE(paid_amount, 0)')
            ]);

        // Add index
        if (!$this->indexExists('invoices', 'invoices_status_date_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['status', 'date'], 'invoices_status_date_index');
            });
        }

        // ========================================
        // 4. PAYMENTS TABLE
        // ========================================
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method', 50)->default('cash')->after('amount');
            }
            if (!Schema::hasColumn('payments', 'transaction_reference')) {
                $table->string('transaction_reference', 100)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'payment_status')) {
                $table->string('payment_status', 50)->default('completed')->after('transaction_reference');
            }
        });

        // Add index
        if (!$this->indexExists('payments', 'payments_date_method_index')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['date', 'payment_method'], 'payments_date_method_index');
            });
        }

        // ========================================
        // 5. COURSES TABLE
        // ========================================
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('name');
            }
            if (!Schema::hasColumn('courses', 'category')) {
                $table->string('category', 100)->nullable()->after('name');
            }
        });

        // ========================================
        // 6. BRANCHES TABLE
        // ========================================
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('name');
            }
            if (!Schema::hasColumn('branches', 'region')) {
                $table->string('region', 100)->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Remove from applicants
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropIndex('applicants_branch_status_index');
            $table->dropColumn(['registration_date', 'status']);
        });

        // Remove from enrollments
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_course_venue_index');
            $table->dropColumn(['enrollment_status', 'completion_date', 'attendance_percentage']);
        });

        // Remove from invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_status_date_index');
            $table->dropColumn(['total_amount', 'payment_method', 'due_date']);
        });

        // Remove from payments
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_date_method_index');
            $table->dropColumn(['payment_method', 'transaction_reference', 'payment_status']);
        });

        // Remove from courses
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'category']);
        });

        // Remove from branches
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'region']);
        });
    }

    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select(
            "SHOW INDEX FROM {$table} WHERE Key_name = ?",
            [$index]
        );
        
        return !empty($indexes);
    }
};
