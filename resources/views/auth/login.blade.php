<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sipenmaru Uji Kesehatan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style type="text/tailwindcss">
        :root {
            --primary: #00BCD4;
            --primary-hover: #0097A7;
            --foreground: #1F2937;
            --secondary: #6B7280;
            --muted: #F3F4F6;
            --border: #E5E7EB;
            --success: #00CCA3;
            --error: #EF4444;
            --font-sans: 'Lexend Deca', sans-serif;
        }
        @theme inline {
            --color-primary: var(--primary);
            --color-primary-hover: var(--primary-hover);
            --color-foreground: var(--foreground);
            --color-secondary: var(--secondary);
            --color-muted: var(--muted);
            --color-border: var(--border);
            --color-success: var(--success);
            --color-error: var(--error);
            --font-sans: var(--font-sans);
        }
    </style>
</head>
<body class="font-sans bg-gradient-to-br from-primary/5 via-white to-success/5 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-primary rounded-3xl mb-4 shadow-xl">
                <i data-lucide="activity" class="w-10 h-10 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-foreground mb-2">Sipenmaru Uji Kesehatan</h1>
            <p class="text-secondary">Poltekkes Kemenkes</p>
        </div>
        
        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-border">
            <h2 class="text-2xl font-bold text-foreground mb-6">Masuk ke Sistem</h2>
            
            @if($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-error rounded-2xl p-4">
                    <i data-lucide="alert-circle" class="size-6 text-error shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-error mb-1">Terjadi Kesalahan</p>
                        <ul class="text-sm text-error/80 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-green-50 border border-success rounded-2xl p-4">
                    <i data-lucide="check-circle" class="size-6 text-success shrink-0"></i>
                    <p class="text-success font-medium">{{ session('success') }}</p>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-foreground mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="user" class="size-5 text-secondary"></i>
                        </div>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="{{ old('username') }}"
                            required
                            autofocus
                            class="w-full pl-12 pr-4 py-3 bg-muted border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 text-foreground placeholder:text-secondary"
                            placeholder="Masukkan username Anda"
                        >
                    </div>
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-foreground mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="size-5 text-secondary"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full pl-12 pr-4 py-3 bg-muted border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 text-foreground placeholder:text-secondary"
                            placeholder="Masukkan password Anda"
                        >
                    </div>
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary bg-muted border-border rounded focus:ring-primary focus:ring-2">
                        <span class="ml-2 text-sm text-secondary">Ingat saya</span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-primary hover:bg-primary-hover text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                    Masuk
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-secondary">
                &copy; {{ date('Y') }} Poltekkes Kemenkes. All rights reserved.
            </p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
