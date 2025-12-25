<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LS25 Aktienmarkt | Professionelles Trading System</title>

    {{-- Use built Vite assets (local) so the site doesn't depend on external CDN at runtime --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617;
        }
        .ticker-wrap {
            overflow: hidden;
            background: #0f172a;
            color: white;
            padding: 10px 0;
        }
        .ticker {
            display: inline-block;
            white-space: nowrap;
            animation: ticker 30s linear infinite;
        }
        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .gradient-text {
            background: linear-gradient(135deg, #22c55e 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.15) 0%, rgba(2, 6, 23, 0) 70%);
            pointer-events: none;
            z-index: -1;
        }
    </style>
</head>
<body class="antialiased text-slate-200 dark">

    <!-- Live Ticker oben -->
    <div class="ticker-wrap border-b border-slate-800">
        <div class="ticker">
            <span class="mx-4 font-mono text-sm uppercase"><span class="text-green-400 font-bold">AGRAR-INDEX ▲ +2.4%</span></span>
            <span class="mx-4 font-mono text-sm uppercase"><span class="text-red-400 font-bold">BIO-FUEL ▼ -0.8%</span></span>
            <span class="mx-4 font-mono text-sm uppercase"><span class="text-green-400 font-bold">RICE-TECH ▲ +5.1%</span></span>
            <span class="mx-4 font-mono text-sm uppercase"><span class="text-slate-400 font-bold">LAND-HOLDING — 0.0%</span></span>
            <span class="mx-4 font-mono text-sm uppercase"><span class="text-green-400 font-bold">TRACTOR-CORP ▲ +1.2%</span></span>
            <span class="mx-4 font-mono text-sm uppercase"><span class="text-green-400 font-bold">WHEAT-FUTURE ▲ +0.9%</span></span>
        </div>
    </div>

    <div class="min-h-screen flex flex-col relative overflow-hidden">
        <div class="hero-glow"></div>

        <!-- Navigation -->
        <nav class="bg-slate-950/80 backdrop-blur-md border-b border-slate-800 py-4 px-6 flex justify-between items-center sticky top-0 z-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center text-white shadow-[0_0_20px_rgba(22,163,74,0.4)]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-white leading-none">LS25<span class="text-green-500 font-black italic">STOCK</span></span>
                    <span class="text-[10px] font-bold text-slate-500 tracking-[0.2em] uppercase">Enterprise Simulation</span>
                </div>
            </div>
            
            <div class="hidden md:flex items-center gap-8 px-6 py-2 bg-slate-900/50 rounded-full border border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Game Time:</span>
                    <span class="text-sm font-bold text-green-400">SEPTEMBER - JAHR 1</span>
                </div>
                <div class="h-4 w-[1px] bg-slate-700"></div>
                <div class="flex items-center gap-2 text-sm font-bold text-slate-300">
                    🌐 DE | EN | NL
                </div>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-green-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-green-700 shadow-lg transition transform active:scale-95">Zum Depot</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-slate-400 hover:text-white transition">Anmelden</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-white text-slate-950 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-200 shadow-xl transition transform active:scale-95">Konto eröffnen</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow p-6 lg:p-12 max-w-7xl mx-auto w-full">
            
            <!-- Hero Section -->
            <header class="mb-20 grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-green-500/10 text-green-400 px-4 py-1.5 rounded-full text-xs font-bold mb-8 border border-green-500/20 uppercase tracking-widest">
                        🚀 GameTime Simulation v1.0
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-[900] text-white mb-8 leading-[1.05]">
                        Dein Hof. <br>Dein Kapital. <br><span class="gradient-text">Deine Regeln.</span>
                    </h1>
                    <p class="text-lg text-slate-400 mb-10 leading-relaxed max-w-lg">
                        Erlebe das erste vollwertige Aktiensystem für LS25. Investiere in Agrar-Giganten, verwalte dein integriertes Bankkonto und nutze Echtzeit-Analysen zur Gewinnmaximierung.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-green-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-green-500 shadow-[0_20px_40px_-10px_rgba(22,163,74,0.3)] transition-all flex items-center gap-3 group">
                            Depot jetzt starten
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        @endif
                        <button class="bg-slate-900 border border-slate-800 text-slate-300 px-8 py-4 rounded-2xl font-bold hover:bg-slate-800 transition-all">
                            Marktdaten-Wiki
                        </button>
                    </div>
                </div>

                <!-- Portfolio Preview Card (Dark Version) -->
                <div class="relative">
                    <div class="glass-card rounded-[3rem] p-8 shadow-2xl">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h3 class="font-extrabold text-2xl text-white">Mein Portfolio</h3>
                                <p class="text-slate-500 text-sm font-medium">Aktualisiert: Heute, 14:02</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-black text-green-400">€ 284.512</p>
                                <p class="text-xs font-bold text-green-400 bg-green-400/10 px-2 py-0.5 rounded-md inline-block">+14.2% Gesamt</p>
                            </div>
                        </div>

                        <!-- Mini Chart Placeholder -->
                        <div class="h-32 flex items-end gap-2 mb-8">
                            <div class="w-full bg-slate-800 h-[30%] rounded-lg"></div>
                            <div class="w-full bg-slate-800 h-[50%] rounded-lg"></div>
                            <div class="w-full bg-slate-800 h-[45%] rounded-lg"></div>
                            <div class="w-full bg-green-900/40 h-[70%] rounded-lg"></div>
                            <div class="w-full bg-green-700/40 h-[85%] rounded-lg border-t-4 border-green-500"></div>
                            <div class="w-full bg-green-500 h-[100%] rounded-lg shadow-[0_0_20px_rgba(34,197,94,0.3)]"></div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-slate-900/50 rounded-2xl border border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-800 rounded-xl shadow-sm flex items-center justify-center text-lg">🌾</div>
                                    <span class="font-bold text-sm text-slate-200">Land-Tech AG</span>
                                </div>
                                <span class="text-green-400 font-bold text-sm">€ 142.50 (+3.2%)</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-slate-900/50 rounded-2xl border border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-800 rounded-xl shadow-sm flex items-center justify-center text-lg">🏦</div>
                                    <span class="font-bold text-sm text-slate-200">Bankkontostand</span>
                                </div>
                                <span class="text-white font-bold text-sm">€ 42.109,23</span>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative Elements -->
                    <div class="absolute -z-10 -bottom-10 -left-10 w-48 h-48 bg-green-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute -z-10 -top-10 -right-10 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl"></div>
                </div>
            </header>

            <!-- 3-Column Features -->
            <section class="mb-32">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-extrabold text-white mb-4 tracking-tight">Enterprise Simulation Engine</h2>
                    <p class="text-slate-500 max-w-2xl mx-auto">Modernste Web-Technologie trifft auf immersives LS25 Gameplay.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1: Banking -->
                    <div class="bg-slate-900/40 p-10 rounded-[2.5rem] border border-slate-800 hover:border-indigo-500/50 transition-all group">
                        <div class="w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-indigo-900/20 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 text-white">Integriertes Banking</h3>
                        <p class="text-slate-400 leading-relaxed mb-6">Automatisierte Bankkonten bei Registrierung, lückenlose Transaktionshistorie und sichere Payment-Autorisierung.</p>
                        <ul class="space-y-2 text-sm font-bold text-slate-500">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Dividenden-Tracking</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Payment Middleware</li>
                        </ul>
                    </div>

                    <!-- Feature 2: Analytics -->
                    <div class="bg-slate-900/40 p-10 rounded-[2.5rem] border border-slate-800 hover:border-green-500/50 transition-all group">
                        <div class="w-14 h-14 bg-green-600 text-white rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-green-900/20 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 text-white">Smart Analytics</h3>
                        <p class="text-slate-400 leading-relaxed mb-6">Live-Charts via Chart.js, Kennzahlen wie KGV & EPS sowie historische Performance-Analysen für informierte Trades.</p>
                        <ul class="space-y-2 text-sm font-bold text-slate-500">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Risiko-Kennzahlen</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Kaufkraft-Analysen</li>
                        </ul>
                    </div>

                    <!-- Feature 3: GameTime -->
                    <div class="bg-slate-900/40 p-10 rounded-[2.5rem] border border-slate-800 hover:border-amber-500/50 transition-all group">
                        <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-amber-900/20 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 text-white">GameTime-System</h3>
                        <p class="text-slate-400 leading-relaxed mb-6">Einzigartige zeitgesteuerte Simulation mit Monatsübergängen. Plane deine Strategie nach dem Ingame-Kalender.</p>
                        <ul class="space-y-2 text-sm font-bold text-slate-500">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Monatliche Simulation</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Kursverlaufs-Historie</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Tech Stack Section -->
            <section class="mb-32 bg-slate-900 rounded-[3.5rem] p-12 lg:p-20 text-white relative overflow-hidden border border-slate-800 shadow-2xl">
                <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-green-600/10 to-transparent"></div>
                <div class="relative z-10 grid lg:grid-cols-2 gap-20">
                    <div>
                        <h2 class="text-4xl font-black mb-8 italic uppercase tracking-tighter">Technische <br><span class="text-green-500">Exzellenz.</span></h2>
                        <p class="text-slate-400 text-lg mb-12">
                            Entwickelt auf Basis von Laravel 11 und Docker (Sail). Unsere Architektur garantiert maximale Sicherheit durch MFA und Rollen-basierte Zugriffskontrolle.
                        </p>
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <h4 class="font-bold text-slate-200 mb-2">Modern Stack</h4>
                                <p class="text-sm text-slate-500">Laravel 11, Vite, Tailwind CSS</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-200 mb-2">Security</h4>
                                <p class="text-sm text-slate-500">MFA & Role-based Access</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 bg-white/5 p-6 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="w-12 h-12 bg-slate-800 rounded-xl flex items-center justify-center font-bold text-green-500">PHP</div>
                            <div>
                                <p class="font-bold">Service-Oriented Architecture</p>
                                <p class="text-xs text-slate-500">Saubere Business-Logik-Services & Repository Pattern</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 bg-white/5 p-6 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="w-12 h-12 bg-slate-800 rounded-xl flex items-center justify-center font-bold text-green-500">SQL</div>
                            <div>
                                <p class="font-bold">Eloquent ORM & Migrations</p>
                                <p class="text-xs text-slate-500">Elegante Datenbank-Interaktion & Schema-Versionierung</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- Footer -->
        <footer class="bg-slate-950 border-t border-slate-900 py-16 px-6">
            <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-white">LS25<span class="text-green-500 italic">STOCK</span></span>
                    </div>
                    <p class="text-slate-500 max-w-sm text-sm">
                        Die professionelle Wirtschafts-Simulation für den Landwirtschafts-Simulator 25. Sicher, modern und global einsetzbar in DE, EN und NL.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-white uppercase text-xs tracking-widest">Plattform</h4>
                    <ul class="text-sm text-slate-500 space-y-2 font-medium">
                        <li><a href="#" class="hover:text-green-500 transition">Aktienmarkt</a></li>
                        <li><a href="#" class="hover:text-green-500 transition">Banking</a></li>
                        <li><a href="#" class="hover:text-green-500 transition">Farm-Manager</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-white uppercase text-xs tracking-widest">Rechtliches</h4>
                    <ul class="text-sm text-slate-500 space-y-2 font-medium">
                        <li><a href="#" class="hover:text-green-500 transition">Datenschutz</a></li>
                        <li><a href="#" class="hover:text-green-500 transition">Impressum</a></li>
                    </ul>
                </div>
            </div>
            <div class="max-w-7xl mx-auto pt-8 border-t border-slate-900 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em]">
                <p>&copy; {{ date('Y') }} LS25 Aktienmarkt Simulation. No official GIANTS Software product.</p>
                <div class="flex gap-6 italic">
                    POWERED BY LARAVEL 11
                </div>
            </div>
        </footer>
    </div>

</body>
</html>