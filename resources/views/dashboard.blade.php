@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold">Dashboard</h1>
        <p class="mt-1 text-sm text-zinc-600">管理サイトの現在状態を一覧できます。</p>
    </div>
</div>

<div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
    <div class="rounded-lg border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">管理サイト数</div><div class="mt-2 text-3xl font-semibold">{{ $siteCount }}</div></div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">正常</div><div class="mt-2 text-3xl font-semibold text-emerald-700">{{ $normalCount }}</div></div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">要確認</div><div class="mt-2 text-3xl font-semibold text-amber-700">{{ $needsCheckCount }}</div></div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">更新あり</div><div class="mt-2 text-3xl font-semibold text-sky-700">{{ $updatesCount }}</div></div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">障害</div><div class="mt-2 text-3xl font-semibold text-red-700">{{ $incidentCount }}</div></div>
</div>

<section class="mt-8">
    <h2 class="mb-3 text-lg font-semibold">最近保守したサイト</h2>
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-100 text-zinc-600">
                <tr>
                    <th class="px-4 py-3">サイト名</th>
                    <th class="px-4 py-3">企業名</th>
                    <th class="px-4 py-3">ステータス</th>
                    <th class="px-4 py-3">最終保守</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @foreach ($recentSites as $site)
                    <tr>
                        <td class="px-4 py-3"><a class="font-medium text-zinc-950 hover:underline" href="{{ route('sites.show', $site) }}">{{ $site->name }}</a></td>
                        <td class="px-4 py-3">{{ $site->company_name }}</td>
                        <td class="px-4 py-3">{{ $site->status->value }}</td>
                        <td class="px-4 py-3">{{ $site->last_maintained_on?->format('Y/m/d') ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
