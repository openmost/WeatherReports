<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\FeltTemperature;
use Piwik\Plugins\WeatherReports\Units;

class GetFeltTemperature extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new FeltTemperature();
        $this->name = Piwik::translate('WeatherReports_FeltTemperature');
        $this->documentation = Piwik::translate('WeatherReports_FeltTemperatureDescription');
        $this->order = 5;
        $this->valueUnit = Units::TEMPERATURE;
    }
}
