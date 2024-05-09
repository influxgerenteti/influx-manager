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
                    <b-col md="3">
                      <label
                        v-help-hint="'filtro-relatorio-cheques_tipo'"
                        for="tipo"
                        class="col-form-label d-block"
                        >Situação</label
                      >
                      <b-form-checkbox-group
                        id="tipo"
                        v-model="filtros.situacao"
                        :options="listaTipo"
                        buttons
                        button-variant="cinza"
                        name="tipo"
                        class="checkbtn-line"
                      />
                    </b-col>

                    <b-col md="3">
                      <label
                        v-help-hint="'filtroAvancado-ocorrencia_'"
                        for="filtro_avancado_tipo_ocorrencia"
                        class="col-form-label"
                        >Tipo ocorrência</label
                      >
                      <g-select
                        id="tipo_ocorrencia"
                        v-model="filtros.tipo"
                        :options="listaTipoOcorrencia"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </b-col>

                    <b-col md="3">
                      <label
                        v-help-hint="
                          'filtroAvancado-ocorrencia_academica_responsavel'
                        "
                        for="filtro_avancado_responsavel"
                        class="col-form-label"
                        >Responsável</label
                      >
                      <g-select
                        id="responsavel"
                        v-model="filtros.funcionario_responsavel"
                        :options="listaFuncionario"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
                      />
                    </b-col>

                    <b-col md="3">
                      <label
                        v-help-hint="
                          'filtroAvancado-relatorio-ocorrencia-aluno'
                        "
                        for="nome_aluno"
                        class="col-form-label"
                        >Aluno</label
                      >
                      <typeahead
                        id="nome_aluno"
                        :item-hit="setAluno"
                        v-model="filtros.aluno"
                        source-path="/api/aluno/buscar-nome"
                        key-name="pessoa.nome_contato"
                      />
                    </b-col>

                    <b-col md="6">
                      <label
                        v-help-hint="'filtro_avancado_relatorio_data'"
                        for="data_inicial"
                        class="col-form-label"
                        >Data de criação</label
                      >
                      <g-data
                        :periodo="'mes_anterior'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                      <div v-if="aviso" class="floating-message bg-danger">
                        {{ aviso }}
                      </div>
                    </b-col>
                    <b-col md="6">
                      <label
                        v-help-hint="'filtro_avancado_relatorio_data'"
                        for="data_inicial"
                        class="col-form-label"
                        >Data Próximo Contato</label
                      >
                      <div class="row">
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">De</div>
                            </div>
                            <g-datepicker
                              v-model="data_inicial_contato"
                              :element-id="'data_inicial_contato'"
                              :value="filtros.data_inicial_contato"
                              :selected="setPeriodoContatoDe"
                            />
                          </div>
                        </div>
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">à</div>
                            </div>
                            <g-datepicker
                              v-model="data_final_contato"
                              :element-id="'data_final_contato'"
                              :value="filtros.data_final_contato"
                              :selected="setPeriodoContatoAte"
                            />
                          </div>
                        </div>
                      </div>
                      <div v-if="aviso" class="floating-message bg-danger">
                        {{ aviso }}
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
                    name="relatorio-ocorrencia"
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
      class="tabela-ocorrencia"
      id="tabela-ocorrencia"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="lista"
    >
      <template #empty>
        <h4>Nenhum registro a ser exibido.</h4>
      </template>

      <template #table-busy>
        <div class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Carregando Dados...</strong>
        </div>
      </template>

      <template #cell(data_criacao)="data">
        {{ data.value | formatarData }}
      </template>
      <template #cell(data_conclusao)="data">
        {{ data.value | formatarData }}
      </template>
  
      <template #cell(detalhes)="data">
        <div v-if="data.item.detalhes" >
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
  </div>
    <b-modal id="modal-1" title="Detalhes" ok-only>
      <p class="my-4">{{ this.listaDetalhe }}</p>
    </b-modal>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToString, dateToCompare } from "../../utils/date";

export default {
  name: "ListaRelatorioOcorrencia",
  data() {
    return {
      listaDetalhe: "",
      sortBy: "aluno",
      sortDesc: false,
      filtroVisivel: true,
      listaTipo: [
        { text: "Aberto", value: "A" },
        { text: "Resolvido", value: "F" },
      ],
      funcionario: null,
      funcionario_responsavel: null,
      data_final_contato: "",
      data_inicial_contato: "",
      aviso: "",
      exportFields: {
        Aluno: "aluno",
        Funcionario: "funcionario",
        Categoria: "categoria",
        "Sub Categoria": "sub_categoria",
        "Data Criação": "data_criacao",
        "Data fechamento": "data_conclusao",
        Detalhes: "detalhes",
      },
      fields: [
        { key: "aluno", sortable: true, label: "Aluno" },
        { key: "funcionario", sortable: true, label: "Funcionario" },
        { key: "categoria", label: "Categoria", sortable: true },
        { key: "sub_categoria", label: "Sub Categoria", sortable: true },
        { key: "data_criacao", label: "Data Abertura", sortable: true },
        { key: "data_conclusao", label: "Data Fechamento", sortable: true },
        { key: "detalhes", label: "Detalhes", sortable: true, class:"no-print" },
      ],
      semestre: ""
    };
  },

  computed: {
    ...mapState("relatorioOcorrencia", ["filtros", "lista", "estaCarregando"]),
    ...mapState("tipoOcorrencia", { listaTipoOcorrenciaRequisicao: "lista" }),
    ...mapState("funcionario", { listaFuncionarioRequisicao: "lista" }),
    listaFuncionario: {
      get() {
        return [
          { id: null, apelido: "Selecione" },
          ...this.listaFuncionarioRequisicao,
        ];
      },
    },

    listaTipoOcorrencia: {
      get() {
        return [
          { id: null, descricao: "Selecione" },
          ...this.listaTipoOcorrenciaRequisicao,
        ];
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.listarFuncionario();
    this.listarTipoOcorrencia();
  },

  methods: {
    dateToString: dateToString,
    ...mapActions("relatorioOcorrencia", ["listar"]),
    ...mapMutations("relatorioOcorrencia", ["SET_LISTA", "SET_PARAMETROS"]),
    ...mapActions("funcionario", { listarFuncionario: "listar" }),
    ...mapMutations("funcionario", {
      SET_PAGINA_ATUAL_FUNCIONARIO: "SET_PAGINA_ATUAL",
      SET_LISTA_FUNCIONARIO: "SET_LISTA",
    }),
    ...mapActions("tipoOcorrencia", { listarTipoOcorrencia: "listar" }),

    carregarDetalhes(data) {
      this.listaDetalhe = data.item.detalhes;
    },

    podeGerarRelatorio() {
      return true;
    },

    setPeriodoContatoDe(value) {
      this.aviso = "";
      this.filtros.data_inicial_contato = value;

      if (this.filtros.data_inicial_contato !== "") {
        const arData = this.filtros.data_inicial_contato.split("/");
        arData[2] = String(parseInt(arData[2]) + 1);

        let dataFinal = arData.join("/");
      }
    },
    setPeriodoContatoAte(value) {
      this.aviso = "";
      this.filtros.data_final_contato = value;

      if (dateToCompare(value) < dateToCompare(this.filtros.data_inicial)) {
        this.aviso = ` Data ${value} não pode ser colocada, data inicial deve ser menor que a data final!`;
      }

      if (value === "") {
        this.aviso = "";
      }
    },

    setAluno(value) {
      this.filtros.aluno = value;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        situacao: form.situacao ? form.situacao : null,
        data_inicial: form.data_inicial ? form.data_inicial : null,
        data_final: form.data_final ? form.data_final : null,
        data_contato_inicial: form.data_inicial_contato
          ? form.data_inicial_contato
          : null,
        data_contato_final: form.data_final_contato
          ? form.data_final_contato
          : null,
        tipo_ocorrencia: form.tipo ? form.tipo.id : null,
        funcionario_responsavel: form.funcionario_responsavel
          ? form.funcionario_responsavel.id
          : null,
        aluno: form.aluno ? form.aluno.id : null,
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
.tabela-ocorrencia >>> tr > th,
.tabela-ocorrencia >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-ocorrencia >>> table thead {
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
#tabela-ocorrencia {
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
  .tabela-ocorrencia {
    overflow: visible;
  }
  .visible-md-block e .visible-lg-block {
    display: none !important;
  }
  .tabela-ocorrencia {
    overflow: hidden;
  }

  .hide-on-print {
    display: none !important;
  }
  .table tr td:nth-child(3) {
    display: none;
  }
}
</style>
