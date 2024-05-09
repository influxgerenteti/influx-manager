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
                <div class="form-group d-md-flex">
                    <div class="col-md-4">
                      <label
                        v-help-hint="'filtro_avancado'"
                        for="data_inicial"
                        class="col-form-label"
                      >
                        Entrada
                      </label>
                      <g-data
                        :periodo="'sem_data'"
                        @dataDe="itemSelecionado.data_inicial_entrada = $event"
                        @dataAte="itemSelecionado.data_final_entrada = $event"
                      />
                    </div>
                    <div class="col-md-4">
                      <label
                        v-help-hint="'filtro_avancado'"
                        for="data_inicial"
                        class="col-form-label"
                      >
                        Bom para
                      </label>
                      <g-data
                        :periodo="'sem_data'"
                        @dataDe="itemSelecionado.data_inicial_bom_para = $event"
                        @dataAte="itemSelecionado.data_final_bom_para = $event"
                      />
                    </div>
                    <div class="col-md-4">
                      <label
                        v-help-hint="'filtro_avancado'"
                        for="data_inicial"
                        class="col-form-label"
                      >
                        Baixa
                      </label>
                      <g-data
                        :periodo="'sem_data'"
                        @dataDe="itemSelecionado.data_inicial_baixa = $event"
                        @dataAte="itemSelecionado.data_final_baixa = $event"
                      />
                    </div>
                    </div>
                    <div class="form-group d-md-flex">
                    <div class="col-md-4">
                      <label
                        v-help-hint="'filtro_avancado'"
                        for="data_inicial"
                        class="col-form-label"
                      >
                        Devolução
                      </label>
                      <g-data
                        :periodo="'sem_data'"
                        @dataDe="itemSelecionado.data_inicial_devolucao = $event"
                        @dataAte="itemSelecionado.data_final_devolucao = $event"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="
                          'filtro-relatorio-cheques_motivo_devolucao'
                        "
                        for="motivo_devolucao"
                        class="col-form-label"
                        >Motivo da devolução</label
                      >
                      <g-select
                        :value="itemSelecionado.motivo_devolucao"
                        :select="setMotivoDevolucao"
                        :options="listaMotivoDevolucao"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </div>
                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-relatorio-cheques_conta'"
                        for="conta"
                        class="col-form-label"
                        >Conta</label
                      >
                      <g-select
                        :value="itemSelecionado.conta"
                        :select="setConta"
                        :options="listaContas"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </div>
                    </div>
                    <div class="form-group d-md-flex">
                    <div class="col-md-auto">
                      <label
                        v-help-hint="'filtro-relatorio-cheques_situacao'"
                        for="situacao"
                        class="col-form-label d-block"
                        >Situação</label
                      >
                      <b-form-checkbox-group
                        id="situacao"
                        v-model="itemSelecionado.situacao"
                        :options="listaSituacao"
                        buttons
                        button-variant="cinza"
                        name="situacao"
                        class="checkbtn-line"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-relatorio-cheques_tipo'"
                        for="tipo"
                        class="col-form-label d-block"
                        >Tipo</label
                      >
                      <b-form-checkbox-group
                        id="tipo"
                        v-model="itemSelecionado.tipo"
                        :options="listaTipo"
                        buttons
                        button-variant="cinza"
                        name="tipo"
                        class="checkbtn-line"
                      />
                    </div>
                    </div>
                </b-collapse>
              </div>
              
              <div class="mb-2 d-md-flex justify-content-end">
                <div class="col-md-auto" v-if="listaItems.length">
                  <g-print></g-print>
                </div>

                <div class="col-md-auto" v-if="listaItems.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="listaItems"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-cheques"
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
    <div class="tabela-wrapper">
      <div v-if="estaCarregando" class="d-flex h-100">
        <load-placeholder :loading="estaCarregando" />
      </div>
    <b-table  
    class="tabela-cheques"
      :items="listaItems"
      :fields="cabecalho"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
        striped
        hover
        outlined
        small
        fixed-header
        sort-icon-right>
        >
      <template #cell(situacao)="data">
        <div style="text-align: center">
          <div
            v-b-tooltip.viewport.top.hover
            :title="data.item.situacao == 'P' ? 'Pendente' : data.item.situacao == 'B' ? 'Baixado' : data.item.situacao == 'D' ? 'Devolvido' :'Cancelado'"
            style="display: inline; margin-right: 6px;">
            {{ data.item.situacao == 'P' ? 'Pendente' : data.item.situacao == 'B' ? 'Baixado' : data.item.situacao == 'D' ? 'Devolvido' :'Cancelado' }}
          </div>
        </div>
      </template>
      <template #cell(tipo)="data">
        <div style="text-align: center">
          <div
            v-b-tooltip.viewport.top.hover
            :title="data.item.tipo == 'P' ? 'Pagar' :'Receber'"
            style="display: inline; margin-right: 6px;">
            {{ data.item.tipo  == 'P' ? 'Pagar' :'Receber' }}
          </div>
        </div>
      </template> 
    </b-table> 
  </div>
  <div v-if="!listaItems.length && !estaCarregando" class="busca-vazia">
    <p>Nenhum resultado encontrado.</p>
  </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import {
  beginOfDay,
  endOfDay,
  getDateFromISO,
  dateToCompare,
  dateToString,
  stringToISODate,
} from "../../utils/date";
import open from "../../utils/open";

export default {
  name: "ListaRelatorioCheques",
  data() {
    return {
      sortBy : 'numero_cheque',
      sortDesc : false,
      lista:[],
      filtroVisivel: true,
      exportFields: {},
      selected: 0,
      situacao: [],
      listaSituacao: [
        { text: "Pendente", value: "P" },
        { text: "Baixado", value: "B" },
        { text: "Devolvido", value: "D" },
      ],
      tipo: [],
      listaTipo: [
        { text: "A receber", value: "R" },
        { text: "A pagar", value: "P" },
      ],
      exportFields:{ 
      
         "Número Cheque": 'numero_cheque',
         'Contato': 'nome_contato', 
         'Situação' : {
          field: 'situacao',
          callback: (value) => value == 'P' ? 'Pendente' : situacao == 'B' ? 'Baixado' : situacao == 'D' ? 'Devolvido' :'Cancelado'
        }, 
        'Tipo' : {
          field: 'tipo',
          callback: (value) => value == 'P' ? 'Pagar' :'Receber'
        }, 
         'conta': 'conta',
         'Motivo Devolução' : 'motivo_devolucao',
         'Data bom para': 'data_bom_para',
         'Data Entrada' : 'data_entrada',
         'Data Baixa' : 'data_baixa',
         'Data Devolução' : 'data_devolucao'},
      cabecalho : [
        { key : 'numero_cheque', label: "Número Cheque", sortable: true },
        { key : 'nome_contato', label: 'Contato', sortable: true },
        { key : 'situacao', label: 'Situacão', sortable: true },
        { key : 'tipo', label: 'Tipo', sortable: true },
        { key : 'conta', label: 'Conta', sortable: true },
        { key : 'motivo_devolucao', label: 'Motivo Devolução', sortable: true },
        { key : 'data_bom_para', label: 'Data Bom Para', sortable: true },
        { key : 'data_entrada', label: 'Data Entrada', sortable: true },
        { key : 'data_baixa', label: 'Data Baixa', sortable: true },
        { key : 'data_devolucao', label: 'Data Devolução', sortable: true },
      ],
    };
  },
  computed: {
    ...mapState("relatorioCheques", {
      listaItems: "lista",
      estaCarregando: "estaCarregando",
      itemSelecionado: "itemSelecionado",
    }),

    listaContas: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.$store.state.conta.lista
        );
      },
    },

    listaMotivoDevolucao: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.$store.state.motivoDevolucaoCheque.lista
        );
      },
    },
  },
  mounted() {
    this.$store.commit("conta/SET_PAGINA_ATUAL", 1);

    this.getListaConta();
    this.getListaMotivoDevolucaoCheque();
  },
  methods: {
    ...mapMutations("relatorioCheques", [
      "SET_PAGINA_ATUAL",
      "SET_ITEM_SELECIONADO",
      "SET_ITEM_SELECIONADO_ID",
      "LIMPAR_ITEM_SELECIONADO",
      "SET_PARAMETROS",
    ]),
    ...mapActions("relatorioCheques", { listarCheques: "listar" }),
    ...mapActions("motivoDevolucaoCheque", {
      getListaMotivoDevolucaoCheque: "listar",
    }),
    ...mapActions("conta", { getListaConta: "getLista" }),

    // getDateFromISO: getDateFromISO,

    // dateToCompare: dateToCompare,

    // stringToISODate: stringToISODate,

    // dateToString: dateToString,

    // setDataInicialEntrada(value) {
    //   this.itemSelecionado.data_inicial_entrada = value;
    // },

    // setDataFinalEntrada(value) {
    //   this.itemSelecionado.data_final_entrada = value;
    // },

    // setDataInicialBomPara(value) {
    //   this.itemSelecionado.data_inicial_bom_para = value;
    // },

    // setDataFinalBomPara(value) {
    //   this.itemSelecionado.data_final_bom_para = value;
    // },

    // setDataInicialBaixa(value) {
    //   this.itemSelecionado.data_inicial_baixa = value;
    // },

    // setDataFinalBaixa(value) {
    //   this.itemSelecionado.data_final_baixa = value;
    // },

    // setDataInicialDevolucao(value) {
    //   this.itemSelecionado.data_inicial_devolucao = value;
    // },

    // setDataFinalDevolucao(value) {
    //   this.itemSelecionado.data_final_devolucao = value;
    // },

    setConta(value) {
      this.itemSelecionado.conta = value;
    },

    setMotivoDevolucao(value) {
      this.itemSelecionado.motivo_devolucao = value;
    },

    abrirRelatorio() {
      // const franqueada =
      //   this.$store.state.root.usuarioLogado.franqueadaSelecionada;
      // const auth =
      //   this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso;
      // const rota = this.$route.matched[0].path;
      // const filtrosRelatorio = this.converterDadosParaLink();
      // open(
      //   `/api/relatorios/cheques?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${filtrosRelatorio}`,
      //   "_blank"
      // );
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listarCheques();
    },

    podeGerarRelatorio() {
      return true;
    },

    // gerarRelatorio() {
    //   if (this.$v.$invalid) {
    //     this.isValid = false;
    //     return;
    //   }

    //   open(this.urlRelatorio(), "_blank");
    // },
    limparFiltros() {
      // TODO: Adicionar os states para realizar a limpeza do filtro
    },

    converterDadosParaLink() {
      const form = { ...this.itemSelecionado };

      const dados = {
        
        tipo: form.tipo ? form.tipo : null,
        situacao: form.situacao ? form.situacao : null,
        conta: form.conta ? form.conta.id : null,
        motivo_devolucao_cheque: form.motivo_devolucao
          ? form.motivo_devolucao.id
          : null,
        data_entrada_inicial: form.data_inicial_entrada
          ? form.data_inicial_entrada
          : null,
        data_entrada_final: form.data_final_entrada
          ? form.data_final_entrada
          : null,
        data_bom_para_inicial: form.data_inicial_bom_para
          ? form.data_inicial_bom_para
          : null,
        data_bom_para_final: form.data_final_bom_para
          ? form.data_final_bom_para
          : null,
        data_baixa_inicial: form.data_inicial_baixa
          ? form.data_inicial_baixa
          : null,
        data_baixa_final: form.data_final_baixa ? form.data_final_baixa : null,
        data_devolucao_inicial: form.data_inicial_devolucao
          ? form.data_inicial_devolucao
          : null,
        data_devolucao_final: form.data_final_devolucao
          ? form.data_final_devolucao
          : null,
      };

      let dadosArray = [];
      for (let key in dados) {
        if (dados[key] !== null) {
          dadosArray.push(`${key}=${dados[key]}`);
        }
      }

      return dadosArray.join("&");
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
.tabela-cheques >>> tr > th,
.tabela-cheques >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-cheques >>> table thead {
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
#tabela-cheques {
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
