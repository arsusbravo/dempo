<footer class="bg-brand-green-dark text-brand-cream">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3">
        <div>
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Gerai Dempo" class="h-12 w-12 rounded-full object-cover">
                <span class="text-lg font-extrabold">Gerai Dempo</span>
            </a>
            <p class="mt-3 text-sm italic text-brand-cream/80">&ldquo;{{ $texts['footer.tagline'] ?? 'Nikmatnya dari Gigitan Pertama' }}&rdquo;</p>
            <p class="mt-3 max-w-xs text-sm text-brand-cream/80">
                {{ $texts['footer.blurb'] ?? 'Katering rumahan yang menghadirkan dimsum, gorengan, dan dessert lezat untuk berbagai acara di Jakarta Pusat.' }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wide text-brand-cream/90">Tautan Cepat</h3>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="text-brand-cream/80 hover:text-white">Beranda</a></li>
                <li><a href="{{ route('menu') }}" class="text-brand-cream/80 hover:text-white">Menu</a></li>
                <li><a href="{{ route('about') }}" class="text-brand-cream/80 hover:text-white">Tentang Kami</a></li>
                <li><a href="{{ route('contact') }}" class="text-brand-cream/80 hover:text-white">Kontak</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wide text-brand-cream/90">Kontak Kami</h3>
            <ul class="mt-4 space-y-2 text-sm text-brand-cream/80">
                <li>{{ $texts['contact.address'] ?? 'Jakarta Pusat, Indonesia' }}</li>
                <li>
                    <a href="{{ whatsapp_url() }}" class="hover:text-white">
                        WhatsApp: {{ $texts['contact.whatsapp_label'] ?? '0859-2327-2645' }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-brand-cream/10 px-4 py-4 text-center text-xs text-brand-cream/70 sm:px-6">
        &copy; {{ date('Y') }} Gerai Dempo. Semua hak cipta dilindungi.
    </div>
</footer>
