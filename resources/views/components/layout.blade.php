<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Brwazi - Your Digital Platform' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('logo-color.ico') }}">
    <link rel="shortcut icon" href="{{ asset('logo-color.ico') }}">

    <!-- CSS & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Legacy compiled CSS (fonts, RTL tweaks) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Font performance hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Page specific styles --}}
    @stack('styles')

    {{-- Notify styles --}}
    @notifyCss
    <!-- AOS Animate On Scroll -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        /* Hide Alpine cloaked elements until Alpine initializes (prevent flash of modals/backdrops) */
        [x-cloak] {
            display: none !important;
        }

        /* Position notifications on the right, below the header */
        .brw-notify-wrapper {
            /* wrapper we add around the notify component */
            position: fixed;
            top: 5.5rem;
            /* pushes it below the header (adjust if header height changes) */
            right: 1rem;
            /* LTR */
            inset-inline-end: 1rem;
            /* RTL aware */
            z-index: 9999;
            width: 20rem;
            max-width: calc(100vw - 2rem);
            pointer-events: none;
            /* allow clicks to pass except inside cards */
        }

        .brw-notify-wrapper>* {
            pointer-events: auto;
        }

        /* If the package itself applies positioning, try to neutralize it */
        .brw-notify-wrapper .notify,
        .brw-notify-wrapper [data-notify] {
            position: static !important;
            inset: auto !important;
            margin: 0 !important;
        }

        @media (max-width: 640px) {
            .brw-notify-wrapper {
                top: 4.5rem;
                right: .75rem;
                inset-inline-end: .75rem;
                width: 18rem;
            }
        }
    </style>

    {{-- Allow pages to inject scripts that must exist before Alpine initializes (e.g., factories used in x-data) --}}
    @stack('pre-alpine')

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome already included above -->
</head>

<body class="bg-gray-50 min-h-screen">

    <x-header>
    </x-header>

    <main>
        {{ $slot }}
    </main>
    <x-footer>
    </x-footer>
    {{-- Notify component (wrapped for custom positioning) & scripts --}}
    <div class="brw-notify-wrapper">
        <x-notify::notify />
    </div>
    @notifyJs
    {{-- Page specific scripts --}}
    <!-- AOS JS and init -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.AOS) {
                AOS.init({
                    duration: 700,
                    easing: 'ease-out-cubic',
                    once: true,
                    offset: 80,
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>