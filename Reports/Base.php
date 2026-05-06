<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Plugin\Report;
use Piwik\Plugin\ViewDataTable;

abstract class Base extends Report
{
    /** Show top N rows before rolling the rest into "Others". */
    protected const DEFAULT_FILTER_LIMIT = 15;

    protected function init()
    {
        $this->categoryId = 'General_Visitors';
    }

    /**
     * Apply the shared default row limit. Subclasses overriding configureView
     * should call parent::configureView($view) first.
     */
    public function configureView(ViewDataTable $view)
    {
        $view->requestConfig->filter_limit = self::DEFAULT_FILTER_LIMIT;
        $view->requestConfig->addPropertiesThatShouldBeAvailableClientSide(['filter_limit']);
    }
}
