<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Template\Tag;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Plugins\TagManager\Template\Tag\BaseTag;
use Piwik\Validators\NotEmpty;

class WeatherTag extends BaseTag
{
    const CATEGORY_CUSTOM = "Openmost";

    public function getName()
    {
        // By default, the name will be automatically fetched from the WeatherReports_CustomHtmlTagName translation key.
        // you can either adjust/create/remove this translation key, or return a different value here directly.
        return parent::getName();
    }

    public function getDescription()
    {
        // By default, the description will be automatically fetched from the WeatherReports_CustomHtmlTagDescription
        // translation key. you can either adjust/create/remove this translation key, or return a different value
        // here directly.
        return parent::getDescription();
    }

    public function getHelp()
    {
        // By default, the help will be automatically fetched from the WeatherReports_CustomHtmlTagHelp translation key.
        // you can either adjust/create/remove this translation key, or return a different value here directly.
        return parent::getHelp();
    }

    public function getCategory()
    {
        return self::CATEGORY_CUSTOM;
    }

    public function getIcon()
    {
        // You may optionally specify a path to an image icon URL, for example:
        //
        // return 'plugins/WeatherReports/images/MyIcon.png';
        //
        // to not return default icon call:
        // return parent::getIcon();
        //
        // The image should have ideally a resolution of about 64x64 pixels.
        return 'plugins/WeatherReports/images/icons/image.svg';
    }

    public function getParameters()
    {
        return array(
            $this->makeSetting('apiKey', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_ApiKey');
                $field->description = Piwik::translate('WeatherReports_ApiKeyDescription');
                $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
                $field->validators[] = new NotEmpty();
            }),

            $this->makeSetting('lang', 'en', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_LanguageTitle');
                $field->description = Piwik::translate('WeatherReports_LanguageDescription');
                $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
                $field->validators[] = new NotEmpty();
            }),

            $this->makeSetting('temperatureUnit', 'c', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_TemperatureUnitTitle');
                $field->description = Piwik::translate('WeatherReports_TemperatureUnitDescription');
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->availableValues = array(
                    'c' => Piwik::translate('WeatherReports_Celsius'),
                    'f' => Piwik::translate('WeatherReports_Fahrenheit'),
                );
                $field->validators[] = new NotEmpty();
            }),

            $this->makeSetting('precipitationUnit', 'mm', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_PrecipitationUnitTitle');
                $field->description = Piwik::translate('WeatherReports_PrecipitationUnitDescription');
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->availableValues = array(
                    'mm' => Piwik::translate('WeatherReports_Millimeters'),
                    'in' => Piwik::translate('WeatherReports_Inches'),
                );
                $field->validators[] = new NotEmpty();
            }),

            $this->makeSetting('pressureUnit', 'mb', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_PressureUnitTitle');
                $field->description = Piwik::translate('WeatherReports_PressureUnitDescription');
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->availableValues = array(
                    'mb' => Piwik::translate('WeatherReports_Millibars'),
                    'in' => Piwik::translate('WeatherReports_Inches'),
                );
                $field->validators[] = new NotEmpty();
            }),

            $this->makeSetting('visibilityUnit', 'km', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_VisibilityUnitTitle');
                $field->description = Piwik::translate('WeatherReports_VisibilityUnitDescription');
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->availableValues = array(
                    'km' => Piwik::translate('WeatherReports_Kilometers'),
                    'miles' => Piwik::translate('WeatherReports_Miles'),
                );
                $field->validators[] = new NotEmpty();
            }),

            $this->makeSetting('windSpeedUnit', 'kph', FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_WindSpeedUnitTitle');
                $field->description = Piwik::translate('WeatherReports_WindSpeedUnitDescription');
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->availableValues = array(
                    'kph' => Piwik::translate('WeatherReports_KilometersPerHour'),
                    'mph' => Piwik::translate('WeatherReports_MilesPerHour'),
                );
                $field->validators[] = new NotEmpty();
            }),
        );
    }

}
