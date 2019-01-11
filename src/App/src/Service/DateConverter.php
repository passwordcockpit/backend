<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Service;

class DateConverter
{
    /**
     * Database date format.
     *
     * @var string
     */
    protected static $databaseDateFormat = 'Y-m-d';

    /**
     * Output date format.
     *
     * @var string
     */
    protected static $outputDateFormat = 'd.m.Y';

    /**
     * Database time format.
     *
     * @var string
     */
    protected static $databaseTimeFormat = 'H:i:s';

    /**
     * Output time format.
     *
     * @var string
     */
    protected static $outputTimeFormat = 'H:i';

    /**
     * Convert database date to output date.
     *
     * @param string $date
     * @return string
     * @throws Exception
     */
    public static function getOutputDate($date)
    {
        $dateObject = \DateTime::createFromFormat(
            self::$databaseDateFormat,
            $date
        );
        return $dateObject
            ? $dateObject->format(self::$outputDateFormat)
            : $date;
    }

    /**
     * Convert database time to output time
     *
     * @param string $time
     * @return string
     */
    public static function getOutputTime($time)
    {
        $timeObject = \DateTime::createFromFormat(
            self::$databaseTimeFormat,
            $time
        );
        return $timeObject
            ? $timeObject->format(self::$outputTimeFormat)
            : $time;
    }

    /**
     * Convert database datetime to output datetime.
     *
     * @param string $date
     * @return string
     * @throws Exception
     */
    public static function getOutputDatetime($date)
    {
        $dateObject = \DateTime::createFromFormat(
            self::$databaseDateFormat . ' ' . self::$databaseTimeFormat,
            $date
        );
        return $dateObject
            ? $dateObject->format(
                self::$outputDateFormat . ' ' . self::$outputTimeFormat
            )
            : $date;
    }

    /**
     * Convert output date to database date
     *
     * @param string $date
     * @return string
     */
    public static function getDatabaseDate($date)
    {
        $dateObject = \DateTime::createFromFormat(
            self::$outputDateFormat,
            $date
        );
        return $dateObject
            ? $dateObject->format(self::$databaseDateFormat)
            : $date;
    }

    /**
     * Convert output time to database time
     *
     * @param string $time
     * @return string
     */
    public static function getDatabaseTime($time)
    {
        $timeObject = \DateTime::createFromFormat(
            self::$outputTimeFormat,
            $time
        );
        return $timeObject
            ? $timeObject->format(self::$databaseTimeFormat)
            : $time;
    }

    /**
     * Convert output datetime to database datetime
     *
     * @param string $date
     * @return string
     */
    public static function getDatabaseDatetime($date)
    {
        $dateObject = \DateTime::createFromFormat(
            self::$outputDateFormat . ' ' . self::$outputTimeFormat,
            $date
        );
        return $dateObject
            ? $dateObject->format(
                self::$databaseDateFormat . ' ' . self::$databaseTimeFormat
            )
            : $date;
    }

    /**
     * Return DateTime object as formatted string.
     * $format can be:
     * 	- "database" for database date format
     * 	- "output" for output date format
     * 	- a specific format given by user
     *
     * @param \DateTime $date
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public static function formatDateTime($date, $format)
    {
        if (!$date instanceof \DateTime) {
            throw new \Exception('Object given is not of type "DateTime"');
        }
        switch ($format) {
            case 'databaseDate':
                return $date->format(self::$databaseDateFormat);
            case 'databaseTime':
                return $date->format(self::$databaseTimeFormat);
            case 'databaseDateTime':
                return $date->format(
                    self::$databaseDateFormat . ' ' . self::$databaseTimeFormat
                );
            case 'outputDate':
                return $date->format(self::$outputDateFormat);
            case 'outputTime':
                return $date->format(self::$outputTimeFormat);
            case 'outputDateTime':
                return $date->format(
                    self::$outputDateFormat . ' ' . self::$outputTimeFormat
                );
            default:
                return $date->format($format);
        }
    }
}
