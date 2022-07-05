<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Product;
use PhpParser\Node\Stmt\Continue_;

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

    public function toDecrypt($collections, array $columns)
    {

        // 複合・検索結果の商品情報を格納
        $decryptedProducts = [];
        // 商品情報一覧を取得
        $products = Product::all();
        // 複合・検索結果の商品情報を格納
        $decryptedProducts = [];

        // 取得結果に復号化処理
        foreach ($collections as $collection) {

            $attrs = $collection->getAttributes();
            foreach($columns as $col) {
                if($attrs[$col] === false) continue;
                $collection->price = Crypt::decryptString($product->price);
            }
            try {
                // 商品名復号化
                $name = Crypt::decryptString($product->name);
                // 復号化したデータに対し検索キーワードが引っかからない場合
                if (strpos($name, $request->keyword) === false) continue;

                // 復号化変換処理
                $product->name = $name;
                $product->price = Crypt::decryptString($product->price);
                $product->info = Crypt::decryptString($product->info);

                // フィルター結果のデータを追加
                array_push($decryptedProducts, $product);
            } catch (DecryptException $e) {
                echo $e;
            }
        }
    }
}
