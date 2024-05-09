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
                  <!-- INSERIR AQUI OS FILTROS -->

                  <div class="row">
                    <div class="col-md-6">
                      <label
                        v-help-hint="'filtroRapido-aluno_nome_aluno'"
                        for="nome_aluno"
                        class="col-form-label"
                        >Aluno</label
                      >
                      <typeahead
                        id="nome_aluno"
                        :item-hit="setNomeAluno"
                        source-path="/api/aluno/buscar-nome"
                        key-name="pessoa.nome_contato"
                      />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <label for="turma" class="col-form-label">Turma</label>
                      <g-select-turma
                        id="turmaDescricao"
                        :multiTag="false"
                        v-model="filtros.turma"
                        class="valid-input"
                      ></g-select-turma>
                    </div>
                    <div class="col-md-3">
                      <label for="livro" class="col-form-label">Livro</label>
                      <g-select
                        id="livro"
                        v-model="filtros.livro"
                        :options="listaLivrosSelect"
                        class="valid-input"
                        label="descricao"
                        track-by="id"
                      />
                    </div>

                    <b-col md="6">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Data da Matrícula</label
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
                    </b-col>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 mt-5 d-flex justify-content-end">
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
        small hover outlined bordered striped show-empty fixed-header sort-icon-right
        id="tabela-pedido-material-didatico"
        class="tabela-pedido-material-didatico"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(aluno)="data">
          <span v-b-tooltip :title='data.value'>
            {{ data.value ? data.value : "--" }}
          </span>
          <br>
          <span v-b-tooltip :title='data.item.sacado'>
            {{ data.item.sacado ? data.item.sacado : "--" }}
          </span>

        </template>

        <template #cell(item)="data">
          <span v-b-tooltip :title='data.value'>
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(contrato)="data">
          {{ data.value ? data.value : "--" }}
        </template>
        <template #cell(data_inicio)="data">
          <span v-if="data.value">
            {{ data.value | formatarData }}
          </span>
          <span v-if="!data.value">
            {{ "--" }}
          </span>
        </template>
        <template #cell(data_matricula)="data">
          <span v-if="data.value">
            {{ data.value | formatarData }}
          </span>
          <span v-if="!data.value">
            {{ "--" }}
          </span>
        </template>
        <template #cell(estagio)="data">
          <span v-b-tooltip :title='data.value'>
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(turma)="data">
          <span v-b-tooltip :title='data.value'>
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #empty>
          <h4>Nenhum registro a ser exibido.</h4>
        </template>
      </b-table>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { beginOfDay, endOfDay, dateToCompare } from "../../utils/date";

export default {
  name: "ListaPedidoMaterialDidatico",
  data() {
    return {
      aviso: "",
      livro: "",
      filtroVisivel: true,
      data_inicial: "",
      data_final: "",
      exportFields: {
        Item: "item",
        Aluno: "aluno",
        Sacado: "sacado",
        Contrato: "contrato",
        "Data Inicio": "data_inicio",
        "Data Matricula": "data_matricula",
        Estagio: "estagio",
        Turma: "turma"
      },
      sortBy: "aluno",
      sortDesc: false,
      fields: [
        { key: "item", sortable: true },
        { key: "aluno", sortable: true,  label: "Aluno/Sacado" },
        { key: "contrato", sortable: true },
        { key: "data_inicio", sortable: true },
        { key: "data_matricula", sortable: true },
        { key: "estagio", sortable: true },
        { key: "turma", sortable: true },
      ],
    };
  },

  computed: {
    ...mapState("livro", { listaLivros: "lista" }),
    ...mapState("relatorioPedidoMaterialDidatico", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),

    listaLivrosSelect: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(this.listaLivros);
      },
    },
  },

  mounted() {
    this.$store.commit("livro/SET_PAGINA_ATUAL", 1);

    this.SET_LISTA([]);
    this.listarLivros();
  },

  methods: {
    ...mapActions("relatorioPedidoMaterialDidatico", ["listar"]),
    ...mapMutations("relatorioPedidoMaterialDidatico", [
      "SET_LISTA",
      "SET_PARAMETROS",
    ]),
    ...mapActions("livro", { listarLivros: "listar" }),

    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
      return true;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },

    setNomeAluno(value) {
      this.alunoTemporario = value;
      if (this.filtroSelecionado === 1) {
        this.filtrar();
      }
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

    filtrar() {
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido();
      }
    },

    executaFiltroRapido() {
      let dataDe = this.data_matricula_de
        ? beginOfDay(this.data_matricula_de)
        : null;
      let dataAte = this.data_matricula_ate
        ? endOfDay(this.data_matricula_ate)
        : null;

      let filtros = Object.assign({
        data_matricula_de: dataDe,
        data_matricula_ate: dataAte,
      });
      this.$store.commit("atividadeExtra/SET_FILTROS", filtros);
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        aluno: this.alunoTemporario ? this.alunoTemporario.id : null,
        turma: form.turma || null,
        livro: form.livro ? form.livro.id : null,
        data_inicial: form.data_inicial ? form.data_inicial : null,
        data_final: form.data_final ? form.data_final : null,
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
.tabela-pedido-material-didatico >>> tr > th,
.tabela-pedido-material-didatico >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-pedido-material-didatico >>> table thead {
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
#tabela-pedido-material-didatico {
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
