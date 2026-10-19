<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Columns;

use Piwik\Common;
use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Plugins\WeatherReports\UnitConverter;
use Piwik\Plugins\WeatherReports\Units;
use Piwik\Plugins\WeatherReports\WeatherReports;
use Piwik\Tracker\Cache as TrackerCache;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;

abstract class Base extends VisitDimension
{
    /**
     * Tracking parameter telling in which unit system the values are sent: 'metric' or 'imperial'.
     * Values sent with it are converted to the units of the site settings. Tracking codes of plugin versions
     * before 6.1.0 don't send it, their values are stored as sent (in the units configured in the tag).
     */
    public const UNIT_SYSTEM_PARAM = 'weather_units';

    /**
     * Tracking parameter name read from the request (e.g. 'weather_temperature').
     */
    protected $paramName = '';

    /**
     * Type the parameter is read as: 'string', 'int', 'float'.
     */
    protected $paramType = 'string';

    /**
     * Quantity of the value (one of the Units constants) when it has a configurable unit.
     */
    protected $unitQuantity = null;

    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        return $this->readValue($request);
    }

    public function onExistingVisit(Request $request, Visitor $visitor, $action)
    {
        $value = $this->readValue($request);
        return $value !== null ? $value : false;
    }

    public function onAnyGoalConversion(Request $request, Visitor $visitor, $action)
    {
        return $visitor->getVisitorColumn($this->columnName);
    }

    /**
     * Read and validate the parameter from the tracking request.
     * Returns null when not present or invalid; subclasses can override
     * sanitize() to apply bounds.
     */
    protected function readValue(Request $request)
    {
        $params = $request->getParams();
        if (!isset($params[$this->paramName]) || $params[$this->paramName] === '') {
            return null;
        }

        $value = $this->readTypedValue(new \Piwik\Request($params));

        $unitSystem = $params[self::UNIT_SYSTEM_PARAM] ?? null;
        if ($this->unitQuantity !== null && UnitConverter::isKnownSystem($unitSystem)) {
            $value = UnitConverter::convertFromSystem((float) $value, $this->unitQuantity, $unitSystem, $this->getSiteUnits($request));
        }

        return $this->sanitize($value);
    }

    /**
     * Invalid numbers read as 0. Texts are stored HTML-escaped, as Matomo stores tracking parameters: reports,
     * segments and the visitor log decode them on display.
     */
    private function readTypedValue(\Piwik\Request $parameters)
    {
        if ($this->paramType === 'int') {
            // Request::getIntegerParameter() would reject whole numbers written "50.0", "050" or "1e1"
            $value = $parameters->getParameter($this->paramName);

            return is_numeric($value) && (int) $value == $value ? (int) $value : 0;
        }

        if ($this->paramType === 'float') {
            // Request::getFloatParameter() would reject a comma as decimal separator
            $value = $parameters->getParameter($this->paramName);
            if (is_int($value) || is_float($value)) {
                return (float) $value;
            }

            $value = is_string($value) ? str_replace(',', '.', $value) : '';

            return is_numeric($value) && trim($value) === $value ? (float) $value : 0.0;
        }

        return Common::sanitizeInputValues($parameters->getStringParameter($this->paramName, ''));
    }

    /**
     * Override to clamp/validate values per dimension.
     */
    public function sanitize($value)
    {
        return $value;
    }

    /**
     * @return array<string, string>
     */
    private function getSiteUnits(Request $request): array
    {
        $idSite = (int) $request->getIdSite();

        $siteAttributes = TrackerCache::getCacheWebsiteAttributes($idSite);
        if (isset($siteAttributes[WeatherReports::SITE_UNITS_CACHE_KEY]) && is_array($siteAttributes[WeatherReports::SITE_UNITS_CACHE_KEY])) {
            return Units::getUnitCodes($siteAttributes[WeatherReports::SITE_UNITS_CACHE_KEY]);
        }

        // tracker cache generated before the plugin was updated
        return Units::getUnitCodesForSite($idSite);
    }
}
