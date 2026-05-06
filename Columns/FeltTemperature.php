<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class FeltTemperature extends Base
{
    protected $columnName = 'weather_felt_temperature';
    protected $columnType = 'FLOAT NULL';
    protected $nameSingular = 'WeatherReports_FeltTemperature';
    protected $segmentName = 'weatherFeltTemperature';
    protected $acceptValues = 'Felt temperature value as decimal';

    protected $paramName = 'weather_felt_temperature';
    protected $paramType = 'float';

    public function sanitize($value)
    {
        if ($value < -100 || $value > 100) {
            return null;
        }
        return $value;
    }
}
