<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TabledIn')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header id="site-header" class="bg-white shadow-sm border-b fixed top-0 left-0 right-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-[#60a5fa]"></div>
                    <span class="font-semibold tracking-wide text-xl">TabledIn Staff</span>
                </div>
                <!-- mobile hamburger -->
                <button id="nav-toggle" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:bg-gray-100" aria-expanded="false" aria-label="Toggle navigation">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <nav id="main-nav" class="hidden md:flex items-center gap-3 text-sm">
                    <a href="{{ route('staff.dashboard') }}" class="px-4 py-1.5 rounded-sm {{ request()->routeIs('staff.dashboard') ? 'bg-[#2563eb] text-white' : 'border border-[#1e3a8a] hover:border-[#3b82f6]' }}">Dashboard</a>
                    <a href="{{ route('staff.reservations') }}" class="px-4 py-1.5 rounded-sm {{ request()->routeIs('staff.reservations*') ? 'bg-[#2563eb] text-white' : 'border border-[#1e3a8a] hover:border-[#3b82f6]' }}">Reservations</a>
                    <a href="{{ route('staff.seating.index') }}" class="px-4 py-1.5 rounded-sm {{ request()->routeIs('staff.seating*') ? 'bg-[#2563eb] text-white' : 'border border-[#1e3a8a] hover:border-[#3b82f6]' }}">Seating</a>
                    <a href="{{ route('staff.restaurants') }}" class="px-4 py-1.5 rounded-sm {{ request()->routeIs('staff.restaurants*') ? 'bg-[#2563eb] text-white' : 'border border-[#1e3a8a] hover:border-[#3b82f6]' }}">Restaurants</a>
                    <a href="{{ route('staff.customers') }}" class="px-4 py-1.5 rounded-sm {{ request()->routeIs('staff.customers*') ? 'bg-[#2563eb] text-white' : 'border border-[#1e3a8a] hover:border-[#3b82f6]' }}">Customers</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="px-4 py-1.5 rounded-sm bg-red-600 hover:bg-red-700 text-white">Logout</button>
                    </form>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main id="site-main" class="">
        {{-- Page content --}}
        @yield('content')
    </main>

    @stack('scripts')

    <script>
        // Ensure the main content has top padding equal to the header height
        function updateMainPadding() {
            var header = document.getElementById('site-header');
            var main = document.getElementById('site-main');
            if (!header || !main) return;
            var height = header.offsetHeight;
            // apply padding-top so content is never hidden under the fixed header
            main.style.paddingTop = height + 'px';
        }

        // Mobile nav toggle
        function initMobileNav() {
            var toggle = document.getElementById('nav-toggle');
            var nav = document.getElementById('main-nav');
            if (!toggle || !nav) return;
            toggle.addEventListener('click', function() {
                var isHidden = nav.classList.contains('hidden');
                if (isHidden) {
                    nav.classList.remove('hidden');
                    nav.classList.add('flex', 'flex-col', 'absolute', 'right-4', 'top-full', 'mt-2', 'bg-white', 'shadow-md', 'p-3', 'rounded-md');
                    toggle.setAttribute('aria-expanded', 'true');
                } else {
                    // revert to hidden on small screens
                    nav.classList.add('hidden');
                    nav.classList.remove('flex', 'flex-col', 'absolute', 'right-4', 'top-full', 'mt-2', 'bg-white', 'shadow-md', 'p-3', 'rounded-md');
                    toggle.setAttribute('aria-expanded', 'false');
                }
                // header size might change when nav toggles
                updateMainPadding();
            });
        }

        // Update on load and when resizing or when fonts/images might change layout
        window.addEventListener('load', function() { updateMainPadding(); initMobileNav(); });
        window.addEventListener('resize', updateMainPadding);

        // Also run after DOM changes (a tiny debounce) in case JS alters the header
        var _debounce;
        function setupObserver() {
            var headerEl = document.getElementById('site-header');
            if (!headerEl) return;
            var observer = new MutationObserver(function() {
                clearTimeout(_debounce);
                _debounce = setTimeout(updateMainPadding, 100);
            });
            observer.observe(headerEl, { attributes: true, childList: true, subtree: true });
        }
        // try to setup observer after load as well
        window.addEventListener('load', setupObserver);
    </script>
</body>
</html>
