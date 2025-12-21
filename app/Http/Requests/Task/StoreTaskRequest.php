<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date_format:Y-m-d'],
            'scheduled_time' => ['required', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'memo' => ['nullable', 'string', 'max:1000'],
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
            'title.required' => 'タイトルは必須です。',
            'title.string' => 'タイトルは文字列で入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'scheduled_date.required' => '予定日は必須です。',
            'scheduled_date.date_format' => '有効な日付を入力してください。',
            'scheduled_time.required' => '予定時刻は必須です。',
            'scheduled_time.regex' => '有効な時刻形式（HH:MM:SS）で入力してください。',
            'memo.string' => 'メモは文字列で入力してください。',
            'memo.max' => 'メモは1000文字以内で入力してください。',
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
