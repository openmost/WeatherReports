<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Condition;

/**
 * @group WeatherReports
 */
class ConditionTest extends TestCase
{
    /** @var Condition */
    private $column;

    protected function setUp(): void
    {
        $this->column = new Condition();
    }

    public function testKeepsTextValues(): void
    {
        $this->assertSame('Overcast', $this->column->sanitize('Overcast'));
        $this->assertSame('Partly cloudy', $this->column->sanitize('Partly cloudy'));
        $this->assertSame('Légèrement nuageux', $this->column->sanitize('Légèrement nuageux'));
    }

    public function testTrimsWhitespace(): void
    {
        $this->assertSame('Sunny', $this->column->sanitize('  Sunny  '));
    }

    public function testRejectsEmptyValues(): void
    {
        $this->assertNull($this->column->sanitize(''));
        $this->assertNull($this->column->sanitize('   '));
    }

    public function testTruncatesAt255Chars(): void
    {
        $long = str_repeat('a', 300);
        $sanitized = $this->column->sanitize($long);
        $this->assertSame(255, mb_strlen($sanitized));
    }
}
