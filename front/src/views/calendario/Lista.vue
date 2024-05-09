<template>
  <div class="fadeIn w-100">
    <div class="form-loading screen-load">
      <load-placeholder :loading="carregando" />
    </div>

    <div class="d-flex">
      <div class="col col-md-2 p-0">
        <g-select
          id="calendario-ano-teste"
          :value="calendario_ano"
          :options="listaAno"
          :select="setAno"
        />
      </div>

      <!-- Feriado acadêmico(Pedagógico) / Feriado bancário(Bancário) -->
      <div class="col col-md-4 p-0">
        <b-form-radio-group
          id="btn-pedagogico-filter"
          v-model="eventFilter"
          :options="pedagogicoFilter"
          buttons
          button-variant="outline-primary"
          name="radios-btn-default"
          @change="build"
        />

        <b-form-radio-group
          id="btn-bancario-filter"
          v-model="eventFilter"
          :options="bancarioFilter"
          buttons
          button-variant="outline-success"
          name="radios-btn-default"
          @change="build"
        />
      </div>

      <a href="javascript:void(0)" title="Adicionar" class="btn btn-azul ml-auto" @click="openFormCal()" >
        <font-awesome-icon icon="plus" /> Adicionar
      </a>

    </div>

    <!-- TABELA DE CALENDÁRIOS =========================== -->
    <div class="tabela-wrapper" >
    <div class="form-group row content-calendario mb-0">

      <div class="calendario col-12 cal-flex">
        <v-calendar
          :value="new Date()"
          :attributes="attributes"

          :columns="layout.columns"
          :rows="layout.rows"
          :is-expanded="layout.isExpanded"

          :first-day-of-week="1"

          :from-page="{month: 1, year: calendario_ano}"

          :min-page="{month: 1, year: calendario_ano}"
          :max-page="{month: 12, year: calendario_ano}"

          is-inline

          transition="none"
          disable-page-swipe
          nav-visibility="hidden"
          content-class="calendario-meses"
          @dayclick="selectEventDate"
        >
          <div slot="day-popover" slot-scope="{ day, dayTitle, attributes }">
            <div class="font-semibold text-center">
              {{ dayTitle }}
            </div>
            <ul class="calendario-event-popover">
              <li v-for="{key, popover} in attributes" :key="key" :class="popover.dot" @click="selectEventDate(popover)">
                {{ popover.label }}
              </li>
            </ul>
          </div>
        </v-calendar>
      </div>
    </div>
    </div>

    <!-- FORM EVENTO -->
    <b-modal id="form-evento" v-model="formCalendario" size="md" centered no-close-on-backdrop hide-header hide-footer modal-class="form-calendario">
      <div class="d-flex justify-content-between">
        <h5 class="title-module">{{ action }}evento</h5>
      </div>

      <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">

        <div v-show="loadForm" class="form-loading">
          <load-placeholder :loading="true" />
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="calendario_descricao" class="col-form-label">Descrição *</label>
            <input id="calendario_descricao" v-model="cardEvento.descricao" type="text" class="form-control" required maxlength="50">
            <div class="invalid-feedback">Preencha o título!</div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="data_hora_inicio" class="col-form-label">Data</label>

            <v-date-picker
              v-model="cardEvento.data_inicial"
              :first-day-of-week="1"
              :min-date="dataLimite.min"
              :max-date="dataLimite.max"
              :input-props="{ class: 'form-control', placeholder: 'Data', id: 'data_hora_inicio', required: true, autocomplete: 'off' }"
              :popover="{ visibility: 'click' }"
              transition="fade"
              content-class="weekend-visible"
            />

            <div v-if="!isValid && !cardEvento.data_inicial" class="multiselect-invalid">
              Selecione uma data!
            </div>
            <div v-if="!isEdit && cardEvento.data_final && validarData()" class="floating-message bg-danger">
              Data inicial deve ser menor que a data final!
            </div>
          </div>

          <div class="col-md-6">
            <b-form-checkbox id="periodo" v-model="periodo" @change="setPeriodo()">Definir período</b-form-checkbox>

            <v-date-picker
              v-model="cardEvento.data_final"
              :from-page="{month: cardEvento.mes, year: calendario_ano}"
              :first-day-of-week="1"
              :min-date="dataLimite.min"
              :max-date="dataLimite.max"
              :input-props="{ class: 'form-control', placeholder: 'Data', id: 'data_hora_fim', required: true, autocomplete: 'off', disabled: !periodo }"
              :popover="{ visibility: 'click' }"
              transition="fade"
              content-class="weekend-visible"
              @dayclick="setDataFinal"
            />

            <div v-if="!isValid && periodo && !cardEvento.data_final" class="multiselect-invalid">
              Selecione uma data!
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <div class="custom-control custom-checkbox">
              <input id="feriado_bancario" v-model="cardEvento.feriado_bancario" type="checkbox" class="custom-control-input">
              <label for="feriado_bancario" class="custom-control-label">Feriado bancário</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="custom-control custom-checkbox">
              <input id="dia_letivo" v-model="cardEvento.dia_letivo" type="checkbox" class="custom-control-input">
              <label for="dia_letivo" class="custom-control-label">Feriado acadêmico</label>
            </div>
          </div>
        </div>

        <!-- <div class="form-group row">
          <div class="col">
            <label for="descricao" class="col-form-label">Descrição</label>
            <b-form-textarea
              id="descricao"
              v-model="cardEvento.descricao"
              class="full-textarea"
              rows="3"
            />
          </div>
        </div> -->

        <div class="form-group pt-2 mb-0 d-flex">
          <b-btn :disabled="isEnviando" type="submit" variant="verde" class="mr-1">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
          <b-btn :disabled="isEnviando" variant="verde" @click="salvar(true)">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn>

          <button type="button" class="btn btn-link" @click="closeFormCal">Cancelar</button>
          <b-btn v-if="isEdit" variant="outline-danger" class="ml-auto" @click="excluirData(cardEvento, true)">Excluir</b-btn>
        </div>

      </form>

    </b-modal>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'
import {stringToISODate, dateToCompare} from '../../utils/date'
import DatePicker from '../../components/fields/DatePicker'

import moment from 'moment'

// import Calendar from '../components/fields/Calendar'
import VueCal from 'vue-cal'
import 'vue-cal/dist/i18n/pt-br.cjs.js'
import 'vue-cal/dist/vuecal.css'

export default {
  components: {
    // Calendar,
    VueCal,
    DatePicker
  },

  data () {
    return {
      isEnviando: false,
      isValid: true,
      isEdit: false,
      formCalendario: false,
      listaAno: [],
      dataLimite: {min: null, max: null},
      calendario_ano: new Date().getUTCFullYear(),
      action: '',

      attributes: [],

      cardEvento: {
        id: '',
        descricao: '',
        data_inicial: null,
        data_final: null,
        feriado_bancario: false,
        dia_letivo: false
      },
      periodo: false,

      carregando: false,
      loadForm: false,

      eventFilter: 'dia_letivo',
      pedagogicoFilter: [
        { text: 'Pedagógico', value: 'dia_letivo' }
      ],
      bancarioFilter: [
        { text: 'Bancário', value: 'feriado_bancario' }
      ],
      eventList: []
    }
  },
  computed: {
    ...mapState('calendario', ['lista', 'itemSelecionado', 'estaCarregando', 'todosItensCarregados', 'itemSelecionadoID']),
    ...mapState('root', ['usuarioLogado']),

    layout () {
      return this.$screens(
        {
          // Default layout for mobile
          default: {
            columns: 1,
            rows: 12,
            isExpanded: true
          },
          sm: { // (min-width: 640px)
            columns: 2,
            rows: 6,
            isExpanded: true
          },
          md: { // (min-width: 768px)
            columns: 4,
            rows: 3,
            isExpanded: true
          },
          lg: { // (min-width: 1024px)
            columns: 4,
            rows: 3,
            isExpanded: true
          },
          xl: { // (min-width: 1280px)
            columns: 4,
            rows: 3,
            isExpanded: true
          }
        }
      )
    }
  },

  created () {
    this.anoCalendario()
    this.build()
  },
  validations: {
    cardEvento: {
      descricao: {required}
    }

  },
  methods: {
    ...mapActions('calendario', ['listar', 'atualizar', 'buscar', 'criar', 'excluir']),
    ...mapMutations('calendario', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'LIMPAR_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_DESCRICAO', 'SET_PADRAO', 'SET_ANO']),

    dateToCompare: dateToCompare,

    stringToISODate: stringToISODate,

    setAno (value) {
      this.carregando = true
      setTimeout(() => {
        this.calendario_ano = value
        this.build()
      }, 100)
    },

    build () {
      this.carregando = true
      this.attributes = []
      this.attributes.push({
        key: 'Hoje',
        highlight: {
          color: 'indigo',
          fillMode: 'light'
        },
        popover: {
          isInteractive: true,
          label: 'Hoje',
          dot: 'hoje'
        },
        dates: new Date()
      })

      this.listar(this.calendario_ano)
        .then(lista => lista.filter(item => {
          if (!!item.dia_letivo && !item.feriado_bancario) {
            item.style = 'default'
            return item
          }

          if (this.eventFilter === 'dia_letivo') { return !item.dia_letivo }

          if (this.eventFilter === 'feriado_bancario') { return item.feriado_bancario }
        })
        )
        .then(lista => {
          this.eventList = lista.sort((a, b) => {
            if (a.data_inicial < b.data_inicial) {
              return -1
            }
            if (a.data_inicial > b.data_inicial) {
              return 1
            }
            return 0
          })

          this.eventList.map(item => {
            let dates = new Date(item.data_inicial)
            let dataFinal = null

            const m = moment(dates)
            const mes = item.mes
            const ano = m.get('year')

            if (item.data_final) {
              dates = {
                start: new Date(item.data_inicial),
                end: new Date(item.data_final)
              }
              dataFinal = new Date(item.data_final)
            }

            const evento = {
              id: item.id,
              permite_editar: item.permite_edicao,
              mes: mes,
              ano: ano,
              dates: dates,
              cardEvento: {
                style: item.style,
                descricao: item.descricao,
                data_inicial: new Date(item.data_inicial),
                data_final: dataFinal,
                feriado_bancario: item.feriado_bancario,
                dia_letivo: item.dia_letivo,
                popover_date: moment(item.data_inicial).format('YYYY-MM-DD')
              }
            }

            this.addEventDate(evento)
          })

          this.carregando = false
        })
    },

    addEventDate (event) {
      const dates = event.dates
      const cardEvento = event.cardEvento

      const periodo = dates.start || dates.end ? ' periodo' : ''
      const isDisabled = event.permite_editar ? '' : ' is-disabled'
      const style = cardEvento.style || this.eventFilter

      const _event = {
        dates: dates,
        highlight: { class: `${style}${periodo}${isDisabled}` },
        popover: {
          label: cardEvento.descricao,
          isInteractive: true,
          dot: `${style}${isDisabled}`,
          cardEvento: cardEvento,
          event_id: event.id,
          placement: 'auto'
        }
      }

      this.attributes.push(_event)
    },

    selectEventDate (item) {
      let selected = false
      if (item.event_id) { selected = this.eventList.find(e => item.event_id === e.id) }

      if (selected) {
        this.openFormCal(selected)
      } else {
        this.loadForm = true

        this.action = 'Novo '
        this.periodo = false
        this.cardEvento = {
          id: null,
          mes: item.month,
          descricao: '',
          data_inicial: item.date,
          data_final: null,
          feriado_bancario: false,
          dia_letivo: false // para salvar no banco deve ser oposto ao que se diz "feriado acadêmico"
        }

        this.loadForm = false
        this.formCalendario = true
      }
    },

    openFormCal (item) {
      // Validar se pode editar por 'permite_edicao'
      if (item && item.id) {
        if (!item.permite_edicao) {
          return
        }

        this.isEdit = true
        this.loadForm = true

        this.action = 'Alterar '

        this.periodo = !!item.data_final

        this.cardEvento = {
          id: item.id,
          mes: item.mes,
          descricao: item.descricao,
          data_inicial: new Date(item.data_inicial),
          data_final: item.data_final ? new Date(item.data_final) : '',
          feriado_bancario: item.feriado_bancario,
          dia_letivo: !item.dia_letivo // para salvar no banco deve ser oposto ao que se diz "feriado acadêmico"
        }
        this.loadForm = false
      } else {
        this.resetFormCal()
      }

      this.formCalendario = true
    },

    closeFormCal () {
      this.formCalendario = false
      this.LIMPAR_ITEM_SELECIONADO()
      this.isValid = true
    },

    resetFormCal (data) {
      this.loadForm = true

      this.isEdit = false

      this.action = 'Novo '
      this.periodo = false
      this.cardEvento = {
        id: null,
        descricao: '',
        data_inicial: null || data,
        data_final: null,
        feriado_bancario: false,
        dia_letivo: false // para salvar no banco deve ser oposto ao que se diz "feriado acadêmico"
      }

      this.loadForm = false
    },

    setDataFinal (item) {
      if (!this.periodo) {
        setTimeout(() => {
          this.cardEvento.data_final = null
        }, 100)
      }
    },

    dataInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== undefined
    },

    validarData () {
      const mInicio = moment(this.cardEvento.data_inicial).format('DD/MM/YYYY')
      const mFim = moment(this.cardEvento.data_final).format('DD/MM/YYYY')

      this.dataInvalida(mInicio, mFim)

      return this.dataInvalida(mInicio, mFim)
    },

    setPeriodo () {
      if (this.periodo) {
        this.cardEvento.data_final = null
      }
    },

    anoCalendario () {
      const ano = new Date().getUTCFullYear()
      let lista = []

      let cont = -2
      for (let i = 5; i--;) {
        let result = ano + cont
        cont++

        lista.push(result)
      }

      this.dataLimite = {
        min: new Date(`${moment().set('year', lista[0]).startOf('year').format('MM-DD-YYYY')}`),
        max: new Date(`${moment().set('year', lista[lista.length - 1]).endOf('year').format('MM-DD-YYYY')}`)
      }

      this.listaAno = lista
    },

    validarEvento () {
      this.isValid = true

      const card = this.cardEvento
      if (card.data_inicial === null || (this.periodo && card.data_final === null) || (!this.isEdit && this.validarData())) {
        this.isValid = false
      }
    },

    salvar (sair) {
      this.isEnviando = true

      this.validarEvento()

      if (!this.isValid || this.$v.cardEvento.$invalid) {
        this.isEnviando = false
        this.isValid = false
        return
      }

      let dataBackend = Object.assign({}, this.cardEvento)

      dataBackend.data_inicial = dataBackend.data_inicial.toISOString()
      dataBackend.data_final = dataBackend.data_final ? dataBackend.data_final.toISOString() : null
      dataBackend.dia_letivo = !dataBackend.dia_letivo

      if (dataBackend.id) {
        this.atualizar(dataBackend).then(() => {
          if (sair) {
            this.closeFormCal()
          }

          this.isEnviando = false
          this.build()
          this.resetFormCal()
        }).catch(console.error)
      } else {
        this.criar(dataBackend).then(() => {
          if (sair) {
            this.closeFormCal()
          }

          this.isEnviando = false
          this.build()
          this.resetFormCal()
        }).catch(console.error)
      }
    },

    excluirData (item) {
      // this.formCalendario = false
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.excluir(item.id)
            .then(() => {
              this.closeFormCal()
              this.build()
            })
        },
        reject: () => {
          // TODO: verificar se o formCalendario estava aberto e abrir novamente
          // this.formCalendario = true
        }
      }, `Deseja excluir "${item.descricao}"?`)
    }
  }
}
</script>
<style scoped>

.body{
  max-height:100vh;
}

.tabela-wrapper {
  width: calc(100vw - 271x);
  height: calc(100vh - 94px);
  overflow-x: scroll;
  overflow-y: scroll;
}

.tabela-wrapper::-webkit-scrollbar {
  width: 5px;
  height: 5px;
}

.tabela-wrapper::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.tabela-wrapper::-webkit-scrollbar-thumb {
  background: #888;
}


.content-calendario {
  display: flex;
  flex: 1;
}
.cal-flex {
  flex-grow: 1;
  position: relative;

  display: flex;
  flex-direction: column;
  margin-top: .5rem;
}
.icon-column {
  display: flex;
  padding-right: 1rem !important;
  min-width: 50px;
  max-width: 70px;
}
.icon-column .icone-link {
  margin: 0 auto;
}

.table-scroll.data-form {
  height: calc(100vh - 360px);
  height: -webkit-calc(100vh - 360px);
  height: -moz-calc(100vh - 360px);
  margin-bottom: 0;
}
.filtro-header {
  color: #4a4a4a;
}
.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151B1E;
  background-color: #fff;
}

.number {
  max-width: 100px;
}

.options-licao div {
  width: 1.25em;
  margin: auto;
}

.invalid-feedback:not(.calendario) {
  position: relative;
}

/* a.icone-link.text-muted {
  font-size: x-large;
} */

.table .number {
  max-width: 120px;
}



@media (min-width: 768px) and (max-width: 1530px) {
  .col-md-3 {
    flex: 0 0 50%;
    max-width: 50%;
  }
/*   .number {
    max-width: 100%;
  }
  .table.mobile-cards td:not(:last-child) > div {
    max-width: 60%;
    padding-right: 0;
  }
  .options-licao div {
    width: auto;
  }
  .table.mobile-cards tr:hover td {
    border-color: #EBECF0;
  } */
}

#calendario-ano {
  height: auto;
}

.content-sector {
  height: calc(100vh - 355px);
  height: -webkit-calc(100vh - 355px);
  height: -moz-calc(100vh - 355px);
}
.table-scroll {
  height: calc(50vh - 100px);
  height: -webkit-calc(50vh - 100px);
  height: -moz-calc(50vh - 100px);
}

.table-calendario {
  border: 1px solid #EBECF0;
  width: 100%;
  /* height: 150px; */
  max-height: 150px;
  height: 100%;


  /* flex-grow: 1; */
  position: relative;

  overflow: hidden;

  border-top: none;
  border-bottom-right-radius: 8px;
  border-bottom-left-radius: 8px;
  margin-bottom: 0;
}
.table-calendario .ps {
  max-height: 100%;
}
.ps__rail-y {
  opacity: 1;
}
.table-calendario ul {
  list-style-type: none;
  display: flex;
  padding: 0;
  margin: 0;
  cursor: default;
  /* display: none; */
}

.table-calendario ul.visible {
  display: flex;
}

.table-calendario ul:hover {
  background-color: #EBECF0;
}
.table-calendario ul li {
  padding: .1rem;
  flex: 1 1 0;
}
.table-calendario ul span,
.table span{
  min-width: 25px;
  font-size: 95%;
  color: #fff;
  margin-right: .5rem;
}

.content-calendario a {
  color: #4a4a4a !important;
}

.nacional {
  color: #4a4a4a !important;
  background-color: #dbdbdb;
  /* background-color: #85d017; */
}

.tr-head:not(.tr-nacional) {
  /* cursor: pointer; */
  display: flex;
}
.tr-nacional {
  cursor: default;
  display: flex;
}

.opt-mes {
  /* background-color: #EBECF0; */
  box-shadow: inset 0 1px 3px #dbdbdb;
  background-color: #fff;
}
.table-scroll.table-calendar tbody tr {
  /* display: none; */
  flex-direction: column;
}

/* .table-scroll tbody tr:last-child .opt-mes {
  border-bottom: 1px solid #EBECF0;
} */
/* .table-scroll tbody tr:last-child {
  background-color: black;
} */

div.collapsed:hover,
.tr-head:hover {
  background-color: #EBECF0;
}
/* select.form-control:not([size]):not([multiple]) */
select.form-control.calendar-select {
  height: 2.0625rem;
  background-color: transparent;
  font-size: larger;
  font-weight: bold;
}

.floating-message {
  position: absolute;
  z-index: 1;
  margin-top: 4px;
  padding: 3px 5px;
  font-size: 0.7rem;
  width: 145px;
  left: 50%;
}
.floating-message::before {
  content: '';
  position: absolute;
  top: -16px;
  border: 8px solid #FF3860;
  border-top-color: transparent;
  border-left-color: transparent;
  border-right-color: transparent;
}

/* Popover */
ul.calendario-event-popover {
  list-style: none;
  padding-left: 18px;
  margin-bottom: 0;
}
ul.calendario-event-popover li:not(.is-disabled) {
  font-weight: 600;
  cursor: pointer;
}
ul.calendario-event-popover li.is-disabled,
ul.calendario-event-popover li.hoje {
  pointer-events: none;
  cursor: default;
  opacity: .8;
}
ul.calendario-event-popover li:before {
  content: "\2022";
  font-size: 30px;
  color: #c3dafe;
  font-weight: bold;
  position: absolute;
  line-height: 15px;
  margin-left: -0.6em;
  pointer-events: none;
  cursor: default;
}
ul.calendario-event-popover li.default:before {
  color: #ec8644;
}
ul.calendario-event-popover li.dia_letivo:before {
  color: #20a8d8;
}
ul.calendario-event-popover li.feriado_bancario:before {
  color: #23d160;
}
ul.calendario-event-popover li.default.is-disabled:before,
ul.calendario-event-popover li.dia_letivo.is-disabled:before,
ul.calendario-event-popover li.feriado_bancario.is-disabled:before {
  opacity: .8;
}

ul.calendario-event-popover li {
  padding: 0 3px;
  transition: all ease .1s;
  border-radius: 2px;
}
ul.calendario-event-popover li:hover {
  background-color: #ffffff;
  color: #2d3748;
}

body {
  overflow: hidden;
}

@media (max-width: 768px) {
  .table .number {
    max-width: 100%;
  }
  .tr-head,
  .tr-nacional {
    display: block;
  }
  .table-scroll {
    height: calc(50vh - 90px);
    height: -webkit-calc(50vh - 90px);
    height: -moz-calc(50vh - 90px);
  }
  .table.mobile-cards td.no-check {
    display: none;
  }
}
</style>
