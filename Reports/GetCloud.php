<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\Cloud;
use Piwik\Plugins\WeatherReports\Units;

class GetCloud extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new Cloud();
        $this->name = Piwik::translate('WeatherReports_Cloud');
        $this->documentation = Piwik::translate('WeatherReports_CloudDescription');
        $this->order = 2;
        $this->valueUnit = Units::PERCENT;
    }
}
