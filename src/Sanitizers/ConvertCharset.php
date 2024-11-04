<?php

namespace Rahul900day\Csv\Sanitizers;

use Closure;

class ConvertCharset
{
    const FROM_CHARSET = 'windows-1251';
    const TO_CHARSET = 'utf-8';

    public function __invoke(string $cell, Closure $next): mixed
    {
        return $next(iconv(self::FROM_CHARSET, self::TO_CHARSET, $cell));
    }
}
