<?php

namespace Tests\Unit\Requests\Task;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StoreTaskRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーション成功のテスト
     */
    #[DataProvider('validDataProvider')]
    public function testValidationPasses(array $data, bool $expected): void
    {
        $request = new StoreTaskRequest();
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
        $request = new StoreTaskRequest();
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

        $request = new StoreTaskRequest();
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
        $request = new StoreTaskRequest();
        $request->setUserResolver(function () {
            return null;
        });

        $this->assertFalse($request->authorize());
    }


    /**
     * バリデーション成功のテストケース
     *
     * @return array<string, array<string, mixed>>
     */
    public static function validDataProvider(): array
    {
        return [
            '正常なデータ' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00:00',
                    'memo' => 'テストメモ',
                ],
                true,
            ],
            'メモがnull' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00:00',
                    'memo' => null,
                ],
                true,
            ],
            'メモが空文字列' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00:00',
                    'memo' => '',
                ],
                true,
            ],
            'タイトルが255文字' => [
                [
                    'title' => str_repeat('a', 255),
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00:00',
                ],
                true,
            ],
            'メモが1000文字' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00:00',
                    'memo' => str_repeat('a', 1000),
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
                    'title' => ['タイトルは必須です。'],
                    'scheduled_date' => ['予定日は必須です。'],
                    'scheduled_time' => ['予定時刻は必須です。'],
                ],
            ],
            'titleがnull' => [
                [
                    'title' => null,
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00:00',
                ],
                [
                    'title' => ['タイトルは必須です。'],
                ],
            ],
            'titleが256文字以上' => [
                [
                    'title' => str_repeat('a', 256),
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00:00',
                ],
                [
                    'title' => ['タイトルは255文字以内で入力してください。'],
                ],
            ],
            'scheduled_dateが不正な形式' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025/12/20',
                    'scheduled_time' => '10:00:00',
                ],
                [
                    'scheduled_date' => ['有効な日付を入力してください。'],
                ],
            ],
            'scheduled_timeが不正な形式' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00',
                ],
                [
                    'scheduled_time' => ['有効な時刻形式（HH:MM:SS）で入力してください。'],
                ],
            ],
            'scheduled_timeが24時を超える' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '24:00:00',
                ],
                [
                    'scheduled_time' => ['有効な時刻形式（HH:MM:SS）で入力してください。'],
                ],
            ],
            'scheduled_timeが60分を超える' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:60:00',
                ],
                [
                    'scheduled_time' => ['有効な時刻形式（HH:MM:SS）で入力してください。'],
                ],
            ],
            'メモが1001文字以上' => [
                [
                    'title' => 'テスト予定',
                    'scheduled_date' => '2025-12-20',
                    'scheduled_time' => '10:00:00',
                    'memo' => str_repeat('a', 1001),
                ],
                [
                    'memo' => ['メモは1000文字以内で入力してください。'],
                ],
            ],
        ];
    }
}
