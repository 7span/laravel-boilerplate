<?php

return [
    'hello' => 'Hello',
    'regards' => 'Best Regards,',
    'welcomeUser' => [
        'subject' => 'Welcome to :app_name',
        'greeting' => 'Welcome to :app_name!',
        'content' => 'Thank you for registering with us. We\'re excited to have you on board!',
        'action' => 'Visit Website',
        'footer' => 'If you have any questions, please don\'t hesitate to contact us.',
    ],
    'forget_password' => [
        'subject' => 'Forgot Password Request',
        'line1' => 'We received a request to reset your password. If you did not make this request, please ignore this email.',
        'line2' => 'Your OTP is :otp. Please note that it is valid for the next :valid_minute minutes.',
        'footer' => 'If you have any questions, please contact our support team.',
    ],
    'forgetPasswordEmailSubject' => 'Forgot Password',
    'verifyUserSubject' => 'Verify User',
    'updateProfileSubject' => 'Update Profile',
    'forgetPasswordLinkEmailLine1' => 'We hope this message finds you well. It appears that you have requested to reset the password associated with your ' . config('app.name') . ' account. If you did not initiate this request, please ignore this email.',
    'forgetPasswordLinkEmailLine2' => 'To reset your password, please click on the following link:',
    'link' => 'Password Reset Link',
    'verifyUserLine1' => 'Thank you for choosing ' . config('app.name') . '. Use the following OTP to complete your\n     :subject process. OTP is valid for :expirationTime minutes.',
];
