<template>
  <div class="animated fadeIn">
    <template v-if="activePage === 'list'">
      <div class="filtro-avancado body-sector">
        <div class="d-flex justify-content-between filtro-header head-content-sector">
          <div>
            <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtros</div>
          </div>

          <b-btn variant="azul" @click="activePage = 'form'">
            <font-awesome-icon icon="plus" /> Adicionar
          </b-btn>
        </div>

        <b-collapse id="filtros-rapidos" v-model="filtroRapido">
          <form class="p-2" @submit.prevent="buscaRapida = true, filtrar()">
            <div class="form-group row mb-0">
              <div class="col-md-6">
                <label v-help-hint="'filtro-modal-turma_situacao_rapida'" for="situacao_rapido" class="col-form-label">Situação</label>
                <div>
                  <b-form-checkbox-group id="situacao_rapido" v-model="selectedRapidos" :options="situacao" :disabled="estaCarregando" buttons button-variant="cinza" name="situacao_rapido" class="checkbtn-line" @input="buscaRapida = true, filtrar()"/>
                </div>
              </div>
              <div class="col-md-6">
                <label v-help-hint="'filtro-modal-turma_descricao'" for="descricao" class="col-form-label">Descrição</label>
                <div class="d-flex">
                  <input id="descricao" v-model="descricaoTemp" type="text" class="form-control" maxlength="255">
                  <button :disabled="estaCarregando" type="submit" class="btn btn-azul">
                    <font-awesome-icon icon="search" />
                  </button>
                </div>
              </div>
            </div>
          </form>
        </b-collapse>
      </div>

      <div class="table-responsive bg-white">
        <g-table :sort="sortTable" :class="{'filtro-aberto': filtroRapido}" >
          <thead>
            <tr>
              <th data-column="t.descricao" style="flex: 0 0 250px;">Descrição</th>
              <th data-column="l.descricao">Livro</th>
              <th data-column="h.descricao" style="flex: 0 0 250px;">Horário</th>
              <th data-column="t.data_inicio">Início</th>
              <th data-column="t.data_fim">Término</th>
              <th data-column="t.maximo_alunos">Alunos/Vagas</th>
              <th class="coluna-icones"></th>
            </tr>
          </thead>
          <tbody>
            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <perfect-scrollbar class="scroller col-12" @ps-y-reach-end="permitirCarregarMais && carregarMais()">
              <tr v-for="item in lista" :key="item.turmaId">
                <td :title="item.turmaDescricao" style="flex: 0 0 250px;">{{ item.turmaDescricao }}</td>
                <td>{{ item.livroDescricao }}</td>
                <td :title="item.horarioDescricao" style="flex: 0 0 250px;">{{ item.horarioDescricao }}</td>
                <td>{{ item.dataInicioTurma | formatarData }}</td>
                <td>{{ item.dataFimTurma | formatarData }}</td>
                <td>{{ item.qtdContratoTurma + '/'+ item.maximoAlunos }}</td>

                <td class="d-flex coluna-icones">
                  <a href="javascript: void(0)" class="icone-link" @click="selecionarTurma(item.turmaId)">
                    <font-awesome-icon icon="hand-paper" />
                  </a>
                </td>
              </tr>
            </perfect-scrollbar>
          </tbody>
        </g-table>
      </div>
    </template>
    <template v-else>
      <formulario :is-modal="true" :filtrar-modalidade="filtrarModalidade" @resolve="selecionarTurma" @reject="activePage = 'list'" />
    </template>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {getDateFromISO, dateToCompare, beginOfDay, endOfDay} from '../../utils/date'
import Formulario from './Formulario.vue'

export default {
  name: 'TurmaListaModal',

  components: {
    Formulario
  },

  props: {
    isModal: {
      type: Boolean,
      required: true,
      default: false
    },
    filtrarModalidade: {
      type: Number,
      required: false,
      default: null
    }
  },

  data () {
    return {
      activePage: 'list',
      className: 'rapido-open',
      cursoSelecionado: null,
      modalidadeSelecionada: null,
      salaFranqueadaSelecionada: null,
      funcionarioSelecionado: null,
      horarioSelecionado: null,
      livroSelecionado: null,
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      descricaoTemp: '',
      semestreTemp: '',
      data_inicial: '',
      data_final: '',
      data_inicial_temporario: '',
      data_final_temporario: '',
      selectedRapidos: ['ABE', 'FOR'],
      selectedAvancados: [],
      listaTemporaria: [],
      situacao: [
        {text: 'Aberta', value: 'ABE'},
        {text: 'Em formação', value: 'FOR'},
        {text: 'Encerrada', value: 'ENC'}
      ],
      situacoes: {
        ABE: {descricao: 'Aberta', cor: 'success'},
        FOR: {descricao: 'Em formação', cor: 'info'},
        ENC: {descricao: 'Encerrada', cor: 'danger'}
      }
    }
  },

  computed: {
    ...mapState('turma', {listaOriginal: 'lista', estaCarregando: 'estaCarregando', filtros: 'filtros', todosItensCarregados: 'todosItensCarregados', paginaAtual: 'paginaAtual', listaTodaCarregada: 'listaTodaCarregada'}),
    ...mapState('livro', {listaLivros: 'lista'}),

    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    lista: {
      get () {
        let lista = this.listaOriginal
        if (this.filtrarModalidade) {
          lista = lista.filter((turma) => {
            return turma.modalidadeTurmaId === this.filtrarModalidade
          })
        }
        return lista
      }
    }
  },

  mounted () {
    this.$store.commit('livro/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('livro/listar')

    if (!this.estaCarregando) {
      this.filtrar()
    }
  },

  methods: {
    ...mapActions('turma', ['listar']),
    ...mapMutations('turma', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY']),

    getDateFromISO: getDateFromISO,

    dateToCompare: dateToCompare,

    resetModalTurma () {
      this.activePage = 'list'
    },

    setDataInicial (value) {
      this.data_inicial_temporario = value
    },

    setDataFinal (value) {
      this.data_final_temporario = value
    },

    setLivro (value) {
      this.livroSelecionado = value
    },

    limparFiltros () {
      this.semestreTemp = ''
      this.modalidadeSelecionada = this.listaModalidadesTurma[0]
      this.cursoSelecionado = this.listaCursos[0]
      this.salaFranqueadaSelecionada = this.listaSalasFranqueada[0]
      this.livroSelecionado = this.listaTemporaria[0]
      this.horarioSelecionado = this.listaHorarios[0]
      this.funcionarioSelecionado = this.listaFuncionarios[0]
      this.data_inicial_temporario = this.data_inicial
      this.data_final_temporario = this.data_final
    },

    limparStateAnterior () {
      this.$store.commit('turma/SET_FILTRO_SITUACAO', [])
      this.$store.commit('turma/SET_FILTRO_DESCRICAO', '')
      this.$store.commit('turma/SET_FILTRO_HORARIO', null)
      this.$store.commit('turma/SET_FILTRO_MODALIDADE_TURMA', null)
      this.$store.commit('turma/SET_FILTRO_SALA_FRANQUEADA', null)
      this.$store.commit('turma/SET_FILTRO_FUNCIONARIO', null)
      this.$store.commit('turma/SET_FILTRO_CURSO', null)
      this.$store.commit('turma/SET_FILTRO_LIVRO', null)
      this.$store.commit('turma/SET_FILTRO_SEMESTRE', null)
      this.$store.commit('turma/SET_FILTRO_DATA_INICIO', null)
      this.$store.commit('turma/SET_FILTRO_DATA_FIM', null)
    },

    executaFiltroRapido () {
      this.$store.commit('turma/SET_FILTRO_SITUACAO', this.selectedRapidos)
      this.$store.commit('turma/SET_FILTRO_DESCRICAO', this.descricaoTemp)
    },

    executaFiltroAvancado () {
      this.$store.commit('turma/SET_FILTRO_SITUACAO', this.selectedAvancados)
      this.$store.commit('turma/SET_FILTRO_DESCRICAO', this.descricaoTemp)
      this.$store.commit('turma/SET_FILTRO_SEMESTRE', this.semestreTemp)
      this.$store.commit('turma/SET_FILTRO_MODALIDADE_TURMA', (this.modalidadeSelecionada ? this.modalidadeSelecionada.id : null))
      this.$store.commit('turma/SET_FILTRO_CURSO', (this.cursoSelecionado ? this.cursoSelecionado.id : null))
      this.$store.commit('turma/SET_FILTRO_SALA_FRANQUEADA', (this.salaFranqueadaSelecionada ? this.salaFranqueadaSelecionada.id : null))
      this.$store.commit('turma/SET_FILTRO_LIVRO', (this.livroSelecionado ? this.livroSelecionado.id : null))
      this.$store.commit('turma/SET_FILTRO_HORARIO', (this.horarioSelecionado ? this.horarioSelecionado.id : null))
      this.$store.commit('turma/SET_FILTRO_FUNCIONARIO', (this.funcionarioSelecionado ? this.funcionarioSelecionado.id : null))
      this.$store.commit('turma/SET_FILTRO_DATA_INICIO', this.data_inicial_temporario ? beginOfDay(this.data_inicial_temporario) : null)
      this.$store.commit('turma/SET_FILTRO_DATA_FIM', this.data_final_temporario ? endOfDay(this.data_final_temporario) : null)
    },

    filtrar () {
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else {
        this.executaFiltroAvancado()
      }

      if (this.estaCarregando === false) {
        this.SET_PAGINA_ATUAL(1)
        this.listar()
      }
    },

    carregarMais () {
      this.listar()
    },

    selecionarTurma (id) {
      if (!this.estaCarregando && this.listaTodaCarregada) {
        this.activePage = 'list'
        this.$emit('resolve', Number(id))
      }
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listar()
    }

  }
}
</script>
<style>
#filtros-rapidos,
#filtros-avancados {
  transition: all .1s;
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
</style>

<style lang="scss" scoped>
.table-scroll {
  height: calc(100vh - 200px);
  height: -webkit-calc(100vh - 200px);
  height: -moz-calc(100vh - 200px);
}

.table-scroll.filtro-aberto {
  height: calc(100vh - 275px);
  height: -webkit-calc(100vh - 275px);
  height: -moz-calc(100vh - 275px);
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
</style>
