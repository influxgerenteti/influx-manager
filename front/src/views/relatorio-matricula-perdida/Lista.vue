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
                    <b-col md="6">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período da Matrícula Perdida </label
                      >
                      <g-data
                        :periodo="'mes_anterior'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
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
                    name="relatorio-matricula-perdidas"
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
        id="tabela-matriculas-perdidas"
        class="tabela-matriculas-perdidas"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(data_primeiro_atendimento)="data">
          <span v-b-tooltip.viewport.top.hover :title='data.value | formatarData'>
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(telefone_contato)="data">
          <span v-b-tooltip.viewport.top.hover :title='data.value | formatarTelefone'>
            {{ data.value | formatarTelefone }}
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
import { mapState, mapActions, mapMutations } from "vuex";
import moment from 'moment';

export default {
  name: "ListaRelatorioMatriculaPerdida",
  data() {
    return {
        aviso:"",
        data_final:"",
        data_inicial:"",
      filtroVisivel: true,
      sortBy: "nome",
      sortDesc: false,
      fields:[
        { key: "data_primeiro_atendimento", sortable: true,  label: "Primeiro Atendimento" },
        { key: "nome", sortable: true },
        { key: "telefone_contato", sortable: true, label: "Telefone" },
        { key: "email_contato", sortable: true,  label: "E-mail" },
        { key: "funcionario", sortable: true },
        { key: "curso", sortable: true },
      ],
      exportFields:{
        'Primeiro Atendimento': {fields:'data_primeiro_atendimento',
          callback: (value) => moment(value).format('DD/MM/YYYY') },
        'Nome': 'nome',
        'Telefone': 'telefone_contato',
        'E-mail': 'email_contato',
        'Funcionário': 'funcionario',
        "Curso": 'curso'
      }
    };
  },

  computed: {
    ...mapState("relatorioMatriculaPerdida", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    ...mapActions("relatorioMatriculaPerdida", ["listar"]),
    ...mapMutations("relatorioMatriculaPerdida", ["SET_LISTA", "SET_PARAMETROS"]),

    podeGerarRelatorio() {
      return true;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink()
      this.SET_PARAMETROS(parametros)
      this.listar()
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        data_inicial: form.data_inicial ? form.data_inicial : null,
        data_final: form.data_final ? form.data_final : null,
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

.tabela-matriculas-perdidas >>> tr > th,
.tabela-matriculas-perdidas >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}

.tabela-matriculas-perdidas >>> table thead {
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
#tabela-matriculas-perdidas {
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
