<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Settings;

use Piwik\Piwik;
use Piwik\Settings\Storage\Backend\MeasurableSettingsTable;
use Piwik\Settings\Storage\Storage;

/**
 * Units of a site, edited on the Weather page of the Websites administration.
 *
 * Stored in the site settings table under the names used by the MeasurableSettings of plugin versions before
 * 5.3.0, so units saved from the website form are kept. Values saved as ["c"] by these versions are read too.
 */
final class SiteUnitsStorage
{
    public const PLUGIN_NAME = 'WeatherReports';

    public const TEMPERATURE = 'temperature';
    public const PRECIPITATION = 'precipitation';
    public const PRESSURE = 'pressure';
    public const VISIBILITY = 'visibility';
    public const WIND = 'wind';

    public const SETTING_NAMES = [
        self::TEMPERATURE => 'weatherTemperatureUnit',
        self::PRECIPITATION => 'weatherPrecipitationUnit',
        self::PRESSURE => 'weatherPressureUnit',
        self::VISIBILITY => 'weatherVisibilityUnit',
        self::WIND => 'weatherWindSpeed',
    ];

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

    /**
     * @return array<string, string> quantity => stored unit code, quantities without a stored value are omitted
     */
    public static function read(int $idSite): array
    {
        if ($idSite <= 0) {
            return [];
        }

        try {
            $storedValues = (new MeasurableSettingsTable($idSite, self::PLUGIN_NAME))->load();
        } catch (\Throwable $e) {
            return [];
        }

        return self::fromStoredValues($storedValues);
    }

    /**
     * @param array<string, mixed> $storedValues setting name => stored value
     * @return array<string, string>
     */
    public static function fromStoredValues(array $storedValues): array
    {
        $units = [];
        foreach (self::SETTING_NAMES as $quantity => $settingName) {
            $value = SingleValue::unwrap($storedValues[$settingName] ?? null);
            if (is_scalar($value) && (string) $value !== '') {
                $units[$quantity] = (string) $value;
            }
        }

        return $units;
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

    public static function isKnownUnit(string $quantity, string $unit): bool
    {
        return isset(self::SYMBOLS[$quantity][$unit]);
    }

    /**
     * Fields of the Weather units page, one select per quantity.
     *
     * @return array<int, array<string, mixed>>
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

    /**
     * @param array<string, string> $units quantity => unit code, validated by the caller
     */
    public static function save(int $idSite, array $units): void
    {
        $storage = new Storage(new MeasurableSettingsTable($idSite, self::PLUGIN_NAME));

        foreach (self::SETTING_NAMES as $quantity => $settingName) {
            if (isset($units[$quantity])) {
                $storage->setValue($settingName, $units[$quantity]);
            }
        }

        $storage->save();
    }
}
