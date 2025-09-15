<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Loan Application Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }

        .credentials {
            background-color: #e7f3ff;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Loan Application Confirmation</h1>
    </div>

    <div class="content">
        <h2>Dear {{ $borrower->first_name }} {{ $borrower->last_name }},</h2>

        <p>Thank you for submitting your loan application through our platform. We have received your application and it
            is now being processed.</p>

        <h3>Application Details:</h3>
        <ul>
            <li><strong>Name:</strong> {{ $borrower->first_name }} {{ $borrower->last_name }}</li>
            <li><strong>Email:</strong> {{ $borrower->email }}</li>
            @if($borrower->phone)
            <li><strong>Phone:</strong> {{ $borrower->phone }}</li>
            @endif
            <li><strong>Loan Type:</strong> {{ $borrower->loan_type }}</li>
            <li><strong>Transaction Type:</strong> {{ $borrower->transaction_type }}</li>
            <li><strong>Purchase Price:</strong> ${{ number_format($borrower->purchase_price, 2) }}</li>
            @if($borrower->selected_loan_program)
            <li><strong>Selected Program:</strong> {{ $borrower->selected_loan_program }}</li>
            @endif
        </ul>

        @if($isNewUser)
        <div class="credentials">
            <h3>🔐 Your Account Credentials</h3>
            <p>We've created an account for you to track your loan application:</p>
            <ul>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Temporary Password:</strong> {{ $tempPassword }}</li>
            </ul>
            <p><strong>Important:</strong> Please log in and change your password as soon as possible for security
                reasons.</p>
            <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
        </div>
        @else
        <div class="credentials">
            <h3>📊 Access Your Dashboard</h3>
            <p>You can track your loan application status by logging into your existing account.</p>
            <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
        </div>
        @endif

        <h3>What's Next?</h3>
        <ol>
            <li>Our loan specialists will review your application within 24-48 hours</li>
            <li>You will receive an email with next steps and any additional documentation needed</li>
            <li>You can track your application status through your account dashboard</li>
        </ol>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Best regards,<br>
            The Loan Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply directly to this email.</p>
        <p>If you did not submit this loan application, please contact us immediately.</p>
    </div>
</body>

</html>