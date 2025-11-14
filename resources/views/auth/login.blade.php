<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sign in</title>
        @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white flex items-center justify-center p-6">
        <div class="w-full max-w-md border rounded-lg p-6 shadow-[0_1px_2px_rgba(0,0,0,0.06)]">
            <h1 class="text-2xl font-medium mb-1 text-[#0f172a]">Welcome back</h1>
            <p class="text-sm text-[#475569] mb-6">Sign in to continue</p>

            @if ($errors->any())
                <div class="mb-4 rounded-sm border border-[#93c5fd] bg-[#eff6ff] p-3 text-sm text-[#1e3a8a]">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="rounded-sm border border-[#bbf7d0] bg-[#f0fdf4] text-[#166534] p-3 mb-2">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm mb-1 text-[#0f172a]">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full border rounded-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:border-[#3b82f6]" />
                </div>
                <div>
                    <label for="password" class="block text-sm mb-1 text-[#0f172a]">Password</label>
                    <input id="password" name="password" type="password" required class="w-full border rounded-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:border-[#3b82f6]" />
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center text-sm text-[#0f172a]">
                        <input type="checkbox" name="remember" class="mr-2">
                        Remember me
                    </label>
                    <a href="{{ url('/') }}" class="text-sm text-[#2563eb] hover:underline">Back home</a>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-sm text-white bg-[#2563eb] hover:bg-[#1d4ed8]">
                    Sign in
                </button>
            </form>

            <p class="text-sm text-[#475569] mt-4">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-[#2563eb] hover:underline">Create one</a>
            </p>
        </div>
    </body>
</html>


