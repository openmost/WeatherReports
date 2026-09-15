/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

export type WeatherValue = string | number;

export interface WeatherValues {
  weather_condition?: WeatherValue;
  weather_cloud?: WeatherValue;
  weather_precipitation?: WeatherValue;
  weather_felt_temperature?: WeatherValue;
  weather_humidity?: WeatherValue;
  weather_pressure?: WeatherValue;
  weather_temperature?: WeatherValue;
  weather_uv?: WeatherValue;
  weather_visibility?: WeatherValue;
  weather_wind_direction?: WeatherValue;
  weather_wind_speed?: WeatherValue;
}

export interface WeatherUnits {
  temperature: string;
  precipitation: string;
  pressure: string;
  visibility: string;
  wind: string;
}

export type NumberFormat = (value: number, maxFractionDigits: number) => string;

const COMPASS = [
  'N', 'NNE', 'NE', 'ENE',
  'E', 'ESE', 'SE', 'SSE',
  'S', 'SSW', 'SW', 'WSW',
  'W', 'WNW', 'NW', 'NNW',
];

export function hasValue(value?: WeatherValue | null): value is WeatherValue {
  return value !== undefined && value !== null && value !== '';
}

/**
 * Formats a measure with its unit symbol. Symbols glued to the number (°C, %) get no space.
 * FLOAT columns may come back as 13.199999809, maxFractionDigits rounds them for display.
 */
export function formatMeasure(
  value: WeatherValue | undefined,
  unit: string,
  maxFractionDigits: number,
  formatNumber: NumberFormat,
): string {
  if (!hasValue(value)) {
    return '';
  }

  const number = Number(value);
  const formatted = Number.isFinite(number)
    ? formatNumber(number, maxFractionDigits)
    : String(value);

  if (!unit) {
    return formatted;
  }

  return unit.startsWith('°') || unit === '%' ? `${formatted}${unit}` : `${formatted} ${unit}`;
}

/**
 * Rotation in degrees of an arrow pointing up, so it shows where the wind blows to.
 * The compass direction tells where the wind comes from. Null when the direction is unknown.
 */
export function windArrowRotation(direction?: WeatherValue): number | null {
  if (!hasValue(direction)) {
    return null;
  }

  const index = COMPASS.indexOf(String(direction).trim().toUpperCase());
  if (index < 0) {
    return null;
  }

  return (index * 22.5 + 180) % 360;
}
