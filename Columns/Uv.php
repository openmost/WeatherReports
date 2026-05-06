<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class Uv extends Base
{
    protected $columnName = 'weather_uv';
    protected $columnType = 'FLOAT NULL';
    protected $nameSingular = 'WeatherReports_Uv';
    protected $segmentName = 'weatherUv';
    protected $acceptValues = 'Decimal between 0 and 20 (UV index)';

    protected $paramName = 'weather_uv';
    protected $paramType = 'float';

    public function sanitize($value)
    {
        if ($value < 0 || $value > 20) {
            return null;
        }
        return $value;
    }
}
