<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\WeatherReports\Settings;

/**
 * Plugin versions before 6.1.0 declared single selects as TYPE_ARRAY and saved them as ["c"].
 */
final class SingleValue
{
    /**
     * Unwraps a legacy ["c"] value, other values are returned as is.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function unwrap($value)
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        return $value === false ? null : $value;
    }
}
