<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Http\Requests\ProductRequest;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        /***************** DB側で復号化を実施するパターン ******************************/
        // 検索キーワード処理(DB側で検索キーワードを絞り込む)
        $products = Product::SearchEncryptedKeywordOnDB('name', $request->keyword)
            // 暗号化された状態で取得するので変換する
            ->decryptColumns(['name', 'price', 'info'])->get();
        /**************************************************************************/

        // 画面に表示
        return view(
            'product.index',
            ['products' => $products]
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
        $app_key = env('APP_KEY');

        // 登録(DB側で暗号化)
        Product::create([
            'name' => DB::raw("HEX(AES_ENCRYPT('{$request->name}', '{$app_key}'))"),
            'price' => DB::raw("HEX(AES_ENCRYPT('{$request->price}', '{$app_key}'))"),
            'info' => DB::raw("HEX(AES_ENCRYPT('{$request->info}', '{$app_key}'))"),
        ]);

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
        $app_key = env('APP_KEY');

        /***************************** DB側で復号化 ***************************************/
        $product =  Product::where('id', $id)->select('*')
            ->decryptColumns(['name', 'price', 'info'])->first();

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
        $app_key = env('APP_KEY');
        $product = Product::find($id);

        $product->fill([
            'name' => DB::raw("HEX(AES_ENCRYPT('{$request->name}', '{$app_key}'))"),
            'price' => DB::raw("HEX(AES_ENCRYPT('{$request->price}', '{$app_key}'))"),
            'info' => DB::raw("HEX(AES_ENCRYPT('{$request->info}', '{$app_key}'))"),
        ])->save();

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
