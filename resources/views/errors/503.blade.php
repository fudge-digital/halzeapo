{{-- resources/views/errors/503.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site is Down</title>

    <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 h-screen flex flex-col justify-center items-center text-center">

    <div>
        <h1 class="text-6xl font-bold text-gray-800 mb-4">503</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Service Unavailable</h2>
        <p class="text-gray-600 mb-8">Site is currently Down.</p>
    </div>

</body>
</html>
