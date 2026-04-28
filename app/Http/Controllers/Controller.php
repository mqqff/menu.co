<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    protected function formatCookTime($cookTime)
    {
        if ($cookTime >= 1440) {
            return ceil($cookTime / 1440) . ' days';
        } elseif ($cookTime >= 60) {
            return ceil($cookTime / 60) . ' hours';
        }

        return $cookTime . ' minutes';
    }
}
