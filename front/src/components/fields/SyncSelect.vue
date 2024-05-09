<template>
  <multiselect
    :value="value"
    :id="id"
    :label="field.descriptionColumn"
    :custom-label="makeDescription"
    :track-by="field.valueColumn"
    :options="options"
    :searchable="true"
    :loading="isLoading"
    :show-labels="false"
    :class="{'invalid-input': !wasValidated && required === true && (!value || (value && !value[field.valueColumn])), 'valid-input': !wasValidated && (required === false || (required === true && value && value[field.valueColumn]))}"
    placeholder="Selecione"
    class="multiselect-truncate"
    @input="onInput">
    <template slot="noResult">Nenhum resultado</template>
    <template slot="noOptions">Digite para buscar</template>
    <template slot="caret"><div class="multiselect__caret"><font-awesome-icon icon="caret-down" /></div></template>
  </multiselect>
</template>

<script>
import Request from '../../utils/request'

export default {
  props: {
    value: {
      type: [Object, Array, String, Number],
      required: false,
      default: null
    },

    field: {
      type: Object,
      required: true
    },

    id: {
      type: [Number, String],
      required: false,
      default: ''
    },

    required: {
      type: Boolean,
      required: false,
      default: false
    },

    wasValidated: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      isLoading: false,
      options: [],
      emptyOption: {},
      firstValueChange: true
    }
  },

  watch: {
    value (newValue) {
      if (this.value !== null && this.firstValueChange === true && this.field.selectOptions && (this.field.type === 'string' || this.field.type === 'number')) {
        let option = this.field.selectOptions.find(option => option[this.field.valueColumn] === this.value)
        if (option === undefined) {
          option = newValue
        }

        this.$emit('input', option)
        this.firstValueChange = false
      }
    }
  },

  mounted () {
    this.emptyOption[this.field.valueColumn] = null
    this.emptyOption[this.field.descriptionColumn] = 'Selecione'

    if (this.field.selectOptions && !this.field.targetEntity) {
      if (typeof this.field.selectOptions !== 'object') {
        this.field.selectOptions = JSON.parse(this.field.selectOptions.replace(/'/g, '"'))
      }

      this.options = this.field.selectOptions.map(option => {
        if (typeof option !== 'string' && typeof option !== 'number') {
          return option
        }

        option = {}
        option[this.field.valueColumn] = option
        option[this.field.descriptionColumn] = option
        return option
      })

      this.options.splice(0, 0, this.emptyOption)
    } else {
      this.onSearch()
    }
  },

  methods: {
    makeDescription (value) {
      const columns = this.field.descriptionColumn.split('.')
      if (!value) {
        return null
      }

      columns.forEach(col => {
        value = value[col] ? value[col] : value
      })

      return value
    },

    onInput (value) {
      this.$emit('input', value)
    },

    onSearch (query) {
      this.isLoading = true

      Request.get('/magic', { entity: this.field.targetEntity, doNotPaginate: true, query, queryColumn: this.field.queryColumn, findQuery: this.field.findQuery, with: this.field.with, where: this.field.where, withFranchisingData: this.field.withFranchisingData })
        .then((response) => {
          this.options = [ this.emptyOption, ...response.body.data ]
        })
        .catch(console.error)
        .finally(() => {
          this.isLoading = false
        })
    }
  }
}
</script>
