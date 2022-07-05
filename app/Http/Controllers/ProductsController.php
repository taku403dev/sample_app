<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Services\ConvertorService;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        /***************** Laravel側で復号化を実施するパターン *************************/
        // 複合・検索結果の商品情報を格納
        $decryptedProducts = [];
        // 商品情報一覧を取得
        $products = Product::all();
        // 複合・検索結果の商品情報を格納
        $decryptedProducts = [];
        // 取得結果に復号化処理
        foreach ($products as $product) {
            // dd($product->getAttributes());

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
        return view(
            'product.index',
            ['products' => $decryptedProducts]
        );
        /**************************************************************************/

        /***************** DB側で復号化を実施するパターン ******************************/
        // 検索キーワード処理(DB側で検索キーワードを絞り込む)
        // $products = Product::SearchEncryptedKeywordOnDB('name', $request->keyword)
        //     // 暗号化された状態で取得するので変換する
        //     ->decryptColumns(['name', 'price', 'info'])->get();
        //  return view(
        //     'product.index',
        //     ['products' => $products]
        // );
        /**************************************************************************/
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
        /********************* 登録(Laravelで暗号化) **************************************/
        $product = new Product;
        $convertedParams = ConvertorService::toEncryptStringFromRequestParameters($request);
        $product->fill($convertedParams)->save();
        /*******************************************************************************/

        /********************* 登録(DB側で暗号化) ****************************************/
        // $app_key = env('APP_KEY');
        // Product::create([
        //     'name' => DB::raw("HEX(AES_ENCRYPT('{$request->name}', '{$app_key}'))"),
        //     'price' => DB::raw("HEX(AES_ENCRYPT('{$request->price}', '{$app_key}'))"),
        //     'info' => DB::raw("HEX(AES_ENCRYPT('{$request->info}', '{$app_key}'))"),
        // ]);
        /******************************************************************************/

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

        /***************************** Laravelで復号化 ************************************/
        $product = Product::find($id);
        try {
            $product->name = Crypt::decryptString($product->name);
            $product->price = Crypt::decryptString($product->price);
            $product->info = Crypt::decryptString($product->info);
            // dd($product);
        } catch (DecryptException $e) {
            echo $e;
        }
        /********************************************************************************/

        /***************************** DB側で復号化 ***************************************/
        // $product =  Product::where('id', $id)->select('*')
        //     ->decryptColumns(['name', 'price', 'info'])->first();
        /********************************************************************************/

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

        /*********************** Laravelで暗号化************************************/
        $product = Product::find($id);

        $convertedParams = [];
        $convertedParams = ConvertorService::toEncryptStringFromRequestParameters($request);
        $product->fill($convertedParams)->save();
        /*************************************************************************/

        /*********************** DB側で暗号化**************************************/
        // $app_key = env('APP_KEY');
        // $product = Product::find($id);
        // $product->fill([
        //     'name' => DB::raw("HEX(AES_ENCRYPT('{$request->name}', '{$app_key}'))"),
        //     'price' => DB::raw("HEX(AES_ENCRYPT('{$request->price}', '{$app_key}'))"),
        //     'info' => DB::raw("HEX(AES_ENCRYPT('{$request->info}', '{$app_key}'))"),
        // ])->save();
        /*************************************************************************/

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
