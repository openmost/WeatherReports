<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\Pressure;
use Piwik\Plugins\WeatherReports\Units;

class GetPressure extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new Pressure();
        $this->name = Piwik::translate('WeatherReports_Pressure');
        $this->documentation = Piwik::translate('WeatherReports_PressureDescription');
        $this->order = 3;
        $this->valueUnit = Units::PRESSURE;
    }
}
