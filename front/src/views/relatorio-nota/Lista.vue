<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado">

      <div class="form-group row">

        <div class="col-md-4">
          <label class="col-form-label">Período</label>
          <div class="row">
            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">De</div>
                </div>
                <g-datepicker :element-id="'data_periodo_de'" :value="filtros.data_periodo_de" :selected="setDataPeriodoDe"/>
              </div>
            </div>

            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">A</div>
                </div>
                <g-datepicker :element-id="'data_periodo_ate'" :value="filtros.data_periodo_ate" :selected="setDataPeriodoAte" class="datepicker-right"/>
              </div>
            </div>
          </div>
          <div v-if="dataPeriodoInvalida()" class="floating-message bg-danger">
            Data inicial deve ser menor que a data final!
          </div>
        </div>
        
        <div class="col-md-3">
          <label v-help-hint="'filtro-turma_curso'" for="curso" class="col-form-label">Curso</label>
          <g-select
            id="curso"
            v-model="filtros.curso"
            :options="listaCursosSelect"
            class="multiselect-truncate"
            label="descricao"
            track-by="id" />
        </div>


        <b-col md="4">
              <label v-help-hint="'filtroRapido-aluno_nome_aluno'" for="nome_aluno" class="col-form-label">Aluno</label>
              <typeahead id="nome_aluno"  :item-hit="setAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
          </b-col>



        <div class="col-md-auto">
          <label for="modalidade_turma" class="col-form-label d-block">Modalidade da turma</label>
          <b-form-checkbox-group id="modalidade_turma" v-model="filtros.modalidade_turma" :options="listaModalidadesTurmaCheckboxes"
                                 buttons button-variant="cinza" name="modalidade_turma" class="checkbtn-line fill-width"/>
        </div>

        <div class="col-md-auto">
          <label for="situacao_turma" class="col-form-label d-block">Situação da turma</label>
          <b-form-checkbox-group id="situacao_turma" v-model="filtros.situacao_turma" :options="situacoesTurma" buttons button-variant="cinza" name="situacao_turma" class="checkbtn-line fill-width"/>
        </div>

        <div class="col-md-3">
          <label v-help-hint="'form-contrato_turma'" for="turma" class="col-form-label">Turma</label>
          <div class="d-flex">
            <g-select
              id="turma"
              v-model="filtros.turma"
              :options="listaTurmasSelect"
              class="flex-grow-1"
              label="turmaDescricao"
              track-by="turmaId"/>
          </div>
        </div>

        <div class="col-md-auto" style="margin-top: auto;">
          <b-form-checkbox id="agrupar_turma" v-model="filtros.agrupar_turma">Agrupar por turma</b-form-checkbox>
        </div>

        <div class="col-md-3">
          <label for="livro" class="col-form-label">Livro</label>
          <g-select
            id="livro"
            :value="filtros.livro"
            :options="listaLivrosSelect"
            class="valid-input"
            label="descricao"
            track-by="id"
            @input="setLivro" />
        </div>

        <div class="col-md-3">
          <label for="instrutor_personal" class="col-form-label">Professor</label>
          <g-select
            id="instrutor_personal"
            v-model="filtros.instrutor"
            :options="listaInstrutores"
            class="valid-input"
            label="apelido"
            track-by="id" />
        </div>

      </div>
      <div class="body-sector p-2">
        <h5 data-v-14b0d2e2="" class="title-module mb-2">Notas entre</h5>
        <div class="form-group row">

          <div class="col-md-3">
            <label v-help-hint="'filtroAvancado-contas-pagar_valor'" class="col-form-label">Mid term</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">De</div>
                  </div>
                  <the-mask id="valor_mid_term_min" :mask="['#,##', '##,##']" v-model="filtros.valor_mid_term_min" type="text" masked class="form-control" />
                </div>
              </div>
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">a</div>
                  </div>
                  <the-mask id="valor_mid_term_max" :mask="['#,##', '##,##']" v-model="filtros.valor_mid_term_max" type="text" masked class="form-control" />
                </div>
              </div>
            </div>
            <div v-if="notaMidTermInvalida" class="floating-message bg-danger">
              Valor máximo do Mid term deve ser superior ao valor mínimo
            </div>
          </div>

          <div class="col-md-3">
            <label v-help-hint="'filtroAvancado-contas-pagar_valor'" class="col-form-label">Final test</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">De</div>
                  </div>
                  <the-mask id="valor_final_test_min" :mask="['#,##', '##,##']" v-model="filtros.valor_final_test_min" type="text" masked class="form-control" />
                </div>
              </div>
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">a</div>
                  </div>
                  <the-mask id="valor_final_test_max" :mask="['#,##', '##,##']" v-model="filtros.valor_final_test_max" type="text" masked class="form-control" />
                </div>
              </div>
            </div>
            <div v-if="notaFinalTestInvalida" class="floating-message bg-danger">
              Valor máximo do Final test deve ser superior ao valor mínimo
            </div>
          </div>

          <div class="col-md-3">
            <label v-help-hint="'filtroAvancado-contas-pagar_valor'" class="col-form-label">WG</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">De</div>
                  </div>
                  <the-mask id="valor_wg_min" :mask="['#,##', '##,##']" v-model="filtros.valor_wg_min" type="text" masked class="form-control" />
                </div>
              </div>
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">a</div>
                  </div>
                  <the-mask id="valor_wg_max" :mask="['#,##', '##,##']" v-model="filtros.valor_wg_max" type="text" masked class="form-control" />
                </div>
              </div>
            </div>
            <div v-if="notaWGInvalida" class="floating-message bg-danger">
              Valor máximo do WG deve ser superior ao valor mínimo
            </div>
          </div>

        </div>
      </div>

      <div class="row">
        <div class="col-md-9">
        </div>
        <div class="col-md-3">
          <b-btn :disabled="!podeGerarRelatorio()" class="btn btn-cinza btn-block text-uppercase" @click="abrirRelatorio()">Gerar relatório</b-btn>
        </div>
      </div>

    </div>
  </div>
</template>

<script>

import {mapState, mapActions} from 'vuex'
import open from '../../utils/open'
import {dateToCompare, stringToISODateMinMax} from '../../utils/date'

export default {
  name: 'ListaRelatorioAlunosSituacao',

  data () {
    return {
      situacoesTurma: [
        {text: 'Aberta', value: 'ABE'},
        {text: 'Encerrada', value: 'ENC'},
        {text: 'Em formação', value: 'FOR'}
      ],
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: true
      }
    }
  },

  computed: {
    ...mapState('relatorioContratos', ['filtros']),
    ...mapState('modalidadeTurma', {listaModalidadesTurma: 'lista'}),
    ...mapState('curso', {listaCursos: 'lista'}),
    ...mapState('funcionario', {listaFuncionarios: 'lista'}),
    ...mapState('livro', {listaLivros: 'lista'}),
    ...mapState('turma', {listaTurmas: 'lista'}),

    ...mapState('aluno',{listaAlunos: 'lista'}),

   // ...mapState('aluno', ['estaCarregando', 'todosItensCarregados', 'totalItens', 'lista', 'variante', 'filtros']),
    // Trocando os nomes das labels pra estar de acordo com o que é aceito no checkboxgroup
    listaModalidadesTurmaCheckboxes: {
      get () {
        const modalidades = this.listaModalidadesTurma.filter(mod => mod.tipo !== 'PER').map(mod => {
          return {text: mod.descricao, value: mod.tipo}
        })
        return modalidades
      }
    },
    listaInstrutores: {
      get () {
        const lista = this.listaFuncionarios && this.listaFuncionarios.length ? this.listaFuncionarios.filter(func => func.instrutor === true) : []
        return [{apelido: 'Selecione', id: null}].concat(lista)
      }
    },
    listaCursosSelect: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.listaCursos)
      }
    },
    listaLivrosSelect: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.listaLivros)
      }
    },

    listaAlunosSelect: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.listaAlunos)
      }
    },

    listaTurmasSelect: {
      get () {
        return [{turmaDescricao: 'Selecione', turmaId: null}].concat(this.listaTurmas)
      }
    },
    notaMidTermInvalida: {
      get () {
        return this.filtros.valor_mid_term_max && this.filtros.valor_mid_term_min && parseFloat(this.filtros.valor_mid_term_max) < parseFloat(this.filtros.valor_mid_term_min)
      }
    },
    notaFinalTestInvalida: {
      get () {
        return this.filtros.valor_final_test_max && this.filtros.valor_final_test_min && parseFloat(this.filtros.valor_final_test_max) < parseFloat(this.filtros.valor_final_test_min)
      }
    },
    notaWGInvalida: {
      get () {
        return this.filtros.valor_wg_max && this.filtros.valor_wg_min && parseFloat(this.filtros.valor_wg_max) < parseFloat(this.filtros.valor_wg_min)
      }
    }
  },

  mounted () {
    this.listarTodosCursos(true)
    this.listarFuncionarios(true)
    this.$store.commit('livro/SET_PAGINA_ATUAL', 1)
    this.listarLivros()
    this.$store.commit('modalidadeTurma/SET_PAGINA_ATUAL', 1)
    this.listarModalidadeTurma()
    this.listarTurmas()
    this.$store.commit('aluno/SET_PAGINA_ATUAL', 1)
    this.listarAlunos()
  },

  methods: {
    dateToCompare: dateToCompare,

    ...mapActions('curso', {listarTodosCursos: 'listar'}),
    ...mapActions('funcionario', {listarFuncionarios: 'listar'}),
    ...mapActions('livro', {listarLivros: 'listar'}),
    ...mapActions('modalidadeTurma', {listarModalidadeTurma: 'listar'}),
    ...mapActions('turma', {listarTurmas: 'listarTodos'}),

    ...mapActions('aluno',{listarAlunos: 'listar'}),



    setAluno (value) {
      this.filtros.aluno = value
    },

    setLivro (value) {
      this.filtros.livro = value
    },

    setDataPeriodoDe (value) {
      this.filtros.data_periodo_de = value
      this.$forceUpdate()
    },

    setDataPeriodoAte (value) {
      this.filtros.data_periodo_ate = value
      this.$forceUpdate()
    },

    dataPeriodoInvalida () {
      return dateToCompare(this.filtros.data_periodo_de) > dateToCompare(this.filtros.data_periodo_ate) && this.filtros.data_periodo_ate !== ''
    },

    podeGerarRelatorio () {
      return !this.dataPeriodoInvalida() && !this.notaMidTermInvalida && !this.notaFinalTestInvalida && !this.notaWGInvalida
    },

    abrirRelatorio () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const filtrosRelatorio = this.converterDadosParaLink()
      const urlRelatorio = `/api/relatorios/nota?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}${filtrosRelatorio}`
      console.log(urlRelatorio)
      open(urlRelatorio, '_blank')
    },

    transformarFloat (valor) {
      if (!valor) {
        return null
      }
      valor += ''
      return valor.replace(',', '.') * 1
    },

    converterDadosParaLink () {
      const form = {...this.filtros}

      const valorWgMin = this.filtros.valor_wg_min ? this.transformarFloat(this.filtros.valor_wg_min) : null
      const valorWgMax = this.filtros.valor_wg_max ? this.transformarFloat(this.filtros.valor_wg_max) : null
      const valorMidTermMin = this.filtros.valor_mid_term_min ? this.transformarFloat(this.filtros.valor_mid_term_min) : null
      const valorMidTermMax = this.filtros.valor_mid_term_max ? this.transformarFloat(this.filtros.valor_mid_term_max) : null
      const valorFinalTestMin = this.filtros.valor_final_test_min ? this.transformarFloat(this.filtros.valor_final_test_min) : null
      const valorFinalTestMax = this.filtros.valor_final_test_max ? this.transformarFloat(this.filtros.valor_final_test_max) : null
      
      const dados = {
        data_periodo_de: form.data_periodo_de ? stringToISODateMinMax(form.data_periodo_de) : null,
        data_periodo_ate: form.data_periodo_ate ? stringToISODateMinMax(form.data_periodo_ate, true) : null,
        curso: form.curso ? form.curso.id : null,
        modalidade_turma: form.modalidade_turma && form.modalidade_turma.length > 0 ? form.modalidade_turma : null,
        situacao_turma: form.situacao_turma && form.situacao_turma.length > 0 ? form.situacao_turma : null,
        turma: form.turma ? form.turma.turmaId : null,
        agrupar_turma: form.agrupar_turma ? form.agrupar_turma : null,
        livro: form.livro ? form.livro.id : null,
        instrutor: form.instrutor ? form.instrutor.id : null,
        valor_mid_term_min: valorMidTermMin,
        valor_mid_term_max: valorMidTermMax,
        valor_final_test_min: valorFinalTestMin,
        valor_final_test_max: valorFinalTestMax,
        valor_wg_min: valorWgMin,
        valor_wg_max: valorWgMax,

        aluno: form.aluno ? form.aluno.id : null,
      }

      let dadosArray = []
      for (let key in dados) {
        if (dados[key] !== null) {
          if (dados[key] instanceof Array) {
            dados[key].forEach(element => {
              dadosArray.push(`${key}[]=${element}`)
            })
          } else {
            dadosArray.push(`${key}=${dados[key]}`)
          }
        }
      }
   
      let retorno = dadosArray.length > 0 ? '&' : ''
      retorno += dadosArray.join('&')
      return retorno
    }
  }
}
</script>
<style scoped>

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
</style>
