<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Plugin\SettingsProvider;
use Piwik\Plugins\Live\VisitorDetailsAbstract;
use Piwik\View;

class VisitorDetails extends VisitorDetailsAbstract
{
    /** Display order in the visitor log details panel (lower = earlier). */
    private const DETAILS_ORDER = 70;

    private const COLUMNS = [
        'weather_condition',
        'weather_cloud',
        'weather_precipitation',
        'weather_felt_temperature',
        'weather_humidity',
        'weather_pressure',
        'weather_temperature',
        'weather_uv',
        'weather_visibility',
        'weather_wind_direction',
        'weather_wind_speed',
    ];

    private const TEMP_UNITS       = ['c' => '°C', 'f' => '°F'];
    private const PRECIP_UNITS     = ['mm' => ' mm', 'in' => ' in'];
    private const PRESSURE_UNITS   = ['mb' => ' mb', 'in' => ' inHg'];
    private const VISIBILITY_UNITS = ['km' => ' km', 'miles' => ' mi'];
    private const WIND_UNITS       = ['kph' => ' km/h', 'mph' => ' mph'];

    public function extendVisitorDetails(&$visitor)
    {
        foreach (self::COLUMNS as $column) {
            $visitor[$column] = $this->normalize($this->details[$column] ?? null);
        }
    }

    public function renderVisitorDetails($visitorDetails)
    {
        try {
            $values = [];
            foreach (self::COLUMNS as $column) {
                $values[$column] = $this->normalize($this->readColumn($visitorDetails, $column));
            }

            // No data at all → render nothing (don't even show an empty card).
            $hasAny = false;
            foreach ($values as $v) {
                if ($v !== null) {
                    $hasAny = true;
                    break;
                }
            }
            if (!$hasAny) {
                return [];
            }

            $units = $this->resolveUnits($this->extractIdSite($visitorDetails));

            $view = new View('@WeatherReports/_visitorDetails.twig');
            $view->wCondition       = $values['weather_condition'];
            $view->wCloud           = $values['weather_cloud'];
            $view->wPrecipitation   = $values['weather_precipitation'];
            $view->wFeltTemperature = $values['weather_felt_temperature'];
            $view->wHumidity        = $values['weather_humidity'];
            $view->wPressure        = $values['weather_pressure'];
            $view->wTemperature     = $values['weather_temperature'];
            $view->wUv              = $values['weather_uv'];
            $view->wVisibility      = $values['weather_visibility'];
            $view->wWindDirection   = $values['weather_wind_direction'];
            $view->wWindSpeed       = $values['weather_wind_speed'];
            $view->unitTemperature   = $units['temperature'];
            $view->unitPrecipitation = $units['precipitation'];
            $view->unitPressure      = $units['pressure'];
            $view->unitVisibility    = $units['visibility'];
            $view->unitWind          = $units['wind'];

            return [[self::DETAILS_ORDER, $view->render()]];
        } catch (\Throwable $e) {
            \Piwik\Log::warning('WeatherReports visitor log render failed: %s in %s:%s', $e->getMessage(), $e->getFile(), $e->getLine());
            return [];
        }
    }

    /**
     * Coerce empty / false / '' to null so the Twig "is not null" check
     * means "there is a real recorded value". Numeric 0 is preserved
     * (UV=0 on cloudy nights is a legitimate value).
     */
    private function normalize($value)
    {
        if ($value === null || $value === false || $value === '') {
            return null;
        }
        return $value;
    }

    /**
     * Read a column from either an array (API path) or a DataTable\Row
     * (visitor-log UI path). Row::getColumn returns false when missing,
     * which the caller normalizes to null.
     */
    private function readColumn($visitorDetails, string $column)
    {
        if (is_array($visitorDetails)) {
            return $visitorDetails[$column] ?? null;
        }
        if (is_object($visitorDetails) && method_exists($visitorDetails, 'getColumn')) {
            return $visitorDetails->getColumn($column);
        }
        return null;
    }

    /**
     * @param array|\Piwik\DataTable\Row $visitorDetails
     */
    private function extractIdSite($visitorDetails): int
    {
        if (is_array($visitorDetails)) {
            return (int) ($visitorDetails['idSite'] ?? 0);
        }
        if (is_object($visitorDetails) && method_exists($visitorDetails, 'getColumn')) {
            return (int) ($visitorDetails->getColumn('idSite') ?: 0);
        }
        return 0;
    }

    private function resolveUnits(int $idSite): array
    {
        $temp       = 'c';
        $precip     = 'mm';
        $pressure   = 'mb';
        $visibility = 'km';
        $wind       = 'kph';

        if ($idSite > 0) {
            try {
                /** @var SettingsProvider $provider */
                $provider = \Piwik\Container\StaticContainer::get(SettingsProvider::class);
                /** @var MeasurableSettings $settings */
                $settings = $provider->getMeasurableSettings('WeatherReports', $idSite);
                if ($settings) {
                    $temp       = $this->stringValue($settings->weatherTemperatureUnit, $temp);
                    $precip     = $this->stringValue($settings->weatherPrecipitationUnit, $precip);
                    $pressure   = $this->stringValue($settings->weatherPressureUnit, $pressure);
                    $visibility = $this->stringValue($settings->weatherVisibilityUnit, $visibility);
                    $wind       = $this->stringValue($settings->weatherWindSpeed, $wind);
                }
            } catch (\Throwable $e) {
                // Fall back silently.
            }
        }

        return [
            'temperature'   => self::TEMP_UNITS[$temp] ?? '',
            'precipitation' => self::PRECIP_UNITS[$precip] ?? '',
            'pressure'      => self::PRESSURE_UNITS[$pressure] ?? '',
            'visibility'    => self::VISIBILITY_UNITS[$visibility] ?? '',
            'wind'          => self::WIND_UNITS[$wind] ?? '',
        ];
    }

    private function stringValue($setting, string $default): string
    {
        if (!$setting) {
            return $default;
        }
        $value = $setting->getValue();
        if (is_array($value)) {
            $value = $value[0] ?? null;
        }
        if (!is_string($value) || $value === '') {
            return $default;
        }
        return $value;
    }
}
