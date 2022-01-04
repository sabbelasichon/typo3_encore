<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

final class JsonDecoder implements JsonDecoderInterface
{
    public function decode(string $json): array
    {
        // In PHP 7.3 you can use JSON_THROW_ON_ERROR constant
        $array = json_decode($json, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonDecodeException(json_last_error_msg());
        }

        return (array) $array;
    }
}
