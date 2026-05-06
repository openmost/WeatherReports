<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class Temperature extends Base
{
    protected $columnName = 'weather_temperature';
    protected $columnType = 'FLOAT NULL';
    protected $nameSingular = 'WeatherReports_Temperature';
    protected $segmentName = 'weatherTemperature';
    protected $acceptValues = 'Temperature value as decimal';

    protected $paramName = 'weather_temperature';
    protected $paramType = 'float';

    public function sanitize($value)
    {
        if ($value < -100 || $value > 200) {
            return null;
        }
        return $value;
    }
}
