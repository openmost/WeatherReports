<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Pressure;

/**
 * @group WeatherReports
 */
class PressureTest extends TestCase
{
    public function testKeepsBothMillibarAndInchesValues(): void
    {
        $column = new Pressure();

        // typical mb range
        $this->assertSame(1011.0, $column->sanitize(1011.0));
        // typical inHg range
        $this->assertSame(29.85, $column->sanitize(29.85));
    }

    public function testRejectsNegativeAndExtreme(): void
    {
        $column = new Pressure();

        $this->assertNull($column->sanitize(-1));
        $this->assertNull($column->sanitize(2001));
    }
}
