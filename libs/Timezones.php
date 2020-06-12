<?php

class Timezones
{
  public const TIME_ZONES = [
    -13 => [
      'timezone' => '-1300',
      'offset' => -46800,
      'label' => 'UTC-13:00 (DST utility)'
    ],
    -12 => [
      'timezone' => '-1200',
      'offset' => -43200,
      'label' => 'UTC-12:00 (Baker Island, Howland Island)'
    ],
    -11 => [
      'timezone' => '-1100',
      'offset' => -39600,
      'label' => 'UTC-11:00 (American Samoa, Niue)'
    ],
    -10 => [
      'timezone' => '-1000',
      'offset' => -36000,
      'label' => 'UTC-10:00 (French Polynesia, Honolulu)'
    ],
    -9 => [
      'timezone' => '-0900',
      'offset' => -32400,
      'label' => 'UTC-9:00 (Alaska, Gambier Islands)'
    ],
    -8 => [
      'timezone' => '-0800',
      'offset' => -28800,
      'label' => 'UTC-8:00 (Los Angeles, Vancouver, Tijuana)'
    ],
    -7 => [
      'timezone' => '-0700',
      'offset' => -25200,
      'label' => 'UTC-7:00 (Phoenix, Calgary, Ciudad Juárez)'
    ],
    -6 => [
      'timezone' => '-0600',
      'offset' => -21600,
      'label' => 'UTC-6:00 (Mexico City, Chicago, Guatemala City, Tegucigalpa, Winnipeg, San José, San Salvador)'
    ],
    -5 => [
      'timezone' => '-0500',
      'offset' => -18000,
      'label' => 'UTC-5:00 (New York, Toronto, Havana, Lima, Bogotá, Kingston)'
    ],
    -4 => [
      'timezone' => '-0400',
      'offset' => -14400,
      'label' => 'UTC-4:00 (Santiago, Santo Domingo, Manaus, Caracas, La Paz, Halifax)'
    ],
    -3 => [
      'timezone' => '-0300',
      'offset' => -10800,
      'label' => 'UTC-3:00 (São Paulo, Buenos Aires, Montevideo)'
    ],
    -2 => [
      'timezone' => '-0200',
      'offset' => -7200,
      'label' => 'UTC-2:00 (Fernando de Noronha, South Georgia and the South Sandwich Islands)'
    ],
    -1 => [
      'timezone' => '-0100',
      'offset' => -3600,
      'label' => 'UTC-1:00 (Azores islands, Cape Verde, Ittoqqortoormiit)'
    ],
    0 => [
      'timezone' => 'UTC',
      'offset' => 0,
      'label' => 'UTC (London, Dublin, Lisbon, Abidjan, Accra, Dakar)'
    ],
    1 => [
      'timezone' => '+0100',
      'offset' => 3600,
      'label' => 'UTC+1:00 (Berlin, Rome, Paris, Prague, Madrid, Warsaw, Lagos, Algiers, Casablanca)'
    ],
    2 => [
      'timezone' => '+0200',
      'offset' => 7200,
      'label' => 'UTC+2:00 (Cairo, Johannesburg, Khartoum, Kiev, Bucharest, Athens, Jerusalem, Sofia)'
    ],
    3 => [
      'timezone' => '+0300',
      'offset' => 10800,
      'label' => 'UTC+3:00 (Moscow, Istanbul, Riyadh, Baghdad, Addis Ababa, Doha)'
    ],
    4 => [
      'timezone' => '+0400',
      'offset' => 14400,
      'label' => 'UTC+4:00 (Dubai, Baku, Tbilisi, Yerevan, Samara)'
    ],
    5 => [
      'timezone' => '+0500',
      'offset' => 18000,
      'label' => 'UTC+5:00 (Karachi, Tashkent, Yekaterinburg)'
    ],
    6 => [
      'timezone' => '+0600',
      'offset' => 21600,
      'label' => 'UTC+6:00 (Dhaka, Almaty, Omsk)'
    ],
    7 => [
      'timezone' => '+0700',
      'offset' => 25200,
      'label' => 'UTC+7:00 (Jakarta, Ho Chi Minh City, Bangkok, Krasnoyarsk)'
    ],
    8 => [
      'timezone' => '+0800',
      'offset' => 28800,
      'label' => 'UTC+8:00 (Shanghai, Taipei, Kuala Lumpur, Singapore, Perth, Manila, Makassar, Irkutsk)'
    ],
    9 => [
      'timezone' => '+0900',
      'offset' => 32400,
      'label' => 'UTC+9:00 (Tokyo, Seoul, Pyongyang, Ambon, Yakutsk)'
    ],
    10 => [
      'timezone' => '+1000',
      'offset' => 36000,
      'label' => 'UTC+10:00 (Sydney, Port Moresby, Vladivostok)'
    ],
    11 => [
      'timezone' => '+1100',
      'offset' => 39600,
      'label' => 'UTC+11:00 (Norfolk Island, Solomon Islands)'
    ],
    12 => [
      'timezone' => '+1200',
      'offset' => 43200,
      'label' => 'UTC+12:00 (Auckland, Suva, Petropavlovsk-Kamchatsky, Marshall Islands, Fiji)'
    ],
    13 => [
      'timezone' => '+1300',
      'offset' => 46800,
      'label' => 'UTC+13:00 (Samoa, Tonga)'
    ],
    14 => [
      'timezone' => '+1400',
      'offset' => 50400,
      'label' => 'UTC+14:00 (Kiribati)'
    ],
    15 => [
      'timezone' => '+1500',
      'offset' => 54000,
      'label' => 'UTC+15:00 (DST utility)'
    ]
  ];

  private const TIMEZONE_NAMES = array(
    'Pacific/Midway'       => "(GMT-11:00) Midway Island",
    'US/Samoa'             => "(GMT-11:00) Samoa",
    'US/Hawaii'            => "(GMT-10:00) Hawaii",
    'US/Alaska'            => "(GMT-09:00) Alaska",
    'US/Pacific'           => "(GMT-08:00) Pacific Time (US &amp; Canada)",
    'America/Tijuana'      => "(GMT-08:00) Tijuana",
    'US/Arizona'           => "(GMT-07:00) Arizona",
    'US/Mountain'          => "(GMT-07:00) Mountain Time (US &amp; Canada)",
    'America/Chihuahua'    => "(GMT-07:00) Chihuahua",
    'America/Mazatlan'     => "(GMT-07:00) Mazatlan",
    'America/Mexico_City'  => "(GMT-06:00) Mexico City",
    'America/Monterrey'    => "(GMT-06:00) Monterrey",
    'Canada/Saskatchewan'  => "(GMT-06:00) Saskatchewan",
    'US/Central'           => "(GMT-06:00) Central Time (US &amp; Canada)",
    'US/Eastern'           => "(GMT-05:00) Eastern Time (US &amp; Canada)",
    'US/East-Indiana'      => "(GMT-05:00) Indiana (East)",
    'America/Bogota'       => "(GMT-05:00) Bogota",
    'America/Lima'         => "(GMT-05:00) Lima",
    'America/Caracas'      => "(GMT-04:30) Caracas",
    'Canada/Atlantic'      => "(GMT-04:00) Atlantic Time (Canada)",
    'America/La_Paz'       => "(GMT-04:00) La Paz",
    'America/Santiago'     => "(GMT-04:00) Santiago",
    'Canada/Newfoundland'  => "(GMT-03:30) Newfoundland",
    'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
    'Greenland'            => "(GMT-03:00) Greenland",
    'Atlantic/Stanley'     => "(GMT-02:00) Stanley",
    'Atlantic/Azores'      => "(GMT-01:00) Azores",
    'Atlantic/Cape_Verde'  => "(GMT-01:00) Cape Verde Is.",
    'Africa/Casablanca'    => "(GMT) Casablanca",
    'Europe/Dublin'        => "(GMT) Dublin",
    'Europe/Lisbon'        => "(GMT) Lisbon",
    'Europe/London'        => "(GMT) London",
    'Africa/Monrovia'      => "(GMT) Monrovia",
    'Europe/Amsterdam'     => "(GMT+01:00) Amsterdam",
    'Europe/Belgrade'      => "(GMT+01:00) Belgrade",
    'Europe/Berlin'        => "(GMT+01:00) Berlin",
    'Europe/Bratislava'    => "(GMT+01:00) Bratislava",
    'Europe/Brussels'      => "(GMT+01:00) Brussels",
    'Europe/Budapest'      => "(GMT+01:00) Budapest",
    'Europe/Copenhagen'    => "(GMT+01:00) Copenhagen",
    'Europe/Ljubljana'     => "(GMT+01:00) Ljubljana",
    'Europe/Madrid'        => "(GMT+01:00) Madrid",
    'Europe/Paris'         => "(GMT+01:00) Paris",
    'Europe/Prague'        => "(GMT+01:00) Prague",
    'Europe/Rome'          => "(GMT+01:00) Rome",
    'Europe/Sarajevo'      => "(GMT+01:00) Sarajevo",
    'Europe/Skopje'        => "(GMT+01:00) Skopje",
    'Europe/Stockholm'     => "(GMT+01:00) Stockholm",
    'Europe/Vienna'        => "(GMT+01:00) Vienna",
    'Europe/Warsaw'        => "(GMT+01:00) Warsaw",
    'Europe/Zagreb'        => "(GMT+01:00) Zagreb",
    'Europe/Athens'        => "(GMT+02:00) Athens",
    'Europe/Bucharest'     => "(GMT+02:00) Bucharest",
    'Africa/Cairo'         => "(GMT+02:00) Cairo",
    'Africa/Harare'        => "(GMT+02:00) Harare",
    'Europe/Helsinki'      => "(GMT+02:00) Helsinki",
    'Europe/Istanbul'      => "(GMT+02:00) Istanbul",
    'Asia/Jerusalem'       => "(GMT+02:00) Jerusalem",
    'Europe/Kiev'          => "(GMT+02:00) Kyiv",
    'Europe/Minsk'         => "(GMT+02:00) Minsk",
    'Europe/Riga'          => "(GMT+02:00) Riga",
    'Europe/Sofia'         => "(GMT+02:00) Sofia",
    'Europe/Tallinn'       => "(GMT+02:00) Tallinn",
    'Europe/Vilnius'       => "(GMT+02:00) Vilnius",
    'Asia/Baghdad'         => "(GMT+03:00) Baghdad",
    'Asia/Kuwait'          => "(GMT+03:00) Kuwait",
    'Africa/Nairobi'       => "(GMT+03:00) Nairobi",
    'Asia/Riyadh'          => "(GMT+03:00) Riyadh",
    'Europe/Moscow'        => "(GMT+03:00) Moscow",
    'Asia/Tehran'          => "(GMT+03:30) Tehran",
    'Asia/Baku'            => "(GMT+04:00) Baku",
    'Europe/Volgograd'     => "(GMT+04:00) Volgograd",
    'Asia/Muscat'          => "(GMT+04:00) Muscat",
    'Asia/Tbilisi'         => "(GMT+04:00) Tbilisi",
    'Asia/Yerevan'         => "(GMT+04:00) Yerevan",
    'Asia/Kabul'           => "(GMT+04:30) Kabul",
    'Asia/Karachi'         => "(GMT+05:00) Karachi",
    'Asia/Tashkent'        => "(GMT+05:00) Tashkent",
    'Asia/Kolkata'         => "(GMT+05:30) Kolkata",
    'Asia/Kathmandu'       => "(GMT+05:45) Kathmandu",
    'Asia/Yekaterinburg'   => "(GMT+06:00) Ekaterinburg",
    'Asia/Almaty'          => "(GMT+06:00) Almaty",
    'Asia/Dhaka'           => "(GMT+06:00) Dhaka",
    'Asia/Novosibirsk'     => "(GMT+07:00) Novosibirsk",
    'Asia/Bangkok'         => "(GMT+07:00) Bangkok",
    'Asia/Jakarta'         => "(GMT+07:00) Jakarta",
    'Asia/Krasnoyarsk'     => "(GMT+08:00) Krasnoyarsk",
    'Asia/Chongqing'       => "(GMT+08:00) Chongqing",
    'Asia/Hong_Kong'       => "(GMT+08:00) Hong Kong",
    'Asia/Kuala_Lumpur'    => "(GMT+08:00) Kuala Lumpur",
    'Australia/Perth'      => "(GMT+08:00) Perth",
    'Asia/Singapore'       => "(GMT+08:00) Singapore",
    'Asia/Taipei'          => "(GMT+08:00) Taipei",
    'Asia/Ulaanbaatar'     => "(GMT+08:00) Ulaan Bataar",
    'Asia/Urumqi'          => "(GMT+08:00) Urumqi",
    'Asia/Irkutsk'         => "(GMT+09:00) Irkutsk",
    'Asia/Seoul'           => "(GMT+09:00) Seoul",
    'Asia/Tokyo'           => "(GMT+09:00) Tokyo",
    'Australia/Adelaide'   => "(GMT+09:30) Adelaide",
    'Australia/Darwin'     => "(GMT+09:30) Darwin",
    'Asia/Yakutsk'         => "(GMT+10:00) Yakutsk",
    'Australia/Brisbane'   => "(GMT+10:00) Brisbane",
    'Australia/Canberra'   => "(GMT+10:00) Canberra",
    'Pacific/Guam'         => "(GMT+10:00) Guam",
    'Australia/Hobart'     => "(GMT+10:00) Hobart",
    'Australia/Melbourne'  => "(GMT+10:00) Melbourne",
    'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
    'Australia/Sydney'     => "(GMT+10:00) Sydney",
    'Asia/Vladivostok'     => "(GMT+11:00) Vladivostok",
    'Asia/Magadan'         => "(GMT+12:00) Magadan",
    'Pacific/Auckland'     => "(GMT+12:00) Auckland",
    'Pacific/Fiji'         => "(GMT+12:00) Fiji",
  );

  public static function getUserTime(DateTime $date, $tz, $dst = false): DateTime
  {
    if ($dst != false) {
      $pragueTime = new DateTime('', new DateTimeZone('Europe/Prague'));
      $isSummer = $pragueTime->format('I');
      if ($isSummer == 1) {
        $timezone = (string) Timezones::TIME_ZONES[$tz + 1]['timezone'];
      } else {
        $timezone = (string) Timezones::TIME_ZONES[$tz]['timezone'];
      }
    } else {
      $timezone = (string) Timezones::TIME_ZONES[$tz]['timezone'];
    }
    $timezone = substr_count($timezone, '+', 0, 1) >= 1 ? str_replace('+', '-', $timezone) : str_replace('-', '+', $timezone);
    $datestr = $date->format('Y-m-d H:i:s');
    return new DateTime("$datestr $timezone");
  }

  public static function getUserTimezone($tz)
  {
     return (string) Timezones::TIME_ZONES[$tz]['timezone'];
  }

	public static function listZones() {
    $zones = null;
    $timezones = null;

    if ($zones === null) {
        $timezones = [];
        $offsets = [];
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
            $offsets[] = $offset = $now->getOffset();
            $timezones[$timezone] = '(' . Timezones::format_GMT_offset($offset) . ') ' . Timezones::format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
        $zones = [
            'timezones' => $timezones,
            'offsets' => $offsets
        ];
    }

    return $zones;
  }

  public static function format_GMT_offset($offset) {
      $hours = intval($offset / 3600);
      $minutes = abs(intval($offset % 3600 / 60));
      return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
  }

  public static function format_timezone_name($name) {
      $name = str_replace('/', ', ', $name);
      $name = str_replace('_', ' ', $name);
      $name = str_replace('St ', 'St. ', $name);
      return $name;
  }
}
