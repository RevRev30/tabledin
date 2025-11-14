<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Reservation' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="robots" content="noindex">
    </head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="bg-white shadow rounded-lg p-8 max-w-lg w-full text-center">
            <h1 class="text-2xl font-semibold mb-4">{{ $title ?? 'Reservation' }}</h1>
            <p class="text-gray-700">{{ $message ?? '' }}</p>
        </div>
    </div>
</body>
</html>



