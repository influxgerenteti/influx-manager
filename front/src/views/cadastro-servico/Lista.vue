<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div>
        </div>
        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" :to="`${$route.path}/adicionar`" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">
            <!-- <b-col md="3">
              <label v-help-hint="'filtroRapido-franqueada'" for="franqueada" class="col-form-label">Franquia</label>
              <g-select id="franqueada"
                        :multi-tag="true"
                        :value="franqueada"
                        :options="listaFranquias"
                        :select="setFranqueada"
                        class="multiselect-truncate g-multiselect-tag"
                        label="nome"
                        track-by="id" />
            </b-col>
            !-->

            <b-col md="3">
              <label v-help-hint="'filtroRapido-cadastro_de_alunos'" for="descricao" class="col-form-label">Descrição</label>
              <input id="descricao" v-model="filtros.descricao" type="text" class="form-control" @change="filtrar()">
            </b-col>

            <b-col md="3">
              <label v-help-hint="'filtroRapido-tipo_servico'" for="tipo_servico" class="col-form-label">Tipo serviço</label>
              <g-select id="tipoServico"
                        :value="tipo_servico"
                        :options="listaTipoServico"
                        :select="setTipoServico"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"/>
            </b-col>

            <b-col md="3">
              <label v-help-hint="'filtroRapido_situacao'" for="situacao" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group id="situacao" v-model="selectedAvancados" :options="situacao" buttons button-variant="cinza" name="situacao" class="checkbtn-line" @change="setSituacao"/>
              </div>
            </b-col>

          </div>

        </form>
      </b-collapse>

      <!-- <b-collapse id="filtros-avancados" v-model="filtroAvancado">
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
      </b-collapse> -->
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <!--<th data-column="">Franqueada</th> !-->
            <th data-column="">Descrição</th>
            <th data-column="">Tipo de serviço</th>
            <th data-column="">Valor do serviço</th>
            <th data-column=""><!-- Situação  --></th>
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
              <!--<td data-label="Franqueada">{{ item.franqueada.nome }}</td> !-->
              <td data-label="Descrição">{{ item.descricao }}</td>
              <td data-label="Tipo de serviço">{{ item.tipo_item.descricao }}</td>
              <td data-label="Valor do serviço">{{ item.itemFranqueadas.length ? item.itemFranqueadas[0].valor_venda : 0 | formatarMoeda }}</td>

              <td data-column="Situação">
                <!-- Situação -->
                <span v-b-tooltip.viewport.left.hover :title="situacao.find(situacao => item.situacao == situacao.value).text" :class="'circle-badge-' + item.situacao.toLowerCase()" class="circle-badge"></span>
              </td>

              <td class="d-flex coluna-icones">
                <!-- Ações -->
                <b-btn v-b-modal.modalAtualizarValor variant="link" class="icone-link" title="Atualizar valor do serviço para esta franquia" @click="atualizarValorItem = { id: item.id, valor_venda: Number(item.itemFranqueadas.length ? item.itemFranqueadas[0].valor_venda : 0) }">
                  <font-awesome-icon icon="dollar-sign" />
                </b-btn>

                <router-link v-b-tooltip.viewport.left.hover v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :to="`${$route.path}/atualizar/${item.id}`" class="icone-link" title="Atualizar">
                  <font-awesome-icon icon="pen" />
                </router-link>
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

    <b-modal id="modalAtualizarValor" ref="modalAtualizarValor" size="md" centered no-close-on-backdrop hide-header hide-footer>
      <form @submit="submitAtualizarValor($event)">
        <div>
          <label v-help-hint="'form-cadastro_de_servico_valor_servico'" for="valor_servico" class="col-form-label">Valor do serviço</label>
          <g-numeric id="valor_servico" v-model="atualizarValorItem.valor_venda" class="form-control" />
        </div>

        <div class="form-group pt-2">
          <b-btn :disabled="atualizandoValorServico" type="submit" variant="verde">Salvar</b-btn>
          <b-btn variant="link" @click="$refs.modalAtualizarValor.hide()">Cancelar</b-btn>
        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
// import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaCadastroServico',
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      selected: 0,
      selectedAvancados: [],
      situacao: [
        {text: 'Ativo', value: 'A'},
        {text: 'Inativo ', value: 'I'}
      ],

      filtros: {
        descicao: '',
        tipo_servico: '',
        franqueada: []
      },

      tipo_servico: '',
      franqueada: [],

      atualizarValorItem: {
        id: null,
        valor_venda: null
      },
      atualizandoValorServico: false
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('franqueadas', { listaFranquias: 'listaFranqueada' }),
    ...mapState('tipoItem', { listaTipoItemRequisicao: 'lista' }),
    ...mapState('cadastroServico', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaTipoServico: {
      get () {
        return [{descricao: 'Selecione', tipo: ''}, ...this.listaTipoItemRequisicao]
      }
    }

  },

  mounted () {
    this.selectedAvancados = ['A']
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listarCamposSelects()
  },

  methods: {
    ...mapActions('cadastroServico', {listarItens: 'listar', atualizarValorServico: 'atualizarValorServico'}),
    ...mapMutations('cadastroServico', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY',
      'SET_FILTRO_DESCRICAO', 'SET_FILTRO_SITUACAO', 'SET_FILTRO_TIPO_ITEM', 'SET_FILTRO_FRANQUEADA']),

    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      this.$store.commit('tipoItem/SET_PAGINA_ATUAL', 1)
      this.$store.commit('tipoItem/SET_FILTROS', {tipo: ['V', 'S', 'M', 'VP32', 'VP48', 'VP64', 'AE', 'R', 'MT', 'MF', 'RM', 'RF', 'VPA']})
      this.$store.dispatch('tipoItem/listar')

      this.$store.commit('franqueadas/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('franqueadas/getListaFranqueada')
        .then(() => {
          this.setFranqueada(this.listaFranquias.find(f => f.id === this.$store.state.root.usuarioLogado.franqueadaSelecionada))
        })
    },

    setTipoServico (value) {
      this.tipo_servico = value.id ? value : null
      this.filtros.tipo_servico = value.id ? value : null
      this.filtrar()
    },

    setFranqueada (value) {
      const index = this.franqueada.indexOf(value)
      if (index === -1) {
        this.franqueada.push(value)
        this.filtros.franqueada.push(value ? value.id : null)
        this.filtrar()
        return
      }

      this.franqueada.splice(index, 1)
      this.filtros.franqueada.splice(index, 1)
      this.filtrar()
    },

    limparStateAnterior () {
      this.SET_FILTRO_DESCRICAO(null)
      this.SET_FILTRO_TIPO_ITEM(null)
      this.SET_FILTRO_SITUACAO([])
    },

    executaFiltroRapido () {
      let tpItemString = this.filtros.tipo_servico && this.filtros.tipo_servico.id ? this.filtros.tipo_servico.tipo : null
      this.SET_FILTRO_DESCRICAO(this.filtros.descricao)
      this.SET_FILTRO_TIPO_ITEM(tpItemString)
      this.SET_FILTRO_SITUACAO(this.selectedAvancados)
      this.SET_FILTRO_FRANQUEADA(this.filtros.franqueada)
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
      this.listarItens()
    },

    setSituacao (value) {
      this.selectedAvancados = value
      this.filtrar()
    },

    limparFiltros () {
      // TODO: Adicionar os states para realizar a limpeza do filtro
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    editar (id) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        event.preventDefault()
        this.$router.push(`${this.$route.path}/atualizar/${id}`)
      }
    },

    submitAtualizarValor ($event) {
      $event.preventDefault()
      this.atualizandoValorServico = true
      this.atualizarValorServico({ ...this.atualizarValorItem, filtro_franqueada: this.filtros.franqueada })
        .then(() => {
          this.$refs.modalAtualizarValor.hide()
          this.SET_PAGINA_ATUAL(1)
          this.SET_LISTA([])
          this.filtrar()
        })
        .catch(console.error)
        .finally(() => {
          this.atualizandoValorServico = false
        })
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
