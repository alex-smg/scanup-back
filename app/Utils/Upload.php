<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class Upload
{
    /**
     * @param Request $request
     * @param string $keyName
     * @return string
     */
    public function storeAsset(Request $request, string $keyName): string
    {
        $image = $request->file($keyName);
        $imageName = 'storage/' . time().'.'.$image->getClientOriginalExtension();
        $image->storeAs('public', $imageName);
        URL::asset($imageName);

        return $imageName;
    }
}
