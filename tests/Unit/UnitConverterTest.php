<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\UnitConverter;
use Piwik\Plugins\WeatherReports\Units;

/**
 * @group WeatherReports
 */
class UnitConverterTest extends TestCase
{
    private const IMPERIAL_SITE = [
        'temperature' => 'f',
        'precipitation' => 'in',
        'pressure' => 'in',
        'visibility' => 'miles',
        'wind' => 'mph',
    ];

    public function testMetricValuesToImperialSite(): void
    {
        $this->assertSame(68.0, UnitConverter::convertFromSystem(20.0, Units::TEMPERATURE, 'metric', self::IMPERIAL_SITE));
        $this->assertSame(-40.0, UnitConverter::convertFromSystem(-40.0, Units::TEMPERATURE, 'metric', self::IMPERIAL_SITE));
        $this->assertSame(0.08, UnitConverter::convertFromSystem(2.0, Units::PRECIPITATION, 'metric', self::IMPERIAL_SITE));
        $this->assertSame(29.92, UnitConverter::convertFromSystem(1013.25, Units::PRESSURE, 'metric', self::IMPERIAL_SITE));
        $this->assertSame(6.2, UnitConverter::convertFromSystem(10.0, Units::VISIBILITY, 'metric', self::IMPERIAL_SITE));
        $this->assertSame(10.0, UnitConverter::convertFromSystem(16.09, Units::WIND, 'metric', self::IMPERIAL_SITE));
    }

    public function testImperialValuesToMetricSite(): void
    {
        $site = Units::DEFAULT_UNITS;

        $this->assertSame(20.0, UnitConverter::convertFromSystem(68.0, Units::TEMPERATURE, 'imperial', $site));
        $this->assertSame(1013.21, UnitConverter::convertFromSystem(29.92, Units::PRESSURE, 'imperial', $site));
        $this->assertSame(16.1, UnitConverter::convertFromSystem(10.0, Units::WIND, 'imperial', $site));
    }

    public function testSameUnitKeepsTheValue(): void
    {
        $this->assertSame(13.25, UnitConverter::convertFromSystem(13.25, Units::TEMPERATURE, 'metric', Units::DEFAULT_UNITS));
        $this->assertSame(55.8, UnitConverter::convertFromSystem(55.8, Units::TEMPERATURE, 'imperial', self::IMPERIAL_SITE));
    }

    public function testUnknownSystemsAndQuantities(): void
    {
        $this->assertTrue(UnitConverter::isKnownSystem('metric'));
        $this->assertTrue(UnitConverter::isKnownSystem('imperial'));
        $this->assertFalse(UnitConverter::isKnownSystem('kelvin'));
        $this->assertFalse(UnitConverter::isKnownSystem(['metric']));
        $this->assertSame(42.0, UnitConverter::convertFromSystem(42.0, 'humidity', 'metric', self::IMPERIAL_SITE));
    }
}
