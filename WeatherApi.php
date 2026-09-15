<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\WeatherReports;

use Matomo\Network\IP;
use Piwik\Cache as PiwikCache;
use Piwik\Http;

/**
 * Server side WeatherAPI client used by the getWeather proxy, so the API key never reaches the browser.
 */
class WeatherApi
{
    public const ENDPOINT = 'https://api.weatherapi.com/v1/current.json';

    /** WeatherAPI refreshes current conditions every 15 minutes */
    public const CACHE_TTL = 1800;

    /** Failed lookups (invalid key, quota, network) are not retried for every visitor */
    public const ERROR_CACHE_TTL = 300;

    private const HTTP_TIMEOUT = 5;

    private const CURRENT_FIELDS = [
        'cloud',
        'feelslike_c',
        'feelslike_f',
        'humidity',
        'is_day',
        'precip_in',
        'precip_mm',
        'pressure_in',
        'pressure_mb',
        'temp_c',
        'temp_f',
        'uv',
        'vis_km',
        'vis_miles',
        'wind_dir',
        'wind_kph',
        'wind_mph',
    ];

    /** @var callable(string): string */
    private $httpGet;

    /** @var object with fetch($id) and save($id, $data, $lifeTime) methods, like Matomo\Cache\Lazy */
    private $cache;

    /**
     * @param callable(string): string|null $httpGet
     * @param object|null $cache
     */
    public function __construct(?callable $httpGet = null, $cache = null)
    {
        $this->httpGet = $httpGet ?? static function (string $url): string {
            return (string) Http::sendHttpRequest($url, self::HTTP_TIMEOUT);
        };
        $this->cache = $cache ?? PiwikCache::getLazyCache();
    }

    /**
     * Current weather for a location query, cached per location and language. Null when unavailable.
     *
     * @return array<string, mixed>|null
     */
    public function getCurrentWeather(#[\SensitiveParameter] string $apiKey, string $query, string $language): ?array
    {
        $cacheId = 'WeatherReports_current_' . md5($apiKey . '|' . $query . '|' . $language);

        $cached = $this->cache->fetch($cacheId);
        if (is_array($cached) && array_key_exists('current', $cached)) {
            return $cached['current'];
        }

        try {
            $url = self::ENDPOINT . '?' . http_build_query([
                'key' => $apiKey,
                'q' => $query,
                'aqi' => 'no',
                'lang' => $language,
            ]);
            $current = self::extractCurrent(json_decode(($this->httpGet)($url), true));
        } catch (\Throwable $e) {
            $current = null;
        }

        $this->cache->save($cacheId, ['current' => $current], $current === null ? self::ERROR_CACHE_TTL : self::CACHE_TTL);

        return $current;
    }

    /**
     * WeatherAPI location query. Uses the coordinates found by Matomo geolocation when available, rounded to
     * about 10 km so the visitor IP is not sent and nearby visitors share the cache. Falls back to the IP with
     * its last byte removed.
     *
     * @param array{lat?: mixed, long?: mixed}|null $location
     */
    public static function buildLocationQuery(string $ip, ?array $location): string
    {
        $latitude = $location['lat'] ?? null;
        $longitude = $location['long'] ?? null;

        if (is_numeric($latitude) && is_numeric($longitude)) {
            return sprintf('%.1F,%.1F', (float) $latitude, (float) $longitude);
        }

        return IP::fromStringIP($ip)->anonymize(1)->toString();
    }

    /**
     * Keeps only the fields needed by the tracking code.
     *
     * @param mixed $response decoded WeatherAPI response
     * @return array<string, mixed>|null
     */
    public static function extractCurrent($response): ?array
    {
        if (!is_array($response) || !isset($response['current']) || !is_array($response['current'])) {
            return null;
        }

        $current = [];
        foreach (self::CURRENT_FIELDS as $field) {
            if (isset($response['current'][$field]) && is_scalar($response['current'][$field])) {
                $current[$field] = $response['current'][$field];
            }
        }

        $condition = $response['current']['condition'] ?? null;
        if (is_array($condition)) {
            $current['condition'] = [
                'text' => (string) ($condition['text'] ?? ''),
                'code' => (int) ($condition['code'] ?? 0),
            ];
        }

        return $current;
    }
}
