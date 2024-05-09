<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit && estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <h5 class="title-module mb-2">Agendamento</h5>

      <div class="form-group row">
        <div class="col">
          <label for="titulo" class="col-form-label">Título *</label>
          <input id="titulo" v-model="cardAgendamento.titulo" type="text" class="form-control" required maxlength="255">
          <div class="invalid-feedback">Preencha o título!</div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'agenda_compromisso-data_hora_inicio'" for="data_hora_inicio" class="col-form-label">Data</label>
          <date-picker id="data_hora_inicio" v-model="cardAgendamento.data_inicio" :popover="{ visibility: 'click' }" :disabled="cardAgendamento.possui_periodo_atrelado" required />
          <div v-if="!isValid && !cardAgendamento.data_inicio" class="multiselect-invalid">
            Selecione uma data!
          </div>
          <div v-if="!isEdit && cardAgendamento.data_fim && validarData()" class="floating-message bg-danger">
            Data inicial deve ser menor que a data final!
          </div>
        </div>

        <div v-if="!cardAgendamento.id" class="col-md-6">
          <b-form-checkbox id="periodo" v-model="periodo" @input="setDataFinal()">Repetir evento até</b-form-checkbox>
          <date-picker id="data_hora_fim" v-model="cardAgendamento.data_fim" :popover="{ visibility: 'click' }" :disabled="!periodo" required @input="setDataFinal" />
          <div v-if="!isValid && periodo && !cardAgendamento.data_fim" class="multiselect-invalid">
            Selecione uma data!
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-6">
          <label for="hora_inicio" class="col-form-label">Início *</label>
          <input v-mask="'##:##'" id="hora_inicio" v-model="cardAgendamento.hora_inicio" :class="{'is-invalid' : (!isValid && !cardAgendamento.hora_inicio) || !$v.cardAgendamento.hora_inicio.validateHour}" :readonly="readOnly" type="text" class="form-control" maxlength="5" placeholder="horário">
          <div v-if="!$v.cardAgendamento.hora_inicio.validateHour" class="invalid-feedback">
            Horário inválido
          </div>
          <div v-if="!isValid && !cardAgendamento.hora_inicio" class="multiselect-invalid">
            Informe um horário!
          </div>
        </div>

        <div class="col-md-6">
          <label for="hora_fim" class="col-form-label">Fim *</label>
          <input v-mask="'##:##'" id="hora_fim" v-model="cardAgendamento.hora_fim" :class="{'is-invalid' : (!isValid && !cardAgendamento.hora_fim) || !$v.cardAgendamento.hora_fim.validateHour || !$v.cardAgendamento.hora_fim.comparaHora}" :readonly="readOnly" type="text" class="form-control" maxlength="5" placeholder="horário">
          <div v-if="!$v.cardAgendamento.hora_fim.validateHour" class="invalid-feedback">
            Horário inválido
          </div>
          <div v-if="!isValid && !cardAgendamento.hora_fim" class="multiselect-invalid">
            Informe um horário!
          </div>
          <div v-if="!$v.cardAgendamento.hora_fim.comparaHora" class="input-invalid">
            Horário de término deve ser maior que horário de início.
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col">
          <label for="tipo_agendamento" class="col-form-label">Tipo agendamento *</label>
          <g-select id="tipo_ocorrencia"
                    :select="setTipoAgendamento"
                    :value="cardAgendamento.tipo_agendamento"
                    :options="listaTipoAgendamento"
                    :class="!isValid && !cardAgendamento.tipo_agendamento ? 'invalid-input' : 'valid-input'"
                    class="multiselect-truncate"
                    required
                    label="descricao"
                    track-by="id"
          />
          <div v-if="!isValid && !cardAgendamento.tipo_agendamento" class="input-invalid">
            Selecione o tipo de agendamento!
          </div>
        </div>

        <div class="col">
          <label for="funcionario" class="col-form-label">Responsável *</label>
          <g-select id="funcionario"
                    :select="setFuncionario"
                    :value="cardAgendamento.funcionario"
                    :options="listaFuncionario"
                    :class="!isValid && !cardAgendamento.funcionario ? 'invalid-input' : 'valid-input'"
                    class="multiselect-truncate"
                    required
                    label="apelido"
                    track-by="id"
          />
          <div v-if="!isValid && !cardAgendamento.funcionario" class="input-invalid">
            Selecione o responsável!
          </div>
        </div>
      </div>

      <div v-if="!readOnly" class="form-group row">
        <div class="col">
          <label for="descricao" class="col-form-label">Descrição</label>
          <b-form-textarea
            id="descricao"
            v-model="cardAgendamento.descricao"
            class="full-textarea"
            rows="3"
          />
        </div>
      </div>

      <div class="form-group row">
        <div class="col">
          <b-form-checkbox
            id="privado"
            v-model="cardAgendamento.privado"
          >
            Compromisso pessoal
          </b-form-checkbox>
        </div>
      </div>

      <div class="form-group pt-2 mb-0 d-flex">
        <b-btn v-if="cardAgendamento.possui_periodo_atrelado" :disabled="enviando" variant="verde" @click="alterarPeriodo(cardAgendamento)">{{ enviando ? 'Salvando...': 'Salvar' }}</b-btn>
        <b-btn v-else :disabled="enviando" type="submit" variant="verde">{{ enviando ? 'Salvando...': 'Salvar' }}</b-btn>

        <b-btn variant="link" @click="cancelarDados()">Cancelar</b-btn>
        <b-btn v-if="isEdit" variant="outline-danger" class="ml-auto" @click="excluir(cardAgendamento, true)">Excluir</b-btn>
      </div>

    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {dateToCompare, converteHorarioParaBanco} from '../../utils/date'
import {validateHour} from '../../utils/validators'
import {required} from 'vuelidate/lib/validators'
import DatePicker from '../../components/fields/DatePicker'
import moment from 'moment'
import EventBus from '../../utils/event-bus'

const comparaHora = (value, vm) => {
  if (vm.hora_fim !== '' && vm.hora_inicio !== '') {
    return vm.hora_inicio < vm.hora_fim
  }

  return true
}

export default {
  name: 'FormularioAgendaCompromisso',
  components: {
    DatePicker
  },

  props: {
    cardAgendamento: {
      type: Object,
      default: null,
      required: false
    },

    readOnly: {
      type: Boolean,
      default: false,
      required: false
    },

    cancelarDados: {
      required: true,
      type: Function,
      default: null
    },

    excluir: {
      required: true,
      type: Function,
      default: null
    },

    alterarPeriodo: {
      required: true,
      type: Function,
      default: null
    },

    events: {
      type: Array,
      default: null,
      required: true
    },

    agenda: {
      type: Object,
      default: null,
      required: false
    },

    listarEventos: {
      required: true,
      type: Function,
      default: null
    }

  },
  data () {
    return {
      isValid: true,
      isEdit: false,
      enviando: false,

      data_hora_inicio: '',
      data_hora_fim: '',

      periodo: false,

      eventos: []
    }
  },
  computed: {
    ...mapState('root', ['usuarioLogado']),
    ...mapState('agendaCompromisso', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('tipoAgendamento', {listaTipoAgendamentoRequisicao: 'lista'}),
    ...mapState('tipoOcorrencia', {listaTipoOcorrenciaRequisicao: 'lista'}),
    ...mapState('funcionario', {listaFuncionarioRequisicao: 'lista'}),

    listaTipoAgendamento: {
      get () {
        return this.listaTipoAgendamentoRequisicao
      }
    },

    listaTipoOcorrencia: {
      get () {
        return this.listaTipoOcorrenciaRequisicao
      }
    },

    listaFuncionario: {
      get () {
        return [this.usuarioLogado.funcionarios[0]].concat(this.listaFuncionarioRequisicao.filter(item => (item.cargo.tipo !== 'ASG')))
      }
    },

    endDate: {
      get () {
        return this.agenda.view.endDate
      }
    }
  },
  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()
    this.listarTipoAgendamento()
    this.listarTipoOcorrencia()

    this.setFuncionario(this.usuarioLogado.funcionarios[0])
  },

  validations: {
    cardAgendamento: {
      titulo: {required},
      hora_inicio: {validateHour},
      hora_fim: {validateHour, comparaHora}
    }
  },

  methods: {
    ...mapMutations('agendaCompromisso', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('tipoAgendamento', {listarTipoAgendamento: 'listar'}),
    ...mapActions('tipoOcorrencia', {listarTipoOcorrencia: 'listar'}),
    ...mapActions('agendaCompromisso', ['buscar', 'criar', 'atualizar', 'verificaDisponibilidadeFuncionario']),

    init () {
      this.isValid = true
      this.eventos = this.events
      setTimeout(() => {
        if (this.cardAgendamento.id) {
          this.isEdit = true
        } else {
          this.periodo = false
        }
      }, 100)
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$emit('fechar')
    },

    dataInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== undefined
    },

    setTipoAgendamento (value) {
      this.cardAgendamento.tipo_agendamento = value
    },

    setFuncionario (value) {
      this.cardAgendamento.funcionario = value
    },

    setTipoOcorrencia (value) {
      this.cardAgendamento.tipo_ocorrencia = value
    },

    setDataFinal (value) {
      if (!this.periodo) {
        setTimeout(() => {
          this.cardAgendamento.data_fim = null
        }, 100)
        return
      }

      if (value > this.endDate) {
        this.listarEventos({data_hora_fim: value}).then(list => (this.eventos = list))
      }
    },

    validarData () {
      const mInicio = moment(this.cardAgendamento.data_inicio).format('DD/MM/YYYY')
      const mFim = moment(this.cardAgendamento.data_fim).format('DD/MM/YYYY')

      return this.dataInvalida(mInicio, mFim)
    },

    confirmarEvento () {
      const c = this.cardAgendamento

      let mIni = moment(c.data_inicio)
      const mFim = moment(c.data_fim ? c.data_fim : c.data_inicio)

      const horaInicio = c.hora_inicio
      const horaFim = c.hora_fim

      let confirmacao = false
      while (mIni.format('YYYY-MM-DD') <= mFim.format('YYYY-MM-DD')) {
        confirmacao = this.eventos.some(e => {
          const dateStart = e.data_hora_inicio.split('T')[0]

          if (mIni.format('YYYY-MM-DD') === dateStart) {
            const eInicio = moment(e.data_hora_inicio).format('HH:mm')
            const eFim = moment(e.data_hora_fim).format('HH:mm')

            return (horaInicio <= eInicio && horaInicio < eFim) ||
              (horaFim > eInicio && horaFim <= eFim) ||
              (horaInicio < eFim && horaFim > eFim)
          }
        })

        if (confirmacao) {
          EventBus.$emit('chamarModal', {
            resolve: success => {
              this.salvarEvento()
            }
          }, `Já existe um evento agendado com este horário. Deseja continuar mesmo assim?`)

          break
        }

        mIni.add(1, 'days')
      }

      return confirmacao
    },

    salvar () {
      this.isValid = true
      if (!this.isValid || this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (!this.confirmarEvento()) {
        this.salvarEvento()
      }
    },

    salvarEvento () {
      this.enviando = true
      const dataFim = !this.periodo ? this.cardAgendamento.data_inicio : this.cardAgendamento.data_fim

      this.cardAgendamento.data_hora_inicio = converteHorarioParaBanco(this.cardAgendamento.hora_inicio, this.cardAgendamento.data_inicio)
      this.cardAgendamento.data_hora_fim = converteHorarioParaBanco(this.cardAgendamento.hora_fim, dataFim)
      this.cardAgendamento.tipo_agendamento = this.cardAgendamento.tipo_agendamento.id
      this.cardAgendamento.funcionario = this.cardAgendamento.funcionario.id

      const item = {...this.cardAgendamento}
      this.SET_ITEM_SELECIONADO(item)

      if (this.cardAgendamento.id) {
        this.atualizar().then(() => {
          this.voltar()
          this.cancelarDados()
          this.enviando = false
        }).catch(console.error)
      } else {
        this.criar().then(() => {
          this.voltar()
          this.cancelarDados()
          this.enviando = false
        }).catch(console.error)
      }
    }
  }
}
</script>
