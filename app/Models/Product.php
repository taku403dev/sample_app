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
}
