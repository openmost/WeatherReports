<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\Visibility;
use Piwik\Plugins\WeatherReports\Units;

class GetVisibility extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new Visibility();
        $this->name = Piwik::translate('WeatherReports_Visibility');
        $this->documentation = Piwik::translate('WeatherReports_VisibilityDescription');
        $this->order = 9;
        $this->valueUnit = Units::VISIBILITY;
    }
}
