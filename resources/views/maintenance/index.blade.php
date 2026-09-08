@extends('layouts.app')

@section('content')
<div class="mb-5">
    <h1 class="text-2xl font-semibold">Maintenance</h1>
    <p class="mt-1 text-sm text-zinc-600">ウェブ管理シートの保守履歴を確認する画面です。</p>
</div>

<form class="mb-4 flex gap-3 rounded-lg border border-zinc-200 bg-white p-4">
    <input name="keyword" value="{{ request('keyword') }}" class="flex-1 rounded-md border-zinc-300 text-sm" placeholder="サイト名・作業内容・カテゴリ">
    <button class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-medium">検索</button>
</form>

<div class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-zinc-100 text-zinc-600">
            <tr>
                <th class="px-4 py-3">作業日</th>
                <th class="px-4 py-3">サイト</th>
                <th class="px-4 py-3">カテゴリ</th>
                <th class="px-4 py-3">作業内容</th>
                <th class="px-4 py-3">担当</th>
                <th class="px-4 py-3">時間</th>
                <th class="px-4 py-3">操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @forelse ($logs as $log)
                <tr>
                    <td class="px-4 py-3">{{ $log->worked_on?->format('Y/m/d') }}</td>
                    <td class="px-4 py-3">{{ $log->site?->name }}</td>
                    <td class="px-4 py-3">{{ $log->category }}</td>
                    <td class="px-4 py-3">{{ $log->description }}</td>
                    <td class="px-4 py-3">{{ $log->user?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $log->minutes_spent ? $log->minutes_spent.'分' : '-' }}</td>
                    <td class="px-4 py-3">
                        @can('update', $log->site)
                            <a class="text-sm font-medium text-zinc-900 hover:underline" href="{{ route('maintenance-logs.edit', $log) }}">編集</a>
                        @endcan
                        @can('delete', $log->site)
                            <form class="mt-2" method="post" action="{{ route('maintenance-logs.destroy', $log) }}" onsubmit="return confirm('この作業記録を削除しますか？')">
                                @csrf
                                @method('delete')
                                <button class="text-sm font-medium text-red-700">削除</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-zinc-500">保守履歴はまだありません。</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
