<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class Precipitation extends Base
{
    protected $columnName = 'weather_precipitation';
    protected $columnType = 'FLOAT NULL';
    protected $nameSingular = 'WeatherReports_Precipitation';
    protected $segmentName = 'weatherPrecipitation';
    protected $acceptValues = 'Precipitation amount as decimal';

    protected $paramName = 'weather_precipitation';
    protected $paramType = 'float';

    public function sanitize($value)
    {
        if ($value < 0 || $value > 1000) {
            return null;
        }
        return $value;
    }
}
