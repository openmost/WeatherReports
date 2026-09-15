<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\Humidity;
use Piwik\Plugins\WeatherReports\Units;

class GetHumidity extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new Humidity();
        $this->name = Piwik::translate('WeatherReports_Humidity');
        $this->documentation = Piwik::translate('WeatherReports_HumidityDescription');
        $this->order = 7;
        $this->valueUnit = Units::PERCENT;
    }
}
