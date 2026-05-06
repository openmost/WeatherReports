<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\WindSpeed;

/**
 * @group WeatherReports
 */
class WindSpeedTest extends TestCase
{
    public function testAcceptsValidSpeeds(): void
    {
        $column = new WindSpeed();

        $this->assertSame(0.0, $column->sanitize(0.0));
        $this->assertSame(9.7, $column->sanitize(9.7));    // kph
        $this->assertSame(120.0, $column->sanitize(120.0));// mph
    }

    public function testRejectsNegativeAndExtreme(): void
    {
        $column = new WindSpeed();

        $this->assertNull($column->sanitize(-1));
        $this->assertNull($column->sanitize(1001));
    }
}
