<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\WeatherReports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Settings\SiteUnitsStorage;

/**
 * Units of the weather values of a site, set on the Weather page of the Websites administration.
 *
 * Values are stored in the units of the site: the tracker converts values sent with a unit
 * system marker, older tracking codes send values in the configured units.
 */
class Units
{
    public const TEMPERATURE = 'temperature';
    public const PRECIPITATION = 'precipitation';
    public const PRESSURE = 'pressure';
    public const VISIBILITY = 'visibility';
    public const WIND = 'wind';
    public const PERCENT = 'percent';

    public const DEFAULT_UNITS = [
        self::TEMPERATURE => 'c',
        self::PRECIPITATION => 'mm',
        self::PRESSURE => 'mb',
        self::VISIBILITY => 'km',
        self::WIND => 'kph',
    ];

    private const SYMBOLS = [
        self::TEMPERATURE => ['c' => '°C', 'f' => '°F'],
        self::PRECIPITATION => ['mm' => 'mm', 'in' => 'in'],
        self::PRESSURE => ['mb' => 'mb', 'in' => 'inHg'],
        self::VISIBILITY => ['km' => 'km', 'miles' => 'mi'],
        self::WIND => ['kph' => 'km/h', 'mph' => 'mph'],
    ];

    private const UNIT_TRANSLATION_KEYS = [
        self::TEMPERATURE => ['c' => 'WeatherReports_Celsius', 'f' => 'WeatherReports_Fahrenheit'],
        self::PRECIPITATION => ['mm' => 'WeatherReports_Millimeters', 'in' => 'WeatherReports_Inches'],
        self::PRESSURE => ['mb' => 'WeatherReports_Millibars', 'in' => 'WeatherReports_Inches'],
        self::VISIBILITY => ['km' => 'WeatherReports_Kilometers', 'miles' => 'WeatherReports_Miles'],
        self::WIND => ['kph' => 'WeatherReports_KilometersPerHour', 'mph' => 'WeatherReports_MilesPerHour'],
    ];

    /** Prefix of the WeatherReports_*UnitTitle and WeatherReports_*UnitDescription translations */
    private const FIELD_TRANSLATION_PREFIXES = [
        self::TEMPERATURE => 'Temperature',
        self::PRECIPITATION => 'Precipitation',
        self::PRESSURE => 'Pressure',
        self::VISIBILITY => 'Visibility',
        self::WIND => 'WindSpeed',
    ];

    /** @var array<int, array<string, string>> */
    private static array $unitCodesBySite = [];

    /**
     * @return array<string, string> quantity => unit code, e.g. ['temperature' => 'c', ...]
     */
    public static function getUnitCodesForSite(int $idSite): array
    {
        if (!isset(self::$unitCodesBySite[$idSite])) {
            self::$unitCodesBySite[$idSite] = self::getUnitCodes(SiteUnitsStorage::read($idSite));
        }

        return self::$unitCodesBySite[$idSite];
    }

    /**
     * @return array<string, string> quantity => unit symbol, e.g. ['temperature' => '°C', ...]
     */
    public static function getSymbolsForSite(int $idSite): array
    {
        return self::getSymbols(self::getUnitCodesForSite($idSite));
    }

    public static function getSymbolForSite(int $idSite, string $quantity): string
    {
        if ($quantity === self::PERCENT) {
            return '%';
        }

        return self::getSymbolsForSite($idSite)[$quantity] ?? '';
    }

    public static function isKnownUnit(string $quantity, string $unit): bool
    {
        return isset(self::SYMBOLS[$quantity][$unit]);
    }

    /**
     * Known unit codes merged with the defaults, unknown codes fall back to the default.
     *
     * @param array<string, mixed> $units quantity => unit code
     * @return array<string, string>
     */
    public static function getUnitCodes(array $units): array
    {
        $codes = [];
        foreach (self::SYMBOLS as $quantity => $symbolsByUnit) {
            $unit = $units[$quantity] ?? null;
            $codes[$quantity] = is_string($unit) && isset($symbolsByUnit[$unit]) ? $unit : self::DEFAULT_UNITS[$quantity];
        }

        return $codes;
    }

    /**
     * @param array<string, mixed> $units quantity => unit code as stored in the settings, e.g. ['temperature' => 'f']
     * @return array<string, string>
     */
    public static function getSymbols(array $units): array
    {
        $symbols = [];
        foreach (self::getUnitCodes($units) as $quantity => $unit) {
            $symbols[$quantity] = self::SYMBOLS[$quantity][$unit];
        }

        return $symbols;
    }

    /**
     * Fields of the Weather units page.
     *
     * @return array<int, array{quantity: string, title: string, description: string, options: array<int, array{key: string, value: string}>}>
     */
    public static function getFieldsMetadata(): array
    {
        $fields = [];
        foreach (self::UNIT_TRANSLATION_KEYS as $quantity => $translationKeys) {
            $options = [];
            foreach ($translationKeys as $unit => $translationKey) {
                $options[] = [
                    'key' => $unit,
                    'value' => Piwik::translate($translationKey) . ' (' . self::SYMBOLS[$quantity][$unit] . ')',
                ];
            }

            $prefix = self::FIELD_TRANSLATION_PREFIXES[$quantity];
            $fields[] = [
                'quantity' => $quantity,
                'title' => Piwik::translate('WeatherReports_' . $prefix . 'UnitTitle'),
                'description' => Piwik::translate('WeatherReports_' . $prefix . 'UnitDescription'),
                'options' => $options,
            ];
        }

        return $fields;
    }

    public static function clearCache(): void
    {
        self::$unitCodesBySite = [];
    }
}
