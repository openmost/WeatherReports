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
