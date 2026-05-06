<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class Humidity extends Base
{
    protected $columnName = 'weather_humidity';
    protected $columnType = 'INT(10) NULL';
    protected $nameSingular = 'WeatherReports_Humidity';
    protected $segmentName = 'weatherHumidity';
    protected $acceptValues = 'Integer between 0 and 100 (relative humidity %)';

    protected $paramName = 'weather_humidity';
    protected $paramType = 'int';

    public function sanitize($value)
    {
        if ($value < 0 || $value > 100) {
            return null;
        }
        return $value;
    }
}
