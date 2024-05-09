<template>
  <b-btn :disabled="processing || disabled" :variant="variant" type="submit">
    <font-awesome-icon v-if="processing" icon="sync-alt" spin />
    <template v-else>
      <slot></slot><!-- Button text -->
    </template>
  </b-btn>
</template>

<script>
export default {
  name: 'SaveButton',
  props: {
    processing: {
      type: Boolean,
      default: false,
      required: false
    },

    disabled: {
      type: Boolean,
      default: false,
      required: false
    },

    variant: {
      type: String,
      default: 'verde',
      required: false
    }
  }
}
</script>
