<?php

namespace App\Utilities;

class UuidGenerator
{
    public static function generateUUID16bit()
    {
        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(
                bin2hex(
                    random_bytes(
                        16
                    )
                ),
                4
            )
        );
    }
}
