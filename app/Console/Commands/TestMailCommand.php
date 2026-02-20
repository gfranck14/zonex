<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test';

    protected $description = 'Test email configuration by sending a test email';

    public function handle()
    {
        $this->info('Sending test email to adjile.glorieux@gmail.com...');

        try {
            Mail::raw('Test email from WiFiProfit', function ($message) {
                $message->to('adjile.glorieux@gmail.com')
                        ->subject('Test WiFiProfit Email Configuration');
            });

            $this->info('✅ Email sent successfully! Check your inbox.');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            return 1;
        }
    }
}
