<?php

if (! function_exists('whatsapp_url')) {
    function whatsapp_url(): string
    {
        return 'https://wa.me/'.config('services.whatsapp.number').'?text='.urlencode(config('services.whatsapp.default_message'));
    }
}

if (! function_exists('combine_country_phone')) {
    /**
     * Combine a country dial code and a local number into international format
     * (digits only, no leading +), stripping any leading zero(s) from the local part.
     */
    function combine_country_phone(string $countryCode, string $localNumber): string
    {
        $codeDigits = preg_replace('/\D/', '', $countryCode);
        $localDigits = ltrim(preg_replace('/\D/', '', $localNumber), '0');

        return $codeDigits.$localDigits;
    }
}
