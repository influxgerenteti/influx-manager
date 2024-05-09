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
                    <div class="col-md-3">
                      <label for="licao" class="col-form-label"> Curso </label>
                      <g-select-curso id="curso" v-model="filtros.curso">
                      </g-select-curso>
                    </div>

                    <div class="col-md-3">
                      <label for="livro" class="col-form-label"> Livro </label>
                      <g-select-livro
                        id="livro"
                        v-model="filtros.livro"
                        :idioma="filtros.idioma || 0"
                      >
                      </g-select-livro>
                    </div>
                    <div class="col-md-3">
                      <label for="instrutor_personal" class="col-form-label"
                        >Instrutor</label
                      >
                      <g-select-instrutor
                        id="instrutor_personal"
                        v-model="filtros.instrutor"
                      />
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col-md-auto">
                      <label for="situacao_turma" class="col-form-label d-block"
                        >Situação da turma</label
                      >
                      <b-form-checkbox-group
                        id="situacao_turma"
                        v-model="filtros.situacao_turma"
                        :options="situacoesTurma"
                        buttons
                        button-variant="cinza"
                        name="situacao_rapido"
                        class="checkbtn-line fill-width"
                      />
                    </div>

                    <div class="col-md-auto">
                      <label
                        for="modalidade_turma"
                        class="col-form-label d-block"
                        >Modalidade da turma</label
                      >
                      <b-form-checkbox-group
                        id="modalidade_turma"
                        v-model="filtros.modalidade_turma"
                        :options="listaModalidadesTurmaCheckboxes"
                        buttons
                        button-variant="cinza"
                        name="modalidade_turma"
                        class="checkbtn-line fill-width"
                      />
                    </div>

                    <div class="col-md-auto">
                      <label for="turnos" class="col-form-label d-block"
                        >Turno</label
                      >
                      <b-form-checkbox-group
                        id="turnos"
                        v-model="filtros.turnos"
                        :options="listaTurnos"
                        buttons
                        button-variant="cinza"
                        name="turnos"
                        class="checkbtn-line fill-width"
                      />
                    </div>

                    <div class="col-md-auto">
                      <label for="dia_semana" class="col-form-label d-block"
                        >Dias da semana</label
                      >
                      <b-form-checkbox-group
                        id="dia_semana"
                        v-model="filtros.dia_semana"
                        :options="listaDiasSemana"
                        buttons
                        button-variant="cinza"
                        name="dia_semana"
                        class="checkbtn-line fill-width"
                      />
                    </div>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end">
                <div class="col-md-2" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-2" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-turma-existente"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
                </div>
                <div class="col-md-2">
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
    <div v-if="!estaCarregando">
   
      <b-table
        small
        hover
        outlined
        bordered
        striped
        show-empty
        fixed-header
        id="tabela-turma-existente"
        class="tabela-turma-existente"
        :fields="fieldsResumo"
        :items="resumo"
      >
        <template #cell(media_aluno_por_turma)="data">
          <span v-b-tooltip :title="data.value">
            {{
              typeof data.value === "number"
                ? data.value.toFixed(2)
                : data.value
            }}
          </span>
        </template>
      </b-table>
   
    </div>
    <div class="tabela-wrapper mt-3">
    <div class="tabela-turma-existente">
      <b-table
        small
        hover
        outlined
        bordered
        striped
        show-empty
        fixed-header
        sort-icon-right
        id="tabela-turma-existente"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(turma)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(professor)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(horario)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(livro)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(sala)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>

        <template #empty>
          <h4>Nenhum registro a ser exibido.</h4>
        </template>
      </b-table>
    </div>
  </div>
  </div>
</template>

<script>
import GSelectInstrutor from "@/components/specificFilters/GSelectInstrutor.vue";
import formatarNumero from "@/filters/formatar-numero";
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  components: { GSelectInstrutor },
  name: "ListaRelatorioTurmaExistente",
  data() {
    return {
      sortBy: "turma",
      sortDesc: false,
      situacoesTurma: [
        { text: "Aberta", value: "ABE" },
        { text: "Formação", value: "FOR" },
        { text: "Encerrada", value: "ENC" },
      ],
      listaTurnos: [
        { text: "Manhã", value: "M" },
        { text: "Tarde", value: "T" },
        { text: "Noite", value: "N" },
      ],
      listaDiasSemana: [
        { text: "Segunda", value: "SEG" },
        { text: "Terça", value: "TER" },
        { text: "Quarta", value: "QUA" },
        { text: "Quinta", value: "QUI" },
        { text: "Sexta", value: "SEX" },
        { text: "Sábado", value: "SAB" },
        { text: "Domingo", value: "DOM" },
      ],
      filtroVisivel: true,
      fields: [
        { key: "curso", sortable: true },
        { key: "turma", sortable: true },
        { key: "livro", sortable: true },
        { key: "instrutor", sortable: true },
        { key: "horario", sortable: true },
        { key: "dia_semana", sortable: true },
        { key: "data_inicio", sortable: true },
        { key: "data_fim", sortable: true },
      ],
      fieldsResumo: [
        { key: "media_aluno_por_turma" },
        { key: "total_alunos" },
        { key: "total_turmas" },
      ],
      exportFields: {
        Turma: "turma",
        Livro: "livro",
        Curso: "curso",
        Professor: "instrutor",
        Horário: "horario",
        "Dia Semana": "dia_semana",
        "Data Inicio": "data_inicio",
        "Data Fim": "data_fim",
      },
    };
  },

  computed: {
    ...mapState("relatorioTurmaExistente", ["filtros"]),
    ...mapState("relatorioTurmaExistente", [
      "filtros",
      "lista",
      "resumo",
      "estaCarregando",
    ]),

    ...mapState("modalidadeTurma", { listaModalidadesTurma: "lista" }),

    listaModalidadesTurmaCheckboxes: {
      get() {
        const modalidades = this.listaModalidadesTurma
          .filter((mod) => mod.tipo !== "PER")
          .map((mod) => {
            return { text: mod.descricao, value: mod.id };
          });
        return modalidades;
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.SET_RESUMO([]);
    this.$store.commit("modalidadeTurma/SET_PAGINA_ATUAL", 1);
    this.modalidade_turma = "";
    this.listarModalidadeTurma();
  },

  methods: {
    ...mapMutations("relatorioTurmaExistente", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "SET_RESUMO",
    ]),
    ...mapActions("relatorioTurmaExistente", ["listar"]),
    ...mapActions("modalidadeTurma", { listarModalidadeTurma: "listar" }),

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
        curso: form.curso || null,
        livro: form.livro || null,
        situacao_turma: form.situacao_turma || null,
        modalidade_turma: form.modalidade_turma || null,
        turma: form.turma || null,
        turnos: form.turnos || null,
        dia_semana: form.dia_semana || null,
        instrutor: form.instrutor || null,
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
.tabela-turma-existente >>> tr > th,
.tabela-turma-existente >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-turma-existente >>> table thead {
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
#tabela-turma-existente {
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
