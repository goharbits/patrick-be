<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }
        .email-header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        .email-body {
            padding: 20px;
            line-height: 1.6;
        }
        .email-body p {
            margin: 10px 0;
        }
        .email-footer {
            background-color: #f1f1f1;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            New Contact Submission Received
        </div>
        <div class="email-body">
            <p>Hello Admin,</p>
            <p>You have received a new contact submission. Here are the details:</p>
            <p><strong>Name:</strong> {{ $contact->name }}</p>
            <p><strong>Email:</strong> {{ $contact->email }}</p>
            <p><strong>Phone Number:</strong> {{ $contact->phone_number ?? 'N/A' }}</p>
            <p><strong>Heard From Us:</strong> {{ $contact->heard_from_us ?? 'N/A' }}</p>
            <p><strong>Message:</strong></p>
            <p>{{ $contact->message }}</p>
            <p>Please review this submission at your earliest convenience.</p>
        </div>
        <div class="email-footer">
            &copy; 2024 Your Application. All rights reserved.
        </div>
    </div>
</body>
</html>
