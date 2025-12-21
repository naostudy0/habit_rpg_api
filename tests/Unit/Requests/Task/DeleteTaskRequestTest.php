<?php

namespace Tests\Unit\Requests\Task;

use App\Http\Requests\Task\DeleteTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class DeleteTaskRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * authorizeメソッドが認証済みユーザーの場合trueを返すことを確認
     */
    public function testAuthorizeReturnsTrueWhenUserAuthenticated(): void
    {
        $user = User::factory()->create();

        $request = new DeleteTaskRequest();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->assertTrue($request->authorize());
    }

    /**
     * authorizeメソッドが未認証ユーザーの場合falseを返すことを確認
     */
    public function testAuthorizeReturnsFalseWhenUserNotAuthenticated(): void
    {
        $request = new DeleteTaskRequest();
        $request->setUserResolver(function () {
            return null;
        });

        $this->assertFalse($request->authorize());
    }

    /**
     * 存在確認が正しく動作すること
     */
    public function testWithValidatorChecksTaskExistence(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        $request = new DeleteTaskRequest();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->setRouteResolver(function () use ($task) {
            return new class ($task->task_uuid) {
                public function __construct(private string $uuid)
                {
                }

                public function parameter(string $key): ?string
                {
                    return $key === 'uuid' ? $this->uuid : null;
                }
            };
        });

        $rules = $request->rules();
        $validator = Validator::make([], $rules, $request->messages());

        // withValidatorが呼ばれるようにする
        $request->withValidator($validator);

        // 存在するタスクの場合はバリデーションが通る
        $this->assertTrue($validator->passes());
    }

    /**
     * 存在しないタスクの場合は404エラーが発生すること
     */
    public function testWithValidatorThrows404WhenTaskNotExists(): void
    {
        $user = User::factory()->create();

        $request = new DeleteTaskRequest();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->setRouteResolver(function () {
            return new class {
                public function parameter(string $key): ?string
                {
                    return $key === 'uuid' ? 'non-existent-uuid' : null;
                }
            };
        });

        $rules = $request->rules();
        $validator = Validator::make([], $rules, $request->messages());

        // withValidatorでコールバックを登録
        $request->withValidator($validator);

        // 存在しないタスクの場合は404エラーが発生（バリデーション実行時に例外がスローされる）
        $this->expectException(HttpResponseException::class);

        // バリデーションを実行してafterコールバックをトリガー
        $validator->passes();
    }
}
