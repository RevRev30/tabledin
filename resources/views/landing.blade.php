<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TabledIn - Smart Restaurant Reservations</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            @keyframes gradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            .animate-fade-in-up {
                animation: fadeInUp 0.8s ease-out;
            }
            .gradient-text {
                background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                background-size: 200% 200%;
                animation: gradient 3s ease infinite;
            }
            .glass-effect {
                background: rgba(15, 23, 42, 0.7);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(148, 163, 184, 0.1);
            }
        </style>
    </head>
    <body class="min-h-screen bg-gradient-to-br from-[#0b1220] via-[#1e293b] to-[#0b1220] text-white overflow-x-hidden">
        <header class="fixed top-0 left-0 right-0 z-50 glass-effect border-b border-[#1e293b]/50">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group cursor-pointer hover:opacity-80 transition-opacity">
                    <div class="w-4 h-4 rounded-full bg-gradient-to-br from-[#60a5fa] to-[#3b82f6] shadow-lg shadow-blue-500/50 group-hover:scale-110 transition-transform"></div>
                    <span class="font-bold text-xl tracking-tight text-white">TabledIn</span>
                </a>
                <nav class="flex items-center gap-3 text-sm">
                @auth
                    @if(auth()->user()->isStaffOrAdmin())
                            <a href="{{ route('staff.dashboard') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-[#2563eb] to-[#1d4ed8] text-white hover:shadow-lg hover:shadow-blue-500/50 transition-all">Staff Dashboard</a>
                            <a href="{{ route('staff.reservations') }}" class="px-4 py-2 rounded-lg border border-[#334155] hover:border-[#60a5fa] hover:bg-[#1e293b]/50 transition-all">Manage Reservations</a>
                    @else
                            <a href="{{ request()->getBaseUrl() }}/restaurants" class="px-4 py-2 rounded-lg border border-[#334155] hover:border-[#60a5fa] hover:bg-[#1e293b]/50 transition-all">Restaurants</a>
                            <a href="{{ route('reservations.index') }}" class="px-4 py-2 rounded-lg border border-[#334155] hover:border-[#60a5fa] hover:bg-[#1e293b]/50 transition-all">My Reservations</a>
                    @endif
                        <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-lg border border-[#334155] hover:border-[#60a5fa] hover:bg-[#1e293b]/50 transition-all">Profile</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                            <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white hover:shadow-lg hover:shadow-red-500/50 transition-all">Logout</button>
                    </form>
                @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-[#334155] hover:border-[#60a5fa] hover:bg-[#1e293b]/50 transition-all">Log in</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-[#2563eb] to-[#1d4ed8] text-white hover:shadow-lg hover:shadow-blue-500/50 transition-all">Register</a>
                @endauth
            </nav>
            </div>
        </header>

        <main class="pt-20">
            <!-- Hero Section -->
            <section class="relative overflow-hidden min-h-[90vh] flex items-center">
                <!-- Animated Background -->
                <div class="absolute inset-0">
                    <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl animate-float"></div>
                    <div class="absolute top-40 right-10 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
                    <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-cyan-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
                </div>
                
                <div class="relative max-w-7xl mx-auto px-6 py-20 grid lg:grid-cols-2 gap-16 items-center">
                    <!-- Left Column -->
                    <div class="space-y-8 animate-fade-in-up">
                        <div class="inline-block px-4 py-2 rounded-full glass-effect border border-[#334155]">
                            <span class="text-sm text-[#93c5fd] font-medium">✨ Revolutionary Reservation System</span>
                        </div>
                        <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                            <span class="block">Smart Reservations</span>
                            <span class="block gradient-text">Made Simple</span>
                        </h1>
                        <p class="text-xl text-[#cbd5e1] leading-relaxed max-w-2xl">
                            Skip the queue. Reserve tables, choose seats, and keep service flowing seamlessly—built for restaurants in the Philippines.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            @auth
                                <a href="{{ request()->getBaseUrl() }}/restaurants" class="group px-8 py-4 rounded-xl bg-gradient-to-r from-[#2563eb] to-[#1d4ed8] text-white font-semibold hover:shadow-2xl hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 inline-flex items-center gap-2">
                                    <span>Browse restaurants</span>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="group px-8 py-4 rounded-xl bg-gradient-to-r from-[#2563eb] to-[#1d4ed8] text-white font-semibold hover:shadow-2xl hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 inline-flex items-center gap-2">
                                    <span>Get started</span>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            @endauth
                            <button id="show-features-btn" class="group px-8 py-4 rounded-xl border-2 border-[#334155] hover:border-[#60a5fa] bg-[#1e293b]/50 backdrop-blur-sm font-semibold hover:bg-[#1e293b] hover:scale-105 transition-all duration-300 inline-flex items-center gap-2">
                                <i class="fas fa-star text-[#60a5fa]"></i>
                                <span>See features</span>
                                <i class="fas fa-chevron-down group-hover:translate-y-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Right Column - Feature Cards -->
                    <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="glass-effect rounded-2xl p-8 border border-[#334155] shadow-2xl">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="group p-6 rounded-xl bg-gradient-to-br from-[#1e293b] to-[#0f172a] border border-[#334155] hover:border-[#60a5fa] hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                                    </div>
                                    <div class="text-[#93c5fd] font-semibold mb-2">Real-time calendar</div>
                                    <div class="text-[#cbd5e1] text-sm">Instant availability and time slots.</div>
                                </div>
                                
                                <div class="group p-6 rounded-xl bg-gradient-to-br from-[#1e293b] to-[#0f172a] border border-[#334155] hover:border-[#60a5fa] hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-chair text-white text-xl"></i>
                                    </div>
                                    <div class="text-[#93c5fd] font-semibold mb-2">Seat selection</div>
                                    <div class="text-[#cbd5e1] text-sm">Visual seating map assignment.</div>
                                </div>
                                
                                <div class="group p-6 rounded-xl bg-gradient-to-br from-[#1e293b] to-[#0f172a] border border-[#334155] hover:border-[#60a5fa] hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-bell text-white text-xl"></i>
                                    </div>
                                    <div class="text-[#93c5fd] font-semibold mb-2">Notifications</div>
                                    <div class="text-[#cbd5e1] text-sm">Confirmations and reminders.</div>
                            </div>
                                
                                <div class="group p-6 rounded-xl bg-gradient-to-br from-[#1e293b] to-[#0f172a] border border-[#334155] hover:border-[#60a5fa] hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-users text-white text-xl"></i>
                            </div>
                                    <div class="text-[#93c5fd] font-semibold mb-2">Queue for walk-ins</div>
                                    <div class="text-[#cbd5e1] text-sm">Tokens and wait-time estimates.</div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="relative py-32 hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#1e293b]/20 to-transparent"></div>
                <div class="relative max-w-7xl mx-auto px-6">
                    <div class="text-center mb-16">
                        <div class="inline-block px-4 py-2 rounded-full glass-effect border border-[#334155] mb-6">
                            <span class="text-sm text-[#93c5fd] font-medium">✨ Discover Our Features</span>
                        </div>
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                            <span class="gradient-text">Everything You Need</span>
                        </h2>
                        <p class="text-xl text-[#cbd5e1] max-w-3xl mx-auto leading-relaxed">
                            Powerful features designed to streamline restaurant reservations and enhance the dining experience
                        </p>
                    </div>
                    
                    <!-- Feature Grid -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="group glass-effect rounded-2xl p-8 border border-[#334155] hover:border-[#60a5fa] hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-blue-500/30">
                                <i class="fas fa-calendar-alt text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Real-time Calendar</h3>
                            <p class="text-[#cbd5e1] leading-relaxed">Instant availability and time slots for all restaurants. Never miss a booking opportunity.</p>
                        </div>
                        
                        <!-- Feature 2 -->
                        <div class="group glass-effect rounded-2xl p-8 border border-[#334155] hover:border-[#60a5fa] hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-blue-500/30">
                                <i class="fas fa-chair text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Seat Selection</h3>
                            <p class="text-[#cbd5e1] leading-relaxed">Visual seating map assignment with table preferences. Choose your perfect spot.</p>
                        </div>
                        
                        <!-- Feature 3 -->
                        <div class="group glass-effect rounded-2xl p-8 border border-[#334155] hover:border-[#60a5fa] hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-blue-500/30">
                                <i class="fas fa-bell text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Smart Notifications</h3>
                            <p class="text-[#cbd5e1] leading-relaxed">Email confirmations and reminders for all bookings. Stay informed every step of the way.</p>
                        </div>
                        
                        <!-- Feature 4 -->
                        <div class="group glass-effect rounded-2xl p-8 border border-[#334155] hover:border-[#60a5fa] hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-blue-500/30">
                                <i class="fas fa-users text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Walk-in Queue</h3>
                            <p class="text-[#cbd5e1] leading-relaxed">Digital tokens and wait-time estimates for walk-in customers. Efficient queue management.</p>
                        </div>
                        
                        <!-- Feature 5 -->
                        <div class="group glass-effect rounded-2xl p-8 border border-[#334155] hover:border-[#60a5fa] hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-blue-500/30">
                                <i class="fas fa-calendar-check text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Customer Booking</h3>
                            <p class="text-[#cbd5e1] leading-relaxed">Create, edit, and cancel reservations with preferred seating options. Full control at your fingertips.</p>
                        </div>
                        
                        <!-- Feature 6 -->
                        <div class="group glass-effect rounded-2xl p-8 border border-[#334155] hover:border-[#60a5fa] hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-blue-500/30">
                                <i class="fas fa-tachometer-alt text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Staff Dashboard</h3>
                            <p class="text-[#cbd5e1] leading-relaxed">Manage tables in real-time and balance peak hours efficiently. Complete operational control.</p>
                </div>
                </div>
                </div>
            </section>
        </main>

        <footer class="relative border-t border-[#334155]/50 mt-20">
            <div class="max-w-7xl mx-auto px-6 py-12">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-br from-[#60a5fa] to-[#3b82f6] shadow-lg shadow-blue-500/50"></div>
                        <span class="font-bold text-lg bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">TabledIn</span>
                    </div>
                    <p class="text-sm text-[#94a3b8]">© {{ date('Y') }} TabledIn. All rights reserved.</p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="text-[#64748b] hover:text-[#93c5fd] transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-[#64748b] hover:text-[#93c5fd] transition-colors">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="text-[#64748b] hover:text-[#93c5fd] transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const showFeaturesBtn = document.getElementById('show-features-btn');
                const featuresSection = document.getElementById('features');
                
                if (showFeaturesBtn && featuresSection) {
                    showFeaturesBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Remove hidden class to show the section
                        featuresSection.classList.remove('hidden');
                        
                        // Add fade-in animation
                        featuresSection.style.opacity = '0';
                        featuresSection.style.transform = 'translateY(30px)';
                        
                        // Smooth scroll to the features section
                        setTimeout(() => {
                            featuresSection.scrollIntoView({ 
                                behavior: 'smooth',
                                block: 'start'
                            });
                            
                            // Animate in
                            setTimeout(() => {
                                featuresSection.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
                                featuresSection.style.opacity = '1';
                                featuresSection.style.transform = 'translateY(0)';
                            }, 300);
                        }, 100);
                    });
                }
            });
        </script>
    </body>
    </html>


