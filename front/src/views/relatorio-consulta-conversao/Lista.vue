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
            @click="filtroAvancado = !filtroAvancado"
            active
          >
            <b-card-text>
              <div class="filtroAvancado">
                <b-collapse
                  id="collapse-4"
                  v-model="filtroAvancado"
                  class="mt-2"
                >
                  <div class="row">
                    <div class="col-md-auto">
                      <label
                        for="tipo_lead_filtro_rapido"
                        class="col-form-label"
                        >Tipo de contato</label
                      >
                      <div class="row">
                        <div class="col-md-3" @change="limparSelect()">
                          <b-form-radio-group
                            id="tipo_lead_filtro_rapido"
                            v-model="filtros.tipo_lead"
                            :options="tipoLeadOpcoes"
                            buttons
                            button-variant="cinza"
                            class="checkbtn-line"
                            name="tipo_lead_filtro_rapido"
                          />
                        </div>
                        <div></div>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <label for="prospeccao" class="col-form-label"
                        >Tipo de Prospecção</label
                      >
                      <g-select
                        id="id"
                        v-model="filtros.tipo_prospeccao"
                        :options="listaProspeccaoSelect"
                        :disabled="filtros.tipo_lead === 'R'"
                        label="descricao"
                        track-by="id"
                      />
                    </div>

                    <div class="col-md-3">
                      <label for="contato" class="col-form-label"
                        >Forma de Contato</label
                      >
                      <g-select
                        id="id"
                        v-model="filtros.tipo_contato"
                        :options="listaTipoContatoSelect"
                       
                        label="nome"
                        track-by="id"
                      />
                    </div>

                    <b-col class="col-md-4">
                      <label for="licao" class="col-form-label">
                        Consultor
                      </label>
                      <g-select-consultor
                        id="consultor"
                        v-model="filtros.consultor"
                      >
                      </g-select-consultor>
                    </b-col>
                  </div>

                  <div class="form-group row">
                    <b-col class="col-md-3">
                      <label
                        v-help-hint="'filtro_avancado'"
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <div class="row">
                        <div class="col">
                          <div class="input-group">
                            <g-data
                              :periodo="'dia_atual'"
                              @dataDe="filtros.data_inicial = $event"
                              @dataAte="filtros.data_final = $event"
                            >
                            </g-data>
                          </div>
                        </div>
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
                    :fields="fieldsConsultaConversao"
                    type="xls"
                    name="relatorio-consulta-conversao"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar Resumo
                  </g-excel>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    type="xls"
                    :data="contatosAtivo"
                    :fields="fieldsResumo"
                    name="relatorio-resumo"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar Relatório Excel
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
    <div id="tabelaConversao" class="tabela-consulta-conversao">
      <div id="tabela-consulta-conversao">
        <b-table
          small
          hover
          outlined
          bordered
          striped
          show-empty
          sort-icon-right
          class="table-card-hover table-schedule"
          v-if="lista && !estaCarregando"
          :fields="filtros.tipo_lead === 'R' ? fieldsReceptivo : fieldsAtivo"
          :sort-by.sync="sortBy"
          :sort-desc.sync="sortDesc"
          :items="lista"
        >
          <template #cell(data_cadastro)="data">
            <span v-b-tooltip :title="data.value">
              {{ data.value | formatarData }}
            </span>
          </template>
          <template #cell(tipo_lead)="data">
            <span>{{ converterTipoLead(data.value) }}</span>
          </template>
          <template #cell(situacao)="data">
            <span>{{ converterSituacao(data.value) }}</span>
          </template>
        </b-table>
      </div>
    </div>
    <h3 v-if="contatosAtivo">Contatos por tipo - Resumo</h3>
    <div v-if="contatosAtivo" id="tabelaResumo">
       <b-table
        id="tabela-resumo"
        :fields="fields"
        class="tabela-resumo"
        :items="contatosAtivo"
        small
        hover
        outlined
        striped
        sort-icon-right
      >
      </b-table>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "ListaRelatorioConsultaConversao",
  data() {
    return {
      isActive: false,
      sortBy: "interessado",
      sortDesc: false,
      filtroVisivel: true,
      filtroAvancado: false,
      exportFields: {},
      tipoContato: [],
      tipoLeadOpcoes: [
        { text: "Ativo", value: "A" },
        { text: "Receptivo", value: "R" },
        { text: "Todos", value: null },
      ],
      fields: [
        { key: "consultor", label: "Consultor", sortable: true },
        { key: "lead", label: "Tipo de Contato", sortable: true },
        { key: "name", label: "Forma de Contato/Prospecção", sortable: true },
        { key: "total", label: "Total", sortable: true },
        { key: "conversaoTotal", label: "Conversao", sortable: true },
      ],
      fieldsAtivo: [
        { key: "interessado", sortable: true },
        { key: "conversao", sortable: true, label: "Conversão" },
        { key: "situacao", sortable: true, label: "Situação" },
        { key: "tipo_lead", sortable: true },
        { key: "data_cadastro", sortable: true },
        { key: "consultor", sortable: true },
        { key: "prospeccao", sortable: true, label: "Prospecção" },
        { key: "prospeccao_pai", sortable: true, label: "Prospecção Pai" },
        { key: "contato", sortable: true, label: "Contato" },
      ],
      fieldsReceptivo: [
        { key: "interessado", sortable: true },
        { key: "conversao", sortable: true, label: "Conversão" },
        { key: "situacao", sortable: true, label: "Situação" },
        { key: "tipo_lead", sortable: true },
        { key: "data_cadastro", sortable: true },
        { key: "consultor", sortable: true },
        { key: "prospeccao", sortable: true, label: "Prospecção" },
        { key: "contato", sortable: true, label: "Contato" },
      ],
      fieldsResumo: {
        Consultor: "consultor",
        "Tipo Lead": "lead",
        "Forma Contato/Prospecção": "name",
        Total: "total",
        Conversão: "conversaoTotal",
      },

      fieldsConsultaConversao: {
        Interessado: "interessado",
        Conversão: "conversao",
        Situação: {
          field: "situacao",
          callback: (value) =>
            value == "A"
              ? "Aberto"
              : value == "C"
              ? "Convertido"
              : value == "I"
              ? "Inativo"
              : value == "P"
              ? "Perdido"
              : "",
        },
        "Tipo Lead": {
          field: "tipo_lead",
          callback: (value) => (value == "A" ? "Ativo" : "Receptivo"),
        },
        "Data Cadastro": "data_cadastro",
        Consultor: "consultor",
        Prospecção: "prospeccao",
        "Prospecção Pai": "prospeccao_pai",
        Contato: "contato",
      },
    };
  },

  computed: {
    ...mapState("relatorioConsultaConversao", [
      "filtros",
      "lista",
      "estaCarregando",
      "consultor",
      "contatos",
      "contatosAtivo",
      "contatosReceptivo",
    ]),
    ...mapState("prospeccao", { listaProspeccao: "lista" }),
    ...mapState("tipoContato", { listaTipoContatos: "lista" }),

    listaProspeccaoSelect: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.listaProspeccao
        );
      },
    },
    listaTipoContatoSelect: {
      get() {
        return [{ nome: "Selecione", id: null }].concat(this.listaTipoContatos);
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.listarItens();
    this.listarTipoContatos();
    this.listaConsultaConversao();
  },

  methods: {
    ...mapActions("relatorioConsultaConversao", {
      listaConsultaConversao: "listar",
    }),
    ...mapMutations("relatorioConsultaConversao", [
      "SET_LISTA",
      "SET_PARAMETROS",
    ]),
    ...mapActions("prospeccao", { listarItens: "listar" }),
    ...mapActions("tipoContato", { listarTipoContatos: "listar" }),


    podeGerarRelatorio() {
      return true;
    },

    limparSelect() {
      if (this.filtros.tipo_lead === "R") {
        this.filtros.tipo_prospeccao = null;
      } else if (this.filtros.tipo_lead === "A") {
        {
          this.filtros.tipo_contato = null;
        }
      }
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listaConsultaConversao();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null,
        consultor: form.consultor ? form.consultor : null,
        tipo_lead: form.tipo_lead ? form.tipo_lead : null,
        tipo_prospeccao: form.tipo_prospeccao ? form.tipo_prospeccao.id : null,
        tipo_contato: form.tipo_contato ? form.tipo_contato.id : null,
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

    converterTipoLead(tipo_lead) {
      const valores = {
        A: "Ativo",
        R: "Receptivo",
      };
      return valores[tipo_lead];
    },
    converterSituacao(situacao) {
      const valores = {
        A: "Aberto",
        C: "Convertido",
        I: "Inativo",
        P: "Perdido",
      };
      return valores[situacao];
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

#tabela-consulta-conversao >>> tr > th,
#tabela-consulta-conversao >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell !important;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 2em;
}
#tabelaConversao {
  max-height: 60vh;
  overflow: auto;
}

.tabela-resumo >>> tr > td,
.tabela-resumo >>> tr > th {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 2em;
}

#tabelaResumo {
  max-height: 25vh;
  overflow: auto;
}

#tabela-consulta-conversao .item-consulta-conversao {
  page-break-inside: avoid;
}

#tabela-consulta-conversao >>> thead {
  background-color: #fff !important;
}
#tabela-resumo >>> thead {
  background-color: #fff !important;
}


@media print {
  #tabela-consulta-conversao {
    overflow: visible;
  }
  .tabela-consulta-conversao {
    overflow: hidden;
  }
  .no-print {
    display: none !important;
  }
}
</style>
