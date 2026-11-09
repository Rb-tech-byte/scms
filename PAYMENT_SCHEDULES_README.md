# Payment Schedules System - Documentation

## Overview
The Payment Schedules system allows you to create and manage installment payment plans for invoices. This comprehensive system includes automated reminders, status tracking, and flexible payment templates.

## Features

### Core Features
- **Flexible Payment Templates**: Weekly, Bi-weekly, Monthly, and Quarterly installment plans
- **Automated Status Management**: Automatic status updates based on due dates
- **Payment Tracking**: Link payments to specific schedule installments
- **Reminder System**: Automated and manual reminder sending
- **Statistics Dashboard**: Comprehensive overview of schedule performance
- **Bulk Operations**: Send reminders to multiple schedules at once

### Status Types
- **Pending**: Schedule created, payment not yet due
- **Sent**: Reminder has been sent to the applicant
- **Paid**: Payment received and recorded
- **Overdue**: Past due date without payment
- **Cancelled**: Schedule cancelled (e.g., when regenerating)

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

This will create the `payment_schedules` table with the following structure:
- `id`: Primary key
- `invoice_id`: Foreign key to invoices table
- `installment_number`: Sequential number (1, 2, 3...)
- `due_date`: When payment is due
- `amount`: Amount for this installment
- `status`: Current status (pending, sent, paid, overdue, cancelled)
- `sent_at`: Timestamp when reminder was sent
- `paid_at`: Timestamp when payment was received
- `payment_id`: Foreign key to payments table (nullable)
- `notes`: Additional notes or cancellation reasons
- `timestamps`: Created and updated timestamps

### 2. Service Provider
The `PaymentScheduleService` is automatically available via dependency injection. No additional configuration needed.

## Usage Guide

### Creating Payment Schedules

#### Via Web Interface
1. Navigate to **Payment Schedules** → **Generate Schedule**
2. Select an invoice with an outstanding balance
3. Choose payment frequency (weekly, monthly, etc.)
4. Set number of installments (1-36)
5. Select first payment date
6. (Optional) Override invoice balance with custom amount
7. Click **Preview Schedule** to see the plan
8. Click **Generate Schedule** to create

#### Programmatically
```php
use App\Services\PaymentScheduleService;
use App\Models\Invoice;
use Illuminate\Support\Carbon;

$service = app(PaymentScheduleService::class);
$invoice = Invoice::find(1);

$schedules = $service->generateSchedules(
    invoice: $invoice,
    template: 'monthly',  // weekly, biweekly, monthly, quarterly
    installments: 6,
    startDate: Carbon::now()->addDays(7),
    customAmount: null  // or specify custom amount
);
```

### Managing Schedules

#### View All Schedules
Navigate to **Payment Schedules** to see all schedules with:
- Filtering by invoice, status, date range
- Statistics cards showing totals
- Quick actions (mark as sent, cancel, delete)

#### View Invoice Schedules
Click on an invoice number or navigate to **Payment Schedules** → **Invoice #X** to see:
- All schedules for that invoice
- Invoice details and applicant information
- Schedule statistics
- Individual schedule management

### Sending Reminders

#### Manual Reminders (Web Interface)
1. Navigate to **Payment Reminders**
2. Filter by days ahead (today, 3 days, 7 days, etc.)
3. Select schedules to remind
4. Click **Send Selected Reminders** or **Send All Reminders**

#### Automated Reminders (Command Line)
```bash
# Send reminders for schedules due today
php artisan payments:send-reminders

# Send reminders for schedules due within 7 days
php artisan payments:send-reminders --days=7
```

#### Schedule Automated Reminders (Cron)
Add to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Send reminders daily at 9 AM for schedules due within 3 days
    $schedule->command('payments:send-reminders --days=3')
             ->dailyAt('09:00');
    
    // Update overdue statuses daily at midnight
    $schedule->call(function () {
        app(PaymentScheduleService::class)->updateOverdueStatuses();
    })->daily();
}
```

### Processing Payments

When a payment is received, you can link it to schedules:

```php
use App\Services\PaymentScheduleService;
use App\Models\Invoice;
use App\Models\Payment;

$service = app(PaymentScheduleService::class);
$invoice = Invoice::find(1);
$payment = Payment::find(1);

// Automatically allocate payment to schedules
$service->processPaymentAgainstSchedules($invoice, $payment);
```

This will:
1. Find pending/overdue schedules ordered by due date
2. Mark schedules as paid (full or partial)
3. Update schedule amounts if partial payment
4. Link payment to schedule via `payment_id`

## API Reference

### PaymentScheduleService Methods

#### `generateSchedules()`
```php
public function generateSchedules(
    Invoice $invoice,
    string $template,
    int $installments,
    Carbon $startDate,
    ?float $customAmount = null
): Collection
```
Generates payment schedules for an invoice.

#### `cancelExistingSchedules()`
```php
public function cancelExistingSchedules(
    Invoice $invoice,
    ?string $reason = null
): int
```
Cancels all pending/sent schedules for an invoice.

#### `updateOverdueStatuses()`
```php
public function updateOverdueStatuses(): int
```
Updates pending schedules past their due date to overdue status.

#### `getSchedulesDueForReminder()`
```php
public function getSchedulesDueForReminder(int $daysAhead = 0): Collection
```
Gets schedules that need reminders sent.

#### `processPaymentAgainstSchedules()`
```php
public function processPaymentAgainstSchedules(
    Invoice $invoice,
    $payment
): void
```
Allocates a payment across pending schedules.

#### `getScheduleStatistics()`
```php
public function getScheduleStatistics(Invoice $invoice): array
```
Returns statistics for an invoice's schedules.

### PaymentSchedule Model Methods

#### Scopes
- `pending()`: Get pending schedules
- `overdue()`: Get overdue schedules
- `dueSoon($days)`: Get schedules due within X days
- `paid()`: Get paid schedules
- `forInvoice($invoiceId)`: Get schedules for specific invoice

#### Instance Methods
- `isOverdue()`: Check if schedule is overdue
- `isDueSoon($days)`: Check if due within X days
- `markAsSent()`: Mark reminder as sent
- `markAsPaid($paymentId)`: Mark as paid
- `markAsOverdue()`: Mark as overdue
- `cancel($notes)`: Cancel the schedule

#### Accessors
- `status_badge_class`: Bootstrap badge class for status
- `days_until_due`: Days until/past due date
- `formatted_amount`: Formatted amount with 2 decimals

## Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/payment-schedules` | payment-schedules.index | List all schedules |
| GET | `/payment-schedules/create` | payment-schedules.create | Show create form |
| POST | `/payment-schedules/generate` | payment-schedules.generate | Generate schedules |
| GET | `/payment-schedules/{invoiceId}` | payment-schedules.show | Show invoice schedules |
| PUT | `/payment-schedules/{schedule}` | payment-schedules.update | Update schedule |
| DELETE | `/payment-schedules/{schedule}` | payment-schedules.destroy | Delete schedule |
| POST | `/payment-schedules/{schedule}/mark-sent` | payment-schedules.mark-sent | Mark as sent |
| POST | `/payment-schedules/{schedule}/cancel` | payment-schedules.cancel | Cancel schedule |
| GET | `/payment-reminders` | payment-schedules.reminders | Reminders page |
| POST | `/payment-reminders/send` | payment-schedules.send-reminders | Send reminders |

## Best Practices

### 1. Regular Status Updates
Run the status update command daily:
```bash
php artisan payments:send-reminders --days=0
```

### 2. Notification Integration
Integrate with your notification system in:
- `PaymentScheduleController::sendReminders()`
- `SendPaymentScheduleReminders` command

Example:
```php
use Illuminate\Support\Facades\Mail;

foreach ($schedules as $schedule) {
    Mail::to($schedule->invoice->applicant->email)
        ->send(new PaymentReminderMail($schedule));
    
    $schedule->markAsSent();
}
```

### 3. Payment Processing
Always use `processPaymentAgainstSchedules()` when recording payments to automatically update schedules.

### 4. Regenerating Schedules
When regenerating schedules, existing pending ones are automatically cancelled with a note.

## Troubleshooting

### Schedules Not Showing
- Check invoice has a balance > 0
- Verify migration ran successfully
- Check relationship in Invoice model exists

### Reminders Not Sending
- Ensure notification system is configured
- Check `sent_at` is null for schedules
- Verify email/SMS credentials

### Status Not Updating
- Run `php artisan payments:send-reminders` to trigger status updates
- Check server cron jobs are configured
- Verify dates are in correct format

## Future Enhancements

- [ ] Email/SMS template customization
- [ ] Webhook notifications for due payments
- [ ] Partial payment tracking improvements
- [ ] Schedule modification (change dates/amounts)
- [ ] Payment plan templates (save and reuse)
- [ ] Multi-currency support
- [ ] Grace period configuration
- [ ] Late fee calculation

## Support

For issues or questions:
1. Check this documentation
2. Review the code comments
3. Contact the development team

---

**Version**: 1.0.0  
**Last Updated**: January 2026  
**Author**: Development Team
