<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Temperature;

/**
 * @group WeatherReports
 */
class TemperatureTest extends TestCase
{
    /** @var Temperature */
    private $column;

    protected function setUp(): void
    {
        $this->column = new Temperature();
    }

    public function testCelsiusValuesAreAccepted(): void
    {
        $this->assertSame(-40.0, $this->column->sanitize(-40.0));
        $this->assertSame(13.2, $this->column->sanitize(13.2));
        $this->assertSame(50.0, $this->column->sanitize(50.0));
    }

    public function testFahrenheitValuesAreAccepted(): void
    {
        // Range -100..200 covers Fahrenheit too
        $this->assertSame(-40.0, $this->column->sanitize(-40.0));
        $this->assertSame(120.0, $this->column->sanitize(120.0));
        $this->assertSame(199.0, $this->column->sanitize(199.0));
    }

    public function testRejectsExtremeValues(): void
    {
        $this->assertNull($this->column->sanitize(-150));
        $this->assertNull($this->column->sanitize(250));
    }
}
