<?php

namespace App\Helpers;

use Spatie\Color\Rgb;
use Spatie\Color\Rgba;
use Filament\Support\Facades\FilamentColor;

class Helpers {
    public static function getColorRgba(string $color, int $intensity, float $alpha = 1): Rgba {
        return Rgba::fromString('rgba(' . FilamentColor::getColors()[$color][$intensity] . ', ' . $alpha .')');
    }

    public static function getColorRgb(string $color, int $intensity): Rgb {
        return Rgb::fromString('rgb(' . FilamentColor::getColors()[$color][$intensity] .')');
    }

    public static function getAllLocales($simple = false): array
    {
        return $simple ? [
            'af' => 'South Africa',
            'ar' => 'United Arab Emirates',
            'hy' => 'Armenia',
            'bn' => 'Bangladesh',
            'bs' => 'Bosnia and Herzegovina',
            'ca' => 'Spain',
            'cs' => 'Czech Republic',
            'cy' => 'United Kingdom',
            'da' => 'Denmark',
            'de' => 'Austria',
            'el' => 'Greece',
            'en' => 'Australia',
            'es' => 'Argentina',
            'et' => 'Estonia',
            'eu' => 'Basque Country',
            'fa' => 'Iran',
            'fi' => 'Finland',
            'fo' => 'Faroe Islands',
            'fr' => 'Belgium',
            'ga' => 'Ireland',
            'gl' => 'Galicia',
            'gu' => 'India',
            'he' => 'Israel',
            'hi' => 'India',
            'hr' => 'Croatia',
            'hu' => 'Hungary',
            'id' => 'Indonesia',
            'is' => 'Iceland',
            'it' => 'Italy',
            'ja' => 'Japan',
            'ka' => 'Georgia',
            'kk' => 'Kazakhstan',
            'km' => 'Cambodia',
            'kn' => 'India',
            'ko' => 'South Korea',
            'ky' => 'Kyrgyzstan',
            'lo' => 'Laos',
            'lt' => 'Lithuania',
            'lv' => 'Latvia',
            'mk' => 'North Macedonia',
            'ml' => 'India',
            'mr' => 'India',
            'ms' => 'Malaysia',
            'nb' => 'Norway',
            'ne' => 'Nepal',
            'nl' => 'Belgium',
            'nn' => 'Norway',
            'pa' => 'India',
            'pl' => 'Poland',
            'ps' => 'Afghanistan',
            'pt' => 'Portugal',
            'qu' => 'Peru',
            'ro' => 'Romania',
            'ru' => 'Russia',
            'si' => 'Sri Lanka',
            'sk' => 'Slovakia',
            'sl' => 'Slovenia',
            'sq' => 'Albania',
            'sr' => 'Serbia',
            'sv' => 'Sweden',
            'sw' => 'Tanzania',
            'ta' => 'India',
            'te' => 'India',
            'th' => 'Thailand',
            'tl' => 'Philippines',
            'tr' => 'Turkey',
            'uk' => 'Ukraine',
            'uz' => 'Uzbekistan',
            'vi' => 'Vietnam',
            'xh' => 'South Africa',
            'zh' => 'China',
            'zu' => 'South Africa'
        ] : [
            'af_ZA' => 'South Africa',
            'ar_AE' => 'United Arab Emirates',
            'ar_BH' => 'Bahrain',
            'ar_DZ' => 'Algeria',
            'ar_EG' => 'Egypt',
            'ar_IQ' => 'Iraq',
            'ar_JO' => 'Jordan',
            'ar_KW' => 'Kuwait',
            'ar_LB' => 'Lebanon',
            'ar_LY' => 'Libya',
            'ar_MA' => 'Morocco',
            'ar_OM' => 'Oman',
            'ar_QA' => 'Qatar',
            'ar_SA' => 'Saudi Arabia',
            'ar_SY' => 'Syria',
            'ar_TN' => 'Tunisia',
            'ar_YE' => 'Yemen',
            'hy_AM' => 'Armenia',
            'bn_BD' => 'Bangladesh',
            'bn_IN' => 'India',
            'bs_BA' => 'Bosnia and Herzegovina',
            'ca_ES' => 'Spain',
            'cs_CZ' => 'Czech Republic',
            'cy_GB' => 'United Kingdom',
            'da_DK' => 'Denmark',
            'de_AT' => 'Austria',
            'de_BE' => 'Belgium',
            'de_CH' => 'Switzerland',
            'de_DE' => 'Germany',
            'de_LU' => 'Luxembourg',
            'el_GR' => 'Greece',
            'en_AU' => 'Australia',
            'en_CA' => 'Canada',
            'en_GB' => 'United Kingdom',
            'en_IE' => 'Ireland',
            'en_IN' => 'India',
            'en_NZ' => 'New Zealand',
            'en_PH' => 'Philippines',
            'en_SG' => 'Singapore',
            'en_US' => 'United States',
            'en_ZA' => 'South Africa',
            'es_AR' => 'Argentina',
            'es_BO' => 'Bolivia',
            'es_CL' => 'Chile',
            'es_CO' => 'Colombia',
            'es_CR' => 'Costa Rica',
            'es_DO' => 'Dominican Republic',
            'es_EC' => 'Ecuador',
            'es_ES' => 'Spain',
            'es_GT' => 'Guatemala',
            'es_HN' => 'Honduras',
            'es_MX' => 'Mexico',
            'es_NI' => 'Nicaragua',
            'es_PA' => 'Panama',
            'es_PE' => 'Peru',
            'es_PR' => 'Puerto Rico',
            'es_UY' => 'Uruguay',
            'es_VE' => 'Venezuela',
            'et_EE' => 'Estonia',
            'eu_ES' => 'Basque Country',
            'fa_IR' => 'Iran',
            'fi_FI' => 'Finland',
            'fo_FO' => 'Faroe Islands',
            'fr_BE' => 'Belgium',
            'fr_CA' => 'Canada',
            'fr_CH' => 'Switzerland',
            'fr_FR' => 'France',
            'fr_LU' => 'Luxembourg',
            'ga_IE' => 'Ireland',
            'gl_ES' => 'Galicia',
            'gu_IN' => 'India',
            'he_IL' => 'Israel',
            'hi_IN' => 'India',
            'hr_HR' => 'Croatia',
            'hu_HU' => 'Hungary',
            'id_ID' => 'Indonesia',
            'is_IS' => 'Iceland',
            'it_IT' => 'Italy',
            'it_CH' => 'Switzerland',
            'ja_JP' => 'Japan',
            'ka_GE' => 'Georgia',
            'kk_KZ' => 'Kazakhstan',
            'km_KH' => 'Cambodia',
            'kn_IN' => 'India',
            'ko_KR' => 'South Korea',
            'ky_KG' => 'Kyrgyzstan',
            'lo_LA' => 'Laos',
            'lt_LT' => 'Lithuania',
            'lv_LV' => 'Latvia',
            'mk_MK' => 'North Macedonia',
            'ml_IN' => 'India',
            'mr_IN' => 'India',
            'ms_MY' => 'Malaysia',
            'nb_NO' => 'Norway',
            'ne_NP' => 'Nepal',
            'nl_BE' => 'Belgium',
            'nl_NL' => 'Netherlands',
            'nn_NO' => 'Norway',
            'pa_IN' => 'India',
            'pl_PL' => 'Poland',
            'ps_AF' => 'Afghanistan',
            'pt_PT' => 'Portugal',
            'pt_BR' => 'Brazil',
            'qu_PE' => 'Peru',
            'ro_RO' => 'Romania',
            'ru_RU' => 'Russia',
            'si_LK' => 'Sri Lanka',
            'sk_SK' => 'Slovakia',
            'sl_SI' => 'Slovenia',
            'sq_AL' => 'Albania',
            'sr_RS' => 'Serbia',
            'sv_SE' => 'Sweden',
            'sw_TZ' => 'Tanzania',
            'ta_IN' => 'India',
            'te_IN' => 'India',
            'th_TH' => 'Thailand',
            'tl_PH' => 'Philippines',
            'tr_TR' => 'Turkey',
            'uk_UA' => 'Ukraine',
            'uz_UZ' => 'Uzbekistan',
            'vi_VN' => 'Vietnam',
            'xh_ZA' => 'South Africa',
            'zh_CN' => 'China',
            'zh_HK' => 'Hong Kong',
            'zh_TW' => 'Taiwan',
            'zu_ZA' => 'South Africa'
        ];
    }

}
