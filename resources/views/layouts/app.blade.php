<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Report Cards')</title>

    {{-- Tailwind through the Play CDN (no npm, no Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Only custom rules that Tailwind classes do not cover --}}
    <link rel="stylesheet" href="{{ asset('css/school.css') }}">
</head>
<body class="bg-slate-100 text-slate-800">
    <header class="bg-blue-800 px-6 py-3">
        <a href="/" class="text-xl font-bold text-white no-underline">Report Cards</a>
    </header>

    <main class="mx-auto my-6 max-w-3xl px-4">
        @yield('content')
    </main>
</body>
</html>
