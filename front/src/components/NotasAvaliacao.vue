<template>
  <div
    :options="optionsArray"
    :table-view="tableView"
    :label="label"
    class="select-container form-control"
  >
    <input :id="id" type="text" class="select-input" data-focus="false" @focus="openOptions($event)" @blur="closeOptions()" >
    <span class="select-value" @click="componentFocus($event)">{{ typeof value === 'object' ? value[label] : value }}</span>

    <div class="select-options">

      <div v-for="(item, index) in optionsArray" :key="index" :class="{'selected' : value === item}" class="select-option" @click="setValue(item)">
        {{ item[label] }}
      </div>

    </div>
  </div>
</template>

<script>

export default {
  name: 'NotasAvaliacao',
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
    options: {
      type: Array,
      required: true
    },
    forceNullable: {
      type: Boolean,
      required: false,
      default: false
    },
    value: {
      type: [String, Number, Object],
      required: false,
      default: null
    },
    tableView: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  /*   data () {
    return {
    }
  }, */

  computed: {
    optionalField () {
      const obj = {}
      const trackBy = this.trackBy || 'value'
      const label = this.label || 'text'
      obj[`${trackBy}`] = null
      obj[`${label}`] = '--'
      return obj
    },

    optionsArray () {
      const arr = []
      if (this.required === false || this.forceNullable === true) {
        arr.push(this.optionalField)
      }

      return [...arr, ...this.options]
    }
  },

  methods: {
    componentFocus (e) {
      const input = e.type === 'click' ? e.target.previousElementSibling : e.target

      if (![...input.classList].includes('opened')) {
        input.focus()

        this.closeOptions()

        input.classList.add('opened')
        return
      }

      input.classList.remove('opened')
    },

    openOptions (e) {
      const input = e.target
      const container = input.parentElement
      const span = input.nextElementSibling
      const options = span.nextElementSibling

      if (!this.tableView) {
        const top = container.offsetHeight
        options.style.top = `${top}px`
        options.style.right = 'unset'
        options.style.left = 0
      }
    },

    closeOptions () {
      const opened = document.querySelectorAll('.opened')
      if (opened) {
        [...opened].map(item => item.classList.remove('opened'))
      }
    },

    setValue (item) {
      this.$emit('input', item)
    }

  }

}
</script>

<style scoped>
.select-container {
  display: flex;
  position: relative;
  align-items: center;
}
.select-container input {
  opacity: 0;
}

.select-value {
  display: flex;
  width: 100%;
  height: 100%;
  cursor: pointer;
}
.select-value:after {
  pointer-events: none;
  content: "";
  border-color: #999 transparent transparent;
  border-style: solid;
  border-width: 5px 5px 0;
  color: #999;
  position: absolute;
  right: 11px;
  top: 13px;
  transition: all .2s ease;
  transition-delay: .2s;
}
.select-container .select-input:focus + .select-value:after {
  transform: rotate(180deg);
}

.select-options {
  opacity: 0;
  visibility: hidden;
  overflow: hidden;
  position: absolute;
  display: flex;
  flex-direction: row;
  width: auto;
  height: 100%;
  max-height: 31px;
  z-index: 9999;
  right: 30px;
  box-sizing: border-box;
  border: .2rem;
  border-style: solid;
  border-color: #EBECF0;
  background-color: #ffffff;
  color: #525252;
  transition: all .2s ease;
  transition-delay: .2s;
}
.select-container .select-input:focus + .select-value + .select-options,
.open .select-options {
  opacity: 1;
  visibility: visible;
}

.select-option {
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  width: 30px;
  height: auto;
  transition: all .2s;
}
.select-option:hover,
.select-option.selected {
  background-color: #9E9E9E;
  color: #ffffff;
}

.select-input {
  position: absolute;
  width: 0px;
  padding: 0px;
}

</style>
