<template>
  <div class="animated fadeIn">
    <table class="table-scroll mobile-cards table b-table table-hover table-borderless">
      <thead>
        <tr>
          <th>Código</th>
          <th>Descrição</th>
        </tr>
      </thead>
      <tbody>
        <perfect-scrollbar>
          <tr v-for="item in lista" :key="item.id">
            <td>{{ item.codigo }}</td>
            <td>{{ item.descricao }}</td>
          </tr>
          <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
            <p>Nenhum resultado encontrado.</p>
          </div>
        </perfect-scrollbar>
      </tbody>
    </table>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'ListaTipoDocumento',
  computed: mapState('tipoDocumento', ['lista', 'totalItens', 'estaCarregando', 'todosItensCarregados']),
  created () {
    this.SET_PAGINA_ATUAL(1)
    this.listar()
  },
  methods: {
    ...mapActions('tipoDocumento', {listar: 'getLista'}),
    ...mapMutations('tipoDocumento', ['SET_PAGINA_ATUAL'])
  }
}
</script>

<style scoped>
.table-scroll {
  height: calc(100vh - 90px);
  height: -webkit-calc(100vh - 90px);
  height: -moz-calc(100vh - 90px);
}

@media (max-width: 991.98px) {
  .table-scroll {
    height: calc(100vh - 149px);
    height: -webkit-calc(100vh - 149px);
    height: -moz-calc(100vh - 149px);
  }
}
</style>
