<template>
  <div>
    <b-modal id="modalAgendaPersonal" ref="modalAgendaPersonal" v-model="visible" size="xl" title="Horários do Personal" no-close-on-backdrop hide-footer>
      <div v-if="estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <form action="submitForm" class="needs-validation form-personal" novalidate>
        <b-row class="form-group mb-4">
          <b-col sm="4" md="">
            <label for="sala_franqueada" class="col-form-label">Sala *</label>
            <g-select
              id="sala_franqueada"
              v-model="form.sala_franqueada"
              :select="setSala"
              :options="listaSalas"
              :class="!form.sala_franqueada ? 'invalid-input' : 'valid-input'"
              required
              class="multiselect-truncate"
              label="descricao"
              track-by="id" />
          </b-col>
          <b-col sm="4">
            <div class="d-flex align-items-end">
              <button class="btn-verde h-75 mx-2 w-auto" type="button" @click="voltarSemana">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="arcs"><path d="M15 18l-6-6 6-6"></path></svg>
              </button>
              <div>
                <label for="filtro_data_personal" class="col-form-label">Data *</label>
                <g-datepicker v-model="form.dataFiltroPersonal" element-id="filtro_data_personal" />
              </div>
              <button class="btn-verde h-75 mx-2 w-auto" type="button" @click="avancarSemana">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="arcs"><path d="M9 18l6-6-6-6"></path></svg>
              </button>
            </div>
            <div v-if="!form.dataFiltroPersonal && form.pristine" class="multiselect-invalid" style="position: relative; width: fit-content; margin: 0 auto;">
              Selecione uma opção!
            </div>
          </b-col>
        </b-row>
      </form>
      <hr>

      <g-agenda-personal ref="agendaPersonal" :dataDeEntrada="form.dataFiltroPersonal || null" :dados="dados"></g-agenda-personal>

      <hr>
      <b-row class="m-0 d-flex justify-content-between">
        <button @click="closeModal" class="btn btn-azul">
          Voltar
        </button>
        <button class="btn btn-verde" @click="formSubmit">
          Confirmar
        </button>
      </b-row>
    </b-modal>
  </div>
</template>
<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {firstDayOfWeek, lastDayOfWeek, nextWeek, previousWeek, formatarDataPadraoBrasileiro, formatarDataPadraoBancoDados} from '../../utils/date'
import moment from 'moment'

export default {
  name: 'ModalAgendaPersonal',
  props: {
  },

  data () {
    return {
      colorPalette: [
      '#FF7474',
      '#85FF73',
      '#73B4FF',
      '#DC73FF',
      '#FFDA73',
      '#73FFD5',
      '#7373FF',
      '#FF73B7',
      '#FF9873',
      '#DCFF73'
      ],
      visible: false,
      dados: {},
      form: {
        pristine: false,
        dataFiltroPersonal: '',
        sala_franqueada: null
      }
    }
  },
  computed: {
    ...mapState('salaFranqueada', {listaSalasFranqueada: 'lista'}),
    ...mapState('agendamentoPersonal', {estaCarregando: 'estaCarregando'}),

    filtradoPorSala: {
      get() {
        return this.form.sala_franqueada?.id != null
      }
    },
    listaSalas: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaSalasFranqueada]
      }
    },
  },
  validations: {
  },
  mounted() {
    this.listarCamposSelects()
  },
  watch: {
    form: {
      handler() {
        this.formSubmit()
      },
      deep: true
    }
  },
  methods: {
    ...mapActions('agendamentoPersonal', {
      getAgendamentos: 'buscarAgendamentos',
      setFiltros: 'setFiltrosAgenda'
    }),
    ...mapActions('salaFranqueada', ['listarDisponibilidade']),
    ...mapMutations('salaFranqueada', ['SET_ITEM_SELECIONADO']),

    formSubmit() {
      this.form.pristine = true
      if (!this.form.dataFiltroPersonal) {
        return false
      }
      this.setFiltros(this.formarFiltros())
      this.buscarAgendamentos()
    },

    voltarSemana() {
      if(this.form.dataFiltroPersonal == '') {
        return
      }
      this.form.dataFiltroPersonal = formatarDataPadraoBrasileiro(previousWeek(this.form.dataFiltroPersonal))
    },

    avancarSemana() {
      if(this.form.dataFiltroPersonal == '') {
        return
      }
      this.form.dataFiltroPersonal = formatarDataPadraoBrasileiro(nextWeek(this.form.dataFiltroPersonal))
    },

    formarFiltros() {
      return {
        sala_franqueada: this.form.sala_franqueada?.id,
        data_inicial: formatarDataPadraoBancoDados(firstDayOfWeek(this.form.dataFiltroPersonal)),
        data_final: formatarDataPadraoBancoDados(lastDayOfWeek(this.form.dataFiltroPersonal))
      }
    },
    
    buscarAgendamentos() {
      this.getAgendamentos().then((data) => {
        this.organizarResultados(data)
      })
    },

    organizarResultados(data) {
      this.dados = {}
      this.dados['instrutores'] = {}
      if(!this.filtradoPorSala && (!'itens' in data || data.itens.length < 1)) {
        return
      }
      this.dados['filtros'] = this.formarFiltros()

      this.organizarResultadoAgrupadoPorFuncionario(data)
      this.organizarInstrutores()
      this.sintetizarDadosParaApresentacao()
      
      if(this.filtradoPorSala){
        this.organizarResultadoPorSala()
      }
    },

    organizarResultadoAgrupadoPorFuncionario(data) {
      data.itens.map((element) => {
        if(!(element.funcionario.id in this.dados.instrutores)) {
          this.dados.instrutores[element.funcionario.id] = element.funcionario
        }
        this.adicionarElementoEmDados(element, element.inicio)
      })
    },

    sintetizarDadosParaApresentacao() {
      for(const [index, element] of Object.entries(this.dados)) {
        if(element.length > 0) {
          let result = {}
          element.map((agendamento) => {
            if(!(agendamento.funcionario.id in result)) {
              result[agendamento.funcionario.id] = {
                alunos: [],
                iniciais: this.dados.instrutores[agendamento.funcionario.id].iniciais,
                instrutor: this.dados.instrutores[agendamento.funcionario.id].nome,
                count: 0,
                color: this.dados.instrutores[agendamento.funcionario.id].color
              }
            }
            result[agendamento.funcionario.id].count += 1
            result[agendamento.funcionario.id].alunos.push(agendamento.contrato.aluno.pessoa.nome_contato)
          })
          this.dados[index]['info'] = Object.values(result)
        }
      }
    },

    organizarResultadoPorSala() {
      this.listarDisponibilidade({
        data_inicial: formatarDataPadraoBancoDados(firstDayOfWeek(this.form.dataFiltroPersonal)),
        data_final: formatarDataPadraoBancoDados(lastDayOfWeek(this.form.dataFiltroPersonal))
      }
      ).then(
        data => {
          this.dados['disponibilidade'] = data.corpo
          this.$refs.agendaPersonal.montarDisponibilidade()
        },
        error => {
          console.error('não foi possível carregar os horários de disponibilidade.')
        }
      )
    },

    adicionarElementoEmDados(element, timestamp) {
      let dataInicio = moment(timestamp)
      let dataAgendada = dataInicio.format('DD/MM/YYYY-HH:mm')
      
      if(!(dataAgendada in this.dados)) {
          this.dados[dataAgendada] = []
        }
      this.dados[dataAgendada].push(element)

      dataAgendada = dataInicio.add(30, 'minute').format('DD/MM/YYYY-HH:mm')
      if(!(dataAgendada in this.dados)) {
        this.dados[dataAgendada] = []
      }
      this.dados[dataAgendada].push(element)
    },

    organizarInstrutores() {
      function obterIniciais(nome) {
        const palavras = nome.split(' ');
        let iniciais = '';

        for (let i = 0; i < (palavras.length > 2 ? 2 : palavras.length); i++) {
          if (palavras[i].length > 2) {
            iniciais += palavras[i][0].toUpperCase();
          }
        }

        return iniciais;
      }
      for (const [key, value] of Object.entries(Object.values(this.dados.instrutores))) {
        let indice = key % 10
        value.color = this.colorPalette[indice]
        value.iniciais = obterIniciais(value.apelido)
        value.nome = value.apelido
      }
    },
    
    listarCamposSelects() {
      this.$store.commit('salaFranqueada/SET_PAGINA_ATUAL', 1)
      this.$store.commit('salaFranqueada/SET_FILTRO_PERSONAL', 1)

      this.$store.dispatch('salaFranqueada/listar')
        .then(() => {
          if (this.listaSalasFranqueada.length === 2) {
            this.form.sala_franqueada = this.listaSalasFranqueada[1]
          }
        })
    },
    closeModal() {
      this.visible = false
    },
    setInstrutor (value) {
      this.form.instrutor = value.id === null ? '' : value
    },
    setSala (value) {
      this.SET_ITEM_SELECIONADO(value)
      this.form.sala_franqueada = value.id === null ? '' : value
    },
  }
}
</script>
<style scoped>
</style>
