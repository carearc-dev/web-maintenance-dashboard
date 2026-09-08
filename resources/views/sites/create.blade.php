@extends('layouts.app')

@section('content')
<div class="mb-5">
    <a href="{{ route('sites.index') }}" class="text-sm text-zinc-500 hover:text-zinc-950">戻る</a>
    <h1 class="mt-2 text-2xl font-semibold">サイトを新規作成</h1>
    <p class="mt-1 text-sm text-zinc-600">URL登録後、自動チェックで取得できる状態を反映します。</p>
</div>

<form method="post" action="{{ route('sites.store') }}" class="rounded-lg border border-zinc-200 bg-white p-5">
    @include('sites._form')
</form>
@endsection
