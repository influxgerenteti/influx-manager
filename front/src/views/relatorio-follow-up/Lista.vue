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
                    <b-col md="4">
                      <label
                        for="situacao_rapido"
                        class="col-form-label d-block"
                        >Data Cadastro - Interessado</label
                      >
                      <g-data
                        :periodo="'sem_data'"
                        @dataDe="filtros.data_cadastro_de = $event"
                        @dataAte="filtros.data_cadastro_ate = $event"
                      />
                    </b-col>
                    <b-col md="3">
                      <label for="situacao_filtro" class="col-form-label"
                        >Situação (Interessado)</label
                      >
                      <div>
                        <b-form-checkbox-group
                          id="situacao_filtro"
                          v-model="filtros.situacaoInteressado"
                          :options="listaSituacao"
                          buttons
                          button-variant="cinza"
                          name="situacao_filtro"
                          class="checkbtn-line"
                        />
                      </div>
                    </b-col>
                  </div>
                 
                </b-collapse>
              </div>
              <div class="mb-2 mt-2 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-follow-up"
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
      small
      hover
      striped
      show-empty
      sort-icon-right
      class="tabela-follow-up"
      id="tabela-follow-up"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="lista"
    >
      <template #cell(data_agendamento)="data">
        <span v-b-tooltip.top :title="data.value">
          {{ data.value | formatarData }}
        </span>
      </template>

      <template #cell(situacao)="data">
        <span>
          {{
            data.value === "A" ? "Ativo" : data.value === "R" ? "Receptivo" : ""
          }}
        </span>
      </template>

      <template #empty>
        <h4>Nenhum registro a ser exibido.</h4>
      </template>

      <template #table-busy>
        <div class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Carregando Dados...</strong>
        </div>
      </template>
    </b-table>
  </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import moment from "moment";

export default {
  name: "ListaRelatorioFollowUp",
  data() {
    return {
      filtroVisivel: true,
      sortBy: "interessado",
      sortDesc: false,
      exportFields: {
        Nome: "interessado",
        "E-mail": "email_contato",
        Telefone: "telefone_contato",

        Data: {
          field: "data_agendamento",
          callback: (value) => {
            const dataConvertida = moment(value);

            if (dataConvertida.isValid()) {
              return dataConvertida.format("DD/MM/YYYY");
            } else {
              return "";
            }
          },
        },

        "Etapa Funil": "estapa_funil",
        Situação: {
          field: "situacao",
          callback: (value) => (value == "A" ? "Ativo" : "Receptivo"),
        },
        "Idioma ": "idioma",
        Curso: "curso",
        "Tipo Contato": "tipo_contato",
      },
      fields: [
        { key: "interessado", sortable: true, label: "Nome" },
        { key: "email_contato", sortable: true, label: "E-mail Contato" },
        { key: "telefone_contato", sortable: true, label: "Telefone" },
        { key: "data_agendamento", sortable: true, label: "Data Agendamento" },
        { key: "estapa_funil", sortable: true, label: "Etapa Funil" },
        { key: "situacao", sortable: true, label: "Situação" },
        { key: "idioma", sortable: true, label: "Idioma" },
        { key: "curso", sortable: true, label: "Curso" },
        { key: "tipo_contato", sortable: true, label: "Tipo Contato" },
      ],

      situacaoFiltro: [
        { value: "V", text: "Vigente" },
        { value: "E", text: "Encerrado" },
        { value: "R", text: "Rescindido" },
        { value: "C", text: "Cancelado" },
        { value: "T", text: "Trancado" },
      ],
      listaSituacao: [
        { value: "A", text: "Aberto" },
        { value: "B", text: "Convertido" },
        { value: "P", text: "Rescindido" },
      ],
      tipoFollowup: [
        { value: 1, text: "Interessado" },
        { value: 3, text: "Aluno" },
        { value: 2, text: "Convenio" },
      ],
    };
  },

  computed: {
    ...mapState("relatorioFollowUp", ["filtros", "lista", "estaCarregando"]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    ...mapActions("relatorioFollowUp", ["listar"]),
    ...mapMutations("relatorioFollowUp", ["SET_LISTA", "SET_PARAMETROS"]),

    setTipoFollowupSelecionado(value) {
      this.tipoFollowupSelecionado = value;
    },

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
        data_cadastro_de: form.data_cadastro_de ? form.data_cadastro_de : null,
        data_cadastro_ate: form.data_cadastro_ate
          ? form.data_cadastro_ate
          : null,
        situacao_interessado: form.situacaoInteressado || null,
        
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
.tabela-follow-up >>> tr > th,
.tabela-follow-up >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-follow-up >>> table thead {
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
#tabela-follow-up {
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