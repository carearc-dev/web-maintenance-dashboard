<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン | {{ config('app.name', 'Web Maintenance Dashboard') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-zinc-50 px-5 text-zinc-950">
    <form method="post" action="{{ route('login') }}" class="w-full max-w-md rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="mb-6">
            <div class="text-sm font-semibold tracking-wide">Web Management Sheet</div>
            <h1 class="mt-2 text-2xl font-semibold">ログイン</h1>
            <p class="mt-1 text-sm text-zinc-600">権限に応じて表示・操作できる範囲が変わります。</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                ログイン情報を確認してください。
            </div>
        @endif

        <label class="block text-sm">メールアドレス
            <input name="email" type="email" value="{{ old('email') }}" autocomplete="username" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
        </label>

        <label class="mt-4 block text-sm">パスワード
            <input name="password" type="password" autocomplete="current-password" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
        </label>

        <label class="mt-4 flex items-center gap-2 text-sm text-zinc-600">
            <input name="remember" type="checkbox" value="1" class="rounded border-zinc-300">
            ログイン状態を保持する
        </label>

        <button class="mt-5 w-full rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white">ログイン</button>
    </form>
</body>
</html>
