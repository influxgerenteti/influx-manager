<template>
  <div class="animated fadeIn">
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
              <div class="filtroVisivel">
                <b-collapse
                  id="collapse-4"
                  v-model="filtroVisivel"
                  class="mt-2"
                >
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label for="idioma" class="col-form-label">Idioma</label>
                      <g-select
                        id="idioma"
                        v-model="filtros.idioma"
                        :options="idioma"
                        class="valid-input"
                        label="text"
                        track-by="id"
                      />
                    </div>
                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-turma_curso'"
                        for="curso"
                        class="col-form-label"
                        label="descricao"
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
                      <label for="livro" class="col-form-label">Livro</label>
                      <g-select
                        id="livro"
                        :value="filtros.livro"
                        :options="listaLivrosSelect"
                        class="valid-input"
                        label="descricao"
                        track-by="id"
                        @input="setLivro"
                      />
                    </div>
                    <div class="col-md-3">
                      <label for="turma" class="col-form-label">Turma</label>
                      <g-select
                        id="turma"
                        v-model="filtros.turma"
                        :options="listaModalidadesTurmaSelect"
                        class="valid-input"
                        label="descricao"
                        track-by="id"
                        @input="setModalidadeTurma"
                      />
                    </div>

                    <div class="col-md-6">
                      <label
                        v-help-hint="
                          'filtroAvancado-ocorrencia_academica_nome_aluno'
                        "
                        for="nome_aluno"
                        class="col-form-label"
                        >Aluno</label
                      >
                      <typeahead
                        id="nome_aluno"
                        :item-hit="setAluno"
                        source-path="/api/aluno/buscar-nome"
                        key-name="pessoa.nome_contato"
                      />
                    </div>
                    <div class="col-md-3">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <div class="row">
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">De</div>
                            </div>
                            <g-datepicker
                              v-model="data_inicial"
                              :element-id="'data_inicial'"
                              :value="filtros.data_inicial"
                              :selected="setPeriodoDe"
                            />
                          </div>
                        </div>
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">à</div>
                            </div>
                            <g-datepicker
                              v-model="data_final"
                              :element-id="'data_final'"
                              :value="filtros.data_final"
                              :selected="setPeriodoAte"
                            />
                          </div>
                        </div>
                      </div>
                      <div v-if="aviso" class="floating-message bg-danger">
                        {{ aviso }}
                      </div>
                    </div>
                    <div class="col-md-3">
                      
                      <label
                        v-help-hint="
                          'filtroAvancado-semestre'
                        "
                        for="semestre"
                        class="col-form-label"
                        >Semestre</label
                      >
                        <g-select-semestre
                          id="semestre"
                          v-model="filtros.semestre"
                          class="valid-input"
                        />
                     
                    </div>
                    <div class="col-auto">
                      <label for="situacao_filtro" class="col-form-label"
                        >Situação da Matricula</label
                      >
                      <div>
                        <b-form-checkbox-group
                          id="situacao_filtro"
                          v-model="filtros.situacaoMatricula"
                          :options="situacaoFiltro"
                          buttons
                          button-variant="cinza"
                          name="situacao_filtro"
                          class="checkbtn-line"
                        />
                      </div>
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'form-contrato_tipo_contrato'"
                        for="tipo_contrato"
                        class="col-form-label"
                        >Tipo de Contrato</label
                      >
                      <div>
                        <b-form-radio-group
                          id="situacao_filtro"
                          v-model="filtros.tipo_contrato"
                          :options="tiposContrato"
                          buttons
                          button-variant="cinza"
                          name="situacao_filtro"
                          class="checkbtn-line"
                        />
                     </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-orderby'"
                        for="orderBy"
                        class="col-form-label"
                        label="ordenar"
                      >
                        Ordenar por
                      </label>
                      <g-select
                        id="orderBy"
                        v-model="filtros.orderBy"
                        :options="orderBy"
                        class="multiselect-truncate"
                        label="text"
                        track-by="id"
                      />
                    </div>
                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-orderdesc'"
                        for="orderDesc"
                        class="col-form-label"
                        label="ordem"
                      >
                        Sentido
                      </label>
                      <g-select
                        id="orderDesc"
                        v-model="filtros.orderDesc"
                        :options="orderDesc"
                        class="multiselect-truncate"
                        label="text"
                        track-by="id"
                      />
                    </div>
                  </div>
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
                    name="relatorio-matriculas"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
                </div>
                <div class="col-md-auto">
                  <b-btn
                    :disabled="!podeGerarRelatorio()"
                    class="btn btn-cinza btn-block text-uppercase"
                    @click="abrirRelatorio()"
                    >Gerar relatório</b-btn
                  >
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
    <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
      <p>Nenhum resultado encontrado.</p>
    </div>
    <div v-if="lista.length && !estaCarregando" id="container-totalizador">
      <div>
        <span v-if="matriculas">Matrículas</span>
        <span v-if="matriculas && rematriculas"> / </span>
        <span v-if="rematriculas">Rematrículas</span>
      </div>
      <div>
        <span v-if="matriculas">{{ matriculas }}</span>
        <span v-if="matriculas && rematriculas"> / </span>
        <span v-if="rematriculas">{{ rematriculas }}</span>
      </div>
    </div>
    <g-table
      id="tabela-matriculas"
      class="table-card-hover table-schedule"
      v-if="lista.length && !estaCarregando"
    >
      <thead></thead>
      <tbody>
        <div
          v-for="item in lista"
          :key="item.contrato_id"
          class="item-matricula"
        >
          <tr class="cabecalho">
            <td>
              <span>Turma: </span>
              <span>{{ item.turma }}</span>
            </td>
            <td>
              <span>Prof.: </span>
              <span>{{ item.professor }}</span>
            </td>
            <td>
              <span>Livro: </span>
              <span>{{ item.livro }}</span>
            </td>
          </tr>
          <tr class="corpo">
            <td>
              <span>Data</span>
              <span>{{ item.data_matricula }}</span>
            </td>
            <td>
              <span>Situação</span>
              <span>{{ converterSituacao(item.situacao) }}</span>
            </td>
            <td>
              <span>Aluno</span>
              <span>{{ item.aluno }}</span>
            </td>
            <td>
              <span>Funcionário</span>
              <span>{{ item.funcionario }}</span>
            </td>
          </tr>
        </div>
      </tbody>
    </g-table>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToCompare } from "../../utils/date";

export default {
  name: "ListaRelatorioMatriculas",

  data() {
    return {
      aviso: "",
      dataMaxima: "",
      selected: [],
      tiposContrato:null,
      data_inicial: "",
      data_final: "",
      curso: "",
      livro: "",
      turma: "",
      motivoDesistencia: "",
      instrutor: "",
      aluno: "",
      semestre: "",
      filtroVisivel: false,
      situacaoFiltro: [
        { value: "V", text: "Vigente" },
        { value: "E", text: "Encerrado" },
        { value: "R", text: "Rescindido" },
        { value: "C", text: "Cancelado" },
        { value: "T", text: "Trancado" },
      ],
      idioma: [
        { id: null, text: "Selecione" },
        { id: 1, text: "Inglês" },
        { id: 2, text: "Espanhol" },
      ],
      orderBy: [
        { id: null, text: "Selecione" },
        { id: "turma.data_inicio", text: "Inicio das Turmas" },
        { id: "pessoa.nome_contato", text: "Aluno" },
        { id: "func.nome_contato", text: "Funcionário" },
        { id: "livro.descricao", text: "Livro" },
      ],
      orderDesc: [
        { id: "asc", text: "Crescente" },
        { id: "desc", text: "Decrescente" },
      ],
      tiposContrato: [
        {value: 'M', text: 'Matrícula'},
        {value: 'R', text: 'Rematrícula'},
        {value: null, text:'Todos'}
      ],
      exportFields: {
        Turma: "turma",
        Aluno: "aluno",
        "Data Matrícula": "data_matricula",
        Situação: "situacao",
        Idioma: "idioma",
        Livro: "livro",
        Professor: "professor",
        Funcionário: "funcionario",
      },
    };
  },

  computed: {
    ...mapState("relatorioTurma", ["filtros"]),
    ...mapState("curso", { listaCursos: "lista" }),
    ...mapState("funcionario", { listaFuncionarios: "lista" }),
    ...mapState("livro", { listaLivros: "lista" }),
    ...mapState("modalidadeTurma", { listaModalidadesTurma: "lista" }),
    ...mapState("relatorioMatriculas", ["lista", "estaCarregando", "matriculas", "rematriculas"]),

  

    listaCursosSelect: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(this.listaCursos);
      },
    },

    listaLivrosSelect: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(this.listaLivros);
      },
    },

    listaModalidadesTurmaSelect: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.listaModalidadesTurma
        );
      },
    },
  },

  mounted() {
    this.$store.commit("livro/SET_PAGINA_ATUAL", 1);
    this.$store.commit("modalidadeTurma/SET_PAGINA_ATUAL", 1);

    this.listarTodosCursos(true);
    this.listarFuncionarios(true);
    this.listarLivros();
    this.listarModalidadesTurma();
  },

  methods: {
    dateToCompare: dateToCompare,
    ...mapMutations("curso", [
      "SET_SIGLA",
      "SET_DESCRICAO",
      "SET_IDIOMA",
      "SET_ITEM_SELECIONADO_ID",
      "LIMPAR_ITEM_SELECIONADO",
      "SET_ESTA_CARREGANDO",
      "SET_SERVICO",
    ]),
    ...mapActions("curso", { listarTodosCursos: "listar" }),
    ...mapActions("funcionario", { listarFuncionarios: "listar" }),
    ...mapActions("livro", { listarLivros: "listar" }),
    ...mapActions("modalidadeTurma", { listarModalidadesTurma: "listar" }),
    ...mapActions("relatorioMatriculas", { listarMatriculas: "listar" }),
    ...mapMutations("relatorioMatriculas", ["SET_PARAMETROS"]),



    setLivro(value) {
      this.filtros.livro = value;
    },

    setAluno(value) {
      this.filtros.aluno = value;
    },

    setModalidadeTurma(value) {
      this.filtros.turma = value;
    },

    podeGerarRelatorio() {
      return true;
    },

    setPeriodoDe(value) {
      this.aviso = "";
      this.filtros.data_inicial = value;

      if (this.filtros.data_inicial !== "") {
        const arData = this.filtros.data_inicial.split("/");
        arData[2] = String(parseInt(arData[2]) + 1);

        let dataFinal = arData.join("/");
      }
    },
    setPeriodoAte(value) {
      this.aviso = "";
      this.filtros.data_final = value;

      if (dateToCompare(value) < dateToCompare(this.filtros.data_inicial)) {
        this.aviso = ` Data ${value} não pode ser colocada, data inicial deve ser menor que a data final!`;
      }

      if (value === "") {
        this.aviso = "";
      }
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listarMatriculas();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        idioma: form.idioma ? form.idioma.id : null,
        curso: form.curso ? form.curso.id : null,
        livro: form.livro ? form.livro.id : null,
        turma: form.turma ? form.turma.id : null,
        aluno: form.aluno ? form.aluno.id : null,
        estagio: form.estagio ? form.estagio.id : null,
        situacaoMatricula: form.situacaoMatricula
          ? form.situacaoMatricula
          : null,
        tipo_contrato: form.tipo_contrato ? form.tipo_contrato : null,
        data_inicial: form.data_inicial ? form.data_inicial : null,
        data_final: form.data_final ? form.data_final : null,
        semestre: form.semestre ? form.semestre : null,
        alteraNiveis: form.alteraNiveis === true ? 1 : 0,
        transferencias: form.transferencias === true ? 1 : 0,
        recisoes: form.recisoes === true ? 1 : 0,
        cancelamentos: form.e === true ? 1 : 0,
        transfUnidadeEntrada: form.transfUnidadeEntrada === true ? 1 : 0,
        transfUnidadeSaida: form.transfUnidadeSaida === true ? 1 : 0,
        trancamentos: form.trancamentos === true ? 1 : 0,
        destrancamentos: form.destrancamentos === true ? 1 : 0,
        encerramentos: form.encerramentos === true ? 1 : 0,
        orderBy: form.orderBy ? form.orderBy.id : null,
        orderDesc: form.orderDesc ? form.orderDesc.id : null,
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

    converterSituacao(situacao) {
      const valores = {
        V: "Vigente",
        E: "Encerrado",
        R: "Rescindido",
        C: "Cancelado",
        T: "Trancado",
      };
      return valores[situacao];
    },
  },
};
</script>

<style scoped>
#filtros-rapidos,
#filtros-avancados {
  transition: all 0.1s;
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

#tabela-matriculas {
  margin-top: 20px;
  height: 80vh;
  overflow-x: auto;
}

#tabela-matriculas .item-matricula {
  page-break-inside: avoid;
}

#tabela-matriculas .item-matricula .cabecalho {
  background-color: var(--gray-200);
  border-top: 1px black solid;
}

#tabela-matriculas .item-matricula .cabecalho td > * {
  font-weight: 600;
}

#tabela-matriculas .item-matricula .cabecalho > td :first-child {
  text-align: left;
  font-weight: 900;
  margin-right: 10px;
}
#tabela-matriculas .item-matricula .cabecalho > td:first-child {
  flex: 2;
}

#tabela-matriculas .item-matricula .corpo {
  height: auto;
}

#tabela-matriculas .item-matricula .corpo td {
  display: flex;
  flex-direction: column;
}

#tabela-matriculas .item-matricula .corpo td :first-child {
  font-weight: 700;
}

#container-totalizador {
  display: flex;
  justify-content: space-between;
  padding: 2px 6px;
  box-shadow: black 0px 0px 5px -1px;
}

@media print {
  #tabela-matriculas {
    overflow: visible;
  }
}
</style>
