<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class Visibility extends Base
{
    protected $columnName = 'weather_visibility';
    // FLOAT to keep precision for vis_miles (e.g. 6.2)
    protected $columnType = 'FLOAT NULL';
    protected $nameSingular = 'WeatherReports_Visibility';
    protected $segmentName = 'weatherVisibility';
    protected $acceptValues = 'Visibility distance as decimal';

    protected $paramName = 'weather_visibility';
    protected $paramType = 'float';

    public function sanitize($value)
    {
        if ($value < 0 || $value > 1000) {
            return null;
        }
        return $value;
    }
}
