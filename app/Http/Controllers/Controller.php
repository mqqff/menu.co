<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    protected $userId = 'db4ff9bc-47f9-44d6-8d0d-f9b419c21159';
    protected $userName = 'John Doe';
    protected $username = 'johndoe';
    protected $userAvatar = "https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YXZhdGFyfGVufDB8fDB8fHww&auto=format&fit=crop&w=500&q=60";

    protected function getJson(string $path, bool $assoc = false)
    {
        if (!Storage::exists($path)) {
            return null;
        }

        return json_decode(Storage::get($path), $assoc);
    }
}
