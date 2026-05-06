<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Humidity;

/**
 * @group WeatherReports
 */
class HumidityTest extends TestCase
{
    /** @var Humidity */
    private $column;

    protected function setUp(): void
    {
        $this->column = new Humidity();
    }

    public function testKeepsValuesInRange(): void
    {
        $this->assertSame(0, $this->column->sanitize(0));
        $this->assertSame(67, $this->column->sanitize(67));
        $this->assertSame(100, $this->column->sanitize(100));
    }

    public function testRejectsOutOfRange(): void
    {
        $this->assertNull($this->column->sanitize(-1));
        $this->assertNull($this->column->sanitize(101));
        $this->assertNull($this->column->sanitize(999));
    }
}
