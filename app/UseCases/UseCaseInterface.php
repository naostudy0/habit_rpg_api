<?php

namespace App\UseCases;

use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;

/**
 * UseCaseの共通インターフェース。
 */
interface UseCaseInterface
{
    public function handle(Input $input): Result;
}
