<?php

namespace App\Utils;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class Upload
{
    public function storeAsset(Request $request, string $keyName)
    {
        $image = $request->file($keyName);
        $imageName = 'storage/' . time().'.'.$image->getClientOriginalExtension();
        $image->storeAs('public', $imageName);
        URL::asset($imageName);

        return $imageName;
    }
}
