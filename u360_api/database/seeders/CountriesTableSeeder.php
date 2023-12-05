<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('countries')->delete();

        DB::table('countries')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Afghanistan',
                'country_code' => 'AFG',
                'code' => 'AF',
                'phone' => 93,
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Aland Islands',
                'country_code' => 'ALA',
                'code' => 'AX',
                'phone' => 358,
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Albania',
                'country_code' => 'ALB',
                'code' => 'AL',
                'phone' => 355,
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Algeria',
                'country_code' => 'DZA',
                'code' => 'DZ',
                'phone' => 213,
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'American Samoa',
                'country_code' => 'ASM',
                'code' => 'AS',
                'phone' => 1684,
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Andorra',
                'country_code' => 'AND',
                'code' => 'AD',
                'phone' => 376,
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Angola',
                'country_code' => 'AGO',
                'code' => 'AO',
                'phone' => 244,
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Anguilla',
                'country_code' => 'AIA',
                'code' => 'AI',
                'phone' => 1264,
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Antarctica',
                'country_code' => 'ATA',
                'code' => 'AQ',
                'phone' => 672,
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'Antigua and Barbuda',
                'country_code' => 'ATG',
                'code' => 'AG',
                'phone' => 1268,
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Argentina',
                'country_code' => 'ARG',
                'code' => 'AR',
                'phone' => 54,
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'Armenia',
                'country_code' => 'ARM',
                'code' => 'AM',
                'phone' => 374,
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'Aruba',
                'country_code' => 'ABW',
                'code' => 'AW',
                'phone' => 297,
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'Australia',
                'country_code' => 'AUS',
                'code' => 'AU',
                'phone' => 61,
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'Austria',
                'country_code' => 'AUT',
                'code' => 'AT',
                'phone' => 43,
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'Azerbaijan',
                'country_code' => 'AZE',
                'code' => 'AZ',
                'phone' => 994,
            ),
            16 =>
            array(
                'id' => 17,
                'name' => 'Bahamas',
                'country_code' => 'BHS',
                'code' => 'BS',
                'phone' => 1242,
            ),
            17 =>
            array(
                'id' => 18,
                'name' => 'Bahrain',
                'country_code' => 'BHR',
                'code' => 'BH',
                'phone' => 973,
            ),
            18 =>
            array(
                'id' => 19,
                'name' => 'Bangladesh',
                'country_code' => 'BGD',
                'code' => 'BD',
                'phone' => 880,
            ),
            19 =>
            array(
                'id' => 20,
                'name' => 'Barbados',
                'country_code' => 'BRB',
                'code' => 'BB',
                'phone' => 1246,
            ),
            20 =>
            array(
                'id' => 21,
                'name' => 'Belarus',
                'country_code' => 'BLR',
                'code' => 'BY',
                'phone' => 375,
            ),
            21 =>
            array(
                'id' => 22,
                'name' => 'Belgium',
                'country_code' => 'BEL',
                'code' => 'BE',
                'phone' => 32,
            ),
            22 =>
            array(
                'id' => 23,
                'name' => 'Belize',
                'country_code' => 'BLZ',
                'code' => 'BZ',
                'phone' => 501,
            ),
            23 =>
            array(
                'id' => 24,
                'name' => 'Benin',
                'country_code' => 'BEN',
                'code' => 'BJ',
                'phone' => 229,
            ),
            24 =>
            array(
                'id' => 25,
                'name' => 'Bermuda',
                'country_code' => 'BMU',
                'code' => 'BM',
                'phone' => 1441,
            ),
            25 =>
            array(
                'id' => 26,
                'name' => 'Bhutan',
                'country_code' => 'BTN',
                'code' => 'BT',
                'phone' => 975,
            ),
            26 =>
            array(
                'id' => 27,
                'name' => 'Bolivia',
                'country_code' => 'BOL',
                'code' => 'BO',
                'phone' => 591,
            ),
            27 =>
            array(
                'id' => 28,
                'name' => 'Bonaire, Sint Eustatius and Saba',
                'country_code' => 'BES',
                'code' => 'BQ',
                'phone' => 599,
            ),
            28 =>
            array(
                'id' => 29,
                'name' => 'Bosnia and Herzegovina',
                'country_code' => 'BIH',
                'code' => 'BA',
                'phone' => 387,
            ),
            29 =>
            array(
                'id' => 30,
                'name' => 'Botswana',
                'country_code' => 'BWA',
                'code' => 'BW',
                'phone' => 267,
            ),
            30 =>
            array(
                'id' => 31,
                'name' => 'Bouvet Island',
                'country_code' => 'BVT',
                'code' => 'BV',
                'phone' => 55,
            ),
            31 =>
            array(
                'id' => 32,
                'name' => 'Brazil',
                'country_code' => 'BRA',
                'code' => 'BR',
                'phone' => 55,
            ),
            32 =>
            array(
                'id' => 33,
                'name' => 'British Indian Ocean Territory',
                'country_code' => 'IOT',
                'code' => 'IO',
                'phone' => 246,
            ),
            33 =>
            array(
                'id' => 34,
                'name' => 'Brunei Darussalam',
                'country_code' => 'BRN',
                'code' => 'BN',
                'phone' => 673,
            ),
            34 =>
            array(
                'id' => 35,
                'name' => 'Bulgaria',
                'country_code' => 'BGR',
                'code' => 'BG',
                'phone' => 359,
            ),
            35 =>
            array(
                'id' => 36,
                'name' => 'Burkina Faso',
                'country_code' => 'BFA',
                'code' => 'BF',
                'phone' => 226,
            ),
            36 =>
            array(
                'id' => 37,
                'name' => 'Burundi',
                'country_code' => 'BDI',
                'code' => 'BI',
                'phone' => 257,
            ),
            37 =>
            array(
                'id' => 38,
                'name' => 'Cambodia',
                'country_code' => 'KHM',
                'code' => 'KH',
                'phone' => 855,
            ),
            38 =>
            array(
                'id' => 39,
                'name' => 'Cameroon',
                'country_code' => 'CMR',
                'code' => 'CM',
                'phone' => 237,
            ),
            39 =>
            array(
                'id' => 40,
                'name' => 'Canada',
                'country_code' => 'CAN',
                'code' => 'CA',
                'phone' => 1,
            ),
            40 =>
            array(
                'id' => 41,
                'name' => 'Cape Verde',
                'country_code' => 'CPV',
                'code' => 'CV',
                'phone' => 238,
            ),
            41 =>
            array(
                'id' => 42,
                'name' => 'Cayman Islands',
                'country_code' => 'CYM',
                'code' => 'KY',
                'phone' => 1345,
            ),
            42 =>
            array(
                'id' => 43,
                'name' => 'Central African Republic',
                'country_code' => 'CAF',
                'code' => 'CF',
                'phone' => 236,
            ),
            43 =>
            array(
                'id' => 44,
                'name' => 'Chad',
                'country_code' => 'TCD',
                'code' => 'TD',
                'phone' => 235,
            ),
            44 =>
            array(
                'id' => 45,
                'name' => 'Chile',
                'country_code' => 'CHL',
                'code' => 'CL',
                'phone' => 56,
            ),
            45 =>
            array(
                'id' => 46,
                'name' => 'China',
                'country_code' => 'CHN',
                'code' => 'CN',
                'phone' => 86,
            ),
            46 =>
            array(
                'id' => 47,
                'name' => 'Christmas Island',
                'country_code' => 'CXR',
                'code' => 'CX',
                'phone' => 61,
            ),
            47 =>
            array(
                'id' => 48,
                'name' => 'Cocos (Keeling) Islands',
                'country_code' => 'CCK',
                'code' => 'CC',
                'phone' => 672,
            ),
            48 =>
            array(
                'id' => 49,
                'name' => 'Colombia',
                'country_code' => 'COL',
                'code' => 'CO',
                'phone' => 57,
            ),
            49 =>
            array(
                'id' => 50,
                'name' => 'Comoros',
                'country_code' => 'COM',
                'code' => 'KM',
                'phone' => 269,
            ),
            50 =>
            array(
                'id' => 51,
                'name' => 'Congo',
                'country_code' => 'COG',
                'code' => 'CG',
                'phone' => 242,
            ),
            51 =>
            array(
                'id' => 52,
                'name' => 'Congo, Democratic Republic of the Congo',
                'country_code' => 'COD',
                'code' => 'CD',
                'phone' => 242,
            ),
            52 =>
            array(
                'id' => 53,
                'name' => 'Cook Islands',
                'country_code' => 'COK',
                'code' => 'CK',
                'phone' => 682,
            ),
            53 =>
            array(
                'id' => 54,
                'name' => 'Costa Rica',
                'country_code' => 'CRI',
                'code' => 'CR',
                'phone' => 506,
            ),
            54 =>
            array(
                'id' => 55,
                'name' => 'Cote D\'Ivoire',
                'country_code' => 'CIV',
                'code' => 'CI',
                'phone' => 225,
            ),
            55 =>
            array(
                'id' => 56,
                'name' => 'Croatia',
                'country_code' => 'HRV',
                'code' => 'HR',
                'phone' => 385,
            ),
            56 =>
            array(
                'id' => 57,
                'name' => 'Cuba',
                'country_code' => 'CUB',
                'code' => 'CU',
                'phone' => 53,
            ),
            57 =>
            array(
                'id' => 58,
                'name' => 'Curacao',
                'country_code' => 'CUW',
                'code' => 'CW',
                'phone' => 599,
            ),
            58 =>
            array(
                'id' => 59,
                'name' => 'Cyprus',
                'country_code' => 'CYP',
                'code' => 'CY',
                'phone' => 357,
            ),
            59 =>
            array(
                'id' => 60,
                'name' => 'Czech Republic',
                'country_code' => 'CZE',
                'code' => 'CZ',
                'phone' => 420,
            ),
            60 =>
            array(
                'id' => 61,
                'name' => 'Denmark',
                'country_code' => 'DNK',
                'code' => 'DK',
                'phone' => 45,
            ),
            61 =>
            array(
                'id' => 62,
                'name' => 'Djibouti',
                'country_code' => 'DJI',
                'code' => 'DJ',
                'phone' => 253,
            ),
            62 =>
            array(
                'id' => 63,
                'name' => 'Dominica',
                'country_code' => 'DMA',
                'code' => 'DM',
                'phone' => 1767,
            ),
            63 =>
            array(
                'id' => 64,
                'name' => 'Dominican Republic',
                'country_code' => 'DOM',
                'code' => 'DO',
                'phone' => 1809,
            ),
            64 =>
            array(
                'id' => 65,
                'name' => 'Ecuador',
                'country_code' => 'ECU',
                'code' => 'EC',
                'phone' => 593,
            ),
            65 =>
            array(
                'id' => 66,
                'name' => 'Egypt',
                'country_code' => 'EGY',
                'code' => 'EG',
                'phone' => 20,
            ),
            66 =>
            array(
                'id' => 67,
                'name' => 'El Salvador',
                'country_code' => 'SLV',
                'code' => 'SV',
                'phone' => 503,
            ),
            67 =>
            array(
                'id' => 68,
                'name' => 'Equatorial Guinea',
                'country_code' => 'GNQ',
                'code' => 'GQ',
                'phone' => 240,
            ),
            68 =>
            array(
                'id' => 69,
                'name' => 'Eritrea',
                'country_code' => 'ERI',
                'code' => 'ER',
                'phone' => 291,
            ),
            69 =>
            array(
                'id' => 70,
                'name' => 'Estonia',
                'country_code' => 'EST',
                'code' => 'EE',
                'phone' => 372,
            ),
            70 =>
            array(
                'id' => 71,
                'name' => 'Ethiopia',
                'country_code' => 'ETH',
                'code' => 'ET',
                'phone' => 251,
            ),
            71 =>
            array(
                'id' => 72,
                'name' => 'Falkland Islands (Malvinas)',
                'country_code' => 'FLK',
                'code' => 'FK',
                'phone' => 500,
            ),
            72 =>
            array(
                'id' => 73,
                'name' => 'Faroe Islands',
                'country_code' => 'FRO',
                'code' => 'FO',
                'phone' => 298,
            ),
            73 =>
            array(
                'id' => 74,
                'name' => 'Fiji',
                'country_code' => 'FJI',
                'code' => 'FJ',
                'phone' => 679,
            ),
            74 =>
            array(
                'id' => 75,
                'name' => 'Finland',
                'country_code' => 'FIN',
                'code' => 'FI',
                'phone' => 358,
            ),
            75 =>
            array(
                'id' => 76,
                'name' => 'France',
                'country_code' => 'FRA',
                'code' => 'FR',
                'phone' => 33,
            ),
            76 =>
            array(
                'id' => 77,
                'name' => 'French Guiana',
                'country_code' => 'GUF',
                'code' => 'GF',
                'phone' => 594,
            ),
            77 =>
            array(
                'id' => 78,
                'name' => 'French Polynesia',
                'country_code' => 'PYF',
                'code' => 'PF',
                'phone' => 689,
            ),
            78 =>
            array(
                'id' => 79,
                'name' => 'French Southern Territories',
                'country_code' => 'ATF',
                'code' => 'TF',
                'phone' => 262,
            ),
            79 =>
            array(
                'id' => 80,
                'name' => 'Gabon',
                'country_code' => 'GAB',
                'code' => 'GA',
                'phone' => 241,
            ),
            80 =>
            array(
                'id' => 81,
                'name' => 'Gambia',
                'country_code' => 'GMB',
                'code' => 'GM',
                'phone' => 220,
            ),
            81 =>
            array(
                'id' => 82,
                'name' => 'Georgia',
                'country_code' => 'GEO',
                'code' => 'GE',
                'phone' => 995,
            ),
            82 =>
            array(
                'id' => 83,
                'name' => 'Germany',
                'country_code' => 'DEU',
                'code' => 'DE',
                'phone' => 49,
            ),
            83 =>
            array(
                'id' => 84,
                'name' => 'Ghana',
                'country_code' => 'GHA',
                'code' => 'GH',
                'phone' => 233,
            ),
            84 =>
            array(
                'id' => 85,
                'name' => 'Gibraltar',
                'country_code' => 'GIB',
                'code' => 'GI',
                'phone' => 350,
            ),
            85 =>
            array(
                'id' => 86,
                'name' => 'Greece',
                'country_code' => 'GRC',
                'code' => 'GR',
                'phone' => 30,
            ),
            86 =>
            array(
                'id' => 87,
                'name' => 'Greenland',
                'country_code' => 'GRL',
                'code' => 'GL',
                'phone' => 299,
            ),
            87 =>
            array(
                'id' => 88,
                'name' => 'Grenada',
                'country_code' => 'GRD',
                'code' => 'GD',
                'phone' => 1473,
            ),
            88 =>
            array(
                'id' => 89,
                'name' => 'Guadeloupe',
                'country_code' => 'GLP',
                'code' => 'GP',
                'phone' => 590,
            ),
            89 =>
            array(
                'id' => 90,
                'name' => 'Guam',
                'country_code' => 'GUM',
                'code' => 'GU',
                'phone' => 1671,
            ),
            90 =>
            array(
                'id' => 91,
                'name' => 'Guatemala',
                'country_code' => 'GTM',
                'code' => 'GT',
                'phone' => 502,
            ),
            91 =>
            array(
                'id' => 92,
                'name' => 'Guernsey',
                'country_code' => 'GGY',
                'code' => 'GG',
                'phone' => 44,
            ),
            92 =>
            array(
                'id' => 93,
                'name' => 'Guinea',
                'country_code' => 'GIN',
                'code' => 'GN',
                'phone' => 224,
            ),
            93 =>
            array(
                'id' => 94,
                'name' => 'Guinea-Bissau',
                'country_code' => 'GNB',
                'code' => 'GW',
                'phone' => 245,
            ),
            94 =>
            array(
                'id' => 95,
                'name' => 'Guyana',
                'country_code' => 'GUY',
                'code' => 'GY',
                'phone' => 592,
            ),
            95 =>
            array(
                'id' => 96,
                'name' => 'Haiti',
                'country_code' => 'HTI',
                'code' => 'HT',
                'phone' => 509,
            ),
            96 =>
            array(
                'id' => 97,
                'name' => 'Heard Island and Mcdonald Islands',
                'country_code' => 'HMD',
                'code' => 'HM',
                'phone' => 0,
            ),
            97 =>
            array(
                'id' => 98,
                'name' => 'Holy See (Vatican City State)',
                'country_code' => 'VAT',
                'code' => 'VA',
                'phone' => 39,
            ),
            98 =>
            array(
                'id' => 99,
                'name' => 'Honduras',
                'country_code' => 'HND',
                'code' => 'HN',
                'phone' => 504,
            ),
            99 =>
            array(
                'id' => 100,
                'name' => 'Hong Kong',
                'country_code' => 'HKG',
                'code' => 'HK',
                'phone' => 852,
            ),
            100 =>
            array(
                'id' => 101,
                'name' => 'Hungary',
                'country_code' => 'HUN',
                'code' => 'HU',
                'phone' => 36,
            ),
            101 =>
            array(
                'id' => 102,
                'name' => 'Iceland',
                'country_code' => 'ISL',
                'code' => 'IS',
                'phone' => 354,
            ),
            102 =>
            array(
                'id' => 103,
                'name' => 'India',
                'country_code' => 'IND',
                'code' => 'IN',
                'phone' => 91,
            ),
            103 =>
            array(
                'id' => 104,
                'name' => 'Indonesia',
                'country_code' => 'IDN',
                'code' => 'ID',
                'phone' => 62,
            ),
            104 =>
            array(
                'id' => 105,
                'name' => 'Iran, Islamic Republic of',
                'country_code' => 'IRN',
                'code' => 'IR',
                'phone' => 98,
            ),
            105 =>
            array(
                'id' => 106,
                'name' => 'Iraq',
                'country_code' => 'IRQ',
                'code' => 'IQ',
                'phone' => 964,
            ),
            106 =>
            array(
                'id' => 107,
                'name' => 'Ireland',
                'country_code' => 'IRL',
                'code' => 'IE',
                'phone' => 353,
            ),
            107 =>
            array(
                'id' => 108,
                'name' => 'Isle of Man',
                'country_code' => 'IMN',
                'code' => 'IM',
                'phone' => 44,
            ),
            108 =>
            array(
                'id' => 109,
                'name' => 'Israel',
                'country_code' => 'ISR',
                'code' => 'IL',
                'phone' => 972,
            ),
            109 =>
            array(
                'id' => 110,
                'name' => 'Italy',
                'country_code' => 'ITA',
                'code' => 'IT',
                'phone' => 39,
            ),
            110 =>
            array(
                'id' => 111,
                'name' => 'Jamaica',
                'country_code' => 'JAM',
                'code' => 'JM',
                'phone' => 1876,
            ),
            111 =>
            array(
                'id' => 112,
                'name' => 'Japan',
                'country_code' => 'JPN',
                'code' => 'JP',
                'phone' => 81,
            ),
            112 =>
            array(
                'id' => 113,
                'name' => 'Jersey',
                'country_code' => 'JEY',
                'code' => 'JE',
                'phone' => 44,
            ),
            113 =>
            array(
                'id' => 114,
                'name' => 'Jordan',
                'country_code' => 'JOR',
                'code' => 'JO',
                'phone' => 962,
            ),
            114 =>
            array(
                'id' => 115,
                'name' => 'Kazakhstan',
                'country_code' => 'KAZ',
                'code' => 'KZ',
                'phone' => 7,
            ),
            115 =>
            array(
                'id' => 116,
                'name' => 'Kenya',
                'country_code' => 'KEN',
                'code' => 'KE',
                'phone' => 254,
            ),
            116 =>
            array(
                'id' => 117,
                'name' => 'Kiribati',
                'country_code' => 'KIR',
                'code' => 'KI',
                'phone' => 686,
            ),
            117 =>
            array(
                'id' => 118,
                'name' => 'Korea, Democratic People\'s Republic of',
                'country_code' => 'PRK',
                'code' => 'KP',
                'phone' => 850,
            ),
            118 =>
            array(
                'id' => 119,
                'name' => 'Korea, Republic of',
                'country_code' => 'KOR',
                'code' => 'KR',
                'phone' => 82,
            ),
            119 =>
            array(
                'id' => 120,
                'name' => 'Kosovo',
                'country_code' => 'XKX',
                'code' => 'XK',
                'phone' => 383,
            ),
            120 =>
            array(
                'id' => 121,
                'name' => 'Kuwait',
                'country_code' => 'KWT',
                'code' => 'KW',
                'phone' => 965,
            ),
            121 =>
            array(
                'id' => 122,
                'name' => 'Kyrgyzstan',
                'country_code' => 'KGZ',
                'code' => 'KG',
                'phone' => 996,
            ),
            122 =>
            array(
                'id' => 123,
                'name' => 'Lao People\'s Democratic Republic',
                'country_code' => 'LAO',
                'code' => 'LA',
                'phone' => 856,
            ),
            123 =>
            array(
                'id' => 124,
                'name' => 'Latvia',
                'country_code' => 'LVA',
                'code' => 'LV',
                'phone' => 371,
            ),
            124 =>
            array(
                'id' => 125,
                'name' => 'Lebanon',
                'country_code' => 'LBN',
                'code' => 'LB',
                'phone' => 961,
            ),
            125 =>
            array(
                'id' => 126,
                'name' => 'Lesotho',
                'country_code' => 'LSO',
                'code' => 'LS',
                'phone' => 266,
            ),
            126 =>
            array(
                'id' => 127,
                'name' => 'Liberia',
                'country_code' => 'LBR',
                'code' => 'LR',
                'phone' => 231,
            ),
            127 =>
            array(
                'id' => 128,
                'name' => 'Libyan Arab Jamahiriya',
                'country_code' => 'LBY',
                'code' => 'LY',
                'phone' => 218,
            ),
            128 =>
            array(
                'id' => 129,
                'name' => 'Liechtenstein',
                'country_code' => 'LIE',
                'code' => 'LI',
                'phone' => 423,
            ),
            129 =>
            array(
                'id' => 130,
                'name' => 'Lithuania',
                'country_code' => 'LTU',
                'code' => 'LT',
                'phone' => 370,
            ),
            130 =>
            array(
                'id' => 131,
                'name' => 'Luxembourg',
                'country_code' => 'LUX',
                'code' => 'LU',
                'phone' => 352,
            ),
            131 =>
            array(
                'id' => 132,
                'name' => 'Macao',
                'country_code' => 'MAC',
                'code' => 'MO',
                'phone' => 853,
            ),
            132 =>
            array(
                'id' => 133,
                'name' => 'Macedonia, the Former Yugoslav Republic of',
                'country_code' => 'MKD',
                'code' => 'MK',
                'phone' => 389,
            ),
            133 =>
            array(
                'id' => 134,
                'name' => 'Madagascar',
                'country_code' => 'MDG',
                'code' => 'MG',
                'phone' => 261,
            ),
            134 =>
            array(
                'id' => 135,
                'name' => 'Malawi',
                'country_code' => 'MWI',
                'code' => 'MW',
                'phone' => 265,
            ),
            135 =>
            array(
                'id' => 136,
                'name' => 'Malaysia',
                'country_code' => 'MYS',
                'code' => 'MY',
                'phone' => 60,
            ),
            136 =>
            array(
                'id' => 137,
                'name' => 'Maldives',
                'country_code' => 'MDV',
                'code' => 'MV',
                'phone' => 960,
            ),
            137 =>
            array(
                'id' => 138,
                'name' => 'Mali',
                'country_code' => 'MLI',
                'code' => 'ML',
                'phone' => 223,
            ),
            138 =>
            array(
                'id' => 139,
                'name' => 'Malta',
                'country_code' => 'MLT',
                'code' => 'MT',
                'phone' => 356,
            ),
            139 =>
            array(
                'id' => 140,
                'name' => 'Marshall Islands',
                'country_code' => 'MHL',
                'code' => 'MH',
                'phone' => 692,
            ),
            140 =>
            array(
                'id' => 141,
                'name' => 'Martinique',
                'country_code' => 'MTQ',
                'code' => 'MQ',
                'phone' => 596,
            ),
            141 =>
            array(
                'id' => 142,
                'name' => 'Mauritania',
                'country_code' => 'MRT',
                'code' => 'MR',
                'phone' => 222,
            ),
            142 =>
            array(
                'id' => 143,
                'name' => 'Mauritius',
                'country_code' => 'MUS',
                'code' => 'MU',
                'phone' => 230,
            ),
            143 =>
            array(
                'id' => 144,
                'name' => 'Mayotte',
                'country_code' => 'MYT',
                'code' => 'YT',
                'phone' => 262,
            ),
            144 =>
            array(
                'id' => 145,
                'name' => 'Mexico',
                'country_code' => 'MEX',
                'code' => 'MX',
                'phone' => 52,
            ),
            145 =>
            array(
                'id' => 146,
                'name' => 'Micronesia, Federated States of',
                'country_code' => 'FSM',
                'code' => 'FM',
                'phone' => 691,
            ),
            146 =>
            array(
                'id' => 147,
                'name' => 'Moldova, Republic of',
                'country_code' => 'MDA',
                'code' => 'MD',
                'phone' => 373,
            ),
            147 =>
            array(
                'id' => 148,
                'name' => 'Monaco',
                'country_code' => 'MCO',
                'code' => 'MC',
                'phone' => 377,
            ),
            148 =>
            array(
                'id' => 149,
                'name' => 'Mongolia',
                'country_code' => 'MNG',
                'code' => 'MN',
                'phone' => 976,
            ),
            149 =>
            array(
                'id' => 150,
                'name' => 'Montenegro',
                'country_code' => 'MNE',
                'code' => 'ME',
                'phone' => 382,
            ),
            150 =>
            array(
                'id' => 151,
                'name' => 'Montserrat',
                'country_code' => 'MSR',
                'code' => 'MS',
                'phone' => 1664,
            ),
            151 =>
            array(
                'id' => 152,
                'name' => 'Morocco',
                'country_code' => 'MAR',
                'code' => 'MA',
                'phone' => 212,
            ),
            152 =>
            array(
                'id' => 153,
                'name' => 'Mozambique',
                'country_code' => 'MOZ',
                'code' => 'MZ',
                'phone' => 258,
            ),
            153 =>
            array(
                'id' => 154,
                'name' => 'Myanmar',
                'country_code' => 'MMR',
                'code' => 'MM',
                'phone' => 95,
            ),
            154 =>
            array(
                'id' => 155,
                'name' => 'Namibia',
                'country_code' => 'NAM',
                'code' => 'NA',
                'phone' => 264,
            ),
            155 =>
            array(
                'id' => 156,
                'name' => 'Nauru',
                'country_code' => 'NRU',
                'code' => 'NR',
                'phone' => 674,
            ),
            156 =>
            array(
                'id' => 157,
                'name' => 'Nepal',
                'country_code' => 'NPL',
                'code' => 'NP',
                'phone' => 977,
            ),
            157 =>
            array(
                'id' => 158,
                'name' => 'Netherlands',
                'country_code' => 'NLD',
                'code' => 'NL',
                'phone' => 31,
            ),
            158 =>
            array(
                'id' => 159,
                'name' => 'Netherlands Antilles',
                'country_code' => 'ANT',
                'code' => 'AN',
                'phone' => 599,
            ),
            159 =>
            array(
                'id' => 160,
                'name' => 'New Caledonia',
                'country_code' => 'NCL',
                'code' => 'NC',
                'phone' => 687,
            ),
            160 =>
            array(
                'id' => 161,
                'name' => 'New Zealand',
                'country_code' => 'NZL',
                'code' => 'NZ',
                'phone' => 64,
            ),
            161 =>
            array(
                'id' => 162,
                'name' => 'Nicaragua',
                'country_code' => 'NIC',
                'code' => 'NI',
                'phone' => 505,
            ),
            162 =>
            array(
                'id' => 163,
                'name' => 'Niger',
                'country_code' => 'NER',
                'code' => 'NE',
                'phone' => 227,
            ),
            163 =>
            array(
                'id' => 164,
                'name' => 'Nigeria',
                'country_code' => 'NGA',
                'code' => 'NG',
                'phone' => 234,
            ),
            164 =>
            array(
                'id' => 165,
                'name' => 'Niue',
                'country_code' => 'NIU',
                'code' => 'NU',
                'phone' => 683,
            ),
            165 =>
            array(
                'id' => 166,
                'name' => 'Norfolk Island',
                'country_code' => 'NFK',
                'code' => 'NF',
                'phone' => 672,
            ),
            166 =>
            array(
                'id' => 167,
                'name' => 'Northern Mariana Islands',
                'country_code' => 'MNP',
                'code' => 'MP',
                'phone' => 1670,
            ),
            167 =>
            array(
                'id' => 168,
                'name' => 'Norway',
                'country_code' => 'NOR',
                'code' => 'NO',
                'phone' => 47,
            ),
            168 =>
            array(
                'id' => 169,
                'name' => 'Oman',
                'country_code' => 'OMN',
                'code' => 'OM',
                'phone' => 968,
            ),
            169 =>
            array(
                'id' => 170,
                'name' => 'Pakistan',
                'country_code' => 'PAK',
                'code' => 'PK',
                'phone' => 92,
            ),
            170 =>
            array(
                'id' => 171,
                'name' => 'Palau',
                'country_code' => 'PLW',
                'code' => 'PW',
                'phone' => 680,
            ),
            171 =>
            array(
                'id' => 172,
                'name' => 'Palestinian Territory, Occupied',
                'country_code' => 'PSE',
                'code' => 'PS',
                'phone' => 970,
            ),
            172 =>
            array(
                'id' => 173,
                'name' => 'Panama',
                'country_code' => 'PAN',
                'code' => 'PA',
                'phone' => 507,
            ),
            173 =>
            array(
                'id' => 174,
                'name' => 'Papua New Guinea',
                'country_code' => 'PNG',
                'code' => 'PG',
                'phone' => 675,
            ),
            174 =>
            array(
                'id' => 175,
                'name' => 'Paraguay',
                'country_code' => 'PRY',
                'code' => 'PY',
                'phone' => 595,
            ),
            175 =>
            array(
                'id' => 176,
                'name' => 'Peru',
                'country_code' => 'PER',
                'code' => 'PE',
                'phone' => 51,
            ),
            176 =>
            array(
                'id' => 177,
                'name' => 'Philippines',
                'country_code' => 'PHL',
                'code' => 'PH',
                'phone' => 63,
            ),
            177 =>
            array(
                'id' => 178,
                'name' => 'Pitcairn',
                'country_code' => 'PCN',
                'code' => 'PN',
                'phone' => 64,
            ),
            178 =>
            array(
                'id' => 179,
                'name' => 'Poland',
                'country_code' => 'POL',
                'code' => 'PL',
                'phone' => 48,
            ),
            179 =>
            array(
                'id' => 180,
                'name' => 'Portugal',
                'country_code' => 'PRT',
                'code' => 'PT',
                'phone' => 351,
            ),
            180 =>
            array(
                'id' => 181,
                'name' => 'Puerto Rico',
                'country_code' => 'PRI',
                'code' => 'PR',
                'phone' => 1787,
            ),
            181 =>
            array(
                'id' => 182,
                'name' => 'Qatar',
                'country_code' => 'QAT',
                'code' => 'QA',
                'phone' => 974,
            ),
            182 =>
            array(
                'id' => 183,
                'name' => 'Reunion',
                'country_code' => 'REU',
                'code' => 'RE',
                'phone' => 262,
            ),
            183 =>
            array(
                'id' => 184,
                'name' => 'Romania',
                'country_code' => 'ROM',
                'code' => 'RO',
                'phone' => 40,
            ),
            184 =>
            array(
                'id' => 185,
                'name' => 'Russian Federation',
                'country_code' => 'RUS',
                'code' => 'RU',
                'phone' => 7,
            ),
            185 =>
            array(
                'id' => 186,
                'name' => 'Rwanda',
                'country_code' => 'RWA',
                'code' => 'RW',
                'phone' => 250,
            ),
            186 =>
            array(
                'id' => 187,
                'name' => 'Saint Barthelemy',
                'country_code' => 'BLM',
                'code' => 'BL',
                'phone' => 590,
            ),
            187 =>
            array(
                'id' => 188,
                'name' => 'Saint Helena',
                'country_code' => 'SHN',
                'code' => 'SH',
                'phone' => 290,
            ),
            188 =>
            array(
                'id' => 189,
                'name' => 'Saint Kitts and Nevis',
                'country_code' => 'KNA',
                'code' => 'KN',
                'phone' => 1869,
            ),
            189 =>
            array(
                'id' => 190,
                'name' => 'Saint Lucia',
                'country_code' => 'LCA',
                'code' => 'LC',
                'phone' => 1758,
            ),
            190 =>
            array(
                'id' => 191,
                'name' => 'Saint Martin',
                'country_code' => 'MAF',
                'code' => 'MF',
                'phone' => 590,
            ),
            191 =>
            array(
                'id' => 192,
                'name' => 'Saint Pierre and Miquelon',
                'country_code' => 'SPM',
                'code' => 'PM',
                'phone' => 508,
            ),
            192 =>
            array(
                'id' => 193,
                'name' => 'Saint Vincent and the Grenadines',
                'country_code' => 'VCT',
                'code' => 'VC',
                'phone' => 1784,
            ),
            193 =>
            array(
                'id' => 194,
                'name' => 'Samoa',
                'country_code' => 'WSM',
                'code' => 'WS',
                'phone' => 684,
            ),
            194 =>
            array(
                'id' => 195,
                'name' => 'San Marino',
                'country_code' => 'SMR',
                'code' => 'SM',
                'phone' => 378,
            ),
            195 =>
            array(
                'id' => 196,
                'name' => 'Sao Tome and Principe',
                'country_code' => 'STP',
                'code' => 'ST',
                'phone' => 239,
            ),
            196 =>
            array(
                'id' => 197,
                'name' => 'Saudi Arabia',
                'country_code' => 'SAU',
                'code' => 'SA',
                'phone' => 966,
            ),
            197 =>
            array(
                'id' => 198,
                'name' => 'Senegal',
                'country_code' => 'SEN',
                'code' => 'SN',
                'phone' => 221,
            ),
            198 =>
            array(
                'id' => 199,
                'name' => 'Serbia',
                'country_code' => 'SRB',
                'code' => 'RS',
                'phone' => 381,
            ),
            199 =>
            array(
                'id' => 200,
                'name' => 'Serbia and Montenegro',
                'country_code' => 'SCG',
                'code' => 'CS',
                'phone' => 381,
            ),
            200 =>
            array(
                'id' => 201,
                'name' => 'Seychelles',
                'country_code' => 'SYC',
                'code' => 'SC',
                'phone' => 248,
            ),
            201 =>
            array(
                'id' => 202,
                'name' => 'Sierra Leone',
                'country_code' => 'SLE',
                'code' => 'SL',
                'phone' => 232,
            ),
            202 =>
            array(
                'id' => 203,
                'name' => 'Singapore',
                'country_code' => 'SGP',
                'code' => 'SG',
                'phone' => 65,
            ),
            203 =>
            array(
                'id' => 204,
                'name' => 'Sint Maarten',
                'country_code' => 'SXM',
                'code' => 'SX',
                'phone' => 721,
            ),
            204 =>
            array(
                'id' => 205,
                'name' => 'Slovakia',
                'country_code' => 'SVK',
                'code' => 'SK',
                'phone' => 421,
            ),
            205 =>
            array(
                'id' => 206,
                'name' => 'Slovenia',
                'country_code' => 'SVN',
                'code' => 'SI',
                'phone' => 386,
            ),
            206 =>
            array(
                'id' => 207,
                'name' => 'Solomon Islands',
                'country_code' => 'SLB',
                'code' => 'SB',
                'phone' => 677,
            ),
            207 =>
            array(
                'id' => 208,
                'name' => 'Somalia',
                'country_code' => 'SOM',
                'code' => 'SO',
                'phone' => 252,
            ),
            208 =>
            array(
                'id' => 209,
                'name' => 'South Africa',
                'country_code' => 'ZAF',
                'code' => 'ZA',
                'phone' => 27,
            ),
            209 =>
            array(
                'id' => 210,
                'name' => 'South Georgia and the South Sandwich Islands',
                'country_code' => 'SGS',
                'code' => 'GS',
                'phone' => 500,
            ),
            210 =>
            array(
                'id' => 211,
                'name' => 'South Sudan',
                'country_code' => 'SSD',
                'code' => 'SS',
                'phone' => 211,
            ),
            211 =>
            array(
                'id' => 212,
                'name' => 'Spain',
                'country_code' => 'ESP',
                'code' => 'ES',
                'phone' => 34,
            ),
            212 =>
            array(
                'id' => 213,
                'name' => 'Sri Lanka',
                'country_code' => 'LKA',
                'code' => 'LK',
                'phone' => 94,
            ),
            213 =>
            array(
                'id' => 214,
                'name' => 'Sudan',
                'country_code' => 'SDN',
                'code' => 'SD',
                'phone' => 249,
            ),
            214 =>
            array(
                'id' => 215,
                'name' => 'Suriname',
                'country_code' => 'SUR',
                'code' => 'SR',
                'phone' => 597,
            ),
            215 =>
            array(
                'id' => 216,
                'name' => 'Svalbard and Jan Mayen',
                'country_code' => 'SJM',
                'code' => 'SJ',
                'phone' => 47,
            ),
            216 =>
            array(
                'id' => 217,
                'name' => 'Swaziland',
                'country_code' => 'SWZ',
                'code' => 'SZ',
                'phone' => 268,
            ),
            217 =>
            array(
                'id' => 218,
                'name' => 'Sweden',
                'country_code' => 'SWE',
                'code' => 'SE',
                'phone' => 46,
            ),
            218 =>
            array(
                'id' => 219,
                'name' => 'Switzerland',
                'country_code' => 'CHE',
                'code' => 'CH',
                'phone' => 41,
            ),
            219 =>
            array(
                'id' => 220,
                'name' => 'Syrian Arab Republic',
                'country_code' => 'SYR',
                'code' => 'SY',
                'phone' => 963,
            ),
            220 =>
            array(
                'id' => 221,
                'name' => 'Taiwan, Province of China',
                'country_code' => 'TWN',
                'code' => 'TW',
                'phone' => 886,
            ),
            221 =>
            array(
                'id' => 222,
                'name' => 'Tajikistan',
                'country_code' => 'TJK',
                'code' => 'TJ',
                'phone' => 992,
            ),
            222 =>
            array(
                'id' => 223,
                'name' => 'Tanzania, United Republic of',
                'country_code' => 'TZA',
                'code' => 'TZ',
                'phone' => 255,
            ),
            223 =>
            array(
                'id' => 224,
                'name' => 'Thailand',
                'country_code' => 'THA',
                'code' => 'TH',
                'phone' => 66,
            ),
            224 =>
            array(
                'id' => 225,
                'name' => 'Timor-Leste',
                'country_code' => 'TLS',
                'code' => 'TL',
                'phone' => 670,
            ),
            225 =>
            array(
                'id' => 226,
                'name' => 'Togo',
                'country_code' => 'TGO',
                'code' => 'TG',
                'phone' => 228,
            ),
            226 =>
            array(
                'id' => 227,
                'name' => 'Tokelau',
                'country_code' => 'TKL',
                'code' => 'TK',
                'phone' => 690,
            ),
            227 =>
            array(
                'id' => 228,
                'name' => 'Tonga',
                'country_code' => 'TON',
                'code' => 'TO',
                'phone' => 676,
            ),
            228 =>
            array(
                'id' => 229,
                'name' => 'Trinidad and Tobago',
                'country_code' => 'TTO',
                'code' => 'TT',
                'phone' => 1868,
            ),
            229 =>
            array(
                'id' => 230,
                'name' => 'Tunisia',
                'country_code' => 'TUN',
                'code' => 'TN',
                'phone' => 216,
            ),
            230 =>
            array(
                'id' => 231,
                'name' => 'Turkey',
                'country_code' => 'TUR',
                'code' => 'TR',
                'phone' => 90,
            ),
            231 =>
            array(
                'id' => 232,
                'name' => 'Turkmenistan',
                'country_code' => 'TKM',
                'code' => 'TM',
                'phone' => 7370,
            ),
            232 =>
            array(
                'id' => 233,
                'name' => 'Turks and Caicos Islands',
                'country_code' => 'TCA',
                'code' => 'TC',
                'phone' => 1649,
            ),
            233 =>
            array(
                'id' => 234,
                'name' => 'Tuvalu',
                'country_code' => 'TUV',
                'code' => 'TV',
                'phone' => 688,
            ),
            234 =>
            array(
                'id' => 235,
                'name' => 'Uganda',
                'country_code' => 'UGA',
                'code' => 'UG',
                'phone' => 256,
            ),
            235 =>
            array(
                'id' => 236,
                'name' => 'Ukraine',
                'country_code' => 'UKR',
                'code' => 'UA',
                'phone' => 380,
            ),
            236 =>
            array(
                'id' => 237,
                'name' => 'United Arab Emirates',
                'country_code' => 'ARE',
                'code' => 'AE',
                'phone' => 971,
            ),
            237 =>
            array(
                'id' => 238,
                'name' => 'United Kingdom',
                'country_code' => 'GBR',
                'code' => 'GB',
                'phone' => 44,
            ),
            238 =>
            array(
                'id' => 239,
                'name' => 'United States',
                'country_code' => 'USA',
                'code' => 'US',
                'phone' => 1,
            ),
            239 =>
            array(
                'id' => 240,
                'name' => 'United States Minor Outlying Islands',
                'country_code' => 'UMI',
                'code' => 'UM',
                'phone' => 1,
            ),
            240 =>
            array(
                'id' => 241,
                'name' => 'Uruguay',
                'country_code' => 'URY',
                'code' => 'UY',
                'phone' => 598,
            ),
            241 =>
            array(
                'id' => 242,
                'name' => 'Uzbekistan',
                'country_code' => 'UZB',
                'code' => 'UZ',
                'phone' => 998,
            ),
            242 =>
            array(
                'id' => 243,
                'name' => 'Vanuatu',
                'country_code' => 'VUT',
                'code' => 'VU',
                'phone' => 678,
            ),
            243 =>
            array(
                'id' => 244,
                'name' => 'Venezuela',
                'country_code' => 'VEN',
                'code' => 'VE',
                'phone' => 58,
            ),
            244 =>
            array(
                'id' => 245,
                'name' => 'Viet Nam',
                'country_code' => 'VNM',
                'code' => 'VN',
                'phone' => 84,
            ),
            245 =>
            array(
                'id' => 246,
                'name' => 'Virgin Islands, British',
                'country_code' => 'VGB',
                'code' => 'VG',
                'phone' => 1284,
            ),
            246 =>
            array(
                'id' => 247,
                'name' => 'Virgin Islands, U.s.',
                'country_code' => 'VIR',
                'code' => 'VI',
                'phone' => 1340,
            ),
            247 =>
            array(
                'id' => 248,
                'name' => 'Wallis and Futuna',
                'country_code' => 'WLF',
                'code' => 'WF',
                'phone' => 681,
            ),
            248 =>
            array(
                'id' => 249,
                'name' => 'Western Sahara',
                'country_code' => 'ESH',
                'code' => 'EH',
                'phone' => 212,
            ),
            249 =>
            array(
                'id' => 250,
                'name' => 'Yemen',
                'country_code' => 'YEM',
                'code' => 'YE',
                'phone' => 967,
            ),
            250 =>
            array(
                'id' => 251,
                'name' => 'Zambia',
                'country_code' => 'ZMB',
                'code' => 'ZM',
                'phone' => 260,
            ),
            251 =>
            array(
                'id' => 252,
                'name' => 'Zimbabwe',
                'country_code' => 'ZWE',
                'code' => 'ZW',
                'phone' => 263,
            ),
        ));
    }
}
