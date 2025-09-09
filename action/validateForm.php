<?php

require_once '../vendor/autoload.php';

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

/** Функция для получения названия страны по ISO коду */
function getCountryName($isoCode) {

    /** Попробуем использовать PHP Intl extension если доступно */
    if (extension_loaded('intl') && !empty($isoCode)) {
        $countryName = Locale::getDisplayRegion('-' . $isoCode, 'ru_RU');
        if (!empty($countryName) && $countryName !== $isoCode) {
            return $countryName;
        }
    }

    /** Fallback массив для основных стран */
    $countries = [
        'RU' => 'Россия', 'US' => 'США', 'GB' => 'Великобритания', 'DE' => 'Германия',
        'FR' => 'Франция', 'IT' => 'Италия', 'ES' => 'Испания', 'CN' => 'Китай',
        'JP' => 'Япония', 'KR' => 'Южная Корея', 'IN' => 'Индия', 'BR' => 'Бразилия',
        'CA' => 'Канада', 'AU' => 'Австралия', 'MX' => 'Мексика', 'AR' => 'Аргентина',
        'UA' => 'Украина', 'KZ' => 'Казахстан', 'BY' => 'Беларусь', 'PL' => 'Польша',
        'TR' => 'Турция', 'EG' => 'Египет', 'SA' => 'Саудовская Аравия', 'AE' => 'ОАЭ',
        'IL' => 'Израиль', 'TH' => 'Таиланд', 'VN' => 'Вьетнам', 'SG' => 'Сингапур',
        'MY' => 'Малайзия', 'ID' => 'Индонезия', 'PH' => 'Филиппины', 'NL' => 'Нидерланды',
        'BE' => 'Бельгия', 'CH' => 'Швейцария', 'AT' => 'Австрия', 'SE' => 'Швеция',
        'NO' => 'Норвегия', 'DK' => 'Дания', 'FI' => 'Финляндия', 'PT' => 'Португалия',
        'GR' => 'Греция', 'CZ' => 'Чехия', 'HU' => 'Венгрия', 'RO' => 'Румыния',
        'BG' => 'Болгария', 'HR' => 'Хорватия', 'RS' => 'Сербия', 'SK' => 'Словакия',
        'SI' => 'Словения', 'LT' => 'Литва', 'LV' => 'Латвия', 'EE' => 'Эстония',
        'IE' => 'Ирландия', 'IS' => 'Исландия', 'LU' => 'Люксембург', 'MT' => 'Мальта',
        'CY' => 'Кипр', 'ZA' => 'ЮАР', 'NG' => 'Нигерия', 'KE' => 'Кения', 'MA' => 'Марокко',
        'TN' => 'Тунис', 'DZ' => 'Алжир', 'LY' => 'Ливия', 'ET' => 'Эфиопия',
        'GH' => 'Гана', 'CI' => 'Кот-д\'Ивуар', 'SN' => 'Сенегал', 'UG' => 'Уганда',
        'TZ' => 'Танзания', 'MZ' => 'Мозамбик', 'MG' => 'Мадагаскар', 'ZW' => 'Зимбабве',
        'BW' => 'Ботсвана', 'NA' => 'Намибия', 'ZM' => 'Замбия', 'MW' => 'Малави',
        'CL' => 'Чили', 'PE' => 'Перу', 'CO' => 'Колумбия', 'VE' => 'Венесуэла',
        'EC' => 'Эквадор', 'BO' => 'Боливия', 'PY' => 'Парагвай', 'UY' => 'Уругвай',
        'CR' => 'Коста-Рика', 'PA' => 'Панама', 'GT' => 'Гватемала', 'HN' => 'Гондурас',
        'SV' => 'Сальвадор', 'NI' => 'Никарагуа', 'BZ' => 'Белиз', 'JM' => 'Ямайка',
        'CU' => 'Куба', 'DO' => 'Доминиканская Республика', 'HT' => 'Гаити',
        'TT' => 'Тринидад и Тобаго', 'BB' => 'Барбадос', 'BS' => 'Багамские Острова',
        'AF' => 'Афганистан', 'AL' => 'Албания', 'AM' => 'Армения', 'AO' => 'Ангола',
        'AZ' => 'Азербайджан', 'BD' => 'Бангладеш', 'BF' => 'Буркина-Фасо', 'BI' => 'Бурунди',
        'BJ' => 'Бенин', 'BN' => 'Бруней', 'BT' => 'Бутан', 'CD' => 'ДР Конго',
        'CF' => 'ЦАР', 'CG' => 'Республика Конго', 'CM' => 'Камерун', 'DJ' => 'Джибути',
        'ER' => 'Эритрея', 'GA' => 'Габон', 'GE' => 'Грузия', 'GN' => 'Гвинея',
        'GQ' => 'Экваториальная Гвинея', 'GW' => 'Гвинея-Бисау', 'IR' => 'Иран',
        'IQ' => 'Ирак', 'JO' => 'Иордания', 'KG' => 'Киргизия', 'KH' => 'Камбоджа',
        'KW' => 'Кувейт', 'LA' => 'Лаос', 'LB' => 'Ливан', 'LK' => 'Шри-Ланка',
        'LR' => 'Либерия', 'LS' => 'Лесото', 'MD' => 'Молдова', 'ML' => 'Мали',
        'MM' => 'Мьянма', 'MN' => 'Монголия', 'MR' => 'Мавритания', 'MU' => 'Маврикий',
        'MV' => 'Мальдивы', 'NE' => 'Нигер', 'NP' => 'Непал', 'OM' => 'Оман',
        'PK' => 'Пакистан', 'QA' => 'Катар', 'RW' => 'Руанда', 'SL' => 'Сьерра-Леоне',
        'SO' => 'Сомали', 'SS' => 'Южный Судан', 'SD' => 'Судан', 'SZ' => 'Эсватини',
        'TD' => 'Чад', 'TG' => 'Того', 'TJ' => 'Таджикистан', 'TM' => 'Туркменистан',
        'UZ' => 'Узбекистан', 'YE' => 'Йемен'
    ];

    return $countries[$isoCode] ?? "Не определен";
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

/** Проверка метода запроса */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => false,
        'error' => [
            'code' => 'METHOD_NOT_ALLOWED',
            'message' => 'Разрешен только POST метод'
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Получение и валидация входных данных */
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$phone = trim($input['phone'] ?? '');

/** Проверка наличия номера телефона */
if (empty($phone)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'error' => [
            'code' => 'PHONE_REQUIRED',
            'message' => 'Номер телефона обязателен'
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Инициализация ответа */
$response = [
    'status' => true,
    'data' => [
        'input' => $phone,
        'libphonenumber' => [
            'valid' => false,
            'is_possible' => false,
            'country_code' => null,
            'national_number' => null,
            'region' => null,
            'type' => null,
        ]
    ]
];

try {
    $phoneNumberUtil = PhoneNumberUtil::getInstance();

    /** Парсинг номера */
    $phoneNumber = $phoneNumberUtil->parse($phone);

    /** Заполнение данных */
    $response['data']['libphonenumber'] = [
        'is_possible' => $phoneNumberUtil->isPossibleNumber($phoneNumber),
        'valid' => $phoneNumberUtil->isValidNumber($phoneNumber),
        'country_code' => $phoneNumber->getCountryCode(),
        'national_number' => $phoneNumber->getNationalNumber(),
        'region' => getCountryName($phoneNumberUtil->getRegionCodeForNumber($phoneNumber)),
        'type' => $phoneNumberUtil->getNumberType($phoneNumber),
    ];

} catch (NumberParseException $exception) {
    http_response_code(400);

    $errorMessages = [
        NumberParseException::INVALID_COUNTRY_CODE => 'Неверный код страны',
        NumberParseException::NOT_A_NUMBER => 'Введенная строка не является номером телефона',
        NumberParseException::TOO_SHORT_NSN => 'Номер слишком короткий',
        NumberParseException::TOO_SHORT_AFTER_IDD => 'Номер слишком короткий после международного кода',
        NumberParseException::TOO_LONG => 'Номер слишком длинный'
    ];

    echo json_encode([
        'status' => false,
        'error' => [
            'code' => 'PARSE_ERROR',
            'message' => $errorMessages[$exception->getErrorType()] ?? 'Ошибка проверки номера',
            'details' => $exception->getMessage()
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'message' => 'Внутренняя ошибка сервера'
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
