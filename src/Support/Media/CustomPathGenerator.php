<?php

namespace Common\Support\Media;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * The callback that should be used to get base path.
     */
    public static ?Closure $basePathCallback;

    /**
     * Set a callback that should be used to create base path.
     */
    public static function basePathUsing($callback): void
    {
        static::$basePathCallback = $callback;
    }

    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media).'/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media).'/conversions/';
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media).'/responsive-images/';
    }

    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        if (static::$basePathCallback) {
            return call_user_func(static::$basePathCallback, $media);
        }

        $environment = App::environment();

        $prefix = config('media-library.prefix', '');

        return Str::ltrim("{$prefix}/{$environment}/{$media->id}", '/');
    }
}
