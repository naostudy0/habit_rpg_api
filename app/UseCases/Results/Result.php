<?php

namespace App\UseCases\Results;

use App\UseCases\Outputs\Output;

/**
 * UseCaseの実行結果を表す共通Result。
 */
final class Result
{
    private bool $is_success;
    private ?Output $output;
    private ?string $error_code;
    private ?string $error_message;

    private function __construct(
        bool $is_success,
        ?Output $output,
        ?string $error_code,
        ?string $error_message
    ) {
        $this->is_success = $is_success;
        $this->output = $output;
        $this->error_code = $error_code;
        $this->error_message = $error_message;
    }

    public static function success(?Output $output = null): self
    {
        return new self(true, $output, null, null);
    }

    public static function failure(string $error_code, string $error_message): self
    {
        return new self(false, null, $error_code, $error_message);
    }

    public function isSuccess(): bool
    {
        return $this->is_success;
    }

    public function getOutput(): ?Output
    {
        return $this->output;
    }

    public function getErrorCode(): ?string
    {
        return $this->error_code;
    }

    public function getErrorMessage(): ?string
    {
        return $this->error_message;
    }
}
