<?php

const ALGORITHM = 'RS256';

const PUBLICKEY = <<<EOD
    -----BEGIN PUBLIC KEY-----
    MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
    4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
    0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
    ehde/zUxo6UvS7UrBQIDAQAB
    -----END PUBLIC KEY-----
    EOD;

const PRIVATEKEY = <<<EOD
    -----BEGIN RSA PRIVATE KEY-----
    MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
    vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
    5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
    AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
    bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
    Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
    cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
    5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
    ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
    k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
    qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
    eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
    B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
    -----END RSA PRIVATE KEY-----
    EOD;

const ADMIN = 1;
const REDAKTOR = 2;
const UZYTKOWNIK = 3;

const PAGES = [
    "2" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Chrzanow',
    "3" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Krakow_I',
    "4" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Krakow_II',
    "5" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Krakow_III',
    "6" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Krakow_IV',
    "7" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Limanowa',
    "8" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Myslenice',
    "9" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Nowy_Sacz',
    "10" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Oswiecim',
    "11" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Podhale_I',
    "12" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Podhale_II',
    "13" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Tarnow_I__Bochnia_',
    "14" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Tarnow_II__Brzesko_',
    "15" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Tarnow_III__Zabno_',
    "16" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Tarnow_IV',
    "17" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Wadowice_I',
    "18" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Wadowice_II',
    "19" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_B/Wieliczka',

    "20" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Chrzanow',
    "21" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Krakow_I',
    "22" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Krakow_II',
    "23" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Krakow_III/',
    "25" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Myslenice',
    "26" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Nowy_Sacz',
    "27" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Nowy_Sacz-Gorlice',
    "28" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Olkusz',
    "29" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Oswiecim',
    "30" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Podhale',
    "31" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Tarnow_I__Bochnia_',
    "32" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Tarnow_II__Brzesko_',
    "33" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Tarnow_III__Zabno_',
    "34" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Tarnow_IV',
    "35" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Wadowice_I',
    "36" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Wadowice_II',
    "37" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Klasa_A/Wieliczka',

    "38" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Liga_okregowa/Krakow_I',
    "39" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Liga_okregowa/Krakow_II',
    "40" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Liga_okregowa/Krakow_III',
    "41" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Liga_okregowa/Nowy_Sacz_I__Nowy_Sacz-Gorlice_',
    "42" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Liga_okregowa/Nowy_Sacz_II__Limanowa-Podhale_',
    "43" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Liga_okregowa/Tarnow_I__Bochnia-Brzesko_',
    "44" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Liga_okregowa/Tarnow_II__Zabno-Tarnow_',
    "45" => 'https://regiowyniki.pl/wyniki/Pilka_Nozna/2022/2023/20221128/mecze/Malopolskie/Liga_okregowa/Wadowice',

];
