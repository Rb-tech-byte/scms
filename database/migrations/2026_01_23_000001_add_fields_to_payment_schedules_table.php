<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns to existing table
        Schema::table('payment_schedules', function (Blueprint $table) {
            // Add installment_number column
            if (!Schema::hasColumn('payment_schedules', 'installment_number')) {
                $table->integer('installment_number')->default(1)->after('invoice_id');
            }
            
            // Add payment_id column
            if (!Schema::hasColumn('payment_schedules', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable()->after('paid_at');
            }
        });

        // Add foreign key constraint
        Schema::table('payment_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_schedules', 'payment_id')) {
                return; // Column doesn't exist, skip foreign key
            }
            
            // Check if foreign key doesn't already exist
            $foreignKeys = DB::select(
                "SELECT CONSTRAINT_NAME 
                 FROM information_schema.KEY_COLUMN_USAGE 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = 'payment_schedules' 
                 AND CONSTRAINT_NAME = 'payment_schedules_payment_id_foreign'"
            );
            
            if (empty($foreignKeys)) {
                $table->foreign('payment_id')
                      ->references('id')
                      ->on('payments')
                      ->onDelete('set null');
            }
        });

        // Add indexes
        Schema::table('payment_schedules', function (Blueprint $table) {
            // Check if index doesn't already exist
            $indexes = DB::select(
                "SHOW INDEX FROM payment_schedules WHERE Key_name = 'payment_schedules_status_due_date_index'"
            );
            
            if (empty($indexes)) {
                $table->index(['status', 'due_date'], 'payment_schedules_status_due_date_index');
            }
        });

        // Update existing records to have sequential installment numbers
        $this->updateInstallmentNumbers();
    }

    /**
     * Update installment numbers for existing records
     */
    private function updateInstallmentNumbers(): void
    {
        // Get all invoices that have payment schedules
        $invoices = DB::table('payment_schedules')
            ->select('invoice_id')
            ->distinct()
            ->pluck('invoice_id');

        foreach ($invoices as $invoiceId) {
            // Get schedules for this invoice ordered by due_date
            $schedules = DB::table('payment_schedules')
                ->where('invoice_id', $invoiceId)
                ->orderBy('due_date')
                ->orderBy('id')
                ->get();

            // Update each schedule with sequential number
            $installmentNumber = 1;
            foreach ($schedules as $schedule) {
                DB::table('payment_schedules')
                    ->where('id', $schedule->id)
                    ->update(['installment_number' => $installmentNumber]);
                $installmentNumber++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('payment_schedules', 'payment_id')) {
                $table->dropForeign(['payment_id']);
                $table->dropColumn('payment_id');
            }
            
            // Drop installment_number column
            if (Schema::hasColumn('payment_schedules', 'installment_number')) {
                $table->dropColumn('installment_number');
            }
            
            // Drop index
            $table->dropIndex('payment_schedules_status_due_date_index');
        });
    }
};
