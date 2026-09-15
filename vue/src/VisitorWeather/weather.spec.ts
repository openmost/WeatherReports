/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { describe, expect, it } from 'vitest';
import { formatMeasure, hasValue, windArrowRotation } from './weather';

const formatNumber = (value: number, maxFractionDigits: number) => String(
  Number(value.toFixed(maxFractionDigits)),
);

describe('WeatherReports/weather', () => {
  it('keeps zero as a recorded value', () => {
    expect(hasValue(0)).toBe(true);
    expect(hasValue('')).toBe(false);
    expect(hasValue(null)).toBe(false);
    expect(hasValue(undefined)).toBe(false);
  });

  it('glues degree and percent symbols to the number', () => {
    expect(formatMeasure('13.199999809', '°C', 1, formatNumber)).toBe('13.2°C');
    expect(formatMeasure(67, '%', 0, formatNumber)).toBe('67%');
  });

  it('separates other units with a space', () => {
    expect(formatMeasure('29.85', 'inHg', 2, formatNumber)).toBe('29.85 inHg');
    expect(formatMeasure(9.7, 'km/h', 1, formatNumber)).toBe('9.7 km/h');
  });

  it('formats values without unit', () => {
    expect(formatMeasure(0, '', 1, formatNumber)).toBe('0');
    expect(formatMeasure(undefined, 'mm', 1, formatNumber)).toBe('');
  });

  it('points the wind arrow where the wind blows to', () => {
    expect(windArrowRotation('N')).toBe(180);
    expect(windArrowRotation('sw')).toBe(45);
    expect(windArrowRotation('NNW')).toBe(157.5);
    expect(windArrowRotation('XYZ')).toBeNull();
    expect(windArrowRotation(undefined)).toBeNull();
  });
});
