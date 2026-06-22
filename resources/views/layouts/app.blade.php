<!DOCTYPE html>
<html lang="nb" class="h-full">
<head>
    <script>
        if (localStorage.theme === 'dark' || (! ('theme' in localStorage) && matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
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
    @production
        <script src="https://cdn.usefathom.com/script.js" data-site="FSTCWXTJ" defer></script>
    @endproduction
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-2xl px-5 py-6 sm:py-10">
        <header class="mb-6 flex items-center justify-between border-b border-zinc-200 pb-4 dark:border-zinc-800">
            <a href="/" class="text-base font-semibold tracking-tight text-zinc-900 dark:text-white">salgstider<span class="text-zinc-400">.no</span></a>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Åpningstider</span>
                <button type="button" data-theme-toggle aria-label="Bytt mellom lyst og mørkt tema" class="inline-flex h-8 w-8 items-center justify-center rounded-full text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white">
                    <svg class="h-4 w-4 dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    <svg class="hidden h-4 w-4 dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                </button>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="mt-20 space-y-2 border-t border-zinc-200 pt-6 text-center text-sm leading-relaxed text-zinc-500 dark:border-zinc-800">
            <p>salgstider.no – åpningstider for ølsalg i butikk og Vinmonopolet i Norge.</p>
            <p>
                Utviklet av <a href="https://rah.no" class="font-medium text-zinc-700 underline decoration-zinc-300 underline-offset-2 transition hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">Rolf Haug</a>
                – en del av <a href="https://enkeltforklart.no" class="font-medium text-zinc-700 underline decoration-zinc-300 underline-offset-2 transition hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">enkeltforklart.no</a>.
            </p>
        </footer>
    </div>
</body>
</html>
