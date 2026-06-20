<!DOCTYPE html>
<html lang="nb" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'salgstider.no')</title>
    <meta name="description" content="@yield('description')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:title" content="@yield('title', 'salgstider.no')">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="salgstider.no">
    <meta property="og:locale" content="nb_NO">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-2xl px-5 py-6 sm:py-10">
        <header class="mb-6 flex items-baseline justify-between border-b border-zinc-200 pb-4 dark:border-zinc-800">
            <a href="/" class="text-base font-semibold tracking-tight text-zinc-900 dark:text-white">salgstider<span class="text-zinc-400">.no</span></a>
            <span class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Åpningstider</span>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="mt-20 border-t border-zinc-200 pt-6 text-sm leading-relaxed text-zinc-500 dark:border-zinc-800">
            salgstider.no – åpningstider for ølsalg i butikk og Vinmonopolet i Norge.
        </footer>
    </div>
</body>
</html>
