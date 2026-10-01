<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Settings\SingleValue;
use Piwik\Plugins\WeatherReports\Settings\SiteUnitsStorage;

/**
 * @group WeatherReports
 */
class SiteUnitsStorageTest extends TestCase
{
    public function testReadsUnitsSavedByPreviousVersions(): void
    {
        $this->assertSame([
            'temperature' => 'f',
            'precipitation' => 'in',
            'pressure' => 'mb',
            'visibility' => 'miles',
            'wind' => 'mph',
        ], SiteUnitsStorage::fromStoredValues([
            // the website form saved arrays, the Weather page saves strings
            'weatherTemperatureUnit' => 'f',
            'weatherPrecipitationUnit' => ['in'],
            'weatherPressureUnit' => ['mb'],
            'weatherVisibilityUnit' => 'miles',
            'weatherWindSpeed' => ['mph'],
        ]));
    }

    public function testIgnoresMissingEmptyAndOtherSettings(): void
    {
        $this->assertSame(['temperature' => 'c'], SiteUnitsStorage::fromStoredValues([
            'weatherTemperatureUnit' => 'c',
            'weatherPrecipitationUnit' => '',
            'weatherPressureUnit' => [],
            'otherSetting' => 'value',
        ]));
        $this->assertSame([], SiteUnitsStorage::fromStoredValues([]));
    }

    public function testUnknownOrMissingUnitsFallBackToTheDefaults(): void
    {
        $this->assertSame([
            'temperature' => 'f',
            'precipitation' => 'mm',
            'pressure' => 'mb',
            'visibility' => 'km',
            'wind' => 'kph',
        ], SiteUnitsStorage::getUnitCodes(['temperature' => 'f', 'precipitation' => 'furlongs']));
    }

    public function testKnownUnits(): void
    {
        $this->assertTrue(SiteUnitsStorage::isKnownUnit('pressure', 'in'));
        $this->assertFalse(SiteUnitsStorage::isKnownUnit('pressure', 'miles'));
        $this->assertFalse(SiteUnitsStorage::isKnownUnit('unknown', 'c'));
    }

    public function testUnwrap(): void
    {
        $this->assertSame('fr', SingleValue::unwrap(['fr']));
        $this->assertSame('fr', SingleValue::unwrap('fr'));
        $this->assertNull(SingleValue::unwrap([]));
        $this->assertNull(SingleValue::unwrap(null));
    }
}
