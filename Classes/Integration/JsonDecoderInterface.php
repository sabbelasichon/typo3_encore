<?php


namespace Ssch\Typo3Encore\Integration;


interface JsonDecoderInterface
{
    public function decode(string $json): array;
}