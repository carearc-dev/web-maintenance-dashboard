@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <a href="{{ route('sites.index') }}" class="text-sm text-zinc-500 hover:text-zinc-950">戻る</a>
        <h1 class="mt-2 text-2xl font-semibold">{{ $site->name }}</h1>
        <p class="text-sm text-zinc-600">{{ $site->company_name }} / {{ $site->url }}</p>
    </div>
    @can('update', $site)
        <div class="flex gap-2">
            <form method="post" action="{{ route('sites.check', $site) }}">
                @csrf
                <button class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-medium">自動チェック</button>
            </form>
            <a href="{{ route('sites.edit', $site) }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-medium text-white">編集</a>
        </div>
    @endcan
</div>

<div class="grid gap-5 xl:grid-cols-[1fr_360px]">
    <section class="space-y-5">
        <div class="rounded-lg border border-zinc-200 bg-white p-5">
            <h2 class="mb-4 text-lg font-semibold">概要</h2>
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-zinc-500">種別</dt><dd>{{ $site->type->value }}</dd></div>
                <div><dt class="text-zinc-500">ステータス</dt><dd>{{ $site->status->value }}</dd></div>
                <div><dt class="text-zinc-500">自動確認日時</dt><dd>{{ $site->last_checked_at?->format('Y/m/d H:i') ?? '未実行' }}</dd></div>
                <div><dt class="text-zinc-500">自動判定理由</dt><dd>{{ $site->auto_status_reason ?? '-' }}</dd></div>
                <div><dt class="text-zinc-500">管理画面</dt><dd>{{ $site->admin_url ?? '-' }}</dd></div>
                <div><dt class="text-zinc-500">GitHub</dt><dd>{{ $site->github_url ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-5">
            <h2 class="mb-4 text-lg font-semibold">環境情報</h2>
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-zinc-500">サーバー会社</dt><dd>{{ $site->serverInformation?->provider ?? '-' }}</dd></div>
                <div><dt class="text-zinc-500">PHP</dt><dd>{{ $site->serverInformation?->php_version ?? '-' }}</dd></div>
                <div><dt class="text-zinc-500">SSL期限</dt><dd>{{ $site->serverInformation?->ssl_expires_on?->format('Y/m/d') ?? '-' }}</dd></div>
                <div><dt class="text-zinc-500">ドメイン期限</dt><dd>{{ $site->serverInformation?->domain_expires_on?->format('Y/m/d') ?? '-' }}</dd></div>
                <div><dt class="text-zinc-500">セキュリティヘッダー</dt><dd>{{ collect($site->serverInformation?->security_headers ?? [])->filter()->keys()->implode(', ') ?: '-' }}</dd></div>
            </dl>
        </div>

        @if ($site->wordpressInformation)
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="mb-4 text-lg font-semibold">WordPress</h2>
                <dl class="grid gap-3 text-sm md:grid-cols-2">
                    <div><dt class="text-zinc-500">WordPress</dt><dd>{{ $site->wordpressInformation->wordpress_version ?? '-' }}</dd></div>
                    <div><dt class="text-zinc-500">テーマ</dt><dd>{{ $site->wordpressInformation->theme_name ?? '-' }}</dd></div>
                    <div><dt class="text-zinc-500">接続状態</dt><dd>{{ $site->wordpressInformation->connection_status }}</dd></div>
                    <div><dt class="text-zinc-500">更新件数</dt><dd>{{ $site->wordpressInformation->plugin_update_count }}件</dd></div>
                    <div class="md:col-span-2"><dt class="text-zinc-500">取得メモ</dt><dd>{{ $site->wordpressInformation->status_message ?? '-' }}</dd></div>
                </dl>
            </div>
        @endif
    </section>

    <aside class="space-y-5">
        <div class="rounded-lg border border-zinc-200 bg-white p-5">
            <h2 class="mb-4 text-lg font-semibold">認証情報</h2>
            <div class="space-y-3 text-sm">
                @foreach ($site->credentials as $credential)
                    <div class="rounded-md border border-zinc-200 p-3">
                        <div class="font-medium">{{ $credential->service_name }}</div>
                        <div class="mt-1 text-zinc-500">{{ $credential->type }} / **********</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-5">
            <h2 class="mb-4 text-lg font-semibold">保守履歴</h2>
            <div class="space-y-4 text-sm">
                @foreach ($site->maintenanceLogs as $log)
                    <div>
                        <div class="font-medium">{{ $log->worked_on->format('Y/m/d') }} / {{ $log->category }}</div>
                        <p class="mt-1 text-zinc-600">{{ $log->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @can('addMaintenanceLog', $site)
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="mb-4 text-lg font-semibold">作業完了内容を入力</h2>
                <form method="post" action="{{ route('sites.maintenance-logs.store', $site) }}" class="space-y-3 text-sm">
                    @csrf
                    <label class="block">作業日
                        <input name="worked_on" type="date" value="{{ now()->toDateString() }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                    </label>
                    <label class="block">カテゴリ
                        <input name="category" placeholder="定期保守 / 更新 / 表示確認" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                    </label>
                    <label class="block">作業内容
                        <textarea name="description" rows="4" class="mt-1 w-full rounded-md border-zinc-300 text-sm"></textarea>
                    </label>
                    <label class="block">確認ページ
                        <input name="notes" placeholder="トップ、お知らせ、フォームなど" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                    </label>
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="block">対応前バージョン
                            <input name="version_before" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                        </label>
                        <label class="block">対応後バージョン
                            <input name="version_after" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                        </label>
                    </div>
                    <label class="block">作業時間（分）
                        <input name="minutes_spent" type="number" min="0" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                    </label>
                    <button class="w-full rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white">完了</button>
                </form>
            </div>
        @endcan
    </aside>
</div>
@endsection
