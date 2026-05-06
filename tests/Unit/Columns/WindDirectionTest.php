<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\WindDirection;

/**
 * @group WeatherReports
 */
class WindDirectionTest extends TestCase
{
    /** @var WindDirection */
    private $column;

    protected function setUp(): void
    {
        $this->column = new WindDirection();
    }

    /**
     * @dataProvider compassPoints
     */
    public function testAccepts16PointCompass(string $point): void
    {
        $this->assertSame($point, $this->column->sanitize($point));
    }

    public function compassPoints(): array
    {
        return array_map(
            static fn ($v) => [$v],
            ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW']
        );
    }

    public function testIsCaseInsensitive(): void
    {
        $this->assertSame('NNW', $this->column->sanitize('nnw'));
        $this->assertSame('SE', $this->column->sanitize(' se '));
        $this->assertSame('W', $this->column->sanitize('w'));
    }

    /**
     * @dataProvider invalidValues
     */
    public function testRejectsInvalidValues($value): void
    {
        $this->assertNull($this->column->sanitize($value));
    }

    public function invalidValues(): array
    {
        return [
            'empty'        => [''],
            'spaces'       => ['   '],
            'unknown'      => ['NORTH'],
            'numeric'      => ['346'],
            'french'       => ['Nord'],
            'long string'  => ['NorthNorthWest'],
            'random text'  => ['XYZ'],
        ];
    }
}
