<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit || salvando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="form-group row">
        <h5>Cadastro de Bônus Class</h5>
      </div>

      <div class="form-group row">
        <b-col md="4">
          <label for="formulario_agendamento_professor_instrutor" class="col-form-label">Instrutor</label>
          <g-select :id="'formulario_agendamento_professor_instrutor'"
                    v-model="itemSelecionado.funcionario"
                    :options="listaDeFuncionario"
                    :disabled="reading"
                    class="multiselect-truncate"
                    label="apelido"
                    track-by="id"
          />
        </b-col>

        <b-col md="4">
          <label for="formulario_agendamento_professor_data" class="col-form-label">Data</label>
          <v-date-picker
            v-model="itemSelecionado.data_aula"
            :input-props="{ id: 'formulario_agendamento_professor_data', class: 'form-control', placeholder: 'Data', required: true, autocomplete: 'off' }"
            :popover="{ visibility: 'click' }"
            :first-day-of-week="1"
            :disabled-dates="{ weekdays: [1,2,3,4,5,7]}"
            :attributes="attributes"
            is-required
          />
        </b-col>

        <b-col md="4">
          <label for="formulario_agendamento_professor_sala" class="col-form-label">Sala</label>
          <g-select :id="'formulario_agendamento_professor_sala'"
                    v-model="itemSelecionado.sala_franqueada"
                    :options="listaDeSalaFranqueada"
                    :disabled="reading"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id"
          />
        </b-col>
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'filtro_rapido-bonus-class_horario_aulas'" class="col-form-label" for="formulario_agendamento_professor_horario_inicio">Horário das aulas</label>
          <div class="row">
            <div class="col">
              <div class="input-group d-flex">
                <div class="input-group-prepend">
                  <div class="input-group-text">De</div>
                </div>
                <!-- <input v-mask="'##:##'" id="formulario_agendamento_professor_horario_inicio" :class="{'valid-input' : !isValid}" v-model="itemSelecionado.horario_inicio" class="form-control" required> -->
                <g-select
                  :id="'formulario_agendamento_professor_horario_inicio'"
                  :value="itemSelecionado.horario_inicio"
                  :select="setHorarioInicio"
                  :options="listaDeHorarios"
                  :class="!isValid && !horarioInicio ? 'invalid-input' : 'valid-input'"
                  :disabled="reading"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id"/>
              </div>
              <div v-if="!isValid && $v.horarioInicio.$invalid" class="floating-message bg-danger">
                {{ !$v.horarioInicio.validateHour ? 'Horário inválido' : 'Campo obrigatório' }}
              </div>
            </div>
            <div class="col">
              <div class="input-group d-flex">
                <div class="input-group-prepend">
                  <div class="input-group-text">Até</div>
                </div>
                <!-- <input v-mask="'##:##'" id="formulario_agendamento_professor_horario_termino" :class="{'valid-input' : !isValid}" v-model="itemSelecionado.horario_termino" class="form-control" required> -->
                <g-select
                  :id="'formulario_agendamento_professor_horario_termino'"
                  :value="itemSelecionado.horario_termino"
                  :select="setHorarioFinal"
                  :options="listaDeHorarios"
                  :class="!isValid && !horarioTermino ? 'invalid-input' : 'valid-input'"
                  :disabled="reading"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id"/>
              </div>
              <div v-if="!isValid && $v.horarioTermino.$invalid" class="floating-message bg-danger">
                {{ !$v.horarioTermino.validateHour ? 'Horário inválido' : 'Campo obrigatório' }}
              </div>
            </div>
          </div>
          <div v-if="horarioFormularioInvalido(horarioInicio,horarioTermino)" class="floating-message bg-danger">
            Horário inicial deve ser menor que a horário final!
          </div>
        </div>
      </div>

      <div class="form-group pt-2">
        <template v-if="!notValid && !reading">
          <b-btn type="submit" variant="verde">Salvar</b-btn>
          <b-btn type="button" variant="verde" @click="salvar(salvarESair)" >Salvar e Sair</b-btn>
        </template>
        <template v-if="notValid && !reading">
          <div class="list-group-item list-group-item-accent-warning  list-group-item-warning border-0">
            Instrutor já possui agendamento na data, sala e horário informados.
          </div>
        </template>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import { required } from 'vuelidate/lib/validators'
import { validateHour } from '../../utils/validators'
import { stringToISODate, dateToString } from '../../utils/date'
import moment from 'moment'

export default {
  name: 'AgendamentoProfessor',
  data () {
    return {
      isValid: true,
      isEdit: false,
      reading: false,
      salvarESair: true,
      salvando: false,
      id: null,
      horarioInicio: null,
      horarioTermino: null,
      mensagem: '',
      attributes: [
        {
          highlight: { class: 'today-mark' },
          dates: this.getFriday()
        }
      ]
    }
  },

  computed: {
    ...mapState('bonusClass', {listaDeBonus: 'lista', itemSelecionado: 'itemSelecionado', itemSelecionadoID: 'itemSelecionadoID', estaCarregando: 'estaCarregando'}),
    ...mapState('funcionario', {listaDeFuncionarioRequisicao: 'lista'}),
    ...mapState('salaFranqueada', {listaDeSalaFranqueadaRequisicao: 'lista'}),
    ...mapState('sala', {listaDeSalaRequisicao: 'lista'}),

    dateToString: dateToString,
    listaDeHorarios: {
      get () {
        let lista = []
        const horarioInicio = 7
        const horarioFinal = 22
        for (let hora = horarioInicio; hora <= horarioFinal; hora++) {
          let horario = hora >= 10 ? '' : '0'
          horario += hora

          if (hora >= horarioInicio) {
            let objHora = {}

            objHora.id = horario + ':00'
            objHora.descricao = horario + ':00'
            objHora.value = horario + ':00'

            lista.push(objHora)
          }

          if (hora !== horarioFinal) {
            let objHora = {}

            objHora.id = horario + ':30'
            objHora.descricao = horario + ':30'
            objHora.value = horario + ':30'

            lista.push(objHora)
          }
        }

        return [{id: null, descricao: ''}, ...lista]
      }
    },
    listaDeFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaDeFuncionarioRequisicao.filter(func => func.instrutor || func.cargo.tipo === 'COP')]
      }
    },

    listaDeSalaFranqueada: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaDeSalaFranqueadaRequisicao]
      }
    },

    notValid: {
      get () {
        let notValidate = true

        const obj = this.listaDeBonus.find((item) => {
          if (item.situacao === 'PEN') {
            // Validar data
            // const dataItensDaLista = this.dateToString(item.data_aula)
            // const dataDoItem = this.stringToISODate(this.itemSelecionado.data_aula).split('T')[0]

            const dataItensDaLista = moment(new Date(item.data_aula)).format('DD/MM/YYYY')
            const dataDoItem = moment(this.itemSelecionado.data_aula).format('DD/MM/YYYY')

            // Validar funcionario
            const funcItensDaLista = item.funcionario ? item.funcionario.id : null
            const funcDoItem = this.itemSelecionado.funcionario ? this.itemSelecionado.funcionario.id : null
            // Validar sala,,
            const salaFranqueadaItensDaLista = item.sala_franqueada ? item.sala_franqueada.id : null
            const salaFranqueadaDoItem = this.itemSelecionado.sala_franqueada ? this.itemSelecionado.sala_franqueada.id : null
            // Periodo de aula
            const horarioInicioItemSelecionado = this.itemSelecionado.horario_inicio.value ? this.itemSelecionado.horario_inicio.value : null
            const horarioFinalItemSelecionado = this.itemSelecionado.horario_termino.value ? this.itemSelecionado.horario_termino.value : null

            const hIDoItemDaLista = item.horario_inicio.match(/(\d{2,2}):(\d{2,2})/)[0]
            const hFDoITemDaLista = item.horario_termino.match(/(\d{2,2}):(\d{2,2})/)[0]

            let listaDeIntervalo = this.listaDeHorarios.map((hora) => {
              if ((hora.value >= hIDoItemDaLista) && (hora.value <= hFDoITemDaLista)) {
                return hora.value
              }
            })

            let listaDeIntervalo2 = this.listaDeHorarios.map((hora) => {
              if ((hora.value >= horarioInicioItemSelecionado) && (hora.value <= horarioFinalItemSelecionado)) {
                return hora.value
              }
            })

            let match = this.verificarEncontros(listaDeIntervalo, listaDeIntervalo2)

            if (dataItensDaLista === dataDoItem) {
              if (match > 1) {
                if ((((!funcItensDaLista === false) && (!funcDoItem === false)) && (funcItensDaLista === funcDoItem)) ||
                  ((((!salaFranqueadaItensDaLista === false) && (!salaFranqueadaDoItem === false))) && (salaFranqueadaItensDaLista === salaFranqueadaDoItem))) {
                  return item
                }
              }
            }
          }
        })

        if (obj) {
          if (obj.id === this.itemSelecionado.id) {
            // SE OBJETO ENCONTRADO FOR O PRÒPRIO ELEMENTO
            notValidate = false
          } else {
            notValidate = true
          }
        } else {
          notValidate = false
        }

        return notValidate
      }
    }
  },

  mounted () {
    this.listarCamposDinamicos()
    this.LIMPAR_ITEM_SELECIONADO()
    this.itemSelecionado.data_aula = this.getFriday()
    this.$emit('modalAgendamentoProfessorReady')
    console.log("call modalAgendamentoProfessorReady")
  },

  validations: {
    itemSelecionado: {
      data_aula: { required }
    },
    horarioInicio: { required, validateHour },
    horarioTermino: { required, validateHour }
  },

  methods: {
    ...mapMutations('bonusClass', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('bonusClass', ['buscar', 'criar', 'atualizar']),

    stringToISODate: stringToISODate,

    getFriday () {
      const valueOfFriday = 5
      let now = new Date()
      return now.addDays(valueOfFriday - now.getDay())
    },

    listarCamposDinamicos () {
      if (this.$store.state.estaCarregando === false) {
        let filtros = {}
        filtros.instrutor = true

        this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
        this.$store.commit('funcionario/SET_LISTA', [])
        this.$store.commit('funcionario/SET_FILTROS', filtros)
        this.$store.dispatch('funcionario/listar')
        this.$store.commit('funcionario/LIMPAR_FILTROS')
      }

      this.$store.commit('salaFranqueada/SET_PAGINA_ATUAL', 1)
      this.$store.commit('salaFranqueada/SET_LISTA', [])
      this.$store.commit('salaFranqueada/SET_FILTRO_APENAS_SALA_ATIVA', true)
      this.$store.dispatch('salaFranqueada/listar')

      this.$store.commit('sala/SET_PAGINA_ATUAL', 1)
      this.$store.commit('sala/SET_LISTA', [])
      this.$store.dispatch('sala/listar')
    },

    setHorarioInicio (value) {
      this.itemSelecionado.horario_inicio = value
      this.horarioInicio = value.value
    },

    setHorarioFinal (value) {
      this.itemSelecionado.horario_termino = value
      this.horarioTermino = value.value
    },

    horarioFormularioInvalido (hInicio, hFim) {
      return (hInicio !== '' && hFim !== '') && (hInicio > hFim)
    },

    setData (value) {
      this.itemSelecionado.data_aula = value
    },

    voltar () {
      this.horarioInicio = ''
      this.horarioTermino = ''
      this.LIMPAR_ITEM_SELECIONADO()
      this.isEdit = false
      this.$emit('cancelar')
    },

    salvar (bSalvarESair = false) {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.salvando = true
      this.itemSelecionado.data_aula = moment(this.itemSelecionado.data_aula).format('DD/MM/YYYY')
      this.itemSelecionado.horario_inicio = this.horarioInicio
      this.itemSelecionado.horario_termino = this.horarioTermino

      if (this.itemSelecionadoID) {
        this.atualizar().then(() => {
          if (bSalvarESair) {
            this.voltar()
          } else {
            this.openEdit(this.itemSelecionadoID)
          }
        }).catch(() => {
          console.error('error')
        })
      } else {
        this.criar().then(() => {
          if (bSalvarESair) {
            this.voltar()
          } else {
            this.LIMPAR_ITEM_SELECIONADO()
            this.resetData()
          }
        }).catch((e) => {
          console.error('error', e)
        })
      }

      this.isValid = true
    },

    resetData () {
      this.isEdit = false
      this.reading = false
      this.horarioInicio = null
      this.horarioTermino = null
    },

    openEdit (id, reading = false) {
      this.reading = reading
      if (id) {
        this.resetData()
        this.isEdit = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscar().then(() => {
          this.itemSelecionado.horario_inicio = this.listaDeHorarios.find((horario) => `${horario.id}` === `${this.itemSelecionado.horario_inicio}`)
          this.itemSelecionado.horario_termino = this.listaDeHorarios.find((horario) => `${horario.id}` === `${this.itemSelecionado.horario_termino}`)

          this.horarioInicio = this.itemSelecionado.horario_inicio.value
          this.horarioTermino = this.itemSelecionado.horario_termino.value
        })
      }
    },

    verificarEncontros (listaDeIntervalo, listaDeIntervalo2) {
      let encontros = 0
      listaDeIntervalo.map((itemListaUm) => {
        listaDeIntervalo2.map((itemListaDois) => {
          if ((!itemListaUm === false) && (!itemListaDois === false)) {
            if (itemListaDois === itemListaUm) {
              encontros++
            }
          }
        })
      })
      return encontros
    }

  }
}
</script>
