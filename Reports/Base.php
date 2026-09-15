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
use Piwik\Plugins\WeatherReports\Units;
use Piwik\Request;

abstract class Base extends Report
{
    /** Show top N rows before rolling the rest into "Others". */
    protected const DEFAULT_FILTER_LIMIT = 15;

    /**
     * Quantity of the report values (one of the Units constants), shown next to the dimension name.
     * Null for dimensions without unit.
     */
    protected ?string $valueUnit = null;

    protected function init()
    {
        $this->categoryId = 'General_Visitors';
        $this->subcategoryId = 'WeatherReports_Weather';
        $this->hasGoalMetrics = true;
    }

    public function configureView(ViewDataTable $view)
    {
        $this->addLabelTranslation($view);

        $view->config->show_search = true;
        $view->requestConfig->filter_limit = self::DEFAULT_FILTER_LIMIT;
        $view->requestConfig->addPropertiesThatShouldBeAvailableClientSide(['filter_limit']);
    }

    public function getRelatedReports()
    {
        return [];
    }

    /**
     * Label column title, e.g. "Temperature (°C)" using the unit of the site settings.
     */
    protected function addLabelTranslation(ViewDataTable $view): void
    {
        if (empty($this->dimension)) {
            return;
        }

        $label = $this->dimension->getName();

        if ($this->valueUnit !== null) {
            $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);
            $symbol = Units::getSymbolForSite($idSite, $this->valueUnit);
            if ($symbol !== '') {
                $label .= ' (' . $symbol . ')';
            }
        }

        $view->config->addTranslations(['label' => $label]);
    }
}
