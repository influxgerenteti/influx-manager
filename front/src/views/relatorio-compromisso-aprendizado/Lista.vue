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
                      <label for="periodo" class="col-form-label"
                        >Período</label
                      >

                      <g-data
                        :periodo="'mes_corrente'"
                        @dataDe="filtros.dataDe = $event"
                        @dataAte="filtros.dataAte = $event"
                      />
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtroRapido-aluno_nome_aluno'"
                        for="nome_aluno"
                        class="col-form-label"
                        >Aluno</label
                      >
                      <typeahead
                        id="nome_aluno"
                        :item-hit="setNomeAluno"
                        source-path="/api/aluno/buscar-nome"
                        key-name="pessoa.nome_contato"
                      />
                    </div>

                    <div class="col-md-3">
                      <label for="turma" class="col-form-label">Curso</label>
                      <g-select-curso
                        id="cursoDescricao"
                        :multiTag="false"
                        v-model="filtros.curso"
                        class="valid-input"
                      ></g-select-curso>
                    </div>
                    <div class="col-md-3">
                      <label for="semestre" class="col-form-label"
                        >Semestre</label
                      >
                      <g-select-semestre
                        id="semestreDescricao"
                        :multiTag="false"
                        v-model="filtros.semestre"
                        class="valid-input"
                      ></g-select-semestre>
                    </div>
                    <div class="col-md-3">
                      <label for="instrutor" class="col-form-label"
                        >Professor</label
                      >
                      <g-select-instrutor
                        id="instrutorDescricao"
                        :multiTag="false"
                        v-model="filtros.instrutor"
                        class="valid-input"
                      ></g-select-instrutor>
                    </div>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end">
                <div class="col-md-auto" v-if="resumo.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="resumo.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="resumo"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-compromisso-aprendizado"
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
    <div class="tabela-dados-compromisso" >
      <b-table
        small
        hover
        outlined
        bordered
        striped
        show-empty
        fixed-header
        sort-icon-right
        id="tabela-dados-compromisso"
        class="tabela-dados-compromisso"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="resumo"
      >
        <template #cell(aluno)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value ? data.value : "--" }}
          </span>
        </template>
        <template #cell(contato)="data">
          <span v-b-tooltip :title="data.value">
            {{ data.value | formatarTelefone }}
          </span>
        </template>
        <template #cell(data_inicio_contrato)="data">
          <span v-if="data.value">
            {{ data.value | formatarData }}
          </span>
          <span v-if="!data.value">
            {{ "--" }}
          </span>
        </template>
        <template #cell(classificacao)="data">
          <span class="print-status">{{
            data.value === 2
              ? "Sem aulas"
              : data.value === 1
              ? "No compromisso"
              : "Fora do compromisso"
          }}</span>
          <span
            v-b-tooltip.viewport.right.hover
            :title="'No compromisso'"
            v-if="data.item.classificacao === 1"
            class="circle-badge circle-badge-for"
          >
            {{ data.value }}
          </span>
          <span
            v-b-tooltip.viewport.right.hover
            :title="'Fora do Compromisso'"
            v-if="data.item.classificacao == 0"
            class="circle-badge circle-badge-ven"
          >
            {{ data.value }}
          </span>
          <span
            v-b-tooltip.viewport.right.hover
            :title="'Sem aulas'"
            v-if="data.item.classificacao == 2"
            class="circle-badge circle-badge-des"
          >
          </span>
        </template>
        <template #empty>
          <h6>Nenhum registro a ser exibido.</h6>
        </template>
      </b-table>
    </div>

    <div class="tabela-compromisso">
      <b-table
        small
        hover
        outlined
        bordered
        striped
        show-empty
        fixed-header
        sort-icon-right
        id="tabela-compromisso"
        class="tabela-compromisso"
        :fields="camposTabela"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="tabelaContagem"
      >
      <template #cell(classificacao)="data">
          <span class="print-status">{{
            data.value === 2
              ? "Sem aulas"
              : data.value === 1
              ? "No compromisso"
              : "Fora do compromisso"
          }}</span>
          <span
            v-b-tooltip.viewport.right.hover
            :title="'No compromisso'"
            v-if="data.item.classificacao == 1"
            class="circle-badge circle-badge-for"
          >
            {{ data.value }}
          </span>
          <span
            v-b-tooltip.viewport.right.hover
            :title="'Fora do Compromisso'"
            v-if="data.item.classificacao == 0"
            class="circle-badge circle-badge-ven"
          >
            {{ data.value }}
          </span>
          <span
            v-b-tooltip.viewport.right.hover
            :title="'Sem aulas'"
            v-if="data.item.classificacao == 2"
            class="circle-badge circle-badge-des"
          >
          </span>
        </template>
      <template #empty>
          <h4>Nenhum registro a ser exibido.</h4>
        </template>
      </b-table>
    </div>
  
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "ListaRelatorioCompromissoAprendizado",
  data() {
    return {
      hoje: null,
      dataDe: "",
      dataAte: "",
      aviso: "",
      filtroVisivel: true,
      sortBy: "aluno",
      sortDesc: false,
      camposTabela: [  
        { key: "classificacao", sortable: true, label: "Classificação" },
        { key: "valor", sortable: true, label: "Quantidade" }],
    
        fields: [
        { key: "aluno", sortable: true },
        { key: "contato", sortable: true },
        { key: "livro", sortable: true },
        { key: "data_inicio_contrato", sortable: true, label: "Início" },
        { key: "classificacao", sortable: true, label: "Classificação" },
      ],
  
      exportFields: {
        Aluno: "aluno",
        Contato: "contato",
        Livro: "livro",
        Início: "data_inicio_contrato",
        Classificacao: {
          field: "classificacao",
          callback: (value) =>
            value === 2
              ? "Sem aulas"
              : value === 1
              ? "No compromisso"
              : "Fora do compromisso",
        },
      },
    };
  },

  computed: {
    ...mapState("relatorioCompromissoAprendizado", [
      "filtros",
      "lista",
      "resumo",
      "tabelaContagem",
      "estaCarregando",
    ]),
 
  },

  mounted() {
  this.limparFiltros([])
    },

  methods: {
    ...mapActions("relatorioCompromissoAprendizado", ["listar"]),
    ...mapMutations("relatorioCompromissoAprendizado", [
      "SET_LISTA",
      "SET_RESUMO",
      "SET_PARAMETROS",
    ]),
      
limparFiltros(){
  this.listar([]); 
},

    setNomeAluno(value) {
      this.alunoTemporario = value;
      if (this.filtroSelecionado === 1) {
        this.filtrar();
      }
    },
    setDataDe(value) {
      this.dataDe = value;
      this.validarDatas();
      this.$emit("dataDe", value, this.extraParam);
    },

    setDataAte(value) {
      this.dataAte = value;
      this.validarDatas();
      this.$emit("dataAte", value, this.extraParam);
    },

    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
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
        professor: form.instrutor || null,
        semestre: form.semestre || null,
        aluno: this.alunoTemporario ? this.alunoTemporario.id : null,
        data_inicial: form.dataDe || null,
        data_final: form.dataAte || null,
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

#tabela-compromisso >>> tr > th,
#tabela-compromisso >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 2em;
  text-align: -webkit-center;
}
#tabela-compromisso >>> thead {
  background-color: #fff !important;
}
.print-status {
  display: none;
}
.circle-badge {
  margin: auto;
}

@media print {
  .print-status {
    display: block;
  }
  .circle-badge {
    display: none;
  }
  #tabela-compromisso {
    overflow: visible;
  }
  .tabela-compromisso {
    overflow: hidden;
  }
}
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
.tabela-dados-compromisso{
  max-height: 50vh;
  overflow: scroll;
  padding-bottom: 5px;
}

.tabela-dados-compromisso>>> tr > th,
.tabela-dados-compromisso>>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-dados-compromisso>>> table thead {
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
#tabela-dados-compromisso{
  overflow: visible;
}
@media (max-width: 992px) {
  .tabela-compromisso {
    margin-bottom: 8%;
}
}
@media print {
  .tabela-wrapper {
    overflow: hidden;
  }
}
</style>
