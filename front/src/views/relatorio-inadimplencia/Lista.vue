<template>
  <div class="animated fadeIn" style="max-height: 92vh;">
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
                    <b-col md="3">
                      <label
                        for="situacao_rapido"
                        class="col-form-label d-block"
                        >Situação do aluno</label
                      >
                      <b-form-checkbox-group
                        id="situacao_rapido"
                        v-model="filtros.situacao"
                        :options="situacoes"
                        buttons
                        button-variant="cinza"
                        class="checkbtn-line"
                        @change="setSituacao"
                        ref="situacao"
                      />
                    </b-col>
                    <b-col md="3">
                      <label
                        v-help-hint="'formulario-inadimplente_classificacao'"
                        :hidden="true"
                        class="col-form-label"
                        for="classificacao"
                        >Classificação :</label
                      >
                      <g-select
                        id="classificacao"
                        :hidden="true"
                        :value="filtros.classificacao"
                        :select="setClassificacao"
                        :options="listaClassificacaoAluno"
                        class="multiselect-truncate"
                        label="nome"
                        track-by="id"
                        ref="classificacao"
                      />
                    </b-col>
                  

                    <b-col md="3">
                      <label
                        :hidden="true"
                        v-help-hint="
                          'filtroAvancado-inadimplente_forma_cobranca'
                        "
                        for="forma-cobranca"
                        class="col-form-label"
                        >Forma Recebimento:</label
                      >
                      <g-select
                        id="forma-cobranca"
                        :hidden="true"
                        :value="filtros.forma_cobranca"
                        :select="setFormaCobranca"
                        :options="listaFormasPagamento"
                        class="multiselect-truncate"
                        label="descricao"
                        ref="recebimento"
                      />
                    </b-col>
                  </div>
                  
                  <div class="form-group row">
                    <b-col md="6">
                    <label
                        v-help-hint="
                          'filtroAvancado-inadimplente_data'
                        "
                        for="forma-cobranca"
                        class="col-form-label"
                        >Período por Vencimento:</label
                      >
                      
                      <g-data
                        :periodo="'mes_corrente'"
                        @dataDe="filtros.data_inicio = $event"
                        @dataAte="filtros.data_fim = $event"
                      />
                      </b-col>
                    </div>
                  <!-- <div class="row">
                    <div class="col-md-auto">
                      <label
                        v-help-hint="'formulario-inadimplente_exibicao'"
                        for="exibicao"
                        class="col-form-label d-block"
                        >Exibir alunos:</label
                      >
                      <b-form-group>
                        <b-form-radio-group
                          id="exibicao"
                          v-model="filtros.exibicao"
                          :options="listaTipo"
                          name="exibicao"
                          class="checkbtn-line"
                        />
                      </b-form-group>
                    </div>
                  </div> -->
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
                    name="relatorio-inadimplente"
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
      small
      hover
      striped
      show-empty
      sort-icon-right
      sticky-header="80vh"
      class="table-card-hover table-schedule tabela-inadimplencia"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="lista"
    >
      <template #cell(inadimplente_desde)="data">
        {{ data.value | formatarData }}
      </template>
      <template #cell(total_vencido)="data">
        {{ data.value | formatarMoeda }}
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

      <template #cell(detalhes)="data">
        <div v-if="data.item.detalhes">
          <a
            v-b-tooltip.viewport.left.hover
            class="icone-link"
            title="Exibir Detalhes"
            v-on:click="carregarDetalhes(data)"
          >
            <font-awesome-icon v-b-modal.modal-1 icon="file" />
          </a>
        </div>
      </template>
    </b-table>
    <div v-if="lista && !estaCarregando">
      <b-table
        small
        hover
        striped
        sticky-header="10vh"
        class="table-card-hover table-schedule tabela-inadimplencia-resumo"
        :fields="fieldsTotal"
        :items="resumo"
      >
        <template #cell(total_devedor)="data">
          {{ data.value | formatarMoeda }}
        </template>
      </b-table>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import moment from "moment";

export default {
  name: "ListaRelatorioInadimplencia",
  data() {
    return {
      situacoes: [
        { text: "Ativo", value: "ATI" },
        { text: "Inativo", value: "INA" },
        { text: "Trancado", value: "TRA" },
      ],
   
      filtroVisivel: true,
      exportFields: {
        Aluno: "aluno_id",
        Bairro: "bairro",
        "Celular Resp.": "celular_responsavel",
        Cep: "cep",
        Cidade: "cidade",
        "CPF/CNPJ": "cnpj_cpf",
        "CPF Responsavel": "cpf_responsavel",
        Endereço: "endereco",
        Telefone: "fone",
        "Fone comercial responsavel": "fone_comercial_responsavel",
        "Fone Responsável": "fone_responsavel",
        Idade: "Idade",
        "Inadimplente desde": {
          field: "inadimplente_desde",
          callback: (value) => moment(value).format("MM/DD/YYYY"),
        },
        Contato: "nome_contato",
        Responsável: "nome_responsavel",
        "Parentesco responsável": "parentesco_responsavel",
        Rg: "rg",
        Situação: "situacao",
        "Total vencido R$": {
          field: "total_vencido",
          callback: (value) => `R$ ${value.replace(".", ",")}`,
        },
      },
      fields: [
        { key: "nome_contato", sortable: true, label: "Contato" },
        { key: "nome_responsavel", sortable: true, label: "Responsável" },
        {
          key: "parentesco_responsavel",
          sortable: true,
          label: "Parentesco Responsável",
        },
        { key: "fone", label: "Telefone", sortable: true },
        {
          key: "fone_responsavel",
          label: "Telefone Responsável",
          sortable: true,
        },
        {
          key: "inadimplente_desde",
          label: "Data Inadimplência",
          sortable: true,
        },
        { key: "total_vencido", label: "Total", sortable: true },
      ],
      fieldsTotal: [
        {
          key: "total_inadimplentes",
          label: "Total de Inadimplentes",
        },
        { key: "total_devedor", label: "Total Devedor" },
      ],
      sortDesc: false,
      sortBy: "nome_contato",
    };
  },

  computed: {
    ...mapState("relatorioInadimplencia", [
      "filtros",
      "lista",
      "resumo",
      "estaCarregando",
    ]),
    ...mapState("classificacaoAlunos", {
      listaClassificacaoAlunoRequisicao: "listaClassificacaoAluno",
    }),
    ...mapState("tipoOcorrencia", { listaTipoOcorrenciaRequisicao: "lista" }),

    listaFormasPagamento: {
      get() {
        return [{ id: null, descricao: "Selecione" }].concat(
          this.$store.state.formaPagamento.lista
        );
      },
    },

    listaClassificacaoAluno: {
      get() {
        return [
          { id: null, nome: "Selecione" },
          ...this.listaClassificacaoAlunoRequisicao,
        ];
      },
    },
    
  },

  mounted() {
    this.SET_LISTA([]);
    this.SET_RESUMO([]);
    this.listarCamposSelects();
  },

  methods: {
    ...mapActions("relatorioInadimplencia", ["listar"]),
    ...mapMutations("relatorioInadimplencia", [
      "SET_LISTA",
      "SET_RESUMO",
      "SET_PARAMETROS",
    ]),

 

    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
      return true;
    },

    listarCamposSelects() {
      this.$store.commit("classificacaoAlunos/SET_PAGINA_ATUAL", 1);
      this.$store.dispatch("classificacaoAlunos/getListaClassificacaoAluno");

      this.$store.commit("formaPagamento/SET_PAGINA_ATUAL", 1);
      this.$store.dispatch("formaPagamento/getLista");
    },

    setClassificacao(value) {
      this.filtros.classificacao = value.id === null ? null : value;
    },

    setFormaCobranca(value) {
      this.filtros.forma_cobranca = value;
    },

    setSituacao(value) {
      this.situacao = value;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        situacao: form.situacao.length > 0 ? form.situacao : null,
        classificacao_aluno: form.classificacao ? form.classificacao.id : null,
        forma_cobranca: form.forma_cobranca ? form.forma_cobranca.id : null,
        data_inicio: form.data_inicio || null,
        data_fim: form.data_fim || null
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
.tabela-inadimplencia >>> tr th,
.tabela-inadimplencia >>> tr td {
  vertical-align: middle;
  text-align: center;
  display: table-cell !important;
}
.tabela-inadimplencia-resumo >>> tr th,
.tabela-inadimplencia-resumo >>> tr td {
  vertical-align: middle;
  text-align: center;
  display: table-cell !important;
}

.tabela-inadimplencia .item-inadimplencia {
  page-break-inside: avoid;
}
.tabela-inadimplencia-resumo {
  page-break-inside: avoid;
}

.tabela-inadimplencia .tabela-inadimplencia-resumo >>> thead {
  background-color: #fff !important;
  position: sticky;
  top: -1px;
}

@media print {
  .tabela-inadimplencia {
    overflow: visible;
  }
  .visible-md-block e .visible-lg-block {
    display: none !important;
  }
  .tabela-inadimplencia {
    overflow: hidden;
  }

  .hide-on-print {
    display: none !important;
  }
}
</style>
