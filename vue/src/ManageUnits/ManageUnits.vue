<!--
  Matomo - free/libre analytics platform

  @link    https://matomo.org
  @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
-->

<template>
  <ContentBlock :content-title="translate('WeatherReports_UnitsPageTitle')">
    <p>{{ translate('WeatherReports_UnitsPageIntro') }}</p>

    <div
      v-for="field in fields"
      :key="field.quantity"
    >
      <Field
        uicontrol="select"
        :name="`weatherUnit_${field.quantity}`"
        :title="field.title"
        :inline-help="field.description"
        :options="field.options"
        v-model="values[field.quantity]"
      />
    </div>

    <SaveButton
      :saving="isSaving"
      @confirm="save()"
    />

    <p v-if="apiKeyUrl">
      {{ translate('WeatherReports_UnitsPageApiKey') }}
      <a :href="apiKeyUrl">{{ translate('WeatherReports_ApiKeySettingTitle') }}</a>
    </p>
  </ContentBlock>
</template>

<script lang="ts">
import {
  defineComponent,
  PropType,
  reactive,
  ref,
} from 'vue';
import {
  AjaxHelper,
  ContentBlock,
  NotificationsStore,
  translate,
} from 'CoreHome';
import { Field, SaveButton } from 'CorePluginsAdmin';

export interface UnitOption {
  key: string;
  value: string;
}

export interface UnitField {
  quantity: string;
  title: string;
  description: string;
  options: UnitOption[];
}

/**
 * Units in which the weather values of a site are stored and displayed. Rendered on the Weather
 * page of the Websites administration, the site is chosen with the site selector of the page.
 */
export default defineComponent({
  name: 'ManageUnits',
  components: {
    ContentBlock,
    Field,
    SaveButton,
  },
  props: {
    idSite: {
      type: [Number, String],
      required: true,
    },
    fields: {
      type: Array as PropType<UnitField[]>,
      required: true,
    },
    units: {
      type: Object as PropType<Record<string, string>>,
      required: true,
    },
    apiKeyUrl: {
      type: String,
      default: '',
    },
  },
  setup(props) {
    const values = reactive<Record<string, string>>({ ...props.units });
    const isSaving = ref(false);

    const save = () => {
      isSaving.value = true;

      AjaxHelper.post(
        { method: 'WeatherReports.setSiteUnits' },
        {
          idSite: props.idSite,
          temperature: values.temperature,
          precipitation: values.precipitation,
          pressure: values.pressure,
          visibility: values.visibility,
          windSpeed: values.wind,
        },
      ).then(() => {
        NotificationsStore.show({
          message: translate('General_YourChangesHaveBeenSaved'),
          context: 'success',
          type: 'toast',
          id: 'weatherReportsUnitsSaved',
        });
      }).finally(() => {
        isSaving.value = false;
      });
    };

    return {
      translate,
      values,
      isSaving,
      save,
    };
  },
});
</script>
