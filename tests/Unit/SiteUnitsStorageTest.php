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
            // 6.1.x stored strings, older versions arrays
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

    public function testUnwrap(): void
    {
        $this->assertSame('fr', SingleValue::unwrap(['fr']));
        $this->assertSame('fr', SingleValue::unwrap('fr'));
        $this->assertNull(SingleValue::unwrap([]));
        $this->assertNull(SingleValue::unwrap(null));
    }
}
