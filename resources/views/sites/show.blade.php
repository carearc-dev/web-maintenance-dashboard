@extends('layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('sites.index') }}" class="text-sm text-zinc-500 hover:text-zinc-950">Sites</a>
    <h1 class="mt-2 text-2xl font-semibold">{{ $site->name }}</h1>
    <p class="text-sm text-zinc-600">{{ $site->company_name }} / {{ $site->url }}</p>
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
    </aside>
</div>
@endsection
