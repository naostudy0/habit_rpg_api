<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\CompleteRegistrationRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SendRegistrationOtpRequest;
use App\Http\Requests\Auth\VerifyRegistrationOtpRequest;
use App\Http\Resources\Auth\CompleteRegistrationResource;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\SendRegistrationOtpResource;
use App\Http\Resources\Auth\VerifyRegistrationOtpResource;
use App\UseCases\Auth\CompleteRegistrationInput;
use App\UseCases\Auth\CompleteRegistrationUseCase;
use App\UseCases\Auth\LoginInput;
use App\UseCases\Auth\LoginUseCase;
use App\UseCases\Auth\SendRegistrationOtpInput;
use App\UseCases\Auth\SendRegistrationOtpUseCase;
use App\UseCases\Auth\VerifyRegistrationOtpInput;
use App\UseCases\Auth\VerifyRegistrationOtpUseCase;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private LoginUseCase $login_use_case;
    private SendRegistrationOtpUseCase $send_registration_otp_use_case;
    private VerifyRegistrationOtpUseCase $verify_registration_otp_use_case;
    private CompleteRegistrationUseCase $complete_registration_use_case;

    public function __construct(
        LoginUseCase $login_use_case,
        SendRegistrationOtpUseCase $send_registration_otp_use_case,
        VerifyRegistrationOtpUseCase $verify_registration_otp_use_case,
        CompleteRegistrationUseCase $complete_registration_use_case
    ) {
        $this->login_use_case = $login_use_case;
        $this->send_registration_otp_use_case = $send_registration_otp_use_case;
        $this->verify_registration_otp_use_case = $verify_registration_otp_use_case;
        $this->complete_registration_use_case = $complete_registration_use_case;
    }

    /**
     * ログイン処理
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $input = new LoginInput(
            $credentials['email'],
            $credentials['password']
        );
        $result = $this->login_use_case->handle($input);

        return LoginResource::fromResult($result);
    }

    /**
     * 会員登録用OTP送信
     *
     * @param SendRegistrationOtpRequest $request
     * @return JsonResponse
     */
    public function sendRegistrationOtp(SendRegistrationOtpRequest $request): JsonResponse
    {
        $input = new SendRegistrationOtpInput($request->validated()['email']);
        $result = $this->send_registration_otp_use_case->handle($input);

        return SendRegistrationOtpResource::fromResult($result);
    }

    /**
     * 会員登録用OTP検証
     *
     * @param VerifyRegistrationOtpRequest $request
     * @return JsonResponse
     */
    public function verifyRegistrationOtp(VerifyRegistrationOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $input = new VerifyRegistrationOtpInput(
            $validated['email'],
            $validated['otp']
        );
        $result = $this->verify_registration_otp_use_case->handle($input);

        return VerifyRegistrationOtpResource::fromResult($result);
    }

    /**
     * 会員登録完了
     *
     * @param CompleteRegistrationRequest $request
     * @return JsonResponse
     */
    public function completeRegistration(CompleteRegistrationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $input = new CompleteRegistrationInput(
            $validated['registration_token'],
            $validated['name'],
            $validated['password']
        );
        $result = $this->complete_registration_use_case->handle($input);

        return CompleteRegistrationResource::fromResult($result);
    }
}
