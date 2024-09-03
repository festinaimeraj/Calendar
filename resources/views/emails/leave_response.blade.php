<!-- <!DOCTYPE html>
<html>
<head>
    <style>
        .email-container {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .email-header {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: center;
        }
        .email-body {
            padding: 20px;
        }
        .email-footer {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: center;
        }
        .email-title {
            color: #444;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2 class="email-title">Leave Request {{ ucfirst($leaveRequest->answer) }}</h2>
        </div>
        <div class="email-body">
            <p>Dear {{ $fullName }},</p>
            <p>Your leave request has been <strong>{{ $leaveRequest->answer }}</strong>.</p>
            <p><strong>Leave Type:</strong> {{ $leaveType }}</p>
            <p><strong>Start Date:</strong> {{ $startDate }}</p>
            <p><strong>End Date:</strong> {{ $endDate }}</p>
            <p><strong>Response:</strong> {{ $leaveRequest->response_message }}</p>
        </div>
        <div class="email-footer">
            <p>This is an automated message. Please do not reply.</p>
        </div>
    </div>
</body>
</html> -->
