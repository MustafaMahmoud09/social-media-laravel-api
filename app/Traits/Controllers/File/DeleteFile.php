<?php

namespace App\Traits\Controllers\File;

use Illuminate\Support\Facades\Storage;

trait DeleteFile
{
    function deleteFile($path)
    {
        Storage::disk('public')->delete($path);
    }
}
