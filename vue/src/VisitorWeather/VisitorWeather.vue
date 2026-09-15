<!--
  Matomo - free/libre analytics platform

  @link    https://matomo.org
  @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
-->

<template>
  <div class="visitorWeather">
    <div class="visitorWeather__header">
      <span class="visitorWeather__title">
        {{ translate('WeatherReports_Weather') }}<template v-if="condition">
          · <span class="visitorWeather__condition">{{ condition }}</span>
        </template>
      </span>
      <span
        v-if="temperature"
        class="visitorWeather__temperature"
      >
        {{ temperature }}
        <span
          v-if="feltTemperature"
          class="visitorWeather__feelsLike"
        >({{ translate('WeatherReports_FeelsLike', feltTemperature) }})</span>
      </span>
    </div>
    <dl
      v-if="measures.length"
      class="visitorWeather__measures"
    >
      <div
        v-for="measure in measures"
        :key="measure.key"
        class="visitorWeather__measure"
        :class="{ 'visitorWeather__measure--wide': measure.wide }"
      >
        <dt>{{ measure.label }}</dt>
        <dd>
          <svg
            v-if="measure.arrowRotation !== null"
            class="visitorWeather__windArrow"
            viewBox="0 0 12 12"
            aria-hidden="true"
            :style="{ transform: `rotate(${measure.arrowRotation}deg)` }"
          >
            <path d="M6 1 10 10 6 8 2 10Z" />
          </svg>
          {{ measure.value }}
        </dd>
      </div>
    </dl>
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, PropType } from 'vue';
import { NumberFormatter, translate } from 'CoreHome';
import {
  formatMeasure,
  hasValue,
  WeatherUnits,
  WeatherValue,
  WeatherValues,
  windArrowRotation,
} from './weather';

interface Measure {
  key: string;
  label: string;
  value: string;
  wide: boolean;
  arrowRotation: number | null;
}

/**
 * Weather card of a visit in the visitor log and visitor profile. Values are the raw weather_*
 * columns of the visit, units the symbols configured in the site measurable settings.
 */
export default defineComponent({
  name: 'VisitorWeather',
  props: {
    weather: {
      type: Object as PropType<WeatherValues>,
      required: true,
    },
    units: {
      type: Object as PropType<WeatherUnits>,
      required: true,
    },
  },
  setup(props) {
    const format = (
      value: WeatherValue | undefined,
      unit: string,
      maxFractionDigits: number,
    ) => formatMeasure(
      value,
      unit,
      maxFractionDigits,
      (number, digits) => NumberFormatter.formatNumber(number, digits, 0),
    );

    const condition = computed(() => (hasValue(props.weather.weather_condition)
      ? String(props.weather.weather_condition)
      : ''));

    const temperature = computed(() => format(
      props.weather.weather_temperature,
      props.units.temperature,
      1,
    ));

    const feltTemperature = computed(() => format(
      props.weather.weather_felt_temperature,
      props.units.temperature,
      1,
    ));

    const measures = computed(() => {
      const { weather, units } = props;
      const list: Measure[] = [];
      const add = (
        key: string,
        label: string,
        value: string,
        wide = false,
        arrowRotation: number | null = null,
      ) => {
        if (value) {
          list.push({
            key,
            label,
            value,
            wide,
            arrowRotation,
          });
        }
      };

      add('humidity', translate('WeatherReports_Humidity'), format(weather.weather_humidity, '%', 0));
      add('cloud', translate('WeatherReports_Cloud'), format(weather.weather_cloud, '%', 0));
      add(
        'pressure',
        translate('WeatherReports_Pressure'),
        format(weather.weather_pressure, units.pressure, 2),
      );
      add(
        'visibility',
        translate('WeatherReports_Visibility'),
        format(weather.weather_visibility, units.visibility, 1),
      );
      add(
        'precipitation',
        translate('WeatherReports_Precipitation'),
        format(weather.weather_precipitation, units.precipitation, 2),
      );
      add('uv', translate('WeatherReports_Uv'), format(weather.weather_uv, '', 1));

      const wind = [
        hasValue(weather.weather_wind_direction) ? String(weather.weather_wind_direction) : '',
        format(weather.weather_wind_speed, units.wind, 1),
      ].filter((part) => part !== '').join(' · ');
      add(
        'wind',
        translate('WeatherReports_Wind'),
        wind,
        true,
        windArrowRotation(weather.weather_wind_direction),
      );

      return list;
    });

    return {
      translate,
      condition,
      temperature,
      feltTemperature,
      measures,
    };
  },
});
</script>
