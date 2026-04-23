<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    protected function getJson(string $path, bool $assoc = false)
    {
        if (!Storage::exists($path)) {
            return null;
        }

        return json_decode(Storage::get($path), $assoc);
    }
}
