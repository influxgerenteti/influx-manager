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
                    <div class="col-md-3">
                      <label
                        v-help-hint="
                          'filtroRapido-atividade_extra_tipo_atividade'
                        "
                        for="atividade_extra_tipo_atividade"
                        class="col-form-label"
                        >Período - Início do Contrato</label
                      >
                      <g-data
                        ref="gdataInicio"
                        :periodo="'sem_data'"
                        @dataDe="atualizarDataContratoInicio($event, 'dataDe')"
                        @dataAte="
                          atualizarDataContratoInicio($event, 'dataAte')
                        "
                      />

                      <div
                        v-if="dataCriacaoInvalida()"
                        class="floating-message bg-danger"
                      >
                        Data inicial deve ser menor que a data final!
                      </div>
                    </div>
                    <div class="col-md-3">
                      <label
                        v-help-hint="
                          'filtroAvancado-ocorrencia_academica_nome_aluno'
                        "
                        for="nome_aluno"
                        class="col-form-label"
                        >Curso</label
                      >
                      <g-select-curso
                        id="cursoDescricao"
                        :multiTag="false"
                        v-model="filtros.curso"
                        class="valid-input"
                      ></g-select-curso>
                    </div>

                    <div class="col-md-3">
                      <label for="livro" class="col-form-label">Livro</label>
                      <g-select-livro
                        id="cursoDescricao"
                        :multiTag="false"
                        v-model="filtros.livro"
                        class="valid-input"
                      ></g-select-livro>
                    </div>
                    <div class="">
                      <label for="tipo_movimentacao" class="col-form-label"
                        >Tipo movimentação</label
                      >
                      <b-form-checkbox-group
                        id="tipo_movimentacao"
                        v-model="filtros.tipo_movimentacao"
                        :options="tiposMovimentacao"
                        buttons
                        button-variant="cinza"
                        name="tipo_movimentacao"
                        class="checkbtn-line fill-width"
                      />
                    </div>
                  </div>
                  <div class="form-group d-md-flex">
                    <div class="col-md-3">
                      <label for="instrutor_personal" class="col-form-label"
                        >Instrutor</label
                      >
                      <g-select-instrutor
                        id="instrutorDescricao"
                        :multiTag="false"
                        v-model="filtros.instrutor"
                        class="valid-input"
                      ></g-select-instrutor>
                    </div>

                    <div class="col-md-3">
                      <label for="semestre" class="col-form-label"
                        >Semestre</label
                      >
                      <g-select-semestre
                        id="semestre"
                        v-model="filtros.semestre"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'form-responsavel_carteira_funcionario'"
                        for="responsavel_carteira_funcionario"
                        class="col-form-label"
                        >Responsável da Carteira</label
                      >
                      <g-select
                        id="responsavel_carteira_funcionario"
                        :value="filtros.responsavel_carteira"
                        :select="setResponsavelCarteiraFuncionario"
                        :options="listaResponsaveisCarteira"
                        class="multiselect-truncate valid-input"
                        label="apelido"
                        track-by="id"
                      />
                    </div>
                    <div class="">
                      <label for="idioma" class="col-form-label"
                        >Idioma do curso</label
                      >
                      <b-form-radio-group
                        id="idioma"
                        v-model="filtros.idioma"
                        :options="idiomas"
                        buttons
                        button-variant="cinza"
                        name="idioma"
                        class="checkbtn-line fill-width"
                      />
                    </div>
                  </div>
                  <div class="form-group d-md-flex">
                    <div class="col-md-auto">
                      <label for="situacao_rapido" class="col-form-label"
                        >Situação do aluno</label
                      >
                      <b-form-checkbox-group
                        id="situacao_rapido"
                        v-model="filtros.situacao_aluno"
                        :options="situacoesAluno"
                        buttons
                        button-variant="cinza"
                        name="situacao_rapido"
                        class="checkbtn-line fill-width"
                      />
                    </div>
                  </div>

                  <div class="body-sector p-2">
                    <div class="form-group row">
                      <div class="col-md-3">
                        <label
                          v-help-hint="
                            'filtro_avancado_relatorio_atividade_extra'
                          "
                          for="data_inicial"
                          class="col-form-label"
                          >Contrato - Data de Cancelamento</label
                        >
                        <g-data
                          ref="gdataCancelamento"
                          :periodo="'sem_data'"
                          @dataDe="
                            atualizarDataContratoCancelamento($event, 'dataDe')
                          "
                          @dataAte="
                            atualizarDataContratoCancelamento($event, 'dataAte')
                          "
                        />

                        <div
                          v-if="dataCancelamentoInvalida()"
                          class="floating-message bg-danger"
                        >
                          Data inicial deve ser menor que a data final!
                        </div>
                      </div>

                      <div class="col-md-auto">
                        <label
                          for="situacao_rapido"
                          class="col-form-label d-block"
                          >Situação do contrato</label
                        >
                        <b-form-checkbox-group
                          id="situacao_rapido"
                          v-model="filtros.situacao_contrato"
                          :options="situacoesContrato"
                          buttons
                          button-variant="cinza"
                          name="situacao_rapido"
                          class="checkbtn-line fill-width"
                        />
                      </div>

                      <div class="col-md-3" style="margin-top: auto">
                        <b-form-checkbox
                          id="mostrar_motivo_cancelamento"
                          v-model="filtros.mostrar_motivo_cancelamento"
                          >Mostrar motivo de cancelamento</b-form-checkbox
                        >
                      </div>
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
    <div v-if="estaCarregando" class="d-flex h-100">
      <load-placeholder :loading="estaCarregando" />
    </div>
    <div class="tabela-wrapper">
        <b-table
        v-if="!estaCarregando"
        class="tabela-contratos"
        id="tabela-contratos"
        :items="lista"
        :fields="fields"
        
        small
        hover
        outlined
        bordered
        striped
        show-empty
        sort-icon-right

    
      >
        <template #empty>
          <h4>Nenhum registro a ser exibido.</h4>
        </template>
        <!-- <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Carregando Dados...</strong>
          </div>
        </template> -->
      </b-table>
    </div> 
   </div>
</template> 

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import open from "../../utils/open";
import { dateToCompare, stringToISODateMinMax } from "../../utils/date";
import GSelectSemestre from "@/components/specificFilters/GSelectSemestre.vue";

export default {
  components: { GSelectSemestre },
  name: "ListaRelatorioContratos",

  data() {
    return {
      filtroVisivel: true,
      situacao_rapido: [],
      situacao_contrato: [],
      situacoesAluno: [
        { text: "Ativo", value: "ATI" },
        { text: "Inativo", value: "INA" },
        { text: "Trancado", value: "TRA" },
      ],

      idiomas: [
        { text: "Inglês", value: "1" },
        { text: "Espanhol", value: "2" },
      ],
      situacoesContrato: [
        { text: "Vigente", value: "V" },
        { text: "Encerrado", value: "E" },
        { text: "Rescindido", value: "R" },
        { text: "Cancelado", value: "C" },
        { text: "Trancado", value: "T" },
      ],
      tiposMovimentacao: [
        { text: "Matrícula", value: "M" },
        { text: "Rematrícula", value: "R" },
      ],
      sortBy: "aluno",
      sortDesc: true,
      fields: [
        { key: "aluno", label: "Aluno", sortable: true },
        { key: "livro", label: "Livro", sortable: true },
        { key: "curso", label: "Curso", sortable: true },
        { key: "idioma", label: "Idioma", sortable: true },
        { key: "instrutor", label: "Instrutor", sortable: true },
        {
          key: "tipo_movimentacao",
          label: "Tipo Movimentação",
          sortable: true,
        },
        { key: "situacao_aluno", label: "Situação Aluno", sortable: true },
        {
          key: "situacao_contrato",
          label: "Situação Contrato",
          sortable: true,
        },
        { key: "semestre", label: "Semestre", sortable: true },
        {
          key: "motivo_cancelamento",
          label: "Motivo Cancelamento",
          sortable: true,
        },
      ],
      exportFields: {
        Aluno: "aluno",
        Livro: "livro",
        Curso: "curso",
        Idioma: "idioma",
        Instrutor: "instrutor",
        "Tipo Movimentação": "tipo_movimentacao",
        "Situação Aluno": "situacao_aluno",
        "Situação Contrato": "situacao_contrato",
        Semestre: "semestre",
        "Motivo Cancelamento": "motivo_cancelamento",
      },

      bloquearAtualizacao: true,
    };
  },

  computed: {
    ...mapState("relatorioContratos", ["filtros", "lista", "estaCarregando"]),
    ...mapState("funcionario", { listaFuncionarios: "lista" }),
    ...mapState("semestre", { listaSemestres: "lista" }),

    listaResponsaveisCarteira: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.listaFuncionarios && this.listaFuncionarios.length
            ? this.listaFuncionarios.filter((func) => func.consultor === true)
            : []
        );
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.listarFuncionarios(true);
    this.$store.commit("semestre/SET_PAGINA_ATUAL", 1);
    this.listarSemestres();
    this.LIMPAR_ITEM_SELECIONADO();
  },

  methods: {
    dateToCompare: dateToCompare,

    ...mapActions("relatorioContratos", ["listar"]),
    ...mapMutations("relatorioContratos", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "LIMPAR_ITEM_SELECIONADO",
    ]),
    ...mapActions("curso", { listarTodosCursos: "listar" }),
    ...mapActions("idioma", { listarIdiomas: "listar" }),
    ...mapActions("funcionario", { listarFuncionarios: "listar" }),
    ...mapActions("semestre", { listarSemestres: "listar" }),

    atualizarDataContratoInicio(event) {
      if (this.bloquearAtualizacao && this.$refs.gdataCancelamento) {
        this.$refs.gdataCancelamento.setDataDe("");

        this.$refs.gdataCancelamento.setDataAte("");
        this.bloquearAtualizacao = false;
      }
    },

    atualizarDataContratoCancelamento(event) {
      if (!this.bloquearAtualizacao && this.$refs.gdataInicio) {
        this.$refs.gdataInicio.setDataDe("");
        this.$refs.gdataInicio.setDataAte("");
        this.bloquearAtualizacao = true;
      }
    },

    setResponsavelCarteiraFuncionario(value) {
      this.filtros.responsavel_carteira = value;
    },

    setDataInicioContrato(value) {
      this.filtros.data_contrato_cancelamento_de = value;
      this.$forceUpdate();
    },

    setDataTerminoContrato(value) {
      this.filtros.data_contrato_cancelamento_ate = value;
      this.$forceUpdate();
    },

    setLivro(value) {
      this.filtros.livro = value;
    },

    setDataCriacaoDe(value) {
      this.filtros.data_criacao_de = value;
      this.$forceUpdate();
    },

    setDataCriacaoAte(value) {
      this.filtros.data_criacao_ate = value;
      this.$forceUpdate();
    },

    dataTerminoInvalida() {
      return (
        dateToCompare(this.filtros.data_inicio_contrato_de) >
          dateToCompare(this.filtros.data_termino_contrato_ate) &&
        this.filtros.data_termino_contrato_ate !== ""
      );
    },

    dataCancelamentoInvalida() {
      return (
        dateToCompare(this.filtros.data_contrato_cancelamento_de) >
          dateToCompare(this.filtros.data_contrato_cancelamento_ate) &&
        this.filtros.data_contrato_cancelamento_ate !== ""
      );
    },

    dataCriacaoInvalida() {
      return (
        dateToCompare(this.filtros.data_criacao_de) >
          dateToCompare(this.filtros.data_criacao_ate) &&
        this.filtros.data_criacao_ate !== ""
      );
    },

    podeGerarRelatorio() {
      return !this.dataTerminoInvalida() && !this.dataCriacaoInvalida();
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        tipo_movimentacao:
          form.tipo_movimentacao.length > 0 ? form.tipo_movimentacao : null,
        curso: form.curso ? form.curso : null,
        livro: form.livro ? form.livro : null,
        instrutor: form.instrutor ? form.instrutor : null,
        idioma: form.idioma.length > 0 ? form.idioma : null,
        semestre: form.semestre ? form.semestre : null,
        situacao_aluno:
          form.situacao_aluno.length > 0 ? form.situacao_aluno : null,
        responsavel_carteira: form.responsavel_carteira
          ? form.responsavel_carteira.id
          : null,
        data_criacao_de: this.$refs.gdataInicio.dataDe
          ? this.$refs.gdataInicio.dataDe
          : null,
        data_criacao_ate: this.$refs.gdataInicio.dataAte
          ? this.$refs.gdataInicio.dataAte
          : null,
        data_contrato_cancelamento_de: this.$refs.gdataCancelamento.dataDe
          ? this.$refs.gdataCancelamento.dataDe
          : null,
        data_contrato_cancelamento_ate: this.$refs.gdataCancelamento.dataAte
          ? this.$refs.gdataCancelamento.dataAte
          : null,

        situacao_contrato:
          form.situacao_contrato.length > 0 ? form.situacao_contrato : null,

        mostrar_motivo_cancelamento: form.mostrar_motivo_cancelamento || null,
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
.tabela-contratos >>> tr > th,
.tabela-contratos >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-contratos >>> table thead {
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
#tabela-contratos {
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

