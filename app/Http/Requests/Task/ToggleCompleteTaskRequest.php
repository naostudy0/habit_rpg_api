<?php

namespace App\Http\Requests\Task;

use App\Services\TaskService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ToggleCompleteTaskRequest extends FormRequest
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
            'is_completed' => ['required', 'boolean'],
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
            'is_completed.required' => '完了状態は必須です。',
            'is_completed.boolean' => '完了状態は真偽値で入力してください。',
        ];
    }

    /**
     * バリデーション実行前の処理
     * タスクの存在確認を行う
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $uuid = $this->route('uuid');
            $user_id = $this->user()->user_id;

            $task_service = app(TaskService::class);
            $exists = $task_service->existsByUuidAndUserId($uuid, $user_id);

            if (!$exists) {
                throw new HttpResponseException(
                    response()->json([
                        'result' => false,
                        'message' => '予定が見つかりません',
                    ], 404)
                );
            }
        });
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
