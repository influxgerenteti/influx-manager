<template>
  <multiselect
    :id="id"
    :value="computedValue"
    :options="options"
    :label="label"
    :track-by="trackBy"
    :allow-empty="false || multiTag"
    :custom-label="customLabel"
    :disabled="disabled"
    :required="required"
    :class="invalid === true ? 'invalid-input' : 'valid-input'"
    v-bind="override"
    :placeholder="placeholder"
    :multiple="false || multiTag"
    :taggable="false || multiTag"
    selected-label=""
    select-label=""
    deselect-label=""
    tag-placeholder=""
    class="multiselect-truncate"
    @select="selectItem"
    @remove="selectItem"
  >
    <template slot="noResult">Nenhum resultado.</template>
  </multiselect>
</template>

<script>
export default {
  name: 'GSelect',
  props: {
    id: {
      type: String,
      required: false,
      default: null
    },
    label: {
      type: String,
      required: false,
      default: null
    },
    customLabel: {
      type: Function,
      required: false,
      default: undefined
    },
    trackBy: {
      type: String,
      required: false,
      default: undefined
    },
    options: {
      type: Array,
      required: true
    },
    value: {
      type: [String, Number, Object, Array],
      required: false,
      default: ''
    },
    placeholder: {
      type: String,
      required: false,
      default: 'Selecione'
    },
    select: {
      type: Function,
      required: false,
      default: function () {}
    },
    extraParam: {
      type: [String, Number, Object, Array, Boolean],
      required: false,
      default: null
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    required: {
      type: Boolean,
      default: false,
      required: false
    },
    multiTag: {
      type: Boolean,
      required: false,
      default: false
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false
    },
    forceNullable: {
      type: Boolean,
      required: false,
      default: false
    },
    preventNullable: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  computed: {
    override () {
      return {
        tabIndex: 0
      }
    },

    computedValue: {
      get () {
        return this.value
      },
      set (newValue) {}
    },

    optionalField () {
      const obj = {}
      const trackBy = this.trackBy || 'value'
      const label = this.label || 'text'
      obj[`${trackBy}`] = null
      obj[`${label}`] = 'Selecione'
      return obj
    },

    optionsArray () {
      const arr = []
      const trackBy = this.trackBy || 'value'
      if (this.options.find(opt => opt[`${trackBy}`] === null) === undefined && (this.required === false || this.forceNullable === true) && this.preventNullable === false) {
        arr.push(this.optionalField)
      }
      return [...arr, ...this.options]
    }
  },

  watch: {
    value () {
      if (!this.value) {
        this.computedValue = this.optionalField
      }
    }
  },

  methods: {
    selectItem (item) {
      this.select(item, this.extraParam)
      this.$emit('input', item, this.extraParam)
    }
  }
}
</script>
