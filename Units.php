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
 * Unit symbols of the weather values, read from the per-site MeasurableSettings.
 *
 * Values are stored in whatever unit the tracking code sent, the settings tell Matomo which unit that is.
 */
class Units
{
    public const TEMPERATURE = 'temperature';
    public const PRECIPITATION = 'precipitation';
    public const PRESSURE = 'pressure';
    public const VISIBILITY = 'visibility';
    public const WIND = 'wind';
    public const PERCENT = 'percent';

    private const DEFAULT_UNITS = [
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
    private static array $symbolsBySite = [];

    /**
     * @return array<string, string> quantity => unit symbol, e.g. ['temperature' => '°C', ...]
     */
    public static function getSymbolsForSite(int $idSite): array
    {
        if (!isset(self::$symbolsBySite[$idSite])) {
            self::$symbolsBySite[$idSite] = self::getSymbols(self::readSiteUnits($idSite));
        }

        return self::$symbolsBySite[$idSite];
    }

    public static function getSymbolForSite(int $idSite, string $quantity): string
    {
        if ($quantity === self::PERCENT) {
            return '%';
        }

        return self::getSymbolsForSite($idSite)[$quantity] ?? '';
    }

    /**
     * @param array<string, string> $units quantity => unit code as stored in the settings, e.g. ['temperature' => 'f']
     * @return array<string, string>
     */
    public static function getSymbols(array $units): array
    {
        $symbols = [];
        foreach (self::SYMBOLS as $quantity => $symbolsByUnit) {
            $unit = $units[$quantity] ?? self::DEFAULT_UNITS[$quantity];
            $symbols[$quantity] = $symbolsByUnit[$unit] ?? $symbolsByUnit[self::DEFAULT_UNITS[$quantity]];
        }

        return $symbols;
    }

    public static function clearCache(): void
    {
        self::$symbolsBySite = [];
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
            // single selects are declared as TYPE_ARRAY, the value may come back wrapped in an array
            $value = $settings->$settingName->getValue();
            if (is_array($value)) {
                $value = reset($value);
            }
            if (is_string($value) && $value !== '') {
                $units[$quantity] = $value;
            }
        }

        return $units;
    }
}
