<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\WeatherReports;

use Piwik\Container\StaticContainer;
use Piwik\Plugin\SettingsProvider;

/**
 * Units of the weather values, read from the per-site MeasurableSettings.
 *
 * Values are stored in the units of the site settings: the tracker converts values sent with a unit
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

    private const SETTING_NAMES = [
        self::TEMPERATURE => 'weatherTemperatureUnit',
        self::PRECIPITATION => 'weatherPrecipitationUnit',
        self::PRESSURE => 'weatherPressureUnit',
        self::VISIBILITY => 'weatherVisibilityUnit',
        self::WIND => 'weatherWindSpeed',
    ];

    /** @var array<int, array<string, string>> */
    private static array $unitCodesBySite = [];

    /**
     * @return array<string, string> quantity => unit code, e.g. ['temperature' => 'c', ...]
     */
    public static function getUnitCodesForSite(int $idSite): array
    {
        if (!isset(self::$unitCodesBySite[$idSite])) {
            self::$unitCodesBySite[$idSite] = self::getUnitCodes(self::readSiteUnits($idSite));
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

    public static function clearCache(): void
    {
        self::$unitCodesBySite = [];
    }

    /**
     * @return array<string, string>
     */
    private static function readSiteUnits(int $idSite): array
    {
        if ($idSite <= 0) {
            return [];
        }

        try {
            $settings = StaticContainer::get(SettingsProvider::class)->getMeasurableSettings('WeatherReports', $idSite);
        } catch (\Throwable $e) {
            return [];
        }

        if (!$settings instanceof MeasurableSettings) {
            return [];
        }

        $units = [];
        foreach (self::SETTING_NAMES as $quantity => $settingName) {
            $value = $settings->$settingName->getValue();
            if (is_string($value) && $value !== '') {
                $units[$quantity] = $value;
            }
        }

        return $units;
    }
}
