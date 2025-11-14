<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Dashboard</title>
        <link rel="icon" href="/favicon.ico" />
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-[#f8fafc] text-[#0f172a]">
        <div class="max-w-3xl mx-auto py-16 px-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold">Dashboard</h1>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-sm bg-[#ef4444] text-white hover:bg-[#dc2626]">Logout</button>
                </form>
            </div>

            @if (session('status'))
                <div class="rounded-sm border border-[#bbf7d0] bg-[#f0fdf4] text-[#166534] p-3 mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-sm border bg-white p-6">
                <p class="mb-2">Welcome, {{ auth()->user()->name }}.</p>
                <p class="text-sm text-[#475569]">You're signed in. Browse restaurants and book a table.</p>
                <div class="mt-4">
                    <a href="{{ route('restaurants.index') }}" class="inline-flex items-center px-4 py-2 rounded-sm bg-[#2563eb] text-white hover:bg-[#1d4ed8]">View restaurants</a>
                    <a href="{{ route('profile.edit') }}" class="ml-2 inline-flex items-center px-4 py-2 rounded-sm border border-[#cbd5e1] hover:bg-[#e2e8f0]">Profile</a>
                </div>
            </div>
        </div>
    </body>
</html>


