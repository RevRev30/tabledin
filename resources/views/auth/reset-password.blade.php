<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Reset Password</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-[#f8fafc] text-[#0f172a]">
        <div class="max-w-md mx-auto py-16 px-6">
            <h1 class="text-2xl font-semibold mb-4">Reset password</h1>

            @if (session('status'))
                <div class="rounded-sm border border-[#bbf7d0] bg-[#f0fdf4] text-[#166534] p-3 mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-sm px-3 py-2">
                    @error('email')<p class="text-sm text-[#b91c1c] mt-1">{{ $message }}</p>@enderror
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
                <button class="w-full px-4 py-2 rounded-sm bg-[#2563eb] text-white hover:bg-[#1d4ed8]">Reset password</button>
            </form>
        </div>
    </body>
    </html>


