<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * General institutional HTML notification email used across alerts and reminders.
 */
class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageBody;
    public string $mailSubject;

    public function __construct(string $messageBody, string $mailSubject = 'OUT SCMIS Notification')
    {
        $this->messageBody = $messageBody;
        $this->mailSubject = $mailSubject;
    }

    public function build(): static
    {
        $safeBody = nl2br(e($this->messageBody));
        $appName = config('app.name', 'OUT SCMIS');

        return $this->subject($this->mailSubject)
            ->html("
                <div style='font-family: Arial, sans-serif; color: #1f2937; max-width: 640px; margin: 0 auto;'>
                    <div style='background: #013ba6; color: #ffffff; padding: 16px 24px;'>
                        <h2 style='margin: 0; font-size: 18px;'>{$appName}</h2>
                    </div>
                    <div style='padding: 24px; border: 1px solid #e5e7eb; background: #ffffff;'>
                        <p style='line-height: 1.6;'>{$safeBody}</p>
                        <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                        <p style='font-size: 12px; color: #6b7280;'>The Open University of Tanzania — Short Courses Management Information System</p>
                    </div>
                </div>
            ");
    }
}

