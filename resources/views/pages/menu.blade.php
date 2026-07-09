@extends('layouts.public')

@section('title', 'Menu Kami — Gerai Dempo')
@section('meta_description', 'Jelajahi menu katering Gerai Dempo: dimsum, gorengan & risol, hingga dessert manis untuk acara Anda di Jakarta Pusat.')

@section('content')

    <section class="bg-white/50 py-14">
        <div class="mx-auto max-w-6xl px-4 text-center sm:px-6">
            <h1 class="text-4xl font-extrabold text-brand-green-dark">Menu Kami</h1>
            <p class="mt-3 text-brand-brown/80">
                Pilih menu dan jumlah yang Anda inginkan, lalu klik &ldquo;Pesan Sekarang&rdquo; untuk memesan lewat WhatsApp.
            </p>
            @error('items')
                <p class="mt-3 text-sm font-semibold text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </section>

    <form id="order-form" method="POST" action="{{ route('orders.store') }}">
        @csrf
    </form>

    <section class="mx-auto max-w-6xl px-4 pt-14 pb-28 sm:px-6">
        <div class="flex flex-wrap justify-center gap-6">
            @foreach ($menus as $menu)
                <div
                    class="w-full overflow-hidden rounded-2xl border border-brand-brown/10 bg-white shadow-sm sm:w-72"
                    @if ($menu->price)
                        data-menu-card
                        data-menu-id="{{ $menu->id }}"
                        data-menu-name="{{ $menu->name }}"
                        data-menu-price="{{ $menu->price }}"
                    @endif
                >
                    <div class="aspect-4/5 w-full overflow-hidden">
                        @if ($menu->image)
                            <img src="{{ asset('storage/'.$menu->image) }}" alt="{{ $menu->name }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="p-5">
                        <span class="text-xs font-bold uppercase tracking-wide text-brand-green">{{ $menu->category->name }}</span>
                        <h3 class="mt-1 font-extrabold text-brand-green-dark">{{ $menu->name }}</h3>
                        <p class="mt-1 text-sm text-brand-brown/80">{{ $menu->description }}</p>

                        @if ($menu->price)
                            <p class="mt-2 text-sm font-bold text-brand-green-dark">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>

                            <div class="mt-3 flex items-center gap-3">
                                <button type="button" data-qty-decrement class="flex h-8 w-8 items-center justify-center rounded-full border border-brand-brown/20 text-brand-brown transition hover:border-brand-green hover:text-brand-green-dark">
                                    &minus;
                                </button>
                                <span data-qty-value class="w-6 text-center font-bold text-brand-green-dark">0</span>
                                <button type="button" data-qty-increment class="flex h-8 w-8 items-center justify-center rounded-full border border-brand-brown/20 text-brand-brown transition hover:border-brand-green hover:text-brand-green-dark">
                                    &plus;
                                </button>
                            </div>
                        @else
                            <a href="{{ whatsapp_url() }}" class="mt-3 inline-block text-sm font-bold text-brand-green-dark hover:underline">
                                Pesan via WhatsApp &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-brand-green py-14">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
            <h2 class="text-2xl font-extrabold text-white">Tidak menemukan menu yang Anda cari?</h2>
            <p class="mt-3 text-white/90">Tanyakan langsung ke kami, kami siap membantu menyesuaikan pesanan sesuai kebutuhan acara Anda.</p>
            <a
                href="{{ whatsapp_url() }}"
                class="mt-6 inline-block rounded-full bg-white px-6 py-3 font-bold text-brand-green-dark shadow-md transition hover:bg-brand-cream"
            >
                Tanya via WhatsApp
            </a>
        </div>
    </section>

    {{-- Sticky order summary bar --}}
    <div data-order-summary-bar class="fixed inset-x-0 bottom-0 z-40 hidden border-t border-brand-brown/10 bg-white px-4 py-3 shadow-[0_-4px_12px_rgba(0,0,0,0.1)] sm:px-6">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 pr-20 sm:pr-24">
            <div>
                <p class="text-xs text-brand-brown/70">Total Pesanan</p>
                <p data-order-total class="text-lg font-extrabold text-brand-green-dark">Rp 0</p>
            </div>
            <button
                type="button"
                data-open-confirm
                disabled
                class="rounded-full bg-brand-green px-6 py-2.5 font-bold text-white shadow-md transition hover:bg-brand-green-dark disabled:cursor-not-allowed disabled:opacity-40"
            >
                Pesan Sekarang
            </button>
        </div>
    </div>

    {{-- Confirmation dialog --}}
    <dialog data-confirm-dialog class="w-full max-w-md rounded-2xl border border-brand-brown/10 p-0 shadow-2xl backdrop:bg-brand-brown/40">
        <div class="p-6">
            <h3 class="text-xl font-extrabold text-brand-green-dark">Konfirmasi Pesanan</h3>
            <p class="mt-1 text-sm text-brand-brown/70">Pastikan pesanan Anda sudah benar sebelum melanjutkan ke WhatsApp.</p>

            <ul data-confirm-items class="mt-4 space-y-1 text-sm text-brand-brown"></ul>

            <div class="mt-4 flex items-center justify-between border-t border-brand-brown/10 pt-3">
                <span class="font-bold text-brand-brown">Total</span>
                <span data-confirm-total class="font-extrabold text-brand-green-dark"></span>
            </div>

            <div class="mt-4 space-y-3 border-t border-brand-brown/10 pt-4">
                <div>
                    <label class="text-xs font-semibold text-brand-brown/70">Nama Anda</label>
                    <input
                        type="text"
                        form="order-form"
                        name="name"
                        data-input-name
                        required
                        maxlength="100"
                        placeholder="Nama Anda"
                        class="mt-1 w-full rounded-lg border border-brand-brown/20 px-3 py-2 text-sm focus:border-brand-green focus:outline-none"
                    >
                </div>
                <div>
                    <label class="text-xs font-semibold text-brand-brown/70">Nomor WhatsApp</label>
                    <div class="mt-1">
                        @include('layouts.partials.phone-input', ['formId' => 'order-form'])
                    </div>
                </div>
                <p data-confirm-error class="hidden text-xs font-semibold text-red-600"></p>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" data-cancel-confirm class="flex-1 rounded-full border-2 border-brand-brown/20 px-4 py-2.5 font-bold text-brand-brown transition hover:border-brand-green hover:text-brand-green-dark">
                    Batal
                </button>
                <button type="button" data-submit-confirm class="flex-1 rounded-full bg-brand-green px-4 py-2.5 font-bold text-white transition hover:bg-brand-green-dark">
                    Ya, Pesan Sekarang
                </button>
            </div>
        </div>
    </dialog>

    <script>
        (function () {
            const cards = document.querySelectorAll('[data-menu-card]');
            const summaryBar = document.querySelector('[data-order-summary-bar]');
            const totalEl = document.querySelector('[data-order-total]');
            const openBtn = document.querySelector('[data-open-confirm]');
            const dialog = document.querySelector('[data-confirm-dialog]');
            const confirmItemsEl = document.querySelector('[data-confirm-items]');
            const confirmTotalEl = document.querySelector('[data-confirm-total]');
            const cancelBtn = document.querySelector('[data-cancel-confirm]');
            const submitBtn = document.querySelector('[data-submit-confirm]');
            const nameInput = document.querySelector('[data-input-name]');
            const phoneLocalInput = document.querySelector('[data-phone-local]');
            const errorEl = document.querySelector('[data-confirm-error]');
            const form = document.getElementById('order-form');
            const phoneLocalPattern = /^[0-9]{5,13}$/;

            function formatRupiah(amount) {
                return 'Rp ' + amount.toLocaleString('id-ID');
            }

            function getCart() {
                const items = [];
                let total = 0;

                cards.forEach(function (card) {
                    const qty = parseInt(card.querySelector('[data-qty-value]').textContent, 10) || 0;

                    if (qty > 0) {
                        const price = parseFloat(card.dataset.menuPrice);
                        items.push({
                            id: card.dataset.menuId,
                            name: card.dataset.menuName,
                            qty: qty,
                            subtotal: price * qty,
                        });
                        total += price * qty;
                    }
                });

                return { items: items, total: total };
            }

            function updateSummary() {
                const cart = getCart();
                totalEl.textContent = formatRupiah(cart.total);
                openBtn.disabled = cart.items.length === 0;
                summaryBar.classList.toggle('hidden', cart.items.length === 0);
            }

            cards.forEach(function (card) {
                const valueEl = card.querySelector('[data-qty-value]');

                card.querySelector('[data-qty-decrement]').addEventListener('click', function () {
                    valueEl.textContent = Math.max(0, parseInt(valueEl.textContent, 10) - 1);
                    updateSummary();
                });

                card.querySelector('[data-qty-increment]').addEventListener('click', function () {
                    valueEl.textContent = Math.min(99, parseInt(valueEl.textContent, 10) + 1);
                    updateSummary();
                });
            });

            openBtn.addEventListener('click', function () {
                const cart = getCart();

                confirmItemsEl.innerHTML = cart.items.map(function (item) {
                    return '<li class="flex justify-between"><span>' + item.qty + 'x ' + item.name + '</span><span>' + formatRupiah(item.subtotal) + '</span></li>';
                }).join('');

                confirmTotalEl.textContent = formatRupiah(cart.total);
                dialog.showModal();
            });

            cancelBtn.addEventListener('click', function () {
                dialog.close();
            });

            submitBtn.addEventListener('click', function () {
                const name = nameInput.value.trim();
                const phoneLocal = phoneLocalInput.value.trim().replace(/\D/g, '');

                if (! name) {
                    errorEl.textContent = 'Nama wajib diisi.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                if (! phoneLocalPattern.test(phoneLocal)) {
                    errorEl.textContent = 'Nomor WhatsApp tidak valid. Pilih kode negara lalu isi nomor lokal Anda (mis. 812xxxxxxx).';
                    errorEl.classList.remove('hidden');
                    return;
                }

                errorEl.classList.add('hidden');

                const cart = getCart();

                cart.items.forEach(function (item, index) {
                    const menuIdInput = document.createElement('input');
                    menuIdInput.type = 'hidden';
                    menuIdInput.name = 'items[' + index + '][menu_id]';
                    menuIdInput.value = item.id;
                    form.appendChild(menuIdInput);

                    const quantityInput = document.createElement('input');
                    quantityInput.type = 'hidden';
                    quantityInput.name = 'items[' + index + '][quantity]';
                    quantityInput.value = item.qty;
                    form.appendChild(quantityInput);
                });

                form.submit();
            });
        })();
    </script>

@endsection
