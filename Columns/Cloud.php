<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class Cloud extends Base
{
    protected $columnName = 'weather_cloud';
    protected $columnType = 'INT(10) NULL';
    protected $nameSingular = 'WeatherReports_Cloud';
    protected $segmentName = 'weatherCloud';
    protected $acceptValues = 'Integer between 0 and 100 (cloud cover %)';

    protected $paramName = 'weather_cloud';
    protected $paramType = 'int';

    public function sanitize($value)
    {
        if ($value < 0 || $value > 100) {
            return null;
        }
        return $value;
    }
}
