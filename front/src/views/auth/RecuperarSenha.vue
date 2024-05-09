<template>
  <div class="app flex-row align-items-center animated fadeIn">
    <div class="container">
      <b-row class="justify-content-center">
        <b-col md="5">
          <b-card no-body class="p-4">
            <b-card-body>
              <form @submit.prevent="submit()">
                <h1>Recuperar senha</h1>
                <p class="text-muted">Informe seu e-mail e lhe enviaremos instruções de recuperação de acesso à conta.</p>

                <b-alert v-if="mensagemSucesso" show variant="success">
                  {{ mensagemSucesso }}
                </b-alert>

                <b-alert v-if="mensagemErro" show variant="danger">
                  {{ mensagemErro }}
                </b-alert>

                <b-input-group class="mb-3">
                  <b-input-group-prepend><b-input-group-text><i class="icon-user"></i></b-input-group-text></b-input-group-prepend>
                  <input v-model="inputEmail" type="email" name="email" class="form-control" placeholder="E-mail" required autofocus>
                </b-input-group>

                <b-row>
                  <b-col cols="6">
                    <b-button :disabled="botaoDesabilitado" type="submit" variant="primary" class="px-4 width-100">
                      <i v-if="formSubmetido" class="fa fa-refresh fa-spin"></i>
                      <template v-if="!formSubmetido">Enviar</template>
                    </b-button>
                  </b-col>
                  <b-col cols="6" class="align-self-center text-right">
                    <router-link to="/login" variant="link" class="px-0">Login</router-link>
                  </b-col>
                </b-row>
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
import {mapActions, mapState, mapMutations} from 'vuex'

export default {
  name: 'RecuperarSenha',
  data () {
    return {
      inputEmail: '',
      botaoDesabilitado: false,
      formSubmetido: false
    }
  },
  computed: mapState('recuperarSenha', ['email', 'mensagemErro', 'mensagemSucesso']),
  watch: {
    inputEmail (value) {
      this.setEmail(value)
    }
  },
  methods: {
    ...mapActions('recuperarSenha', ['enviarEmail']),
    ...mapMutations('recuperarSenha', ['setEmail']),

    resetForm () {
      this.inputEmail = ''
      this.formSubmetido = false
      this.botaoDesabilitado = false
    },

    submit () {
      this.formSubmetido = true
      this.botaoDesabilitado = true

      this.enviarEmail()
        .then(() => {
          this.formSubmetido = false
          this.botaoDesabilitado = false
          this.resetForm()
        })
        .catch(() => {
          this.formSubmetido = false
          this.botaoDesabilitado = false
        })
    }
  }
}
</script>
