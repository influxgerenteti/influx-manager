<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-4">
      <router-link to="/cadastros/categoria/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <table id="listaCategorias" class="table-scroll mobile-cards table b-table table-hover table-borderless">
      <thead>
        <tr>
          <th>Nome</th>
          <th class="coluna-icones"></th>
        </tr>
      </thead>

      <tbody>
        <!-- implementar o scroll infinito como em outras telas -->
        <perfect-scrollbar>
          <div v-if="!listaCategorias.length && !estaCarregando" class="busca-vazia">
            <p>Nenhum resultado encontrado.</p>
          </div>
          <tr v-for="categoria in listaCategorias" :key="categoria.id">
            <td data-label="Nome">{{ categoria.nome }}</td>

            <td class="d-flex coluna-icones">
              <a href="javascript:void(0)" title="Atualizar" class="icone-link" @click.prevent="alterarCategoria(categoria)">
                <font-awesome-icon icon="pen" />
              </a>

              <a href="javascript:void(0)" title="Remover" class="icone-link text-muted" @click.prevent="inativarCategoria(categoria)">
                <font-awesome-icon icon="trash-alt" />
              </a>
            </td>
          </tr>
        </perfect-scrollbar>
      </tbody>

      <label class="table-count">{{ listaCategorias.length }} itens na lista.</label>
    </table>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaCategoria',
  computed: mapState('categorias', ['listaCategorias', 'estaCarregando', 'todosItensCarregados']),
  created () {
    this.SET_PAGINA_ATUAL(1)
    this.listar()
  },
  methods: {
    ...mapActions('categorias', {listar: 'getListaCategorias', getCategoria: 'getCategoria', removerCategoria: 'removerCategoria'}),
    ...mapMutations('categorias', ['setCategoria', 'setCategoriaSelecionada', 'SET_PAGINA_ATUAL']),

    alterarCategoria (itemCategoria) {
      this.setCategoria(itemCategoria)
      this.$router.push(`/cadastros/categoria/alterar/${itemCategoria.id}`)
    },

    inativarCategoria (item) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.setCategoriaSelecionada(item.id)
          this.removerCategoria()
            .then(() => {
              this.SET_PAGINA_ATUAL(1)
              this.listar()
            })
        }
      }, `Deseja excluir "${item.nome}"?`)
    }
  }
}
</script>
<style scoped>
tbody td:first-child {
  flex-grow: 6;
}
tbody td:last-child {
  flex-grow: 1;
}
</style>
