<template>
  <div>
    <div class="app-header navbar ">
      <b-link class="navbar-brand" to="#"/>

    </div>

    <div class="menu-nav justify-content-between p-0 sidebar-header">
      <div :class="selected === 'principal' ? 'selected' : ''" class="d-flex flex-fill justify-content-center align-items-center menu-select" title="Principal" @click="menuOpen('principal')">
        <font-awesome-icon icon="bars"/>
      </div>
      <div :class="selected === 'favoritos' ? 'selected' : ''" class="d-flex flex-fill justify-content-center align-items-center menu-select" title="Favoritos" @click="menuOpen('favoritos')">
        <font-awesome-icon icon="star"/>
      </div>
    </div>
  </div>
</template>
<script>
import EventBus from '../../utils/event-bus'

export default {
  name: 'SidebarHeader',
  data () {
    return {
      selected: 'principal',
      mobileFirst: true
    }
  },
  created () {
    EventBus.$on('moduloToggle', (id, mobile) => {
      this.$parent.moduloSelected(id)

      if (this.mobileFirst && mobile) {
        this.mobileFirst = false
        this.selected = ''
      } else {
        this.mobileFirst = true
      }

      if (id === this.selected) {
        document.body.classList.remove('sidebar-mobile-show')
      }
      this.selected = id
    })
  },
  methods: {
    menuOpen (id) {
      EventBus.$emit('moduloToggle', id)
    }
  }
}
</script>
<style scoped>
.menu-nav {
  display: flex;
}
</style>
