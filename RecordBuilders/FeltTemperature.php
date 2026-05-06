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

class FeltTemperature extends Base
{
    public function __construct()
    {
        parent::__construct(Archiver::FELT_TEMPERATURE_RECORD_NAME, Archiver::FELT_TEMPERATURE_DIMENSION, true, true);
    }
}
