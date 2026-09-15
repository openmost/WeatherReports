<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Settings\Plugin\SystemSetting;

class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var SystemSetting WeatherAPI key used by the getWeather proxy, never sent to the browser */
    public $weatherApiKey;

    protected function init()
    {
        $this->weatherApiKey = $this->makeSetting('weatherApiKey', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('WeatherReports_ApiKeySettingTitle');
            $field->description = Piwik::translate('WeatherReports_ApiKeySettingDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_PASSWORD;
            $field->transform = static function ($value) {
                return trim((string) $value);
            };
        });
    }
}
