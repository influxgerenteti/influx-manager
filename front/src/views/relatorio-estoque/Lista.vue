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
                    <div class="col-md-6">
                      <label for="idioma" class="col-form-label"
                        >Entrada entre</label
                      >
                      <g-data
                        :periodo="'dia_atual'"
                        @dataDe="filtros.data_entrega_inicio = $event"
                        @dataAte="filtros.data_entrega_fim = $event"
                      ></g-data>
                    </div>

                    <b-col md="3">
                      <label
                        v-help-hint="'form-livro_item'"
                        for="item"
                        class="col-form-label"
                        >Item</label
                      >
                      <g-select
                        id="id"
                        v-model="filtros.item"
                        :options="listaItens"
                        label="descricao"
                        class="multiselect-truncate"
                        required
                        track-by="id"
                      />
                    </b-col>
                  </div>

                  <div class="form-group row">
                    <div class="col-md-auto">
                      <label
                        v-help-hint="'filtro-tabela-estoque-tipo_responsavel'"
                        for="tipo_responsavel"
                        class="col-form-label d-block"
                        >Tipo de relatório</label
                      >
                      <b-form-group>
                        <b-form-radio-group
                          id="tipo_impressao"
                          v-model="relatorioSelecionado"
                          :options="tipoRelatorio"
                          name="tipo_relatorio"
                          class="checkbtn-line"
                        />
                      </b-form-group>
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
                    :data="listaSelecionada"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-estoque"
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
      hover small
      id="tabela-estoque"
      class="tabela-estoque"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="listaSelecionada"
    >
      <template #cell(data_movimento)="row">
        <span>{{ row.value | formatarData }}</span>
      </template>
      <template #cell(item)="row">
        <span v-if="row.value">{{ row.value }}</span>
        <span v-if="!row.value">{{ "--" }}</span>
      </template>
      <template #cell(saldo_estoque)="row">
        <span v-if="row.value">{{ row.value }}</span>
        <span v-if="!row.value">{{ "--" }}</span>
      </template>
      <template #cell(pedido_valor_item)="data">
        R$ {{ data.value | formatarMoeda(true, true) }}
      </template>
      <template #cell(movimento)="row">
        <span>{{ converterMovimento(row.value) }}</span>
      </template>
    </b-table>
  </div>
  <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
    <p>Nenhum resultado encontrado.</p>
  </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "ListaRelatorioEstoque",
  data() {
    return {
      filtroVisivel: true,
      sortBy: "data_movimento",
      sortDesc: false,
      fields: [
        { key: "data_movimento", sortable: true, label: "Data" },
        { key: "item", sortable: true, label: "Item" },
        { key: "saldo_estoque", sortable: true, label: "Saldo Estoque" },
        { key: "estoque_minimo", sortable: true, label: "Estoque Mínimo" },
        { key: "pedido_quantidade", sortable: true, label: "Quantidade" },
        { key: "pedido_valor_item", sortable: true, label: "Valor Unit." },
        { key: "movimento", sortable: true, label: "Tipo Movimento" },
      ],
      tipoRelatorio: [
        { text: "Saida ", value: 0 },
        { text: "Entradas ", value: 1 },
        { text: "Movimentações manuais ", value: 2 },
        { text: "Todas as Movimentações ", value: 3 },
      ],
      exportFields: {
        "Data do Movimento": "data_movimento",
        Item: "item",
        "Saldo Estoque": "saldo_estoque",
        "Estoque Mínimo": "estoque_minimo",
        Quantidade: "pedido_quantidade",
        "Valor Unit.": "pedido_valor_item",
        Movimento: "movimento",
      },
      relatorioSelecionado: 0,
      listaSelecionada: [],
    };
  },

  computed: {
    ...mapState("item", { listaItensState: "lista" }),
    ...mapState("relatorioEstoque", ["filtros", "lista", "estaCarregando"]),

    listaPessoas: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.listaFornecedor
        );
      },
    },
    listaItens: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.listaItensState
        );
      },
    },
  },

  watch: {
    relatorioSelecionado: function (value, oldValue) {
      oldValue,
      this.filtrarLista(value);
    },
  },

  mounted() {
    this.$store.commit("item/SET_PAGINA_ATUAL", 1);
    this.$store.dispatch("item/getLista").then(this.countCarregamento);
    this.SET_LISTA([]);
  },

  methods: {
    ...mapActions("item", { listarItems: "listar" }),
    ...mapActions("relatorioEstoque", { listarEstoque: "listar" }),
    ...mapMutations("relatorioEstoque", ["SET_LISTA", "SET_PARAMETROS"]),

    converterMovimento(movimento) {
      const valores = {
        E: "Entrada",
        S: "Saída",
        AE: "Entrada Manual",
        AS: "Saída Manual",
      };
      return valores[movimento];
    },
    podeGerarRelatorio() {
      return true;
    },

    filtrarLista(value) {
      if (value == 0) {
        return (this.listaSelecionada = this.lista.filter(
          (element) => element.movimento == "S"
        ));
      }
      if (value == 1) {
        return (this.listaSelecionada = this.lista.filter(
          (element) => element.movimento == "E"
        ));
      }
      if (value == 2) {
        return (this.listaSelecionada = this.lista.filter(
          (element) => element.movimento == "AE" || element.movimento == "AS"
        ));
      }
      if (value == 3) {
        return (this.listaSelecionada = this.lista);
      }
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listarEstoque().then((data) => {
        data,
        this.filtrarLista(this.relatorioSelecionado);
      });
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        data_inicial: form.data_entrega_inicio
          ? form.data_entrega_inicio
          : null,
        data_final: form.data_entrega_fim ? form.data_entrega_fim : null,
        item: form.item ? form.item.id : null,
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
.tabela-estoque >>> tr > th,
.tabela-estoque >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-estoque >>> table thead {
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
#tabela-estoque {
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