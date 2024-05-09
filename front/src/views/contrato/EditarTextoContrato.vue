<template>
  <form :class="{ 'was-validated': true }" class="needs-validation p-3" novalidate>
    <div class="body-sector ">
      <div class="animated fadeIn p-3">
        <div class="form-group">
          <editor-texto v-model="contrato_html"/>
        </div>
      </div>
      <div class="form-group pt-2">
        <b-btn variant="verde" class="mr-1" @click="salvarEAbrirContrato()">Imprimir contrato</b-btn>
        <b-btn variant="link" @click="voltar()">Fechar</b-btn>
      </div>
    </div>
  </form>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import open from '../../utils/open'

export default {
  name: 'EditarTextoContrato',
  props: {
    contratoId: {
      type: Number,
      default: 0
    },

    modeloContratoId: {
      type: Number,
      default: 0
    }
  },

  data () {
    return {
    }
  },

  computed: {
    ...mapState('root', ['usuarioLogado']),
    ...mapState('contrato', ['textoContrato']),
    contrato_html: {
      get () {
        return this.textoContrato
      },
      set (value) {
        this.SET_TEXTO_CONTRATO(value)
      }
    }
  },

  mounted () {
  },

  methods: {
    ...mapActions('contrato', ['buscarTextoContrato', 'atualizarTextoContrato']),
    ...mapMutations('contrato', ['SET_TEXTO_CONTRATO']),

    getTextoContrato () {
      const data = {
        contratoId: this.contratoId,
        modeloContratoId: this.modeloContratoId
      }
      this.buscarTextoContrato(data).then(texto => {
        this.contrato_html = texto
      })
    },

    salvarEAbrirContrato () {
      this.atualizarTextoContrato().then(res => {
        this.imprimir()
        this.voltar()
      })
    },

    imprimir () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const rota = this.$route.matched[0].path
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const contratoId = this.contratoId
      const modeloContratoId = this.modeloContratoId
      const url = `/api/contrato/imprimir/${contratoId}?Authorization=${auth}&URLModulo=${rota}&franqueada=${franqueada}&modelo_contrato=${modeloContratoId}`
      
      // open(url, '_blank')
      var host = process.env.VUE_APP_HOST;
      window.open(`${host}${url}`, '_blank') 
    },

    voltar () {
      this.$emit('voltar')
    }

  }
}
</script>
<style scoped>
</style>
