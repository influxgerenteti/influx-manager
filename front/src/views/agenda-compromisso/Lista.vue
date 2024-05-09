<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1">Filtros</div>
        </div>

        <!-- ADICIONAR AGENDAMENTO -->
        <button v-b-modal.modalFormularioAgendaCompromisso class="btn btn-azul" type="button"><font-awesome-icon icon="plus" /> Adicionar</button>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">

          <div class="form-group row mb-0">

            <div class="col-md-auto">
              <label for="tipo_agenda_filtro_rapido" class="col-form-label">Tipo de Agenda</label>
              <div class="d-block">
                <b-form-radio-group
                  id="tipo_agenda_filtro_rapido"
                  v-model="selected"
                  :options="tiposAgendas"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="tipo_agenda_filtro_rapido"
                  @change="setTipoAgenda"
                />
              </div>
            </div>

            <div v-if="selected === '1'" class="col">
              <label for="funcionario" class="col-form-label">Responsável *</label>
              <g-select id="funcionario"
                        :select="setFuncionario"
                        :value="funcionario"
                        :options="listaFuncionario"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </div>

          </div>

        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">

      <div v-if="cardLoad" class="form-loading agenda-card">
        <load-placeholder :loading="cardLoad" />
      </div>

      <!-- :on-event-dbclick="alterarAgendamento" -->
      <vue-cal
        ref="agenda"
        :selected-date="selected_date"
        :hide-weekdays="[7]"
        :time-from="7 * 60"
        :time-to="23 * 60"
        :time-step="30"

        :events="agendamentos"

        :on-event-click="alterarAgendamento"

        :disable-views="['years']"
        :dblclick-to-navigate="false"
        :cell-click-hold="false"
        :on-event-create="criarAgendamento"
        style="position: absolute; height: 100%; width: 100%;"
        locale="pt-br"
        today-button

        show-week-numbers
        start-week-on-sunday

        small
        class="vuecal--g-theme"

        @cell-dblclick="$refs.agenda.createEvent($event)"
        @view-change="changeView($event)"
      >

        <template v-slot:event-renderer="{ event }">
          <template v-if="event">

            <div class="vuecal-btn-close" @click="confirmarExcluir(event)">
              <font-awesome-icon icon="times" />
            </div>

            <div class="vuecal__event-title" v-html="event.title" />

            <small class="vuecal__event-time">
              <span>{{ event.startDate.formatTime("HH:mm") }}</span> - <span>{{ event.endDate.formatTime("HH:mm") }}</span>
            </small>
          </template>
        </template>

      </vue-cal>
    </div>

    <!-- MODAL FORM AGENDAMENTO -->
    <b-modal id="modalFormularioAgendaCompromisso" ref="modalFormularioAgendaCompromisso" v-model="modalFormularioAgendaCompromisso" size="md" centered no-close-on-backdrop hide-header hide-footer @show="openModal()" >
      <formulario-agenda-compromisso
        ref="formularioAgendaCompromisso"
        :card-agendamento="cardAgendamento"
        :cancelar-dados="cancelarModal"
        :excluir="confirmarExcluir"
        :alterar-periodo="confirmarAlteracaoPeriodo"
        :events="agendamentos"
        :agenda="$refs.agenda"
        :listar-eventos="listarItens"
        @fechar="cancelarModal" />
    </b-modal>

    <!-- Modal de confirmação alteração de período -->
    <b-modal id="confirmar-excluir" ref="confirmar-excluir" v-model="modalConfirmarPeriodo" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center">
        <p>Alterar todos os eventos desse período?</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn class="mt-3 mr-3" variant="outline-success" block @click="alterarPeriodoTodo(cardConfirmacao)">Sim</b-btn>
        <b-btn class="mt-3" variant="outline-danger" block @click="cancelarAlterarPeriodo(cardConfirmacao)">Não</b-btn>
      </div>
    </b-modal>

    <!-- Modal de confirmação exclusão -->
    <b-modal id="confirmar-excluir" ref="confirmar-excluir" v-model="modalConfirmarExcluir" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center">
        <p>Deseja excluir este agendamento?</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn class="mt-3 mr-3" variant="outline-success" block @click="excluirAgendamento(cardConfirmacao)">Confirmar</b-btn>
        <b-btn class="mt-3" variant="outline-danger" block @click="cancelarExcluir">Cancelar</b-btn>
      </div>
    </b-modal>

    <!-- Modal de confirmação exclusão período -->
    <b-modal id="confirmar-excluir" ref="confirmar-excluir" v-model="modalConfirmarExcluirPeriodo" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center">
        <p>Deseja excluir todos os eventos desse período?</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn class="mt-3 mr-3" variant="outline-success" block @click="confirmarExcluirPeriodo(cardConfirmacao, true)">Sim</b-btn>
        <b-btn class="mt-3" variant="outline-danger" block @click="confirmarExcluirPeriodo(cardConfirmacao, false)">Não</b-btn>
      </div>
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'
import FormularioAgendaCompromisso from './Formulario'
import moment from 'moment'
import VueCal from 'vue-cal'
import 'vue-cal/dist/i18n/pt-br.cjs.js'
import 'vue-cal/dist/vuecal.css'

export default {
  name: 'ListaAgendaCompromisso',
  components: {
    VueCal,
    FormularioAgendaCompromisso
  },
  data () {
    return {
      className: 'rapido-open',
      data_hora_inicio: moment().format('DD/MM/YYYY'),
      data_hora_fim: undefined,
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: false,
      modalFormularioAgendaCompromisso: false,
      filtroSelecionado: 1,
      selected: 0,
      tiposAgendas: [
        {text: 'Agenda Pessoal', value: '0'},
        {text: 'Agenda Publica', value: '1'}
      ],

      cardLoad: false,

      visao_agenda: {id: 1, descricao: 'Semanal'},
      listaVisaoAgenda: [
        {id: 1, descricao: 'Semanal'},
        {id: 2, descricao: 'Compromissos'}
      ],
      compromissos: false,

      agendamentos: [],

      selected_date: moment().format('DD/MM/YYYY'),

      funcionario: {id: null, apelido: 'Todos'},

      cardAgendamento: {
        tipo_agendamento: null,
        tipo_ocorrencia: null,
        data_inicio: null,
        data_fim: null,
        hora_inicio: '',
        hora_fim: '',
        titulo: '',
        descricao: '',
        funcionario: '',
        privado: false
      },

      modalConfirmarPeriodo: false,
      modalConfirmarExcluir: false,
      modalConfirmarExcluirPeriodo: false,
      isOpen: false,
      cardConfirmacao: {},
      alterarTodos: true,
      eventList: []
    }
  },
  computed: {
    ...mapState('root', ['usuarioLogado']),
    ...mapState('agendaCompromisso', {listaItens: 'lista', estaCarregando: 'estaCarregando', filtros: 'filtros'}),
    ...mapState('funcionario', {listaFuncionarioRequisicao: 'lista'}),

    listaFuncionario: {
      get () {
        return [{id: null, apelido: 'Todos'}].concat(this.listaFuncionarioRequisicao.filter(item => (item.cargo.tipo !== 'ASG')))
      }
    }
  },
  mounted () {
    this.selected = 0
    this.filtros.tipo = 0
    this.SET_LISTA([])

    this.setDataAgenda(this.selected_date)
    this.listarFuncionario()
  },
  methods: {
    ...mapActions('agendaCompromisso', {listarItens: 'listar', excluirPorId: 'excluir', alterarPeriodo: 'atualizar'}),
    ...mapMutations('agendaCompromisso', ['SET_LISTA', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO']),
    ...mapActions('funcionario', {listarFuncionario: 'listar'}),

    formatMomentDate (date) {
      const d = date.split('/')
      return `${d[2]}-${d[1]}-${d[0]}`
    },

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== undefined
    },

    setTipoAgenda (value) {
      this.selected = value
      this.filtros.tipo = value
      if (value === '0') {
        delete this.filtros.funcionario
      } else {
        this.filtros.funcionario = this.funcionario.id
      }
      this.filtrar()
    },

    setFuncionario (value) {
      this.funcionario = value

      if (value.id) {
        this.filtros.funcionario = value.id
      } else {
        delete this.filtros.funcionario
      }

      this.filtrar()
    },

    setDataHoraInicio (value) {
      let dataDe = value ? beginOfDay(value) : null
      this.filtros.data_hora_inicio = dataDe

      this.filtrar()
    },

    setDataHoraFim (value) {
      let dataAte = value ? endOfDay(value) : null
      this.filtros.data_hora_fim = dataAte

      this.filtrar()
    },

    setDataAgenda (value) {
      this.selected_date = this.formatMomentDate(value)

      const m = moment(this.selected_date)

      const dataDe = beginOfDay(m.startOf('week').add(1, 'd').format('DD/MM/YYYY'))
      const dataAte = endOfDay(m.endOf('week').format('DD/MM/YYYY'))

      this.filtros.data_hora_inicio = dataDe
      this.filtros.data_hora_fim = dataAte

      this.filtrar()
    },

    setVisaoAgenda (value) {
      this.visao_agenda = value
    },

    filtrar () {
      this.cardLoad = true
      this.listarItens().then(() => {
        this.loadCards(this.listaItens)
      })
    },

    loadCards (list) {
      this.eventList = []
      this.agendamentos = []
      list.map(card => {
        const momentIni = moment(card.data_hora_inicio)
        const momentEnd = moment(card.data_hora_fim)

        this.data_hora_inicio = momentIni.format('DD/MM/YYYY')

        const dateIni = momentIni.format('YYYY-MM-DD')
        const hourIni = momentIni.format('HH:mm')

        const dateEnd = momentEnd.format('YYYY-MM-DD')
        const hourEnd = momentEnd.format('HH:mm')

        card.start = `${dateIni} ${hourIni}`
        card.end = `${dateEnd} ${hourEnd}`
        card.title = card.titulo
        card.content = card.descricao

        card.data_inicio = momentIni.toDate()
        card.data_fim = momentEnd.toDate()

        card.hora_inicio = hourIni
        card.hora_fim = hourEnd

        card.titulo = card.titulo

        card.possui_periodo_atrelado = card.possui_periodo_atrelado

        card.descricao = card.descricao || ''
        card.privado = card.privado || false
        card.tipo_agendamento = card.tipo_agendamento
        card.tipo_ocorrencia = card.tipo_ocorrencia
        card.class = `tipo-${card.tipo_agendamento.cor.replace('#', '')}`

        this.agendamentos.push(card)
      })

      this.eventList = this.agendamentos
      this.cardLoad = false
    },

    criarAgendamento (event, deleteEventFunction) {
      const time = event.start.split(' ')
      const regex = new RegExp(/\d+$/i)

      this.cardAgendamento.data_inicio = moment(time[0]).toDate()
      this.cardAgendamento.hora_inicio = time[1].replace(regex, regex.exec(time[1]) >= '30' ? '30' : '00')
      this.cardAgendamento.hora_fim = ''

      this.modalFormularioAgendaCompromisso = true
      return false
    },

    alterarAgendamento (event, e) {
      const el = e.target
      if (!(el.classList.contains('vuecal-btn-close') || el.classList.contains('fa-times')) && event.titulo !== 'Indisponível') {
        this.cardAgendamento = event

        e.stopPropagation()
        this.modalFormularioAgendaCompromisso = true
      }
    },

    cancelarModal () {
      this.setDataAgenda(this.data_hora_inicio)

      this.cardAgendamento = {
        tipo_agendamento: null,
        tipo_ocorrencia: null,
        data_inicio: null,
        data_fim: null,
        hora_inicio: '',
        hora_fim: '',
        titulo: '',
        descricao: '',
        funcionario: this.usuarioLogado.funcionarios[0],
        privado: false
      }

      this.modalFormularioAgendaCompromisso = false
      this.$refs.formularioAgendaCompromisso.isEdit = false
    },

    changeView (event) {
      const m = moment(event.startDate).format('DD/MM/YYYY')
      this.setDataAgenda(m)
    },

    confirmarExcluir (event, isOpen) {
      this.modalConfirmarExcluir = true
      this.cardConfirmacao = {...event}
      this.isOpen = isOpen
    },

    confirmarAlteracaoPeriodo (event) {
      this.modalConfirmarPeriodo = true
      this.cardConfirmacao = {...event}
      this.isOpen = true
    },

    cancelarExcluir () {
      if (this.isOpen) {
        this.modalFormularioAgendaCompromisso = true
        this.isOpen = false
      }
      this.modalConfirmarExcluir = false
    },

    cancelarAlterarPeriodo (event) {
      if (this.isOpen) {
        this.isOpen = false
      }
      this.alterarTodos = false
      this.modalConfirmarPeriodo = false

      this.alterarPeriodoTodo(event)
    },

    alterarPeriodoTodo (event) {
      this.modalConfirmarPeriodo = false

      const inicio = moment(event.data_inicio).format('DD/MM/YYYY')
      const fim = moment(event.data_fim).format('DD/MM/YYYY')

      event.data_hora_inicio = moment(`${inicio} ${this.cardAgendamento.hora_inicio}`, 'DD/MM/YYYY HH:mm').toISOString()
      event.data_hora_fim = moment(`${fim} ${this.cardAgendamento.hora_fim}`, 'DD/MM/YYYY HH:mm').toISOString()

      const item = {
        id: event.id,
        tipo_agendamento: event.tipo_agendamento.id,
        funcionario: event.funcionario.id,
        data_hora_inicio: event.data_hora_inicio,
        data_hora_fim: event.data_hora_fim,
        privado: event.privado,
        titulo: event.titulo,
        descricao: event.descricao,
        alterar_todos: this.alterarTodos
      }

      this.SET_ITEM_SELECIONADO(item)

      this.alterarPeriodo()
        .then(() => {
          this.SET_ITEM_SELECIONADO_ID(null)
          this.filtrar()
        })
        .catch((error) => {
          this.SET_ITEM_SELECIONADO_ID(null)
          console.error(error)
        })

      this.alterarTodos = true
      this.cancelarModal()
    },

    excluirAgendamento (event) {
      this.modalConfirmarExcluir = false

      if (event.possui_periodo_atrelado) {
        this.modalConfirmarExcluirPeriodo = true
        return
      }

      this.SET_ITEM_SELECIONADO(event)
      this.excluirPorId()
        .then(() => {
          this.SET_ITEM_SELECIONADO_ID(null)
          this.filtrar()
        })
        .catch((error) => {
          this.SET_ITEM_SELECIONADO_ID(null)
          console.error(error)
        })

      this.cancelarModal()
    },

    confirmarExcluirPeriodo (event, excluirTodos) {
      this.modalConfirmarExcluirPeriodo = false
      const item = {
        id: event.id,
        alterar_todos: excluirTodos
      }

      this.excluirAgendamento(item)
    },

    openModal () {
      this.$refs.formularioAgendaCompromisso.init()
    }
  }
}
</script>
<style scoped>
.form-loading.agenda-card {
  z-index: 4;
}

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

.table-schedule tbody td span {
  font-size: x-small;
}

.table-schedule th, .table-schedule td {
  padding: 0;
}

.table-schedule th {
  display: block;
  text-align: center;
}

.table-schedule th:first-child,
.table-schedule td:first-child {
  max-width: 70px;
  font-size: 0.65rem;
  padding: 1px;
}

.table-schedule td:first-child {
  font-weight: 900;
  border-right: 1px solid #dbdeea;
}
.table-schedule.table-scroll tbody tr,
.table-schedule.table-scroll tbody td {
  min-height: unset;
}

.table-schedule.table-scroll tbody td:not(:first-child) {
  flex-direction: row;
  align-items: stretch;
}

.table-schedule.table-scroll tbody td {
  flex-direction: column;
  justify-content: center;
}

.table-schedule.table-scroll tbody td > div:not(:last-child) {
  margin-bottom: .25rem;
}

.table-schedule.table-scroll thead tr,
.table-schedule.table-scroll tbody tr {
  flex-grow: 1;
  align-items: stretch;
  justify-items: stretch;
}

.table-schedule.table-scroll.cards-atrasados thead tr,
.table-schedule.table-scroll.cards-atrasados tbody tr {
  flex-grow: unset;

}

.table-schedule.table tbody tr td:not(:first-child):not(:last-child) {
  border-right: 1px dashed #c2cfd6;
}

.table-schedule.table tbody tr:not(:first-child) td.ocorrencias {
  border-color: #ffffff;
  background-color: #fff;
}

/* AGENDA */
.table-responsive-sm > div,
#agenda.table-schedule {
  height: 100%;
}
#agenda.table-schedule thead tr {
  text-transform: uppercase;
}

#agenda.table-schedule thead tr th {
  display: flex;
  flex-direction: column;
}

#agenda.table-schedule thead tr th:not(:first-child) span:first-child {
  color: #b7b7b7;
  font-size: smaller;
}
#agenda.table-schedule thead tr th:not(:first-child) span:not(:first-child) {
  display: flex;
  width: 25px;
  justify-content: center;
  margin-bottom: 3px;
}

.dia-vigente > span:last-child {
  color: #fff;
  background-color: #2d4899;
  border-radius: 20px;
}

#agenda.table-schedule thead tr th[data-anterior="true"] span:not(:first-child) {
  color: #b7b7b7;
}

#agenda.table-schedule td[data-time="true"] {
  background-color: #efefef;
}
</style>
