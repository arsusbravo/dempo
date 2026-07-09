@php
    $keunggulan = [
        ['emoji' => '🥬', 'title' => 'Bahan Berkualitas', 'desc' => 'Bahan-bahan segar pilihan setiap hari.'],
        ['emoji' => '🧼', 'title' => 'Higienis & Terpercaya', 'desc' => 'Diproses dengan standar kebersihan tinggi.'],
        ['emoji' => '⏰', 'title' => 'Tepat Waktu', 'desc' => 'Pesanan tiba sesuai jadwal acara Anda.'],
        ['emoji' => '💚', 'title' => 'Harga Bersahabat', 'desc' => 'Kualitas premium, harga tetap terjangkau.'],
    ];
@endphp

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($keunggulan as $item)
        <div class="rounded-2xl border border-brand-brown/10 bg-white/60 p-6 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-pink/25 text-2xl">
                {{ $item['emoji'] }}
            </div>
            <h3 class="mt-4 font-extrabold text-brand-green-dark">{{ $item['title'] }}</h3>
            <p class="mt-1 text-sm text-brand-brown/80">{{ $item['desc'] }}</p>
        </div>
    @endforeach
</div>
