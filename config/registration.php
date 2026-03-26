<?php

return [
    'otp' => [
        'expires_minutes' => (int) env('REGISTRATION_OTP_EXPIRES_MINUTES', 10),
        'max_attempts' => (int) env('REGISTRATION_OTP_MAX_ATTEMPTS', 5),
        'max_resend_count' => (int) env('REGISTRATION_OTP_MAX_RESEND_COUNT', 3),
        'resend_wait_seconds' => (int) env('REGISTRATION_OTP_RESEND_WAIT_SECONDS', 60),
    ],
    'token' => [
        'expires_minutes' => (int) env('REGISTRATION_TOKEN_EXPIRES_MINUTES', 15),
    ],
];
