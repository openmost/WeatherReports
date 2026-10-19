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
use Piwik\Tracker\Action;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;

abstract class Base extends VisitDimension
{
    /**
     * Tracking parameter name read from the request (e.g. 'weather_temperature').
     */
    protected $paramName = '';

    /**
     * Type the parameter is read as: 'string', 'int', 'float'.
     */
    protected $paramType = 'string';

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
            // Request::getFloatParameter() would reject a comma as decimal separator, and "21.50" before Matomo 5.10
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
}
