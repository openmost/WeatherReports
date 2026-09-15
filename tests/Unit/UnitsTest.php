<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Units;

/**
 * @group WeatherReports
 */
class UnitsTest extends TestCase
{
    public function testDefaultsToMetricUnits(): void
    {
        $this->assertSame([
            'temperature' => '°C',
            'precipitation' => 'mm',
            'pressure' => 'mb',
            'visibility' => 'km',
            'wind' => 'km/h',
        ], Units::getSymbols([]));
    }

    public function testImperialUnits(): void
    {
        $this->assertSame([
            'temperature' => '°F',
            'precipitation' => 'in',
            'pressure' => 'inHg',
            'visibility' => 'mi',
            'wind' => 'mph',
        ], Units::getSymbols([
            'temperature' => 'f',
            'precipitation' => 'in',
            'pressure' => 'in',
            'visibility' => 'miles',
            'wind' => 'mph',
        ]));
    }

    public function testUnknownUnitFallsBackToDefault(): void
    {
        $this->assertSame('°C', Units::getSymbols(['temperature' => 'kelvin'])['temperature']);
    }

    public function testPercentDoesNotNeedSettings(): void
    {
        $this->assertSame('%', Units::getSymbolForSite(0, Units::PERCENT));
    }
}
