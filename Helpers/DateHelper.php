<?php

namespace Piffy\Helpers;

use DateTime;
use stdClass;

class DateHelper
{

    protected static ?DateHelper $_instance = null;

    protected static array $monthsEnglish = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
    ];

    protected static array $monthsGerman = [
        'Januar',
        'Februar',
        'März',
        'April',
        'Mai',
        'Juni',
        'Juli',
        'August',
        'September',
        'Oktober',
        'November',
        'Dezember',
    ];

    private stdClass $birthday;

    private function __construct()
    {
        $this->birthday = new stdClass();
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function getReadableDate($date)
    {
        $time = strtotime($date);
        $date = date('d. M', $time);
        $date = str_replace(self::$monthsEnglish, self::$monthsGerman, $date);
        // $date .= ' Uhr';
        return $date;
    }

    public function addLeadingZero($num)
    {
        if ($num <= 9) {
            return '0' . $num;
        }
        return $num;
    }

    public function readableMonth($month)
    {
        return isset($this->months[$month - 1]) ? $this->months[$month - 1] : $month . '.';
    }

    public function getFullMonth($month)
    {
        return $this->months[$month - 1];
    }

    public function getMonthsBetweenDates($dateEnd, $dateStart)
    {
        return abs((date('Y', $dateEnd) - date('Y', $dateStart)) * 12 + (date('m', $dateEnd) - date('m', $dateStart)));
    }

    /**
     * @throws \Exception
     */
    public function getDaysBetweenDates($dateA, $dateB): int
    {
        $earlier = new DateTime($dateA);
        $later = new DateTime($dateB);

        // $neg_diff = $later->diff($earlier)->format("%r%a"); //-3
        return $earlier->diff($later)->format("%r%a"); // 3

    }

}