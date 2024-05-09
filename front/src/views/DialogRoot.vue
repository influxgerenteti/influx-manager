<template>
    <DialogModal :isOpen="!!component" :title="title" @onClose="handleModalClose">
      <component :is="component" @onClose="handleClose" v-bind="props" />
    </DialogModal>
  </template>
  
  <script>
  import { DialogModalBus } from '../eventBus'
  
  import DialogModal from './DialogModal'
  
  export default {
    data () {
      return {
        component: null,
        title: '',
        props: null,
        closeOnClick: true
      }
    },
    created () {
        DialogModalBus.$on('open', ({ component, title = '', props = null, closeOnClick = true }) => {
        console.log("entrou")
        this.component = component
        this.title = title
        this.props = props
        this.closeOnClick = closeOnClick
        console.log(this.props)
      })
       document.addEventListener('keyup', this.handleKeyup)
       DialogModalBus.$on('onClose', () => {
        console.log("close dialog")
        this.handleClose()
      })
    },
    beforeDestroy () {
       document.removeEventListener('keyup', this.handleKeyup)
    },
    methods: {
      handleModalClose (force = false) {
        if (!this.closeOnClick && !force) return
        this.handleClose()
      },
      handleClose () {
        this.component = null
      },
      handleKeyup (e) {
        if (e.keyCode === 27) this.handleClose()
      }
    },
    components: { DialogModal }
  }
  </script>