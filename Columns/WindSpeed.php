<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class WindSpeed extends Base
{
    protected $columnName = 'weather_wind_speed';
    protected $columnType = 'FLOAT NULL';
    protected $nameSingular = 'WeatherReports_WindSpeed';
    protected $segmentName = 'weatherWindSpeed';
    protected $acceptValues = 'Wind speed value as decimal';

    protected $paramName = 'weather_wind_speed';
    protected $paramType = 'float';

    public function sanitize($value)
    {
        if ($value < 0 || $value > 1000) {
            return null;
        }
        return $value;
    }
}
