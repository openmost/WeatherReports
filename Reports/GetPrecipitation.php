<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\Precipitation;
use Piwik\Plugins\WeatherReports\Units;

class GetPrecipitation extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new Precipitation();
        $this->name = Piwik::translate('WeatherReports_Precipitation');
        $this->documentation = Piwik::translate('WeatherReports_PrecipitationDescription');
        $this->order = 6;
        $this->valueUnit = Units::PRECIPITATION;
    }
}
