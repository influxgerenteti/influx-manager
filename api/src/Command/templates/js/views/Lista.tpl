<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label for="situacao_filtro_rapido" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-radio-group
                  id="situacao_filtro_rapido"
                  v-model="selected" :options="situacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="situacao_filtro_rapido"
                  @change="setSituacao"
                />
              </div>
            </div>
          </div>
        </form>
      </b-collapse>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaRapida=false, buscaAvancada = true, filtrar()">
          <div class="form-group row">
            <div class="col-md-12">
              <label for="situacao_filtro_avancado" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-radio-group
                  id="situacao_filtro_avancado"
                  v-model="selected" :options="situacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="situacao_filtro_avancado"
                />
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="">Campos</th>
            <th data-column=""><!-- Situação --> </th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-if="!listaItens.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            
            <tr v-for="item in listaItens" :key="item.id" @dblclick="editar(item.id)">
              <td data-label="Campos">{{ item.id }}</td>
              <td data-label=" ">
                <!-- Situação -->
              </td>
              <td class="d-flex coluna-icones">
                <!-- Ações -->
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
  name: 'Lista$(nomeModulo)',
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
    ...mapState('$(nomeModuloSensitiveCase)', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.selected = 0
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.filtrar()
    this.listarCamposSelects()
  },
  methods: {
    ...mapActions('$(nomeModuloSensitiveCase)', {listarItens: 'listar'}),
    ...mapMutations('$(nomeModuloSensitiveCase)', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY']),


    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      //TODO: Se necessario filtro de Select Preencher
    },

    setSituacao (value) {
      this.selected = value
      this.filtrar()
    },

    limparStateAnterior () {
      //TODO: Adicionar os Mutations do modulo para retornar os valores para nulo
    },

    executaFiltroRapido () {
      //TODO: Adicionar os states de filtro Rapido
    },

    executaFiltroAvancado () {
      //TODO: Adicionar os states de filtro Rapido
    },

    filtrar () {
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else {
        this.executaFiltroAvancado()
      }
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    limparFiltros () {
      //TODO: Adicionar os states para realizar a limpeza do filtro
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    editar (id) {
      
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
