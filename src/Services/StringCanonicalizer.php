<?php

namespace Cybalex\OauthServer\Services;

class StringCanonicalizer
{
    public function canonicalize(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        $encoding = \mb_detect_encoding($string);

        return $encoding
            ? \mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : \mb_convert_case($string, MB_CASE_LOWER);
    }
}
