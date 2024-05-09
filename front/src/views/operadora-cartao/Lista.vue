<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-2">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/cadastros/operadora-cartao/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="oc.descricao">Descrição</th>
            <th data-column="oc.tipo_operacao">Tipo de Operação</th>
            <th data-column="oc.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaItems.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <tr v-for="item in listaItems" :key="item.id" @dblclick="alterar(item)">
              <td data-label="Descrição">{{ item.descricao }}</td>
              <td data-label="Tipo de Operação">{{ item.tipo_operacao === 'C' ? 'Crédito' : 'Débito' }}</td>
              <td data-label="Situação">
                <div @click.prevent="inativar(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>
              <td class="d-flex coluna-icones">
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :class="item.situacao === 'A' ? null : 'disable-icon'" href="javascript:void(0)" class="icone-link" title="Atualizar" @click.prevent="alterar(item)">
                  <font-awesome-icon icon="pen" />
                </a>
              </td>
            </tr>
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
  name: 'ListaOperadoraCartao',
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      selected: 0,
      situacao: [
        {text: 'Situação A', value: '1'},
        {text: 'Situação B', value: '0'}
      ]
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('operadoraCartao', {listaItems: 'lista', estaCarregando: 'estaCarregando', todosItensCarregados: 'todosItensCarregados', totalItens: 'totalItens'}),
    permitirCarregarMais: {
      get () {
        return !!this.listaItems.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.selected = 0
    this.filtrar()
    this.listarCamposSelects()
  },
  methods: {
    ...mapActions('operadoraCartao', {listarItens: 'listar', atualizar: 'atualizar'}),
    ...mapMutations('operadoraCartao', ['SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_LISTA', 'SET_ORDER_BY']),

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItens()
    },

    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      // TODO: Se necessario filtro de Select Preencher
    },

    limparStateAnterior () {
      // TODO: Adicionar os Mutations do modulo para retornar os valores para nulo
    },

    executaFiltroRapido () {
      // TODO: Adicionar os states de filtro Rapido
    },

    executaFiltroAvancado () {
      // TODO: Adicionar os states de filtro Rapido
    },

    filtrar () {
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else {
        this.executaFiltroAvancado()
      }
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItens()
    },

    limparFiltros () {
      // TODO: Adicionar os states para realizar a limpeza do filtro
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          return
        }
        this.$router.push(`/cadastros/operadora-cartao/atualizar/${item.id}`)
      }
    },

    inativar (item) {
      const mensagem = item.situacao === 'A' ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_ITEM_SELECIONADO_ID(item.id)

          const data = Object.assign({}, item)
          data.situacao = item.situacao === 'A' ? 'I' : 'A'
          this.SET_ITEM_SELECIONADO(data)

          this.atualizar(item)
            .then(() => {
              item.situacao = data.situacao
            })
            .catch(error => {
              console.error(error)
            })
        }
      }, `Deseja ${mensagem} "${item.descricao}"?`)
    }
  }
}
</script>
<style scoped>
span.badge {
  font-size: 95%;
}

#filtros-rapidos,
#filtros-avancados {
  transition: all .1s;
}

.filtro-avancado .form-group {
  margin-bottom: 1rem;
}
.filtro-header {
  color: #4a4a4a;
}
.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151B1E;
  background-color: #fff;
}
.filtro-avancado .input-group-text {
  border: 0;
  background-color: #E5E5E5;
}

.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

.datepicker {
  padding: 0;
}

.table-actions {
  padding-top: .5rem;
  padding-bottom: .5rem;
  color: #3e515b;
  border-top: 1px solid #EBECF0;
}
</style>
