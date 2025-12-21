<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    /**
     * 認証の有無
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * 認証失敗時の処理
     *
     * @return void
     * @throws HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            response()->json([
                'result' => false,
                'message' => '認証が必要です。',
            ], 401)
        );
    }

    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8'],
            'is_dark_mode' => ['sometimes', 'boolean'],
            'is_24_hour_format' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * バリデーションエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => '名前は文字列で入力してください。',
            'name.max' => '名前は255文字以内で入力してください。',
            'password.string' => 'パスワードは文字列で入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'is_dark_mode.boolean' => 'ダークモード設定は真偽値で入力してください。',
            'is_24_hour_format.boolean' => '24時間形式設定は真偽値で入力してください。',
        ];
    }

    /**
     * バリデーション失敗時の処理
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            response()->json([
                'result' => false,
                'message' => 'バリデーションエラーが発生しました。',
                'errors' => $errors,
            ], 422)
        );
    }
}
