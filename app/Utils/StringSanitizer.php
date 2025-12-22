<?php

namespace App\Utils;

class StringSanitizer
{
    /**
     * DB保存時にエラーを起こす可能性のある文字が含まれていないかチェック
     *
     * @param string|null $string
     * @return bool
     */
    public static function canStoreToDb(?string $string): bool
    {
        if ($string === null) {
            return true;
        }

        if (!mb_check_encoding($string, 'UTF-8')) {
            return false;
        }

        // NULL byte
        return !str_contains($string, "\0");
    }

    /**
     * DB保存時にエラーを起こす可能性のある文字のみを除去
     *
     * @param string $string
     * @return string
     */
    public static function removeDbUnsafeChars(string $string): string
    {
        return str_replace("\0", '', $string);
    }
}
