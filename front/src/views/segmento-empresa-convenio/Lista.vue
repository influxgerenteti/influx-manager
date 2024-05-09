<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" aria-controls="filtros-rapidos" aria-expanded="true" class="btn filtro-selecionado" @click="filtroRapido = !filtroRapido, className = filtroRapido ? 'filtro-open' : null, filtroSelecionado = !filtroSelecionado">
            Filtros
          </div>
        </div>
        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" :to="`${$route.path}/adicionar`" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false, filtrar()">
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label for="descricao_filtro" class="col-form-label">Descrição</label>
              <input id="descricao_filtro" v-model="descricaoFiltro" type="text" class="form-control" >
            </div>
          </div>
          <button type="submit" class="btn btn-cinza btn-block text-uppercase mt-3" @click="filtroRapido=false">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="sec.descricao">Descrição</th>
            <th data-column="sec.situacao" class="coluna-situacao">Situação</th>
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
              <td data-label="Situação" class="coluna-situacao">
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
  name: 'ListaSegmentoEmpresaConvenio',
  data () {
    return {
      descricaoFiltro: '',
      className: null,
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: false,
      filtroSelecionado: 1
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('segmentoEmpresaConvenio', {listaItems: 'lista', estaCarregando: 'estaCarregando', todosItensCarregados: 'todosItensCarregados', totalItens: 'totalItens'}),
    permitirCarregarMais: {
      get () {
        return !!this.listaItems.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.filtrar()
    this.listarCamposSelects()
  },
  methods: {
    ...mapActions('segmentoEmpresaConvenio', {listarItems: 'listar', atualizarItem: 'atualizar'}),
    ...mapMutations('segmentoEmpresaConvenio', ['SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_FILTRO_DESCRICAO', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listarItems()
    },

    listarCamposSelects () {
      // TODO: Se necessario filtro de Select Preencher
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          return
        }
        this.$router.push(`/cadastros/segmento-empresa-convenio/atualizar/${item.id}`)
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

          this.atualizarItem(item)
            .then(() => {
              item.situacao = data.situacao
            })
            .catch(error => {
              console.error(error)
              // this.SET_PAGINA_ATUAL(1)
              // this.listarItems()
            })
        }
      }, `Deseja ${mensagem} "${item.descricao}" ?`)
    },

    limparStateAnterior () {
      this.SET_FILTRO_DESCRICAO(null)
    },

    executaFiltroRapido () {
      this.SET_FILTRO_DESCRICAO(this.descricaoFiltro)
    },

    executaFiltroAvancado () {
      // TODO: Adicionar os states de filtro Rapido
    },

    filtrar () {
      this.limparStateAnterior()
      this.executaFiltroRapido()
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItems()
    },

    limparFiltros () {
      this.SET_FILTRO_DESCRICAO(null)
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItems()
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
