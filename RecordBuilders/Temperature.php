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

class Temperature extends Base
{
    public function __construct()
    {
        parent::__construct(Archiver::TEMPERATURE_RECORD_NAME, Archiver::TEMPERATURE_DIMENSION, true, true);
    }
}
