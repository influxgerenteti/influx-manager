<template>
  <g-calendar v-mask="'##/##/####'"
              v-if="hasInput"
              ref="calendarElement"
              :value="value"
              :width="'100%'"
              :format="format"
              :placeholder="placeholder"
              :has-input="hasInput"
              :lang="lang"
              :element-id="elementId"
              :disabled="disabled"
              :class="className"
              :min-date="minDate"
              :max-date="maxDateCustom"
              @input="input"
  />
  <g-calendar v-else
              :value="value"
              :width="'100%'"
              :format="format"
              :placeholder="placeholder"
              :class="className"
              :has-input="hasInput"
              :lang="lang"
              :disabled="disabled"
              :element-id="elementId"
              :on-day-click="input"
              :min-date="minDateCustom"
              :max-date="maxDateCustom"
              :show-date-only="showDateOnly"
              :can-change-month="canChangeMonth"
              :start-month="startMonth"
  />
</template>

<script>
import Calendar from 'vue2-slot-calendar'
import {stringToISODate, dateToString, isISODate} from '../utils/date'

window.VueCalendarLang = lang => {
  const translations = {
    'pt-BR': {
      daysOfWeek: ['Do', 'Se', 'Te', 'Qu', 'Qu', 'Se', 'Sa'],
      limit: 'Limite alcançado (máximo de {{limit}} itens).',
      loading: 'Carregando...',
      minLength: 'Comprimento mínimo',
      months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
      notSelected: 'Não selecionado',
      required: 'Obrigatório',
      search: 'Buscar'
    }
  }

  return translations[lang] || translations['pt-BR']
}

export default {
  components: {
    GCalendar: Calendar
  },

  props: {
    elementId: {
      type: String,
      required: false,
      default: ''
    },

    placeholder: {
      type: String,
      required: false,
      default: ''
    },

    hasInput: {
      type: Boolean,
      default: true
    },

    maxlength: {
      type: String,
      required: false,
      default: ''
    },

    selected: {
      type: Function,
      default: function () {},
      required: false
    },

    value: {
      type: String,
      default: ''
    },

    extraParam: {
      type: [String, Number, Object, Array],
      default: null
    },

    canChangeMonth: {
      type: Boolean,
      default: true
    },

    startMonth: {
      type: [String, Date],
      default: null
    },

    disabled: {
      type: Boolean,
      default: false
    },

    className: {
      type: [String, Number, Object, Array],
      default: undefined
    },

    minDate: {
      type: String,
      default: ''
    },

    maxDate: {
      type: String,
      default: ''
    },

    required: {
      type: Boolean,
      default: false,
      required: false
    }
  },

  data () {
    return {
      lang: 'pt-BR',
      format: 'dd/MM/yyyy',
      minDateCustom: '',
      // maxDate: '',
      maxDateCustom: '',
      showDateOnly: false,
      start: new Date()
    }
  },

  watch: {
    value (newValue, oldValue) {
      if (!this.value && this.value !== '') {
        this.input('')
      } else if (isISODate(this.value)) {
        this.input(dateToString(new Date(this.value)))
      }
    },
    disabled (newValue) {
      const calendar = this.$children[0]
      if (calendar) {
        const input = calendar.$el.querySelector('input')
        if (input) {
          input.disabled = false
          if (newValue === true) {
            input.disabled = true
          }
        }
      }
    }
  },

  mounted () {
    const calendar = this.$children[0]
    if (!calendar) {
      return
    }

    this.maxDateCustom = this.maxDate

    const input = calendar.$el.querySelector('input')
    if (input) {
      input.autocomplete = 'off'
      input.maxlength = this.maxlength
      input.required = this.required

      input.addEventListener('blur', ($event) => {
        const value = $event.target.value

        if (value.length > 0 && value.length < 10) {
          this.input('')
          return
        }

        const date = new Date(stringToISODate(value))
        if (!(date instanceof Date) || isNaN(date)) {
          this.input('')
        }
      })
    }

    const prev = calendar.$el.querySelector('.glyphicon.glyphicon-chevron-left')
    const next = calendar.$el.querySelector('.glyphicon.glyphicon-chevron-right')

    if (this.startMonth) {
      this.start = this.startMonth
      this.input(this.startMonth)
    }

    if (this.canChangeMonth) {
      if (prev) {
        const prevClasses = prev.classList
        prevClasses.remove('glyphicon')
        prevClasses.remove('glyphicon-chevron-left')
        prevClasses.add('fa')
        prevClasses.add('fa-chevron-left')
      }

      if (next) {
        const nextClasses = next.classList
        nextClasses.remove('glyphicon')
        nextClasses.remove('glyphicon-chevron-right')
        nextClasses.add('fa')
        nextClasses.add('fa-chevron-right')
      }
    } else {
      this.showDateOnly = true
      prev.remove()
      next.remove()

      const year = this.start.getFullYear()
      const month = this.start.getMonth()
      const firstDay = new Date(year, month, 1)
      const lastDay = new Date(year, month + 1, 0)

      this.minDateCustom = firstDay.toISOString()
      this.maxDateCustom = lastDay.toISOString()
    }
  },

  methods: {
    input (value) {
      let parsedValue = value

      if (this.selected) {
        this.selected(parsedValue, this.extraParam)
        this.$emit('selected', {value: parsedValue, extraParam: this.extraParam})
      }

      this.$emit('input', parsedValue, this.extraParam)
    }
  }
}
</script>

<style>
.datepicker {
  width: 100%;
}

.datepicker-ctrl span.datepicker-preBtn {
  margin-top: 6px;
  margin-left: 6px;
}

.datepicker-ctrl span.datepicker-nextBtn {
  margin-top: 6px;
  margin-right: 6px;
}

.data-calendario .datepicker-popup {
  border: 0;
  box-shadow: none;
  background-color: #ececec;
  border-radius: 0;
}
.data-calendario .datepicker-wrapper {
  position: relative;
  z-index: 0;
}
.data-calendario .datepicker-inner {
  width: 100%;
  margin: 0 auto;
}
.data-calendario .datepicker-ctrl p,
.data-calendario .datepicker-ctrl span,
.data-calendario .datepicker-body span {
  width: 13%;
}

.datepicker {
  /* font-family: 'Comfortaa', 'Helvetica', sans-serif !important; */
  font-family: BlinkMacSystemFont,-apple-system,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,Fira Sans,Droid Sans,Helvetica Neue,Helvetica,Arial,sans-serif;
}

.datepicker .form-control,
.datepicker.form-control {
  height: 31px !important;
  /* padding: 0.375rem 0.75rem !important; */
  font-size: 0.875rem !important;
  line-height: 1.5 !important;
  color: #3e515b !important;
  background-clip: padding-box !important;
  border-radius: 0 !important;
  transition: border-color 0.15s !important;
  box-shadow: none !important;
  border-radius: 0 !important;
  border: 0 !important;
  background-color: #ececec !important;
}
.datepicker .form-control {
  border: 0 !important;
  background-color: #ececec !important;
}

.datepicker .form-control[readonly], .datepicker .form-control[disabled] {
  background-color: #c2cfd6 !important;
}

.datepicker .form-control:hover, .datepicker .form-control:focus
.datepicker:not(.invalid-input) .form-control:hover {
  outline: 0 !important;
  border-color: transparent !important;
  box-shadow: none !important;
}
.datepicker .form-control:focus:hover,
.datepicker .form-control:focus {
  color: #3e515b !important;
  background-color: #fff !important;
  border-color: #8ad4ee !important;
  outline: 0 !important;
  box-shadow: 0 0 0 0.2rem rgba(32, 168, 216, 0.25) !important;
}
.was-validated .datepicker:not(.invalid-input) .form-control:valid,
.datepicker:not(.invalid-input) .form-control .valid-input,
.datepicker:not(.invalid-input).form-control.valid-input {
  -webkit-box-shadow: inset 0 0 0 1px #23d160 !important;
  -moz-box-shadow: inset 0 0 0 1px #23d160 !important;
  box-shadow: inset 0 0 0 1px #23d160 !important;
  background-color: #fff !important;
}

.datepicker.invalid-input .form-control:hover,
.datepicker.invalid-input input,
.datepicker .form-control .invalid-input,
.datepicker.form-control.invalid-input,
.was-validated .form-control:invalid {
  -webkit-box-shadow: inset 0 0 0 1px #FF3860 !important;
  -moz-box-shadow: inset 0 0 0 1px #FF3860 !important;
  box-shadow: inset 0 0 0 1px #FF3860 !important;
  background-color: #fff !important;
}

.datepicker.datepicker-right .datepicker-wrapper {
  right: 0;
}
</style>
