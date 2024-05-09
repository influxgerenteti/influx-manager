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
              <div class="filtro-avancado">
                <b-collapse
                  id="collapse-4"
                  v-model="filtroVisivel"
                  class="mt-2"
                >
                  <div class="row">
                    <b-col md="3">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <g-data
                        :periodo="'mes_anterior'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                    </b-col>

                    <b-col md="3">
                      <div>
                        <label class="col-form-label" for="atendente"
                          >Atendente</label
                        >
                        <g-select-atendente
                          id="atendente"
                          v-model="filtros.atendente"
                        />
                      </div>
                    </b-col>

                    <b-col md="3">
                      <label
                        v-help-hint="'filtro-relatorio-cheques_tipo'"
                        for="tipo"
                        class="col-form-label d-block"
                        >Tipo de Contato</label
                      >
                      <b-form-radio-group
                        id="tipo"
                        v-model="filtros.tipo_contato"
                        :options="listaTipo"
                        buttons
                        button-variant="cinza"
                        name="tipo"
                        class="checkbtn-line"
                      />
                    </b-col>
                  </div>
                </b-collapse>
              </div>
            <b-col>
              <b-col></b-col>
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
                    name="relatorio-matricula-venda"
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
            </b-col>
            </b-card-text>
          </b-tab>
        </b-tabs>
      </b-card>
    </div>
    <div v-if="estaCarregando" class="d-flex h-100">
      <load-placeholder :loading="estaCarregando" />
    </div>
    <div v-if="!estaCarregando && lista.length > 0">
      <b-table
        v-if="!estaCarregando"
        class="tabela-matricula-venda"
        :busy="estaCarregando"
        :fields="fields"
        :items="lista"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :key="index"
        small
        hover
        outlined
        striped
        sticky-header="60vh"
        fixed-header
        sort-icon-right
      >
        <template #cell(index)="data">
          {{ data.index + 1 }}
        </template>
        <template #cell(data_matricula)="data">
          {{ data.value | formatarData }}
        </template>
        <template #cell(taxa_matricula)="data">
          R$ {{ data.value | formatarMoeda(true, true) }}
        </template>
        <template #cell(percentual_desconto)="data">
          R$ {{ data.value | formatarMoeda(true, true) }}
        </template>
        <template #cell(tipo_contato)="data">
          <span>{{ converterTipoContato(data.value) }}</span>
        </template>
        <template #cell(tipo_pagamento)="data">
          <span>{{ converterTipoPagamento(data.value) }}</span>
        </template>
        <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Carregando Dados...</strong>
          </div>
        </template>
      </b-table>
      
      <div v-if="contatosAtivo">
        <b-table
          :fields="fieldsResumo"
          class="tabela-resumo-matriculas"
          :items="contatosAtivo"
          small
          hover
          outlined
          striped
          sort-icon-right
        >
        </b-table>
      </div>
    </div>
    <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
      <p>Nenhum resultado encontrado.</p>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import moment from "moment";

export default {
  name: "ListaRelatorioMatriculaVenda",

  data() {
    return {
      index: "0",
      sortBy: "data_matricula",
      sortDesc: true,
      filtroVisivel: true,

      listaTipo: [
      { text: "Ativo", value: "A" },
        { text: "Receptivo", value: "R" },
        { text: "Todos", value: null },
      ],
      fields: [
        { label: "", key: "index" },
        { label: "Responsável Venda", key: "responsavel_venda",sortable: true},
        { label: "Consultor", key: "consultor_responsavel", sortable: true },
        { label: "Data Matrícula", key: "data_matricula", sortable: true },
        { label: "Desconto %", key: "percentual_desconto", sortable: true },
        {
          label: "Responsável Venda",
          key: "responsavel_venda",
          sortable: true
        },
        { label: "Superamigos", key: "superamigos", sortable: true },
        { label: "Taxa Matricula", key: "taxa_matricula", sortable: true },
        { label: "Tipo Contato", key: "tipo_contato", sortable: true },
        { label: "Tipo Pagamento", key: "tipo_pagamento", sortable: true },
      ],
      exportFields: {
        'Consultor': "consultor_responsavel",
        "Responsável Venda": "responsavel_venda",
        "Data matricula": {
          'field': "data_matricula",
          callback: (value) => moment(value).format("DD/MM/YYYY"),
        },
        "Desconto %": "percentual_desconto",
        'Superamigos': "superamigos",
        "Taxa Matrícula": {
          field: "taxa_matricula",
          callback: (value) => (value ? `R$ ${value.replace(".", ",")}` : "-"),
        },
        "Tipo de Contato": {
          field: "tipo_contato",
          callback: (value) => (value == "A" ? "Ativo" : "Receptivo"),
        },
        "Tipo de Pagamento": {
          field: "tipo_pagamento",
          callback: (value) => (value == "H" ? "Horista" : "Mensalista"),
        },
      },
      fieldsResumo: [
        { key: "lead", label: "Tipo de Contato", sortable: true },
        { key: "count", label: "Total", sortable: true },
      ],
    };
  },

  computed: {
    ...mapState("relatorioMatriculaVenda", [
      "filtros",
      "lista",
      "estaCarregando",
      "contatosAtivo",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
    this.SET_CONTATO([]);
  },

  methods: {
    ...mapActions("relatorioMatriculaVenda", ["listar"]),
    ...mapMutations("relatorioMatriculaVenda", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "SET_CONTATO",
    ]),

    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
      return true;
    },

    converterTipoContato(tipo_contato) {
      const valores = {
        A: "Ativo",
        R: "Receptivo",
      };
      return valores[tipo_contato];
    },
    converterTipoPagamento(tipo_pagamento) {
      const valores = {
        H: "Horista",
        M: "Mensalista",
      };
      return valores[tipo_pagamento];
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        data_inicial: form.data_inicial ? form.data_inicial : null,
        data_final: form.data_final ? form.data_final : null,
        tipo_contato: form.tipo_contato || null,
        responsavel_venda_funcionario: form.atendente || null,
        responsavel: form.responsavel === true ? 1 : 0,
        geralUnidade: form.geralUnidade === true ? 1 : 0,
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
#filtros-rapidos,
#filtros-avancados {
  transition: all 0.1s;
}

.tabela-matricula-venda >>> tr > th,
.tabela-matricula-venda >>> tr > td {
  vertical-align: middle;
  text-align: left;
  display: table-cell;

  text-overflow: ellipsis;
  max-width: 2em;
}

.tabela-matricula-venda >>> table thead {
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

.tabela-resumo-matriculas >>> tr > td,
.tabela-resumo-matriculas >>> tr > th {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 2em;
}

@media print {
  .tabela-matricula-venda >>> table {
    font-size: 12px;
    margin-top: -5px;
  }
  .tabela-matricula-venda >>> table :after {
    content: "";
    height: 10px;
  }
  .tabela-matricula-venda >>> table thead {
    display: contents;
  }
}
</style>
