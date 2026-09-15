<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\WindSpeed;
use Piwik\Plugins\WeatherReports\Units;

class GetWindSpeed extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new WindSpeed();
        $this->name = Piwik::translate('WeatherReports_WindSpeed');
        $this->documentation = Piwik::translate('WeatherReports_WindSpeedDescription');
        $this->order = 11;
        $this->valueUnit = Units::WIND;
    }
}
