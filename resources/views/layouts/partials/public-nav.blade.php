@php
    $navLinks = [
        'home' => ['label' => 'Beranda', 'route' => route('home')],
        'menu' => ['label' => 'Menu', 'route' => route('menu')],
        'about' => ['label' => 'Tentang Kami', 'route' => route('about')],
        'contact' => ['label' => 'Kontak', 'route' => route('contact')],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-brand-brown/10 bg-brand-cream/95 backdrop-blur">
    <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Gerai Dempo" class="h-12 w-12 rounded-full object-cover">
            <span class="text-lg font-extrabold tracking-tight text-brand-green-dark">Gerai Dempo</span>
        </a>

        <div class="hidden items-center gap-6 md:flex">
            @foreach ($navLinks as $key => $link)
                <a
                    href="{{ $link['route'] }}"
                    class="text-sm font-semibold transition hover:text-brand-green {{ ($activeNav ?? null) === $key ? 'text-brand-green' : 'text-brand-brown' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <a
            href="{{ whatsapp_url() }}"
            class="hidden shrink-0 rounded-full bg-brand-green px-5 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-brand-green-dark md:inline-block"
        >
            Pesan Sekarang
        </a>

        <label for="mobile-nav-toggle" class="flex h-10 w-10 items-center justify-center rounded-full text-brand-brown md:hidden">
            <span class="sr-only">Buka menu</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </label>
    </nav>

    <input type="checkbox" id="mobile-nav-toggle" class="peer hidden">
    <div class="hidden flex-col gap-1 border-t border-brand-brown/10 bg-brand-cream px-4 py-3 peer-checked:flex md:hidden">
        @foreach ($navLinks as $key => $link)
            <a
                href="{{ $link['route'] }}"
                class="rounded-lg px-3 py-2 text-sm font-semibold {{ ($activeNav ?? null) === $key ? 'bg-brand-pink/25 text-brand-green-dark' : 'text-brand-brown' }}"
            >
                {{ $link['label'] }}
            </a>
        @endforeach
        <a
            href="{{ whatsapp_url() }}"
            class="mt-1 rounded-full bg-brand-green px-4 py-2 text-center text-sm font-bold text-white"
        >
            Pesan Sekarang
        </a>
    </div>
</header>
