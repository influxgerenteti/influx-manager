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
                  <div class="form-group d-md-flex">
                    <div class="col-md-6">
                      <label class="col-form-label" for="periodo">Período</label>
                      <g-data
                        :id="'periodo'"
                        :periodo="'mes_anterior'"
                        :labelAte="'Até'"
                        :labelDe="'De'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        for="forma_contato_filtro_rapido"
                        class="col-form-label"
                        >Tipo de Prospecção <span class="info" v-b-tooltip.top title="Selecione o 'Tipo de Contato' para liberar esta ação"><em>i</em></span></label
                      >
                      <g-select
                        id="forma_contato_filtro_rapido"
                        v-model="filtros.tipo_prospecao"
                        :options="listaFormaContato"
                        :disabled="
                          listaFormaContato.length === 0 ||
                          tipoLead.length === 2
                        "
                        :label="tipoLead[0] === 'A' ? 'descricao' : 'nome'"
                        class="multiselect-truncate"
                        track-by="id"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        for="etapa_do_funil_filtro_rapido"
                        class="col-form-label"
                        >Workflow</label
                      >
                      <g-select
                        id="etapa_do_funil_filtro_rapido"
                        v-model="filtros.subtipo_prospeccao"
                        :options="listaWorkflow"
                        class="multiselect-truncate valid-input"
                        label="descricao"
                        track-by="id"
                      />
                    </div>
                  </div>
                  <div class="form group d-md-flex">
                    <div class="col-md-auto">
                      <label for="grau_interesse" class="col-form-label"
                        >Situação (Interessado)</label
                      >
                      <div class="d-block">
                        <b-form-checkbox-group
                          id="grau_interesse"
                          v-model="filtros.grauInteresseSelecionado"
                          :options="opcoesGrausInteresse"
                          buttons
                          button-variant="cinza"
                          class="checkbtn-line"
                          name="grau-interesse"
                          @change="setGrauInteresse"
                        />
                      </div>
                    </div>

                    <div class="col-md-auto">
                      <label
                        for="tipo_contato_filtro_rapido"
                        class="col-form-label"
                        >Tipo de contato</label
                      >
                      <div class="d-block">
                        <b-form-radio-group
                          id="tipo_contato_filtro_rapido"
                          v-model="filtros.tipo_contato"
                          :options="tipoContatoOpcoes"
                          buttons
                          button-variant="cinza"
                          class="checkbtn-line"
                          name="tipo_filtro_rapido"
                          @change="setTipoLead"
                        />
                      </div>
                    </div>
                  </div>
                    <div class="form-group d-md-flex">
                    <div class="col-md-3 mt-3">
                      <b-form-group label="Opções de impressão">
                        <b-form-radio
                          v-for="option in options"
                          v-model="selectedCheck"
                          :key="option.value"
                          :value="option.value"
                        >
                          {{ option.text }}
                        </b-form-radio>
                      </b-form-group>
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
                    :data="selectedCheck == 'leads' ? lista : contatosAtivo"
                    :fields="
                      selectedCheck == 'leads'
                        ? exportFieldsLead
                        : exportFieldsConsultor
                    "
                    type="xls"
                    name="relatorio-prospeccao"
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
      id="tabela-prospeccao"
      class="tabela-prospeccao"
      v-if="lista && !estaCarregando"
      :fields="
        selectedCheck == 'leads'
          ? fieldsLead
          : selectedCheck == 'por_consultor'
          ? fieldsConsultor
          : []
      "
      :items="selectedCheck == 'leads' ? lista : contatosAtivo"
    >
      <template #cell(tipo_lead)="data">
        <span><b>Lead:</b> {{data.value ? (data.value == 'R' ? 'Receptivo' : data.value == 'A' ? 'Ativo' : data.value) : '--'}}</span><br>
        <span v-if="data.value == 'A'"><b>Prospecção:</b> {{data.item.nome_prospeccao ? data.item.nome_prospeccao : "--"}}</span>
        <span v-if="data.value == 'R'"><b>Contato:</b> {{data.item.tipo_contato ? data.item.tipo_contato : "--"}}</span><br>
        <span><b>Workflow:</b> {{data.item.nome_workflow ? data.item.nome_workflow : '--'}}</span>
      </template>
      <template #cell(data_cadastro)="data">
        {{ data.value | formatarData }}
      </template>
      <template #cell(telefone_contato)="data">
        <span><b>Nome:</b> {{ data.item.nome_contato }}</span><br>
        <span><b>Fone:</b> {{ data.value }}</span><br>
        <span> <b>E-mail:</b> {{ data.item.email_contato ? data.item.email_contato : '--' }}</span>
      </template>
      <template #cell(idade)="data">
        <div>
          {{ data.value ? data.value : "--" }}
        </div>
      </template> 

      <!-- <template #cell(tipo_lead)="data">
        {{ converterLead(data.value) }}
      </template> -->
      <template #cell(grau_interesse)="data">
        {{ converterInteresse(data.value) }}
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
  name: "ListaRelatorioProspeccao",
  data() {
    return {
      aviso: "",
      data_inicial: "",
      data_final: "",
      filtroVisivel: true,
      tipoLead: [],
      situacaoAlunoSelecionado: [],
      selectedCheck: "por_consultor",
      options: [
        { text: "Por Consultor", value: "por_consultor" },
        { text: "Leads", value: "leads" },
      ],
      tipoContatoOpcoes: [
        { text: "Ativo", value: "A" },
        { text: "Receptivo", value: "R" },
      ],
      opcoesGrausInteresse: [
        { value: "L", text: "Lead" },
        { value: "I", text: "Interessado" },
        { value: "H", text: "Hotlist" },
      ],
      fieldsLead: [
        { key: "tipo_lead", label: "Tipo", sortable: true },
        { key: "telefone_contato", label: "Contato", sortable: true },
        { key: "data_cadastro", label: "Cadastro", sortable: true },
        { key: "grau_interesse", label: "Interesse", sortable: true },
        { key: "nome", label: "Nome", sortable: true },
        { key: "idade", label: "Idade", sortable: true },
      ],
      fieldsConsultor: [
        { key: "nome_contato", label: "Consultor", sortable: true },
        { key: "total", label: "Quantidade", sortable: true },
        { key: "conversaoTotal", label: "Total Lead", sortable: true },
        { key: "lead", label: "Tipo Lead", sortable: true },
      ],
      exportFieldsLead: {
        "Tipo Lead": {
          field: "tipo_lead",
          callback: (value) =>
            value == "A" ? "Ativo" : value == "R" ? "Receptivo" : "--",
        },
        "Forma Contato": "tipo_contato",
        Workflow: "nome_workflow",
        "Nome Prospecção": "nome_prospeccao",
        "Data Cadastro": "data_cadastro",
        "E-mail": "email_contato",
        "Grau de Interesse": {
          field: "grau_interesse",
          callback: (value) =>
            value == "L" ? "Lead" : value == "H" ? "Hotlist" : "Interessado",
        },
        Idade: "idade",
        Nome: "nome",
        Consultor: "nome_contato",
        Telefone: "telefone_contato",
      },
      exportFieldsConsultor: {
        Consultor: "nome_contato",
        Quantidade: "total",
        "Total Lead": "conversaoTotal",
        Lead: "lead",
      },
    };
  },

  computed: {
    ...mapState("relatorioProspeccao", [
      "filtros",
      "lista",
      "estaCarregando",
      "resumo",
      "geral",
      "contato",
      "temp",
      "contatosAtivo",
      "contatosReceptivo",
    ]),
    ...mapState("tipoContato", { listaTipoContatoReceptivo: "lista" }),
    ...mapState("prospeccao", { listaTipoContatoAtivo: "lista" }),
    ...mapState("workflow", { workflowRequisicao: "lista" }),

    listaWorkflow: {
      get() {
        return [
          { id: null, descricao: "Selecione" },
          ...this.workflowRequisicao,
        ];
      },
    },

    listaFormaContato() {
      if (this.tipoLead[0] === "A") {
        return [
          { id: null, descricao: "Selecione" },
          ...this.listaTipoContatoAtivo,
        ];
      }

      if (this.tipoLead[0] === "R") {
        return [
          { id: null, nome: "Selecione" },
          ...this.listaTipoContatoReceptivo,
        ];
      }

      return [];
    },
  },

  mounted() {
    this.SET_LISTA([]);

    this.listarCamposSelects();
    this.SET_CONTATO([]);
  },

  methods: {
    ...mapActions("interessados", { listarItems: "listar" }),
    ...mapActions("relatorioProspeccao", ["listar"]),
    ...mapMutations("relatorioProspeccao", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "SET_CONTATO",
    ]),

    podeGerarRelatorio() {
      return true;
    },

    listarCamposSelects() {
      this.$store.commit("tipoContato/SET_PAGINA_ATUAL", 1);
      this.$store.commit("tipoContato/SET_LISTA", []);
      this.$store.dispatch("tipoContato/listar");

      this.$store.commit("prospeccao/SET_PAGINA_ATUAL", 1);
      this.$store.commit("prospeccao/SET_LISTA", []);
      this.$store.dispatch("prospeccao/listar");

      this.$store.commit("workflow/SET_PAGINA_ATUAL", 1);
      this.$store.commit("workflow/SET_LISTA", []);
      this.$store.dispatch("workflow/listar");
    },

    setTipoLead(value) {
      this.tipoLead = value;
    },

    setGrauInteresse(value) {
      this.grauInteresseSelecionado = value;
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
    converterInteresse(grau_interesse) {
      const valores = {
        L: "Lead",
        I: "Interessado",
        H: "Hotlist",
      };
      return valores[grau_interesse];
    },

    converterLead(tipo_lead) {
      const valores = {
        A: "Ativo",
        R: "Receptivo",
      };
      return valores[tipo_lead];
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
        grau_interesse: form.grauInteresseSelecionado
          ? form.grauInteresseSelecionado
          : null,
        tipo_contato: form.tipo_contato ? form.tipo_contato : null,
        tipo_prospeccao: form.tipo_prospecao ? form.tipo_prospecao.id : null,
        workflow: form.subtipo_prospeccao ? form.subtipo_prospeccao.id : null,
        detalhar_consultor: form.detalhar_consultor === true ? 1 : 0,
        listar_leads: form.listar_leads === true ? 1 : 0,
        convenio: form.convenio ? form.convenio.id : null,
        tipo_contato: form.tipo_contato ? form.tipo_contato : null,
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
  converterTipoContato(tipo_lead) {
    const valores = {
      A: "Ativo",
      R: "Receptivo",
    };
    return valores[tipo_lead];
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
.tabela-prospeccao >>> tr > th,
.tabela-prospeccao >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-prospeccao >>> table thead {
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
#tabela-prospeccao {
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
