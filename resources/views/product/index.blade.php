@extends('adminlte::page')

@section('title', '商品一覧')

@section('content_header')
<h1>商品一覧</h1>
@stop

@section('content')
{{-- 完了メッセージ --}}
@if (session('message'))
<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
        ×
    </button>
    {{ session('message') }}
</div>
@endif

<div class="row ml-1">
    {{-- 新規登録画面 --}}
    <a class="btn btn-primary mb-2 mr-2" href="{{ route('product.create') }}" role="button">新規登録</a>
    {{-- 商品説明検索 --}}
    <form class="form-inline mt-2 mb-2 mt-md-0" method="GET" route>
        <input class="form-control mr-sm-2" type="text" name="keyword" placeholder="商品名入力" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">検索</button>
    </form>
</div>
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>商品説明</th>
                    <th style="width: 70px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    {{-- 数字フォーマット --}}
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->info }}</td>
                    <td>
                        <a class="btn btn-primary btn-sm mb-2" href="{{ route('product.edit', $product->id) }}"
                            role="button">編集</a>
                        <form action="{{ route('product.destroy', $product->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            {{-- 簡易的に確認メッセージを表示 --}}
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('削除してもよろしいですか?');">
                                削除
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{-- ページネーション --}}
{{-- @if ($products->hasPages())
<div class="card-footer clearfix">
    {{ $products->links() }}
</div>
@endif --}}
</div>
@stop
