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

                  <b-col class="col-md-4">
                    <label v-help-hint="'filtroAvancado-item_produto'" for="item_produto" class="col-form-label">Item</label>
                    <typeahead id="item_produto" :item-hit="setItemDescricao" source-path="/api/item_produto/buscar_descricao" key-name="corpo.descricao" />
                  </b-col>
                    <div class="d-flex col-md-auto">
                      <div>
                        <label for="valor_servico" class="col-form-label"
                          >Valor de custo entre:</label
                        >
                        <div class="d-flex">
                          <div class="mr-2">
                            <g-numeric
                              id="valor_servico"
                              v-model="filtros.valor_custo_de"
                              class="form-control"
                            />
                          </div>
                          <div>
                            <g-numeric
                              id="valor_servico"
                              v-model="filtros.valor_custo_ate"
                              class="form-control"
                            />
                          </div>
                        </div>
                       <div v-if="filtros.valor_custo_de && filtros.valor_custo_ate && !validarValorCusto()" class="floating-message bg-danger">
                          Valor inicial deve ser menor que o valor final!
                        </div>
                      </div>
                    </div>
                    <div class="d-flex col-md-auto">
                      <div>
                        <label for="valor_servico" class="col-form-label"
                          >Valor de Venda entre:</label
                        >
                        <div class="d-flex">
                          <div class="mr-2">
                            <g-numeric
                              id="valor_servico"
                              v-model="filtros.valor_venda_de"
                              class="form-control"
                            />
                          </div>
                          <div>
                            <g-numeric
                              id="valor_servico"
                              v-model="filtros.valor_venda_ate"
                              class="form-control"
                            />
                          </div>
                        </div>
                    
                        <div v-if="filtros.valor_venda_de && filtros.valor_venda_ate && !validarValorVenda()" class="floating-message bg-danger">
                          Valor inicial deve ser menor que o valor final!
                        </div>
                          </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-4">
                      <label class="col-form-label">Status</label>
                      <b-form-radio-group
                        v-model="filtros.status_item"
                        :options="[
                          { text: 'Ativo', value: 'A' },
                          { text: 'Inativo', value: 'I' },
                        ]"
                        name="status"
                      />
                    </div>
                    <div class="col-md-auto">
                      
                      <label
                        v-help-hint="'filtroRapido-atividade_extra_situacao'"
                        for="filtro_avancado_situacao"
                        class="col-form-label"
                        >Situação</label
                      >
                      <div class="d-block">
                        <b-form-radio-group
                          id="filtro_avancado_situacao"
                          v-model="filtros.estoque_negativo"
                          :options="[
                            { text: 'Todos', value: null },
                            { text: 'Estoque negativo', value: 'true' },
                            { text: 'Estoque positivo', value: 'false' },
                          ]"
                          name="situacao"
                          button-variant="cinza"
                          class="e"
                        />
                      </div>
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
                    name="relatorio-itens-de-estoque"
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
        id="tabela-itens-de-estoque"
        class="tabela-itens-de-estoque"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(item)="data">
          <span v-b-tooltip.viewport.top.hover :title='data.value'>
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(valor_custo)="data">
          <span v-b-tooltip.viewport.top.hover :title='data.value| formatarMoeda(false, false)'>
            {{ data.value | formatarMoeda(false, false) }}
          </span>
        </template>
        <template #cell(valor_venda)="data">
          <span v-b-tooltip.viewport.top.hover :title='data.value | formatarMoeda(false, false)'>
            {{ data.value | formatarMoeda(false, false) }}
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
import GNumeric from "@/components/GNumeric.vue";
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  components: { GNumeric },
  name: "ListaRelatorioItensDeEstoque",
  data() {
    return {
      filtroVisivel: true,
      estoque_negativo: null,
      itemId: null,
      sortBy: "item",
      sortDesc: false,
      fields:[
        { key: "item", sortable: true },
        { key: "valor_custo", sortable: true,  label: "Valor Custo" },
        { key: "valor_venda", sortable: true,  label: "Valor Venda" },
        { key: "estoque_minimo", sortable: true,  label: "Estoque Mínimo" },
        { key: "saldo", sortable: true },
      ],
      exportFields: {
        'Item' : 'item',
        'Valor Custo' : {
          field: 'valor_custo',
          callback: (value) =>  `R$ ${value.replace(".", ",")}`
        },
        'Valor Venda' : {
          field: 'valor_venda',
          callback: (value) =>  `R$ ${value.replace(".", ",")}`
        },
        'Estoque Mínimo' : 'estoque_minimo',
        'Saldo' : 'saldo',
      },

    };
  },

  computed: {
    ...mapState("relatorioItensDeEstoque", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);

  },

  methods: {
    ...mapActions("relatorioItensDeEstoque", ["listar"]),
    ...mapMutations("relatorioItensDeEstoque", [
      "SET_LISTA",
      "SET_PARAMETROS"
    ]),

    podeGerarRelatorio() {
      if(this.validarValorCusto() && this.validarValorVenda()){
        return true
      }
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
      return false;
    },
    setItemDescricao(value){
      console.log(value)
      if(value){
        this.itemId = value.id
        return
      }
      this.itemId = null
    },
    validarValorCusto(){
      if(this.filtros.valor_custo_de <= this.filtros.valor_custo_ate){

        return true
      }
   
      return false
    },

    validarValorVenda(){
      if(this.filtros.valor_venda_de <= this.filtros.valor_venda_ate){
        return true
      }
      return false
    },

    setBusca(value) {
      this.item = value;
      this.abrirRelatorio();
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },
    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        item: this.itemId || null,
        valor_venda_de: form.valor_venda_de || null,
        valor_venda_ate: form.valor_venda_ate || null,
        valor_custo_de: form.valor_custo_de || null,
        valor_custo_ate: form.valor_custo_ate || null,
        estoque_negativo: form.estoque_negativo || null,
        situacao: form.status_item || null,
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

.tabela-itens-de-estoque >>> tr > th,
.tabela-itens-de-estoque >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
}

.tabela-itens-de-estoque >>> table thead {
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

#tabela-itens-de-estoque {
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