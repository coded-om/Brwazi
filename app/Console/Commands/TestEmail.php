<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Mailgun email configuration...');
        $this->info('===========================================');

        // Display current configuration
        $this->table(['Setting', 'Value'], [
            ['MAIL_MAILER', env('MAIL_MAILER')],
            ['MAIL_HOST', env('MAIL_HOST')],
            ['MAILGUN_DOMAIN', env('MAILGUN_DOMAIN')],
            ['MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')],
            ['MAIL_FROM_NAME', env('MAIL_FROM_NAME')],
        ]);

        try {
            Mail::raw(
                '🚀 Test email from Brwaz Platform via Mailgun' . PHP_EOL .
                'If you receive this email, Mailgun is configured correctly!' . PHP_EOL .
                'Time: ' . now()->format('Y-m-d H:i:s'),
                function ($msg) {
                    $msg->to('test@example.com')
                        ->subject('✅ Mailgun Test - Brwaz Platform');
                }
            );

            $this->info('✅ Email sent successfully via Mailgun!');
            $this->info('Check your Mailgun dashboard for delivery status.');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->warn('💡 Make sure to:');
            $this->warn('   1. Set correct MAILGUN_DOMAIN');
            $this->warn('   2. Set correct MAILGUN_SECRET');
            $this->warn('   3. Verify domain in Mailgun dashboard');
        }

        return 0;
    }
}
