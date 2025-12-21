<?php

namespace Tests\Unit\Requests\Task;

use App\Http\Requests\Task\ToggleCompleteTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ToggleCompleteTaskRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーション成功のテスト
     */
    #[DataProvider('validDataProvider')]
    public function testValidationPasses(array $data, bool $expected): void
    {
        $request = new ToggleCompleteTaskRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules, $request->messages());

        $this->assertEquals($expected, $validator->passes());
    }

    /**
     * バリデーション失敗のテスト
     */
    #[DataProvider('invalidDataProvider')]
    public function testValidationFails(array $data, array $expected_errors): void
    {
        $request = new ToggleCompleteTaskRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules, $request->messages());

        $this->assertFalse($validator->passes());
        $this->assertEquals($expected_errors, $validator->errors()->toArray());
    }

    /**
     * authorizeメソッドが認証済みユーザーの場合trueを返すことを確認
     */
    public function testAuthorizeReturnsTrueWhenUserAuthenticated(): void
    {
        $user = User::factory()->create();

        $request = new ToggleCompleteTaskRequest();
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
        $request = new ToggleCompleteTaskRequest();
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

        $request = new ToggleCompleteTaskRequest();
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
        $validator = Validator::make(['is_completed' => true], $rules, $request->messages());

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

        $request = new ToggleCompleteTaskRequest();
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
        $validator = Validator::make(['is_completed' => true], $rules, $request->messages());

        // withValidatorでコールバックを登録
        $request->withValidator($validator);

        // 存在しないタスクの場合は404エラーが発生（バリデーション実行時に例外がスローされる）
        $this->expectException(HttpResponseException::class);

        // バリデーションを実行してafterコールバックをトリガー
        $validator->passes();
    }

    /**
     * バリデーション成功のテストケース
     *
     * @return array<string, array<string, mixed>>
     */
    public static function validDataProvider(): array
    {
        return [
            'is_completedがtrue' => [
                [
                    'is_completed' => true,
                ],
                true,
            ],
            'is_completedがfalse' => [
                [
                    'is_completed' => false,
                ],
                true,
            ],
            'is_completedが1' => [
                [
                    'is_completed' => 1,
                ],
                true,
            ],
            'is_completedが0' => [
                [
                    'is_completed' => 0,
                ],
                true,
            ],
        ];
    }

    /**
     * バリデーション失敗のテストケース
     *
     * @return array<string, array<string, mixed>>
     */
    public static function invalidDataProvider(): array
    {
        return [
            '値が空' => [
                [],
                [
                    'is_completed' => ['完了状態は必須です。'],
                ],
            ],
            '値がnull' => [
                [
                    'is_completed' => null,
                ],
                [
                    'is_completed' => ['完了状態は必須です。'],
                ],
            ],
            '値が文字列' => [
                [
                    'is_completed' => 'true',
                ],
                [
                    'is_completed' => ['完了状態は真偽値で入力してください。'],
                ],
            ],
            '値が数値（0,1以外）' => [
                [
                    'is_completed' => 2,
                ],
                [
                    'is_completed' => ['完了状態は真偽値で入力してください。'],
                ],
            ],
        ];
    }
}
