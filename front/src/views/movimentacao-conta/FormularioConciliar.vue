<template>
  <form @submit.prevent="salvar()">
    <b-row class="form-group">
      <b-col md="6">
        <label v-help-hint="'form-conciliar_data_lancamento'" for="conciliar_data_contabil" class="col-form-label">Data de lançamento *</label>
        <g-datepicker :element-id="'conciliar_data_contabil'" :value="dadosConciliarVarios.data_contabil" :selected="setDataContabil"/>
        <div class="invalid-feedback">Campo obrigatório</div>
      </b-col>

      <b-col md="6">
        <label v-help-hint="'form-conciliar_data_deposito'" for="conciliar_data_deposito" class="col-form-label">Data de depósito *</label>
        <g-datepicker :element-id="'conciliar_data_deposito'" :value="dadosConciliarVarios.data_deposito" :selected="setDataDeposito"/>
        <div class="invalid-feedback">Campo obrigatório</div>
      </b-col>
    </b-row>

    <div>
      <b-btn :disabled="salvando" type="submit" variant="verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</b-btn>
      <b-btn type="button" variant="link" @click="$emit('fechar')">Cancelar</b-btn>
    </div>
  </form>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import EventBus from '../../utils/event-bus'
import {dateToString} from '../../utils/date'

export default {
  data () {
    return {
      salvando: false
    }
  },

  computed: {
    ...mapState('movimentacaoConta', ['dadosConciliarVarios'])
  },

  mounted () {
    EventBus.$on('form-conciliar:abrir', (selected) => {
      const data = {}
      data.data_deposito = dateToString(new Date())
      data.data_contabil = dateToString(new Date())
      data.ids = selected.map(item => item.id)

      this.$store.commit('movimentacaoConta/SET_CONCILIAR_VARIOS', Object.assign({}, this.dadosConciliarVarios, data))
    })
  },

  methods: {
    ...mapActions('movimentacaoConta', ['conciliarVarios']),

    salvar () {
      this.salvando = true

      this.conciliarVarios()
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
      this.dadosConciliarVarios.data_contabil = value
    },

    setDataDeposito (value) {
      this.dadosConciliarVarios.data_deposito = value
    }
  }
}
</script>
