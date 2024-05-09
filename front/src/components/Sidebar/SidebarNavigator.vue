<template>
  <div class="sidebar-navigator">
    <b-navbar-nav class="nav menu-navigator">
      <!-- <b-nav-item class="d-md-down-none">
        <font-awesome-icon icon="bell" class="nav-icon" />
      </b-nav-item> -->
      <HeaderDropdown/>
      <HeaderFranqueada/>
    </b-navbar-nav>
  </div>
</template>
<script>
import HeaderDropdown from '../HeaderDropdown.vue'
import HeaderFranqueada from '../HeaderFranqueada.vue'

export default {
  name: 'SidebarFooter',
  components: {
    HeaderDropdown,
    HeaderFranqueada
  }
}
</script>
