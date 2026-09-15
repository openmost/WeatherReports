<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Template\Tag;

use Piwik\Piwik;
use Piwik\Plugins\TagManager\Template\Tag\BaseTag;
use Piwik\Plugins\WeatherReports\Settings\SingleValue;
use Piwik\Settings\FieldConfig;
use Piwik\SettingsPiwik;
use Piwik\Validators\NotEmpty;

/**
 * Sends the visitor weather to Matomo. Values are always sent in metric units with the unit system marker,
 * Matomo converts them to the units of the website settings.
 *
 * Tags saved before 6.1.0 may still hold the removed unit parameters: they are ignored when the container is
 * published and dropped the next time the tag is saved.
 */
class WeatherTag extends BaseTag
{
    public const CATEGORY_CUSTOM = 'Openmost';

    private const LANGUAGES = [
        'ar' => 'Arabic',
        'bn' => 'Bengali',
        'bg' => 'Bulgarian',
        'zh' => 'Chinese Simplified',
        'zh_tw' => 'Chinese Traditional',
        'cs' => 'Czech',
        'da' => 'Danish',
        'nl' => 'Dutch',
        'en' => 'English',
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
    ];

    public function getCategory()
    {
        return self::CATEGORY_CUSTOM;
    }

    public function getIcon()
    {
        return 'plugins/WeatherReports/images/icons/image.svg';
    }

    public function getParameters()
    {
        return [
            $this->makeSetting('apiKey', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_ApiKey');
                $field->description = Piwik::translate('WeatherReports_ApiKeyDescription');
                $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            }),

            $this->makeSetting('matomoUrl', (string) SettingsPiwik::getPiwikUrl(), FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_MatomoUrlTitle');
                $field->description = Piwik::translate('WeatherReports_MatomoUrlDescription');
                $field->customFieldComponent = self::FIELD_VARIABLE_COMPONENT;
            }),

            $this->makeSetting('lang', 'en', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = Piwik::translate('WeatherReports_LanguageTitle');
                $field->description = Piwik::translate('WeatherReports_LanguageDescription');
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->availableValues = self::LANGUAGES;
                $field->validators[] = new NotEmpty();
                // tags saved before 6.1.0 hold the language as ["fr"]
                $field->prepare = static function ($value) {
                    return SingleValue::unwrap($value);
                };
            }),
        ];
    }
}
