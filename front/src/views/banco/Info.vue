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
              <label class="col-form-label col-md-4">Código</label>
              <span class="col-md-8">{{ itemSelecionado.codigo }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Descrição</label>
              <span class="col-md-8">{{ itemSelecionado.descricao }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <router-link :to="`/configuracoes/banco/atualizar/${itemSelecionadoID}`" class="btn btn-roxo">
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
  computed: mapState('banco', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
    }
  },
  methods: {
    ...mapMutations('banco', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO']),
    ...mapActions('banco', ['buscar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/banco')
    }
  }
}
</script>
