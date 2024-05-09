<template>
  <div class="animated fadeIn">

    <div v-if="objCheque && objCheque.id" class="body-sector info-view">
      <div class="row">
        <div class="col-md-6">
          <label for="titular_text" class="col-form-label">Titular</label>
          <span id="titular_text" class="d-block">{{ objCheque.titular }}</span>
        </div>
        <div class="col-md-6">
          <label for="numero_cheque_text" class="col-form-label">Número do cheque</label>
          <span id="numero_cheque_text" class="d-block">{{ objCheque.numero }}</span>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3">
          <label for="banco_text" class="col-form-label">Banco</label>
          <span id="banco_text" class="d-block">{{ objCheque.banco.descricao }}</span>
        </div>
        <div class="col-md-3">
          <label for="agencia_text" class="col-form-label">Agência</label>
          <span id="agencia_text" class="d-block">{{ objCheque.agencia }}</span>
        </div>
        <div class="col-md-6">
          <label for="conta_text" class="col-form-label">Conta</label>
          <span id="conta_text" class="d-block">{{ objCheque.conta }}</span>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <label for="complemento_text" class="col-form-label">Complemento</label>
          <span id="complemento_text" class="d-block">{{ objCheque.complemento }}</span>
        </div>
        <div class="col-md-6">
          <label for="valor_text" class="col-form-label">Valor</label>
          <span id="valor_text" class="d-block">{{ objCheque.valor | formatarMoeda }}</span>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3">
          <label for="data_entrada_text" class="col-form-label">Data de Entrada</label>
          <span id="data_entrada_text" class="d-block">{{ objCheque.data_entrada | formatarData }}</span>
        </div>
        <div class="col-md-3">
          <label for="data_bom_para_text" class="col-form-label">Bom para</label>
          <span id="data_bom_para_text" class="d-block">{{ objCheque.data_bom_para | formatarData }}</span>
        </div>
        <div class="col-md-3">
          <label for="data_baixa_text" class="col-form-label">Data de baixa</label>
          <span v-if="objCheque.data_baixa" id="data_baixa_text" class="d-block">{{ objCheque.data_baixa | formatarData }}</span>
        </div>
        <div class="col-md-3">
          <label for="situacao_texto" class="col-form-label">Situação</label>
          <template v-if="objCheque.situacao === 'B'">
            <span id="situacao_texto" class="d-block badge date-success align-middle rounded">Baixado</span>
          </template>
          <template v-else-if="objCheque.situacao === 'D'">
            <span id="situacao_texto" class="d-block badge date-warning align-middle rounded">Devolvido</span>
          </template>
          <template v-else-if="objCheque.situacao === 'P'">
            <span id="situacao_texto" class="d-block badge date-danger align-middle rounded">Pendente</span>
          </template>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <label for="observacao_text" class="col-form-label">Observação</label>
          <span id="observacao_text" class="d-block">{{ objCheque.observacao }}</span>
        </div>
      </div>
    </div>

    <div class="mt-3">
      <b-btn variant="link" @click="voltar()">Voltar</b-btn>
    </div>

  </div>
</template>

<script>
import {mapActions, mapMutations, mapState} from 'vuex'

export default {
  name: 'InfoChequePagarReceber',
  data () {
    return {
    }
  },
  computed: {
    ...mapState('chequesPagarReceber', ['objCheque', 'estaCarregando'])
  },
  mounted () {
    this.SET_CHEQUE_SELECIONADO(this.$route.params.id)
    this.getChequesPagarReceber()
  },
  methods: {
    ...mapActions('chequesPagarReceber', ['getChequesPagarReceber']),
    ...mapMutations('chequesPagarReceber', ['SET_CHEQUE', 'SET_CHEQUE_SELECIONADO']),

    voltar () {
      this.$emit('cancel')
    }

  }
}
</script>
