<template>
  <vue-numeric
    :value="value === null ? '' : value"
    :currency="currency"
    :placeholder="placeholder"
    :min="min"
    :max="max"
    :minus="minus"
    :empty-value="emptyValue"
    :read-only="readOnly"
    :precision="precision"
    separator="."
    autocomplete="off"
    @input="triggerInput"
    @blur="blankValue($event)"
  />
</template>

<script>
import VueNumeric from 'vue-numeric'
import { setTimeout } from 'timers'
import { toNumber } from '../utils/number'

export default {
  name: 'GNumeric',

  components: {
    VueNumeric
  },

  props: {
    value: {
      type: [String, Number, null],
      required: false,
      default: null
    },

    precision: {
      type: Number,
      default: 2,
      required: false
    },

    currency: {
      type: String,
      required: false,
      default: ''
    },
    placeholder: {
      type: String,
      required: false,
      default: ' '
    },
    min: {
      type: Number,
      required: false,
      default: undefined
    },
    max: {
      type: Number,
      required: false,
      default: undefined
    },
    minus: {
      type: Boolean,
      required: false,
      default: false
    },
    emptyValue: {
      type: Number,
      required: false,
      default: null
    },
    isWiped: {
      type: Boolean,
      required: false,
      default: false
    },
    readOnly: {
      type: Boolean,
      required: false,
      default: false
    },
    input: {
      type: Function,
      required: false,
      default: undefined
    },
    extraParam: {
      type: [String, Number, Object, Array],
      default: null
    }
  },

  methods: {
    toNumber,

    blankValue (e) {
      if (!this.isWiped || (this.value !== 0 && this.value !== '')) {
        return
      }

      setTimeout(() => {
        e.target.value = ''
      }, 10)
    },

    triggerInput (value) {
      this.$emit('input', value, this.extraParam)

      if (this.input) {
        this.input(value, this.extraParam)
      }
    }
  }
}
</script>
