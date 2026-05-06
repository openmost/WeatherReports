<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Precipitation;

/**
 * @group WeatherReports
 */
class PrecipitationTest extends TestCase
{
    public function testAcceptsValidValues(): void
    {
        $column = new Precipitation();

        $this->assertSame(0.0, $column->sanitize(0.0));
        $this->assertSame(0.08, $column->sanitize(0.08));
        $this->assertSame(45.5, $column->sanitize(45.5));
        $this->assertSame(1000.0, $column->sanitize(1000.0));
    }

    public function testRejectsNegativeAndExtremeValues(): void
    {
        $column = new Precipitation();

        $this->assertNull($column->sanitize(-0.1));
        $this->assertNull($column->sanitize(1001));
    }
}
