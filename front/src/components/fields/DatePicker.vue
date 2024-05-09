<template>
  <v-date-picker
    :value="value"
    :attributes="attr"
    :input-props="{ class: 'form-control', placeholder: 'Data', id, required, disabled, autocomplete: 'off' }"
    :first-day-of-week="1"
    :popover="{ visibility: 'click' }"
    locale="pt-BR"
    @input="onInput" />
</template>

<script>
export default {

  props: {
    value: {
      type: Date,
      default: null,
      required: false
    },

    id: {
      type: String,
      default: '',
      required: false
    },

    required: {
      type: Boolean,
      default: false,
      required: false
    },

    disabled: {
      type: Boolean,
      default: false,
      required: false
    }
  },

  data () {
    return {
      attr: [
        {
          key: 'Hoje',
          highlight: {
            color: 'indigo',
            fillMode: 'light'
          },
          dates: new Date()
        }
      ]
    }
  },

  methods: {
    onInput (value) {
      this.$emit('input', value)
    }
  }
}
</script>
