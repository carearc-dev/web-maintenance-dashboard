@extends('layouts.app')

@section('content')
<div class="mb-5">
    <h1 class="text-2xl font-semibold">Users</h1>
    <p class="mt-1 text-sm text-zinc-600">管理者、社内スタッフ、エンジニアの権限を管理します。</p>
</div>

<section class="mb-5 rounded-lg border border-zinc-200 bg-white p-5">
    <h2 class="mb-4 text-lg font-semibold">ユーザーを追加</h2>
    <form method="post" action="{{ route('users.store') }}" class="grid gap-3 md:grid-cols-4">
        @csrf
        <input name="name" class="rounded-md border-zinc-300 text-sm" placeholder="名前">
        <input name="email" type="email" class="rounded-md border-zinc-300 text-sm" placeholder="メールアドレス">
        <select name="role" class="rounded-md border-zinc-300 text-sm">
            <option value="admin">管理者</option>
            <option value="staff">スタッフ</option>
            <option value="external_engineer">エンジニア</option>
        </select>
        <input name="password" type="password" class="rounded-md border-zinc-300 text-sm" placeholder="初期パスワード">
        <div class="md:col-span-4">
            <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white">追加</button>
        </div>
    </form>
</section>

<div class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-zinc-100 text-zinc-600">
            <tr>
                <th class="px-4 py-3">名前</th>
                <th class="px-4 py-3">メール</th>
                <th class="px-4 py-3">権限</th>
                <th class="px-4 py-3">状態</th>
                <th class="px-4 py-3">操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @foreach ($users as $managedUser)
                <tr>
                    <td class="px-4 py-3">
                        <form id="user-{{ $managedUser->id }}" method="post" action="{{ route('users.update', $managedUser) }}">
                            @csrf
                            @method('put')
                            <input name="name" value="{{ $managedUser->name }}" class="w-full rounded-md border-zinc-300 text-sm">
                    </td>
                    <td class="px-4 py-3">{{ $managedUser->email }}</td>
                    <td class="px-4 py-3">
                            <select name="role" class="rounded-md border-zinc-300 text-sm">
                                <option value="admin" @selected($managedUser->role->value === 'admin')>管理者</option>
                                <option value="staff" @selected($managedUser->role->value === 'staff')>スタッフ</option>
                                <option value="external_engineer" @selected($managedUser->role->value === 'external_engineer')>エンジニア</option>
                            </select>
                    </td>
                    <td class="px-4 py-3">
                            <select name="is_active" class="rounded-md border-zinc-300 text-sm">
                                <option value="1" @selected($managedUser->email_verified_at)>有効</option>
                                <option value="0" @selected(! $managedUser->email_verified_at)>権限不要</option>
                            </select>
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <button form="user-{{ $managedUser->id }}" class="text-sm font-medium text-zinc-900">保存</button>
                        @unless ($managedUser->is(auth()->user()))
                            <form class="mt-2" method="post" action="{{ route('users.destroy', $managedUser) }}" onsubmit="return confirm('このユーザーを権限不要にしますか？')">
                                @csrf
                                @method('delete')
                                <button class="text-sm font-medium text-red-700">権限不要</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
