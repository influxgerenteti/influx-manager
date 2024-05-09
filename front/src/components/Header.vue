<template>
  <header class="app-header navbar mobile no-print">
   
    <div>
      <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button" @click="mobileSidebarToggle($event, 'principal')">
        <font-awesome-icon icon="bars" class="nav-icon" />
      </button>
      <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button" @click="mobileSidebarToggle($event, 'favoritos')">
        <font-awesome-icon icon="star" class="nav-icon" />
      </button>
    </div>

    <b-link class="navbar-brand" to="#"/>

    <ul class="navbar-nav nav menu-navigator">
      <HeaderFranqueada/>
      <HeaderDropdown/>
    </ul>
  </header>
  
</template>
<script>
import HeaderDropdown from './HeaderDropdown.vue'
import HeaderFranqueada from './HeaderFranqueada.vue'
import EventBus from '../utils/event-bus'

export default {
  name: 'CHeader',
  components: {
    HeaderDropdown,
    HeaderFranqueada
  },
  methods: {
    sidebarToggle (e) {
      e.preventDefault()
      document.body.classList.toggle('sidebar-hidden')
    },
    sidebarMinimize (e) {
      e.preventDefault()
      document.body.classList.toggle('sidebar-minimized')
    },
    mobileSidebarToggle (e, menu) {
      e.preventDefault()
      EventBus.$emit('moduloToggle', menu, true)

      document.body.classList.toggle('sidebar-mobile-show')
    },
    asideToggle (e) {
      e.preventDefault()
      document.body.classList.toggle('aside-menu-hidden')
    }
  }
}
</script>
