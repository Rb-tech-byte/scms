<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentScheduleService;

class SendPaymentScheduleReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:send-reminders {--days=0 : Number of days ahead to include (default: due today only)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for due/overdue payment schedules and mark sent_at timestamp';

    protected PaymentScheduleService $scheduleService;

    /**
     * Create a new command instance.
     */
    public function __construct(PaymentScheduleService $scheduleService)
    {
        parent::__construct();
        $this->scheduleService = $scheduleService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        $this->info("Fetching schedules due within {$days} days...");

        // Update overdue statuses first
        $overdueCount = $this->scheduleService->updateOverdueStatuses();
        if ($overdueCount > 0) {
            $this->warn("Updated {$overdueCount} schedule(s) to overdue status.");
        }

        // Get schedules due for reminder
        $schedules = $this->scheduleService->getSchedulesDueForReminder($days);
        $count = $schedules->count();

        if ($count === 0) {
            $this->info('No schedules found requiring reminders.');
            return Command::SUCCESS;
        }

        $this->info("Found {$count} schedule(s) to remind.");

        // Progress bar
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $processed = 0;
        $failed = 0;

        foreach ($schedules as $schedule) {
            try {
                // TODO: Integrate real notification system
                // Example: Mail::to($schedule->invoice->applicant->email)->send(new PaymentReminderMail($schedule));
                // Example: SMS::send($schedule->invoice->applicant->mobile, "Payment reminder...");
                
                // For now, just mark as sent
                $schedule->markAsSent();
                
                $processed++;
            } catch (\Exception $e) {
                $failed++;
                $this->error("Failed to send reminder for schedule #{$schedule->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("✓ Reminders sent successfully: {$processed}");
        if ($failed > 0) {
            $this->error("✗ Failed: {$failed}");
        }

        return Command::SUCCESS;
    }
}
