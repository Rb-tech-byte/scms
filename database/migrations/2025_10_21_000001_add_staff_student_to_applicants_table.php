<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'is_staff_student')) {
                $table->boolean('is_staff_student')->default(false)->after('is_out_student');
            }
            if (!Schema::hasColumn('applicants', 'parent_staff_id')) {
                $table->string('parent_staff_id')->nullable()->after('is_staff_student');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (Schema::hasColumn('applicants', 'parent_staff_id')) {
                $table->dropColumn('parent_staff_id');
            }
            if (Schema::hasColumn('applicants', 'is_staff_student')) {
                $table->dropColumn('is_staff_student');
            }
        });
    }
};
