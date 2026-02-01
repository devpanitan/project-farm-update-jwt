<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Farm Listings | Farm Application</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    <div class="container mx-auto px-4 py-8">
        
        <header class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-2">
                Discover Our Farms
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                A collection of the finest farms managed by our system.
            </p>
        </header>

        @if($farms->isEmpty())
            <div class="text-center py-16">
                <p class="text-xl text-gray-500">No farms have been added yet.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($farms as $farm)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $farm->name }}</h2>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-blue-200 dark:text-blue-800">{{ $farm->farmCategory->cat_name }}</span>
                            </div>
                            
                            <p class="text-gray-700 dark:text-gray-300 mb-4 h-16 overflow-hidden">
                                {{ $farm->description }}
                            </p>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span>
                                        <strong>Size:</strong> {{ $farm->size ? $farm->size . ' acres' : 'N/A' }}
                                    </span>
                                    <span>
                                        <strong>Prefix:</strong> {{ $farm->farm_prefix ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                   ID: {{ $farm->id }} | Last Updated: {{ $farm->updated_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

</body>
</html>
