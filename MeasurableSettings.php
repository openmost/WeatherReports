<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Piwik;
use Piwik\Plugins\WeatherReports\Settings\SingleValueMeasurableSetting;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;

/**
 * Per-site units of the weather values: values are stored and displayed in these units.
 */
class MeasurableSettings extends \Piwik\Settings\Measurable\MeasurableSettings
{
    /** @var SingleValueMeasurableSetting */
    public $weatherTemperatureUnit;
    /** @var SingleValueMeasurableSetting */
    public $weatherPrecipitationUnit;
    /** @var SingleValueMeasurableSetting */
    public $weatherPressureUnit;
    /** @var SingleValueMeasurableSetting */
    public $weatherVisibilityUnit;
    /** @var SingleValueMeasurableSetting */
    public $weatherWindSpeed;

    protected function init()
    {
        $this->weatherTemperatureUnit = $this->makeUnitSetting('weatherTemperatureUnit', 'c', 'Temperature', [
            'c' => 'WeatherReports_Celsius',
            'f' => 'WeatherReports_Fahrenheit',
        ]);
        $this->weatherPrecipitationUnit = $this->makeUnitSetting('weatherPrecipitationUnit', 'mm', 'Precipitation', [
            'mm' => 'WeatherReports_Millimeters',
            'in' => 'WeatherReports_Inches',
        ]);
        $this->weatherPressureUnit = $this->makeUnitSetting('weatherPressureUnit', 'mb', 'Pressure', [
            'mb' => 'WeatherReports_Millibars',
            'in' => 'WeatherReports_Inches',
        ]);
        $this->weatherVisibilityUnit = $this->makeUnitSetting('weatherVisibilityUnit', 'km', 'Visibility', [
            'km' => 'WeatherReports_Kilometers',
            'miles' => 'WeatherReports_Miles',
        ]);
        $this->weatherWindSpeed = $this->makeUnitSetting('weatherWindSpeed', 'kph', 'WindSpeed', [
            'kph' => 'WeatherReports_KilometersPerHour',
            'mph' => 'WeatherReports_MilesPerHour',
        ]);
    }

    /**
     * @param array<string, string> $unitTranslationKeys unit code => translation key
     */
    private function makeUnitSetting(string $name, string $defaultUnit, string $quantity, array $unitTranslationKeys): SingleValueMeasurableSetting
    {
        $setting = new SingleValueMeasurableSetting($name, $defaultUnit, $this->pluginName, $this->idSite);
        $setting->setConfigureCallback(function (FieldConfig $field) use ($quantity, $unitTranslationKeys) {
            $field->title = Piwik::translate('WeatherReports_' . $quantity . 'UnitTitle');
            $field->description = Piwik::translate('WeatherReports_' . $quantity . 'UnitDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array_map([Piwik::class, 'translate'], $unitTranslationKeys);
            $field->validators[] = new NotEmpty();
            // API clients may still send the legacy ["c"] format
            $field->prepare = static function ($value) {
                return SingleValueMeasurableSetting::unwrap($value);
            };
        });

        $this->addSetting($setting);

        return $setting;
    }
}
