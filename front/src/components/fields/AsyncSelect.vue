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
    :internal-search="false"
    :show-labels="false"
    :class="{'invalid-input': !wasValidated && required === true && !value, 'valid-input': !wasValidated && (required === false || (required === true && value))}"
    placeholder="Buscar"
    class="multiselect-truncate"
    @input="onInput"
    @search-change="debouncedSearch">
    <template slot="noResult">Nenhum resultado</template>
    <template slot="noOptions">Digite para buscar</template>
    <template slot="caret">
      <div class="multiselect__caret">
        <font-awesome-icon v-if="!value || !value[field.valueColumn]" icon="search" />
        <font-awesome-icon v-else icon="times" @click="onClear" />
      </div>
    </template>
  </multiselect>
</template>

<script>
import Request from '../../utils/request'
import {debounce} from '../../utils/functions'

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
      debouncedSearch: debounce(this.onSearch, 300)
    }
  },

  methods: {
    makeDescription (value) {
      let columnGroup = []
      this.field.descriptionColumn.split(',').forEach(column => {
        let val = {...value}
        if (val) {
          column.split('.').forEach(path => {
            val = val[path] || val
          })
        }

        columnGroup.push(val)
      })

      return columnGroup.join(' - ')
    },

    onInput (value) {
      this.$emit('input', value, this.field)
    },

    onSearch (query) {
      this.isLoading = true

      if (!this.field.queryColumn) {
        console.warn('Coluna para pesquisa não informada')
        this.isLoading = false
        return
      }

      if (!query) {
        this.isLoading = false
        return
      }

      let where = this.field.queryColumn.split(/[,\s?]/).map(column => ({ field: column, criteria: 'LIKE', value: `^${query}^` }))
      where = [...where, ...(this.field.where ? this.field.where : [])]

      Request.get('/magic', { entity: this.field.targetEntity, doNotPaginate: true, where, whereOperator: 'OR', findQuery: this.field.findQuery, with: this.field.with, withFranchisingData: this.field.withFranchisingData })
        .then((response) => {
          this.options = response.body.data
        })
        .catch(console.error)
        .finally(() => {
          this.isLoading = false
        })
    },

    onClear () {
      this.$emit('input', null, this.field)
    }
  }
}
</script>
