<!DOCTYPE html>
<html class="h-full bg-slate-100 dark:bg-[#080c14] text-slate-900 dark:text-slate-100 antialiased selection:bg-[#295384] selection:text-white" lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Insurance Platform' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles

    <script>
        const applyTheme = () => {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        };
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="min-h-full flex items-center justify-center p-4 sm:p-6 py-12 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-200 dark:from-[#080c14] dark:via-[#0f172a] dark:to-[#020617]">
    {{ $slot }}

    @filamentScripts
</body>
</html>
