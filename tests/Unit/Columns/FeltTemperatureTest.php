<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\FeltTemperature;

/**
 * @group WeatherReports
 */
class FeltTemperatureTest extends TestCase
{
    public function testRangeBoundary(): void
    {
        $column = new FeltTemperature();

        $this->assertSame(-100.0, $column->sanitize(-100.0));
        $this->assertSame(0.0, $column->sanitize(0.0));
        $this->assertSame(100.0, $column->sanitize(100.0));

        $this->assertNull($column->sanitize(-101));
        $this->assertNull($column->sanitize(101));
    }
}
