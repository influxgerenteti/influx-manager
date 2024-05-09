<template>
  <div>


    <router-view v-if="usuarioCarregado === true" />    
    <div v-else class="d-flex vh-100">
      <load-placeholder :loading="true" />
    </div>
    <DialogRoot />
  </div>
</template>

<script>
/* eslint-disable  */
import Vue from 'vue'
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from './utils/event-bus'
import IndexedDB from './utils/indexed-db'
import {rotasAbertas, rotasErros} from './utils/router-config'
import DialogRoot from './views/DialogRoot'
export default {
  name: 'App',

  data () {
    return {
      usuarioCarregado: false
    }
  },
  components: {    
    DialogRoot
  },

  computed: mapState('root', ['usuarioLogado', 'menuCarregado']),

  watch: {
    usuarioLogado (value) {
      const rotaAtual = this.$route.fullPath
      if (value && value.usuario_acesso) {
        Vue.http.headers.common['Authorization'] = `Bearer ${value.usuario_acesso.token_acesso}`

        /*
        if (value.forca_troca_senha === true) {
          this.$router.push('/redefinir-senha')
          EventBus.$emit('usuarioLogado', true)
          return
        }
        */
        if (rotaAtual === '/login') {
           this.$router.push('/dashboard')
        }

        EventBus.$emit('usuarioLogado', true)
        if (this.menuCarregado === false && rotasAbertas.find(rota => rota !== '/' && !rotaAtual.includes(rota))) {
          // this.menuCarregado = true
          this.listarMenus().then(() => {
            this.setMenuCarregado(true)
            EventBus.$emit('toastNotificacoes')
          })
        }

        return
      }

      if (rotasAbertas.find(rota => rota !== '/' && rotaAtual.includes(rota))) {
        return
      }

      const erro = rotasErros[401]
      this.setMensagemErro(erro ? erro.mensagem : null)
      this.fazerLogout()
        .then(() => {
          this.$store.commit('root/setListaMenus', [])
          this.$store.commit('root/setListaFavoritos', [])
          this.$router.push(erro.redirecionar)
          this.setMenuCarregado(false)
          // this.menuCarregado = false
        })
        .catch(error => {
          console.error(error)
          this.$router.push(erro.redirecionar)
        })
    }
  },

  created () {
    EventBus.$on('usuario-logado::validar-acesso', () => {
      this.validarAcessoUsuario()
    })

    IndexedDB.open('influx-manager').then(() => {
      IndexedDB.retrieveFirst('usuarioLogado')
        .then(usuario => {
          if (usuario && !usuario.franqueadaSelecionada) {
            usuario.franqueadaSelecionada = usuario.franqueada_padrao.id
          }

          this.setUsuarioLogado(usuario)
          this.usuarioCarregado = true
          this.getParametrosFranqueadora()
        })
        .catch(error => {
          console.log(error)
          this.setUsuarioLogado(null)
          this.usuarioCarregado = true
          console.error(error)
        })
    })
  },

  methods: {
    ...mapActions('root', ['listarMenus', 'fazerLogout', 'validarAcessoUsuario']),
    ...mapActions('parametrosFranqueadora', {getParametrosFranqueadora: 'getItem'}),
    ...mapMutations('root', ['setUsuarioLogado', 'setMenuCarregado']),
    ...mapMutations('parametrosFranqueadora', ['SET_LISTA']),
    ...mapMutations('login', ['setMensagemErro'])
  }
}
</script>

<style lang="scss">
  // Import Main styles for this application
  @import './assets/css/app';
</style>
