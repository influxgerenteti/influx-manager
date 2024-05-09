<template>
  <form :class="{ 'was-validated': !estaValido }" class="needs-validation" novalidate @submit.prevent="salvar()">
    <div class="form-group">
      <label for="transferir_conta_destino" class="col-form-label">Conta de Destino *</label>
      <g-select
        id="transferir_conta_destino"
        :class="!estaValido && !transferirExistente.conta_destino ? 'invalid-input' : 'valid-input'"
        :value="transferirExistente.conta_destino"
        :select="setContaDestino"
        :options="listaContas"
        class="multiselect-truncate"
        label="descricao"
      />
      <div v-if="!estaValido && !transferirExistente.conta_destino" class="multiselect-invalid">Campo obrigatório</div>
    </div>

    <div class="mt-3">
      <b-btn :disabled="salvando || !podeSalvar" type="submit" variant="verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</b-btn>
      <b-btn type="button" variant="link" @click="$emit('fechar')">Cancelar</b-btn>
    </div>
  </form>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'

export default {
  data () {
    return {
      estaValido: true,
      podeSalvar: true,
      salvando: false,
      contaOrigem: null
    }
  },

  computed: {
    ...mapState('movimentacaoConta', ['transferirExistente']),
    listaContas: {
      get () {
        return this.$store.state.conta.lista.filter(item => item.id !== this.contaOrigem)
      }
    }
  },

  validations: {
    transferirExistente: {
      conta_destino: {required}
    }
  },

  mounted () {
    EventBus.$on('form-transferir-existente:abrir', (item) => {
      this.transferirExistente.id = item.id
      this.contaOrigem = item.conta.id
      this.estaValido = true
    })
  },

  methods: {
    ...mapActions('movimentacaoConta', ['efetuarTransferenciaExistente']),

    salvar () {
      this.salvando = true
      this.estaValido = false

      if (this.$v.$invalid) {
        this.salvando = false
        return false
      }

      this.efetuarTransferenciaExistente()
        .then(() => {
          this.$emit('filtrar')
          this.$emit('fechar')
          EventBus.$emit('form-filtros:buscar-contas')
        })
        .finally(() => {
          this.salvando = false
        })
    },

    setContaDestino (value) {
      this.podeSalvar = !(value && this.transferirExistente.conta_origem && value.id === this.transferirExistente.conta_origem.id)

      this.transferirExistente.conta_destino = value
    }
  }
}
</script>
