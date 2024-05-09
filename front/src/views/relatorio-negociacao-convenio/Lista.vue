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
                    <div class="col-md-4">
                      <label for="situacaoConvenio" class="col-form-label"
                        >Situação</label
                      >
                      <g-select
                        id="situacaoConvenio"
                        v-model="filtros.situacao"
                        :options="listaDeSituacao"
                        class="multiselect-truncate valid-input"
                        label="descricao"
                        track-by="id"
                      />
                    </div>

                    <div class="col-md-4">
                      <label for="segmento_empresa" class="col-form-label"
                        >Segmento</label
                      >
                      <g-select
                        id="segmento_empresa"
                        v-model="filtros.segmento"
                        :options="listaSegmentoEmpresa"
                        class="multiselect-truncate valid-input"
                        label="descricao"
                        track-by="id"
                      />
                    </div>
                    <b-col>
                      <div class="col-md-4">
                        <label for="abrangencia_filtro" class="col-form-label"
                          >Abrangência</label
                        >
                        <div>
                          <b-form-checkbox-group
                            id="abrangencia_filtro"
                            v-model="filtros.abrangencia"
                            :options="abrangenciaOpcoes"
                            buttons
                            button-variant="cinza"
                            name="abrangencia_filtro"
                            class="checkbtn-line"
                          />
                        </div>
                      </div>
                    </b-col>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 mt-5 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-negocicao-convenio"
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
        id="tabela-negocicao-convenio"
        class="tabela-negocicao-convenio"
        v-if="lista && !estaCarregando"
        :fields="tableFields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(situacao)="data">
          <span v-b-tooltip.top :title="situacaoValue(data)">
            {{ situacaoValue(data) }}
          </span>
        </template>
        <template #cell(abrangencia_nacional)="data">
          <span v-b-tooltip.top :title="data.value == true ? 'Nacional' : 'Estadual'">
            {{ data.value == true ? 'Nacional' : 'Estadual' }}
          </span>
        </template>
        <template #cell(descricao)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value }}
          </span>
        </template>
        <template #cell(data_ultimo_contato)="data">
          <span v-b-tooltip.top :title="data.value | formatarData">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(motivo_nao_fechamento_convenio)="data">
          {{ data.value ? data.value : '--' }}
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
  name: "ListaRelatorioNegociacaoConvenio",
  data() {
    return {
      filtroVisivel: true,
      abrangenciaOpcoes: [
        { text: "Nacional", value: 1 },
        { text: "Estadual", value: 0 },
      ],
      sortBy: "nome_turma",
      sortDesc: false,
      tableFields: [
        { key: "nome_fantasia", sortable: true, label:'Nome Fantasia'},
        { key: "situacao", sortable: true, label: 'Situação'},
        { key: "abrangencia_nacional", sortable: true, label: 'Abrangência' },
        { key: "descricao", sortable: true, label: 'Segmento' },
        { key: "consultor_responsavel", sortable: true, label: 'Consultor' },
        { key: "data_ultimo_contato", sortable: true, label: 'Último contato' },
        { key: "ultimo_consultor_contato", sortable: true, label: 'Último consultor' },
        { key: "motivo_nao_fechamento_convenio", sortable: true, label: 'Motivo' },
      ],
      exportFields: {
          'Nome Fantasia' : 'nome_fantasia',
          'Situação' : {
            field: 'situacao',
            callback: (value) => value == 'ATI' ? 'Ativo' :
            value == 'EN' ? 'Em negociação' : 
            value == 'PNR' ? ' Parceria Não Realizada' : 
            value == 'PV' ? 'Pendnete de Aprovação' : 
            value == "NE" ? 'Negado pela franqueadora' : 
            value == 'I' ? 'Inativo' : 
            value 
          },
          'Abrangência': {
            field: 'abrangencia_nacional',
            callback: (value) => value == true || value == 'VERDADEIRO' ? 'Nacional' : 'Estadual' 
          }, 
          'Segmento' : 'descricao',
          'Consultor' : 'consultor_responsavel',
          'Último contato' : 'data_ultimo_contato',
          'Último consultor' : 'ultimo_consultor_contato',
          'Motivo' : {
            field: 'motivo_nao_fechamento_convenio',
            callback: (value) => value ? value : '--' 
          },
      },
    };
  },

  computed: {
    ...mapState("relatorioNegociacaoConvenio", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
    ...mapState("negociacaoParceriaWorkflow", {
      listaNegociacaoParceriaWorkflow: "lista",
    }),
    ...mapState("segmentoEmpresaConvenio", { listaEmpresaConvenio: "lista" }),

    listaDeSituacao: {
      get() {
        return [
          { id: null, descricao: "Selecione", tipo_workflow: null },
          ...this.listaNegociacaoParceriaWorkflow,
        ];
      },
    },
    listaSegmentoEmpresa: {
      get() {
        return [
          { id: null, descricao: "Selecione" },
          ...this.listaEmpresaConvenio,
        ];
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.listarNegociacaoParceriaWorkflow();
    this.listarEmpresasConvenio();
  },

  methods: {
    ...mapActions("relatorioNegociacaoConvenio", ["listar"]),
    ...mapMutations("relatorioNegociacaoConvenio", [
      "SET_LISTA",
      "SET_PARAMETROS",
    ]),
    ...mapActions("negociacaoParceriaWorkflow", {
      listarNegociacaoParceriaWorkflow: "listar",
    }),
    ...mapActions("segmentoEmpresaConvenio", {
      listarEmpresasConvenio: "listar",
    }),
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

      console.log(this.filtros);
      const dados = {
        situacao: form.situacao ? form.situacao.tipo_workflow : null,
        segmento_empresa_convenio: form.segmento ? form.segmento.id : null,
        abrangencia_nacional: form.abrangencia ? form.abrangencia : null,
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

    situacaoValue(data){
      return data.value == 'ATI' ? 'Ativo' :
      data.value == 'EN' ? 'Em negociação' : 
      data.value == 'PNR' ? ' Parceria Não Realizada' : 
      data.value == 'PV' ? 'Pendnete de Aprovação' : 
      data.value == "NE" ? 'Negado pela franqueadora' : 
      data.value == 'I' ? 'Inativo' : 
      data.value 
    }
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
.tabela-negocicao-convenio >>> tr > th,
.tabela-negocicao-convenio >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-negocicao-convenio >>> table thead {
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
#tabela-negocicao-convenio {
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
