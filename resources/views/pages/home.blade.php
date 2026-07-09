@extends('layouts.public')

@section('title', 'Gerai Dempo — Katering Dimsum & Snack Box Jakarta')
@section('meta_description', 'Gerai Dempo adalah katering rumahan di Jakarta Pusat yang menyajikan dimsum, gorengan, dan dessert lezat untuk berbagai acara Anda.')

@section('content')

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute -top-16 -left-16 h-72 w-72 rounded-full bg-brand-pink/40 blur-2xl"></div>
        <div class="pointer-events-none absolute top-24 right-0 h-64 w-64 rounded-full bg-brand-green/20 blur-2xl"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 md:py-20">
            <div>
                <span class="inline-block rounded-full bg-brand-pink/30 px-4 py-1 text-sm font-bold text-brand-green-dark">
                    {{ $texts['home.hero.badge'] ?? 'Katering Jakarta Pusat' }}
                </span>
                <h1 class="mt-4 text-4xl font-extrabold leading-tight text-brand-green-dark sm:text-5xl">
                    {{ $texts['home.hero.headline'] ?? 'Catering Lezat untuk Setiap Momen Spesial Anda' }}
                </h1>
                <p class="mt-4 text-lg text-brand-brown/80">
                    {{ $texts['home.hero.subheadline'] ?? 'Gerai Dempo menghadirkan dimsum, gorengan, dan dessert rumahan yang lezat untuk berbagai acara Anda.' }}
                </p>
                <p class="mt-2 text-sm italic text-brand-brown/60">&ldquo;{{ $texts['home.hero.tagline'] ?? 'Nikmatnya dari Gigitan Pertama' }}&rdquo;</p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a
                        href="{{ whatsapp_url() }}"
                        class="rounded-full bg-brand-green px-6 py-3 font-bold text-white shadow-md transition hover:bg-brand-green-dark"
                    >
                        Pesan via WhatsApp
                    </a>
                    <a
                        href="{{ route('menu') }}"
                        class="rounded-full border-2 border-brand-brown/30 px-6 py-3 font-bold text-brand-brown transition hover:border-brand-green hover:text-brand-green-dark"
                    >
                        Lihat Menu
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 -z-10 rounded-[2.5rem] bg-brand-pink/30"></div>
                <img
                    src="{{ asset('assets/images/header.png') }}"
                    alt="Hidangan katering Gerai Dempo"
                    class="w-full rounded-4xl object-cover shadow-xl"
                >
            </div>
        </div>
    </section>

    {{-- Keunggulan --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-brand-green-dark">{{ $texts['home.keunggulan.title'] ?? 'Keunggulan Kami' }}</h2>
            <p class="mt-2 text-brand-brown/70">{{ $texts['home.keunggulan.subtitle'] ?? 'Alasan kenapa Gerai Dempo jadi pilihan katering Anda.' }}</p>
        </div>
        <div class="mt-10">
            @include('layouts.partials.keunggulan-cards')
        </div>
    </section>

    {{-- Menu Unggulan --}}
    <section id="menu-unggulan" class="bg-white/50 py-14">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-brand-green-dark">{{ $texts['home.menu.title'] ?? 'Menu Unggulan' }}</h2>
                <p class="mt-2 text-brand-brown/70">{{ $texts['home.menu.subtitle'] ?? 'Beberapa favorit pelanggan kami.' }}</p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach ($featuredMenus as $menu)
                    <div class="overflow-hidden rounded-2xl border border-brand-brown/10 bg-white shadow-sm">
                        <div class="aspect-4/5 w-full overflow-hidden">
                            @if ($menu->image)
                                <img src="{{ asset('storage/'.$menu->image) }}" alt="{{ $menu->name }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-extrabold text-brand-green-dark">{{ $menu->name }}</h3>
                            <p class="mt-1 text-sm text-brand-brown/80">{{ $menu->description }}</p>
                            @if ($menu->price)
                                <p class="mt-2 text-sm font-bold text-brand-green-dark">Mulai dari Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a
                    href="{{ route('menu') }}"
                    class="inline-block rounded-full bg-brand-green px-6 py-3 font-bold text-white shadow-md transition hover:bg-brand-green-dark"
                >
                    Lihat Menu Lengkap
                </a>
            </div>
        </div>
    </section>

    {{-- Tentang Kami teaser --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="grid items-center gap-10 md:grid-cols-2">
            <div>
                <h2 class="text-3xl font-extrabold text-brand-green-dark">Tentang Gerai Dempo</h2>
                <p class="mt-4 text-brand-brown/80">
                    {{ $texts['home.about.teaser'] ?? 'Gerai Dempo adalah usaha katering rumahan yang berbasis di Jakarta Pusat, lahir dari kecintaan terhadap hidangan lezat dan higienis.' }}
                </p>
                <a href="{{ route('about') }}" class="mt-4 inline-block font-bold text-brand-green-dark hover:underline">
                    Selengkapnya Tentang Kami &rarr;
                </a>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 -z-10 rounded-[2.5rem] bg-brand-green/15"></div>
                <img
                    src="{{ asset('assets/images/logo.png') }}"
                    alt="Gerai Dempo"
                    class="mx-auto w-56 rounded-full shadow-lg"
                >
            </div>
        </div>
    </section>

    {{-- Kontak / CTA banner --}}
    <section class="bg-brand-green py-14">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
            <h2 class="text-3xl font-extrabold text-white">{{ $texts['home.cta.title'] ?? 'Siap Memesan Catering untuk Acara Anda?' }}</h2>
            <p class="mt-3 text-white/90">{{ $texts['home.cta.subtitle'] ?? 'Hubungi kami sekarang dan wujudkan acara Anda dengan hidangan lezat dari Gerai Dempo.' }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a
                    href="{{ whatsapp_url() }}"
                    class="rounded-full bg-white px-6 py-3 font-bold text-brand-green-dark shadow-md transition hover:bg-brand-cream"
                >
                    Pesan via WhatsApp
                </a>
                <a
                    href="{{ route('contact') }}"
                    class="rounded-full border-2 border-white px-6 py-3 font-bold text-white transition hover:bg-white/10"
                >
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

@endsection
