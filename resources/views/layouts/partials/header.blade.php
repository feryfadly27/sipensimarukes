<div class="flex items-center justify-between w-full h-[90px] shrink-0 border-b border-border bg-white px-5 md:px-8">
    <button onclick="toggleSidebar()" aria-label="Open menu" class="lg:hidden size-11 flex items-center justify-center rounded-xl ring-1 ring-border hover:ring-primary transition-all duration-300 cursor-pointer">
        <i data-lucide="menu" class="size-6 text-foreground"></i>
    </button>
    
    <h2 class="hidden lg:block font-bold text-2xl text-foreground">
        @yield('page-title', 'Dashboard')
    </h2>
    
    <div class="flex items-center gap-3">
        <!-- Notification Bell -->
        <button class="size-11 flex items-center justify-center rounded-xl ring-1 ring-border hover:ring-primary transition-all duration-300 cursor-pointer relative" aria-label="Notifications">
            <i data-lucide="bell" class="size-6 text-secondary"></i>
            @if(isset($notificationCount) && $notificationCount > 0)
                <span class="absolute -top-1 -right-1 h-5 px-1.5 rounded-full bg-error text-white text-xs font-medium flex items-center justify-center">
                    {{ $notificationCount }}
                </span>
            @endif
        </button>
        
        <!-- User Profile -->
        <div class="hidden md:flex items-center gap-3 pl-3 border-l border-border">
            <div class="text-right">
                <p class="font-semibold text-foreground text-sm">{{ auth()->user()->nama ?? 'User' }}</p>
                <p class="text-secondary text-xs">{{ ucfirst(auth()->user()->role ?? 'Guest') }}</p>
            </div>
            <div class="size-11 rounded-full object-cover ring-2 ring-border bg-primary/10 flex items-center justify-center">
                <span class="text-primary font-bold text-lg">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                </span>
            </div>
        </div>
    </div>
</div>
