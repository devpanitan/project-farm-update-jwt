
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Catalog</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 text-white">
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold mb-4">User Catalog</h1>
        <p class="text-lg text-gray-400 mb-8">A list of users from the database.</p>

        @if ($users->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($users as $user)
                    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-2">{{ $user->name }}</h2>
                        <p class="text-gray-400">{{ $user->email }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-yellow-900/50 border border-yellow-700 text-yellow-200 px-4 py-3 rounded-lg">
                <p class="font-bold">No Users Found!</p>
                <p>The `users` table is empty.</p>
            </div>
        @endif
    </div>
</body>
</html>
