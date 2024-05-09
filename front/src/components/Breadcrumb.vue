<template>
  <ol v-if="showBreadcrumbs" class="breadcrumb barra-fixa no-print">
    <li v-for="(item, index) in list" :key="index" class="breadcrumb-item">
      <span v-if="isLast(index)" class="active">{{ showName(item) }}</span>
      <span v-else-if="item.meta.disabled" class="active">{{ showName(item) }}</span>
      <router-link v-else :to="showEndpoint(item)">{{ showName(item) }}</router-link>
    </li>
  </ol>
</template>

<script>
import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState('root', ['showBreadcrumbs']),
    list () {
      return this.$route.matched
    }
  },

  methods: {
    isLast (index) {
      return index === this.list.length - 1
    },

    showEndpoint (item) {
      const routePath = {
        path: this.$route.params.entity ? item.path.replace(/(.*):entity(.*)/, `$1${this.$route.params.entity}$2`) : item.path
      }

      return routePath
    },

    showName (item) {
      let name = ''
      if (item.meta && item.meta.label) {
        name = item.meta && item.meta.label
      } else if (item.name) {
        name = item.name
      }

      return name
    }
  }
}
</script>
<style scoped>
.breadcrumb {
  /* -webkit-box-shadow: 0 0.25rem 0.125rem 0 #EBECF0;
  box-shadow: 0 0.25rem 0.125rem 0 #EBECF0; */
  -webkit-box-shadow: none;
  box-shadow: none;
}

.barra-fixa {
  margin-bottom: 0;
}
/*
.barra-fixa{
  position:fixed;
  width:100%;
  z-index: 1015;
  z-index: 1009;
}
*/
</style>
