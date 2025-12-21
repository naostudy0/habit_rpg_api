<?php

namespace App\Http\Requests\Task;

use Illuminate\Contracts\Validation\Validator;

class StoreTaskRequest extends BaseTaskRequest
{
    /**
     * バリデーション実行前の処理
     * 存在確認を無効化（作成時は存在確認不要）
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        // 存在確認は不要のため継承元のメソッドをオーバーライド
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
}
