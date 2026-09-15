<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Common;
use Piwik\Plugins\Live\VisitorDetailsAbstract;

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

    public function extendVisitorDetails(&$visitor)
    {
        foreach (self::COLUMNS as $column) {
            $visitor[$column] = $this->normalize($this->details[$column] ?? null);
        }
    }

    public function renderVisitorDetails($visitorDetails)
    {
        $weather = [];
        foreach (self::COLUMNS as $column) {
            $value = $this->normalize($this->readColumn($visitorDetails, $column));
            if ($value !== null) {
                $weather[$column] = $value;
            }
        }

        // No data at all: don't render an empty card
        if (empty($weather)) {
            return [];
        }

        if (isset($weather['weather_condition'])) {
            // stored sanitized by the tracker, Vue escapes it again when rendering
            $weather['weather_condition'] = Conditions::translateText(
                Common::unsanitizeInputValue((string) $weather['weather_condition']),
                Conditions::getCurrentLanguage()
            );
        }

        $props = [
            'weather' => $weather,
            'units' => Units::getSymbolsForSite((int) $this->readColumn($visitorDetails, 'idSite')),
        ];

        $attributes = '';
        foreach ($props as $name => $value) {
            $json = json_encode($value, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRESERVE_ZERO_FRACTION);
            $attributes .= sprintf(' %s="%s"', $name, htmlspecialchars((string) $json, ENT_QUOTES, 'UTF-8'));
        }

        return [[self::DETAILS_ORDER, '<div vue-entry="WeatherReports.VisitorWeather"' . $attributes . '></div>']];
    }

    /**
     * Coerce empty / false / '' to null so null means "no recorded value".
     * Numeric 0 is preserved (UV=0 on cloudy nights is a legitimate value).
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
     * (visitor-log UI path). Row::getColumn returns false when missing.
     *
     * @param array|\Piwik\DataTable\Row $visitorDetails
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
}
