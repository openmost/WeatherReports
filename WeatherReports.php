<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

class WeatherReports extends \Piwik\Plugin
{
    public function registerEvents()
    {
        return [
            'CronArchive.getArchivingAPIMethodForPlugin' => 'getArchivingAPIMethodForPlugin',
        ];
    }

    public function getArchivingAPIMethodForPlugin(&$method, $plugin)
    {
        if ($plugin === 'WeatherReports') {
            $method = 'WeatherReports.getCondition';
        }
    }
}
