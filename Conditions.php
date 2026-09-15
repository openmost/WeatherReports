<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\WeatherReports;

use Piwik\Container\StaticContainer;
use Piwik\Translation\Translator;

/**
 * Translates the weather condition texts sent by WeatherAPI.
 *
 * The tracker stores the condition text in the language chosen in the tag. Texts are matched against the
 * official WeatherAPI list (every code in every language, day and night) to find their condition, then
 * displayed in the language of the Matomo user. Works on already tracked data, no schema change needed.
 */
final class Conditions
{
    /**
     * Texts WeatherAPI used to return before renaming them, lower case.
     */
    private const LEGACY_ALIASES = [
        'patchy rain possible' => '1063',
        'patchy snow possible' => '1066',
        'patchy sleet possible' => '1069',
        'patchy freezing drizzle possible' => '1072',
        'thundery outbreaks possible' => '1087',
    ];

    private const NIGHT_SUFFIX = '-night';

    /** @var array<int, array<string, string|string[]>>|null */
    private static ?array $texts = null;

    /** @var array<string, string>|null normalized text => condition key */
    private static ?array $keysByText = null;

    /**
     * Condition key of a text, e.g. "1000" for "Sunny" or "Ensoleillé", "1000-night" for "Clear".
     * When a text is used by several conditions (some translations are), the lowest code wins.
     */
    public static function findKey(string $text): ?string
    {
        $normalized = self::normalize($text);
        if ($normalized === '') {
            return null;
        }

        return self::getKeysByText()[$normalized] ?? null;
    }

    /**
     * Text of a condition key in a Matomo language ("fr", "pt-br", "zh-cn", ...), English when not translated.
     */
    public static function translate(string $key, string $language): ?string
    {
        $isNight = str_ends_with($key, self::NIGHT_SUFFIX);
        $code = (int) ($isNight ? substr($key, 0, -strlen(self::NIGHT_SUFFIX)) : $key);

        $textsByLanguage = self::getTexts()[$code] ?? null;
        if ($textsByLanguage === null) {
            return null;
        }

        foreach (self::getLanguageCandidates($language) as $candidate) {
            if (isset($textsByLanguage[$candidate])) {
                $texts = $textsByLanguage[$candidate];
                if (is_array($texts)) {
                    return $isNight ? $texts[1] : $texts[0];
                }
                return $texts;
            }
        }

        return null;
    }

    /**
     * Text translated in the given language, the text itself when it is not a known WeatherAPI condition.
     */
    public static function translateText(string $text, string $language): string
    {
        $key = self::findKey($text);
        if ($key === null) {
            return $text;
        }

        return self::translate($key, $language) ?? $text;
    }

    public static function getCurrentLanguage(): string
    {
        try {
            $language = StaticContainer::get(Translator::class)->getCurrentLanguage();
        } catch (\Throwable $e) {
            return 'en';
        }

        return is_string($language) && $language !== '' ? $language : 'en';
    }

    /**
     * @return string[] WeatherAPI language codes to try, e.g. "pt-br" => ["pt_br", "pt", "en"]
     */
    private static function getLanguageCandidates(string $language): array
    {
        $language = strtolower(str_replace('-', '_', trim($language)));
        $base = explode('_', $language)[0];

        return array_values(array_unique(array_filter([$language, $base, 'en'])));
    }

    private static function normalize(string $text): string
    {
        // tracked values are stored HTML encoded
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = (string) preg_replace('/\s+/u', ' ', trim($text));

        return mb_strtolower($text, 'UTF-8');
    }

    /**
     * @return array<int, array<string, string|string[]>>
     */
    private static function getTexts(): array
    {
        if (self::$texts === null) {
            self::$texts = require __DIR__ . '/data/conditions.php';
        }

        return self::$texts;
    }

    /**
     * @return array<string, string>
     */
    private static function getKeysByText(): array
    {
        if (self::$keysByText !== null) {
            return self::$keysByText;
        }

        $keysByText = [];
        $add = static function (string $text, string $key) use (&$keysByText): void {
            $normalized = self::normalize($text);
            if ($normalized !== '' && !isset($keysByText[$normalized])) {
                $keysByText[$normalized] = $key;
            }
        };

        // English first so an English text is never shadowed by the same word in another language
        foreach ([true, false] as $englishPass) {
            foreach (self::getTexts() as $code => $textsByLanguage) {
                foreach ($textsByLanguage as $language => $texts) {
                    if (($language === 'en') !== $englishPass) {
                        continue;
                    }

                    $texts = (array) $texts;
                    $add($texts[0], (string) $code);
                    if (isset($texts[1]) && self::normalize($texts[1]) !== self::normalize($texts[0])) {
                        $add($texts[1], $code . self::NIGHT_SUFFIX);
                    }
                }
            }
        }

        foreach (self::LEGACY_ALIASES as $text => $key) {
            $add($text, $key);
        }

        return self::$keysByText = $keysByText;
    }
}
