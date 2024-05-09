<template>
  <table v-grid-order :id="id" :class="{ autoheight: autoheight }" class="table-scroll mobile-cards table b-table table-hover table-borderless table-sm" @sort="sort">
    <slot></slot>
  </table>
</template>

<script>
import gridOrder from '../directives/grid-order'

export default {
  name: 'GTable',
  directives: {
    gridOrder
  },
  props: {
    id: {
      type: String,
      required: false,
      default: null
    },
    sort: {
      type: Function,
      required: false,
      default: function () {}
    },
    autoheight: {
      type: Boolean,
      required: false,
      default: false
    }
  }
}
</script>

<style>
.autoheight {
  height: auto !important;
}
</style>
