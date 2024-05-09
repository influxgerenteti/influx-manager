<template>
   <b-nav-item-dropdown class="nav-menu dropdown nav-franqueada scroll-menu" dropright dropup no-caret>
    <template boundary="window" slot="button-content">
      <span><font-awesome-icon icon="building" class="franqueada-select-icon" /></span>

      <div class="truncate franqueada-ativa">
        <span class="sidebar-form franqueada-select">{{ franqueadaSelecionada }}</span>
      </div>

      <span v-if="!!permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false)" class="franqueada-config" @click.stop.prevent="configurarFranqueada()">
        <font-awesome-icon icon="cog" class="franqueada-config-icon" />
      </span>
 
    </template>
      <template v-for="(franqueada, index) in listaFranqueada">
        <b-dropdown-item v-if="franqueada && franqueada.situacao === 'A'" :key="index" class="dropdown-item " @click="selecionarFranqueada(franqueada, franqueada.id !== usuarioLogado.franqueadaSelecionada)">{{ franqueada.nome }}</b-dropdown-item>
      </template>
  </b-nav-item-dropdown>

</template>

<script>
import {mapActions, mapState} from 'vuex'

export default {
  name: 'HeaderFranqueada',
  data () {
    return {
      franqueadaSelecionada: '',
      listaFranqueada: [],
      permissoes: {}
    }
  },

  computed: {
    ...mapState('root', {usuarioLogado: 'usuarioLogado', franqueada: 'franqueadaSelecionada'})
  },

  watch: {
    usuarioLogado (value) {
      if (!value) {
        return
      }

      this.consultaInformacoesFranqueada()
    }
  },

  created () {
    this.consultaInformacoesFranqueada()

    this.$store.dispatch('modulos/buscarPermissaoPorModulo', { URLModulo: '/configuracoes/franqueada', commitMutation: false })
      .then((modulo) => {
        if (!modulo || !modulo.permissoes) {
          this.permissoes = {}
          return
        }

        const perm = {}
        modulo.permissoes.forEach(permissao => {
          if (permissao.possui_permissao) {
            perm[permissao.descricao] = permissao
          }
        })

        this.permissoes = perm
      })
  },

  methods: {
    ...mapActions('root', ['fazerLogout']),

    consultaInformacoesFranqueada () {
      if (this.usuarioLogado && this.usuarioLogado.franqueadas) {
        this.listaFranqueada = this.usuarioLogado.franqueadas
        const franqueadaFiltrada = this.usuarioLogado.franqueadas.find(item => item.id === this.usuarioLogado.franqueadaSelecionada)
        const franqueada = Object.assign({}, franqueadaFiltrada || this.usuarioLogado.franqueada_padrao)

        if (franqueada.situacao !== 'A') {
          this.fazerLogout()
          return
        }

        if (this.usuarioLogado.franqueadaSelecionada !== franqueada.id) {
          this.selecionarFranqueada(franqueada)
        } else {
          this.franqueadaSelecionada = franqueada.nome
        }
      }
    },

    limparFiltros() {
      this.$store.commit('contasReceber/LIMPAR_FILTROS')
      this.$store.commit('movimentacaoConta/LIMPAR_FILTROS')
    },

    selecionarFranqueada (franqueada, reloadPage = false) {
      this.$store.commit('root/setFranqueadaSelecionada', franqueada.id)
      this.franqueadaSelecionada = franqueada ? franqueada.nome : null

      this.limparFiltros()
      const usuario = Object.assign({}, this.usuarioLogado)
      usuario.franqueadaSelecionada = franqueada.id
      this.$store.dispatch('root/salvarLogin', usuario)
        .then(() => {
          if (reloadPage === true) {
            //this.$router.go('/dashboard')
            this.$router.push({path: '/dashboard', replace: true}).catch(()=>{});
            // this.$router.pop('/dashboard')
          }
        })
    },

    configurarFranqueada () {
      const franqueadaId = this.usuarioLogado.franqueadaSelecionada
      this.$router.push(`/configuracoes/franqueada/atualizar/${franqueadaId}`)
    }
  }
}
</script>

<style scoped>
.nav-franqueada:hover .franqueada-select,
.nav-franqueada .franqueada-ativa:hover .franqueada-select,
.franqueada-config:hover {
  color: #e5dbff;
}

.nav-link .franqueada-select-icon,
.nav-link .franqueada-config {
  font-size: medium;
}

.franqueada-config {
  padding-left: 0.5rem;
}

.franqueada-ativa {
  display: inline-block;
  margin-bottom: -5px;
}

.mobile .franqueada-ativa {
  display: none;
}

.mobile .franqueada-config {
  padding-left: 0;
  display: inline-block;
  height: 30px;
  width: 30px;
  line-height: 30px;
}

</style>
