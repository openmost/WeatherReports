<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

class WeatherReports extends \Piwik\Plugin
{
    public function registerEvents()
    {
        return [
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
        ];
    }

    public function getStylesheetFiles(&$files)
    {
        $files[] = 'plugins/WeatherReports/vue/src/VisitorWeather/VisitorWeather.less';
    }

    public function getClientSideTranslationKeys(&$translationKeys)
    {
        $translationKeys[] = 'WeatherReports_Weather';
        $translationKeys[] = 'WeatherReports_FeelsLike';
        $translationKeys[] = 'WeatherReports_Cloud';
        $translationKeys[] = 'WeatherReports_Humidity';
        $translationKeys[] = 'WeatherReports_Precipitation';
        $translationKeys[] = 'WeatherReports_Pressure';
        $translationKeys[] = 'WeatherReports_Uv';
        $translationKeys[] = 'WeatherReports_Visibility';
        $translationKeys[] = 'WeatherReports_Wind';
    }
}
