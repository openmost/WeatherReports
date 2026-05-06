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
     * Type passed to Common::getRequestVar: 'string', 'int', 'float'.
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

        $default = $this->paramType === 'string' ? '' : 0;
        $value = Common::getRequestVar($this->paramName, $default, $this->paramType, $params);

        return $this->sanitize($value);
    }

    /**
     * Override to clamp/validate values per dimension.
     */
    public function sanitize($value)
    {
        return $value;
    }
}
