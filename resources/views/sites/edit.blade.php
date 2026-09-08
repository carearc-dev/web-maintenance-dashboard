@extends('layouts.app')

@section('content')
<div class="mb-5">
    <a href="{{ route('sites.show', $site) }}" class="text-sm text-zinc-500 hover:text-zinc-950">戻る</a>
    <h1 class="mt-2 text-2xl font-semibold">サイト情報を編集</h1>
    <p class="mt-1 text-sm text-zinc-600">{{ $site->name }}</p>
</div>

<form method="post" action="{{ route('sites.update', $site) }}" class="rounded-lg border border-zinc-200 bg-white p-5">
    @method('put')
    @include('sites._form', ['site' => $site])
</form>
@endsection
