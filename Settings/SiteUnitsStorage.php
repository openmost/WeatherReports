<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\WeatherReports\Settings;

use Piwik\Plugins\WeatherReports\Units;
use Piwik\Settings\Storage\Backend\MeasurableSettingsTable;
use Piwik\Settings\Storage\Storage;

/**
 * Units of a site, edited on the Weather page of the Websites administration.
 *
 * Stored in the site settings table under the names used by the MeasurableSettings of plugin versions before
 * 6.2.0, so units saved from the website form are kept. Values saved as ["c"] by versions before 6.1.0 are read too.
 */
final class SiteUnitsStorage
{
    public const PLUGIN_NAME = 'WeatherReports';

    public const SETTING_NAMES = [
        Units::TEMPERATURE => 'weatherTemperatureUnit',
        Units::PRECIPITATION => 'weatherPrecipitationUnit',
        Units::PRESSURE => 'weatherPressureUnit',
        Units::VISIBILITY => 'weatherVisibilityUnit',
        Units::WIND => 'weatherWindSpeed',
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
     * Saving clears the settings and tracker caches, the tracker converts the next values to the new units.
     *
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
        Units::clearCache();
    }
}
