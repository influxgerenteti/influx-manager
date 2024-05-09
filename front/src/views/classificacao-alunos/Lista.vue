<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-4">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/configuracoes/classificacao-aluno/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table id="listaClassificacaoAluno" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="ca.icone">Ícone</th>
            <th data-column="ca.nome">Descrição</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="classificacao_aluno in listaClassificacaoAluno" :key="classificacao_aluno.id" @dblclick="alterar(classificacao_aluno.id)">
              <td data-label="Ícone"><font-awesome-icon :icon="classificacao_aluno.icone"/></td>
              <td data-label="Descrição">{{ classificacao_aluno.nome }}</td>
              <td class="d-flex coluna-icones">
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="javascript:void(0)" title="Atualizar" class="icone-link" @click.prevent="atualizar(classificacao_aluno.id)">
                  <font-awesome-icon icon="pen" />
                </a>
                <a v-if="permissoes['EXCLUIR'] && (permissoes['EXCLUIR'].possui_permissao === true)" href="javascript:void(0)" title="Remover" class="icone-link text-muted" @click.prevent="inativar(classificacao_aluno)">
                  <font-awesome-icon icon="trash-alt" />
                </a>
              </td>
            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaClassificacaoAluno.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div></div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaClassificacaoAluno',
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('classificacaoAlunos', ['listaClassificacaoAluno', 'objClassificacaoAluno', 'estaCarregando', 'todosItensCarregados', 'totalItens']),
    permitirCarregarMais: {
      get () {
        return !!this.listaClassificacaoAluno.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listar()
  },
  methods: {
    ...mapActions('classificacaoAlunos', {listar: 'getListaClassificacaoAluno', removerClassificacaoAluno: 'removerClassificacaoAluno'}),
    ...mapMutations('classificacaoAlunos', ['SET_ITEM', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA', 'SET_CLASSIFICACAO_ALUNO_SELECIONADA']),

    carregarMais () {
      this.listar()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    atualizar (id) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.$router.push(`/configuracoes/classificacao-aluno/atualizar/${id}`)
      }
    },

    inativar (item) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_CLASSIFICACAO_ALUNO_SELECIONADA(item.id)
          this.removerClassificacaoAluno()
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
