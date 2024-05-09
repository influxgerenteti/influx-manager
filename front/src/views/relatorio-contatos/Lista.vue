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
                        :disabled="filtros.tipo_lead === 'A'"
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
                              :periodo="'mes_anterior'"
                              @dataDe="filtros.data_inicial = $event"
                              @dataAte="filtros.data_final = $event"
                            >
                            </g-data>
                          </div>
                        </div>
                      </div>
                    </b-col>

                    <b-col md="auto">
                      <label class="col-form-label">Opções de Impressão</label>
                      <b-form-radio-group
                        v-model="filtros.opcoesDeImpressao"
                        :options="opcoesImpressao"
                        name="opcoesDeImpressao"
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
                    :data="filtros.opcoesDeImpressao == 'true' ? resumo : geral"
                    :fields="
                      filtros.opcoesDeImpressao == 'true'
                        ? exportFieldsConsultor
                        : exportFieldsGeral
                    "
                    type="xls"
                    name="relatorio-contatos"
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
    <div class="tabela-contatos">
      <b-table
        small
        hover
        outlined
        bordered
        striped
        show-empty
        fixed-header
        sort-icon-right
        id="tabela-contatos"
        class="table-card-hover table-schedule"
        v-if="lista && !estaCarregando"
        :fields="filtros.opcoesDeImpressao ? fieldsConsultor : fieldsGeral"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="filtros.opcoesDeImpressao ? resumo : geral"
      >
        <template #cell(consultor)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>

        <template #cell(data_cadastro)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(retorno)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(efetivo)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(retornoEfetivo)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value + " %" }}
          </span>
        </template>

        <template #empty>
          <h4>Nenhum registro a ser exibido.</h4>
        </template>
      </b-table>
    </div>
    <div v-if="contatosAtivo">
      <h3>Contatos por tipo</h3>
      <b-table
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
  name: "ListaRelatorioContatos",

  data() {
    return {
      filtroVisivel: true,
      filtroAvancado: false,
      sortBy: "retorno",
      sortDesc: true,
      fields:[
        {key: "name", label:'Forma de Contato/Prospecção', sortable: true} ,
        {key: "count", label:'Total', sortable: true} ,
        {key:"lead", label:"Tipo de Contato", sortable: true}
      ],

      fieldsConsultor: [
        {
          key: "consultor",
          label: "Consultor",
          sortable: true,
          class: "no-break right-border font-weight-bold",
        },
        {
          key: "retorno",
          label: "Retornos Agendados",
          sortable: true,
          class: "no-break right-border",
        },
        {
          key: "efetivo",
          label: "Retornos Efetivos",
          sortable: true,
          class: "no-break right-border",
        },
        {
          key: "retornosEfetivo",
          label: "Retornos Efetivos %",
          sortable: true,
          class: "no-break right-border",
        },
      ],
      fieldsGeral: [
        {
          key: "data_cadastro",
          label: "Data",
          sortable: true,
          class: "no-break right-border font-weight-bold",
        },
        {
          key: "retorno",
          label: "Retornos Agendados",
          sortable: true,
          class: "no-break right-border",
        },
        {
          key: "efetivo",
          label: "Retornos Efetivos",
          sortable: true,
          class: "no-break right-border",
        },
        {
          key: "retornosEfetivo",
          label: "Retornos Efetivos %",
          sortable: true,
          class: "no-break right-border",
        },
      ],
      exportFieldsConsultor: {
        Consultor: "consultor",
        Retorno: "retorno",
        Efetivo: "efetivo",
        "Retorno Efetivo %": "retornosEfetivo",
      },
      exportFieldsGeral: {
        "Data Cadastro": "data_cadastro",
        Retorno: "retorno",
        Efetivo: "efetivo",
        "Retorno Efetivo %": "retornosEfetivo",
      },
      opcoesImpressao: [
        { text: "Consultor", value: true },
        { text: "Geral", value: false },
      ],
      tipoLeadOpcoes: [
        { text: "Ativo", value: "A" },
        { text: "Receptivo", value: "R" },
        { text: "Todos", value: null },
      ],
    };
  },

  computed: {
    ...mapState("relatorioContatos", [
      "filtros",
      "lista",
      "estaCarregando",
      "resumo",
      "geral",
      "contato",
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
    this.filtros.tipo_lead = null;
    this.listarItens();
    this.listarTipoContatos();
    this.limparSelect();
    this.SET_RESUMO([]);
    this.SET_GERAL([]);
    this.SET_LISTA([]);
    this.SET_CONTATO([]);
  },

  methods: {
    ...mapMutations("relatorioContatos", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "SET_RESUMO",
      "SET_GERAL",
      "SET_CONTATO",
    ]),
    ...mapActions("relatorioContatos", { listarContatos: "listar" }),
    ...mapActions("prospeccao", { listarItens: "listar" }),
    ...mapActions("tipoContato", { listarTipoContatos: "listar" }),

    converterSituacao(situacao) {
      const valores = {
        A: "Aberto",
        C: "Convertido",
        I: "Inativo",
        P: "Perdido",
      };
      return valores[situacao];
    },

    converterTipoContato(tipo_lead) {
      const valores = {
        A: "Ativo",
        R: "Receptivo",
      };
      return valores[tipo_lead];
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

    setTipoLead(value) {
      this.tipo_lead = value;
    },

    podeGerarRelatorio() {
      return true;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listarContatos();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        tipo_lead: form.tipo_lead ? form.tipo_lead : null,
        data_inicial: form.data_inicial ? form.data_inicial : null,
        data_final: form.data_final ? form.data_final : null,
        consultor: form.consultor ? form.consultor : null,
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
.tabela-contatos {
  max-height: 65vh;
  overflow: scroll;
}

#tabela-contatos >>> tr > th,
#tabela-contatos >>> tr > td,
.tabela-resumo >>> tr > td, .tabela-resumo >>> tr > th
 {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 2em;
}

#tabela-contatos .item-contatos {
  page-break-inside: avoid;
}

#tabela-contatos >>> thead {
  background-color: #fff !important;
}

@media print {
  #tabela-contatos {
    overflow: visible;
  }
  .tabela-contatos {
    overflow: hidden;
  }
}
</style>
