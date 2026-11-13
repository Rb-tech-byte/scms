<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('force_password_reset');
            }
            if (!Schema::hasColumn('users', 'blocked_at')) {
                $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            }
            if (!Schema::hasColumn('users', 'blocked_reason')) {
                $table->string('blocked_reason')->nullable()->after('blocked_at');
            }
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('blocked_reason');
            }
            if (!Schema::hasColumn('users', 'lockout_count')) {
                $table->unsignedTinyInteger('lockout_count')->default(0)->after('failed_login_attempts');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('lockout_count');
            }
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('locked_until');
            }
            if (!Schema::hasColumn('users', 'active_session_id')) {
                $table->string('active_session_id', 120)->nullable()->after('password_changed_at');
            }
            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('active_session_id');
            }
        });

        DB::table('users')
            ->whereNull('password_changed_at')
            ->update(['password_changed_at' => now()]);

        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'teaching_method')) {
                $table->enum('teaching_method', ['face_to_face', 'online', 'both'])
                    ->default('face_to_face')
                    ->after('duration');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'control_number')) {
                $table->string('control_number', 40)->nullable()->unique()->after('invoice_prefix');
            }
            if (!Schema::hasColumn('invoices', 'control_number_generated_at')) {
                $table->timestamp('control_number_generated_at')->nullable()->after('control_number');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'receipt_file')) {
                $table->string('receipt_file')->nullable()->after('receipt');
            }
            if (!Schema::hasColumn('payments', 'verification_status')) {
                $table->enum('verification_status', ['verified', 'pending', 'rejected'])
                    ->default('verified')
                    ->after('receipt_file');
            }
        });

        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
                $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->string('serial_number', 64)->unique();
                $table->enum('status', ['pending', 'ready', 'issued'])->default('pending');
                $table->timestamp('ready_at')->nullable();
                $table->timestamp('issued_at')->nullable();
                $table->unsignedBigInteger('issued_by')->nullable();
                $table->timestamp('notified_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('attendance_records')) {
            Schema::create('attendance_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_assignment_id')->constrained('course_assignments')->cascadeOnDelete();
                $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
                $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
                $table->unsignedBigInteger('tutor_id')->nullable();
                $table->enum('status', ['present', 'absent', 'excused'])->default('present');
                $table->timestamp('marked_at')->nullable();
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->unique(['course_assignment_id', 'enrollment_id'], 'attendance_assignment_enrollment_unique');
            });
        }

        if (!Schema::hasTable('notification_templates')) {
            Schema::create('notification_templates', function (Blueprint $table) {
                $table->id();
                $table->string('key', 80)->unique();
                $table->string('name', 150);
                $table->enum('channel', ['email', 'sms', 'both'])->default('both');
                $table->string('subject')->nullable();
                $table->text('body');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('certificates');

        Schema::table('payments', function (Blueprint $table) {
            foreach (['verification_status', 'receipt_file'] as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'control_number_generated_at')) {
                $table->dropColumn('control_number_generated_at');
            }
            if (Schema::hasColumn('invoices', 'control_number')) {
                $table->dropUnique(['control_number']);
                $table->dropColumn('control_number');
            }
        });

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'teaching_method')) {
                $table->dropColumn('teaching_method');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'last_activity_at',
                'active_session_id',
                'password_changed_at',
                'locked_until',
                'lockout_count',
                'failed_login_attempts',
                'blocked_reason',
                'blocked_at',
                'is_blocked',
            ] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

