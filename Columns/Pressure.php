<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class Pressure extends Base
{
    protected $columnName = 'weather_pressure';
    // FLOAT to keep precision when the user picks inHg (e.g. 29.85)
    protected $columnType = 'FLOAT NULL';
    protected $nameSingular = 'WeatherReports_Pressure';
    protected $segmentName = 'weatherPressure';
    protected $acceptValues = 'Atmospheric pressure as decimal';

    protected $paramName = 'weather_pressure';
    protected $paramType = 'float';

    public function sanitize($value)
    {
        if ($value < 0 || $value > 2000) {
            return null;
        }
        return $value;
    }
}
