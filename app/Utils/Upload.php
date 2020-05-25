<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Upload
{
    /**
     * @param Request $request
     * @param string $keyName
     * @return bool
     */
    public function storeAsset(Request $request, string $keyName)
    {
        $image = $request->file($keyName);
        $imageName = Storage::put('storage', $image);

        return $imageName;
    }
}
