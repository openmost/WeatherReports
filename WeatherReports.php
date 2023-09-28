<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Plugins\Live\Model;
use Piwik\View;

class WeatherReports extends \Piwik\Plugin
{
    public function registerEvents()
    {
        return [
            'CronArchive.getArchivingAPIMethodForPlugin' => 'getArchivingAPIMethodForPlugin',
        ];
    }

    // support archiving just this plugin via core:archive
    public function getArchivingAPIMethodForPlugin(&$method, $plugin)
    {
        if ($plugin == 'WeatherReports') {
            $method = 'WeatherReports.getWeather';
        }
    }

    public function extendVisitorDetails(&$visitor) {
        $crmData = Model::getCRMData($visitor['userid']);

        foreach ($crmData as $prop => $value) {
            $visitor[$prop] = $value;
        }
    }
    public function provideActionsForVisit(&$actions, $visitorDetails) {
        $adviews = Model::getAdviews($visitorDetails['visitid']);
        $actions += $adviews;
    }
}
