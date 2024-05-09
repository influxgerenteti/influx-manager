<template >
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
                  <div class="form-group d-md-flex">
                    <div class="col-md-6">
                      <label
                        v-help-hint="'filtro_avancado'"
                        for="data_inicial"
                        class="col-form-label"
                      >
                        Período</label
                      >
                      <g-data
                        :periodo="'dia_atual'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_conta'
                        "
                        for="conta"
                        class="col-form-label"
                      >
                        Conta *
                      </label>
                      <g-select
                        :value="filtros.contas"
                        :select="setConta"
                        :options="listaContas"
                        :class="
                          !isValid && !filtros.contas
                            ? 'invalid-input'
                            : 'valid-input'
                        "
                        :required="true"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                      <div
                        v-if="!isValid && filtros.contas === null"
                        class="multiselect-invalid"
                      >
                        Selecione uma opção!
                      </div>
                    </div>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end d-md-flex">
                <div class="col-md-auto" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-balancete-financeiro"
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
        show-empty
        striped
        hover
        outlined
        small
        fixed-header
        sort-icon-right
        class="tabela-balancete-financeiro"
        id="tabela-balancete-financeiro"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #empty>
          <p>Nenhum resultado encontrado!</p>
        </template>
        <template #cell(valor_desconto)="data">
          <b-tr-ln md="6">
            <div>
              <span class="d-flex align-items-start b"
                ><b>Desconto: </b>{{ data.value | formatarMoeda }}</span
              >
              <span class="d-flex align-items-start b"
                ><b>Diferença Baixa</b
                >{{ data.item.valor_diferenca_baixa | formatarMoeda }}</span
              >
            </div>
            <div>
              <span class="d-flex align-items-start b"
                ><b>Juros: </b>{{ data.item.valor_juros | formatarMoeda }}</span
              >
              <span class="d-flex align-items-start b"
                ><b>Lançamento: </b
                >{{ data.item.valor_lancamento | formatarMoeda }}</span
              >
            </div>

            <div>
              <span class="d-flex align-items-start b"
                ><b>Multa: </b>{{ data.item.multa | formatarMoeda }}</span
              >
              <span class="d-flex align-items-start b"
                ><b>Saldo Final: </b
                >{{ data.item.valor_saldo_final_conta | formatarMoeda }}</span
              >
            </div>
            <div>
              <span class="d-flex align-items-start b"
                ><b>Valor Título: </b
                >{{ data.item.muvalor_titulo | formatarMoeda }}</span
              >
            </div>
          </b-tr-ln>
        </template>

        <template #cell(data_contabil)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(data_deposito)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(data_movimento)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(conciliado)="data">
          <span v-b-tooltip.top :title="data.value == 'S' ? 'Sim' : 'Não'">
            {{ data.value == "S" ? "Sim" : "Não" }}
          </span>
        </template>
        <template #cell(operacao)="data">
          <span
            v-b-tooltip.top
            :title="data.value == 'D' ? 'Débito' : 'Crédito'"
          >
            {{ data.value == "D" ? "Débito" : "Crédito" }}
          </span>
        </template>
        <template #cell(estornado)="data">
          <span v-b-tooltip.top :title="data.value === false ? 'Não' : 'Sim'">
            {{ data.value === false ? "Não" : "Sim" }}
          </span>
        </template>
        <template #cell(observacao)="data">
          <div v-if="data.item.observacao">
            <a
              v-b-tooltip.viewport.left.hover
              class="icone-link"
              title="Exibir Observação"
              v-on:click="carregarObservacao(data)"
            >
              <font-awesome-icon v-b-modal.modal-1 icon="file" />
            </a>
          </div>
        </template>
      </b-table>
      <b-modal id="modal-1" title="Observação" ok-only>
        <p class="my-4">{{ this.listaObservacao }}</p>
      </b-modal>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { required } from "vuelidate/lib/validators";
import GData from "@/components/specificFilters/GData.vue";

export default {
  components: { GData },
  name: "ListaRelatorioBalanceteFinanceiro",
  data() {
    return {
      listaObservacao: "",
      sortBy: "data_contabil",
      sortDesc: false,
      filtroVisivel: true,
      isValid: true,
      fields: [
        { key: "conciliado", label: "Conciliado", sortable: true },
        { key: "data_contabil", label: "Data", sortable: true },
        { key: "data_deposito", label: "Depósito", sortable: true },
        { key: "data_movimento", label: "Data Movimento", sortable: true },
        { key: "estornado", label: "Estornado", sortable: true },
        { key: "numero_documento", label: "Número Documento", sortable: true },
        { key: "operacao", label: "Operação", sortable: true },
        { key: "valor_desconto", label: "Valores - R$", sortable: false },
        { key: "observacao", label: "Observação", class: "no-print" },
      ],
      exportFields: {
        Conciliado: {
          field: "conciliado",
          callback: (value) => (value == "S" ? "Sim" : "Não"),
        },
        Data: "data_contabil",
        "Data Deposito": "data_deposito",
        "Data Movimento": "data_movimento",
        Estornado: {
          field: "estornado",
          callback: (value) => (value == "S" ? "Sim" : "Não"),
        },
        "Número Documento": "numero_documento",
        Operação: {
          field: "operacao",
          callback: (value) => (value == "D" ? "Débito" : "Crédito"),
        },
        Valor: "valor_desconto",
        "Valor Diferença Baixa": "valor_diferenca_baixa",
        "Valor Juros": "valor_juros",
        "Valor Lançamento": "valor_lancamento",
        "Valor Multa": "valor_multa",
        "Valor Saldo Final da Conta": "valor_saldo_final_conta",
        "Valor Título": "valor_titulo",
        Observação: "observacao",
      },
    };
  },

  computed: {
    ...mapState("relatorioBalanceteFinanceiro", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
    ...mapState("conta", { listaContaRequisicao: "lista" }),

    listaContas: {
      get() {
        return this.listaContaRequisicao;
      },
    },
  },

  validations: {
    filtros: {
      contas: { required },
    },
  },

  mounted() {
    this.listarCamposSelects();
  },

  methods: {
    ...mapActions("relatorioBalanceteFinanceiro", { listar: "listar" }),
    ...mapMutations("relatorioBalanceteFinanceiro", [
      "SET_LISTA",
      "SET_PARAMETROS",
    ]),
    ...mapActions("conta", { listarContas: "getLista" }),

    carregarObservacao(data) {
      this.listaObservacao = data.item.observacao;
    },

    listarCamposSelects() {
      this.listarContas();
    },

    setConta(value) {
      this.filtros.contas = value.id === null ? null : value;
    },

    podeGerarRelatorio() {
      if (this.$v.$invalid) {
        this.isValid = false;
        return;
      }
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
        data_inicial: form.data_inicial ? form.data_inicial : null,
        data_final: form.data_final ? form.data_final : null,
        conta: form.contas ? form.contas.id : null,
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
.tabela-balancete-financeiro >>> tr > th,
.tabela-balancete-financeiro >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-balancete-financeiro >>> table thead {
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
#tabela-balancete-financeiro {
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
