<template>
  <form :class="{ 'was-validated': !estaValido }" class="needs-validation" novalidate @submit.prevent="salvar()">
    <b-row class="form-group">
      <b-col md="6">
        <label class="col-form-label">Data de lançamento</label>
        <span class="form-control form-control-disabled">{{ itemSelecionado.data_movimento | formatarData }}</span>
      </b-col>

      <b-col md="6">
        <label class="col-form-label">Data de movimento</label>
        <span class="form-control form-control-disabled">{{ itemSelecionado.data_deposito | formatarData }}</span>
      </b-col>

      <b-col md="6">
        <label class="col-form-label">Valor</label>
        <span class="form-control form-control-disabled">{{ itemSelecionado.valor_lancamento | formatarNumero }}</span>
      </b-col>

      <b-col md="6">
        <label class="col-form-label">Nº documento</label>
        <span class="form-control form-control-disabled">{{ itemSelecionado.numero_documento }}</span>
      </b-col>

      <b-col md="6">
        <label class="col-form-label">Operação</label>
        <span class="form-control form-control-disabled">{{ itemSelecionado.operacao === 'C' ? 'Entrada' : 'Saída' }}</span>
      </b-col>

      <b-col md="6">
        <label class="col-form-label">Conciliado</label>
        <span class="form-control form-control-disabled">{{ itemSelecionado.conciliado === 'S' ? 'Sim' : 'Não' }}</span>
      </b-col>

      <b-col md="6">
        <label class="col-form-label">Tipo de movimentação</label>
        <span class="form-control form-control-disabled">{{ itemSelecionado.forma_pagamento ? itemSelecionado.forma_pagamento.descricao : '' }}</span>
      </b-col>
    </b-row>

    <div class="form-group">
      <label for="editar_observacao" class="col-form-label">Observação</label>
      <textarea id="editar_observacao" v-model="itemSelecionado.observacao" class="form-control" placeholder="Detalhes da movimentação" rows="3" maxlength="150"></textarea>
    </div>

    <div>
      <b-btn :disabled="salvando" type="submit" variant="verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</b-btn>
      <b-btn type="button" variant="link" @click="$emit('fechar')">Cancelar</b-btn>
    </div>
  </form>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'

export default {
  data () {
    return {
      estaValido: true,
      salvando: false
    }
  },

  computed: {
    ...mapState('movimentacaoConta', ['itemSelecionadoID', 'itemSelecionado'])
  },

  mounted () {
    EventBus.$on('form-editar:abrir', (item) => {
      this.SET_ITEM_SELECIONADO_ID(item.id)
      this.SET_ITEM_SELECIONADO(Object.assign({}, item))
      this.estaValido = true
    })
  },

  methods: {
    ...mapActions('movimentacaoConta', ['atualizar']),
    ...mapMutations('movimentacaoConta', ['SET_ITEM_SELECIONADO_ID', 'SET_ITEM_SELECIONADO']),

    salvar () {
      this.estaValido = false
      this.salvando = true

      this.atualizar()
        .then(() => {
          this.$emit('filtrar')
          this.$emit('fechar')
          EventBus.$emit('form-filtros:buscar-contas')
        })
        .finally(() => {
          this.salvando = false
        })
    }
  }
}
</script>
