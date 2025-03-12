<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SubscriptionReminderEmail;

class SendSubscriptionReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:buffer-continue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to users whose subscriptions are expired but in buffer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current date (end of the day)
        $today = Carbon::today();
        $buffer_days  = Carbon::today()->addDays(config('app.buffer_days')) ;

        // Retrieve all active subscriptions that expire today
        $subscriptions = UserSubscription::whereDate('start_date', $today)
            ->with(['user'])
            ->where('status', 'paid')
            ->get();

        if ($subscriptions->isEmpty()) {
            Log::info('No subscriptions are expiring today.');
            return;
        }

        // Loop through the subscriptions and send an email to each user
        foreach ($subscriptions as $subscription) {

            $user = $subscription->user;
            // Skip if user does not exist
            if (!$user) {
                Log::warning("Subscription ID {$subscription->id} has no associated user.");
                continue;
            }

                // Update user and subscription to buffer status
            $user->update([
                'subscription_status' => 'buffer',
                'buffer_end_date' => $bufferEndDate,
            ]);

            $subscription->update([
                'status' => 'buffer',
            ]);

            try {
                // Send subscription reminder email
                Mail::to($user->email)->send(
                    new SubscriptionReminderEmail($user, $subscription, $bufferEndDate->format('F j, Y'))
                );
                Log::info("Reminder email sent to user: {$user->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send reminder email to {$user->email}: {$e->getMessage()}");
            }
        }

         Log::info("Subscription reminder emails sent for {$today->toDateString()}.");

    }
}