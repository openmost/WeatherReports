<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

class Condition extends Base
{
    protected $columnName = 'weather_condition';
    protected $columnType = 'VARCHAR(255) NULL';
    protected $nameSingular = 'WeatherReports_Condition';
    protected $segmentName = 'weatherCondition';
    protected $acceptValues = 'A text value for the current weather';

    protected $paramName = 'weather_condition';
    protected $paramType = 'string';

    public function sanitize($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        return mb_substr($value, 0, 255);
    }
}
