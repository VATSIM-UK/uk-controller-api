<?php

namespace App\Filament\Resources;

trait TranslatesStrings
{
    public static function translateTablePath(string $path)
    {
        return static::translatePath('table', $path);
    }

    private static function translatePath(string $type, string $path)
    {
        return __(
            sprintf('%s.%s.%s', $type, static::translationPathRoot(), $path)
        );
    }

    /**
     * Returns the root of the translation path for the relations manager, to build
     * labels etc.
     *
     * @return string
     */
    abstract protected static function translationPathRoot(): string;
}
