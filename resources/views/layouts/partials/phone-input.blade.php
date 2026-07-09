@php
    $countryCodes = [
        '62' => '🇮🇩 Indonesia (+62)',
        '65' => '🇸🇬 Singapura (+65)',
        '60' => '🇲🇾 Malaysia (+60)',
        '31' => '🇳🇱 Belanda (+31)',
        '61' => '🇦🇺 Australia (+61)',
        '1' => '🇺🇸 Amerika Serikat (+1)',
        '44' => '🇬🇧 Inggris (+44)',
        '49' => '🇩🇪 Jerman (+49)',
        '33' => '🇫🇷 Prancis (+33)',
        '81' => '🇯🇵 Jepang (+81)',
        '82' => '🇰🇷 Korea Selatan (+82)',
        '86' => '🇨🇳 Tiongkok (+86)',
        '91' => '🇮🇳 India (+91)',
        '66' => '🇹🇭 Thailand (+66)',
        '63' => '🇵🇭 Filipina (+63)',
        '971' => '🇦🇪 UEA (+971)',
    ];
@endphp

<div class="flex gap-2">
    <select
        name="country_code"
        @if (isset($formId)) form="{{ $formId }}" @endif
        data-country-code
        class="rounded-lg border border-brand-brown/20 bg-white px-2 py-2 text-sm focus:border-brand-green focus:outline-none"
    >
        @foreach ($countryCodes as $code => $label)
            <option value="{{ $code }}" @selected(old('country_code', '62') === $code)>{{ $label }}</option>
        @endforeach
    </select>
    <input
        type="text"
        name="phone_local"
        @if (isset($formId)) form="{{ $formId }}" @endif
        data-phone-local
        value="{{ old('phone_local') }}"
        placeholder="812xxxxxxx"
        required
        class="flex-1 rounded-lg border border-brand-brown/20 px-3 py-2 text-sm focus:border-brand-green focus:outline-none"
    >
</div>
@error('country_code')
    <p class="text-xs text-red-600">{{ $message }}</p>
@enderror
@error('phone_local')
    <p class="text-xs text-red-600">{{ $message }}</p>
@enderror
