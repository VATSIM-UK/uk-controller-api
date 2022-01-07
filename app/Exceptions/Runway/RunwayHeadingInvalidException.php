<?php

namespace App\Exceptions\Runway;

class RunwayHeadingInvalidException extends RunwayInvalidException
{
    public static function forHeading(int $heading): RunwayHeadingInvalidException
    {
        return new static(sprintf('Runway heading %d is not valid', $heading));
    }

    public static function forHeadings(int $first, int $second): RunwayHeadingInvalidException
    {
        return new static(sprintf('Runway headings %d and %d are not inverse', $first, $second));
    }
}
