<template>
  <div class="app flex-row align-items-center animated fadeIn">
    <div class="container">
      <b-row class="justify-content-center">
        <b-col md="5">
          <b-card no-body class="p-4">
            <b-card-body>
              <form @submit.prevent="submit()">
                <h1>Criar senha</h1>
                <p class="text-muted">Insira sua nova senha e a confirmação.</p>

                <b-alert v-if="mensagemSucesso" show variant="success">
                  {{ mensagemSucesso }}<br>
                  <router-link to="/login">Clique aqui para fazer login</router-link>
                </b-alert>

                <b-alert v-if="mensagemErro" show variant="danger">
                  {{ mensagemErro }}
                </b-alert>

                <b-input-group class="mb-3">
                  <b-input-group-prepend><b-input-group-text><i class="icon-key"></i></b-input-group-text></b-input-group-prepend>
                  <input v-model="inputSenha" type="password" name="password" class="form-control" placeholder="Senha" required autofocus>
                </b-input-group>

                <b-input-group class="mb-3">
                  <b-input-group-prepend><b-input-group-text><i class="icon-key"></i></b-input-group-text></b-input-group-prepend>
                  <input v-model="inputConfirmarSenha" type="password" name="confirmPassword" class="form-control" placeholder="Confirmar senha" required>
                </b-input-group>

                <b-button :disabled="botaoDesabilitado" type="submit" variant="primary" class="px-4 width-100">
                  <i v-if="formSubmetido" class="fa fa-refresh fa-spin"></i>
                  <template v-if="!formSubmetido">Enviar</template>
                </b-button>
              </form>
            </b-card-body>
          </b-card>

          <div class="logo-gatilabs"></div>

        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'

export default {
  name: 'CriarSenha',
  data () {
    return {
      inputSenha: '',
      inputConfirmarSenha: '',
      tokenValido: false,
      formSubmetido: false,
      botaoDesabilitado: true
    }
  },
  computed: mapState('criarSenha', ['senha', 'confirmarSenha', 'token', 'mensagemErro', 'mensagemSucesso']),
  watch: {
    inputSenha (value) {
      this.setSenha(value)
    },
    inputConfirmarSenha (value) {
      this.setConfirmarSenha(value)
    }
  },
  created () {
    this.setToken(this.$route.query.token)

    this.validarToken()
      .then(() => {
        this.botaoDesabilitado = false
        this.tokenValido = true
      })
      .catch(() => {
        this.setMensagemErro('Token inválido.')
      })
  },
  methods: {
    ...mapMutations('criarSenha', ['setSenha', 'setConfirmarSenha', 'setToken', 'setMensagemSucesso', 'setMensagemErro']),
    ...mapActions('criarSenha', ['validarToken', 'registrarSenha']),

    resetForm () {
      this.inputSenha = ''
      this.inputConfirmarSenha = ''
      this.formSubmetido = false
    },

    submit () {
      this.formSubmetido = true
      this.botaoDesabilitado = true

      if (this.tokenValido === false) {
        this.setMensagemErro('Token inválido.')
        this.formSubmetido = false
        return
      }

      this.registrarSenha()
        .then(resposta => {
          this.formSubmetido = false
          this.botaoDesabilitado = false
          this.setMensagemSucesso(resposta.body.mensagem)
          this.resetForm()
        })
        .catch(erro => {
          this.formSubmetido = false
          this.botaoDesabilitado = false
          this.setMensagemErro(erro.body.mensagem)
        })
    }
  }
}
</script>
