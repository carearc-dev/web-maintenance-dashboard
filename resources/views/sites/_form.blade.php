@csrf

<div class="grid gap-4 md:grid-cols-2">
    <label class="text-sm">企業名
        <input name="company_name" value="{{ old('company_name', $site->company_name ?? '') }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm">サイト名
        <input name="name" value="{{ old('name', $site->name ?? '') }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm">サイトURL
        <input name="url" value="{{ old('url', $site->url ?? '') }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm">管理画面URL
        <input name="admin_url" value="{{ old('admin_url', $site->admin_url ?? '') }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm">種別
        <select name="type" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
            @foreach (['wordpress' => 'WordPress', 'static_html' => '静的HTML', 'php' => 'PHP', 'lp' => 'LP', 'other' => 'その他'] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', isset($site) ? $site->type->value : 'wordpress') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label class="text-sm">ステータス
        <select name="status" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
            @foreach (['normal' => '正常', 'needs_check' => '要確認', 'updates_available' => '更新あり', 'incident' => '障害', 'suspended' => '保守停止'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', isset($site) ? $site->status->value : 'needs_check') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label class="text-sm md:col-span-2">GitHub URL
        <input name="github_url" value="{{ old('github_url', $site->github_url ?? '') }}" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
    </label>
    <label class="text-sm md:col-span-2">メモ
        <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border-zinc-300 text-sm">{{ old('notes', $site->notes ?? '') }}</textarea>
    </label>
</div>

<div class="mt-5">
    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white">保存</button>
</div>
