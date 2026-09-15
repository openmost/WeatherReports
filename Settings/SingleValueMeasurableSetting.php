<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Settings;

use Piwik\Settings\FieldConfig;
use Piwik\Settings\Measurable\MeasurableSetting;

/**
 * Single select setting stored as a string.
 *
 * Before 6.1.0 the unit settings were declared as TYPE_ARRAY and saved as ["c"]. Reading accepts both formats so
 * existing sites keep their units without any migration, the next save of the site settings stores a string.
 */
class SingleValueMeasurableSetting extends MeasurableSetting
{
    public function __construct($name, $defaultValue, $pluginName, $idSite)
    {
        parent::__construct($name, $defaultValue, FieldConfig::TYPE_STRING, $pluginName, $idSite);
    }

    public function getValue()
    {
        // read as array: a string value becomes [value], a legacy array stays as is, no "Array to string" cast
        $value = $this->storage->getValue($this->name, $this->defaultValue, FieldConfig::TYPE_ARRAY);

        return self::toSingleValue($value, (string) $this->defaultValue);
    }

    /**
     * Unwraps a legacy ["c"] value, used both when reading and before validating a value to save.
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

    /**
     * @param mixed $value
     */
    private static function toSingleValue($value, string $default): string
    {
        $value = self::unwrap($value);

        return is_scalar($value) && (string) $value !== '' ? (string) $value : $default;
    }
}
