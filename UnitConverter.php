<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\WeatherReports;

/**
 * Converts tracked weather values between the metric and imperial units supported by the site settings.
 */
final class UnitConverter
{
    public const SYSTEM_METRIC = 'metric';
    public const SYSTEM_IMPERIAL = 'imperial';

    /** Unit codes (as stored in the site settings) of each unit system */
    private const SYSTEM_UNITS = [
        self::SYSTEM_METRIC => [
            Units::TEMPERATURE => 'c',
            Units::PRECIPITATION => 'mm',
            Units::PRESSURE => 'mb',
            Units::VISIBILITY => 'km',
            Units::WIND => 'kph',
        ],
        self::SYSTEM_IMPERIAL => [
            Units::TEMPERATURE => 'f',
            Units::PRECIPITATION => 'in',
            Units::PRESSURE => 'in',
            Units::VISIBILITY => 'miles',
            Units::WIND => 'mph',
        ],
    ];

    private const PRECISION = [
        Units::TEMPERATURE => 1,
        Units::PRECIPITATION => 2,
        Units::PRESSURE => 2,
        Units::VISIBILITY => 1,
        Units::WIND => 1,
    ];

    private const MM_PER_INCH = 25.4;
    private const MB_PER_INCH_OF_MERCURY = 33.8639;
    private const KM_PER_MILE = 1.609344;

    public static function isKnownSystem($system): bool
    {
        return is_string($system) && isset(self::SYSTEM_UNITS[$system]);
    }

    /**
     * Converts a value sent in a unit system to the unit configured for the site.
     *
     * @param array<string, string> $siteUnits quantity => unit code, see Units::getUnitCodesForSite()
     */
    public static function convertFromSystem(float $value, string $quantity, string $system, array $siteUnits): float
    {
        $fromUnit = self::SYSTEM_UNITS[$system][$quantity] ?? null;
        $toUnit = $siteUnits[$quantity] ?? null;

        if ($fromUnit === null || $toUnit === null) {
            return $value;
        }

        return self::convert($value, $quantity, $fromUnit, $toUnit);
    }

    public static function convert(float $value, string $quantity, string $fromUnit, string $toUnit): float
    {
        if ($fromUnit === $toUnit || !isset(self::PRECISION[$quantity])) {
            return $value;
        }

        $metricValue = self::toMetric($value, $quantity, $fromUnit);
        $converted = self::fromMetric($metricValue, $quantity, $toUnit);

        return round($converted, self::PRECISION[$quantity]);
    }

    private static function toMetric(float $value, string $quantity, string $unit): float
    {
        if ($unit === self::SYSTEM_UNITS[self::SYSTEM_METRIC][$quantity]) {
            return $value;
        }

        return match ($quantity) {
            Units::TEMPERATURE => ($value - 32) * 5 / 9,
            Units::PRECIPITATION => $value * self::MM_PER_INCH,
            Units::PRESSURE => $value * self::MB_PER_INCH_OF_MERCURY,
            Units::VISIBILITY, Units::WIND => $value * self::KM_PER_MILE,
        };
    }

    private static function fromMetric(float $value, string $quantity, string $unit): float
    {
        if ($unit === self::SYSTEM_UNITS[self::SYSTEM_METRIC][$quantity]) {
            return $value;
        }

        return match ($quantity) {
            Units::TEMPERATURE => $value * 9 / 5 + 32,
            Units::PRECIPITATION => $value / self::MM_PER_INCH,
            Units::PRESSURE => $value / self::MB_PER_INCH_OF_MERCURY,
            Units::VISIBILITY, Units::WIND => $value / self::KM_PER_MILE,
        };
    }
}
