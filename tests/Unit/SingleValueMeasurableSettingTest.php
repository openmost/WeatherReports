<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\Settings\SingleValueMeasurableSetting;
use Piwik\Settings\Storage\Backend\BackendInterface;
use Piwik\Settings\Storage\Storage;

/**
 * @group WeatherReports
 */
class SingleValueMeasurableSettingTest extends TestCase
{
    public function testReadsValuesSavedBeforeVersion610AsArray(): void
    {
        $this->assertSame('f', $this->makeSetting(['weatherTemperatureUnit' => ['f']])->getValue());
    }

    public function testReadsValuesSavedAsString(): void
    {
        $this->assertSame('f', $this->makeSetting(['weatherTemperatureUnit' => 'f'])->getValue());
    }

    public function testFallsBackToTheDefault(): void
    {
        $this->assertSame('c', $this->makeSetting([])->getValue());
        $this->assertSame('c', $this->makeSetting(['weatherTemperatureUnit' => []])->getValue());
        $this->assertSame('c', $this->makeSetting(['weatherTemperatureUnit' => ''])->getValue());
    }

    public function testReadingTwiceKeepsTheValue(): void
    {
        $setting = $this->makeSetting(['weatherTemperatureUnit' => 'f']);

        $this->assertSame('f', $setting->getValue());
        $this->assertSame('f', $setting->getValue());
    }

    public function testUnwrap(): void
    {
        $this->assertSame('fr', SingleValueMeasurableSetting::unwrap(['fr']));
        $this->assertSame('fr', SingleValueMeasurableSetting::unwrap('fr'));
        $this->assertNull(SingleValueMeasurableSetting::unwrap([]));
    }

    private function makeSetting(array $storedValues): SingleValueMeasurableSetting
    {
        $backend = new class ($storedValues) implements BackendInterface {
            private array $values;

            public function __construct(array $values)
            {
                $this->values = $values;
            }

            public function getStorageId()
            {
                return 'WeatherReportsTest';
            }

            public function delete()
            {
            }

            public function load()
            {
                return $this->values;
            }

            public function save($values)
            {
                $this->values = $values;
            }
        };

        // the MeasurableSetting constructor needs the DI container, only the value handling is tested here
        $reflection = new \ReflectionClass(SingleValueMeasurableSetting::class);
        $setting = $reflection->newInstanceWithoutConstructor();
        foreach (['name' => 'weatherTemperatureUnit', 'defaultValue' => 'c', 'type' => 'string'] as $property => $value) {
            $reflection->getProperty($property)->setValue($setting, $value);
        }
        $setting->setStorage(new Storage($backend));

        return $setting;
    }
}
