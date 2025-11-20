<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
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
    /**
     * @var string
     */
    private $recordName;

    /**
     * @var string
     */
    private $labelSql;

    /**
     * @var bool
     */
    private $enrichWithConversionMetrics;

    public function __construct(string $recordName, string $labelSql, bool $enrichWithConversionMetrics = false)
    {
        parent::__construct();

        $this->recordName = $recordName;
        $this->labelSql = $labelSql;

        $this->maxRowsInTable = PiwikConfig::getInstance()->General['datatable_archiving_maximum_rows_standard'];
        $this->maxRowsInSubtable = $this->maxRowsInTable;
        $this->columnToSortByBeforeTruncation = Metrics::INDEX_NB_VISITS;
        $this->enrichWithConversionMetrics = $enrichWithConversionMetrics;
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
                Metrics::INDEX_NB_UNIQ_VISITORS => $row[Metrics::INDEX_NB_UNIQ_VISITORS],
                Metrics::INDEX_NB_VISITS => $row[Metrics::INDEX_NB_VISITS],
                Metrics::INDEX_NB_ACTIONS => $row[Metrics::INDEX_NB_ACTIONS],
                Metrics::INDEX_NB_USERS => $row[Metrics::INDEX_NB_USERS],
                Metrics::INDEX_MAX_ACTIONS => $row[Metrics::INDEX_MAX_ACTIONS],
                Metrics::INDEX_SUM_VISIT_LENGTH => $row[Metrics::INDEX_SUM_VISIT_LENGTH],
                Metrics::INDEX_BOUNCE_COUNT => $row[Metrics::INDEX_BOUNCE_COUNT],
                Metrics::INDEX_NB_VISITS_CONVERTED => $row[Metrics::INDEX_NB_VISITS_CONVERTED],
            ];

            // Convert empty, null, or 0 labels to "-"
            $label = $row['label'] ?? '';
            if ($label === '' || $label === 0 || $label === '0' || $label === null) {
                $label = '-';
            }

            $report->sumRowWithLabel($label, $columns);
        }

        if ($this->enrichWithConversionMetrics) {
            // Join conversions with visits to get weather data from log_visit
            // since weather columns may not exist in log_conversion or may not have data
            $extraFrom = [
                [
                    'table' => 'log_visit',
                    'tableAlias' => 'log_visit',
                    'joinOn' => 'log_conversion.idvisit = log_visit.idvisit'
                ]
            ];

            $query = $logAggregator->queryConversionsByDimension(
                ['label' => $this->labelSql],
                false,
                [],
                $extraFrom
            );

            while ($conversionRow = $query->fetch()) {
                $label = $conversionRow['label'] ?? '';

                // Convert empty, null, or 0 labels to "-"
                if ($label === '' || $label === 0 || $label === '0' || $label === null) {
                    $label = '-';
                }

                $idGoal = (int) $conversionRow['idgoal'];
                $columns = [
                    Metrics::INDEX_GOALS => [
                        $idGoal => Metrics::makeGoalColumnsRow($idGoal, $conversionRow),
                    ],
                ];

                $report->sumRowWithLabel($label, $columns);
            }

            $report->filter(DataTable\Filter\EnrichRecordWithGoalMetricSums::class);
        }

        // Apply callback sorting for proper numeric ordering
        // This ensures values are sorted as 1, 2, 10, 20 instead of 1, 10, 2, 20
        $report->filter('Sort', function ($row) {
            $label = $row->getColumn('label');
            // If label is "-", put it at the end
            if ($label === '-') {
                return PHP_FLOAT_MAX;
            }
            // Convert to float for numeric sorting
            return (float) $label;
        }, 'asc');

        return [$this->recordName => $report];
    }
}
