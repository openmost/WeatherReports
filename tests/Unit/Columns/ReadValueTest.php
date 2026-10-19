<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit\Columns;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Columns\Base;
use Piwik\Plugins\WeatherReports\Columns\Cloud;
use Piwik\Plugins\WeatherReports\Columns\Condition;
use Piwik\Plugins\WeatherReports\Columns\Temperature;
use Piwik\Plugins\WeatherReports\Columns\WindDirection;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;

/**
 * Values read from the tracking request are parsed and escaped like Common::getRequestVar() did, except "21,5"
 * now reads as 21.5 instead of 21 and an array no longer throws
 *
 * @group WeatherReports
 */
class ReadValueTest extends TestCase
{
    public function testWholeNumbersAreReadInEveryNotation(): void
    {
        foreach (['50' => 50, '50.0' => 50, '050' => 50, '1e1' => 10, '5.' => 5, '-0' => 0] as $sent => $expected) {
            $this->assertSame($expected, $this->read(new Cloud(), 'weather_cloud', $sent), "Sent: $sent");
        }
        $this->assertSame(42, $this->read(new Cloud(), 'weather_cloud', 42));
    }

    public function testInvalidWholeNumbersReadAsZero(): void
    {
        foreach (['5.5', 'abc', '5abc', '<script>'] as $sent) {
            $this->assertSame(0, $this->read(new Cloud(), 'weather_cloud', $sent), "Sent: $sent");
        }
        $this->assertSame(0, $this->read(new Cloud(), 'weather_cloud', ['50']));
    }

    public function testDecimalNumbersAreRead(): void
    {
        foreach (['21.5' => 21.5, '21.50' => 21.5, '21,5' => 21.5, '+1.5' => 1.5, '.5' => 0.5, '1e1' => 10.0, '-12' => -12.0] as $sent => $expected) {
            $this->assertSame($expected, $this->read(new Temperature(), 'weather_temperature', $sent), "Sent: $sent");
        }
        $this->assertSame(12.25, $this->read(new Temperature(), 'weather_temperature', 12.25));
    }

    public function testInvalidDecimalNumbersReadAsZero(): void
    {
        foreach (['abc', ' 21', '21 ', '1_000', '0x1A', '<script>'] as $sent) {
            $this->assertSame(0.0, $this->read(new Temperature(), 'weather_temperature', $sent), "Sent: $sent");
        }
        $this->assertSame(0.0, $this->read(new Temperature(), 'weather_temperature', ['21']));
    }

    public function testTextIsStoredHtmlEscaped(): void
    {
        $this->assertSame(
            '&quot;Rain&quot; &amp; &#039;snow&#039; &lt;b&gt;',
            $this->read(new Condition(), 'weather_condition', '"Rain" & \'snow\' <b>')
        );
        $this->assertSame('&lt;b&gt;', $this->read(new Condition(), 'weather_condition', '&lt;b&gt;'));
        $this->assertSame('Ensoleillé', $this->read(new Condition(), 'weather_condition', 'Ensoleillé'));
        $this->assertNull($this->read(new Condition(), 'weather_condition', ['Sunny']));
    }

    public function testCompassPointIsWhitelisted(): void
    {
        $this->assertSame('NNE', $this->read(new WindDirection(), 'weather_wind_direction', 'nne'));
        $this->assertNull($this->read(new WindDirection(), 'weather_wind_direction', '<script>'));
    }

    public function testMissingOrEmptyParameterIsNotStored(): void
    {
        $this->assertNull($this->read(new Cloud(), 'other_param', '50'));
        $this->assertNull($this->read(new Cloud(), 'weather_cloud', ''));
    }

    /**
     * @param mixed $sent
     * @return mixed
     */
    private function read(Base $column, string $param, $sent)
    {
        $request = $this->createMock(Request::class);
        $request->method('getParams')->willReturn([$param => $sent]);

        $visitor = (new \ReflectionClass(Visitor::class))->newInstanceWithoutConstructor();

        return $column->onNewVisit($request, $visitor, null);
    }
}
