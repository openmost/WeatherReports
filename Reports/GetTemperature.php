<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\Temperature;
use Piwik\Plugins\WeatherReports\Units;

class GetTemperature extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new Temperature();
        $this->name = Piwik::translate('WeatherReports_Temperature');
        $this->documentation = Piwik::translate('WeatherReports_TemperatureDescription');
        $this->order = 4;
        $this->valueUnit = Units::TEMPERATURE;
    }
}
