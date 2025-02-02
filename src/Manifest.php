<?php

namespace Tonysm\TailwindCss;

use Exception;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Manifest
{
    public function __invoke(string $path)
    {
        static $manifests = [];

        if (! Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }

        $manifestPath = public_path('/tailwindcss-manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! is_file($manifestPath)) {
                throw new Exception('The Tailwind CSS manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (! isset($manifest[$path])) {
            $exception = new Exception("Unable to locate Tailwind CSS compiled file: {$path}.");

            if (! app('config')->get('app.debug')) {
                report($exception);

                return $path;
            } else {
                throw $exception;
            }
        }

        return new HtmlString(asset($manifest[$path]));
    }
}
