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

class Controller extends \Piwik\Plugin\Controller
{
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
}
