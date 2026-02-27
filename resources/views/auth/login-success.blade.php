<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Berhasil - Sipenmaru Uji Kesehatan</title>

    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style type="text/tailwindcss">
        :root {
            --primary: #00BCD4;
            --foreground: #1F2937;
            --secondary: #6B7280;
            --border: #E5E7EB;
            --success: #00CCA3;
            --font-sans: 'Lexend Deca', sans-serif;
        }
        @theme inline {
            --color-primary: var(--primary);
            --color-foreground: var(--foreground);
            --color-secondary: var(--secondary);
            --color-border: var(--border);
            --color-success: var(--success);
            --font-sans: var(--font-sans);
        }
    </style>
</head>
<body class="font-sans bg-gradient-to-br from-primary/5 via-white to-success/5 min-h-screen flex items-center justify-center p-4 pb-20">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-border text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-success rounded-3xl mb-5 shadow-xl">
                <i data-lucide="check" class="w-10 h-10 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-foreground mb-2">Login Berhasil</h1>
            <p class="text-secondary mb-4">Selamat datang, {{ $welcomeName }}.</p>
            <p class="text-sm text-secondary">Anda akan diarahkan ke dashboard dalam <span id="countdown">3</span> detik...</p>
        </div>
    </div>

    <footer class="fixed bottom-0 left-0 right-0 z-30 border-t border-border bg-white/95 backdrop-blur-sm">
        <div class="px-5 md:px-8 py-3 text-right">
            <p id="footer-live-clock" class="text-sm text-secondary"></p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();

            let remaining = 3;
            const countdownElement = document.getElementById('countdown');

            const timer = setInterval(function () {
                remaining -= 1;
                if (remaining <= 0) {
                    clearInterval(timer);
                    window.location.href = "{{ route('dashboard') }}";
                    return;
                }
                countdownElement.textContent = remaining;
            }, 1000);

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
</body>
</html>
