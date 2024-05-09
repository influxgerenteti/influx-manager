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
                    <b-col md="auto" lg="auto">
                      <label for="modalidade-filtro" class="col-form-label nowrap"
                        >Modalidade da Turma</label
                      >
                      <div>
                        <b-form-checkbox-group
                          id="situacao_filtro"
                          v-model="filtros.modalidadeTurma"
                          :options="modalidadeTurma"
                          buttons
                          button-variant="cinza"
                          name="modalidade-filtro"
                          class="checkbtn-line"
                        />
                      </div>
                    </b-col>
                    <b-col md="5" lg="4">
                        <label for="livro-filtro" class="col-form-label">
                            Livro
                        </label>
                        <div>
                            <g-select-livro
                                id="livro"
                                v-model="filtros.livro"
                            >
                            </g-select-livro>
                        </div>
                    </b-col>

                    <b-col md="4" lg="4">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label nowrap"
                        >Intervalo de Contratação</label
                      >
                      <div class="row">
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">De</div>
                            </div>
                            <g-datepicker
                              v-model="data_inicial"
                              :element-id="'data_inicial'"
                              :value="filtros.data_inicial"
                              :selected="setPeriodoDe"
                            />
                          </div>
                        </div>
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">à</div>
                            </div>
                            <g-datepicker
                              v-model="data_final"
                              :element-id="'data_final'"
                              :value="filtros.data_final"
                              :selected="setPeriodoAte"
                            />
                          </div>
                        </div>
                      </div>
                      <div v-if="aviso" class="floating-message bg-danger">
                        {{ aviso }}
                      </div>
                    </b-col>
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
                    name="relatorio-saldo-horas"
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
      striped hover
      id="tabela-saldo-horas"
      class="tabela-saldo-horas"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="lista"
    >

    <template #cell(data_contrato)="row">
      <span>{{ row.value | formatarData }}</span>
    </template>
    <template #cell(data_inicio_contrato)="row">
      <span>{{ row.value | formatarData }}</span>
    </template>
    <template #cell(data_termino_contrato)="row">
      <span>{{ row.value | formatarData }}</span>
    </template>
    <template #cell(qnt_creditos)="row">
       <span v-if="row.value">{{ row.value }}</span>
      <span v-if="!row.value">{{ "--" }}</span>
    </template>
    <template #cell(saldo_creditos)="row">
      <span v-if="row.value">{{ row.value }}</span>
      <span v-if="!row.value">{{ "--" }}</span>
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
import { dateToCompare } from "../../utils/date";
import moment from 'moment';

export default {
  name: "ListaRelatorioSaldoHorasVipPersonal",
  data() {
    return {
      aviso:"",
      data_inicial:"",
      data_final:"",
      sortBy: 'Aluno',
      sortDesc: false,
      filtroVisivel: true,
      modalidadeTurma:[
        { value: "2", text: "VIP" },
        { value: "3", text: "Personal" },
    ],
    fields: [
      { key: 'aluno', sortable: true, label: 'Aluno'},
      { key: 'data_contrato', sortable: true, label: 'Data Contrato'},
      { key: 'data_inicio_contrato', sortable: true, label: 'Data Início'},
      { key: 'data_termino_contrato', sortable: true, label: 'Data Fim'},
      { key: 'livro', sortable: true, label: 'Livro'},
      { key: 'qnt_creditos', sortable: true, label: 'Créditos'},
      { key: 'saldo_creditos', sortable: true, label: 'Saldo'},
    ],
    exportFields: {
      'Aluno' : 'aluno',
      'Data Contrato' : {
          field : 'data_contrato',
          callback: (value) => moment(value).format('DD/MM/YYYY')
        },
      'Data Início' : {
        field : 'data_inicio_contrato',
        callback: (value) => moment(value).format('DD/MM/YYYY')
      },
      'Data Fim' : {
        field : 'data_termino_contrato',
        callback: (value) => moment(value).format('DD/MM/YYYY')
      },
      'Livro' : 'livro',
      'Créditos' : 'qnt_creditos',
      'Saldo Créditos' : 'saldo_creditos'
      }
    }
  },

  computed: {
    ...mapState("relatorioSaldoHorasVipPersonal", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    dateToCompare: dateToCompare,
    ...mapActions("relatorioSaldoHorasVipPersonal", ["listar"]),
    ...mapMutations("relatorioSaldoHorasVipPersonal", ["SET_LISTA", "SET_PARAMETROS"]),

    podeGerarRelatorio() {
      return true;
    },

    testar(old, newValue){
      console.log(old)
      console.log(newValue)
    },

    setPeriodoDe(value) {
      this.aviso = "";
      this.filtros.data_inicial = value;

      if (this.filtros.data_inicial !== "") {
        const arData = this.filtros.data_inicial.split("/");
        arData[2] = String(parseInt(arData[2]) + 1);

        let dataFinal = arData.join("/");
      }
    },

    setPeriodoAte(value) {
      this.aviso = "";
      this.filtros.data_final = value;

      if (dateToCompare(value) < dateToCompare(this.filtros.data_inicial)) {
        this.aviso = ` Data ${value} não pode ser colocada, data inicial deve ser menor que a data final!`;
      }

      if (value === "") {
        this.aviso = "";
      }
    },


    abrirRelatorio() {
      let parametros = this.converterDadosParaLink()
      this.SET_PARAMETROS(parametros)
      this.listar()
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        modalidade_turma: form.modalidadeTurma || null,
        livro: form.livro || null,
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null
        
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
.tabela-saldo-horas >>> tr > th,
.tabela-saldo-horas >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-saldo-horas >>> table thead {
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
#tabela-saldo-horas {
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
