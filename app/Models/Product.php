<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'info',
    ];

    /**
     * 検索したキーワード結果のクエリを取得する
     *
     * @param  $query
     * @param  $keyword 検索文字列
     * @return $query or void 検索結果のクエリ
     */
    public function scopeSearchKeyword($query, $keyword)
    {
        $app_key = env('APP_KEY');
        // 検索文字列の存在チェック
        if (!is_null($keyword)) {

            // 全角スペースを半角に変換
            $spaceConvert = mb_convert_kana($keyword, 's');
            // 空白で区切る
            $keywords = \preg_split('/[\s]+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY);

            // 含まれている単語を検索する
            foreach ($keywords as $keyword) {
                $query->where('products.name', 'like', '%' . $keyword . '%');
            }

            return $query;
        } else {
            return;
        }
    }

    /**
     * 暗号化されたキーワード文字列を検索する
     *
     * @param  $query
     * @param  $column 検索列
     * @param  $keyword 検索文字列
     * @return $query or void 検索結果のクエリ
     */
    public function scopeSearchEncryptedKeywordOnDB($query, $column, $keyword)
    {
        $app_key = env('APP_KEY');
        // 検索文字列の存在チェック
        if (!is_null($keyword)) {
            // 含まれている単語を検索する
            $query->select('*')
                ->whereRaw("CONVERT(AES_DECRYPT(UNHEX(`${column}`), '{$app_key}') USING utf8) LIKE '%{$keyword}%'");
        } else {
            $query->select('*');
        }

        return;
    }
    /**
     * 暗号化されているデータ列を復号化する
     *
     * @param [type] $query
     * @param array $復号化したい列名を配列で指定する
     * @return void
     */
    public function scopeDecryptColumns($query, array $columns)
    {

        $app_key = env('APP_KEY');
        foreach($columns as $column) {
            $query->selectRaw(
                "CONVERT(AES_DECRYPT(UNHEX(`{$column}`), '{$app_key}') USING utf8) as ${column}"
            );
        }
   }
}
