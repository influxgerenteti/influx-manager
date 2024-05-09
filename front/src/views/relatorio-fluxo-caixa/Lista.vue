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
                  <b-row>
                    <div class="col-md-12 d-flex">
                      <div class="col-md-6">
                        <label class="col-form-label" for="buscaDestino"
                          >Fornecedor/Cliente</label
                        >
                        <typeahead
                          id="buscaDestino"
                          :item-hit="setFavorecidoPessoa"
                          :additional-data="{
                            name: 'tipo_fornecedor',
                            data: tipoFornecedor,
                          }"
                          :required="true"
                          class="w-100"
                          source-path="/api/pessoa/buscar_nome_contato"
                          key-name="nome_contato"
                        />
                      </div>
                      <div class="col-md-6">
                        <label
                          v-help-hint="
                            'filtroAvancado-contas-pagar_data_inicial_vencimento'
                          "
                          for="data_inicial_vencimento"
                          class="col-form-label"
                          >Vencimento</label
                        >
                        <g-data
                          :periodo="'dia_atual'"
                          @dataDe="filtros.data_inicial_vencimento = $event"
                          @dataAte="filtros.data_final_vencimento = $event"
                        />
                      </div>
                    </div>
                  </b-row>
                  <b-row>
                    <div class="d-flex col-md-12 mt-3 mb-3">
                      <div class="col-md-4">
                        <label
                          v-help-hint="
                            'filtroAvancado-contas-pagar_forma_cobranca'
                          "
                          for="forma_pagamento"
                          class="col-form-label"
                          >Forma de Cobrança</label
                        >
                        <g-select
                          v-model="forma_pagamento"
                          :select="setFormaPagamento"
                          :options="listaFormaPagamento"
                          label="descricao"
                          track-by="id"
                        />
                      </div>
                      <div class="col-md-4">
                        <label
                          v-help-hint="'filtro-relatorio-cheques_conta'"
                          for="conta"
                          class="col-form-label"
                          >Conta</label
                        >
                        <g-select
                          :value="contaSelecionada"
                          :select="setConta"
                          :options="listaContas"
                          class="multiselect-truncate"
                          label="descricao"
                          track-by="id"
                        />
                      </div>
                      
                      <div class="col-md-auto" style="display: inline-grid;">
                        <label for="situacao_rapido" class="col-form-label"
                          >Situação</label
                        >
                        <b-form-checkbox-group
                          id="situacao_rapido"
                          v-model="filtros.situacao"
                          :options="situacao"
                          buttons
                          button-variant="cinza"
                          name="situacao_rapido"
                          class="checkbtn-line"
                        />
                      </div>
                   
                    </div>
                  </b-row>
                </b-collapse>
              </div>
              <div class="mb-2 mt-5 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="excelList"
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
      <div v-if="lista && lista.length > 0" >
      
        <div class="pt-2">
          <b-table
            striped hover small fixed
            id="tabela-fluxo-de-caixa"
            class="tabela-fluxo-de-caixa"
            :fields="fields"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :items="lista"
          >
            <template #cell(vencimento)="data">
              {{ data.value | formatarData }}
            </template>
            <template #cell(situacao)="data">
              {{ obterTextoSituacao(data.value) }}
            </template>
            <template #cell(tipo)="data">
              {{ data.value.toUpperCase() }}
            </template>
          </b-table>
        </div>
      </div>
      </div>
      <div v-if="lista && lista.length > 0" class="result-table mt-3">
        <p>Total: <b>{{ totais.total | formatarMoeda }}</b></p>
        <p>Saldo: <b>{{ totais.saldo | formatarMoeda }}</b></p>
      </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "FormularioFluxoCaixa",
  data() {
    return {
      filtros: {
        contato: "",
        forma_pagamento: "",
        situacao: [],
        conta: "",
        data_inicial_vencimento: "",
        data_final_vencimento: "",
      },
      exportFields: {
        'Vencimento' : 'vencimento',
        'Tipo' : {
          field: 'tipo',
          callback: (value) => value == 'e' ? 'Entrada' : value == 's' ? 'Saída' : ''
        },
        'Contato': 'contato',
        'Situação' : {
          field: 'situacao',
          callback: (value) => {
            return value == 'PEN' ? 'Pendente'
            : value == 'LIQ' ? 'Liquidado'
            : value == 'SUB' ? 'Substituido'
            : value == 'CAN' ? 'Cancelado' : ''
          }
        },
        'Conta' : 'conta',
        'Narrativa Plano de Contas' : 'narrativa_plano_conta',
        'Forma de Pagamento' : 'forma_pagamento',
        'Total' : 'total',
        'Saldo' : 'saldo'
      },
      contaSelecionada: null,
      filtroVisivel: true,
      tipoFornecedor: [],
      setFornecedor: null,
      forma_pagamento: "",
      situacao: [
        { text: "Pendente", value: "PEN" },
        { text: "Liquidado", value: "LIQ" },
        { text: "Substituido", value: "SUB" },
        { text: "Cancelado", value: "CAN" },
      ],
      sortBy: "vencimento",
      sortDesc: false,
      fields: [
        { key: "tipo", sortable: true },
        { key: "conta", sortable: true },
        { key: "contato", sortable: true },
        { key: "forma_pagamento", sortable: true },
        { key: "narrativa_plano_conta", sortable: true },
        { key: "situacao", sortable: true },
        { key: "saldo", sortable: true },
        { key: "total", sortable: true },
        { key: "vencimento", sortable: true },
      ],
    };
  },

  computed: {
    ...mapState("relatorioFluxoCaixa", [
      "itemSelecionado",
      "itemSelecionadoID",
      "estaCarregando",
    ]),
    ...mapState("relatorioFluxoCaixa", [
      "lista",
      "estaCarregando",
      "totais",
      "excelList",
    ]),

    ...mapState("planoConta", { planoConta: "selectDespesas" }),

    listaFormaPagamento: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.$store.state.formaPagamento.lista
        );
      },

      listaPlanoConta: {
        get() {
          return this.childrenStructure(this.planoConta);
        },
      },
    },

    estaEditando: {
      get() {
        return !!this.itemSelecionado.id;
      },
    },

    listaContas: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.$store.state.conta.lista
        );
      },
    },
  },

  mounted() {
    this.getListaFormaPagamento();
    this.getListaConta();
    this.limparLista();
  },

  methods: {
    ...mapActions("formaPagamento", { getListaFormaPagamento: "getLista" }),
    ...mapActions("conta", { getListaConta: "getLista" }),

    ...mapActions("relatorioFluxoCaixa", { getRelatorio: "listar" }),
    ...mapMutations("relatorioFluxoCaixa", { setFiltros: "SET_FILTROS" }),
    ...mapMutations("relatorioFluxoCaixa", "SET_LISTA"),

    abrirRelatorio() {
      this.setFiltros(this.filtros);
      this.getRelatorio();
    },

    limparLista(){
    
    },

    setFavorecidoPessoa(value) {
      if(value) {
        return this.filtros.contato = value.id;
      }
      this.filtros.contato = null
    },

    obterTextoSituacao(value) {
      let texto = value;
      this.situacao.forEach((sit) => {
        if (sit.value == value) {
          texto = sit.text;
        }
      });
      return texto;
    },

    setConta(value) {
      this.contaSelecionada = value;
      this.filtros.conta = value.id;
    },

    setFormaPagamento(value) {
      this.filtros.forma_pagamento = value.id;
    },
  },

  setFornecedor(value) {
    this.$store.commit("relatorioFluxoCaixa/SET_FORNECEDOR_PESSOA", value);
    if (!this.estaEditando) {
      if (value !== null && value.plano_conta !== undefined) {
        this.setPlanoContaCategoria(value.plano_conta, 0);
      }
    }
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

#tabela-fluxo-de-caixa >>> thead tr {
  background-color: white;
}
#tabela-fluxo-de-caixa >>> tr > th,
#tabela-fluxo-de-caixa >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell !important;

}

#tabela-fluxo-de-caixa .item-matricula .cabecalho > td :first-child {
  text-align: left;
  font-weight: 900;
  margin-right: 10px;
}
#tabela-fluxo-de-caixa {
  margin-top: 20px;
  max-height: 65vh;
  overflow-x: auto;
}
.table-area {
  max-height: 65vh;
  overflow: scroll;
}
.result-table{
  background-color: #f7f7f7;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  font-size: 18px;
  line-height: 0.6;
  border: 1px solid #6c757d75;
  color: #434343;
  box-shadow: rgb(0 0 0 / 5%) 0px 0px 0px 1px;

}

  @media print {
    #tabela-fluxo-caixa {
      overflow: visible;
    }
    .table-area {
      max-height: 100%;
      overflow: visible;
    }
  }
</style>