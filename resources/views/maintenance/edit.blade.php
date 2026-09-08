@extends('layouts.app')

@section('content')
<div class="mb-5">
    <a href="{{ route('maintenance.index') }}" class="text-sm text-zinc-500 hover:text-zinc-950">戻る</a>
    <h1 class="mt-2 text-2xl font-semibold">作業記録を編集</h1>
    <p class="mt-1 text-sm text-zinc-600">{{ $log->site?->name }}</p>
</div>

<form method="post" action="{{ route('maintenance-logs.update', $log) }}" class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 md:grid-cols-2">
    @csrf
    @method('put')

    <label class="text-sm">作業日
        <input name="worked_on" type="date" value="{{ old('worked_on', $log->worked_on?->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm">カテゴリ
        <input name="category" value="{{ old('category', $log->category) }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm md:col-span-2">作業内容
        <textarea name="description" rows="5" class="mt-1 w-full rounded-md border-zinc-300 text-sm">{{ old('description', $log->description) }}</textarea>
    </label>
    <label class="text-sm">対応前バージョン
        <input name="version_before" value="{{ old('version_before', $log->version_before) }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm">対応後バージョン
        <input name="version_after" value="{{ old('version_after', $log->version_after) }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm">作業時間（分）
        <input name="minutes_spent" type="number" min="0" value="{{ old('minutes_spent', $log->minutes_spent) }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm">GitHub URL
        <input name="repository_url" value="{{ old('repository_url', $log->repository_url) }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm md:col-span-2">備考
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-zinc-300 text-sm">{{ old('notes', $log->notes) }}</textarea>
    </label>

    <div class="md:col-span-2">
        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white">保存</button>
    </div>
</form>
@endsection
