<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\WeatherReports\WeatherApi;

/**
 * @group WeatherReports
 */
class WeatherApiTest extends TestCase
{
    private const RESPONSE = '{"location":{"name":"Paris"},"current":{"temp_c":13.2,"temp_f":55.8,"is_day":1,'
        . '"condition":{"text":"Overcast","icon":"//cdn/113.png","code":1009},"wind_kph":9.7,"wind_dir":"NNW",'
        . '"pressure_mb":1011,"precip_mm":0.08,"humidity":67,"cloud":100,"feelslike_c":12.5,"vis_km":10,"uv":0,'
        . '"gust_kph":20}}';

    /** @var string[] */
    private array $requestedUrls = [];

    private object $cache;

    protected function setUp(): void
    {
        $this->requestedUrls = [];
        $this->cache = new class {
            public array $entries = [];

            public function fetch($id)
            {
                return $this->entries[$id][0] ?? false;
            }

            public function save($id, $data, $lifeTime = 0)
            {
                $this->entries[$id] = [$data, $lifeTime];
                return true;
            }
        };
    }

    public function testReturnsOnlyTheNeededFields(): void
    {
        $current = $this->makeApi(self::RESPONSE)->getCurrentWeather('key', '48.9,2.3', 'fr');

        $this->assertSame(13.2, $current['temp_c']);
        $this->assertSame('NNW', $current['wind_dir']);
        $this->assertSame(['text' => 'Overcast', 'code' => 1009], $current['condition']);
        $this->assertArrayNotHasKey('gust_kph', $current);

        $this->assertCount(1, $this->requestedUrls);
        $this->assertSame(
            'https://api.weatherapi.com/v1/current.json?key=key&q=48.9%2C2.3&aqi=no&lang=fr',
            $this->requestedUrls[0]
        );
    }

    public function testResponsesAreCachedPerLocationAndLanguage(): void
    {
        $api = $this->makeApi(self::RESPONSE);

        $api->getCurrentWeather('key', '48.9,2.3', 'fr');
        $api->getCurrentWeather('key', '48.9,2.3', 'fr');
        $this->assertCount(1, $this->requestedUrls);

        $api->getCurrentWeather('key', '48.9,2.3', 'en');
        $api->getCurrentWeather('key', '45.8,4.8', 'fr');
        $this->assertCount(3, $this->requestedUrls);

        $lifeTimes = array_column($this->cache->entries, 1);
        $this->assertSame([WeatherApi::CACHE_TTL], array_values(array_unique($lifeTimes)));
    }

    public function testErrorsAreCachedForAShorterTime(): void
    {
        $api = $this->makeApi('{"error":{"code":2006,"message":"API key is invalid."}}');

        $this->assertNull($api->getCurrentWeather('bad', '48.9,2.3', 'en'));
        $this->assertNull($api->getCurrentWeather('bad', '48.9,2.3', 'en'));

        $this->assertCount(1, $this->requestedUrls);
        $this->assertSame(WeatherApi::ERROR_CACHE_TTL, array_values($this->cache->entries)[0][1]);
    }

    public function testNetworkFailureReturnsNull(): void
    {
        $api = new WeatherApi(static function (): string {
            throw new \Exception('timeout');
        }, $this->cache);

        $this->assertNull($api->getCurrentWeather('key', '48.9,2.3', 'en'));
    }

    public function testLocationQueryUsesRoundedCoordinates(): void
    {
        $this->assertSame('48.9,2.4', WeatherApi::buildLocationQuery('203.0.113.57', ['lat' => 48.8566, 'long' => 2.3522]));
        $this->assertSame('-33.9,151.2', WeatherApi::buildLocationQuery('203.0.113.57', ['lat' => '-33.8688', 'long' => '151.2093']));
    }

    public function testLocationQueryFallsBackToAnonymizedIp(): void
    {
        $this->assertSame('203.0.113.0', WeatherApi::buildLocationQuery('203.0.113.57', null));
        $this->assertSame('203.0.113.0', WeatherApi::buildLocationQuery('203.0.113.57', ['country_code' => 'fr']));
    }

    private function makeApi(string $response): WeatherApi
    {
        return new WeatherApi(function (string $url) use ($response): string {
            $this->requestedUrls[] = $url;
            return $response;
        }, $this->cache);
    }
}
