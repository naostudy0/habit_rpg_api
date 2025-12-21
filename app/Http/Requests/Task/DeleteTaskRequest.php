<?php

namespace App\Http\Requests\Task;

class DeleteTaskRequest extends BaseTaskRequest
{
    /**
     * バリデーションルール
     * 削除リクエストでは追加のバリデーションルールは不要
     * UUIDの存在確認はBaseTaskRequestのwithValidatorで行われる
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * バリデーションエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }
}
