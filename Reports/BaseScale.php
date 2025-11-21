<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\WeatherReports\Reports;

use Piwik\Plugin\ViewDataTable;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Bar;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Evolution;
use Piwik\Plugins\CoreVisualizations\Visualizations\Graph;

/**
 * Base class for scale-based reports (temperature, pressure, humidity, etc.)
 * These reports display as bar charts by default with logical ordering on the x-axis.
 */
abstract class BaseScale extends Base
{
    /**
     * Disable default sorting by metric, allow sorting by label
     * @var string
     */
    protected $defaultSortColumn = '';

    /**
     * Returns Bar chart as the default visualization type
     *
     * @return string
     */
    public function getDefaultTypeViewDataTable()
    {
        return Bar::ID;
    }

    /**
     * Configure view properties for scale-based reports
     *
     * @param ViewDataTable $view
     */
    protected function setBasicConfigViewProperties(ViewDataTable $view)
    {
        // Sort by label in ascending order for logical scale progression
        $view->requestConfig->filter_sort_column = 'label';
        $view->requestConfig->filter_sort_order = 'asc';

        $view->requestConfig->addPropertiesThatShouldBeAvailableClientSide(array('filter_sort_column'));

        // Disable search and pagination for cleaner visualization
        $view->config->show_search = false;
        $view->config->show_exclude_low_population = false;
        $view->config->show_offset_information = false;
        $view->config->show_pagination_control = false;

        if (!$view->isViewDataTableId(Evolution::ID)) {
            $view->config->show_limit_control = false;
        }
    }

    /**
     * Configure view for scale-based reports
     *
     * @param ViewDataTable $view
     */
    public function configureView(ViewDataTable $view)
    {
        $this->setBasicConfigViewProperties($view);

        if (!empty($this->dimension)) {
            $view->config->addTranslations(array('label' => $this->dimension->getName()));
        }

        // For graph visualizations, show all data points
        if ($view->isViewDataTableId(Graph::ID)) {
            $view->config->max_graph_elements = false;
        }

        // Hide undefined values (labeled as "-") in chart/graph modes only
        // In table mode, users can still see all data including undefined values
        if ($view->isViewDataTableId(Bar::ID) || $view->isViewDataTableId(Graph::ID)) {
            $view->config->filters[] = function ($dataTable) {
                $dataTable->filter('Pattern', array('label', '^-$', true)); // true = inverted (exclude)
            };
        }
    }
}
