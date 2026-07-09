@extends('layouts.public')

@section('title', 'Tentang Kami — Gerai Dempo')
@section('meta_description', 'Kenali Gerai Dempo, usaha katering rumahan di Jakarta Pusat yang menghadirkan hidangan lezat dan higienis untuk berbagai acara.')

@section('content')

    <section class="bg-white/50 py-14">
        <div class="mx-auto max-w-6xl px-4 text-center sm:px-6">
            <h1 class="text-4xl font-extrabold text-brand-green-dark">{{ $texts['about.hero.title'] ?? 'Tentang Gerai Dempo' }}</h1>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-14 sm:px-6">
        <div class="grid items-center gap-10 md:grid-cols-2">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Gerai Dempo" class="mx-auto w-48 rounded-full shadow-lg">
            <div>
                <p class="text-brand-brown/80">
                    {{ $texts['about.story'] ?? 'Gerai Dempo lahir dari kecintaan terhadap hidangan rumahan yang lezat dan higienis. Berbasis di Jakarta Pusat, kami melayani berbagai acara keluarga, kantor, hingga perayaan khusus dengan sepenuh hati.' }}
                </p>
                <p class="mt-4 text-brand-brown/80">
                    {{ $texts['about.mission'] ?? 'Setiap hidangan kami siapkan dengan bahan pilihan dan proses yang bersih, supaya setiap gigitan pertama selalu berkesan.' }}
                </p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-14 sm:px-6">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-brand-green-dark">Misi &amp; Nilai Kami</h2>
        </div>
        <div class="mt-10">
            @include('layouts.partials.keunggulan-cards')
        </div>
    </section>

    <section class="bg-brand-green py-14">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
            <h2 class="text-2xl font-extrabold text-white">Ingin Bekerja Sama dengan Kami?</h2>
            <p class="mt-3 text-white/90">Hubungi Gerai Dempo untuk kebutuhan katering acara Anda.</p>
            <a
                href="{{ whatsapp_url() }}"
                class="mt-6 inline-block rounded-full bg-white px-6 py-3 font-bold text-brand-green-dark shadow-md transition hover:bg-brand-cream"
            >
                Pesan via WhatsApp
            </a>
        </div>
    </section>

@endsection
