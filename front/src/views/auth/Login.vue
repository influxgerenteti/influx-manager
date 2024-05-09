<template>
  <div class="app flex-row align-items-center animated fadeIn login-bg">
    <div class="container">
      <b-row class="justify-content-center">
        <b-col md="5">
          <b-card no-body class="p-4 bloco-card mb-0">
            <b-card-body>
              <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="submit()">
                <h1 class="login-title">Login</h1>
                <p class="login-subtitle">Entre com sua conta</p>

                <b-alert v-if="mensagemErro" show variant="danger border-0">
                  {{ mensagemErro }}
                </b-alert>

                <b-input-group class="mb-3">
                  <b-input-group-prepend><b-input-group-text><font-awesome-icon icon="user" /></b-input-group-text></b-input-group-prepend>
                  <input v-model="inputEmailCPF" type="text" class="form-control login-form" name="inputEmailCPF" placeholder="CPF" required autofocus>
                  <div class="invalid-feedback">Campo obrigatório, preencha corretamente!</div>
                </b-input-group>

                <b-input-group class="mb-3">
                  <b-input-group-prepend><b-input-group-text><font-awesome-icon icon="lock" /></b-input-group-text></b-input-group-prepend>
                  <input v-model="inputSenha" type="password" name="password" class="form-control login-form" placeholder="Senha" required>
                  <div class="invalid-feedback">Informe sua senha!</div>
                </b-input-group>

                <b-row>
                  <b-col cols="6">
                    <!-- <div id="login-captcha" class="g-recaptcha" data-sitekey="6Le1BpAbAAAAALr452qf9bml1fs3ul7CEgjvnDnN" data-size="invisible"></div> -->
                    <b-btn :disabled="botaoDesabilitado" type="submit" variant="primary" class="btn btn-verde px-4 width-100">
                      <i v-if="formSubmetido" class="fa fa-refresh fa-spin"></i>
                      <template v-if="!formSubmetido">Entrar</template>
                    </b-btn>

                  </b-col>
                  <b-col cols="6" class="align-self-center text-right">
                    <router-link to="/recuperar-senha" variant="link" class="px-0 login-alink">Esqueceu a senha?</router-link>
                  </b-col>
                </b-row>
              </form>
            </b-card-body>
          </b-card>

          <div class="logo-influx"></div>

        </b-col>
      </b-row>
    </div>

    <b-modal ref="modalFranqueadas" :title="quantidadeFranqueadasAtivas > 0 ? 'Escolha uma franqueada' : 'Nenhuma franqueada disponível'" hide-footer no-close-on-backdrop hide-header-close no-close-on-esc>
      <template v-if="quantidadeFranqueadasAtivas > 0">
        <b-form-group label="A sua franqueada padrão encontra-se inativa. Por favor, selecione uma das franqueadas abaixo para continuar:">
          <b-form-radio-group v-model="franqueada" name="radioFranqueada" stacked>
            <b-form-radio v-for="franq in franqueadas" :key="franq.id" :value="franq.id" :disabled="franq.situacao !== 'A'">
              {{ franq.nome }}
              <span v-if="franq.id === dadosUsuario.franqueada_padrao.id" class="badge rounded badge-cinza">Padrão</span>
            </b-form-radio>
          </b-form-radio-group>
        </b-form-group>

        <b-btn class="btn-azul" @click="setFranqueada()">Confirmar</b-btn>
      </template>
      <template v-else>
        <p>Entre em contato com a inFlux Franchising para normalizar o cadastro.</p>

        <b-btn class="btn-azul" @click="$refs.modalFranqueadas.hide()">Fechar</b-btn>
      </template>
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import { required } from 'vuelidate/lib/validators'

export default {
  name: 'Login',
  data () {
    return {
      inputEmailCPF: null,
      inputSenha: null,
      botaoDesabilitado: false,
      formSubmetido: false,
      isValid: true,
      dadosUsuario: null,
      franqueadas: [],
      quantidadeFranqueadasAtivas: null,
      franqueada: null,
      tentativas: window.localStorage.getItem('tentativas')
    }
  },
  validations: {
    inputEmailCPF: {required},
    inputSenha: {required}
  },
  computed: mapState('login', ['emailCPF', 'senha', 'mensagemErro']),
  watch: {
    inputEmailCPF (value) {
      this.setEmailCPF(value)
    },

    inputSenha (value) {
      this.setSenha(value)
    }

  },
  created () {
    // setTimeout(function () {
    //   grecaptcha.render('login-captcha')
    // }, 2000)
  },
  methods: {
    ...mapActions('login', ['doLogin']),
    ...mapActions('root', ['salvarLogin']),
    ...mapMutations('login', ['setEmailCPF', 'setSenha', 'setMensagemErro']),
    ...mapMutations('franqueadas', {setListaFranqueadas: 'SET_LISTA', setFranqueadaSelecionada: 'setFranqueadaSelecionada'}),

    resetForm () {
      this.formSubmetido = false
      this.inputEmailCPF = null
      this.inputSenha = null
    },

    limparListasVuex () {
      // Função executada para limpar todas as listas e resetar conteúdos preenchidos antes de proceder com o login
      const mutations = {
        paginaAtual: [],
        totalItens: [],
        listas: []
      }

      Object.keys(this.$store._mutations).forEach(mutation => {
        if (mutation.includes('SET_PAGINA_ATUAL')) {
          mutations.paginaAtual.push(mutation)
        }

        if (mutation.includes('SET_TOTAL_ITENS')) {
          mutations.totalItens.push(mutation)
        }

        if (mutation.includes('SET_LISTA')) {
          mutations.listas.push(mutation)
        }
      })

      mutations.paginaAtual.forEach(mutation => this.$store.commit(mutation, 1))
      mutations.totalItens.forEach(mutation => this.$store.commit(mutation, 0))
      mutations.listas.forEach(mutation => this.$store.commit(mutation, []))
    },

    login (usuario) {
      if (usuario.franqueada_padrao && usuario.franqueada_padrao.id) {
        usuario.franqueadaSelecionada = usuario.franqueada_padrao.id
      } else {
        usuario.franqueadaSelecionada = usuario.franqueadas.length ? usuario.franqueadas[0].id : null
      }

      this.limparListasVuex()

      this.setListaFranqueadas(usuario.franqueadas)
      this.setFranqueadaSelecionada(usuario.franqueadaSelecionada)
      this.$store.commit('root/setFranqueadaSelecionada', usuario.franqueadaSelecionada)

      this.salvarLogin(usuario)
        .then(() => {
          this.$router.push('/dashboard')
        })
    },

    submit (event) {
      this.tentativas = window.localStorage.getItem('tentativas')

      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      // if (Boolean(Number(this.tentativas)) && grecaptcha.getResponse() === '') {
      //   this.setMensagemErro(null)
      //   // grecaptcha.execute()
      //   return
      // }

      window.localStorage.setItem('tentativas', 0)
      this.tentativas = window.localStorage.getItem('tentativas')

      this.formSubmetido = true
      this.botaoDesabilitado = true
      this.setMensagemErro(null)

      this.doLogin()
        .then(resposta => {
          // console.log("rb:",resposta.body)
          this.formSubmetido = false
          this.botaoDesabilitado = false

          this.dadosUsuario = resposta.body.corpo.usuario
          if (this.dadosUsuario.franqueada_padrao.situacao !== 'I') {
            this.login(this.dadosUsuario)
            return
          }

          this.$refs.modalFranqueadas.show()
          this.franqueadas = this.dadosUsuario.franqueadas
          this.quantidadeFranqueadasAtivas = this.franqueadas.filter(item => item.situacao === 'A').length
          window.localStorage.setItem('tentativas', 0)
          this.tentativas = window.localStorage.getItem('tentativas')
        })
        .catch(erro => {
          console.error(erro)
          this.formSubmetido = false
          this.botaoDesabilitado = false
          this.setMensagemErro(erro.body.mensagem)
          window.localStorage.setItem('tentativas', 1)
          this.tentativas = window.localStorage.getItem('tentativas')
        })
    },

    setFranqueada () {
      this.$store.commit('root/setFranqueadaSelecionada', this.franqueada.id)
      this.$refs.modalFranqueadas.hide()

      this.dadosUsuario.franqueadaSelecionada = this.franqueada

      this.salvarLogin(this.dadosUsuario)
      
    }
  }
}

</script>

<style scoped>
.login-title {
  /* color: #fbfdff; */
  /* color: #4387cc; */
  color: #290e72b8;
}

.login-subtitle {
  color: #708BE0;
}

/* @media screen and (min-width: 800px) {
  .login-bg {
    background-image: url('../../../img/background/nuvem-bg03.png');
    background-repeat: repeat-x;
    background-size: contain;
    background-position: bottom;
  }
} */

.login-bg {
  /* background-color: #a4d1f0; */
  /* background-color: #daf0ff; */
  background-color: #fbfdff;
  /* background-color: #2d4899; */
}

.bloco-card {
  background-color: transparent;
}

.login-form {
  background-color: #c5e0f6;
}

.input-group-text {
  color: #fff;
  border: 0;
  background-color: #91a7ff;
}

.login-submit {
  border: 0;
  background-color: #72cf01;
}
.login-submit:hover,
.login-submit:focus {
  background-color: #449d44;
}
.login-submit:active:hover {
  background-color: #398439;
}

.logo-influx {
  margin: 0 auto 1.5em;
 /*  width: 97.5px;
  height: 85px; */
  width: 117.5px;
  height: 100px;
  background-repeat: no-repeat;
  background-image: url('../../assets/img/logo/logo_INFLUX-1.png');
  /* background-size: 97.5px; */
  background-size: 117.5px;
}

.login-alink:hover {
  text-decoration: none;
}

.invalid-feedback {
  /* position: relative; */
  bottom: -18px;
  right: 0;
}

.badge-cinza {
  background-color: #c2cfd6;
  color: #888;
}
</style>
