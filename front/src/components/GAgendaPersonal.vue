<template>
  <div class="table-responsive-sm" v-if="('filtros' in dados)">
    <g-table id="table-personal" class="table-card-hover table-schedule">
      <thead>
        <tr>
          <th data-column="">&nbsp;</th>
          <th v-for="(diaDaSemana, index) in listaDias" :key="index" data-column="">
            <span class="text-uppercase">
              {{ diaDaSemana.diaDaSemana }}
            </span>
            <span>
              {{ diaDaSemana.diaDoMes }}
            </span>
          </th>
        </tr>
      </thead>
      <tbody>
        <div>
          <tr v-for="(horario, indiceHorario) in listaHorarios" :key="indiceHorario">
            <td>
              <span>{{ horario }}</span>
            </td>
            <td class="td-personal-info" v-for="(diaDaSemana, indiceSemana) in listaDias" :key="indiceSemana">
              <div v-if="dados[diaDaSemana.diaDoMes +'-'+ horario] && Object.keys(dados[diaDaSemana.diaDoMes +'-'+ horario]).length > 0">

                <div v-if="!dados.filtros.sala_franqueada">
                  <div class="hover-personal-info">
                    <div :style="{ '--custom-background-color': item.color }" v-for="(item, indice) in dados[diaDaSemana.diaDoMes +'-'+ horario].info" :key="indice">
                      <h5>
                        {{ item.instrutor }}:
                      </h5>
                      <p v-for="(aluno, indiceAluno) in item.alunos" :key="indiceAluno">{{aluno}}</p>
                    </div>
                  </div>
                  <span class="badge-instrutor" :style="{ 'background-color': item.color}" v-for="(item, indice) in dados[diaDaSemana.diaDoMes +'-'+ horario].info" :key="indice">
                    {{ item.iniciais + ' ' + item.count }}
                  </span>
                </div>
                <div v-if="dados.filtros.sala_franqueada">
                  <p v-bind:class="{ 'text-red': dados[diaDaSemana.diaDoMes +'-'+ horario].length > 2 }">
                      <span class="font-xs font-weight-bold">
                        {{ dados[diaDaSemana.diaDoMes +'-'+ horario].length }}
                      </span>
                      Aluno{{ (dados[diaDaSemana.diaDoMes +'-'+ horario].length > 1 ? 's' : '') }}
                    </p>
                </div>
              </div>
              <div class='nao-alocar' v-if="dados.filtros && dados.filtros.sala_franqueada && dados.hasOwnProperty('disponibilidade') && dados.disponibilidade.length > 1 && !dados[diaDaSemana.diaDoMes +'-'+ horario]">
              </div>
            </td>
          </tr>
        </div>
      </tbody>
    </g-table>
    <div v-if="dados.hasOwnProperty('disponibilidade') && dados.disponibilidade.length == 0">
      <span>A sala selecionada não possui horários de disponibilidade cadastrados</span>
    </div>
    <div id="legenda-instrutores" v-if="('filtros' in dados) && ('funcionario' in dados.filtros) && !dados.filtros.sala_franqueada && Object.keys(dados.instrutores).length > 0">
      <h4>Legenda:</h4>
      <p v-for="(instrutor, indiceInstrutor) in dados.instrutores" :key="indiceInstrutor">
        <span class="badge-instrutor" :style="{ 'background-color': instrutor.color}">{{ instrutor.iniciais}}</span>
        <span> - </span>
        <span>{{ instrutor.nome }}</span>
      </p>
    </div>
  </div>
</template>

<script>
import moment from 'moment'

export default {
  name: 'GAgendaPersonal',

  props: {
    dataDeEntrada: {
      type: String,
      required: false
    },
    dados: {
      type: Object,
      required: false
    }
  },

  data () {
    return {
      diaIndice: null,
      listaDias: [],
      listaHorarios: [],
    }
  },

  computed: {
  },

  watch: {
    dataDeEntrada() {
      this.montarSemana()
    }
  },

  mounted () {
    this.montarHorarios()
    this.montarSemana()
    this.montarDisponibilidade()
  },

  methods: {
    obterInfoDoInstrutor(id) {
      return this.dados.instrutores[id]
    },

    montarSemana() {
      let semana = this.dataDeEntrada ? moment(this.dataDeEntrada, 'DD/MM/YYYY').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD')
      this.diaIndice = moment(semana, 'YYYY-MM-DD').startOf('week').add(1, 'day')
      let ultimoDiaDaSemana = moment(semana, 'YYYY-MM-DD').endOf('week')

      this.listaDias = []

      if(this.diaIndice.diff(ultimoDiaDaSemana, 'day', true) < 1) {
        while(parseInt(this.diaIndice.diff(ultimoDiaDaSemana, 'day', true)) < 1) {
          this.listaDias.push({
            diaDaSemana: this.diaIndice.locale('pt-br').format('ddd'),
            diaDoMes: this.diaIndice.format('DD/MM/YYYY')
          })
          this.diaIndice.add(1, 'day')
        }
      }
    },

    montarHorarios() {
      let data = moment().hour(7).minute(0).second(0)
      while(data.hours() < 22 || (data.hours() == 22 && data.minutes() == 0)) {
        this.listaHorarios.push(data.format('HH:mm'))
        data.add(30, 'minute')
      }
    },

    montarDisponibilidade() {
      if(('disponibilidade' in this.dados) && this.dados.disponibilidade && this.dados.disponibilidade.length > 0) {
        this.oranizarDisponibilidade()
      }
      this.$forceUpdate()
    },

    oranizarDisponibilidade() {
      let agrupamento = {}
      this.dados.disponibilidade.map((element) =>{
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
              if(!this.dados.hasOwnProperty(horarioIndice)) {
                this.dados[horarioIndice] = {}
              }
              dataInicio.add('minutes', 30)
            }
          });
        }
      }
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

#table-personal.table-borderless.table-hover {
  height: fit-content !important;
  margin-bottom: 10px;
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
  background-color: #f4fcff;
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

.badge-instrutor {
  padding: 1px 4px;
  border-radius: 2px;
  font-weight: bolder;
  margin: 0 1px;
}

#legenda-instrutores p {
  display: inline-block;
  margin: 0 4px;
}
.hover-personal-info {
  position: fixed;
  display: flex;
  opacity: 0;
  transition: opacity 0.2s;
  width: 200px;
  z-index: 10;
  pointer-events: none;
  transform-origin: left;
  transform: translateX(-40%);
  text-align: left;
  flex-direction: column;
}
.hover-personal-info > div {
  background-color: var(--custom-background-color, #fff);
  padding: 4px;
}
.hover-personal-info * {
  font-size: 0.75rem !important;
}
.hover-personal-info p {
  display: block;
  margin: 0;
}
.td-personal-info:hover .hover-personal-info {
  transition: opacity 0.1s;
  opacity: 1;
}
.nao-alocar {
  background-color: #ccc;
  width: 100%;
}
</style>
