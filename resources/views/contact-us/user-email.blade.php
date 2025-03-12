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
            background-color: #28a745;
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
            Thank You for Contacting Us!
        </div>
        <div class="email-body">
            <p>Hello {{ @$contact->name }},</p>
            <p>Thank you for reaching out to us! We have received your message and will get back to you shortly. Here are the details you provided:</p>
            <p>If you have any further questions, feel free to reply to this email.</p>
            <p>Best regards,<br>Your Application Team</p>
        </div>
        <div class="email-footer">
            &copy; 2024 Your Application. All rights reserved.
        </div>
    </div>
</body>
</html>
