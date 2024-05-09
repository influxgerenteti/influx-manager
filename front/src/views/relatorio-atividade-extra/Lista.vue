<template>
   <div class="animated fadeIn wrapper-table-scroll">
    <div class="no-print pb-3">
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
                    <div class="col-md-3">
                      <label
                        v-help-hint="
                          'filtroRapido-atividade_extra_tipo_atividade'
                        "
                        for="atividade_extra_tipo_atividade"
                        class="col-form-label"
                        >Tipo Atividade</label
                      >
                      <g-select
                        :select="setTipoAtividade"
                        v-model="filtros.atividade"
                        :options="listaTipoAtividade"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </div>
                    <div class="col-md-3">
                      <label
                        v-help-hint="
                          'filtroAvancado-ocorrencia_academica_nome_aluno'
                        "
                        for="nome_aluno"
                        class="col-form-label"
                        >Aluno</label
                      >
                      <typeahead
                        id="id"
                        :item-hit="setAluno"
                        source-path="/api/aluno/buscar-nome"
                        key-name="pessoa.nome_contato"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtroRapido-atividade_extra_situacao'"
                        for="filtro_avancado_situacao"
                        class="col-form-label"
                        >Situação</label
                      >
                      <div class="col-md-3 p-0">
                        <b-form-checkbox-group
                          id="filtro_avancado_situacao"
                          v-model="filtros.situacao_selecionada"
                          :options="situacao"
                          buttons
                          button-variant="cinza"
                          class="checkbtn-line"
                          @change="setSituacao"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-4 p-0">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_atividade_extra'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <g-data
                        :periodo="'dia_atual'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
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
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-atividade-extra"
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
    <div v-if="estaCarregando" class="d-flex h-50">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div class="tabela-wrapper">
      <b-table
        id="tabela-atividade-extra"
        class="tabela-atividade-extra"
        v-if="lista && !estaCarregando"
        :fields="tableFields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
        small
        hover
        outlined
        striped
        show-empty
        fixed-header
        sort-icon-right
      >
        <template #cell(aluno)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value ? data.value : "Sem nome" }}
          </span>
        </template>
        <template #cell(data_atividade)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value | formatarData }}
          </span>
        </template>
        <template #cell(hora_inicial_atividade)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value }}
          </span>
        </template>
        <template #cell(hora_final_atividade)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value }}
          </span>
        </template>
        <template #cell(presenca)="data">
          <span>
            {{
              data.value
                ? data.value == "P"
                  ? "Presente"
                  : data.value == "F"
                  ? "Faltante"
                  : data.value == "R"
                  ? "Reposição"
                  : ""
                : "-"
            }}
          </span>
        </template>
        <template #cell(situacao)="data">
          <span v-b-tooltip.top>
            {{
              data.value == "P"
                ? "Pendente"
                : data.value == "C"
                ? "Concluído"
                : data.value == "CC"
                ? "Cancelado"
                : ""
            }}
          </span>
        </template>
        <template #cell(valor)="data">
          <span v-b-tooltip.top :title="data.value">
            {{ data.value | formatarMoeda }}
          </span>
        </template>
        <template #empty>
          <p>Nenhum resultado encontrado!</p>
        </template>
      </b-table>
    </div>
  </div>
</template>

<script>
import formatarMoeda from "@/filters/formatar-moeda";
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "ListaRelatorioAtividadeExtra",
  data() {
    return {
      aviso: "",
      filtroVisivel: true,
      data_inicial: "",
      data_final: "",
      filtroSelecionado: 2,
      sortBy: "descricao",
      sortDesc: false,
      tableFields: [
        { key: "aluno", sortable: true },
        { key: "descricao", sortable: true, label: "Descrição" },
        { key: "data_atividade", sortable: true, label: "Data Atividade" },
        {
          key: "hora_inicial_atividade",
          sortable: true,
          label: "Hora inicial",
        },
        { key: "hora_final_atividade", sortable: true, label: "Hora final" },
        { key: "presenca", sortable: true, label: "Presença" },
        { key: "situacao", sortable: true, label: "Situação" },
        { key: "valor", sortable: true },
      ],
      situacao: [
        { text: "Pendente", value: "P" },
        { text: "Concluido", value: "C" },
        { text: "Cancelado", value: "CC" },
      ],
      exportFields: {
        Aluno: {
          field: "aluno",
          callback: (value) => (value ? value : "Sem Nome *"),
        },
        Descrição: "descricao",
        "Data Atividade": "data_atividade",
        "Hora Inicial": "hora_inicial_atividade",
        "Hora Final": "hora_final_atividade",
        Presença: {
          field: "presenca",
          callback: (value) =>
            value
              ? value == "P"
                ? "Presente"
                : value == "F"
                ? "Faltante"
                : value == "R"
                ? "Reposição"
                : ""
              : "-",
        },
        Situação: {
          field: "situacao",
          callback: (value) =>
            value == "P"
              ? "Pendente"
              : value == "C"
              ? "Concluído"
              : value == "CC"
              ? "Cancelado"
              : "",
        },
        Valor: {
          field: "valor",
          callback: (value) => (value ? `R$ ${value.replace(".", ",")}` : "-"),
        },
      },
      tipoAtividade: null,
    };
  },

  computed: {
    ...mapState("relatorioAtividadeExtra", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
    ...mapState("cadastroServico", { listaDeItemRequisicao: "lista" }),

    listaTipoAtividade: {
      get() {
        return [
          { id: null, descricao: "Selecione" },
          ...this.listaDeItemRequisicao.filter(
            (item) => item.tipo_item.tipo === "AE"
          ),
        ];
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.listarCamposSelects();
  },

  methods: {
    ...mapActions("cadastroServico", { listarItensRequisicao: "listar" }),
    ...mapMutations("relatorioAtividadeExtra", ["SET_LISTA", "SET_PARAMETROS"]),
    ...mapActions("relatorioAtividadeExtra", { listarAtividades: "listar" }),

    listarCamposSelects() {
      this.$store.commit("cadastroServico/SET_PAGINA_ATUAL", 1);
      this.$store.commit("cadastroServico/SET_LISTA", []);
      this.$store.commit("cadastroServico/SET_FILTRO_FRANQUEADA", [
        this.$store.state.root.usuarioLogado.franqueadaSelecionada,
      ]);
      this.$store.dispatch("cadastroServico/listar");
    },

    setAtividadeExtra(value) {
      this.filtros.atividade = value;
    },

    setTipoAtividade(value) {
      this.tipoAtividade = value;
    },

    setAluno(value) {
      this.filtros.aluno = value;
    },

    setSituacao(value) {
      this.situacao_selecionada = value;
    },

    podeGerarRelatorio() {
      return true;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listarAtividades();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null,
        situacao: form.situacao_selecionada || null,
        aluno: form.aluno ? form.aluno.id : null,
        atividade_extra: form.atividade ? form.atividade.id : null,
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
.tabela-atividade-extra >>> tr > th,
.tabela-atividade-extra >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;
}
.tabela-atividade-extra >>> table thead {
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
#tabela-atividade-extra {
  overflow: visible;
}
.main .container-fluid,
.main .container-sm,
.main .container-md,
.main .container-lg,
.main .container-xl {
  overflow: hidden;
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
