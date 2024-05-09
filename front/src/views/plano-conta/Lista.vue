<template>
  <div class="animated fadeIn " style="max-height: 90vh;" >
    <div class="d-flex justify-content-end mb-4">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/configuracoes/plano-conta/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm ">
      <ul class="p-0">
        <tree v-for="filho in arvoreItens" :key="filho.id" :item="filho" />
      </ul>

      <div v-if="estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div></div>

      <div class="info-label d-flex flex-column align-items-end">
        <div style="min-height: 5vh;">
          <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import Tree from './Tree'

export default {
  components: { Tree },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('planoConta', ['arvoreItens', 'estaCarregando', 'totalItens'])
  },

  created () {
    this.SET_ARVORE_ITENS([])
    this.listar()
  },

  methods: {
    ...mapActions('planoConta', ['listar']),
    ...mapMutations('planoConta', ['SET_ARVORE_ITENS'])
  }
}
</script>
<style scoped>
.app{
  max-height: 100vh;
}
.scroll-menu{
  max-height: 93vh;
}
@media (max-height: 579px) {
  .scroll-menu{
  max-height: 80vh !important;
}
}
</style>