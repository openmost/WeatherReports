<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Cloud;

/**
 * @group WeatherReports
 */
class CloudTest extends TestCase
{
    /** @var Cloud */
    private $column;

    protected function setUp(): void
    {
        $this->column = new Cloud();
    }

    /**
     * @dataProvider validValues
     */
    public function testValidValuesArePreserved($value): void
    {
        $this->assertSame($value, $this->column->sanitize($value));
    }

    public function validValues(): array
    {
        return [
            'min'    => [0],
            'low'    => [25],
            'mid'    => [50],
            'high'   => [99],
            'max'    => [100],
        ];
    }

    /**
     * @dataProvider invalidValues
     */
    public function testInvalidValuesAreRejected($value): void
    {
        $this->assertNull($this->column->sanitize($value));
    }

    public function invalidValues(): array
    {
        return [
            'negative'      => [-1],
            'large'         => [101],
            'very large'    => [1000],
            'far negative'  => [-100],
        ];
    }
}
