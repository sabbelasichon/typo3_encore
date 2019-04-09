<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\Integration;


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