<template>
  <form v-if="!!dadosLancamento" :class="{ 'was-validated': !estaValido }" class="needs-validation" novalidate @submit.prevent="salvar()">
    <b-row class="form-group">
      <b-col md="4">
        <label class="col-form-label">Nº lançamento</label>
        <span class="form-control form-control-disabled">{{ dadosLancamento.id }}</span>
      </b-col>

      <b-col md="4">
        <label class="col-form-label">Data lançamento</label>
        <span class="form-control form-control-disabled">{{ dadosLancamento.data_contabil | formatarData }}</span>
      </b-col>

      <b-col md="4">
        <label for="estornar_data_estorno" class="col-form-label">Data de estorno *</label>
        <g-datepicker :class-name="!estaValido && !itemEstornar.data_estorno ? 'invalid-input' : 'valid-input'" :element-id="'estornar_data_estorno'" :value="itemEstornar.data_estorno" :selected="setDataEstorno" :max-date="(new Date()).toISOString()" required />
        <div v-if="!estaValido && !itemEstornar.data_estorno" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>
    </b-row>

    <b-row class="form-group">
      <b-col md="4">
        <label class="col-form-label">Tipo de movimentação</label>
        <span v-b-tooltip.bottom :title="dadosLancamento.forma_pagamento.descricao" class="form-control form-control-disabled text-truncate">{{ dadosLancamento.forma_pagamento.descricao }}</span>
      </b-col>

      <b-col md="4">
        <label class="col-form-label">Valor</label>
        <span class="form-control form-control-disabled">{{ dadosLancamento.valor_lancamento | formatarMoeda }}</span>
      </b-col>
    </b-row>

    <div class="form-group">
      <label for="estornar_observacao" class="col-form-label">Observação</label>
      <textarea id="estornar_observacao" v-model="itemEstornar.observacao" class="form-control" placeholder="Detalhes da movimentação" rows="3" maxlength="150"></textarea>
    </div>

    <div>
      <b-btn :disabled="salvando" type="submit" variant="verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</b-btn>
      <b-btn type="button" variant="link" @click="$emit('fechar')">Cancelar</b-btn>
    </div>
  </form>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import {dateToString} from '../../utils/date'
import EventBus from '../../utils/event-bus'

export default {
  data () {
    return {
      estaValido: true,
      salvando: false,
      dadosLancamento: null
    }
  },

  validations: {
    itemEstornar: {
      data_estorno: {required},
      observacao: {required}
    }
  },

  computed: {
    ...mapState('movimentacaoConta', {itemEstornar: 'estornar'})
  },

  mounted () {
    EventBus.$on('form-estornar:abrir', (item) => {
      this.dadosLancamento = item
      this.itemEstornar.lancamento = item.id
      this.itemEstornar.observacao = `Estorno de ${item.observacao || 'lançamento'} (nº ${item.id})`
      this.setDataEstorno(dateToString(new Date()))
    })
  },

  methods: {
    ...mapActions('movimentacaoConta', ['estornar']),

    salvar () {
      this.estaValido = false

      if (this.$v.$invalid) {
        return
      }

      this.salvando = true

      this.estornar()
        .then(() => {
          this.$emit('filtrar')
          this.$emit('fechar')
          EventBus.$emit('form-filtros:buscar-contas')
        })
        .finally(() => {
          this.salvando = false
        })
    },

    setDataEstorno (value) {
      this.itemEstornar.data_estorno = value
    }
  }
}
</script>
