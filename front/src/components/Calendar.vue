<template>
  <div>
    <input v-mask="'##/##/####'" v-if="!isOpen"
           :id="elementId"
           :value="formattedDate"
           :placeholder="placeholder"
           :required="required"
           :class="className"
           type="text"
           class="form-control"
           autocomplete="off"
           @focus="focus"
           @input="input">

    <div v-if="focado || isOpen" :class="className" :data-open="isOpen" class="calendar">
      <header class="header">
        <button type="button" @click="previousMonth">&lt;&lt;</button>
        <span>{{ currentMonthLabel }} {{ dayEndMonth == false ? currentYear : null }}</span>
        <button type="button" @click="nextMonth">&gt;&gt;</button>

      </header>
      <div v-for="(dayLabel, i) in dayLabels" :key="i" class="headings">
        {{ dayLabel }}
      </div>

      <div v-for="(day, index) in daysArray"
           :key="`${index}_${day.date.getDate()}_${day.date.getMonth()}`"
           :class="dayClassObj(day)"
           class="day">
        <button type="button" @click="setSelectedDate(day)">
          {{ day.date | formatDateToDay }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import * as dateFns from 'date-fns'
import moment from 'moment'
import {stringToISODate} from '../utils/date'
import { format } from 'date-fns/esm'
import addDays from 'date-fns/esm/addDays/index.js'
import parseISO from 'date-fns/esm/fp/parseISO/index.js'

const DAY_LABELS = ['Do', 'Se', 'Te', 'Qu', 'Qu', 'Se', 'Sa']
const MONTH_LABELS = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']
const STRING_VAZIA = ''

export default {
  name: 'Calendar',

  filters: {
    formatDateToDay (val) {
      return dateFns.format(val, 'd')
    }
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
      default: 'Data'
    },

    value: {
      type: [Date, String],
      required: false,
      default: null
    },

    isOpen: {
      type: Boolean,
      required: false,
      default: false
    },

    startDate: {
      required: false,
      type: Date,
      default: undefined
    },

    className: {
      required: false,
      type: [String, Number, Object, Array],
      default: undefined
    },

    required: {
      required: false,
      type: [Boolean],
      default: false
    },

    dayEndMonth: {
      required: false,
      type: [Boolean],
      default: false
    }
  },

  data () {
    return {
      focado: false,
      dataDisplay: '',
      today: null,
      selectedDate: null,
      currDateCursor: null,
      dayLabels: null
    }
  },

  computed: {
    formattedDate () {
      if (typeof this.selectedDate === 'string') {
        return
      }
      if (this.selectedDate === STRING_VAZIA) {
      return "";
      } else if (typeof this.selectedDate === 'object') {
        if (this.dayEndMonth) {
          return moment(this.selectedDate).format('DD/MM')
        }
        return moment(this.selectedDate).format('DD/MM/YYYY')
      }
      return "";
    },

    currentMonth () {
      return this.currDateCursor.getMonth()
    },

    currentYear () {
      return this.currDateCursor.getFullYear()
    },

    currentMonthLabel () {
      return MONTH_LABELS[this.currentMonth]
    },

    daysArray () {
 
      const date = this.currDateCursor

      const startOfMonth = dateFns.startOfMonth(date)
      const start = parseISO(format(startOfMonth, 'yyyy-MM-dd'))
      const endOfMonth = dateFns.endOfMonth(date)
     const end = parseISO(format(endOfMonth, 'yyyy-MM-dd'))


      const days = dateFns.eachDayOfInterval({start: start, end: end }).map((day) => ({
        date: day,
        isCurrentMonth: dateFns.isSameMonth(new Date(this.currentYear, this.currentMonth), day),
        isToday: dateFns.isToday(day),
        isSelected: dateFns.isSameDay(this.selectedDate, day)
      }))

      // gen the days from last month
      let previousMonthCursor = dateFns.lastDayOfMonth(dateFns.addMonths(date, -1))
      const begIndex = dateFns.getDay(days[0].date)
      for (let i = begIndex; i > 0; i--) {
        days.unshift({
          date: previousMonthCursor,
          isCurrentMonth: false,
          isToday: dateFns.isToday(previousMonthCursor),
          isSelected: dateFns.isSameDay(this.selectedDate, previousMonthCursor)
        })
        previousMonthCursor = dateFns.addDays(previousMonthCursor, -1)
      }

      // gen days for next month
      const daysNeededAtEnd = (days.length % 7 > 0) ? (7 - (days.length % 7)) : 0
      let nextMonthCursor = dateFns.addMonths(date, 1)
      nextMonthCursor = dateFns.setDate(nextMonthCursor, 1)
      for (let x = 1; x <= daysNeededAtEnd; x++) {
        days.push({
          date: nextMonthCursor,
          isCurrentMonth: false,
          isToday: dateFns.isToday(nextMonthCursor),
          isSelected: dateFns.isSameDay(this.selectedDate, nextMonthCursor)
        })
        nextMonthCursor = dateFns.addDays(nextMonthCursor, 1)
      }
      return days
    }
  },

  created () {
    this.dayLabels = DAY_LABELS.slice()
    this.today = new Date()
    this.currDateCursor = this.today
    this.selectedDate = null
  },

  mounted () {
    if (this.value === null) {
      this.currDateCursor = this.today
      this.selectedDate = STRING_VAZIA
    } else {
      if (this.value.constructor.name === 'Date') {
        this.selectedDate = this.value
        this.currDateCursor = this.value
      } else {
        const date = new Date(stringToISODate(this.value, true))
        this.selectedDate = date
        this.currDateCursor = date
      }
    }

    if (this.startDate) {
      this.currDateCursor = this.startDate
      this.selectedDate = this.startDate
    }

    function isDatePicker (target, depth) {
      if (!target.parentNode || depth === 0) {
        return false
      }

      if (target.classList.contains('calendar') === true) {
        return true
      }

      return isDatePicker(target.parentNode, depth--)
    }

    document.body.addEventListener('mousedown', ($event) => {
      if (this.focado === true && isDatePicker($event.target, 10) === false) {
        this.blur()
      }
    })
  },

  methods: {
    dayClassObj (day) {
      return {
        'today': day.isToday,
        'current': day.isCurrentMonth,
        'selected': day.isSelected
      }
    },

    nextMonth () {
      this.currDateCursor = dateFns.addMonths(this.currDateCursor, 1)
    },

    previousMonth () {
      this.currDateCursor = dateFns.addMonths(this.currDateCursor, -1)
    },

    setSelectedDate (day = null) {
      this.selectedDate = day.date
      this.$emit('input', this.formattedDate)
      this.focado = false
    },

    focus () {
      this.focado = true
    },

    blur () {
      this.focado = false
    },

    input (event) {
      const valor = event.target.value

      if (valor.length < 10) {
        this.currDateCursor = this.today
        this.selectedDate = STRING_VAZIA

        this.$emit('input', this.formattedDate)
      } else if (valor.length === 10) {
        const date = new Date(stringToISODate(valor, true))
        this.selectedDate = date
        this.currDateCursor = date
        this.$emit('input', this.formattedDate)
      }
    }
  }
}
</script>

<style lang="scss" scoped>

.calendar {
  position: absolute;
  z-index: 2;
  margin-top: 3px;
  min-width: 240px;
  max-width: 240px;
  background-color: var(--white);
  border-radius: 4px;
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  padding: .25rem .5rem 1rem .5rem;
  color:rgba(0, 0, 0, 0.65098);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);

  > .header {
    padding: .5rem;
    font-size: 14px;
    grid-column: 1 / span 7;

    >span {
      flex: 1;
      text-align: center;
    }

    button {
      border: none;
      background: var(--white);

      &:hover {
        background: var(--black-400);
        transition: background 150ms;
      }
    }
  }

  > .headings {
      font-weight: bold;
      font-size: 14px;
    }

  > * {
    align-items: center;
    display: flex;
    justify-content: center;
  }

  > .day {
    color: #999999;
    font-size: 14px;

    &.current {
      color: rgba(0,0,0,0.65);
    }

    &.selected {
      background: #1985ac;
      color: white;
    }

    &:hover{
      background: #9999;
      // background: #1985ac;
      // color: white;
    }

    // &::before {
    //   content: "";
    //   display: inline-block;
    //   height: 0;
    //   padding-bottom: 100%;
    //   width: 1px;
    // }

    button {
      color: inherit;
      background: transparent;
      border: none;
      height: 32px;
      width: 100%;
    }
  }

  > .today {
    background: var(--black-400);
    border-radius: 2px;
  }
}

.text-center {
  text-align: center;
}

.calendario-direita {
  right: 0;
}

.input-group .input-group-prepend ~ div {
  flex-grow: 1;
  width: calc(100% - 60px);
  width: -webkit-calc(100% - 60px);
  width: -moz-calc(100% - 60px);
}

div.calendar[data-open="true"] {
  position: relative;
  box-shadow: none;
  padding: 0;
  margin: 0;
  z-index: 0;
}
</style>
