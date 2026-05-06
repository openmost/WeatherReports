<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\RecordBuilders;

use Piwik\ArchiveProcessor;
use Piwik\ArchiveProcessor\Record;
use Piwik\ArchiveProcessor\RecordBuilder;
use Piwik\Config as PiwikConfig;
use Piwik\DataTable;
use Piwik\Metrics;

abstract class Base extends RecordBuilder
{
    private $recordName;
    private $labelSql;
    private $enrichWithConversionMetrics;

    /**
     * When true the report is sorted numerically by label (scale dimensions).
     * When false labels are kept as-is and rows are sorted by visit count.
     */
    private $isNumericScale;

    public function __construct(
        string $recordName,
        string $labelSql,
        bool $enrichWithConversionMetrics = false,
        bool $isNumericScale = false
    ) {
        parent::__construct();

        $this->recordName = $recordName;
        $this->labelSql = $labelSql;
        $this->enrichWithConversionMetrics = $enrichWithConversionMetrics;
        $this->isNumericScale = $isNumericScale;

        $this->maxRowsInTable = PiwikConfig::getInstance()->General['datatable_archiving_maximum_rows_standard'];
        $this->maxRowsInSubtable = $this->maxRowsInTable;
        $this->columnToSortByBeforeTruncation = Metrics::INDEX_NB_VISITS;
    }

    public function getRecordMetadata(ArchiveProcessor $archiveProcessor): array
    {
        return [
            Record::make(Record::TYPE_BLOB, $this->recordName),
        ];
    }

    protected function aggregate(ArchiveProcessor $archiveProcessor): array
    {
        $logAggregator = $archiveProcessor->getLogAggregator();

        $report = new DataTable();

        $query = $logAggregator->queryVisitsByDimension(['label' => $this->labelSql]);
        while ($row = $query->fetch()) {
            $columns = [
                Metrics::INDEX_NB_UNIQ_VISITORS    => $row[Metrics::INDEX_NB_UNIQ_VISITORS],
                Metrics::INDEX_NB_VISITS           => $row[Metrics::INDEX_NB_VISITS],
                Metrics::INDEX_NB_ACTIONS          => $row[Metrics::INDEX_NB_ACTIONS],
                Metrics::INDEX_NB_USERS            => $row[Metrics::INDEX_NB_USERS],
                Metrics::INDEX_MAX_ACTIONS         => $row[Metrics::INDEX_MAX_ACTIONS],
                Metrics::INDEX_SUM_VISIT_LENGTH    => $row[Metrics::INDEX_SUM_VISIT_LENGTH],
                Metrics::INDEX_BOUNCE_COUNT        => $row[Metrics::INDEX_BOUNCE_COUNT],
                Metrics::INDEX_NB_VISITS_CONVERTED => $row[Metrics::INDEX_NB_VISITS_CONVERTED],
            ];

            $report->sumRowWithLabel($this->normalizeLabel($row['label'] ?? ''), $columns);
        }

        if ($this->enrichWithConversionMetrics) {
            // Join conversions to visits to read the weather column from log_visit
            $extraFrom = [
                [
                    'table'      => 'log_visit',
                    'tableAlias' => 'log_visit',
                    'joinOn'     => 'log_conversion.idvisit = log_visit.idvisit',
                ],
            ];

            $query = $logAggregator->queryConversionsByDimension(
                ['label' => $this->labelSql],
                false,
                [],
                $extraFrom
            );

            while ($conversionRow = $query->fetch()) {
                $idGoal = (int) $conversionRow['idgoal'];
                $columns = [
                    Metrics::INDEX_GOALS => [
                        $idGoal => Metrics::makeGoalColumnsRow($idGoal, $conversionRow),
                    ],
                ];

                $report->sumRowWithLabel($this->normalizeLabel($conversionRow['label'] ?? ''), $columns);
            }

            $report->filter(DataTable\Filter\EnrichRecordWithGoalMetricSums::class);
        }

        if ($this->isNumericScale) {
            $this->sortNumerically($report);
        }

        return [$this->recordName => $report];
    }

    private function normalizeLabel($label): string
    {
        if ($label === null || $label === '') {
            return '-';
        }
        return (string) $label;
    }

    /**
     * Sort scale dimensions (temperature, pressure, ...) by numeric value
     * so the chart x-axis shows 1, 2, 10, 20 rather than 1, 10, 2, 20.
     * Undefined values ("-") sink to the end.
     */
    private function sortNumerically(DataTable $report): void
    {
        $report->filter('ColumnCallbackAddColumn', [['label'], '_sort_key', function ($label) {
            if ($label === '-') {
                return PHP_FLOAT_MAX;
            }
            return (float) $label;
        }]);
        $report->filter('Sort', ['_sort_key', 'asc']);
        $report->filter('ColumnDelete', ['_sort_key']);
    }
}
