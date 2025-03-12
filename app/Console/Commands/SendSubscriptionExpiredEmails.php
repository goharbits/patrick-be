<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SubscriptionExpiredEmail;

class SendSubscriptionExpiredEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:buffer-expired';

    /**
     * The console command description.
     *
     * @var string
     */
       protected $description = 'Send emails to users whose subscriptions expired';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current date (end of the day)
        $today = Carbon::now()->startOfDay();

        // Retrieve all active subscriptions that expire today
        $subscriptions = UserSubscription::whereDate('start_date', $today)
            ->with(['user'])
            ->where('status', 'buffer')
            ->get();

        if ($subscriptions->isEmpty()) {
            Log::info('No subscriptions are expiring in buffer today.');
            return;
        }


        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            // Validate user and subscription status
            if (!$user || $user->subscription_status !== 'buffer') {
                Log::warning("Invalid subscription or user for subscription ID: {$subscription->id}");
                continue;
            }

            // Check if the buffer end date matches today
            $bufferEndDate = Carbon::parse($user->buffer_end_date);
            if (!$bufferEndDate->isSameDay($today)) {
                Log::info("Buffer end date for user {$user->id} does not match today.");
                continue;
            }

            // Update user and subscription status
            $user->update([
                'subscription_status' => 'inactive',
                'buffer_end_date' => null,
            ]);

            $subscription->update([
                'status' => 'expired',
            ]);
            try {
                // Send subscription expired email
                Mail::to($user->email)->send(new SubscriptionExpiredEmail($user, $subscription));
                Log::info("Subscription expired email sent to user: {$user->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send email to {$user->email}: {$e->getMessage()}");
            }
        }
        Log::info("Subscription expiration process completed for {$today->toDateString()}.");
    }
}