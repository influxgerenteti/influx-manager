<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtros</div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">

            <b-col md="4">
              <label v-help-hint="'filtro_rapido-diario_personal_aluno'" for="aluno" class="col-form-label">Aluno</label>
              <typeahead id="nome_aluno" :item-hit="setAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
            </b-col>
            <b-col md="4">
              <label v-help-hint="'filtro_rapido-diario_personal_instrutor'" for="instrutor" class="col-form-label">Instrutor</label>
              <g-select :id="'instrutor'"
                        :select="setInstrutor"
                        :value="instrutor"
                        :options="listaDeFuncionario"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </b-col>
            <b-col md="2">
              <label v-help-hint="'filtro_rapido-diario_personal_data_aula'" for="data_aula" class="col-form-label">Data aula</label>
              <calendar :element-id="'data_aula'" v-model="data" @input="setData" />
            </b-col>

          </div>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="">Nome</th>
            <th data-column="">Instrutor</th>
            <th data-column="">Sala</th>
            <th data-column="">Livro</th>
            <th data-column="">Data/Hora</th>
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

            <tr v-for="item in listaItens" :key="item.id" @dblclick="editar(item.contrato.id)">
              <td data-label="Nome">{{ item.contrato.aluno ? item.contrato.aluno.pessoa.nome_contato : '' }}</td>
              <td data-label="Instrutor">{{ item.funcionario ? item.funcionario.apelido : '' }}</td>
              <td data-label="Sala">{{ item.sala_franqueada ? item.sala_franqueada.sala.descricao : '' }}</td>
              <td data-label="Livro">{{ item.contrato.livro ? item.contrato.livro.descricao : '' }}</td>
              <td data-label="Data/Hora">{{ dataInicioAgendamento(item) | formatarDataHora }}</td>
              <td class="d-flex coluna-icones">
                <!-- Ações -->
                <b-link :href="linkDiarioPersonal(item.contrato.id)" class="icone-link" title="Diário personal" @click="diarioPersonal(item.contrato.id)"><font-awesome-icon icon="book-open" /></b-link>
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
import {beginOfDay} from '../../utils/date'
import moment from 'moment'

export default {
  name: 'ListaDiarioPersonal',
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      listaSituacao: [
        {id: 'T', descricao: 'Todos'},
        {id: 'I', descricao: 'Iniciado'},
        {id: 'S', descricao: 'Sem registro'}
      ],

      aluno: null,
      instrutor: {id: null, apelido: 'Selecione'},
      // data: moment().format('DD/MM/YYYY'),
      data: moment().format('DD/MM/YYYY'),
      situacao: {id: 'T', descricao: 'Todos'}
    }
  },
  computed: {
    ...mapState('personal', {listaItensRequisicao: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),
    ...mapState('funcionario', {listaDeFuncionarioRequisicao: 'lista'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItensRequisicao.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaDeFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaDeFuncionarioRequisicao]
      }
    },

    listaItens: {
      get () {
        return Object.values(this.listaItensRequisicao.reduce((lista, item) => {
          if (!lista[item.contrato.id]) {
            lista[item.contrato.id] = item
          }
          return lista
        }, []))
      }
    }

  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.filtrar()
    this.listarCamposSelects()
  },
  methods: {
    ...mapActions('personal', {listarItens: 'listar'}),
    ...mapMutations('personal', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY', 'SET_FILTROS']),

    dataInicioAgendamento (item) {
      let reagendamento = false
      if (item.reagendado) {
        reagendamento = item.datasReagendamentoPersonals.find(reag => reag.ultimo_reagendamento === true)
      }
      if (!reagendamento) {
        return item.inicio || ''
      }
      return reagendamento.data_reagendada || ''
    },

    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      // TODO: Se necessario filtro de Select Preencher
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_LISTA', [])
      this.$store.commit('funcionario/SET_FILTROS', { instrutor_personal: true, consultor_ou_gestor_comercial: false })
      this.$store.dispatch('funcionario/listar')
      this.$store.commit('funcionario/SET_LIMPAR_FILTROS')
    },

    setAluno (value) {
      this.aluno = value
      this.filtrar()
    },

    setInstrutor (value) {
      this.instrutor = value
      this.filtrar()
    },

    setData (value) {
      if (!this.estaCarregando) {
        this.data = value
        if (!this.data || this.data.length === 10) {
          this.filtrar()
        }
      }
    },

    setSituacao (value) {
      this.situacao = value
      this.filtrar()
    },

    limparStateAnterior () {
      // TODO: Adicionar os Mutations do modulo para retornar os valores para nulo
    },

    executaFiltroRapido () {
      const filtros = {}

      filtros.aluno = this.aluno ? this.aluno.id : null
      filtros.funcionario = this.instrutor.id
      filtros.data = this.data ? beginOfDay(this.data) : null
      filtros.situacao = this.situacao ? this.situacao : null
      filtros.diario_personal = true

      this.SET_FILTROS(filtros)
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

    limparFiltros () {
      // TODO: Adicionar os states para realizar a limpeza do filtro
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    editar (id) {

    },

    diarioPersonal (id) {
      this.$router.push(this.linkDiarioPersonal(id))
    },

    linkDiarioPersonal (id) {
      return `${this.$route.path}/diario-personal/${id}`
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
