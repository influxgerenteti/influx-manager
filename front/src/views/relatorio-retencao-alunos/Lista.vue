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
            active
          >
            <b-card-text>
              <div class="filtro-avancado">
                <b-collapse
                  id="collapse-4"
                  v-model="filtroVisivel"
                  class="mt-2"
                >
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label for="instrutor_personal" class="col-form-label"
                        >Professor</label
                      >
                      <g-select-instrutor id="instrutor" v-model="filtros.instrutor" />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-turma_livro'"
                        for="livro"
                        class="col-form-label"
                        >Livro</label
                      >
                      <g-select-livro id="livros" v-model="filtros.livro"/>
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'form-contrato_turma'"
                        for="turma"
                        class="col-form-label"
                        >Turma</label
                      >
                      <g-select-turma id="turma" v-model="filtros.turma" />
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
                        label="label"
                        track-by="id"
                      />
                    </div>
                  </div>
                  
                  <div class="form-group row mb-0">
                    <b-col sm="6" class="col-md-4">
                      <label v-help-hint="'Data Abertura da Turma'" class="col-form-label">
                        Data abertura da turma
                      </label>
                      <g-data
                        :periodo="'mes_anterior'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                    </b-col>
                  </div>
                  <hr />
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-retencao-alunos"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
                </div>
                <div class="col-md-auto">
                  <b-btn
                    class="btn btn-cinza btn-block text-uppercase"
                    @click="abrirRelatorio()"
                  >
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
      small
        hover
        outlined
        striped
        show-empty
             fixed-header
        sort-icon-right
        id="tabela-retencao-alunos"
        class="tabela-retencao-alunos"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
      <template #empty>
        <h6>Nenhum registro a ser exibido.</h6>
      </template>
        <template #cell(turma)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(professor)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(data_termino_contrato)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(data_inicio_contrato)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(livro)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "" }}
          </span>
        </template>

        <template #cell(tipo_contrato)="data">
          {{ converterTipo(data.value) }}
        </template>
        <template #cell(aluno_id)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "" }}/
            {{
              data.item.sequencia_contrato ? data.item.sequencia_contrato : ""
            }}
          </span>
        </template>
        <template #cell(sequencia_contrato_posterior)="data">
          <span v-if="data.value" v-b-tooltip :title="data.value">
            {{ data.item.aluno_id ? data.item.aluno_id : "--" }}/
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
      </b-table>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "ListaRelatorioRetencaoAlunos",
  data() {
    return {
      sortBy: "turma",
      sortDesc: false,
      filtroVisivel: true,
      data_inicial: "",
      data_final: "",
      instrutor: null,
      turma: null,
      estagio: null,
      turma_impressao: null,
      alunos: null,
      exportFields: {
        Aluno: "aluno",
        Funcionario: "professor",
        Estágio: "livro",
        Turma: "turma",
        "Data Início": "data_inicio_contrato",
        "Data Fim": "data_termino_contrato",
        "Status Inícial": {
          field: "tipo_contrato",
          callback: (value) =>
            value == "M" ? "Matricula" : value == "R" ? "Rematrícula" : "",
        },
        "Status Inicial/Contrato": {
          field: null,
          callback: (value, key, item) => {
            return `${value.aluno_id}/${value.sequencia_contrato}`;
          },
        },
        "Status Final/Contrato": {
          field: null,
          callback: (value, key, item) => {
            return `${value.aluno_id}/${value.sequencia_contrato_posterior}`;
          },
        },

        "Motivo Cancelamento": "motivo_cancelamento",
      },
      fields: [
        { key: "aluno", sortable: true, label: "Aluno" },
        { key: "professor", sortable: true, label: "Funcionario" },
        { key: "livro", sortable: true, label: "Estágio" },
        { key: "turma", sortable: true, label: "Turma" },
        { key: "data_inicio_contrato", sortable: true, label: "Data Início" },
        { key: "data_termino_contrato", sortable: true, label: "Data Fim" },
        { key: "tipo_contrato", sortable: true, label: "Status Inícial" },
        { key: "aluno_id", sortable: true, label: "Status Inicial/Contrato" },

        {
          key: "sequencia_contrato_posterior",
          sortable: true,
          label: "Status final/Contrato",
        },
        {
          key: "motivo_cancelamento",
          sortable: true,
          label: "Motivo Cancelamento",
        },
      ],
      listaModalidadesTurma : [
        {id: null, label: 'Selecione'},
        {id: 1, label: 'Regular'},
        {id: 2, label: 'VIP'},
        {id: 3, label: 'Personal'},
        {id: 4, label: 'Hybrid'}
      ]
    };
  },

  computed: {
    ...mapState("relatorioTurma", ["filtros"]),
    ...mapState("funcionario", { listaFuncionarios: "lista" }),
    ...mapState("turma", { listaTurmas: "lista" }),
    ...mapState("livro", { listaTodosLivros: "lista" }),
    ...mapState("relatorioRetencaoAlunos", [
      "lista",
      "estaCarregando",
      "filtros",
    ]),

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

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {

    ...mapActions("funcionario", { listarFuncionarios: "listar" }),
    ...mapActions("turma", { listarTurmas: "listarTodos" }),
    ...mapActions("modalidadeTurma", { listarModalidadesTurma: "listar" }),
    ...mapActions("relatorioRetencaoAlunos", ["listar"]),
    ...mapMutations("relatorioRetencaoAlunos", ["SET_LISTA", "SET_PARAMETROS"]),

    converterTipo(situacao) {
      const valores = {
        M: "Matrícula",
        R: "Rematrícula",
      };
      return valores[situacao];
    },
    ...mapActions('relatorioRetencaoAlunos', ['listar']),
    ...mapMutations('relatorioRetencaoAlunos', ['SET_LISTA', 'SET_PARAMETROS']),

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        instrutor: form.instrutor || null,
        turma: form.turma || null,
        modalidade_turma: form.modalidade_turma ? form.modalidade_turma.id : null,
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null,
        livro: form.livro || null,
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
.tabela-retencao-alunos >>> tr > th,
.tabela-retencao-alunos >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;

}
.tabela-retencao-alunos >>> table thead {
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
#tabela-retencao-alunos {
  overflow: visible;
}
.main .container-fluid, .main .container-sm, .main .container-md, .main .container-lg, .main .container-xl {
    overflow: hidden;
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
