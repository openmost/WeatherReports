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
    /** Tracker cache site attribute holding the unit codes of the site settings */
    public const SITE_UNITS_CACHE_KEY = 'weather_reports_units';

    public function registerEvents()
    {
        return [
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            'Tracker.Cache.getSiteAttributes' => 'addSiteUnitsToTrackerCache',
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
        $translationKeys[] = 'WeatherReports_UnitsPageTitle';
        $translationKeys[] = 'WeatherReports_UnitsPageIntro';
        $translationKeys[] = 'WeatherReports_UnitsPageApiKey';
        $translationKeys[] = 'WeatherReports_ApiKeySettingTitle';
        $translationKeys[] = 'General_YourChangesHaveBeenSaved';
    }

    /**
     * The tracker converts values to the site units without reading the settings for every request.
     * Saving settings clears the tracker cache.
     */
    public function addSiteUnitsToTrackerCache(&$content, $idSite)
    {
        Units::clearCache();
        $content[self::SITE_UNITS_CACHE_KEY] = Units::getUnitCodesForSite((int) $idSite);
    }
}
