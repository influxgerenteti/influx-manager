<template>
  <div class="animated fadeIn">
    <!-- <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div> -->

    <div class="body-sector info-view">

      <div class="row p-3">
        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Descrição</label>
              <span class="col-md-8">{{ itemSelecionado.descricao || '--' }}</span>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Quantidade de parcelas</label>
              <span class="col-md-8">{{ itemSelecionado.quantidade_parcelas || '--' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="itemSelecionado.quantidade_parcelas > 0" class="form-group p-3 mb-0">
        <h5 class="title-module pb-3">Parcelas</h5>

        <b-table :items="itemSelecionado.condicao_pagamento_parcelas" :fields="fields" class="table-scroll table b-table table-hover table-borderless"/>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <b-btn variant="link" @click="voltar()">Voltar</b-btn>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
export default {
  data () {
    return {
      fields: [
        {key: 'numero_parcela', label: 'Número'},
        {key: 'dias_vencimento', label: 'Dias de vencimento'},
        {key: 'percentual_parcela', label: 'Percentual'}
      ]
    }
  },
  computed: mapState('condicaoPagamento', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
    } else {
      this.voltar()
    }
  },
  methods: {
    ...mapMutations('condicaoPagamento', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO']),
    ...mapActions('condicaoPagamento', ['buscar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/condicao-pagamento')
    }
  }
}
</script>
<style scoped>
.table-scroll {
  height: calc(100vh - 320px);
  height: -webkit-calc(100vh - 320px);
  height: -moz-calc(100vh - 320px);
  position: relative;
}
</style>
