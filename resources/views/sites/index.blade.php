@extends('layouts.app')

@section('content')
<div class="mb-5 flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Sites</h1>
    @can('create', App\Models\Site::class)
        <a href="{{ route('sites.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-medium text-white">新規登録</a>
    @endcan
</div>

<form class="mb-4 grid gap-3 rounded-lg border border-zinc-200 bg-white p-4 md:grid-cols-4">
    <input name="keyword" value="{{ request('keyword') }}" class="rounded-md border-zinc-300 text-sm" placeholder="サイト名・企業名・URL">
    <select name="type" class="rounded-md border-zinc-300 text-sm">
        <option value="">サイト種別</option>
        @foreach (['wordpress' => 'WordPress', 'static_html' => '静的HTML', 'php' => 'PHP', 'lp' => 'LP', 'other' => 'その他'] as $value => $label)
            <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="status" class="rounded-md border-zinc-300 text-sm">
        <option value="">ステータス</option>
        @foreach (['normal' => '正常', 'needs_check' => '要確認', 'updates_available' => '更新あり', 'incident' => '障害', 'suspended' => '保守停止'] as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-medium">検索</button>
</form>

<div class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-zinc-100 text-zinc-600">
            <tr>
                <th class="px-4 py-3">サイト名</th>
                <th class="px-4 py-3">URL</th>
                <th class="px-4 py-3">種別</th>
                <th class="px-4 py-3">ステータス</th>
                <th class="px-4 py-3">WP</th>
                <th class="px-4 py-3">PHP</th>
                <th class="px-4 py-3">自動確認</th>
                <th class="px-4 py-3">最終保守</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @foreach ($sites as $site)
                <tr>
                    <td class="px-4 py-3"><a class="font-medium hover:underline" href="{{ route('sites.show', $site) }}">{{ $site->company_name }}<br><span class="text-zinc-500">{{ $site->name }}</span></a></td>
                    <td class="px-4 py-3">{{ $site->url }}</td>
                    <td class="px-4 py-3">{{ $site->type->value }}</td>
                    <td class="px-4 py-3">{{ $site->status->value }}</td>
                    <td class="px-4 py-3">{{ $site->wordpressInformation?->wordpress_version ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $site->serverInformation?->php_version ?? $site->wordpressInformation?->php_version ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div>{{ $site->last_checked_at?->format('Y/m/d H:i') ?? '未実行' }}</div>
                        <div class="text-xs text-zinc-500">{{ $site->auto_status_reason ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $site->last_maintained_on?->format('Y/m/d') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $sites->links() }}</div>
@endsection
