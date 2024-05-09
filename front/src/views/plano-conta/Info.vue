<template>
  <div class="animated fadeIn">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div class="body-sector info-view">
      <div class="list-group-accent">
        <div v-if="itemSelecionado.situacao === 'A'" class="list-group-item-success p-3">Ativo</div>
        <div v-else class="list-group-item-danger p-3">Inativo</div>
      </div>

      <div class="row p-3">
        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Descrição</label>
              <span class="col-md-8">{{ itemSelecionado.descricao || '--' }}</span>
            </div>

            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Pai</label>
              <span class="col-md-8">{{ itemSelecionado.pai ? itemSelecionado.pai.descricao : 'Não tem' }}</span>
            </div>

            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Tipo de movimento</label>
              <span class="col-md-8">{{ itemSelecionado.tipo_movimento_nota === 'E' ? 'Entrada' : 'Saída' }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <router-link v-if="itemSelecionado.franqueada !== undefined" :to="`/configuracoes/plano-conta/atualizar/${itemSelecionadoID}`" class="btn btn-roxo">
          <font-awesome-icon icon="pen" /> Atualizar
        </router-link>
        <b-btn variant="link" @click="voltar()">Voltar</b-btn>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
export default {
  computed: mapState('planoConta', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
    }
  },
  methods: {
    ...mapMutations('planoConta', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO']),
    ...mapActions('planoConta', ['buscar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/plano-conta')
    }
  }
}
</script>
