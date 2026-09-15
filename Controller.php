<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Common;
use Piwik\Http\JsonResponse;
use Piwik\IP;
use Piwik\Piwik;
use Piwik\Plugins\UserCountry\LocationProvider;
use Piwik\Plugins\WeatherReports\Settings\SiteUnitsStorage;
use Piwik\Request;
use Piwik\Url;

class Controller extends \Piwik\Plugin\Controller
{
    /**
     * Weather units of a site, in Administration > Websites > Weather.
     */
    public function manage(): string
    {
        $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);
        Piwik::checkUserHasAdminAccess($idSite);

        $apiKeyUrl = '';
        if (Piwik::hasUserSuperUserAccess()) {
            $apiKeyUrl = 'index.php' . Url::getCurrentQueryStringWithParametersModified([
                'module' => 'CoreAdminHome',
                'action' => 'generalSettings',
            ]) . '#/WeatherReports';
        }

        return $this->renderTemplate('manage', [
            'idSite' => $idSite,
            'fields' => Units::getFieldsMetadata(),
            'units' => Units::getUnitCodes(SiteUnitsStorage::read($idSite)),
            'apiKeyUrl' => $apiKeyUrl,
        ]);
    }

    /**
     * Public endpoint that returns the visitor's IP as Matomo resolves it
     * (honouring proxy_client_headers / proxy_host_headers in config.ini.php).
     *
     * URL: /index.php?module=WeatherReports&action=getUserIp
     *
     * Intended as a self-hosted replacement for third-party IP lookups when
     * WeatherAPI's q=auto:ip cannot be used, for example behind a CDN where the
     * client IP needs Matomo's proxy configuration to be resolved correctly.
     */
    #[JsonResponse]
    public function getUserIp(): string
    {
        Common::sendHeader('Cache-Control: no-store');
        // Called from the tracked website, usually another origin. The response only holds the caller's own IP.
        Common::sendHeader('Access-Control-Allow-Origin: *');

        return json_encode(['ip' => IP::getIpFromHeader()], JSON_THROW_ON_ERROR);
    }

    /**
     * Public endpoint returning the current weather of the visitor, fetched by Matomo with the WeatherAPI key of
     * the plugin settings so the key is not exposed in the website code.
     *
     * URL: /index.php?module=WeatherReports&action=getWeather&lang=fr
     * Response: {"current": {...WeatherAPI current fields...}}
     *
     * WeatherAPI receives the coordinates found by Matomo geolocation (rounded to about 10 km) or the visitor IP
     * without its last byte, never the full IP. Responses are cached per location.
     */
    #[JsonResponse]
    public function getWeather(): string
    {
        Common::sendHeader('Access-Control-Allow-Origin: *');

        $apiKey = trim((string) (new SystemSettings())->weatherApiKey->getValue());
        if ($apiKey === '') {
            Common::sendHeader('Cache-Control: no-store');
            Common::sendResponseCode(404);
            return json_encode(['error' => 'WeatherAPI key is not configured in the WeatherReports plugin settings'], JSON_THROW_ON_ERROR);
        }

        $language = strtolower(Request::fromRequest()->getStringParameter('lang', 'en'));
        if (!preg_match('/^[a-z]{2,3}(_[a-z]{2,4})?$/', $language)) {
            $language = 'en';
        }

        $ip = IP::getIpFromHeader();
        $query = WeatherApi::buildLocationQuery($ip, $this->geolocate($ip));
        $current = (new WeatherApi())->getCurrentWeather($apiKey, $query, $language);

        if ($current === null) {
            Common::sendHeader('Cache-Control: no-store');
            Common::sendResponseCode(502);
            return json_encode(['error' => 'Weather is not available'], JSON_THROW_ON_ERROR);
        }

        Common::sendHeader('Cache-Control: private, max-age=900');

        return json_encode(['current' => $current], JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function geolocate(string $ip): ?array
    {
        try {
            $provider = LocationProvider::getCurrentProvider();
            $location = $provider ? $provider->getLocation(['ip' => $ip]) : null;
        } catch (\Throwable $e) {
            return null;
        }

        if (!is_array($location)) {
            return null;
        }

        return [
            'lat' => $location[LocationProvider::LATITUDE_KEY] ?? null,
            'long' => $location[LocationProvider::LONGITUDE_KEY] ?? null,
        ];
    }
}
