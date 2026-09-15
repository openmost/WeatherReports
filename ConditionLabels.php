<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\WeatherReports;

use Piwik\Common;
use Piwik\DataTable;

/**
 * Merges the rows of the Condition report that describe the same WeatherAPI condition in different
 * languages, and labels them in the language of the Matomo user.
 */
final class ConditionLabels
{
    public const SEGMENT_NAME = 'weatherCondition';

    private const UNDEFINED_LABEL = '-';

    public static function translateTable(DataTable $table, string $language): void
    {
        // translated label => stored values merged in the row, used to build the row segment
        $storedValuesByLabel = [];

        $table->filter('GroupBy', ['label', static function ($label) use ($language, &$storedValuesByLabel) {
            if (!is_string($label) || $label === '' || $label === self::UNDEFINED_LABEL) {
                return $label;
            }

            $translated = Common::sanitizeInputValue(
                Conditions::translateText(Common::unsanitizeInputValue($label), $language)
            );
            // segments compare with the value as stored by the tracker, HTML encoded like the archived label
            $storedValuesByLabel[$translated][$label] = true;

            return $translated;
        }]);

        foreach ($table->getRowsWithoutSummaryRow() as $row) {
            $label = $row->getColumn('label');
            if (!is_string($label) || empty($storedValuesByLabel[$label])) {
                continue;
            }

            $conditions = [];
            foreach (array_keys($storedValuesByLabel[$label]) as $storedValue) {
                $conditions[] = self::SEGMENT_NAME . '==' . urlencode((string) $storedValue);
            }
            // OR of every tracked text so the segmented visitor log shows all visits of the row
            $row->setMetadata('segment', implode(',', $conditions));
        }
    }
}
