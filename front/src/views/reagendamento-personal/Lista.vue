<template>
  <div class="animated fadeIn">
    <div v-if="carregandoInformacoesPersonal" class="form-loading">
      <load-placeholder :loading="carregandoInformacoesPersonal" />
    </div>

    <form :class="{ 'was-validated': personalConfirmado }" class="needs-validation form-personal" novalidate @submit.prevent="buscarAgendamentos()">
      <b-row class="form-group mb-0">
        <b-col sm="4" md="">
          <label v-help-hint="'filtro_rapido-diario_personal_instrutor'" for="instrutor" class="col-form-label">Instrutor *</label>
          <g-select id="instrutor"
                    v-model="instrutor"
                    :select="setInstrutor"
                    :options="listaFuncionario"
                    :class="personalConfirmado && !instrutor ? 'invalid-input' : 'valid-input'"
                    required
                    class="multiselect-truncate"
                    label="apelido"
                    track-by="id"
          />
          <div v-if="personalConfirmado && !instrutor" class="multiselect-invalid">
            Selecione uma opção!
          </div>
        </b-col>

        <b-col sm="4" md="">
          <label for="sala_franqueada" class="col-form-label">Sala *</label>
          <g-select
            id="sala_franqueada"
            v-model="sala_franqueada"
            :select="setSala"
            :options="listaSalasFranqueada"
            :class="personalConfirmado && !sala_franqueada ? 'invalid-input' : 'valid-input'"
            required
            class="multiselect-truncate"
            label="descricao"
            track-by="id" />
          <div v-if="personalConfirmado && !sala_franqueada" class="multiselect-invalid">
            Selecione uma opção!
          </div>
        </b-col>

        <b-col sm="2">
          <label for="filtro_data_personal" class="col-form-label">Data *</label>
          <g-datepicker v-model="dataFiltroPersonal" element-id="filtro_data_personal" />
        </b-col>

        <b-col>
          <label class="col-form-label d-block">&nbsp;</label>
          <b-btn type="submit" variant="roxo" block>Buscar agendamentos</b-btn>
        </b-col>
      </b-row>
    </form>

    <p v-if="$store.state.agendamentoPersonal.lista.length > 0 && !filtrarNovamente" class="mb-1">Selecione um <b>card personal</b> abaixo para ser reagendado:</p>

    <!-- GPERSONAL -->
    <g-personal ref="calendarioPersonal" :dates="weekDates" click-card is-reagendamento @select="selectCard()" />

    <div class="mb-3">
      <div v-if="$store.state.agendamentoPersonal.lista.length > 0">
        <label class="col-form-label">Data e horário para reagendamento:</label>

        <div v-if="!filtrarNovamente && !itemSelecionado.agendamento_personal.length" :class="{'list-group-item list-group-item-accent-danger list-group-item-danger border-0' : personalConfirmado && !cardSelected}">
          Selecione a data e horário de reagendamento acima.
        </div>

        <div v-if="filtrarNovamente" class="list-group-item list-group-item-accent-warning list-group-item-warning border-0 mt-2">
          Busque os agendamentos confome os novos parâmetros antes de realizar o reagendamento.
        </div>

        <div>
          <b-btn v-for="(agendamento, index) in itemSelecionado.agendamento_personal" :key="index" class="mr-2" variant="roxo" @click="removerAgendamento(index)">
            {{ agendamento.inicio | formatarDataHora }} &times;
          </b-btn>
        </div>
      </div>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center px-0">
      <div class="col-12 row m-0 p-0">
        <b-col sm="" md="auto" class="pr-0">
          <div class="info-btn">
            <b-btn :disabled="carregandoInformacoesPersonal || filtrarNovamente" variant="azul" @click="salvar()">{{ carregandoInformacoesPersonal ? 'Reagendando...': 'Reagendar' }}</b-btn>
          </div>
        </b-col>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {beginOfDay} from '../../utils/date'
import {required} from 'vuelidate/lib/validators'
import moment from 'moment'

export default {
  name: 'ReagendamentoPersonal',
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

      // instrutor: {id: null, apelido: 'Selecione'},
      instrutor: '',
      // sala_franqueada: {id: null, descricao: 'Selecione'},
      sala_franqueada: '',
      data: moment().format('DD/MM/YYYY'),

      situacao: {id: 'T', descricao: 'Todos'},

      dataFiltroPersonal: moment().format('DD/MM/YYYY'),

      carregandoInformacoesPersonal: false,

      card: null,
      cardList: [],
      cardSelected: 0,
      personalConfirmado: false,
      filtrarNovamente: false,
      invalid: false
    }
  },
  validations: {
    instrutor: {required},
    sala_franqueada: {required}
  },
  computed: {
    ...mapState('personal', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    ...mapState('salaFranqueada', {listaSalasFranqueada: 'lista'}),
    ...mapState('contrato', ['itemSelecionado']),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionariosRequisicao]
      }
    },

    listaSalasFranqueada: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.salaFranqueada.lista.filter(item => item.personal))
      }
    },

    weekDates: {
      get () {
        const dates = {}
        let selectedDate = moment(this.dataFiltroPersonal, 'DD/MM/YYYY').startOf('week')
        dates[0] = selectedDate.format('DD/MM/YYYY')

        for (let i = 1; i < 7; i++) {
          selectedDate = selectedDate.add(1, 'day')
          dates[i] = selectedDate.format('DD/MM/YYYY')
        }

        return dates
      }
    }

  },
  mounted () {
    // this.SET_PAGINA_ATUAL(1)
    // this.SET_LISTA([])
    // this.filtrar()
    this.limparAgendamento()
    this.listarCamposSelects()

    // this.buscarAgendamentos()
  },
  methods: {
    ...mapActions('personal', {listarItens: 'listar', reagendar: 'reagendar'}),
    ...mapMutations('personal', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY', 'SET_FILTROS']),

    buscarAgendamentos () {
      if (!this.instrutor.id || !this.sala_franqueada.id) {
        return false
      }

      this.carregandoInformacoesPersonal = true
      this.limparAgendamento()
      const data = moment(this.dataFiltroPersonal, 'DD/MM/YYYY').hour(0).minutes(0).second(0).toISOString()
      this.$store.commit('agendamentoPersonal/SET_FILTROS', { funcionario: this.instrutor.id, sala_franqueada: this.sala_franqueada.id, data })
      this.$store.commit('indisponibilidadePersonal/SET_FILTROS', { data })

      Promise.all([
        this.$store.dispatch('agendamentoPersonal/listar'),
        this.$store.dispatch('indisponibilidadePersonal/listar')
      ]).then(() => {
        this.carregandoInformacoesPersonal = false
        this.filtrarNovamente = false
      })
    },

    selectCard (card) {
      this.cardSelected = document.querySelectorAll('.card-selected').length
    },

    limparAgendamento () {
      this.itemSelecionado.agendamento_personal = []
    },

    removerAgendamento (index) {
      this.itemSelecionado.agendamento_personal.splice(index, 1)
    },

    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_LISTA', [])
      this.$store.commit('funcionario/SET_FILTROS', { instrutor_personal: true, consultor_ou_gestor_comercial: false })
      this.$store.commit('salaFranqueada/SET_PAGINA_ATUAL', 1)

      this.$store.dispatch('salaFranqueada/listar')
        .then(() => {
          if (this.listaSalasFranqueada.length === 2) {
            this.sala_franqueada = this.listaSalasFranqueada[1]
          }

          this.buscarAgendamentos()
        })

      this.$store.dispatch('funcionario/listar')
        .then(() => {
          if (this.listaFuncionario.length === 2) {
            this.instrutor = this.listaFuncionario[1]
          }

          this.buscarAgendamentos()
        })

      this.$store.commit('funcionario/SET_LIMPAR_FILTROS')
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

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    editar (id) {

    },

    diarioPersonal (id) {
      this.$router.push(`${this.$route.path}/diario-personal/${id}`)
    },

    salvar () {
      this.personalConfirmado = true

      if (this.$v.$invalid || !this.cardSelected || !this.itemSelecionado.agendamento_personal.length) {
        this.invalid = true
        return
      }

      this.invalid = false
      this.carregandoInformacoesPersonal = true
      const agendamentoPersonal = this.itemSelecionado.agendamento_personal[0]

      const personal = {
        contrato: agendamentoPersonal.contratoReagendamento.id,
        aluno: agendamentoPersonal.alunoReagendamento.id,
        agendamento_personal: agendamentoPersonal.personalId,
        data_reagendada: agendamentoPersonal.inicio.subtract(3, 'hour').toISOString(),
        funcionario: this.instrutor.id,
        sala_franqueada: this.sala_franqueada.id
      }

      this.itemSelecionado.agendamento_personal = []

      this.reagendar(personal).then(() => {
        this.listarCamposSelects()
        this.buscarAgendamentos()
        this.personalConfirmado = false

        this.$refs.calendarioPersonal.limparAgendamento()
      }).catch((err) => {
        this.listarCamposSelects()
        this.buscarAgendamentos()
        this.personalConfirmado = false

        this.$refs.calendarioPersonal.limparAgendamento()

        console.log(err)
      })
    },

    setInstrutor (value) {
      // this.resetPersonal()
      this.instrutor = value.id === null ? '' : value
    },

    setSala (value) {
      // this.resetPersonal()
      this.sala_franqueada = value.id === null ? '' : value
    },

    resetPersonal () {
      this.filtrarNovamente = true
      this.removerAgendamento(0)
      this.personalConfirmado = false
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
