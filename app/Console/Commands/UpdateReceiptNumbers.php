<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateReceiptNumbers extends Command
{
    protected $signature = 'update:receipt-numbers';
    protected $description = 'Update receipt numbers for all payments';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();

        try {
            DB::statement("SET @receipt_number := 0;");

            DB::table('payments')
                ->orderBy('created_at', 'asc')
                ->select('id') // select only the id to minimize memory usage
                ->chunkById(100, function ($payments) {
                    foreach ($payments as $payment) {
                        $newReceiptNumber = DB::select("SELECT CONCAT('OUT-', LPAD(@receipt_number := @receipt_number + 1, 4, '0')) as receipt_number")[0]->receipt_number;
                        DB::table('payments')
                            ->where('id', $payment->id)
                            ->update(['receipt_number' => $newReceiptNumber]);
                    }
                });

            DB::commit();
            $this->info('Receipt numbers updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
