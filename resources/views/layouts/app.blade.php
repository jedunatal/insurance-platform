<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insurance Platform</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

            <h1 class="text-2xl font-bold" style="color:#295384;">
                Insurance Platform
            </h1>

            <span class="text-sm text-gray-500">
                v1.0
            </span>

        </div>
    </header>

    <main class="flex-1">

        @yield('content')

    </main>

    <footer class="bg-white border-t">

        <div class="max-w-7xl mx-auto px-6 py-4 text-center text-sm text-gray-400">

            © 2026 Insurance Platform.

        </div>

    </footer>

</body>

</html>