<?php

namespace App\Services;

use App\Mail\RegistrationOtpMail;
use App\Models\RegistrationOtp;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegistrationService
{
    public const ERROR_EMAIL_ALREADY_REGISTERED = 'EMAIL_ALREADY_REGISTERED';
    public const ERROR_RESEND_WAIT = 'RESEND_WAIT';
    public const ERROR_RESEND_LIMIT_EXCEEDED = 'RESEND_LIMIT_EXCEEDED';
    public const ERROR_OTP_NOT_FOUND = 'OTP_NOT_FOUND';
    public const ERROR_OTP_EXPIRED = 'OTP_EXPIRED';
    public const ERROR_OTP_INVALID = 'OTP_INVALID';
    public const ERROR_OTP_ATTEMPTS_EXCEEDED = 'OTP_ATTEMPTS_EXCEEDED';
    public const ERROR_REGISTRATION_TOKEN_INVALID = 'REGISTRATION_TOKEN_INVALID';
    public const ERROR_REGISTRATION_TOKEN_EXPIRED = 'REGISTRATION_TOKEN_EXPIRED';
    public const ERROR_REGISTRATION_FAILED = 'REGISTRATION_FAILED';

    /**
     * OTPを発行してメール送信
     *
     * @param string $email
     * @return array<string, mixed>
     */
    public function sendOtp(string $email): array
    {
        if ($this->isRegisteredEmail($email)) {
            return $this->failure(self::ERROR_EMAIL_ALREADY_REGISTERED, 'このメールアドレスはすでに登録されています。');
        }

        $now = CarbonImmutable::now();
        $record = RegistrationOtp::where('email', $email)->first();

        if ($record && $record->last_sent_at !== null) {
            $resend_available_at = CarbonImmutable::instance($record->last_sent_at)
                ->addSeconds($this->otpResendWaitSeconds());
            if ($resend_available_at->isFuture()) {
                $wait_seconds = $now->diffInSeconds($resend_available_at);

                return $this->failure(
                    self::ERROR_RESEND_WAIT,
                    "ワンタイムパスワードの再送可能時間までお待ちください。{$wait_seconds}秒後に再試行できます。",
                    ['wait_seconds' => $wait_seconds]
                );
            }
        }

        if ($record && CarbonImmutable::instance($record->expires_at)->isFuture()) {
            if ((int) $record->resend_count >= $this->otpMaxResendCount()) {
                return $this->failure(
                    self::ERROR_RESEND_LIMIT_EXCEEDED,
                    'ワンタイムパスワードの再送上限に達しました。時間をおいて再度お試しください。'
                );
            }
        }

        $otp_code = $this->generateOtpCode();
        $expires_at = $now->addMinutes($this->otpExpiresMinutes());

        if (!$record) {
            $record = new RegistrationOtp();
            $record->email = $email;
            $resend_count = 0;
        } else {
            $resend_count = CarbonImmutable::instance($record->expires_at)->isFuture()
                ? ((int) $record->resend_count + 1)
                : 0;
        }

        $record->otp_hash = Hash::make($otp_code);
        $record->attempt_count = 0;
        $record->resend_count = $resend_count;
        $record->last_sent_at = $now;
        $record->expires_at = $expires_at;
        $record->verified_at = null;
        $record->registration_token_hash = null;
        $record->registration_token_expires_at = null;
        $record->save();

        Mail::to($email)->send(new RegistrationOtpMail($otp_code, $expires_at));

        return [
            'success' => true,
            'email' => $email,
            'expires_at' => $expires_at,
            'resend_available_at' => $now->addSeconds($this->otpResendWaitSeconds()),
        ];
    }

    /**
     * OTPを検証して本登録トークンを発行
     *
     * @param string $email
     * @param string $otp_code
     * @return array<string, mixed>
     */
    public function verifyOtp(string $email, string $otp_code): array
    {
        if ($this->isRegisteredEmail($email)) {
            return $this->failure(self::ERROR_EMAIL_ALREADY_REGISTERED, 'このメールアドレスはすでに登録されています。');
        }

        $record = RegistrationOtp::where('email', $email)->first();
        if (!$record) {
            return $this->failure(self::ERROR_OTP_NOT_FOUND, 'ワンタイムパスワードが見つかりません。先にワンタイムパスワード送信を行ってください。');
        }

        $now = CarbonImmutable::now();
        if (CarbonImmutable::instance($record->expires_at)->isPast()) {
            return $this->failure(self::ERROR_OTP_EXPIRED, 'ワンタイムパスワードの有効期限が切れています。再送してください。');
        }

        if ((int) $record->attempt_count >= $this->otpMaxAttempts()) {
            return $this->failure(self::ERROR_OTP_ATTEMPTS_EXCEEDED, 'ワンタイムパスワードの試行回数が上限に達しました。再送してください。');
        }

        if (!Hash::check($otp_code, $record->otp_hash)) {
            $record->attempt_count = (int) $record->attempt_count + 1;
            $record->save();

            if ((int) $record->attempt_count >= $this->otpMaxAttempts()) {
                return $this->failure(self::ERROR_OTP_ATTEMPTS_EXCEEDED, 'ワンタイムパスワードの試行回数が上限に達しました。再送してください。');
            }

            return $this->failure(self::ERROR_OTP_INVALID, 'ワンタイムパスワードが正しくありません。');
        }

        $registration_token = Str::random(64);
        $registration_token_expires_at = $now->addMinutes($this->registrationTokenExpiresMinutes());

        $record->verified_at = $now;
        $record->registration_token_hash = hash('sha256', $registration_token);
        $record->registration_token_expires_at = $registration_token_expires_at;
        $record->save();

        return [
            'success' => true,
            'registration_token' => $registration_token,
            'registration_token_expires_at' => $registration_token_expires_at,
        ];
    }

    /**
     * 検証済みトークンで本登録を完了
     *
     * @param string $registration_token
     * @param string $name
     * @param string $password
     * @return array<string, mixed>
     */
    public function completeRegistration(string $registration_token, string $name, string $password): array
    {
        $record = RegistrationOtp::where('registration_token_hash', hash('sha256', $registration_token))->first();
        if (!$record) {
            return $this->failure(self::ERROR_REGISTRATION_TOKEN_INVALID, '本登録トークンが不正です。');
        }

        $now = CarbonImmutable::now();
        if (
            $record->registration_token_expires_at === null
            || CarbonImmutable::instance($record->registration_token_expires_at)->isPast()
        ) {
            return $this->failure(self::ERROR_REGISTRATION_TOKEN_EXPIRED, '本登録トークンの有効期限が切れています。');
        }

        if ($this->isRegisteredEmail($record->email)) {
            return $this->failure(self::ERROR_EMAIL_ALREADY_REGISTERED, 'このメールアドレスはすでに登録されています。');
        }

        try {
            $user = DB::transaction(function () use ($record, $name, $password, $now) {
                $user = User::create([
                    'name' => $name,
                    'email' => $record->email,
                    'password' => $password,
                    'email_verified_at' => $now,
                    'is_dark_mode' => false,
                    'is_24_hour_format' => true,
                ]);

                $record->delete();

                return $user;
            });
        } catch (QueryException $e) {
            if ($this->isDuplicateEmailException($e)) {
                return $this->failure(self::ERROR_EMAIL_ALREADY_REGISTERED, 'このメールアドレスはすでに登録されています。');
            }

            Log::error('会員登録に失敗しました。', [
                'email' => $record->email,
                'exception' => $e,
            ]);

            return $this->failure(self::ERROR_REGISTRATION_FAILED, '会員登録に失敗しました。');
        } catch (\Throwable $e) {
            Log::error('会員登録に失敗しました。', [
                'email' => $record->email,
                'exception' => $e,
            ]);

            return $this->failure(self::ERROR_REGISTRATION_FAILED, '会員登録に失敗しました。');
        }

        return [
            'success' => true,
            'user' => [
                'user_uuid' => $user->user_uuid,
                'name' => $user->name,
                'email' => $user->email,
                'is_dark_mode' => (bool) $user->is_dark_mode,
                'is_24_hour_format' => (bool) $user->is_24_hour_format,
            ],
        ];
    }

    private function isRegisteredEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    private function otpExpiresMinutes(): int
    {
        return max(1, (int) config('registration.otp.expires_minutes', 10));
    }

    private function otpMaxAttempts(): int
    {
        return max(1, (int) config('registration.otp.max_attempts', 5));
    }

    private function otpMaxResendCount(): int
    {
        return max(0, (int) config('registration.otp.max_resend_count', 3));
    }

    private function otpResendWaitSeconds(): int
    {
        return max(0, (int) config('registration.otp.resend_wait_seconds', 60));
    }

    private function registrationTokenExpiresMinutes(): int
    {
        return max(1, (int) config('registration.token.expires_minutes', 15));
    }

    private function isDuplicateEmailException(QueryException $exception): bool
    {
        $message = $exception->getMessage();
        $error_info = $exception->errorInfo;

        return str_contains($message, 'users_email_unique')
            || str_contains($message, 'Duplicate entry')
            || ((string) $exception->getCode() === '23000')
            || ((isset($error_info[1]) ? (int) $error_info[1] : 0) === 1062);
    }

    private function generateOtpCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $error_code
     * @param string $message
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function failure(string $error_code, string $message, array $extra = []): array
    {
        return array_merge([
            'success' => false,
            'error_code' => $error_code,
            'message' => $message,
        ], $extra);
    }
}
