<?php

namespace Tests\Feature\Controllers\Auth;

use App\Mail\RegistrationOtpMail;
use App\Models\RegistrationOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterWithOtpTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterFlowCompletesAndCanLogin(): void
    {
        Mail::fake();

        $email = 'new-user@example.com';
        $password = 'password123';

        $send_response = $this->postJson(route('auth.register.otp.send'), [
            'email' => $email,
        ]);
        $send_response->assertStatus(200);

        $otp_code = $this->fetchLastOtpCodeFromMail();

        $verify_response = $this->postJson(route('auth.register.otp.verify'), [
            'email' => $email,
            'otp' => $otp_code,
        ]);
        $verify_response->assertStatus(200);

        $registration_token = $verify_response->json('data.registration_token');
        $this->assertNotEmpty($registration_token);

        $complete_response = $this->postJson(route('auth.register.complete'), [
            'registration_token' => $registration_token,
            'name' => 'New User',
            'password' => $password,
        ]);

        $complete_response->assertStatus(201);
        $complete_response->assertJsonPath('data.email', $email);
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'New User',
        ]);

        $login_response = $this->postJson(route('auth.login'), [
            'email' => $email,
            'password' => $password,
        ]);
        $login_response->assertStatus(200);
        $this->assertTrue($login_response->json('result'));
    }

    public function testSendOtpReturnsConflictWhenEmailAlreadyExists(): void
    {
        Mail::fake();

        User::factory()->create([
            'email' => 'exists@example.com',
        ]);

        $response = $this->postJson(route('auth.register.otp.send'), [
            'email' => 'exists@example.com',
        ]);

        $response->assertStatus(409);
        $this->assertFalse($response->json('result'));
    }

    public function testSendOtpGeneratesRegistrationOtpUuid(): void
    {
        Mail::fake();

        $email = 'uuid-check@example.com';

        $this->postJson(route('auth.register.otp.send'), [
            'email' => $email,
        ])->assertStatus(200);

        $registration_otp = RegistrationOtp::where('email', $email)->first();

        $this->assertNotNull($registration_otp);
        $this->assertNotNull($registration_otp->registration_otp_uuid);
        $this->assertTrue(Str::isUuid($registration_otp->registration_otp_uuid));
    }

    public function testVerifyOtpReturnsErrorWhenCodeIsInvalid(): void
    {
        Mail::fake();

        $email = 'verify-failed@example.com';
        $this->postJson(route('auth.register.otp.send'), ['email' => $email])
            ->assertStatus(200);

        $otp_code = $this->fetchLastOtpCodeFromMail();
        $wrong_code = $otp_code === '000000' ? '999999' : '000000';

        $response = $this->postJson(route('auth.register.otp.verify'), [
            'email' => $email,
            'otp' => $wrong_code,
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('result'));
    }

    public function testVerifyOtpReturnsErrorWhenExpired(): void
    {
        Mail::fake();

        $email = 'expired@example.com';
        $this->postJson(route('auth.register.otp.send'), ['email' => $email])
            ->assertStatus(200);

        $otp_code = $this->fetchLastOtpCodeFromMail();

        RegistrationOtp::where('email', $email)
            ->update(['expires_at' => now()->subMinute()]);

        $response = $this->postJson(route('auth.register.otp.verify'), [
            'email' => $email,
            'otp' => $otp_code,
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('result'));
    }

    public function testSendOtpReturnsTooManyRequestsWhenResendWaitIsActive(): void
    {
        Mail::fake();

        $email = 'resend-wait@example.com';
        $this->postJson(route('auth.register.otp.send'), ['email' => $email])
            ->assertStatus(200);

        $response = $this->postJson(route('auth.register.otp.send'), ['email' => $email]);

        $response->assertStatus(429);
        $this->assertFalse($response->json('result'));
    }

    public function testSendOtpReturnsTooManyRequestsWhenResendLimitExceeded(): void
    {
        Mail::fake();

        config()->set('registration.otp.resend_wait_seconds', 0);
        config()->set('registration.otp.max_resend_count', 1);

        $email = 'resend-limit@example.com';

        $this->postJson(route('auth.register.otp.send'), ['email' => $email])
            ->assertStatus(200);
        $this->postJson(route('auth.register.otp.send'), ['email' => $email])
            ->assertStatus(200);

        $response = $this->postJson(route('auth.register.otp.send'), ['email' => $email]);

        $response->assertStatus(429);
        $this->assertFalse($response->json('result'));
    }

    public function testVerifyOtpReturnsTooManyRequestsWhenAttemptsExceeded(): void
    {
        Mail::fake();

        config()->set('registration.otp.max_attempts', 2);

        $email = 'attempt-limit@example.com';

        $this->postJson(route('auth.register.otp.send'), ['email' => $email])
            ->assertStatus(200);

        $this->postJson(route('auth.register.otp.verify'), [
            'email' => $email,
            'otp' => '000000',
        ])->assertStatus(422);

        $response = $this->postJson(route('auth.register.otp.verify'), [
            'email' => $email,
            'otp' => '000000',
        ]);

        $response->assertStatus(429);
        $this->assertFalse($response->json('result'));
    }

    private function fetchLastOtpCodeFromMail(): string
    {
        $otp_code = '';

        Mail::assertSent(RegistrationOtpMail::class, function (RegistrationOtpMail $mail) use (&$otp_code) {
            $otp_code = $mail->otp_code;

            return true;
        });

        return $otp_code;
    }
}
