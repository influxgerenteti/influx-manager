<template>
  <!-- <div :id="id" :click-card="clickCard"> -->

  <!-- <div class="form-loading">
    <load-placeholder :loading="diarioCarregando || turmaAulaCarregando" />
  </div> -->

  <div :id="id" :click-card="clickCard" class="table-responsive-sm">
    <!-- <div v-if="!listaPersonal.length && isReagendamento" class="busca-vazia"> -->
    <!--   <p>Nenhum agendamento personal cadastrado.</p> -->
    <!-- </div> -->

    <g-table id="table-personal" class="table-card-hover table-schedule">
      <thead>
        <tr>
          <th data-column="">&nbsp;</th>
          <th v-for="(diaItem, diaIndex) in dias" :key="diaIndex" data-column="" data-date="" class="dia-da-semana">{{ diaItem.diaSemana }}<span>{{ diaItem.diaMes }}</span></th>
        </tr>
      </thead>
      <tbody>
        <perfect-scrollbar suppress-scroll-x="true">
          <tr v-for="(item, index) in listaHover" :key="index">
            <td><span>{{ item }}</span></td>

            <td v-for="(dateItem, dateIndex) in filteredDates" :key="dateIndex">
              <div
                :id="`card-wrapper-${index}-${parseInt(dateIndex)}`"
                :index="index"
                v-bind:class="{ 'nao-alocar' : !salaDisponibilidades.hasOwnProperty(dateItem + '-' + item)}"
                :column="dias[dateIndex].diaSemana"
                class="card-wrapper"
                @mouseover="setGhost($event, index, item, parseInt(dateIndex))"
                @mouseout="resetGhost()"
                @click.prevent="adicionarAgendamento($event, { data: dateItem, horario: item })">
                <template v-if="semana[parseInt(dateIndex)+1]">
                  <div
                    v-for="(semanaCard, semanaIndex) in semana[(parseInt(dateIndex)+1)][item]"
                    :name="`card-${semanaIndex}-${semanaCard.id}`"
                    :key="semanaCard.id"
                    :class="semanaCard.half_card ? 'half-card' : semanaCard.position ? 'position-card card-wrapper' : semanaCard.hora_indisponivel ? 'hora-indisponivel' : ''"
                    :data-reagendado="semanaCard.reagendado"
                    class="list-group-item ct-card"
                    @mouseover="hoverCard(semanaIndex, semanaCard)"
                    @mouseout="resetHoverCard()"
                    @click.prevent="selectCard($event, semanaCard)"
                  >
                    <template v-if="!semanaCard.position && !semanaCard.hora_indisponivel">
                      <div :class="semanaCard.reagendamento ? 'reagendamento-card' : 'agendado-card'" class="truncate">
                        <div class="card-name truncate mr-auto">
                          <span>{{ semanaCard.nome }}</span>
                        </div>
                      </div>
  
                      <div class="hover-card truncate">
                        <span><b>Nome:</b> {{ semanaCard.nome }}</span>
                        <span><b>Livro:</b> {{ semanaCard.livro }}</span>
                        <span><b>Instrutor:</b> {{ semanaCard.instrutor }}</span>
  
                        <span v-if="semanaCard.reagendamento" class="data-origem-reagendado"><b>Origem:</b> {{ semanaCard.original | formatarData }}</span>
                        <span v-if="semanaCard.reagendado" class="data-origem-reagendado"><b>Reagendado:</b> {{ semanaCard.data_reagendada | formatarData }}</span>
                      </div>
                    </template>
                  </div>
                </template>
              </div>
            </td>
            
          </tr>
        </perfect-scrollbar>
      </tbody>
    </g-table>
    <span v-if="agendamentoSemDisponibilidade" class="text-danger d-block w-100 text-center font-weight-bold">
      Existe(m) agendamento(s) fora da janela de disponibilidade da sala.
    </span>
  </div>
</template>

<script>
import moment from 'moment'
// import {required} from 'vuelidate/lib/validators'
import {mapState} from 'vuex'
import EventBus from '../utils/event-bus'

export default {
  name: 'GFormularioDiarioClasse',

  props: {
    id: {
      type: String,
      required: false,
      default: null
    },

    clickCard: {
      type: Boolean,
      required: false,
      default: false
    },

    dates: {
      type: Object,
      required: true
    },

    isReagendamento: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      isEnviando: false,
      isEdit: false,
      isValid: true,
      estaCarregando: false,
      agendamentoSemDisponibilidade: false,
      planilhapersonal: false,
      listaHover: [],
      salaDisponibilidades: [],
      semana: {
        1: {},
        2: {},
        3: {},
        4: {},
        5: {},
        6: {}
      },

      cardReagendamento: null,

      placeCard: false
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('salaFranqueada', ['disponibilidades']),

    listaPersonal () {
      const agendamentosSelecionados = this.$store.state.contrato.itemSelecionado.agendamento_personal
      return this.$store.state.agendamentoPersonal.lista.concat(agendamentosSelecionados)
    },

    listaIndisponivel () {
      return this.$store.state.indisponibilidadePersonal.lista
    },

    filteredDates() {
      return Object.values(this.dates).map(item => (moment(item, 'DD/MM/YYYY'))).filter(filterItem => filterItem.day()).map(mapItem => mapItem.format('DD/MM/YYYY'))
    },

    dias () {
      let diasSemana = Object.values(this.dates).map(item => (moment(item, 'DD/MM/YYYY'))).filter(filterItem => filterItem.day())
      return Object.values(diasSemana).map(item => {
        return {
          diaSemana: item.locale('pt-br').format('ddd'),
          diaMes: item.format('DD/MM')
        }
      })
    }
  },

  watch: {
    listaPersonal () {
      this.build()
    }
  },

  mounted () {
    this.build()
  },

  methods: {
    build () {
      const times = []

      for (let i = 7; i <= 22; i++) {
        let hora = i >= 10 ? '' : '0'
        hora += i

        if (i > 7) {
          times.push(hora + ':00')
        }

        if (i < 22) {
          times.push(hora + ':30')
        }
      }

      this.listaHover = times
      this.semana = {
        1: {},
        2: {},
        3: {},
        4: {},
        5: {},
        6: {}
      }

      // CARREGAR HORÁRIOS INDISPONÍVEIS
      this.listaIndisponivel.forEach(obj => {
        let timeInicio = moment(obj.hora_inicio).format('HH:mm')
        const timeFim = moment(obj.hora_fim).format('HH:mm')
        const diaSemana = obj.dia_semana

        delete obj.id
        const range = 3
        while (timeInicio < timeFim) {
          if (!this.semana[obj.dia_semana][timeInicio]) {
            this.semana[diaSemana][timeInicio] = (new Array(range)).fill({...obj, hora_indisponivel: true})
          }

          timeInicio = this.getNextTime(timeInicio)
        }
      })

      // CARREGAR CARDS PERSONAL
      const rowLength = 3
      const position = {position: true}

      this.listaPersonal.forEach(item => {
        if (item.hora_indisponivel) {
          return
        }

        const reagendamento = item.datasReagendamentoPersonals ? item.datasReagendamentoPersonals.find(reag => reag.ultimo_reagendamento) : null

        if (reagendamento) {
          if (!this.isReagendamento) {
            item.data_reagendada = reagendamento.data_reagendada
            this.setCard(item, rowLength, position)
          }

          if (!this.isReagendamento && item.inicio === item.data_reagendada) {
            return
          }

          reagendamento.id = `${reagendamento.id}-${item.id}`
          reagendamento.contrato = item.contrato
          reagendamento.funcionario = item.funcionario
          reagendamento.sala_franqueada = item.sala_franqueada

          reagendamento.original = item.inicio

          reagendamento.inicio = reagendamento.data_reagendada
          reagendamento.reagendamento = true

          this.setCard(reagendamento, rowLength, position)
        } else {
          this.setCard(item, rowLength, position)
        }
      })

      this.listaHover.sort()
      this.oranizarDisponibilidade()
    },

    oranizarDisponibilidade() {
      let agrupamento = {}
      this.disponibilidades.map((element) =>{
        const dataElement = moment(element.data, 'DD/MM/YYYY').hour(0).minute(0).second(0)
        const inicioHora = parseInt((element.hora_inicial.substring(0,2)))
        const inicioMinutos = parseInt((element.hora_inicial.substring(3,5)))
        const dataInicio = dataElement.clone().hour(inicioHora).minute(inicioMinutos)
        const finalHoras = parseInt((element.hora_final.substring(0,2)))
        const finalMinutos = parseInt((element.hora_final.substring(3,5)))
        const dataFinal = dataElement.clone().hour(finalHoras).minute(finalMinutos)
        const indice = dataElement.format('DD/MM/YYYY')
        if( !(agrupamento.hasOwnProperty(indice)) ) {
          agrupamento[indice] = []
        }
        agrupamento[indice].push({
          inicio: dataInicio,
          final: dataFinal
        })
      })
      for(const [key, element] of Object.entries(agrupamento)) {
        if(element.length >= 1) {
          element.forEach((item, index) => {
            const minutosInicio = item.inicio.minutes() > 30 ? 60 : (item.inicio.minutes() > 0 ? 30 : 0)
            let dataInicio = item.inicio.clone().minute(minutosInicio)
            
            const minutosFinal = item.final.minutes() < 30 ? 0 : 30
            let dataFinal = item.final.clone().minute(minutosFinal)
            
            while(dataFinal.diff(dataInicio, 'minutes', true) >= 0){
              const horarioIndice = dataInicio.format('DD/MM/YYYY-HH:mm')
              if(!this.salaDisponibilidades.hasOwnProperty(horarioIndice)) {
                this.salaDisponibilidades[horarioIndice] = {}
              }
              dataInicio.add('minutes', 30)
            }
          });
        }
      }
    },
    verificarAgendamentoDisponibilidade() {
      this.agendamentoSemDisponibilidade = false
      Object.values(this.semana).map((semanaItem, semanaIndice) => {
        let dataTemp = this.filteredDates[semanaIndice]
        for (const [key, value] of Object.entries(semanaItem)) {
          if(!this.salaDisponibilidades.hasOwnProperty(dataTemp + '-' + key)){
            this.agendamentoSemDisponibilidade = true
            return
          }
        }
      })
    },

    setCard (item, rowLength, position) {
      const datetime = new Date(item.inicio)

      const time = this.getTime(datetime)
      const nextTime = this.getNextTime(time)
      const diaSemana = datetime.getDay()

      if (item.contrato) {
        item.nome = item.contrato.aluno.pessoa.nome_contato
        item.livro = item.contrato.livro.descricao
        item.instrutor = item.funcionario.apelido
      } else if (!item.reagendamento) {
        item.nome = '<novo>'
      }

      if (!this.semana[diaSemana][time]) {
        this.semana[diaSemana][time] = (new Array(rowLength)).fill(position)
      }

      if (!this.semana[diaSemana][nextTime]) {
        this.semana[diaSemana][nextTime] = (new Array(rowLength)).fill(position)
      }

      const indexOfNullTime = this.semana[diaSemana][time].indexOf(position)
      const indexOfNullNextTime = this.semana[diaSemana][nextTime].indexOf(position)

      if (indexOfNullTime >= indexOfNullNextTime) {
        this.semana[diaSemana][time].splice(indexOfNullTime, 1, item)
        this.semana[diaSemana][nextTime].splice(indexOfNullTime, 1, {...item, half_card: true})
      } else {
        this.semana[diaSemana][time].splice(indexOfNullNextTime, 1, item)
        this.semana[diaSemana][nextTime].splice(indexOfNullNextTime, 1, {...item, half_card: true})
      }
      this.verificarAgendamentoDisponibilidade()
    },

    setGhost (event, index, item, diaSemana) {
      if (![...event.target.classList].includes('card-wrapper') || index > 28) {
        return
      }

      const ghostStart = document.getElementById(`card-wrapper-${index}-${diaSemana}`)
      const ghostEnd = document.getElementById(`card-wrapper-${index + 1}-${diaSemana}`)

      let hasPositionStart = false
      let hasPositionEnd = false

      let isIndisponivelStart = false
      let isIndisponivelEnd = false

      const gStartChildren = [...ghostStart.children]
      const gEndChildren = [...ghostEnd.children]

      gStartChildren.map(item => {
        if (!hasPositionStart && [...item.classList].includes('position-card')) {
          hasPositionStart = true
        }

        if (!isIndisponivelStart && [...item.classList].includes('hora-indisponivel')) {
          isIndisponivelStart = true
        }
      })

      gEndChildren.map(item => {
        if (!hasPositionEnd && [...item.classList].includes('position-card')) {
          hasPositionEnd = true
        }

        if (!isIndisponivelEnd && [...item.classList].includes('hora-indisponivel')) {
          isIndisponivelEnd = true
        }
      })

      const doesNotHaveStartPosition = gStartChildren.length === 3 && !hasPositionStart
      const doesNotHaveEndPosition = gEndChildren.length === 3 && !hasPositionEnd
      if (index === 28 || isIndisponivelEnd || isIndisponivelStart || doesNotHaveStartPosition || doesNotHaveEndPosition) {
        this.placeCard = false
        return
      }
      this.placeCard = true

      ghostStart.classList.add('ghost-start')
      ghostEnd.classList.add('ghost-start')
      ghostEnd.classList.add('ghost-end')

      // const time_in = ghostStart.parentElement.nextElementSibling
      const timeOut = ghostEnd.parentElement.parentElement.nextElementSibling
      if (timeOut) {
        // time_in.classList.add('time-in')
        timeOut.classList.add('time-out')
      }
    },
    resetGhost () {
      const ghost = [...document.getElementsByClassName('ghost-start')]
      ghost.map(item => {
        item.classList.remove('ghost-start')
        item.classList.remove('ghost-end')
      })

      const timeend = [...document.getElementsByClassName('time-out')]
      if (timeend.length) {
        timeend[0].classList.remove('time-out')
      }
    },

    hoverCard (index, card) {
      if (!card.id) {
        return
      }

      const table = document.querySelector('#table-personal tbody')
      const hoverCard = document.getElementsByName(`card-${index}-${card.id}`)[0]
      const cardInfo = hoverCard.lastElementChild

      // TABLE
      const tWidth = table.offsetWidth
      const tHeight = table.offsetHeight

      // HOVER CARD
      const hWidth = hoverCard.offsetWidth
      const hLeft = hoverCard.offsetLeft
      const hTop = hoverCard.offsetTop
      const hHeight = hoverCard.offsetHeight

      // CARD INFO
      const iWidth = cardInfo.offsetWidth
      const iHeight = cardInfo.offsetHeight

      if ((hLeft + hWidth + iWidth) > tWidth) {
        cardInfo.style.left = `-${iWidth}px`
      } else {
        cardInfo.style.left = `${hWidth}px`
      }

      if ((hTop + hHeight + iHeight) > tHeight) {
        cardInfo.style.bottom = 0
      } else {
        cardInfo.style.top = 0
      }

      cardInfo.classList.add('show-info')
    },
    resetHoverCard () {
      const info = document.getElementsByClassName('show-info')
      if (info.length) {
        info[0].removeAttribute('style')
        info[0].classList.remove('show-info')
      }
    },

    getTime (datetime) {
      let hora = datetime.getHours()
      hora = (hora < 10 ? '0' : '') + hora

      let minuto = datetime.getMinutes()
      minuto = minuto >= 30 ? '30' : '00'

      return hora + ':' + minuto
    },

    getNextTime (time) {
      time = time.split(':')
      let hora = time[0] * 1
      let minuto = time[1]

      if (time[1] === '30') {
        hora = hora + 1
        minuto = '00'
      } else {
        minuto = '30'
      }

      if (hora < 10) {
        hora = `0${hora}`
      }

      return hora + ':' + minuto
    },

    // SELECT CARD ========================================================================================================================================================================
    selectCard (event, item) {
      const el = event.target
      const name = el.getAttribute('name')

      if (!this.clickCard || name === null || name.includes('undefined') || this.$store.state.contrato.itemSelecionado.agendamento_personal.length) {
        return
      }

      let selected = [...document.querySelectorAll('.card-selected')]
      if (selected.length && name !== selected[0].getAttribute('name')) {
        selected.map(sel => {
          sel.classList.remove('card-selected')
        })
      }

      const cards = [...document.getElementsByName(name)]

      if (item.reagendamento && !el.unlocked) {
        const permissao = this.permissoes['SEGUNDO_REAGENDAMENTO']
        EventBus.$emit('unlockRequestModal', { dataId: permissao.descricao, personal: el, acao_sistema: permissao.id, show: true })
        return
      }

      cards.map(i => {
        i.classList.toggle('card-selected')
      })

      this.cardReagendamento = document.querySelectorAll('.card-selected').length ? item : null

      this.$emit('select', item)
    },

    adicionarAgendamento ($event, options) {
      if (this.placeCard && $event.target.classList.contains('card-wrapper')) {
        if (this.cardReagendamento) {
          if (this.$store.state.contrato.itemSelecionado.agendamento_personal.length) {
            return
          }

          const [horas, minutos] = options.horario.split(':')
          const data = moment(options.data, 'DD/MM/YYYY').hours(horas).minutes(minutos)

          const reagendamento = {
            personalId: isNaN(this.cardReagendamento.id * 1) ? this.cardReagendamento.id.split('-')[1] : this.cardReagendamento.id,
            nome: this.cardReagendamento.nome,
            livro: this.cardReagendamento.livro,
            instrutor: this.cardReagendamento.instrutor,
            contratoReagendamento: this.cardReagendamento.contrato,
            alunoReagendamento: this.cardReagendamento.contrato.aluno,

            inicio: data,
            origem: this.cardReagendamento.inicio,
            datasReagendamentoPersonals: [], // TODO: verificar se virá de this.cardReagendamento
            reagendamento: true
            // reagendado: true
          }

          this.$store.state.contrato.itemSelecionado.agendamento_personal.push(reagendamento)
        }

        this.$emit('adicionar-agendamento', options)
      }
    },

    reagendar () {
    },

    /* ======================================================================================================================================================================== */

    voltar () {
      this.isEdit = false
      this.$store.commit('turmaAula/LIMPAR_ITEM_SELECIONADO')
      this.resetDiario()

      this.$router.push('/cadastros/turma')
    },

    salvar (saveClose) {
      this.isEnviando = true

      if (this.$v.$invalid) {
        this.isEdit = false
        this.isValid = false
        this.isEnviando = false
        return
      }

      this.lancarAtualizarFrequencias(this.salvarTurmaAula()).then(() => {
        if (saveClose) {
          this.voltar()
          return
        }

        this.buildDiario()

        this.isEnviando = false
      })
        .catch((error) => {
          console.info(error)
          this.isEnviando = false
        })
    },

    carregarAula (licao) {
      this.setLicaoPlanejada(licao)
      this.historicoModal = false
    },

    limparAgendamento () {
      this.cardReagendamento = null
    }
  }
}
</script>
<style scoped>
.check-homework {
  display: flex;
  align-items: center;
  line-height: 1;
  margin-right: .5rem;
}
.check-homework > div {
  padding-left: .5rem;
}

#table-personal.table-borderless.table-hover tbody tr {
  position: relative;
}

.table-hover tbody tr:hover .form-control:not(input[disabled='disabled']) {
  background-color: #fff;
}
.input-group-text {
  background-color: transparent;
}
tr input[type="text"] {
  text-transform: uppercase;
}

/* .main .container-fluid .animated .table-responsive-sm, .main .container-fluid form .table-responsive-sm {
  min-height: 180px;
} */

.content-sector {
  flex-grow: 1;
}

.notas-column {
  padding-right: 3.5rem;
}
td.size-75 input {
  max-width: 30px;
}

.table-card-hover th {
  display: block;
  text-align: center;
}
/* .table-card-hover.table tbody tr td:hover {
  background-color: aqua;
} */
/* .table-card-hover.table tbody tr:hover {
  border-top: 1px solid aqua;
} */

#table-personal.table-borderless.table-hover tbody tr:hover {
  background-color: transparent;
}

/* GHOST */
.table-card-hover.table tbody tr td {
  overflow: visible;
}
.table-card-hover.table tbody tr.time-out:before,
#table-personal.table-borderless.table-hover tbody tr:hover:before {
  content: '';
  position: absolute;
  top: -1px;
  width: 100%;
  border-top: 2px solid #FF3860;
  z-index: 2;
}
#table-personal.table-borderless.table-hover tbody tr:hover:before {
  pointer-events: none;
  border-top: 1px solid #50c4e6;
}
.table-card-hover.table tbody tr.time-out:after,
#table-personal.table-borderless.table-hover tbody tr:hover:after {
  content: '';
  position: absolute;
  border-width: 5px;
  border-top-color: #FF3860;
  border-left-color: #FF3860;
  border-right-color: #FF3860;
  border-bottom-color: transparent;
  border-style: solid;
}
#table-personal.table-borderless.table-hover tbody tr:hover:after {
  border-top-color: #50c4e6;
  border-left-color: transparent;
  border-right-color: transparent;
  border-bottom-color: transparent;
}
.table-card-hover.table tbody tr td div.ghost-start {
  cursor: pointer;
  position: relative;
  overflow: visible;
  background-color: #a7e7b0;
}
.table-card-hover.table tbody tr td div.ghost-start.ghost-end:before,
.half-card:before {
  content: '';
  cursor: inherit;
  width: calc(100% + 2px);
  height: 5px;
  background-color: inherit;
  position: absolute;
  top: -4px;
  left: -1px;
  border: inherit;
  border-top: 0;
  border-bottom: 0;
  z-index: inherit;
}
.half-card:before {
  width: 100%;
  left: 0;
  top: -5px;
  z-index: 1;
}
.list-group-item.ct-card.half-card:hover:before {
  z-index: 2;
}

.table-card-hover.table tbody tr td div.ghost-start .ct-card {
  z-index: 1;
}

.table-card-hover .list-group-item:hover, .table-card-hover .list-group-item:focus {
  z-index: initial;
}

.table-schedule th,
.table-schedule th span {
  font-weight: normal;
}
.table-schedule th {
  color: #6e6e6e;
}
.table-schedule th span {
  color: #151b1e;
  display: block;
}

/* ------ */
/* CARD */
.half-card .card-name {
  display: none;
}

.ct-card {
  display: flex;
  position: relative;
  padding-left: 1rem;
  cursor: default;
  min-width: 100%;
  max-width: 100%;
  padding: 0;
  margin: -1px 0;
  align-items: center;
  font-size: 0.7rem;
  background-color: #f3f3f3;
  border: 0;
  text-align: center;
}
.ct-card > div:first-child {
  pointer-events: none;
}
/* HOVER CARD */
.hover-card {
  display: flex;
  flex-direction: column;
  opacity: 0;
  visibility: hidden;
  position: absolute;
  width: 150px;
  /* height: 68px; */
  height: auto;
  color: #29363d;
  background-color: #ffffff;
  padding: .5rem;
  border-radius: 3px;
  top: -140%;
  left: -200%;
  box-shadow: 0 1px 2px 1px rgba(0,1,0,.2);
  z-index: 3;
  transition: opacity .2s ease-in;
  text-align: left;
}
.hover-card.show-info {
  opacity: 1;
  visibility: visible;
}
/* ------- */
/* POSITION CARD */
.position-card {
  background-color: transparent;
  border: 0;
  cursor: inherit;
}
/* ------- */
/* AGENDADO/REAGENDAMENTO CARD */
.ct-card:not(.half-card) .agendado-card {
  border-top: 3px solid #56f436;
  width: 100%;
}
.ct-card:not(.half-card) .reagendamento-card {
/* .reagendamento-card { */
  border-top: 3px solid #F44336;
  width: 100%;
}
/* .ct-card[data-reagendado="true"] {
  background-color: #FFDD57;
} */

 .data-origem-reagendado {
  border-top: 1px solid rgba(0,1,0,.2);
  padding-top: .3rem;
  margin-top: .3rem
 }

/* ------- */
/* HORA INDISPONÍVEL */
.hora-indisponivel.ct-card {
  cursor: not-allowed;
  background-color: #e2e2e2;
  padding: 9px;
  width: 0%;
  margin: -1px;
}
.hora-indisponivel.ct-card:before {
  content: '';
  width: 3px;
  height: 100%;
  position: absolute;
  background-color: inherit;
  top: 0;
  right: -3px;
}

.table-schedule.table tbody tr td:not(:last-child) .hora-indisponivel.ct-card:last-child:before {
  content: '';
  border-right: 1px dashed #c2cfd6;
}
/* ------- */
/* CARD SELECTED */
.ct-card.card-selected:not(.card-wrapper) {
  color: #ffffff;
  background-color: #a5a5a5;
}
/* ------- */

.table-schedule tbody td span {
  font-size: x-small;
}

.table-schedule th, .table-schedule td {
  padding: 0;
}

.table-schedule th:first-child,
.table-schedule td:first-child {
  max-width: 50px;
  font-size: 0.65rem;
  padding: 1px;
}

.table-schedule td:first-child {
  border-right: 1px solid #dbdeea;
  font-weight: 900;
}
.table-schedule.table-scroll tbody tr,
.table-schedule.table-scroll tbody td {
  min-height: unset;
  height: 20px;
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

.card-wrapper:not(.position-card) {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  grid-column-gap: 2px;
  width: 100%;
  border: 1px solid transparent;
}

.table-schedule.table tbody tr td:not(:first-child):not(:last-child) {
  border-right: 1px dashed #c2cfd6;
}

.table-schedule.table tbody tr:not(:first-child) td.ocorrencias {
  border-color: #ffffff;
}

.nao-alocar {
  background-color: #ccc;
}
.dia-da-semana {
  text-transform: capitalize;
}
.half-card {
  height: 100%;
}
</style>
