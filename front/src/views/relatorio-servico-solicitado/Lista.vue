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
                  <div class="form-group row mb-0">
                    <b-col md="4">
                      <label
                        v-help-hint="'filtroRapido-aluno_nome_aluno'"
                        for="nome_aluno"
                        class="col-form-label"
                        >Aluno</label
                      >
                      <typeahead
                        id="nome_aluno"
                        :item-hit="setAluno"
                        source-path="/api/aluno/buscar-nome"
                        key-name="pessoa.nome_contato"
                      />
                    </b-col>

                    <b-col md="3">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <g-data
                        :periodo="'mes_corrente'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                      
                    </b-col>

                    <b-col md="3">
                      <label
                        v-help-hint="'filtroRapido-tipo_servico'"
                        for="tipo_servico"
                        class="col-form-label"
                        >Tipo serviço</label
                      >
                      <g-select
                        id="tipoServico"
                        v-model="filtros.tipo_servico"
                        :options="listaTipoServico"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </b-col>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end mt-3">
                <div class="col-md-auto" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-servico-solicitado"
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
        striped
        hover
        id="tabela-servico-solicitado"
        class="tabela-servico-solicitado"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(data_solicitacao)="row">
          <span v-if="row.value">{{ row.value | formatarData }}</span>
          <span v-if="!row.value">{{ "" }}</span>
        </template>
        <template #cell(data_vencimento)="row">
          <span v-if="row.value">{{ row.value | formatarData }}</span>
          <span v-if="!row.value">{{ "" }}</span>
        </template>
        <template #cell(situacao)="row">
          <span v-if="row.value">{{
                row.value == "PEN"
              ? "Pendente"
              : row.value == "CAN"
              ? "Cancelado"
              : row.value == "LIQ" 
              ? 'Liquidado'
              : "--"
          }}</span>
          <span v-if="!row.value">{{ "--" }}</span>
        </template>
        <template #cell(valor)="row">
          <span v-if="row.value">R$ {{ row.value | formatarMoeda(true) }}</span>
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
import moment from "moment";

export default {
  name: "ListaRelatorioServicoSolicitado",
  data() {
    return {
      aviso: "",
      filtroVisivel: true,
      filtroSelecionado: 1,
      sortBy: "aluno",
      sortDesc: false,
      fields: [
        { key: "aluno", sortable: true},
        { key: "descricao", sortable: true, label: "Descrição" },
        { key: "tipo_servico", sortable: true, label: "Tipo Serviço" },
        { key: "data_solicitacao", sortable: true, label: "Solicitação" },
        { key: "data_vencimento", sortable: true, label: "Vencimento" },
        { key: "situacao", sortable: true, label: "Situação" },
        { key: "valor", sortable: true, label: "Valor" },
      ],
      exportFields: {
        Aluno: "aluno",
        Descrição: "descricao",
        'Tipo Serviço': "tipo_servico",     
        'Data Solicitação': {
          field: "data_solicitacao",
          callback: (value) => value != '' ? moment(value).format("DD/MM/YYYY") : '',
        },
        'Data Vencimento': {
          field: "data_vencimento",
          callback: (value) => value != '' ? moment(value).format("DD/MM/YYYY") : '',
        },
        Situação: { field: "situacao",
        callback: (value)  => (value == 'LIQ' ? 'Liquidado' : value == 'CAN' ? 'Cancelado' : value == 'PEN' ? 'Pendente' : value)    
        },
         Valor: "valor"
      },
    };
  },

  computed: {
    ...mapState("relatorioServicoSolicitado", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
    ...mapState("tipoItem", { listaTipoItemRequisicao: "lista" }),
    ...mapState("aluno", { listaAlunos: "lista" }),

    listaAlunosSelect: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(this.listaAlunos);
      },
    },

    listaTipoServico: {
      get() {
        return [
          { descricao: "Selecione", tipo: "" },
          ...this.listaTipoItemRequisicao,
        ];
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.$store.dispatch("tipoItem/listar");
  },

  methods: {
    dateToCompare: dateToCompare,
    ...mapActions("relatorioServicoSolicitado", ["listar"]),
    ...mapMutations("relatorioServicoSolicitado", [
      "SET_LISTA",
      "SET_PARAMETROS",
    ]),
    ...mapActions("aluno", { listarAlunos: "listar" }),

    setAluno(value) {
      this.filtros.aluno = value;
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
        aluno: form.aluno ? form.aluno.id : null,
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null,
        tipo_servico: form.tipo_servico ? form.tipo_servico.id : null,
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
.tabela-servico-solicitado >>> tr > th,
.tabela-servico-solicitado >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-servico-solicitado >>> table thead {
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
#tabela-servico-solicitado {
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
