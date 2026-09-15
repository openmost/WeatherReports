<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Columns\Uv;

class GetUv extends BaseScale
{
    protected function init()
    {
        parent::init();

        $this->dimension = new Uv();
        $this->name = Piwik::translate('WeatherReports_Uv');
        $this->documentation = Piwik::translate('WeatherReports_UvDescription');
        $this->order = 8;
    }
}
