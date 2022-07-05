<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Services\ConvertorService;
use Illuminate\Contracts\Encryption\DecryptException;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // 複合・検索結果の商品情報を格納
        $decryptedProducts = [];
        // 商品情報一覧を取得
        $products = Product::all();
        // 複合・検索結果の商品情報を格納
        $decryptedProducts = [];

        // 取得結果に復号化処理
        foreach ($products as $product) {
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

        // 画面に表示
        return view(
            'product.index',
            ['products' => $decryptedProducts]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = new Product;
        $convertedParams = ConvertorService::toEncryptStringFromRequestParameters($request);

        $product->fill($convertedParams)->save();

        return redirect()->route('product.index')->with('message', '登録しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        try {
            $product->name = Crypt::decryptString($product->name);
            $product->price = Crypt::decryptString($product->price);
            $product->info = Crypt::decryptString($product->info);
            // dd($product);
        } catch (DecryptException $e) {
            echo $e;
        }

        return view('product.edit', [
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);

        $convertedParams = [];
        $convertedParams = ConvertorService::toEncryptStringFromRequestParameters($request);
        $product->fill($convertedParams)->save();

        // 一覧へ戻り完了メッセージを表示
        return redirect()->route('product.index')->with('message', '編集しました');
    }

    /**
    * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::where('id', $id)->delete();

        // 完了メッセージを表示
        return redirect()->route('product.index')->with('message', '削除しました');
    }
}
