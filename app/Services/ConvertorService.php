<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ConvertorService
{

    /**
     * リクエストパラメーターを暗号化する
     *
     * @param Request $request
     * @return array
     */
    public static function toEncryptStringFromRequestParameters(Request $request): array
    {

        $convertedParams = [];
        foreach ($request->all() as $key => $val) {
            // トークンパラメーターは除外
            if ($key == '_token') continue;
            $convertedParams[$key] = Crypt::encryptString($val);
        }

        return $convertedParams;
    }

}
