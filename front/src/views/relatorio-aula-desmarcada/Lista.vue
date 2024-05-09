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
                      <b-col md="6">
                        <label
                          v-help-hint="
                            'filtroAvancado-aula_desmarcada_nome_aluno'
                          "
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

                      <b-col md="6">
                        <label
                          v-help-hint="
                            'filtro_avancado_relatorio_aula-desmarcada'
                          "
                          for="data_inicial"
                          class="col-form-label"
                          >Período</label
                        >
                        <div class="row">
                          <g-data
                            :periodo="'mes_anterior'"
                            @dataDe="filtros.data_inicial = $event"
                            @dataAte="filtros.data_final = $event"
                          />
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
                      :fields="exportFields"
                      type="xls"
                      name="relatorio-aula-desmarcada"
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
          show-empty
          hover
          outlined
          small
          fixed-header
          sort-icon-right
          id="tabela-aula-desmarcada"
          class="tabela-aula-desmarcada"
          v-if="lista && !estaCarregando"
          :fields="fields"
          :items="lista"
        >
          <template #cell(data_agenda)="data">
            {{ data.value | formatarData }}
          </template>
          <template #empty>
            <h6>Nenhum resultado encontrado!</h6>
          </template>
        </b-table>
      </div>
   
  </div>
</template>

<script>
import formatarData from "@/filters/formatar-data";
import moment from "moment";
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "ListaRelatorioAulaDesmarcada",
  data() {
    return {
      filtroVisivel: true,
      data_inicial: "",
      data_final: "",
      fields: [
        { key: "nome_aluno", label: "Aluno", sortable: true },
        { key: "nome_livro", label: "Turma", sortable: true },
        { key: "data_agenda", label: "Data", sortable: true },
      ],
      exportFields: {
        Aluno: "nome_aluno",
        Turma: "nome_livro",
        Data: {
          field: "data_agenda",
          callback: (value) => moment(value).format("DD/MM/YYYY"),
        },
      },
    };
  },

  computed: {
    ...mapState("relatorioAulaDesmarcada", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    ...mapActions("relatorioAulaDesmarcada", ["listar"]),
    ...mapMutations("relatorioAulaDesmarcada", ["SET_LISTA", "SET_PARAMETROS"]),

    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
      return true;
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
        aluno: form.aluno ? form.aluno.id : null,
        data_inicial: form.data_inicial ? form.data_inicial : null,
        data_final: form.data_final ? form.data_final : null,
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
.tabela-aula-desmarcada >>> tr > th,
.tabela-aula-desmarcada >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-aula-desmarcada >>> table thead {
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
#tabela-aula-desmarcada {
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
