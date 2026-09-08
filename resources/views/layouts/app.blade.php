<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Web Maintenance Dashboard') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-950">
    <div class="min-h-screen lg:grid lg:grid-cols-[240px_1fr]">
        <aside class="border-r border-zinc-200 bg-white px-4 py-5">
            <div class="mb-6 text-sm font-semibold tracking-wide">Maintenance</div>
            <nav class="space-y-1 text-sm">
                @foreach ([
                    'dashboard' => 'Dashboard',
                    'sites.index' => 'Sites',
                    'maintenance.index' => 'Maintenance',
                    'alerts.index' => 'Alerts',
                    'credentials.index' => 'Credentials',
                    'users.index' => 'Users',
                    'activity-logs.index' => 'Activity Logs',
                    'settings.edit' => 'Settings',
                ] as $route => $label)
                    <a href="{{ Route::has($route) ? route($route) : '#' }}" class="block rounded-md px-3 py-2 text-zinc-700 hover:bg-zinc-100">{{ $label }}</a>
                @endforeach
            </nav>
            <form method="post" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button class="w-full rounded-md border border-zinc-300 px-3 py-2 text-left text-sm text-zinc-700 hover:bg-zinc-100">ログアウト</button>
            </form>
        </aside>
        <main class="px-5 py-6 lg:px-8">
            @yield('content')
        </main>
    </div>
</body>
</html>
