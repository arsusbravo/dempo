@php
    $waConfigured = filled(config('services.whatsapp.access_token')) && filled(config('services.whatsapp.phone_number_id'));
    $waKeepOpen = session('whatsapp_chat_sent') || session('whatsapp_chat_error') || $errors->any();
@endphp

<div class="fixed bottom-6 right-6 z-50">
    <input type="checkbox" id="wa-chat-toggle" class="peer hidden" @checked($waKeepOpen)>

    <div
        class="pointer-events-none absolute bottom-20 right-0 w-80 max-w-[calc(100vw-1.5rem)] origin-bottom-right scale-95 overflow-hidden rounded-2xl bg-white opacity-0 shadow-2xl transition duration-200 peer-checked:pointer-events-auto peer-checked:scale-100 peer-checked:opacity-100"
    >
        <div class="flex items-center gap-3 bg-brand-green px-4 py-3 text-white">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Gerai Dempo" class="h-10 w-10 rounded-full object-cover">
            <div class="flex-1">
                <p class="text-sm font-bold">Gerai Dempo</p>
                <p class="text-xs text-white/80">{{ $texts['whatsapp.status'] ?? 'Biasanya membalas dengan cepat' }}</p>
            </div>
            <label for="wa-chat-toggle" class="cursor-pointer rounded-full p-1 text-white/80 hover:text-white" aria-label="Tutup chat">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </label>
        </div>

        <div class="max-h-112 overflow-y-auto bg-[#e5ded8] p-4">
            <div class="max-w-[85%] rounded-lg rounded-tl-none bg-white px-3 py-2 text-sm text-brand-brown shadow-sm">
                {{ $texts['whatsapp.greeting'] ?? 'Halo! 👋 Ada yang bisa kami bantu seputar menu atau pemesanan katering hari ini?' }}
            </div>

            @if (session('whatsapp_chat_sent'))
                <div class="mt-3 max-w-[85%] rounded-lg rounded-tl-none bg-white px-3 py-2 text-sm text-brand-brown shadow-sm">
                    Pesan Anda terkirim ke WhatsApp kami! Silakan buka aplikasi WhatsApp Anda untuk melanjutkan percakapan &mdash; kami akan segera membalas di sana. 🎉
                </div>
            @endif

            @if (session('whatsapp_chat_error'))
                <div class="mt-3 max-w-[85%] rounded-lg rounded-tl-none bg-white px-3 py-2 text-sm text-red-600 shadow-sm">
                    {{ session('whatsapp_chat_error') }}
                </div>
            @endif
        </div>

        @if ($waConfigured)
            <form action="{{ route('whatsapp.start') }}" method="POST" class="space-y-2 p-3">
                @csrf
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Nama Anda"
                    required
                    class="w-full rounded-lg border border-brand-brown/20 px-3 py-2 text-sm focus:border-brand-green focus:outline-none"
                >
                @error('name')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                @include('layouts.partials.phone-input')

                <textarea
                    name="message"
                    placeholder="Tulis pesan Anda..."
                    required
                    rows="2"
                    class="w-full resize-none rounded-lg border border-brand-brown/20 px-3 py-2 text-sm focus:border-brand-green focus:outline-none"
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-full bg-[#25D366] px-4 py-2.5 text-sm font-bold text-white transition hover:brightness-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-4 w-4 fill-white">
                        <path d="M16.004 2.667c-7.363 0-13.333 5.97-13.333 13.333 0 2.353.615 4.65 1.784 6.667L2.667 29.333l6.83-1.756a13.28 13.28 0 0 0 6.507 1.756h.006c7.362 0 13.333-5.97 13.333-13.333 0-3.56-1.387-6.907-3.904-9.424a13.245 13.245 0 0 0-9.435-3.909Zm0 24.4h-.005a11.06 11.06 0 0 1-5.64-1.544l-.405-.24-4.053 1.042 1.082-3.951-.264-.406a11.04 11.04 0 0 1-1.69-5.968c0-6.1 4.965-11.065 11.07-11.065 2.957 0 5.735 1.153 7.827 3.246a10.996 10.996 0 0 1 3.24 7.826c-.003 6.1-4.968 11.06-11.062 11.06Zm6.07-8.284c-.332-.166-1.966-.97-2.271-1.08-.305-.111-.527-.166-.75.167-.222.332-.86 1.08-1.055 1.302-.194.222-.388.25-.72.083-.332-.167-1.403-.517-2.671-1.65-.987-.88-1.654-1.967-1.848-2.3-.194-.332-.02-.512.146-.678.15-.15.332-.389.499-.583.166-.194.222-.333.332-.555.111-.223.056-.417-.028-.583-.083-.167-.75-1.807-1.028-2.475-.271-.65-.546-.562-.75-.573-.194-.01-.417-.012-.64-.012-.222 0-.583.083-.888.417-.305.332-1.166 1.14-1.166 2.78s1.194 3.226 1.36 3.448c.166.222 2.35 3.588 5.694 5.032.796.344 1.417.55 1.901.703.799.254 1.526.218 2.101.132.641-.096 1.966-.804 2.243-1.581.278-.777.278-1.443.194-1.582-.083-.138-.305-.222-.638-.389Z"/>
                    </svg>
                    Kirim Pesan
                </button>
            </form>
        @else
            <div class="p-3">
                <a
                    href="{{ whatsapp_url() }}"
                    class="flex items-center justify-center gap-2 rounded-full bg-[#25D366] px-4 py-2.5 text-sm font-bold text-white transition hover:brightness-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-4 w-4 fill-white">
                        <path d="M16.004 2.667c-7.363 0-13.333 5.97-13.333 13.333 0 2.353.615 4.65 1.784 6.667L2.667 29.333l6.83-1.756a13.28 13.28 0 0 0 6.507 1.756h.006c7.362 0 13.333-5.97 13.333-13.333 0-3.56-1.387-6.907-3.904-9.424a13.245 13.245 0 0 0-9.435-3.909Zm0 24.4h-.005a11.06 11.06 0 0 1-5.64-1.544l-.405-.24-4.053 1.042 1.082-3.951-.264-.406a11.04 11.04 0 0 1-1.69-5.968c0-6.1 4.965-11.065 11.07-11.065 2.957 0 5.735 1.153 7.827 3.246a10.996 10.996 0 0 1 3.24 7.826c-.003 6.1-4.968 11.06-11.062 11.06Zm6.07-8.284c-.332-.166-1.966-.97-2.271-1.08-.305-.111-.527-.166-.75.167-.222.332-.86 1.08-1.055 1.302-.194.222-.388.25-.72.083-.332-.167-1.403-.517-2.671-1.65-.987-.88-1.654-1.967-1.848-2.3-.194-.332-.02-.512.146-.678.15-.15.332-.389.499-.583.166-.194.222-.333.332-.555.111-.223.056-.417-.028-.583-.083-.167-.75-1.807-1.028-2.475-.271-.65-.546-.562-.75-.573-.194-.01-.417-.012-.64-.012-.222 0-.583.083-.888.417-.305.332-1.166 1.14-1.166 2.78s1.194 3.226 1.36 3.448c.166.222 2.35 3.588 5.694 5.032.796.344 1.417.55 1.901.703.799.254 1.526.218 2.101.132.641-.096 1.966-.804 2.243-1.581.278-.777.278-1.443.194-1.582-.083-.138-.305-.222-.638-.389Z"/>
                    </svg>
                    Mulai Chat di WhatsApp
                </a>
            </div>
        @endif
    </div>

    <label
        for="wa-chat-toggle"
        aria-label="Chat via WhatsApp"
        class="flex h-14 w-14 cursor-pointer items-center justify-center rounded-full bg-[#25D366] shadow-lg transition hover:scale-105"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-8 w-8 fill-white">
            <path d="M16.004 2.667c-7.363 0-13.333 5.97-13.333 13.333 0 2.353.615 4.65 1.784 6.667L2.667 29.333l6.83-1.756a13.28 13.28 0 0 0 6.507 1.756h.006c7.362 0 13.333-5.97 13.333-13.333 0-3.56-1.387-6.907-3.904-9.424a13.245 13.245 0 0 0-9.435-3.909Zm0 24.4h-.005a11.06 11.06 0 0 1-5.64-1.544l-.405-.24-4.053 1.042 1.082-3.951-.264-.406a11.04 11.04 0 0 1-1.69-5.968c0-6.1 4.965-11.065 11.07-11.065 2.957 0 5.735 1.153 7.827 3.246a10.996 10.996 0 0 1 3.24 7.826c-.003 6.1-4.968 11.06-11.062 11.06Zm6.07-8.284c-.332-.166-1.966-.97-2.271-1.08-.305-.111-.527-.166-.75.167-.222.332-.86 1.08-1.055 1.302-.194.222-.388.25-.72.083-.332-.167-1.403-.517-2.671-1.65-.987-.88-1.654-1.967-1.848-2.3-.194-.332-.02-.512.146-.678.15-.15.332-.389.499-.583.166-.194.222-.333.332-.555.111-.223.056-.417-.028-.583-.083-.167-.75-1.807-1.028-2.475-.271-.65-.546-.562-.75-.573-.194-.01-.417-.012-.64-.012-.222 0-.583.083-.888.417-.305.332-1.166 1.14-1.166 2.78s1.194 3.226 1.36 3.448c.166.222 2.35 3.588 5.694 5.032.796.344 1.417.55 1.901.703.799.254 1.526.218 2.101.132.641-.096 1.966-.804 2.243-1.581.278-.777.278-1.443.194-1.582-.083-.138-.305-.222-.638-.389Z"/>
        </svg>
    </label>
</div>
