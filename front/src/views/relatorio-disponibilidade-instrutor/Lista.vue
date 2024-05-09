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
                    <b-col md="6">
                      <label
                        v-help-hint="
                          'filtro_relatorio_disponibilidade_instrutor'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <g-data
                        :periodo="'mes_corrente'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                    </b-col>

                    <div class="col-md-6">
                      <label
                        v-help-hint="'filtros-instrutor'"
                        for="instrutor"
                        class="col-form-label"
                        label="descricao"
                        >Instrutor</label
                      >
                      <g-select-instrutor
                        id="instrutor"
                        v-model="filtros.instrutor"
                      />
                    </div>
                  </div>
                  <div class="form-group row"></div>
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
                    name="relatorio-disponibilidade-instrutor"
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
    <div class="tabela-wrapper">
      <div v-if="estaCarregando" class="d-flex h-100">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <b-table
      striped
          show-empty
          hover
          outlined
          small
          fixed-header
          sort-icon-right
        id="tabela-disponibilidade-instrutor"
        class="tabela-disponibilidade-instrutor"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(nome)="data">
          <span v-b-tooltip.viewport.top.hover :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(data)="data">
          <span v-b-tooltip.viewport.top.hover :title="data.value">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(hora_final)="data">
          <span v-b-tooltip.viewport.top.hover :title="data.value">
            {{ data.value | formatarDataHHMM }}
          </span>
        </template>
        <template #cell(hora_inicial)="data">
          <span v-b-tooltip.viewport.top.hover :title='data.value'>
            {{ data.value | formatarDataHHMM }}
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

export default {
  name: "ListaRelatorioDisponibilidadeInstrutor",
  data() {
    return {
      aviso: "",
      filtroVisivel: true,
      sortBy: "nome",
      sortDesc: false,
      fields: [
        { key: "nome", sortable: true },
        { key: "data", sortable: true },
        { key: "dia_semana", sortable: true, label: "Dia da Semana" },
        { key: "hora_inicial", sortable: true, label: "Hora Inicial" },
        { key: "hora_final", sortable: true, label: "Hora Final" },
      ],
      exportFields: {
        Nome: "nome",
        Data: "data",
        "Dia da Semana": "dia_semana",
        "Hora Inicial": "hora_inicial",
        "Hora Final": "hora_final",
      },
    };
  },

  computed: {
    ...mapState("relatorioDisponibilidadeInstrutor", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    ...mapActions("relatorioMapaSalaTurma", ["listar"]),
    ...mapActions("relatorioDisponibilidadeInstrutor", ["listar"]),
    ...mapMutations("relatorioDisponibilidadeInstrutor", [
      "SET_LISTA",
      "SET_PARAMETROS",
    ]),

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

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null,
        instrutor: form.instrutor || null,
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
.tabela-disponibilidade-instrutor >>> tr > th,
.tabela-disponibilidade-instrutor >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-disponibilidade-instrutor >>> table thead {
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
#tabela-disponibilidade-instrutor {
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
  
</style>
