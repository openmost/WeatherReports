<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Plugins\Live\VisitorDetailsAbstract;
use Piwik\View;

/**
 * @see plugins/Provider/functions.php
 */
class VisitorDetails extends VisitorDetailsAbstract
{
    public function extendVisitorDetails(&$visitor)
    {
        $visitor['weather_condition'] = $this->details['weather_condition'];
        $visitor['weather_cloud'] = $this->details['weather_cloud'];
        $visitor['weather_precipitation'] = $this->details['weather_precipitation'];
        $visitor['weather_felt_temperature'] = $this->details['weather_felt_temperature'];
        $visitor['weather_humidity'] = $this->details['weather_humidity'];
        $visitor['weather_pressure'] = $this->details['weather_pressure'];
        $visitor['weather_temperature'] = $this->details['weather_temperature'];
        $visitor['weather_uv'] = $this->details['weather_uv'];
        $visitor['weather_visibility'] = $this->details['weather_visibility'];
        $visitor['weather_wind_direction'] = $this->details['weather_wind_direction'];
        $visitor['weather_wind_speed'] = $this->details['weather_wind_speed'];
    }

    public function renderVisitorDetails($visitorDetails)
    {
        $view = new View('@WeatherReports/_visitorDetails.twig');
        $view->visitInfo = $visitorDetails;
        return [[70, $view->render()]];
    }
}