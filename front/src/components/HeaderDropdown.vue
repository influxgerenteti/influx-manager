<template>
  <b-nav-item-dropdown class="nav-menu" no-caret>
    <template v-if="usuarioLogado" slot="button-content">
      <span class="avatar-initials">
        <template v-if="initials">{{ initials }}</template>
        <template v-else><font-awesome-icon icon="user" /></template>
      </span>
      <span class="sidebar-form">{{ usuarioLogado.nome }}</span>
    </template>

    <template v-if="usuarioLogado">
      <b-dropdown-header>{{ usuarioLogado.nome }}</b-dropdown-header>
      <b-dropdown-item @click="logout()"><font-awesome-icon icon="sign-out-alt" flip="horizontal"/> Sair</b-dropdown-item>
    </template>
    <template v-else>
      <b-dropdown-item to="/login"><i class="icon-login"></i> Login</b-dropdown-item>
    </template>
  </b-nav-item-dropdown>
</template>

<script>
import {mapActions, mapState} from 'vuex'

export default {
  name: 'HeaderDropdown',
  data () {
    return {
      user: {},
      initials: null
    }
  },
  computed: mapState('root', ['usuarioLogado']),
  watch: {
    usuarioLogado (value) {
      if (!value) {
        this.initials = ''
        return
      }

      const split = value.nome.split(' ')
      const initials = []
      split.map((part, index) => {
        if (index > 1) {
          return
        }

        initials.push(part[0])
      })
      this.initials = initials.join()
    }
  },
  methods: {
    ...mapActions('root', ['fazerLogout']),
    logout () {
      this.fazerLogout()
        .then(() => {
          this.$router.push('/login')
        })
    }
  }
}
</script>

<style scoped>
.mobile .avatar-initials,
.sidebar .sidebar-navigator .avatar-initials {
  color: #eee;
  border: 1px solid transparent;
  border-radius: 100%;
}
</style>
