<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WeatherReports\RecordBuilders;

use Piwik\Plugins\WeatherReports\Archiver;

class Humidity extends Base
{
    public function __construct()
    {
        // Cast to SIGNED for proper numeric sorting in SQL
        parent::__construct(Archiver::HUMIDITY_RECORD_NAME, 'CAST(' . Archiver::HUMIDITY_DIMENSION . ' AS SIGNED)', true);
    }
}
