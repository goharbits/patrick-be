<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interface\ContactUsInterface;
use Carbon\Carbon;
use App\Mail\AdminContactNotification;
use App\Mail\UserContactAcknowledgment;
use Illuminate\Support\Facades\Mail;


class ContactUsRepository implements ContactUsInterface
{
    public function __construct(
        private User $user,private ContactUs $contactUs
    ) {
    }

    public function index(){
        return ContactUs::all();
    }


    public function store($request){
        try {
            $contactUs = new ContactUs();
            $contactUs->fill($request->all());
            $contactUs->save();

             // Send emails
            $this->sendEmails($contactUs);

            return [
                'status' => 200,
                'message' => 'Contact submission successfully created.',
                'data' => $contactUs
            ];

        } catch (\Exception $e) {
            // Log the exception for debugging (optional)
            Log::error('ContactUs Store Error: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => 'Failed to create contact submission.',
                'data' => null
            ];
        }
    }


    private function sendEmails(ContactUs $contactUs)
    {
        try {
            // Mail to admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                Mail::to($admin->email)->send(new AdminContactNotification($contactUs));
            }

            // Mail to user
            Mail::to($contactUs->email)->send(new UserContactAcknowledgment($contactUs));
        } catch (\Exception $e) {
            // Log any email sending errors
            Log::error('Error sending emails: ' . $e->getMessage());
        }
    }


}