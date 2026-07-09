<?php

namespace Database\Seeders;

use App\Models\WebsiteText;
use Illuminate\Database\Seeder;

class WebsiteTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $texts = [
            ['key' => 'home.hero.badge', 'group' => 'home', 'label' => 'Hero Badge', 'value' => 'Katering Jakarta Pusat'],
            ['key' => 'home.hero.headline', 'group' => 'home', 'label' => 'Hero Headline', 'value' => 'Catering Lezat untuk Setiap Momen Spesial Anda'],
            ['key' => 'home.hero.subheadline', 'group' => 'home', 'label' => 'Hero Subheadline', 'value' => 'Gerai Dempo menghadirkan dimsum, gorengan, dan dessert rumahan yang lezat — siap mengisi acara keluarga, kantor, dan perayaan Anda di Jakarta Pusat dan sekitarnya.'],
            ['key' => 'home.hero.tagline', 'group' => 'home', 'label' => 'Hero Tagline', 'value' => 'Nikmatnya dari Gigitan Pertama'],
            ['key' => 'home.keunggulan.title', 'group' => 'home', 'label' => 'Keunggulan Title', 'value' => 'Keunggulan Kami'],
            ['key' => 'home.keunggulan.subtitle', 'group' => 'home', 'label' => 'Keunggulan Subtitle', 'value' => 'Alasan kenapa Gerai Dempo jadi pilihan katering Anda.'],
            ['key' => 'home.menu.title', 'group' => 'home', 'label' => 'Menu Section Title', 'value' => 'Menu Unggulan'],
            ['key' => 'home.menu.subtitle', 'group' => 'home', 'label' => 'Menu Section Subtitle', 'value' => 'Beberapa favorit pelanggan kami.'],
            ['key' => 'home.about.teaser', 'group' => 'home', 'label' => 'About Teaser', 'value' => 'Gerai Dempo adalah usaha katering rumahan yang berbasis di Jakarta Pusat, lahir dari kecintaan terhadap hidangan lezat dan higienis. Kami senang membantu acara keluarga, kantor, maupun perayaan Anda menjadi lebih berkesan.'],
            ['key' => 'home.cta.title', 'group' => 'home', 'label' => 'CTA Title', 'value' => 'Siap Memesan Catering untuk Acara Anda?'],
            ['key' => 'home.cta.subtitle', 'group' => 'home', 'label' => 'CTA Subtitle', 'value' => 'Hubungi kami sekarang dan wujudkan acara Anda dengan hidangan lezat dari Gerai Dempo.'],

            ['key' => 'about.hero.title', 'group' => 'about', 'label' => 'About Hero Title', 'value' => 'Tentang Gerai Dempo'],
            ['key' => 'about.story', 'group' => 'about', 'label' => 'Founding Story', 'value' => 'Gerai Dempo lahir dari kecintaan terhadap hidangan rumahan yang lezat dan higienis. Berbasis di Jakarta Pusat, kami melayani berbagai acara keluarga, kantor, hingga perayaan khusus dengan sepenuh hati.'],
            ['key' => 'about.mission', 'group' => 'about', 'label' => 'Mission Statement', 'value' => 'Setiap hidangan kami siapkan dengan bahan pilihan dan proses yang bersih, supaya setiap gigitan pertama selalu berkesan — sesuai dengan motto kami, "Nikmatnya dari Gigitan Pertama".'],

            ['key' => 'contact.hero.title', 'group' => 'contact', 'label' => 'Contact Hero Title', 'value' => 'Hubungi Kami'],
            ['key' => 'contact.hero.subtitle', 'group' => 'contact', 'label' => 'Contact Hero Subtitle', 'value' => 'Kami siap membantu kebutuhan katering acara Anda.'],
            ['key' => 'contact.address', 'group' => 'contact', 'label' => 'Address', 'value' => 'Jakarta Pusat, Indonesia'],
            ['key' => 'contact.whatsapp_label', 'group' => 'contact', 'label' => 'WhatsApp Display Number', 'value' => '0859-2327-2645'],

            ['key' => 'footer.tagline', 'group' => 'footer', 'label' => 'Footer Tagline', 'value' => 'Nikmatnya dari Gigitan Pertama'],
            ['key' => 'footer.blurb', 'group' => 'footer', 'label' => 'Footer Blurb', 'value' => 'Katering rumahan yang menghadirkan dimsum, gorengan, dan dessert lezat untuk berbagai acara di Jakarta Pusat.'],

            ['key' => 'whatsapp.greeting', 'group' => 'whatsapp', 'label' => 'WhatsApp Widget Greeting', 'value' => 'Halo! 👋 Ada yang bisa kami bantu seputar menu atau pemesanan katering hari ini?'],
            ['key' => 'whatsapp.status', 'group' => 'whatsapp', 'label' => 'WhatsApp Widget Status', 'value' => 'Biasanya membalas dengan cepat'],

            ['key' => 'payment.bank_name', 'group' => 'payment', 'label' => 'Bank Name', 'value' => 'BCA'],
            ['key' => 'payment.bank_account_number', 'group' => 'payment', 'label' => 'Bank Account Number', 'value' => '000-000-0000'],
            ['key' => 'payment.bank_account_holder', 'group' => 'payment', 'label' => 'Bank Account Holder Name', 'value' => 'Gerai Dempo'],
        ];

        foreach ($texts as $text) {
            WebsiteText::updateOrCreate(['key' => $text['key']], $text);
        }
    }
}
