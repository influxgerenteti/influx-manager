<template>
  <form :class="{ 'was-validated': !estaValido }" class="needs-validation" novalidate @submit.prevent="salvar()">
    <b-row class="form-group">
      <b-col md="6">
        <label v-help-hint="'form-transferir_conta_origem'" for="transferir_conta_origem" class="col-form-label">Conta de Origem *</label>
        <g-select
          id="transferir_plano_conta"
          :class="!estaValido && !transferir.conta_origem ? 'invalid-input' : 'valid-input'"
          :value="transferir.conta_origem"
          :select="setContaOrigem"
          :options="listaContas"
          class="multiselect-truncate"
          label="descricao"
        />
        <div v-if="!estaValido && !transferir.conta_origem" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>

      <b-col md="6">
        <label v-help-hint="'form-transferir_conta_destino'" for="transferir_conta_destino" class="col-form-label">Conta de Destino *</label>
        <g-select
          id="transferir_conta_destino"
          :class="!estaValido && !transferir.conta_destino ? 'invalid-input' : 'valid-input'"
          :value="transferir.conta_destino"
          :select="setContaDestino"
          :options="listaContas"
          class="multiselect-truncate"
          label="descricao"
        />
        <div v-if="!estaValido && !transferir.conta_destino" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>
    </b-row>

    <div v-if="!podeSalvar && transferir.conta_origem && transferir.conta_destino && transferir.conta_origem.id === transferir.conta_destino.id" class="alert alert-danger form-group">As contas de origem e destino devem ser diferentes.</div>

    <b-row class="form-group">
      <b-col md="6">
        <label v-help-hint="'form-transferir_valor'" for="transferir_valor_lancamento" class="col-form-label">Valor *</label>
        <vue-numeric id="transferir_valor_lancamento" :class="!estaValido && !transferir.valor_lancamento ? 'invalid-input' : 'valid-input'" :precision="2" :empty-value="null" v-model="transferir.valor_lancamento" :max="9999999.99" separator="." class="form-control" required />
        <div v-if="!estaValido && !transferir.valor_lancamento" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>

      <b-col md="6">
        <label v-help-hint="'form-transferir_data_lancamento'" for="transferir_data_contabil" class="col-form-label">Data de movimento *</label>
        <g-datepicker :class="!estaValido && !transferir.data_contabil ? 'invalid-input' : 'valid-input'" :element-id="'transferir_data_contabil'" :value="transferir.data_contabil" :selected="setDataContabil"/>
        <div v-if="!estaValido && !transferir.data_contabil" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>
    </b-row>

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
import {dateToString} from '../../utils/date'

export default {
  data () {
    return {
      estaValido: true,
      podeSalvar: true,
      salvando: false
    }
  },

  computed: {
    ...mapState('movimentacaoConta', ['transferir']),
    ...mapState('conta', {listaContas: 'lista'}),

    contaSelecionada: {
      get () {
        return this.$store.state.movimentacaoConta.filtros.conta
      }
    }
  },

  validations: {
    transferir: {
      conta_origem: {required},
      conta_destino: {required},
      data_contabil: {required},
      valor_lancamento: {required}
    }
  },

  watch: {
    contaSelecionada (value) {
      this.setContaOrigem(value)
    }
  },

  mounted () {
    EventBus.$on('form-transferir:abrir', (item) => {
      this.estaValido = true
      this.setDataContabil(dateToString(new Date()))
    })
  },

  methods: {
    ...mapActions('movimentacaoConta', {efetuarTransferencia: 'transferir'}),

    salvar () {
      this.salvando = true
      this.estaValido = false

      if (this.$v.$invalid) {
        this.salvando = false
        return false
      }

      this.efetuarTransferencia()
        .then(() => {
          this.$emit('filtrar')
          this.$emit('fechar')
          EventBus.$emit('form-filtros:buscar-contas')
        })
        .finally(() => {
          this.salvando = false
        })
    },

    setDataContabil (value) {
      this.transferir.data_contabil = value
    },

    setContaOrigem (value) {
      this.podeSalvar = !(value && this.transferir.conta_origem && value.id === this.transferir.conta_origem.id)

      this.transferir.conta_origem = value
    },

    setContaDestino (value) {
      this.podeSalvar = !(value && this.transferir.conta_origem && value.id === this.transferir.conta_origem.id)

      this.transferir.conta_destino = value
    }
  }
}
</script>
