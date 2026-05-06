<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class WindDirection extends Base
{
    protected $columnName = 'weather_wind_direction';
    protected $columnType = 'VARCHAR(8) NULL';
    protected $nameSingular = 'WeatherReports_WindDirection';
    protected $segmentName = 'weatherWindDirection';
    protected $acceptValues = '16-point compass: N NNE NE ENE E ESE SE SSE S SSW SW WSW W WNW NW NNW';

    protected $paramName = 'weather_wind_direction';
    protected $paramType = 'string';

    private const COMPASS = [
        'N', 'NNE', 'NE', 'ENE',
        'E', 'ESE', 'SE', 'SSE',
        'S', 'SSW', 'SW', 'WSW',
        'W', 'WNW', 'NW', 'NNW',
    ];

    public function sanitize($value)
    {
        $value = strtoupper(trim((string) $value));
        if (!in_array($value, self::COMPASS, true)) {
            return null;
        }
        return $value;
    }
}
