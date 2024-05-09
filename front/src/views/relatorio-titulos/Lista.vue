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
                  <div class="form-group row">
                    <div class="col-md-4">
                      <label
                        v-help-hint="'filtroAvancado-contas-receber_vencimento'"
                        class="col-form-label"
                        >Data Vencimento</label
                      >
                      <div class="row">
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">De</div>
                            </div>
                            <g-datepicker
                              :element-id="'data_inicial_vencimento'"
                              v-model="filtros.data_inicial_vencimento"
                            />
                          </div>
                        </div>
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">à</div>
                            </div>
                            <g-datepicker
                              :element-id="'data_final_vencimento'"
                              v-model="filtros.data_final_vencimento"
                            />
                          </div>
                        </div>
                      </div>

                      <div
                        v-if="!datasValidasVencimento"
                        class="floating-message bg-danger"
                      >
                        {{ mensagemErroData }}
                      </div>
                    </div>

                    <div class="col-md-auto">
                      <label
                        for="situacao_rapido"
                        class="col-form-label d-block"
                        >Situação da parcela</label
                      >
                      <b-form-checkbox-group
                        id="situacao_rapido"
                        v-model="filtros.situacao"
                        :options="situacoes"
                        buttons
                        button-variant="cinza"
                        name="situacao_rapido"
                        class="checkbtn-line fill-width"
                      />
                    </div>

             
                    <b-col md="12">
                      <label for="forma-cobranca" class="col-form-label"
                        >Forma cobrança</label
                      >
                      <g-select
                        id="forma-cobranca"
                        :multi-tag="true"
                        :value="filtros.formas_cobranca"
                        :select="setTagFormaCobranca"
                        :options="listaFormaCobranca"
                        class="multiselect-truncate g-multiselect-tag"
                        label="descricao"
                        track-by="id"
                      />
                    </b-col>

                    <b-col md="12">
                      <label for="forma-pagamento" class="col-form-label"
                        >Forma pagamento</label
                      >
                      <g-select
                        id="forma-pagamento"
                        :multi-tag="true"
                        :value="filtros.formas_pagamento"
                        :select="setTagFormaPagamento"
                        :options="listaFormaCobranca"
                        class="multiselect-truncate g-multiselect-tag"
                        label="descricao"
                        track-by="id"
                      />
                    </b-col>

                    <div class="col-md-auto">
                      <b-col md="auto">
                        <label class="col-form-label"
                          >Ordenar por data de:</label
                        >
                        <b-form-radio-group
                          v-model="filtros.order"
                          :options="opcoesOrdenar"
                          name="order"
                        />
                      </b-col>
                    </div>

                    <div class="col-md-auto">
                      <b-col md="auto">
                        <label class="col-form-label"
                          >Agrupar por parcelas?</label
                        >
                        <b-form-radio-group
                          v-model="filtros.agrupar_por_parcelas"
                          :options="opcoesAgruparParcelas"
                          name="agrupar_parcelas"
                        />
                      </b-col>
                    </div>

                    <div class="col-md-auto">
                      <b-col>
                        <label class="col-form-label">Exibir Títulos</label>
                        <div class="row checkboxes">
                          <b-form-checkbox v-model="filtros.forma_cheque"
                            >Cheque</b-form-checkbox
                          >
                          <b-form-checkbox v-model="filtros.forma_cartao"
                            >Cartão</b-form-checkbox
                          >
                        </div>
                      </b-col>
                    </div>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista && lista.data">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista && lista.data">
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

    <div v-if="estaCarregando" class="d-flex">
      <load-placeholder :loading="estaCarregando" />
    </div>
    <div class="table-area">
    <b-table

       
        outlined
        bordered
          
        fixed-header
        sort-icon-right

      small
      striped 
      show-empty
      hover
      id="tabela-titulos"
      class="table-card-hover table-schedule"
      v-if="lista && lista.data && !estaCarregando && !lista.agrupado"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="lista.data"
    
      
    >
      <template #empty> Nenhum resultado encontrado! </template>
      <template #cell(data_vencimento)="data">
        {{ data.value | formatarData }}
        {{ data.item.data_vencimento ? " / " : "" }}
        {{ data.item.data_pagamento | formatarData }}
      </template>
      <template #cell(forma_cobranca)="data">
        {{
          data.value +
          (data.item.forma_pagamento ? " / " + data.item.forma_pagamento : "")
        }}
      </template>
      <template #cell(valor_pago)="data">
        <span>{{ data.value | formatarMoeda(true, true) }}</span>
      </template>
      <template #cell(valor_parcela_sem_desconto)="data">
        <span>{{ data.value | formatarMoeda(true, true) }}</span>
      </template>
      <template #cell(desconto_antecipacao)="data">
        <span>{{ data.value | formatarMoeda(true, true) }}</span>
      </template>
      <template #cell(situacao_titulo)="data">
        {{ converterSituacao(data.value) }}
      </template>
    </b-table>
  </div>

    <!-- Tabela agrupada por parcelas -->
    <div>
      <b-table
        id="tabela-agrupada"
        class="grouped_table_to_view"
        v-if="lista && lista.data && !estaCarregando && lista.agrupado"
        :items="Object.values(lista.data)"
        :fields="fieldsAgrupado"
        striped

        show-empty
       
       
      >
        <template #empty> Nenhum resultado encontrado! </template>
        <template #cell(show_details)="row">
          <b-button size="sm" @click="row.toggleDetails" class="mr-2">
            <font-awesome-icon v-if="row.detailsShowing" icon="minus" />
            <font-awesome-icon v-if="!row.detailsShowing" icon="plus" />
          </b-button>
        </template>

        <template #row-details="row">
          <b-card>
            <b-table :items="row.item.data" :fields="fieldsAgrupadoDetalhes">
              <template #cell(data_pagamento)="row">
                <span>{{ row.value | formatarData }}</span>
              </template>
              <template #cell(data_vencimento)="row">
                <span>{{ row.value | formatarData }}</span>
              </template>
              <template #cell(situacao_titulo)="row">
                <span>{{ converterSituacao(row.value) }}</span>
              </template>
              <template #cell(valor_pago)="row">
                <span>{{ row.value | formatarMoeda(true, true) }}</span>
              </template>
              <template #cell(valor_parcela_sem_desconto)="row">
                <span>{{ row.value | formatarMoeda(true, true) }}</span>
              </template>
            </b-table>
          
          </b-card>
        </template>
      </b-table>

      <div class="w-100 d-flex">
        <b-table
          id="tabela-titulos-agrupada"
          v-if="lista && lista.data && !estaCarregando && lista.agrupado"
          class="grouped_table_to_print"
          :items="excelList"
          striped
          hover
         
         
        >
          <template #empty> Nenhum resultado encontrado! </template>
          <template #cell(data_vencimento)="data">
            {{ data.value | formatarData }}
            {{ data.item.forma_pagamento ? " / " : "" }}
            {{ data.item.forma_pagamento | formatarData }}
          </template>
          <template #cell(forma_cobranca)="data">
            {{
              data.value +
              (data.item.forma_pagamento
                ? " / " + data.item.forma_pagamento
                : "")
            }}
          </template>
          <template #cell(situacao_titulo)="data">
            {{ converterSituacao(data.value) }}
          </template>
          <template #cell(valor_pago)="data">
            {{ data.value | formatarMoeda(true, true) }}
          </template>
          <template #cell(valor_parcela_sem_desconto)="data">
            {{ data.value | formatarMoeda(true, true) }}
          </template>
        </b-table>
      </div>

      
    </div>


    <div id="total-container" class="d-flex justify-content-between align-items-center fixed"  v-if="lista && lista.data && !estaCarregando">
      <div class="d-flex">
        </div>
      <div class="info-label" >
        <div class="text-right">
          <p> <b>Total de Pagamentos:</b></p>
          <small>
            
            <div  v-for="item in resumo" :key="item.situacao" >
                      <p>
                       <b>{{ item.situacao }}:</b> R${{
                          item.valor | formatarMoeda(true, true)
                        }}
                      </p>
                    </div>  
          </small>
        </div>
      </div>
    </div>

 
  </div>
</template>




<script>
import formatarData from "@/filters/formatar-data";
import { mapState, mapMutations, mapActions } from "vuex";
import {
  dateToCompare,
  converteFormatoBrasilParaAmericano,
} from "../../utils/date";

export default {
  name: "ListaRelatorioTitulosAVencerVencidos",

  data() {
    return {
      filtroVisivel: false,
      sortBy: "vencimento",
      sortDesc: false,
      fields: [
        { label: "Contato", key: "nome_contato", sortable: true },
        { label: "Parcela", key: "parcela", sortable: true },
        { label: "Situação", key: "situacao_titulo", sortable: true },
        {
          label: "Data Vencimento / Pagamento",
          key: "data_vencimento",
          sortable: true,
        },
        {
          label: "Forma Cobrança / Pagamento",
          key: "forma_cobranca",
          sortable: false,
        },
        { label: "Valor Pago", key: "valor_pago", sortable: true },
        {
          label: "Valor Parcela Sem Desconto",
          key: "valor_parcela_sem_desconto",
          sortable: true,
        },
        {
          label: "Desconto Antecipação",
          key: "desconto_antecipacao",
          sortable: true,
        },
      ],

      situacoes: [
        { text: "Pendente", value: "PEN" },
        { text: "Quitado", value: "LIQ" },
        { text: "Cancelado", value: "CAN" },
        { text: "Vencido", value: "VEN" },
        { text: "Renegociado", value: "SUB" },
      ],
      opcoesAgruparParcelas: [
        { text: "Sim", value: "S" },
        { text: "Não", value: "N" },
      ],
      opcoesOrdenar: [
        { text: "Vencimento", value: "V" },
        { text: "Pagamento", value: "P" },
      ],

      fieldsAgrupado: [
        { label: "Detalhes", key: "show_details", sortable: false },
        { label: "Nome do Contato", key: "nome_contato", sortable: true },
        { label: "Valor Pago", key: "valor_pago", sortable: true },
        { label: "Valor Total", key: "valor_total", sortable: true },
      ],
      fieldsAgrupadoDetalhes: [
        { label: "Data Pagamento", key: "data_pagamento", sortable: true },
        { label: "Data Vencimento", key: "data_vencimento", sortable: true },
        { label: "Forma Cobrança", key: "forma_cobranca", sortable: false },
        { label: "Forma Pagamento", key: "forma_pagamento", sortable: false },
        { label: "Parcela", key: "parcela", sortable: false },
        { label: "Titulo", key: "situacao_titulo", sortable: true },
        { label: "Valor Pago", key: "valor_pago", sortable: true },
        {
          label: "Valor Parcela",
          key: "valor_parcela_sem_desconto",
          sortable: true,
        },
      ],
      exportFields: {
        "Nome do Contato": "nome_contato",
        Titulo: "situacao_titulo",
        Parcela: "parcela",
        "Data Pagamento": "data_pagamento",
        "Data Vencimento": "data_vencimento",
        "Forma Cobrança": "forma_cobranca",
        "Forma Pagamento": "forma_pagamento",
        "Valor Pago": "valor_pago",
        "Valor Parcela": "valor_parcela_sem_desconto",
      },
      mensagemErroData: "Data inicial deve ser menor que a data final!",
      mensagemErroSaldo: "Saldo mínimo deve ser menor que o máximo!",
    };
  },

  computed: {
    ...mapState("relatorioTitulos", [
      "filtros",
      "lista",
      "estaCarregando",
      "excelList",
      "resumo",
    ]),
    ...mapState("formaPagamento", { listaFormaCobranca: "lista" }),
    ...mapState("planoConta", { listaPlanosConta: "selectReceitas" }),
    datasValidasVencimento: {
      get() {
        return (
          !this.filtros.data_final_vencimento ||
          !this.filtros.data_inicial_vencimento ||
          dateToCompare(this.filtros.data_inicial_vencimento) <=
            dateToCompare(this.filtros.data_final_vencimento)
        );
      },
    },
    listaPlanosContaSelect: {
      get() {
        const lista = [{ descricao: "Selecione", id: 0, filhos: [] }].concat(
          this.listaPlanosConta
        );
        return lista;
      },
    },
  },

  mounted() {
    this.listarFormaCobranca();
    this.listarPlanosContas();
    this.limparLista([]);
    
  },

  methods: {
    ...mapMutations("formaPagamento", {
      SET_LISTA_FORMA_PAGAMENTO: "SET_LISTA",
      SET_PAGINA_ATUAL_FORMA_PAGAMENTO: "SET_PAGINA_ATUAL",
    }),
    ...mapActions("relatorioTitulos", ["listar"]),
    ...mapMutations("relatorioTitulos", ["SET_LISTA", "SET_RESUMO"]),

    dateToCompare: dateToCompare,

    limparLista() {
      this.SET_LISTA();
      this.SET_RESUMO();
    },

    listarFormaCobranca() {
      this.SET_LISTA_FORMA_PAGAMENTO([]);
      this.SET_PAGINA_ATUAL_FORMA_PAGAMENTO(1);
      this.$store.dispatch("formaPagamento/getLista");
    },

    listarPlanosContas() {
      this.$store.commit("planoConta/SET_PAGINA_ATUAL", 1);
      this.$store.dispatch("planoConta/listar");
    },

    setPlanoConta(value) {
      this.filtros.plano_conta = value;
    },

    setTagFormaCobranca(value) {
      let possuiaForma = false;
      for (let i = 0; i < this.filtros.formas_cobranca.length; i++) {
        if (this.filtros.formas_cobranca[i].id === value.id) {
          this.filtros.formas_cobranca.splice(i, 1);
          possuiaForma = true;
        }
      }
      if (!possuiaForma) {
        this.filtros.formas_cobranca.push(value);
      }
    },

    setTagFormaPagamento(value) {
      let possuiaForma = false;
      for (let i = 0; i < this.filtros.formas_pagamento.length; i++) {
        if (this.filtros.formas_pagamento[i].id === value.id) {
          this.filtros.formas_pagamento.splice(i, 1);
          possuiaForma = true;
        }
      }
      if (!possuiaForma) {
        this.filtros.formas_pagamento.push(value);
      }
    },

    converterSituacao(situacao) {
      const valores = {
        PEN: "Pendente",
        CAN: "Cancelado",
        LIQ: "Quitado",
        VEN: "Vencido",
        Sub: "Renegociado",
      };
      return valores[situacao];
    },

    abrirRelatorio() {
      const filtrosRelatorio = this.converterDadosParaLink();
      this.listar(filtrosRelatorio);
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        formaCobranca: form.formaCobranca || null,
        situacao: form.situacao.length > 0 ? form.situacao : null,
        formas_cobranca:
          form.formas_cobranca.length > 0
            ? form.formas_cobranca.map((forma) => forma.id)
            : null,
        formas_pagamento:
          form.formas_pagamento.length > 0
            ? form.formas_pagamento.map((forma) => forma.id)
            : null,
        data_inicial_vencimento: form.data_inicial_vencimento
          ? converteFormatoBrasilParaAmericano(form.data_inicial_vencimento)
          : null,
        data_final_vencimento: form.data_final_vencimento
          ? converteFormatoBrasilParaAmericano(form.data_final_vencimento)
          : null,
        plano_conta:
          form.plano_conta && form.plano_conta.id !== 0
            ? form.plano_conta.id
            : null,
        agrupar_por_parcelas: form.agrupar_por_parcelas
          ? form.agrupar_por_parcelas
          : null,
        order: form.order ? form.order : null,

        forma_cartao: form.forma_cartao === true ? 1 : null,
        forma_cheque: form.forma_cheque === true ? 1 : null,
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
span.badge {
  font-size: 95%;
}

small{
  line-height: 0.3px;
}

#filtros-rapidos,
#filtros-avancados {
  transition: all 0.1s;
}

#tabela-titulos >>> tr > th,
#tabela-titulos >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;
  background-color: white;
  max-height: 80vh !important;
  overflow: auto;
}



::v-deep #tabela-agrupada thead tr > th,
::v-deep #tabela-agrupada tbody tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;
  background-color: white;
 
}

::v-deep #tabela-titulos-agrupada thead tr > th,
::v-deep #tabela-titulos-agrupada tbody tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;

}

.table-area {
  max-height: 65vh;
  overflow-y: auto;
}

.filtro-avancado .form-group {
  margin-bottom: 1rem;
}
/* .grouped_table_to_view{
  display: none;
} */
.grouped_table_to_print {
  display: none;
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

.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

.datepicker {
  padding: 0;
}

.table-actions {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  color: #3e515b;
  border-top: 1px solid #ebecf0;
}

.checkboxes > *:not(:last-child) {
  margin-right: 10px;
  margin-left: 6px;
}



@media print {
  .grouped_table_to_view {
    display: none;
  }
  .grouped_table_to_print {
    display: block;
  }
  .table-area {
    max-height: 100%;
    max-width: 100%;
  }
}
</style>
