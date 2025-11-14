<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Verify Email</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-[#f8fafc] text-[#0f172a]">
        <div class="max-w-md mx-auto py-16 px-6">
            <h1 class="text-2xl font-semibold mb-4">Verify your email</h1>
            <p class="text-sm text-[#475569] mb-6">We sent a verification link to your email. Click the link to verify your account.</p>

            @if (session('status'))
                <div class="rounded-sm border border-[#bbf7d0] bg-[#f0fdf4] text-[#166534] p-3 mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-sm px-3 py-2">
                </div>
                <button class="w-full px-4 py-2 rounded-sm bg-[#2563eb] text-white hover:bg-[#1d4ed8]">Resend verification email</button>
            </form>
            <div class="mt-3 text-sm">
                <a href="{{ route('login') }}" class="text-[#2563eb] hover:underline">Back to sign in</a>
            </div>
        </div>
    </body>
    </html>


