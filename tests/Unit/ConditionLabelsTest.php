<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\DataTable;
use Piwik\Plugins\WeatherReports\ConditionLabels;

// DataTable needs it, defined by the Matomo test bootstrap when running through ./console tests:run
if (!defined('PIWIK_INCLUDE_PATH')) {
    define('PIWIK_INCLUDE_PATH', realpath(__DIR__ . '/../../../..'));
}

/**
 * @group WeatherReports
 */
class ConditionLabelsTest extends TestCase
{
    public function testMergesTheSameConditionTrackedInSeveralLanguages(): void
    {
        $table = new DataTable();
        $table->addRowsFromSimpleArray([
            ['label' => 'Sunny', 'nb_visits' => 2],
            ['label' => 'Ensoleillé', 'nb_visits' => 3],
            ['label' => 'Clear', 'nb_visits' => 1],
            ['label' => '-', 'nb_visits' => 4],
            ['label' => 'Pluie &amp; brouillard', 'nb_visits' => 5],
        ]);

        ConditionLabels::translateTable($table, 'fr');

        $rows = [];
        foreach ($table->getRows() as $row) {
            $rows[$row->getColumn('label')] = [$row->getColumn('nb_visits'), $row->getMetadata('segment')];
        }

        $this->assertSame([
            'Ensoleillé' => [5, 'weatherCondition==Sunny,weatherCondition==Ensoleill%C3%A9'],
            'Dégagé' => [1, 'weatherCondition==Clear'],
            '-' => [4, false],
            'Pluie &amp; brouillard' => [5, 'weatherCondition==Pluie+%26amp%3B+brouillard'],
        ], $rows);
    }

    public function testLabelsFollowTheUserLanguage(): void
    {
        $table = new DataTable();
        $table->addRowsFromSimpleArray([
            ['label' => 'Ensoleillé', 'nb_visits' => 3],
        ]);

        ConditionLabels::translateTable($table, 'en');

        $this->assertSame('Sunny', $table->getFirstRow()->getColumn('label'));
    }
}
