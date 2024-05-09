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
                      <label for="idioma" class="col-form-label">Idioma</label>
                      <g-select-idioma id="idioma" v-model="filtros.idioma">
                      </g-select-idioma>
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-turma_curso'"
                        for="curso"
                        class="col-form-label"
                        label="descricao"
                        >Curso</label
                      >
                      <g-select-curso id="curso" v-model="filtros.curso" />
                    </div>

                    <div class="col-md-3">
                      <label for="livro" class="col-form-label">Livro</label>
                      <g-select-livro id="livro" :value="filtros.livro" />
                    </div>
                    <div class="col-md-3">
                      <label for="turma" class="col-form-label">Turma</label>
                      <g-select-turma id="turma" v-model="filtros.turma" />
                    </div>

                    <b-col class="col-md-3">
                      <br />
                      <label for="situacao_filtro" class="col-form-label"
                        >Situação do Contrato</label
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
                    </b-col>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 mt-2 d-flex justify-content-end">
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
      class="tabela-valor-turma w-full"
      :busy="estaCarregando"
      :items="lista"
      :fields="cabecalho"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      v-if="lista && !estaCarregando"
      small
      hover
      outlined
      striped
      show-empty
      fixed-header
      sort-icon-right
    >
      <template #cell(tr_valor_item)="data">
        <span>R$ {{ data.value | formatarMoeda(true, true) }}</span>
      </template>
      <template #cell(tr_saldo_devedor)="data">
        <span>R$ {{ data.value | formatarMoeda(true, true) }}</span>
      </template>
      <template #cell(tr_valor_pago)="data">
        <span>R$ {{ data.value | formatarMoeda(true, true) }}</span>
      </template>
      <template #empty>
      <p>Nenhum resultado encontrado!</p>
    </template>
    </b-table>
  </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToCompare } from "../../utils/date";

export default {
  name: "ListaRelatorioValoresTurma",

  data() {
    return {
      filtroVisivel: false,

      situacaoFiltro: [
        { value: "V", text: "Vigente" },
        { value: "E", text: "Encerrado" },
        { value: "R", text: "Rescindido" },
        { value: "C", text: "Cancelado" },
        { value: "T", text: "Trancado" },
      ],
      orderDesc: [
        { id: "asc", text: "Crescente" },
        { id: "desc", text: "Decrescente" },
      ],

      situacao: [],
      sortBy: "data",
      sortDesc: false,

      cabecalho: [
        { key: "turma", label: "Turma", sortable: true },
        { key: "nome_aluno", label: "Aluno", sortable: true },
        { key: "numero_parcela_documento", label: "Parcela", sortable: true },
        { key: "tr_valor_item", label: "Valor Item", sortable: true },
        { key: "tr_saldo_devedor", label: "Saldo Devedor", sortable: true },
        { key: "tr_valor_pago", label: "Valor Pago", sortable: true },
      ],
      exportFields: {
        Turma: "turma",
        Aluno: "nome_aluno",
        Parcela: "numero_parcela_documento",
        "Valor Devedor": "tr_saldo_devedor",
        "Valor Item": "tr_valor_item",
        "Valor Pago": "tr_valor_pago",
        Idioma: "idioma",
        Livro: "livro",
      },
    };
  },

  computed: {
    ...mapState("relatorioValoresTurma", [
      "lista",
      "filtros",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
    this.limparLista([]);
    this.listarValores();
  },

  methods: {
    ...mapActions("relatorioValoresTurma", { listarValores: "listar" }),
    ...mapMutations("relatorioValoresTurma", ["SET_LISTA", "SET_PARAMETROS"]),

    podeGerarRelatorio() {
      return true;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listarValores();
      this.listar();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        idioma: form.idioma ? form.idioma : null,
        curso: form.curso ? form.curso : null,
        livro: form.livro ? form.livro : null,
        turma: form.turma ? form.turma : null,
        situacao_contrato: form.situacaoMatricula
          ? form.situacaoMatricula
          : null,
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
.tabela-valor-turma  >>> tr > th,
.tabela-valor-turma  >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-valor-turma  >>> table thead {
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
#tabela-valor-turma  {
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

@media print {
  .tabela-valor-turma >>> table {
    font-size: 12px;
    margin-top: -5px;
  }
  .tabela-valor-turma >>> table :after {
    content: "";
    height: 10px;
  }
  .tabela-valor-turma >>> table thead {
    display: contents;
  }
}

</style>
