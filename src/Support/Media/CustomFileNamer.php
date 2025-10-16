<?php

namespace Common\Support\Media;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

class CustomFileNamer extends FileNamer
{
    /**
     * Generate a new file name for the original file.
     */
    public function originalFileName(string $fileName): string
    {
        return Str::random(40);
    }

    /**
     * Generate a new file name for a conversion of the file.
     */
    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $strippedFileName = pathinfo($fileName, PATHINFO_FILENAME);

        return "{$strippedFileName}-{$conversion->getName()}";
    }

    /**
     * Generate a new file name for a responsive variant of the file.
     */
    public function responsiveFileName(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }
}
