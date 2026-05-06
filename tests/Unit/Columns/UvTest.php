<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Uv;

/**
 * @group WeatherReports
 */
class UvTest extends TestCase
{
    /** @var Uv */
    private $column;

    protected function setUp(): void
    {
        $this->column = new Uv();
    }

    public function testKeepsValuesInRange(): void
    {
        $this->assertSame(0.0, $this->column->sanitize(0.0));
        $this->assertSame(5.5, $this->column->sanitize(5.5));
        $this->assertSame(20.0, $this->column->sanitize(20.0));
    }

    public function testRejectsOutOfRange(): void
    {
        $this->assertNull($this->column->sanitize(-0.1));
        $this->assertNull($this->column->sanitize(20.1));
        $this->assertNull($this->column->sanitize(50));
    }
}
