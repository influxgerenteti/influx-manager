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
                  <div class="form-group row" >
                    <div class="col-form-label d-flex wrap w-100">
                      <div class="col-md-6 col-sm-12">
                        <label for="semestre">Semestre</label>
                        <g-select-semestre
                          id="semestre"
                          v-model="filtros.semestre"
                          class="valid-input"
                        />
                      </div>
                      <div class="col-md-6 col-sm-12" style="width: calc(100% - 40px);">
                        <label for="saldo_atual">Saldo Atual</label>
                        <div class="row">
                          <div class="col col-sm col-md-auto">
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <div class="input-group-text">De</div>
                                <input
                                  min="0"
                                  max="100000"
                                  class="input-group-text number"
                                  type="number"
                                  v-model.number="filtros.saldo_de"
                                />
                              </div>
                            </div>
                          </div>
                          <div class="col col-sm col-md-auto">
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <div class="input-group-text">A</div>
                                <input
                                  min="0"
                                  max="100000"
                                  class="input-group-text number"
                                  type="number"
                                  v-model.number="filtros.saldo_ate"
                                />
                              </div>
                            </div>
                          </div>
                        </div>
                          <div style="color: red" 
                          v-if="filtros.saldo_de && 
                          (filtros.saldo_ate || filtros.saldo_ate == '0')&&
                          (filtros.saldo_de > filtros.saldo_ate)"
                          >
                            "Saldo de" deve ser menor que "Saldo até"! 
                          </div>
                      </div>
                    </div>
                  </div>

                  <!-- <b-col md="6" class="col-form-label row">
                    <b-row>
                    <div md="3">
                      <label for="saldoDe">Saldo Atual de</label>
                      <input id="saldoDe" type="number">
                    </div>
                    </b-row>
                    
                    <div md="3">
                      <label for="saldoAte">Saldo Atual Até</label>
                      <input id="saldoDeAte" type="number">
                    </div>
                  </b-col> -->
                </b-collapse>
              </div>
              <div class="mb-2 mt-5 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="resumo"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-controle-material"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
                </div>
                <div class="col-md-auto">
                  <b-btn
                    :disabled="podeGerarRelatorio()"
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
        id="tabela-controle-material"
        class="tabela-controle-material"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="resumo"
        outlined
        small
        fixed-header
        sort-icon-right
        striped
        show-empty
        hover
      >
        <template #head()="data">
            <span v-b-tooltip.hover :title=data.field.tooltip>
             {{ data.label }}
            </span>
        </template>
        <template #cell(item)="data">
           <span v-b-tooltip.hover :title=data.value>{{ data.value}}</span>
        </template>

        <template #cell(tipo_item)="data">
           <span v-b-tooltip.hover title="Material Didático" v-if="data.value == 'm'">Material Didático</span>
           <span v-b-tooltip.hover title="Produto Influx" v-if="data.value == 'p'">Produto Influx</span>
        </template>

        <template #cell(quantidade_demandada)="data">
           <span v-if="Math.sign(data.value) == 1" v-b-tooltip.hover :title=data.value>{{ data.value}}</span>
           <span v-if="Math.sign(data.value) != 1" v-b-tooltip.hover :title="0"> {{0}} </span>
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
  name: "ListaControleMaterialDidatico",
  data() {
    return {
      sortBy : 'item',
      sortDesc : false,
      filtroVisivel: true,
      exportFields:{
        'Item' : 'item',
        'Tipo': {
          field: 'tipo_item',
          callback: (value) => value == 'm'? 'Material Didático' : 'Produto Influx'
        },
        'Saldo' : 'saldo_estoque',
        'Quantidade Demandada': {
        field: 'quantidade_demandada',
          callback: (value) => Math.sign(value) == 1 ? value : '0'
        }
      },
      fields: [
        { key: "item",label: "item", sortable: true, tooltip:'Item' },
        { key: "tipo_item",label: "Tipo", sortable: true},
        { key: "saldo_estoque",label: "Saldo", sortable: true, tooltip:'Saldo de estoque'  },
        { key: "quantidade_demandada",label: "Quantidade Demandada", sortable: true, tooltip:'Quantidade Demandada' }
      ],
    };
  },

  computed: {
    ...mapState("relatorioControleMaterialDidatico", [
      "filtros",
      "lista",
      "resumo",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    ...mapActions("relatorioControleMaterialDidatico", ["listar"]),
    ...mapMutations("relatorioControleMaterialDidatico", ["SET_LISTA", "SET_FILTROS", "SET_RESUMO"]),

    podeGerarRelatorio() {
      if(this.filtros.saldo_de > this.filtros.saldo_ate){
        return true
      }
      if(!this.filtros.semestre){
        return true
      }

      return false
    },

    abrirRelatorio() {
      this.SET_FILTROS(this.filtros);
      this.listar();
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
.tabela-controle-material >>> tr > th,
.tabela-controle-material >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-controle-material >>> table thead {
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
#tabela-controle-material {
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