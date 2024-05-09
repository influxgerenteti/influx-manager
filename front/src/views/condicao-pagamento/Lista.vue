<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-4">
      <router-link to="/configuracoes/condicao-pagamento/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <table class="table-scroll mobile-cards table b-table table-hover table-borderless">
      <thead>
        <tr>
          <th>Descrição</th>
          <th>Quantidade de Parcelas</th>
          <th class="coluna-icones"></th>
        </tr>
      </thead>
      <tbody>
        <perfect-scrollbar>
          <tr v-for="item in lista" :key="item.id">
            <td data-label="Descrição">{{ item.descricao }}</td>
            <td data-label="Quantidade de Parcelas">{{ item.quantidade_parcelas }}</td>

            <td class="d-flex coluna-icones">
              <router-link :to="`/configuracoes/condicao-pagamento/info/${item.id}`" class="icone-link" title="Informações">
                <font-awesome-icon icon="eye" />
              </router-link>
            </td>
          </tr>
          <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
            <p>Nenhum resultado encontrado.</p>
          </div>
        </perfect-scrollbar>
      </tbody>

      <label class="table-count">{{ lista.length }} itens na lista.</label>
    </table>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  computed: mapState('condicaoPagamento', ['lista', 'estaCarregando', 'todosItensCarregados']),
  created () {
    this.SET_PAGINA_ATUAL(1)
    this.listar()
  },
  methods: {
    ...mapActions('condicaoPagamento', ['listar', 'atualizar']),
    ...mapMutations('condicaoPagamento', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID'])
  }
}
</script>
