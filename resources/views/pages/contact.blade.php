@extends('layouts.public')

@section('title', 'Kontak — Gerai Dempo')
@section('meta_description', 'Hubungi Gerai Dempo via WhatsApp untuk pemesanan katering di Jakarta Pusat.')

@section('content')

    <section class="bg-white/50 py-14">
        <div class="mx-auto max-w-6xl px-4 text-center sm:px-6">
            <h1 class="text-4xl font-extrabold text-brand-green-dark">{{ $texts['contact.hero.title'] ?? 'Hubungi Kami' }}</h1>
            <p class="mt-3 text-brand-brown/80">{{ $texts['contact.hero.subtitle'] ?? 'Kami siap membantu kebutuhan katering acara Anda.' }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-14 sm:px-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-brand-brown/10 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-8 w-8 fill-white">
                        <path d="M16.004 2.667c-7.363 0-13.333 5.97-13.333 13.333 0 2.353.615 4.65 1.784 6.667L2.667 29.333l6.83-1.756a13.28 13.28 0 0 0 6.507 1.756h.006c7.362 0 13.333-5.97 13.333-13.333 0-3.56-1.387-6.907-3.904-9.424a13.245 13.245 0 0 0-9.435-3.909Zm0 24.4h-.005a11.06 11.06 0 0 1-5.64-1.544l-.405-.24-4.053 1.042 1.082-3.951-.264-.406a11.04 11.04 0 0 1-1.69-5.968c0-6.1 4.965-11.065 11.07-11.065 2.957 0 5.735 1.153 7.827 3.246a10.996 10.996 0 0 1 3.24 7.826c-.003 6.1-4.968 11.06-11.062 11.06Z"/>
                    </svg>
                </div>
                <h2 class="mt-4 font-extrabold text-brand-green-dark">WhatsApp</h2>
                <p class="mt-1 text-brand-brown/80">{{ $texts['contact.whatsapp_label'] ?? '0859-2327-2645' }}</p>
                <a
                    href="{{ whatsapp_url() }}"
                    class="mt-4 inline-block rounded-full bg-brand-green px-6 py-2.5 font-bold text-white shadow-sm transition hover:bg-brand-green-dark"
                >
                    Chat Sekarang
                </a>
            </div>

            <div class="rounded-2xl border border-brand-brown/10 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-pink/30 text-2xl">
                    📍
                </div>
                <h2 class="mt-4 font-extrabold text-brand-green-dark">Lokasi</h2>
                <p class="mt-1 text-brand-brown/80">{{ $texts['contact.address'] ?? 'Jakarta Pusat, Indonesia' }}</p>
            </div>
        </div>

        <div class="mt-10 overflow-hidden rounded-2xl border border-brand-brown/10 shadow-sm">
            <iframe
                src="https://www.google.com/maps?q=Jakarta+Pusat&output=embed"
                class="h-80 w-full"
                style="border:0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Lokasi Gerai Dempo di Jakarta Pusat"
            ></iframe>
        </div>
    </section>

@endsection
