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
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_atividade_extra'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <g-data
                        :periodo="'sem_data'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                    </div>
                    <div class="col-md-6">
                      <div>
                        <label for="consultor" class="col-form-label"
                          >Consultor</label
                        >
                        <g-select-consultor
                          id="consultor"
                          v-model="filtros.consultor"
                          class="valid-input"
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
                    name="relatorio-matriculas"
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
      <div class="tabela-wrapper"></div>
      <b-table
        striped
        show-empty
        hover
        id="tabela-alunos-por-turma"
        class="tabela-alunos-por-turma"
        v-if="lista && !estaCarregando"
        :fields="tableFields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(consultor)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value ? data.value : '--'}}
          </span>
        </template>
        <template #cell(evasao_percentual)="data">
          <span>
            {{ data.value ? parseFloat(data.value).toFixed(2).replace(".",",") : '' }} %
          </span>
        </template>
        <template #cell(desistencias)="data">
          <span>
            {{ data.value }}
          </span>
        </template>
        <template #cell(retencao_percentual)="data">
          <span>
            {{ data.value ? parseFloat(data.value).toFixed(2).replace(".",",") : '' }} %
          </span>
        </template>
        <template #empty>
          <p>Nenhum resultado encontrado!</p>
        </template>
      </b-table>
      </div>
    </div>
    </div>
   
</template>
  
  <script>
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToCompare } from "../../utils/date";

export default {
  name: "ListaRelatorioConsultaDesistencia",
  data() {
    return {
      aviso: "",
      data_final: "",
      data_inicial: "",
      filtroVisivel: true,
      sortBy: "nome_turma",
      sortDesc: false,
      tableFields: [
        { key: "consultor", sortable: true },
        { key: "alunos_na_carteira", sortable: true, label:'Qnt de Alunos' },
        { key: "desistencias", sortable: true, label: 'Desistência de Interessados'},
        { key: "evasao_percentual", sortable: true, label: 'Evasão' },
        { key: "retencao_percentual", sortable: true, label: 'Retenção' }
      ],
      exportFields: {
        'Consultor': { 
          field: "consultor",
          callback: (value) => value ? value : '--'},

        'Alunos Carteira': { 
          field: "alunos_na_carteira",
          callback: (value) => value ? value : '--'},
        
        'Desistência de interessados': { 
          field: "desistencias",
          callback: (value) => value ? value : '--'
        },

        'evasao_percentual %': {
          field: "evasao_percentual",
          callback: (value) => value ? parseFloat(value).toFixed(2).replace(".",",") : '--'
        },

        'Retenção %': {
          field: "retencao_percentual",
          callback: (value) => value ? parseFloat(value).toFixed(2).replace(".",",") : '--'
        },
      }
    };
  },

  computed: {
    ...mapState("relatorioConsultaDesistencia", [
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
    ...mapActions("relatorioConsultaDesistencia", ["listar"]),
    ...mapMutations("relatorioConsultaDesistencia", [
      "SET_LISTA",
      "SET_PARAMETROS",
    ]),

    podeGerarRelatorio() {
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
        consultor_responsavel_funcionario: form.consultor ? form.consultor : null,
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
.tabela-alunos-por-turma >>> tr > th,
.tabela-alunos-por-turma >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-alunos-por-turma >>> table thead {
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
#tabela-alunos-por-turma {
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