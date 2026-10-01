<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Conditions;

/**
 * @group WeatherReports
 */
class ConditionsTest extends TestCase
{
    public function testFindsEnglishDayAndNightTexts(): void
    {
        $this->assertSame('1000', Conditions::findKey('Sunny'));
        $this->assertSame('1000-night', Conditions::findKey('Clear'));
        $this->assertSame('1003', Conditions::findKey('Partly Cloudy'));
    }

    public function testMatchIgnoresCaseSpacesAndHtmlEntities(): void
    {
        $this->assertSame('1003', Conditions::findKey('  partly   cloudy '));
        $this->assertSame('1000', Conditions::findKey('Ensoleill&eacute;'));
    }

    public function testFindsTranslatedTexts(): void
    {
        $this->assertSame('1000', Conditions::findKey('Ensoleillé'));
        $this->assertSame('1000-night', Conditions::findKey('Dégagé'));
        $this->assertSame('1000', Conditions::findKey('Sonnig'));
    }

    public function testFindsTextsWeatherApiUsedToReturn(): void
    {
        $this->assertSame('1063', Conditions::findKey('Patchy rain possible'));
        $this->assertSame('1063', Conditions::findKey('Patchy rain nearby'));
    }

    public function testFindsThunderTextsTheApiSendsWithoutInArea(): void
    {
        $this->assertSame('1273', Conditions::findKey('Patchy light rain with thunder'));
        $this->assertSame('1276', Conditions::findKey('Moderate or heavy rain with thunder'));
        $this->assertSame('1279', Conditions::findKey('Patchy light snow with thunder'));
        $this->assertSame('1282', Conditions::findKey('Moderate or heavy snow with thunder'));
        $this->assertSame('1276', Conditions::findKey('Moderate or heavy rain in area with thunder'));
        $this->assertSame(
            Conditions::translate('1276', 'fr'),
            Conditions::translateText('Moderate or heavy rain with thunder', 'fr')
        );
        $this->assertNotSame(
            'Moderate or heavy rain with thunder',
            Conditions::translateText('Moderate or heavy rain with thunder', 'de')
        );
    }

    /**
     * Two conditions with the same text in a language would be merged in one row of the Condition report.
     */
    public function testEveryConditionHasItsOwnTextInEachPluginLanguage(): void
    {
        $keys = [];
        foreach ([1000, 1003, 1006, 1009, 1012, 1015, 1018, 1021, 1024, 1027, 1030, 1033, 1036, 1039, 1042, 1045, 1048,
            1063, 1066, 1069, 1072, 1087, 1114, 1117, 1135, 1147, 1150, 1153, 1168, 1171, 1180, 1183, 1186, 1189,
            1192, 1195, 1198, 1201, 1204, 1207, 1210, 1213, 1216, 1219, 1222, 1225, 1237, 1240, 1243, 1246, 1249,
            1252, 1255, 1258, 1261, 1264, 1273, 1276, 1279, 1282] as $code) {
            $keys[] = (string) $code;
        }

        $languages = ['en', 'fr', 'de', 'es', 'it', 'nl', 'pt', 'pl', 'ar', 'ja', 'zh-cn', 'zh-tw', 'sv'];
        foreach ($languages as $language) {
            $codesByText = [];
            foreach ($keys as $key) {
                $texts = [Conditions::translate($key, $language), Conditions::translate($key . '-night', $language)];
                foreach (array_unique($texts) as $text) {
                    $this->assertNotNull($text, "$key in $language");
                    $codesByText[mb_strtolower($text)][$key] = true;
                }
            }
            foreach ($codesByText as $text => $codes) {
                $this->assertCount(1, $codes, "$language uses \"$text\" for " . implode(', ', array_keys($codes)));
            }
        }

        $this->assertSame('Leichter Nebel', Conditions::translate('1030', 'de'));
        $this->assertSame('Nebel', Conditions::translate('1135', 'de'));
        $this->assertNotSame(Conditions::translate('1030', 'ja'), Conditions::translate('1135', 'ja'));
        $this->assertNotSame(Conditions::translate('1030', 'zh-cn'), Conditions::translate('1135', 'zh-cn'));
        $this->assertNotSame(Conditions::translate('1030', 'zh-tw'), Conditions::translate('1135', 'zh-tw'));
    }

    public function testUnknownTexts(): void
    {
        $this->assertNull(Conditions::findKey('Raining cats and dogs'));
        $this->assertNull(Conditions::findKey(''));
        $this->assertSame('Raining cats and dogs', Conditions::translateText('Raining cats and dogs', 'fr'));
    }

    public function testTranslatesInMatomoLanguage(): void
    {
        $this->assertSame('Ensoleillé', Conditions::translate('1000', 'fr'));
        $this->assertSame('Klar', Conditions::translate('1000-night', 'de'));
        $this->assertSame('Ensoleillé', Conditions::translateText('Sunny', 'fr'));
        $this->assertSame('Sunny', Conditions::translateText('Soleado', 'en'));
    }

    public function testEveryTranslationMatchesBack(): void
    {
        foreach (['1183', '1195', '1000-night', '1276'] as $key) {
            foreach (['en', 'fr', 'de', 'es', 'it', 'nl', 'sv'] as $language) {
                $text = Conditions::translate($key, $language);
                $this->assertNotNull($text);
                $this->assertSame($key, Conditions::findKey($text), "$key in $language: $text");
            }
        }
    }

    public function testLanguageFallbacks(): void
    {
        $this->assertSame(Conditions::translate('1000', 'pt'), Conditions::translate('1000', 'pt-br'));
        $this->assertSame(Conditions::translate('1000', 'zh_tw'), Conditions::translate('1000', 'zh-tw'));
        $this->assertSame('Sunny', Conditions::translate('1000', 'xx'));
        $this->assertNull(Conditions::translate('9999', 'en'));
    }
}
