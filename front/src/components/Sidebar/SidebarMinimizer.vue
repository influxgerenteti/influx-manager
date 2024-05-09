<template>
  <div class="d-flex align-items-center sidebar-minimizer" @click="sidebarMinimize();brandMinimize()">
    <div><font-awesome-icon icon="angle-left" /></div>
    <!-- <span class="sidebar-footer"><a class="link-footer" href="http://gatilabs.com.br" target="_blank">GATI labs.</a> &copy; {{ new Date().getFullYear() }}.</span>
    -->
    <!-- <font-awesome-icon icon="angle-left" flip="horizontal" @click="sidebarMinimize();brandMinimize()"/> -->
    <!-- <button class="sidebar-minimizer" type="button" @click="sidebarMinimize();brandMinimize()"></button> -->
  </div>
</template>
<script>

export default {
  name: 'SidebarMinimizer',
  methods: {
    sidebarMinimize () {
      document.body.classList.toggle('sidebar-minimized')
    },
    brandMinimize () {
      document.body.classList.toggle('brand-minimized')
    }
  }
}
</script>
