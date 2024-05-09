<template>
  <div class="app animated fadeIn">
    <template>
      <loading
        :show="show"
        :label="label"/>
    </template>
    <AppHeader/>
    <!-- <Modal /> -->
    <div class="app-body">
      <Sidebar :nav-items="listaMenus" :nav-favoritos="listaFavoritos" />
      <main class="main">
        <Alerta />
        <Toast />
        <Modal />

        <breadcrumb />

        <div class="container-fluid">
          <router-view :key="$route.path" />
        </div>

        <b-modal id="unlock-request" ref="unlock-request" v-model="unlockRequestModal" size="md" centered no-close-on-backdrop hide-header hide-footer>
          <form class="needs-validation" novalidate @submit.prevent="unlockRequest()">

            <h5 class="login-title">Campo bloqueado</h5>
            <p class="login-subtitle">Insira credenciais com permissão de acesso para continuar.</p>

            <b-alert v-if="mensagemErro" show variant="danger border-0">
              {{ mensagemErro }}
            </b-alert>

            <b-input-group class="mb-3">
              <b-input-group-prepend><b-input-group-text><font-awesome-icon icon="user" /></b-input-group-text></b-input-group-prepend>
              <input v-model="unlock_login" type="text" class="form-control login-form" name="unlock_login" autocomplete="off" placeholder="CPF" required autofocus>
            </b-input-group>

            <b-input-group class="mb-3">
              <b-input-group-prepend><b-input-group-text><font-awesome-icon icon="lock" /></b-input-group-text></b-input-group-prepend>
              <input v-model="unlock_senha" type="password" name="unlock_senha" class="form-control login-form" autocomplete="off" placeholder="Senha" required>
              <div class="invalid-feedback">Informe sua senha!</div>
            </b-input-group>

            <b-btn :disabled="$v.unlock_login.$invalid || $v.unlock_senha.$invalid || botaoDesabilitado" type="submit" variant="roxo" class="btn btn-roxo">
              <i v-if="formSubmetido" class="fa fa-refresh fa-spin"></i>
              <template v-if="!formSubmetido">Desbloquear</template>
            </b-btn>
            <b-btn variant="link" @click="resetForm()" >Cancelar</b-btn>

          </form>
        </b-modal>
      </main>
      <AppAside/>
    </div>
    <div  class="versao"><span > {{ versao }}</span></div>
    <!-- <AppFooter/> -->
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

import {Header as AppHeader, Sidebar, Aside as AppAside, Footer as AppFooter, Breadcrumb, Alerta, Toast, Modal} from '../components/'
import EventBus from './../utils/event-bus'
import loading from 'vue-full-loading'
import {required} from 'vuelidate/lib/validators'

export default {
  name: 'Full',
  components: {
    AppHeader,
    Sidebar,
    AppAside,
    AppFooter,
    Breadcrumb,
    Alerta,
    Toast,
    loading,
    Modal
  },

  data () {
    return {
      show: false,
      label: 'Carregando...',
      versao: '1.0.0.0',
      unlockRequestModal: false,
      unlock_login: '',
      unlock_senha: '',
      formSubmetido: false,
      botaoDesabilitado: false,
      acao_sistema: null,
      input_locker: null,
      cardPersonal: null
    }
  },

  validations: {
    unlock_login: {required},
    unlock_senha: {required}
  },

  computed: {
    ...mapState('root', ['listaMenus', 'listaFavoritos', 'permissaoModuloId']),
    ...mapState('login', ['mensagemErro']),

    name () {
      return this.$route.name
    }
  },

  created () {
    this.versao = process.env.VUE_APP_VERSION
    /* EventBus.$on('carregarPagina', load => {
      // console.log(load)
      this.show = load.show
      // this.carregarPagina(alerta)
    }) */
    EventBus.$on('unlockRequestModal', item => {
      this.input_locker = item
      this.acao_sistema = item.acao_sistema
      this.unlockRequestModal = item.show
      this.callBack = item.callBack
      this.personal = item.personal
    })
  },

  methods: {
    ...mapActions('permissao', ['verificaPermissaoModulo']),
    ...mapMutations('login', ['setMensagemErro']),

    unlockRequest () {
      if (this.$v.$invalid) {
        return
      }

      this.formSubmetido = true
      this.botaoDesabilitado = true

      const data = {
        cpfEmail: this.unlock_login,
        senha: this.unlock_senha,
        modulo: this.permissaoModuloId,
        acao_sistema: this.acao_sistema
      }

      this.setMensagemErro(null)

      this.verificaPermissaoModulo(data).then(resposta => {
        this.unlock_login = ''
        this.unlock_senha = ''
        this.acao_sistema = null

        this.formSubmetido = false
        this.botaoDesabilitado = false

        if (this.personal) {
          const card = [...document.getElementsByName(this.personal.getAttribute('name'))]
          card.map(c => { c.unlocked = true })
        }

        const $ = document.querySelectorAll.bind(document)

        let checkboxList = $(`.unlock-checkbox[data-id="${this.input_locker.dataId}"]`)
        let inputList = $(`.unlock-input[data-id="${this.input_locker.dataId}"]`)

        let unlockList = [...checkboxList, ...inputList]

        for (let i = unlockList.length; i--;) {
          const unlocked = unlockList[i]

          const child = unlocked.lastElementChild
          child.lockedPass = false
          child.disabled = false

          unlocked.classList.add('unlocked')

          if (this.callBack) { this.callBack.input_locker_callback = resposta }
        }

        this.unlockRequestModal = false
        EventBus.$emit('modal:reabrir-modal')
      }).catch(response => {
        this.formSubmetido = false
        this.botaoDesabilitado = false
        this.setMensagemErro(response.body.mensagem)
      })
    },

    resetForm () {
      this.unlockRequestModal = false
      this.formSubmetido = false
      this.unlock_login = ''
      this.unlock_senha = ''
      EventBus.$emit('modal:reabrir-modal')
    }
  }
}
</script>
<style scoped>
.app-body{
  max-height: 100vh;
}
</style>
