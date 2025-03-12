<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;
use App\Repositories\ContactUsRepository;
use App\Http\Requests\ContactUsRequest;
use Illuminate\Support\Facades\Log;

class ContactUsController extends Controller
{

    public function __construct(
        private ContactUsRepository $contactUsRepository,
    ) {}

    public function index()
    {
       try {
            $response = $this->contactUsRepository->index();
                return self::successResponse(
                    self::OK,
                    'Record Found',
                    $response
            );
        } catch (\Exception $error) {
            Log::Error('Error at get contacts: '. $e->getMessage());
            return self::errorResponse(
                self::INTERNAL_SERVER_ERROR,
                $error->getMessage()
            );
        }

    }

    public function store(ContactUsRequest $request)
    {
        try {
            $response = $this->contactUsRepository->store($request);

            if ($response['status'] == 200) {
                return self::successResponse(
                    self::OK,
                    $response['message'],
                    $response['data']
                );
            }

            return self::errorResponse(
                self::INTERNAL_SERVER_ERROR,
                $response['message']
            );

        } catch (\Exception $error) {
            Log::Error('Error at store contacts: '. $e->getMessage());
            return self::errorResponse(
                self::INTERNAL_SERVER_ERROR,
                $error->getMessage()
            );
        }


    }

    public function show($id)
    {
        try {
            // Use findOrFail to automatically throw a ModelNotFoundException if the record is not found
            $contact = ContactUs::findOrFail($id);
            return self::successResponse(
                self::OK,
                'Record found',
                $contact
            );
        } catch (\Exception $e) {
            // Handle any unexpected exceptions
            Log::Error('An unexpected error occurred: '. $e->getMessage());
            return self::errorResponse(
                self::INTERNAL_SERVER_ERROR,
                'Record not found'
            );
        }
    }


    public function destroy(ContactUs $contactUs)
    {
        $contactUs->delete();

        return self::successResponse(
            self::OK,
            'Contact submission successfully deleted.',
            $contact
        );
    }
}