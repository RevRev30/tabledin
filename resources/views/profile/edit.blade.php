<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Profile</title>
        <link rel="icon" href="/favicon.ico" />
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-[#f8fafc] text-[#0f172a]">
        <div class="max-w-3xl mx-auto py-16 px-6 space-y-10">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Profile</h1>
                <div class="flex gap-2">
                    <a href="{{ request()->getBaseUrl() }}/restaurants" class="px-3 py-2 rounded-sm bg-[#e2e8f0] hover:bg-[#cbd5e1]">Restaurants</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-sm bg-[#ef4444] text-white hover:bg-[#dc2626]">Logout</button>
                    </form>
                </div>
            </div>

            @if (session('status'))
                <div class="rounded-sm border border-[#bbf7d0] bg-[#f0fdf4] text-[#166534] p-3">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-sm border bg-white p-6">
                <h2 class="text-lg font-medium mb-4">Update profile</h2>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm mb-1">Full name</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full border rounded-sm px-3 py-2">
                        @error('name')<p class="text-sm text-[#b91c1c] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full border rounded-sm px-3 py-2">
                        @error('email')<p class="text-sm text-[#b91c1c] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-sm bg-[#2563eb] text-white hover:bg-[#1d4ed8]">Save changes</button>
                </form>
            </div>

            <div class="rounded-sm border bg-white p-6">
                <h2 class="text-lg font-medium mb-4">Change password</h2>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm mb-1">Current password</label>
                        <input type="password" name="current_password" required class="w-full border rounded-sm px-3 py-2">
                        @error('current_password')<p class="text-sm text-[#b91c1c] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">New password</label>
                        <input type="password" name="password" required class="w-full border rounded-sm px-3 py-2">
                        @error('password')<p class="text-sm text-[#b91c1c] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Confirm new password</label>
                        <input type="password" name="password_confirmation" required class="w-full border rounded-sm px-3 py-2">
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-sm bg-[#2563eb] text-white hover:bg-[#1d4ed8]">Update password</button>
                </form>
            </div>
        </div>
    </body>
    </html>


