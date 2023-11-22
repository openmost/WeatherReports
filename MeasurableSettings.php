<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Piwik;
use Piwik\Settings\Setting;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;

/**
 * Defines Settings for WeatherReports.
 *
 * Usage like this:
 * // require Piwik\Plugin\SettingsProvider via Dependency Injection eg in constructor of your class
 * $settings = $settingsProvider->getMeasurableSettings('WeatherReports', $idSite);
 * $settings->appId->getValue();
 * $settings->contactEmails->getValue();
 */
class MeasurableSettings extends \Piwik\Settings\Measurable\MeasurableSettings
{
    /**
     * @var \Piwik\Settings\Measurable\MeasurableSetting
     */
    public $weatherTemperatureUnit;
    /**
     * @var \Piwik\Settings\Measurable\MeasurableSetting
     */
    public $weatherPrecipitationUnit;
    /**
     * @var \Piwik\Settings\Measurable\MeasurableSetting
     */
    public $weatherPressureUnit;
    /**
     * @var \Piwik\Settings\Measurable\MeasurableSetting
     */
    public $weatherVisibilityUnit;
    /**
     * @var \Piwik\Settings\Measurable\MeasurableSetting
     */
    public $weatherWindSpeed;

    /**
     * @var \Piwik\Settings\Measurable\MeasurableSetting
     */

    protected function init()
    {
        //$this->weatherLang = $this->makeWeatherLangSetting();
        $this->weatherTemperatureUnit = $this->makeWeatherTemperatureUnitSetting();
        $this->weatherPrecipitationUnit = $this->makeWeatherPrecipitationUnitSetting();
        $this->weatherPressureUnit = $this->makeWeatherPressureUnitSetting();
        $this->weatherVisibilityUnit = $this->makeWeatherVisibilityUnitSetting();
        $this->weatherWindSpeed = $this->makeWeatherWindSpeedUnitSetting();
    }

    /*
    private function makeWeatherLangSetting()
    {
        return $this->makeSetting('weatherLang', 'en', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('WeatherReports_LanguageTitle');
            $field->description = Piwik::translate('WeatherReports_LanguageDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array(
                'ar' => 'Arabic',
                'bn' => 'Bengali',
                'bg' => 'Bulgarian',
                'zh' => 'Chinese Simplified',
                'zh_tw' => 'Chinese Traditional',
                'cs' => 'Czech',
                'da' => 'Danish',
                'nl' => 'Dutch',
                'fi' => 'Finnish',
                'fr' => 'French',
                'de' => 'German',
                'el' => 'Greek',
                'hi' => 'Hindi',
                'hu' => 'Hungarian',
                'it' => 'Italian',
                'ja' => 'Japanese',
                'jv' => 'Javanese',
                'ko' => 'Korean',
                'zh_cmn' => 'Mandarin',
                'mr' => 'Marathi',
                'pl' => 'Polish',
                'pt' => 'Portuguese',
                'pa' => 'Punjabi',
                'ro' => 'Romanian',
                'ru' => 'Russian',
                'sr' => 'Serbian',
                'si' => 'Sinhalese',
                'sk' => 'Slovak',
                'es' => 'Spanish',
                'sv' => 'Swedish',
                'ta' => 'Tamil',
                'te' => 'Telugu',
                'tr' => 'Turkish',
                'uk' => 'Ukrainian',
                'ur' => 'Urdu',
                'vi' => 'Vietnamese',
                'zh_wuu' => 'Wu (Shanghainese)',
                'zh_hsn' => 'Xiang',
                'zh_yue' => 'Yue (Cantonese)',
                'zu' => 'Zulu',
            );
            $field->validators[] = new NotEmpty();
        });
    }
    */

    public function makeWeatherTemperatureUnitSetting()
    {

        return $this->makeSetting('weatherTemperatureUnit', 'c', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('WeatherReports_TemperatureUnitTitle');
            $field->description = Piwik::translate('WeatherReports_TemperatureUnitDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array(
                'c' => Piwik::translate('WeatherReports_Celsius'),
                'f' => Piwik::translate('WeatherReports_Fahrenheit'),
            );
            $field->validators[] = new NotEmpty();
        });
    }


    public function makeWeatherPrecipitationUnitSetting()
    {
        return $this->makeSetting('weatherPrecipitationUnit', 'mm', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('WeatherReports_PrecipitationUnitTitle');
            $field->description = Piwik::translate('WeatherReports_PrecipitationUnitDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array(
                'mm' => Piwik::translate('WeatherReports_Millimeters'),
                'in' => Piwik::translate('WeatherReports_Inches'),
            );
            $field->validators[] = new NotEmpty();
        });
    }

    public function makeWeatherPressureUnitSetting()
    {
        return $this->makeSetting('weatherPressureUnit', 'mb', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('WeatherReports_PressureUnitTitle');
            $field->description = Piwik::translate('WeatherReports_PressureUnitDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array(
                'mb' => Piwik::translate('WeatherReports_Millibars'),
                'in' => Piwik::translate('WeatherReports_Inches'),
            );
            $field->validators[] = new NotEmpty();
        });
    }

    public function makeWeatherVisibilityUnitSetting()
    {
        return $this->makeSetting('weatherVisibilityUnit', 'km', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('WeatherReports_VisibilityUnitTitle');
            $field->description = Piwik::translate('WeatherReports_VisibilityUnitDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array(
                'km' => Piwik::translate('WeatherReports_Kilometers'),
                'miles' => Piwik::translate('WeatherReports_Miles'),
            );
            $field->validators[] = new NotEmpty();
        });
    }

    public function makeWeatherWindSpeedUnitSetting()
    {
        return $this->makeSetting('weatherWindSpeed', 'kph', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = Piwik::translate('WeatherReports_WindSpeedUnitTitle');
            $field->description = Piwik::translate('WeatherReports_WindSpeedUnitDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->availableValues = array(
                'kph' => Piwik::translate('WeatherReports_KilometersPerHour'),
                'mph' => Piwik::translate('WeatherReports_MilesPerHour'),
            );
            $field->validators[] = new NotEmpty();
        });
    }
}
