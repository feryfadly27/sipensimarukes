<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sipenmaru Uji Kesehatan</title>
    <meta name="description" content="Sistem Informasi Uji Kesehatan Sipenmaru Poltekkes Kemenkes">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" onload="window.lucideLoaded=true; if(window.initLucide) window.initLucide();"></script>
    <script>
        window.initLucide = function() { if(window.lucide) lucide.createIcons(); };
        document.addEventListener('DOMContentLoaded', function() { if(window.lucideLoaded) window.initLucide(); });
    </script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style type="text/tailwindcss">
        :root {
            --primary: #00BCD4;
            --primary-hover: #0097A7;
            --foreground: #1F2937;
            --secondary: #6B7280;
            --muted: #F3F4F6;
            --border: #E5E7EB;
            --card-grey: #F9FAFB;
            --card-message: #CCFBF1;
            --accent-blue: #CCFBF1;
            --accent-teal: #00CCA3;
            --success: #00CCA3;
            --success-light: #D1FAE5;
            --error: #EF4444;
            --error-light: #FEE2E2;
            --warning: #D4E157;
            --warning-light: #FEF9C3;
            --warning-dark: #9E9D24;
            --font-sans: 'Lexend Deca', sans-serif;
        }
        @theme inline {
            --color-primary: var(--primary);
            --color-primary-hover: var(--primary-hover);
            --color-foreground: var(--foreground);
            --color-secondary: var(--secondary);
            --color-muted: var(--muted);
            --color-border: var(--border);
            --color-card-grey: var(--card-grey);
            --color-success: var(--success);
            --color-success-light: var(--success-light);
            --color-error: var(--error);
            --color-error-light: var(--error-light);
            --color-warning: var(--warning);
            --color-warning-light: var(--warning-light);
            --font-sans: var(--font-sans);
            --radius-card: 24px;
            --radius-button: 50px;
            --radius-icon: 12px;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans bg-white min-h-screen overflow-x-hidden">
    
    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/80 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>
    
    <div class="flex h-screen max-h-screen flex-1 bg-muted overflow-hidden">
        <!-- SIDEBAR -->
        @include('layouts.partials.sidebar')
        
        <!-- MAIN CONTENT -->
        <main class="flex-1 lg:ml-[280px] flex flex-col bg-white min-h-screen overflow-x-hidden">
            <!-- HEADER -->
            @include('layouts.partials.header')
            
            <!-- CONTENT -->
            <div class="flex-1 overflow-y-auto p-5 md:p-8 pb-24">
                @if(session('success'))
                    <div class="mb-6 flex items-center gap-3 bg-success-light border border-success rounded-2xl p-4">
                        <i data-lucide="check-circle" class="size-6 text-success shrink-0"></i>
                        <p class="text-success font-medium">{{ session('success') }}</p>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 flex items-center gap-3 bg-error-light border border-error rounded-2xl p-4">
                        <i data-lucide="alert-circle" class="size-6 text-error shrink-0"></i>
                        <p class="text-error font-medium">{{ session('error') }}</p>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>

    <footer class="fixed bottom-0 left-0 right-0 lg:left-[280px] z-30 border-t border-border bg-white/95 backdrop-blur-sm">
        <div class="px-5 md:px-8 py-3 text-right">
            <p id="footer-live-clock" class="text-sm text-secondary"></p>
        </div>
    </footer>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            const footerClockElement = document.getElementById('footer-live-clock');
            if (footerClockElement) {
                const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });

                const toTitleCase = (text) => text.replace(/\b\w/g, (char) => char.toUpperCase());

                const updateFooterClock = () => {
                    const now = new Date();
                    const formattedDate = toTitleCase(dateFormatter.format(now));
                    const timeParts = [
                        String(now.getHours()).padStart(2, '0'),
                        String(now.getMinutes()).padStart(2, '0'),
                        String(now.getSeconds()).padStart(2, '0')
                    ];

                    footerClockElement.textContent = `${formattedDate} | ${timeParts.join('.')} || Made by Fery Fadly`;
                };

                updateFooterClock();
                setInterval(updateFooterClock, 1000);
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
