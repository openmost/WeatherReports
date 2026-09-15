<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\WindDirection;

class GetWindDirection extends Base
{
    protected function init()
    {
        parent::init();

        $this->dimension = new WindDirection();
        $this->name = Piwik::translate('WeatherReports_WindDirection');
        $this->documentation = Piwik::translate('WeatherReports_WindDirectionDescription');
        $this->order = 10;
    }
}
