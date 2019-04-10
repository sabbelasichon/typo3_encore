<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\Integration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

final class JsonDecoder implements JsonDecoderInterface
{

    public function decode(string $json): array
    {
        // In PHP 7.3 you can use JSON_THROW_ON_ERROR constant
        $array = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodeException(json_last_error_msg());
        }

        return $array;
    }

}