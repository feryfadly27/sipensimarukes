<aside id="sidebar" class="flex flex-col w-[280px] shrink-0 h-screen fixed inset-y-0 left-0 z-50 bg-white border-r border-border transform -translate-x-full lg:translate-x-0 transition-transform duration-300 overflow-hidden">
    <!-- Top Bar -->
    <div class="flex items-center justify-between border-b border-border h-[90px] px-5 gap-3">
        <div class="flex items-center gap-3">
            <div class="w-11 h-9 bg-primary rounded-xl flex items-center justify-center">
                <i data-lucide="activity" class="w-5 h-5 text-white"></i>
            </div>
            <div class="leading-tight">
                <h1 class="font-semibold text-xl">Sipenmaru</h1>
                <p class="text-xs text-secondary">Poltekkes Tasikmalaya</p>
            </div>
        </div>
        <button onclick="toggleSidebar()" aria-label="Close sidebar" class="lg:hidden size-11 flex shrink-0 bg-white rounded-xl p-[10px] items-center justify-center ring-1 ring-border hover:ring-primary transition-all duration-300 cursor-pointer">
            <i data-lucide="x" class="size-6 text-secondary"></i>
        </button>
    </div>

    <!-- Navigation -->
    <div class="flex flex-col p-5 pb-28 gap-6 overflow-y-auto flex-1">
        @if(auth()->check())
            @php
                $role = auth()->user()->role;
            @endphp
            
            <div class="flex flex-col gap-4">
                <h3 class="font-medium text-sm text-secondary">Menu Utama</h3>
                <div class="flex flex-col gap-1">
                    <!-- Dashboard - Semua Role -->
                    <a href="{{ route('dashboard') }}" class="group {{ request()->routeIs('dashboard') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="layout-dashboard" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Dashboard</span>
                        </div>
                    </a>
                    
                    @if(in_array($role, ['superadmin', 'admin']))
                    <!-- Data Peserta -->
                    <a href="{{ route('mahasiswa.index') }}" class="group {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="users" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Data Peserta</span>
                        </div>
                    </a>
                    @endif

                    @if(in_array($role, ['superadmin', 'pendaftaran']))
                    <!-- Validasi Kehadiran -->
                    <a href="{{ route('pendaftaran.index') }}" class="group {{ request()->routeIs('pendaftaran.index') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="user-check" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Validasi Kehadiran</span>
                        </div>
                    </a>
                    @endif
                    
                    @if(in_array($role, ['plp', 'superadmin']))
                    <!-- Pemeriksaan PLP -->
                    <a href="{{ route('plp.index') }}" class="group {{ request()->routeIs('plp.*') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="clipboard-list" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Pemeriksaan PLP</span>
                        </div>
                    </a>
                    @endif

                    @if(in_array($role, ['dokter', 'superadmin']))
                    <!-- Pemeriksaan Dokter -->
                    <a href="{{ route('dokter.index') }}" class="group {{ request()->routeIs('dokter.index') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="stethoscope" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Pemeriksaan Dokter</span>
                        </div>
                    </a>
                    @endif
                    
                    @if(in_array($role, ['dokter', 'superadmin']))
                    <a href="{{ route('dokter.completed') }}" class="group {{ request()->routeIs('dokter.completed') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="clipboard-check" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Sudah Diperiksa</span>
                        </div>
                    </a>
                    @endif
                </div>
            </div>

            @if(in_array($role, ['superadmin', 'admin']))
            <div class="flex flex-col gap-4">
                <h3 class="font-medium text-sm text-secondary">Laporan</h3>
                <div class="flex flex-col gap-1">
                    <a href="{{ route('laporan.index') }}" class="group {{ request()->routeIs('laporan.*') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="file-text" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Laporan</span>
                        </div>
                    </a>
                    <a href="{{ route('logs.index') }}" class="group {{ request()->routeIs('logs.*') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="history" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Log Aktivitas</span>
                        </div>
                    </a>
                </div>
            </div>
            @endif

            @if($role === 'superadmin')
            <div class="flex flex-col gap-4">
                <h3 class="font-medium text-sm text-secondary">Pengaturan</h3>
                <div class="flex flex-col gap-1">
                    <a href="{{ route('users.index') }}" class="group {{ request()->routeIs('users.*') ? 'active' : '' }} cursor-pointer">
                        <div class="flex items-center rounded-xl p-4 gap-3 bg-white group-[.active]:bg-muted group-hover:bg-muted transition-all duration-300">
                            <i data-lucide="user-cog" class="size-6 text-secondary group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300"></i>
                            <span class="font-medium text-secondary group-[.active]:font-semibold group-[.active]:text-foreground group-hover:text-foreground transition-all duration-300">Kelola User</span>
                        </div>
                    </a>
                </div>
            </div>
            @endif
        @endif
    </div>

    <!-- Help Card / Logout -->
    <div class="absolute bottom-0 left-0 w-[280px]">
        <div class="flex items-center justify-between border-t bg-white border-border p-5 gap-3">
            <div class="min-w-0">
                <p class="font-semibold text-foreground">{{ auth()->user()->nama ?? 'User' }}</p>
                <p class="text-sm text-secondary truncate">{{ ucfirst(auth()->user()->role ?? 'guest') }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="submit" class="size-11 bg-error/10 rounded-xl flex items-center justify-center flex-shrink-0 hover:bg-error/20 transition-all duration-300" onclick="handleLogout(event)">
                    <i data-lucide="log-out" class="size-6 text-error"></i>
                </button>
            </form>
        </div>
    </div>
    
    <script>
        function handleLogout(event) {
            event.preventDefault();
            
            // Prevent back button access by clearing browser cache
            if (window.history.forward(1) == null) {
                window.history.go(1);
            }
            
            // Submit logout form
            document.getElementById('logout-form').submit();
        }
        
        // Prevent accessing cached pages via back button
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Force reload if page was loaded from cache
                window.location.reload();
            }
        });
        
        // Set cache control for back button behavior
        window.addEventListener('pagehide', function() {
            // Add meta tags dynamically
            if (!document.querySelector('meta[http-equiv="Cache-Control"]')) {
                const meta = document.createElement('meta');
                meta.httpEquiv = 'Cache-Control';
                meta.content = 'no-store, no-cache, must-revalidate, max-age=0';
                document.head.appendChild(meta);
            }
        });
    </script>
</aside>
