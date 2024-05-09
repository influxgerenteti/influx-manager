<template>
 <div class="animated fadeIn wrapper-table-scroll">
    <div class="no-print">
      <b-card no-body>
        <b-tabs card>
          <b-tab
          title="Filtros"
          :class="filtroVisivel ? null : 'collapsed'"
          :aria-expanded="filtroVisivel ? 'true' : 'false'"
          aria-controls="collapse-4"
          @click="filtroVisivel = !filtroVisivel"
          active>
            <b-card-text>
              <div class="filtro-avancado">
                <b-collapse id="collapse-4" v-model="filtroVisivel" class="mt-2">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro_avancado'"
                        for="data_inicial"
                        class="col-form-label">
                        Período
                      </label>
                      <g-data
                        :periodo="'dia_atual'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                  
                      
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-turma_modalidade_turma'"
                        for="modalidade_turma"
                        class="col-form-label"
                        >Modalidade da Turma</label
                      >
                      <g-select
                        id="modalidade_turma"
                        v-model="filtros.modalidade_turma"
                        :options="listaModalidadesTurma"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-turma_curso'"
                        for="curso"
                        class="col-form-label"
                        >Curso</label
                      >
                      <g-select
                        id="curso"
                        v-model="filtros.curso"
                        :options="listaCursosSelect"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-turma_livro'"
                        for="livro"
                        class="col-form-label"
                        >Livro</label
                      >
                      <g-select
                        id="livro"
                        v-model="filtros.livro"
                        :options="listaLivros"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </div>

                    <div class="col-md-3">
                      <label for="instrutor_personal" class="col-form-label">
                        Professor
                      </label>
                      <g-select
                        id="instrutor_personal"
                        v-model="filtros.instrutor"
                        :options="listaInstrutores"
                        class="valid-input"
                        label="apelido"
                        track-by="id"
                      />
                    </div>

                    <div class="col-md-3">
                      <b-col md="col-md-3">
                        <label for="licao" class="col-form-label">
                          Lição Aplicada
                        </label>
                        <g-select-licao
                          id="licao"
                          v-model="filtros.licao"
                          :livroId="filtros.livro && filtros.livro.id ? filtros.livro.id : 0">
                        </g-select-licao>
                      </b-col>
                    </div>

                    <b-col md="3">
                      <label for="idioma" class="col-form-label">
                        Idioma
                      </label>
                      <g-select-idioma
                        id="idioma"
                        v-model="filtros.idioma"
                        >
                      </g-select-idioma>
                    </b-col>

                    <b-col md="3">
                      <label v-help-hint="'filtroRapido-aluno_nome_aluno'" for="nome_aluno" class="col-form-label">Aluno</label>
                      <typeahead id="nome_aluno"  :item-hit="setAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
                    </b-col>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end">
                <div class="col-md-2" v-if="lista.length">
                    <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                    <g-excel
                        class="btn btn-cinza btn-block text-uppercase"
                        :data="lista"
                        :fields="exportFields"
                        type="xls"
                        name="relatorio-matriculas">
                  

                  
                        <font-awesome-icon icon="file-code" />
                       Exportar para Excel
                     
                  
                        
                    </g-excel>
                </div>
                <div class="col-md-auto">
                  <b-btn
                      class="btn btn-cinza btn-block text-uppercase wrap"
                      @click="abrirRelatorio()">
                   
                      Gerar relatório
                       
                  </b-btn>
                </div>
              </div>
            </b-card-text>
          </b-tab>
        </b-tabs>
      </b-card>
    </div>
    <div v-if="estaCarregando" class="d-flex h-100">
      <load-placeholder :loading="estaCarregando" />
    </div>
    <div class="tabela-wrapper">
    <b-table
      v-if="!estaCarregando"
      class="tabela-aulas-ocorridas"
      :busy="estaCarregando"
      :items="lista"
      :fields="cabecalho"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      striped
          show-empty
          hover
          outlined
          small
          fixed-header
          sort-icon-right>
      
      <template #empty>
            <h6>Nenhum registro a ser exibido.</h6>
      </template>
      
      <template #table-busy>
        <div class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Carregando Dados...</strong>
        </div>
      </template>

      <template #cell(data_aula)="data">
            {{ data.value | formatarDataHora }}
      </template>
      
      <!-- <template #cell(finalizada)="data">
        <div v-if="data.value" v-b-tooltip.viewport.top.hover title="Finalizada">
          <font-awesome-icon icon="fa-circle" style="color: var(--success)"/>
        </div>

        <div v-if="!data.value" v-b-tooltip.viewport.top.hover title="Andamento">
          <font-awesome-icon icon="fa-circle" style="color: var(--warning)"/>
        </div>
      </template> -->

      <template #cell(turma)="data">
        <div style="text-align: left">
          {{ data.value }}
        </div>
      </template>

      <template #cell(situacaoTurma)="data">
        <div style="text-align: center">
          <div
            v-b-tooltip.viewport.top.hover
            :title="data.item.situacao == 'ENC' ? 'Encerrada' : data.item.situacao == 'FOR' ? 'Formação' : 'Aberta'"
            style="display: inline; margin-right: 6px;">
            {{ data.item.situacao == 'ENC' ? 'Encerrada' : data.item.situacao == 'FOR' ? 'Formação' : 'Aberta' }}
          </div>
        </div>
      </template>

    </b-table>
  </div>
  </div>
</template>

<script>

import formatarDataHora from '@/filters/formatar-data-hora';
import moment from 'moment';
import {required} from 'vuelidate/lib/validators'
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToCompare } from "../../utils/date";

export default {
  name: "ListaRelatorioAulasOcorridas",
  data() {
    return {
      isValid: true,
      filtroVisivel: true,
      cabecalho : [
        { key : 'data_aula', label: 'Data Aula', sortable: true },
        { key : 'turma', label: 'Turma', sortable: true },
        { key : 'situacaoTurma', label: 'Situacão Turma', sortable: true },
        { key : 'licao', label: 'Lição', sortable: true },
        { key : 'livro', label: 'Livro', sortable: true },
        { key : 'professor', label: 'Professor', sortable: true },
      ],
      exportFields : {
        'Aula Finalizada' : {
          field: 'finalizada',
          callback: (value) => value ? 'Sim' : 'Não'
        },
        'Data da Aula' : {
          field : 'data_aula',
          callback: (value) => moment(value).format('DD/MM/YYYY HH:mm')
        },
        'Situação da Turma' : {
          field: 'situacao',
          callback: (value) => value == 'ABE' ? 'Aberta' : value == 'ENC' ? 'Encerrada' : 'Formação'
        },
        'Turma' : 'turma',
        'Lição' : 'licao',
        'Livro' : 'livro',
        'Professor' : 'professor',
      },
      sortBy : 'data_aula',
      sortDesc : false,
      data_inicial: "",
      data_final: "",
      modalidade_turma: null,
      curso: null,
      livro: null,
      instrutor: null,
      listaLicoesAplicadas: [],
      tagsLicoesAplicadas: [],
    };
  },

  computed: {
    ...mapState("relatorioTurma", ["filtros"]),
    ...mapState("modalidadeTurma", { listaModalidadesTurma: "lista" }),
    ...mapState("curso", { listaCursos: "lista" }),
    ...mapState("livro", { listaTodosLivros: "lista" }),
    ...mapState("funcionario", { listaFuncionarios: "lista" }),
    ...mapState('aluno',{listaAlunos: 'lista'}),


    ...mapState('personal', ['itemSelecionado', 'itemSelecionadoID']),
    ...mapState('diarioPersonal', ['listaDiario', 'contratoSelecionadoID', 'listaLicoesRealizadas']),
    ...mapState('funcionario', {listaInstrutores: 'lista'}),
    ...mapState('livro', {listaLivros: 'lista'}),
    ...mapState('relatorioAulasOcorridas', ['filtros', 'lista', 'estaCarregando']),

    listaAlunosSelect: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.listaAlunos)
      }
    },

    listaLivros: {
      get() {
        let lista = this.listaTodosLivros || [];
        if (this.filtros.curso && this.filtros.curso.id) {
          lista = this.listaTodosLivros.filter((livro) => {
            return (
              livro.curso.filter((curso) => {
                return this.filtros.curso.id === curso.id;
              }).length > 0
            );
          });
        }

        return [
          {
            id: null,
            descricao: lista.length > 0 ? "Selecione" : "Selecione o livro",
          },
        ].concat(lista);
      },
    },

    listaCursosSelect: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(this.listaCursos);
      },
    },
    listaInstrutores: {
      get() {
        const lista =
          this.listaFuncionarios && this.listaFuncionarios.length
            ? this.listaFuncionarios.filter((func) => func.instrutor === true)
            : [];
        return [{ apelido: "Selecione", id: null }].concat(lista);
      },
    },
    listaTurmasSelect: {
      get() {
        return [{ turmaDescricao: "Selecione", turmaId: null }].concat(
          this.listaTurmas
        );
      },
    },
  },

  buscarLicoesLivro(limparLicoesAplicadas) {
    this.buscarLicoesPorLivro().then((listaLicoes) => {
      this.listaLicoesAplicadas = listaLicoes;

      if (limparLicoesAplicadas) {
        this.tagsLicoesAplicadas = [];
      } else if (this.isEdit) {
        this.agendamentoSelecionado.alunoDiarioPersonal.aluno_diario_personal_licao.map(
          (item) => {
            this.setTagLicoesAplicadas(item);
          }
        );
      }
      this.isLoading = false;
    });
  },

  mounted() {
    this.$store.commit("modalidadeTurma/SET_PAGINA_ATUAL", 1);
    this.listarModalidadesTurma();
    this.listarTodosCursos(true);
    this.$store.commit("livro/SET_PAGINA_ATUAL", 1);
    this.$store.dispatch("livro/listar");
    this.listarFuncionarios(true);
    this.$store.commit('aluno/SET_PAGINA_ATUAL', 1)
    this.listarAlunos()

    this.idSelecionado = this.$route.params.id
    this.carregarDiario(this.idSelecionado)

    this.SET_LISTA([])
  },

  validations: {
    tagsLicoesAplicadas: {required}
  },
  

  methods: {
    dateToCompare: dateToCompare,

    ...mapActions("modalidadeTurma", { listarModalidadesTurma: "listar" }),
    ...mapActions("curso", { listarTodosCursos: "listar" }),
    ...mapActions("funcionario", { listarFuncionarios: "listar" }),
    ...mapMutations('licao', ['SET_LIVRO_SELECIONADO_ID']),
    ...mapActions('licao', ['buscarLicoesPorLivro']),
    ...mapActions('aluno',{listarAlunos: 'listar'}),

    ...mapMutations('diarioPersonal', ['SET_CONTRATO_SELECIONADO_ID', 'SET_ESTA_CARREGANDO', 'SET_AGENDAMENTO_PERSONAL', 'LIMPAR_AGENDAMENTO_PERSONAL']),
    ...mapMutations('licao', ['SET_LIVRO_SELECIONADO_ID']),
    ...mapMutations('personal', ['SET_ITEM_SELECIONADO_ID']),
    ...mapMutations('livro', {SET_PAGINA_ATUAL_LIVROS: 'SET_PAGINA_ATUAL'}),
    ...mapMutations('relatorioAulasOcorridas', ['SET_LISTA']),
    ...mapActions('diarioPersonal', ['buscarDiarioPorContrato', 'buscarLicoesAplicadasPorContrato', 'lancarAtualizarFrequencias']),
    ...mapActions('licao', ['buscarLicoesPorLivro']),
    ...mapActions('livro', {listarLivros: 'listar'}),
    ...mapActions('relatorioAulasOcorridas', ['listar']),

    carregarDiario (id) {
      this.isLoading = true

      this.SET_CONTRATO_SELECIONADO_ID(id)
      this.SET_PAGINA_ATUAL_LIVROS(1)
      this.listarLivros()

      this.buscarDiarioPorContrato()
        .then(() => {
          this.tagsLicoesAplicadas = []
          this.diarioPersonal.atividade_ca = 'false'
          this.diarioPersonal.atividade_ce = 'false'
          this.diarioPersonal.presenca = 'P'
          this.instrutorSubstituto = {id: null, apelido: 'Selecione'}
          this.diarioPersonal.observacao = ''

          this.agendamentoSelecionado = this.listaDiario.agendamentoPersonals.find(item => (item.finalizado === false))

          if (this.isEdit) {
            this.agendamentoSelecionado = this.listaDiario.agendamentoPersonals.find(item => (item.finalizado === true))
            this.todasAulasDadas = this.agendamentoSelecionado.length > 1

            if (this.agendamentoSelecionado.alunoDiarioPersonal.atividade_ca) {
              this.diarioPersonal.atividade_ca = this.agendamentoSelecionado.alunoDiarioPersonal.atividade_ca
            }
            if (this.agendamentoSelecionado.alunoDiarioPersonal.atividade_ca) {
              this.diarioPersonal.atividade_ce = this.agendamentoSelecionado.alunoDiarioPersonal.atividade_ce
            }

            this.diarioPersonal.presenca = this.agendamentoSelecionado.alunoDiarioPersonal.presenca

            const funcionario = this.agendamentoSelecionado.alunoDiarioPersonal.funcionario
            this.instrutorSubstituto = this.agendamentoSelecionado.funcionario.id !== funcionario.id ? funcionario : {id: null, apelido: 'Selecione'}
            this.diarioPersonal.observacao = this.agendamentoSelecionado.alunoDiarioPersonal.observacao
          }

          if (this.agendamentoSelecionado.alunoDiarioPersonal && this.agendamentoSelecionado.alunoDiarioPersonal.data_aula) {
            this.data_aula = this.agendamentoSelecionado.alunoDiarioPersonal.data_aula
          } else {
            let reagendamento = false
            if (this.agendamentoSelecionado.reagendado) {
              reagendamento = this.agendamentoSelecionado.datasReagendamentoPersonals.find(reag => reag.ultimo_reagendamento === true) || false
            }
            this.data_aula = reagendamento === false ? this.agendamentoSelecionado.inicio : reagendamento.data_reagendada
          }

          if (this.isEdit && this.agendamentoSelecionado.alunoDiarioPersonal && this.agendamentoSelecionado.alunoDiarioPersonal.livro) {
            this.listaDiario.livro = this.agendamentoSelecionado.alunoDiarioPersonal.livro
          }
          this.diarioPersonal.livro = this.listaDiario.livro
          this.diarioPersonal.sala_franqueada = this.agendamentoSelecionado.sala_franqueada
          this.diarioPersonal.funcionario = this.agendamentoSelecionado.funcionario
          this.diarioPersonal.aluno = this.listaDiario.aluno.pessoa.nome_contato
          this.buscarLicoesAplicadasPorContrato().then(lista => {
            this.configuraListaLicoesAnteriores(this.listaLicoesRealizadas)
            if (this.isEdit) {
              this.licaoAnterior = this.listaLicoesRealizadas.find(item => (item.id === this.agendamentoSelecionado.id))
            }
          })
          this.SET_LIVRO_SELECIONADO_ID(this.listaDiario.livro.id)
          console.log('carregar diario')
          this.buscarLicoesLivro(false)

          if (!this.$store.state.funcionario.estaCarregando) {
            this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
            this.$store.commit('funcionario/SET_LISTA', [])
            this.$store.dispatch('funcionario/listar')
          }
        })
    },

    setAluno (value) {
      this.filtros.aluno = value
    },

    setLivro (value) {
      this.diarioPersonal.livro = value
      this.listaDiario.livro = value
      this.SET_LIVRO_SELECIONADO_ID(value.id)
      this.buscarLicoesLivro(true)
    },

    setTagLicoesAplicadas (value) {
      let possuiaLicao = false
      for (let i = 0; i < this.tagsLicoesAplicadas.length; i++) {
        if (this.tagsLicoesAplicadas[i].id === value.id) {
          this.tagsLicoesAplicadas.splice(i, 1)
          possuiaLicao = true
        }
      }
      if (!possuiaLicao) {
        this.tagsLicoesAplicadas.push(value)
      }
    },

    setDataInicio(value) {
      this.filtros.data_inicial = value;
    },

    setDataFim(value) {
      this.filtros.data_final = value;
    },

    abrirRelatorio() {
      this.listar()
    },

    converterDadosParaLink() {
      const form = this.filtros

      const dados = {
        data_inicial: form.data_inicial ? beginOfDay(form.data_inicial) : null,
        data_final: form.data_final ? endOfDay(form.data_final) : null,
        modalidade_turma:
          form.modalidade_turma && form.modalidade_turma.length > 0
            ? form.modalidade_turma
            : null,

        curso: form.curso ? form.curso.id : null,
        livro: form.livro ? form.livro.id : null,
        licao: form.licao ? form.licao : null
      };

      let dadosArray = [];
      for (let key in dados) {
        if (dados[key] !== null) {
          if (dados[key] instanceof Array) {
            dados[key].forEach((element) => {
              dadosArray.push(`${key}[]=${element}`);
            });
          } else {
            dadosArray.push(`${key}=${dados[key]}`);
          }
        }
      }

      let retorno = dadosArray.length > 0 ? "&" : "";
      retorno += dadosArray.join("&");
      return retorno;
    },
  },
};
</script>

<style scoped>
.table {
  margin-bottom: 0 !important;
}
.tabela-wrapper {
  overflow-y: scroll;
  min-height: auto;
}
.tabela-wrapper::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
.tabela-wrapper::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.tabela-wrapper::-webkit-scrollbar-thumb {
  background: #888;
}
.fadeIn {
  max-width: 98vw;
  overflow: hidden;
}
#filtros-rapidos,
#filtros-avancados {
  transition: all 0.1s;
}
.tabela-aulas-ocorridas >>> tr > th,
.tabela-aulas-ocorridas >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-aulas-ocorridas >>> table thead {
  position: sticky;
  top: -1px;
}
.filtro-avancado .form-group {
  margin-bottom: 1rem;
}
.filtro-header {
  color: #4a4a4a;
}
.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151b1e;
  background-color: #fff;
}
.filtro-avancado .input-group-text {
  border: 0;
  background-color: #e5e5e5;
}
#tabela-aulas-ocorridas {
  overflow: visible;
}
@media (max-width: 992px) {
  .tabela-wrapper {
    margin-bottom: 8%;
}
}
@media print {
  .tabela-wrapper {
    overflow: hidden;
  }
}
</style>
