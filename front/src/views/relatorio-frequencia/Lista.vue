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
                    <div class="col-md-6">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <g-data
                         :validarMesmoAno="true"
                        :periodo="'mes_anterior'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      >
                      </g-data>
                    </div>
                  </div>
                </b-collapse>
              </div>

              <div class="mb-2 mt-2 d-flex justify-content-end">
                <div class="col-md-auto">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportfields"
                    type="xls"
                    name="relatorio-frequencia"
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

     <b-table
      striped
      hover
      small
      id="tabela-frequencia"
      class="table-card-hover table-schedule"
      :busy="estaCarregando"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="lista"
    >
 
    <template #empty>
            <h4>Nenhum registro a ser exibido.</h4>
      </template>

      <template #cell(frequencia)="data">
        <span> {{ data.value ? parseFloat(data.value).toFixed(2).replace(".",",") : '' }} %</span>
      </template> 
    </b-table> 


    <div v-if="estaCarregando" class="d-flex h-100">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <b-table
      striped
      hover
      small
      id="tabela-frequencia"
      class="table-card-hover table-schedule"
      :busy="estaCarregando"
      :fields="fieldsResumo"
      :items="media_frequencia"
    >
 
    <template #cell(media_frequencia)="data">
        <span> {{ data.value ? parseFloat(data.value).toFixed(2).replace(".",",") : '' }} %</span>
      </template> 

    <template #empty>
            <h4>Nenhum registro a ser exibido.</h4>
      </template>
    
    </b-table>


   

  
  </div>
</template>
<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "listaRelatorioFrequencias",
  data() {
    return {
      fields: [
        { label: "Aluno", key: "aluno_nome", sortable: true },
        { label: "Turma", key: "aluno_turma", sortable: true },
        { label: "Total Aulas", key: "total_aulas", sortable: true },
        { label: "Total Faltas", key: "total_faltas", sortable: true },
        { label: "Total Presença", key: "total_presencas", sortable: true },
        { label: "Frequência", key: "frequencia", sortable: true },
      ],
      fieldsResumo: [
        { label: "Média Total da Escola", key: "media_frequencia", sortable: true },
      ],
      sortBy: "aluno_nome",
      sortDesc: false,
      filtroVisivel: true,
      exportfields: {
        Nome: "aluno_nome",
        Turma: "aluno_turma",
        "Frequência":{field: "frequencia", callback:(value) => value ? parseFloat(value).toFixed(2).replace(".",",")+" %" : '--'},
        "Total Aulas": "total_aulas",
        "Total Faltas": "total_faltas",
        "Total Presença": "total_presencas",
        "media_frequencia":"media_frequencia"
      },
    };
  },
  computed: {
    ...mapState("relatorioFrequencias", [
      "filtros",
      "lista",
      "media_frequencia",
      "estaCarregando",
    ]),

    
  },

  mounted() {
    this.SET_LISTA([]);
    this.SET_RESUMO([]);
  },

  methods: {
    ...mapActions("relatorioFrequencias", ["listar"]),
    ...mapMutations("relatorioFrequencias", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "SET_RESUMO"
    ]),
    ...mapMutations("relatorioFrequencias", ["SET_PARAMETROS"]),

  
    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
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
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null,
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

#tabela-frequencia >>> tr > th,
#tabela-frequencia >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;
}
#tabela-frequencia >>> thead {
  background: #fff;
}
.table-area {
  min-height: 500px;
  max-height: 65vh;
  overflow: scroll;
}

@media print {
  #tabela-estoque {
    overflow: visible;
  }
  .table-area {
    overflow: hidden;
  }
}
</style>
