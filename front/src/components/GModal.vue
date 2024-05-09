<template>
  <div :id="id" :class="className" class="modal fade" tabindex="-1" role="dialog">
    <div class="backdrop"></div>
    <div :class="`modal-${size}`" class="modal-dialog" role="document">
      <div class="modal-content">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    id: {
      type: String,
      required: true
    },

    size: {
      type: String,
      required: false,
      default: 'md'
    },

    value: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      className: ''
    }
  },

  watch: {
    value (value) {
      if (value === true) {
        this.show()
      } else {
        this.hide()
      }
    }
  },

  methods: {
    show () {
      this.className = 'd-block'
      setTimeout(() => {
        this.className = 'd-block show'
        this.$emit('show')
        setTimeout(() => {
          this.$emit('shown')
        }, 150)
      }, 1)
    },

    hide () {
      this.className = 'd-block'
      this.$emit('hide')
      setTimeout(() => {
        this.className = ''
        this.$emit('hidden')
      }, 150)
    }
  }
}
</script>
<style scoped>
.modal-content {
  overflow-x: hidden;
  overflow-y: auto;
  max-height: calc(100vh - 55px);
}

.modal .modal .modal-dialog {
  margin-top: 0;
}

.backdrop {
  background: #151b1e;
  opacity: .3;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
}
</style>
