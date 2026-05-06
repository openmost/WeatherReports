<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Visibility;

/**
 * @group WeatherReports
 */
class VisibilityTest extends TestCase
{
    public function testAcceptsValidDistances(): void
    {
        $column = new Visibility();

        $this->assertSame(0.0, $column->sanitize(0.0));
        $this->assertSame(10.0, $column->sanitize(10.0));   // km
        $this->assertSame(6.2, $column->sanitize(6.2));     // miles
    }

    public function testRejectsNegativeAndExtreme(): void
    {
        $column = new Visibility();

        $this->assertNull($column->sanitize(-1));
        $this->assertNull($column->sanitize(1001));
    }
}
