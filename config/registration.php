<?php

$to_valid_int = static function (mixed $value, int $default, int $min): int {
    if ($value === null) {
        return $default;
    }

    if (is_int($value)) {
        return $value >= $min ? $value : $default;
    }

    if (is_string($value) && preg_match('/^-?\d+$/', $value) === 1) {
        $int_value = (int) $value;

        return $int_value >= $min ? $int_value : $default;
    }

    return $default;
};

return [
    'otp' => [
        'expires_minutes' => $to_valid_int(env('REGISTRATION_OTP_EXPIRES_MINUTES'), 10, 1),
        'max_attempts' => $to_valid_int(env('REGISTRATION_OTP_MAX_ATTEMPTS'), 5, 1),
        'max_resend_count' => $to_valid_int(env('REGISTRATION_OTP_MAX_RESEND_COUNT'), 3, 0),
        'resend_wait_seconds' => $to_valid_int(env('REGISTRATION_OTP_RESEND_WAIT_SECONDS'), 60, 1),
    ],
    'token' => [
        'expires_minutes' => $to_valid_int(env('REGISTRATION_TOKEN_EXPIRES_MINUTES'), 15, 1),
    ],
];
