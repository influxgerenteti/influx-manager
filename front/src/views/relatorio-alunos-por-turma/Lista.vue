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
                      <label 
                      class="col-form-label d-block"
                       for="turma">Turma</label>
                      <g-select-turma
                        id="turma"
                        v-model="filtros.turma"
                        class="valid-input"
                      />
                    </div>
                    <div class="col-md-3">
                      <label for="livros" class="col-form-label">Livro</label>
                      <g-select-livro id="livros" v-model="filtros.livro" />
                    </div>
                    
                    <div class="col-md-3">
                    <label for="sala_franqueada" class="col-form-label"
                      >Sala</label
                    >
                    <g-select
                      id="sala_franqueada"
                      v-model="filtros.sala_franqueada"
                      :select="setSala"
                      :options="listaSalasFranqueada"
                      class="valid-input"
                      label="descricao"
                      track-by="id"
                    />
                    </div>
                    <div class="col-md-3">
                      <label for="instrutor_personal" class="col-form-label">
                        Professor
                      </label>
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
              <div class="mb-2 mt-3 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista && lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista && lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-aluno-por-turma"
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
    v-if="lista && !estaCarregando"
            class="tabela-alunos-por-turma"
            :items="lista"
            :fields="cabecalho"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            small
        hover
        outlined
        striped
        show-empty
     
        fixed-header
        sort-icon-right
    >
    <template #cell(nome_turma)="data">
      <span v-b-tooltip.top :title="data.value">
        {{ data.value }}
      </span>
    </template>
   
    <template #cell(livro)="data">
      <span v-b-tooltip.top :title="data.value">
        {{ data.value }}
      </span>
    </template>
    <template #cell(professor)="data">
      <span v-b-tooltip.top :title="data.value">
        {{ data.value }}
      </span>
    </template>
    <template #cell(sala)="data">
      {{ data.value ? data.value : '--' }}
    </template>
    <template #empty>
      <p>Nenhum resultado encontrado!</p>
    </template>
    </b-table>
    </div>
 
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "ListaRelatorioAlunosPorTurma",
  data() {
    return {
      filtroVisivel: true,
      exportFields: {
        'Nome Turma': 'nome_turma',
        'Livro': 'livro',
        'Professor': 'professor',
        'Sala': 'sala',
        "Máximo Alunos": 'maximo_alunos',
        'Alunos': 'alunos',
      },
   
      sortBy: "nome_turma",
      sortDesc: false,
      tableFields: [
        { key: "nome_turma", sortable: true, label:'Nome Turma'},
        { key: "livro", sortable: true },
        { key: "professor", sortable: true },
        { key: "sala", sortable: true },
        { key: "maximo_alunos", sortable: true, label:'Max Alunos' },
        { key: "alunos", sortable: true },
      ],
      cabecalho: [
        { key: "nome_turma", label: "Nome Turma", sortable: false },
        { key: "livro", label: "Livro", sortable: true },
        { key: "professor", label: "Professor", sortable: true },
        { key: "sala", label: "Professor", sortable: true },
        { key: "maximo_alunos", label: "Max Alunos", sortable: true },
        { key: "alunos", label: "Alunos", sortable: true },
      ],
    };
  },

  computed: {
    ...mapState("relatorioAlunosPorTurma", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
       ...mapState('salaFranqueada', {listaSalasFranqueada: 'lista'}),

    listaSalasFranqueada: {
      get () {
          return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.salaFranqueada.lista)
      }
    },
  
  },

  mounted() {
    this.executaFiltros();
    this.SET_LISTA([]);
    this.LIMPAR_ITEM_SELECIONADO();
  },

  methods: {
    ...mapActions("relatorioAlunosPorTurma", ["listar"]),
    ...mapMutations("relatorioAlunosPorTurma", ["SET_LISTA", "SET_PARAMETROS", "LIMPAR_ITEM_SELECIONADO"]),


    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
      return true;
    },

    setSala (value) {
      this.sala_franqueada = value.id === null ? '' : value
    },

    setHorario(value) {
      this.horarioSelecionado = value;
    },
  


    executaFiltros() {
      this.$store.commit(
        "turma/SET_FILTRO_HORARIO",
        this.horarioSelecionado ? this.horarioSelecionado.id : null
      );
      this.listarCamposSelects()
    },
    listarCamposSelects() {
      this.$store.commit("horario/SET_PAGINA_ATUAL", 1);
      this.$store.dispatch("horario/listar");

      this.$store.commit('salaFranqueada/SET_PAGINA_ATUAL', 1)

      this.$store.dispatch('salaFranqueada/listar')
        .then(() => {
          if (this.listaSalasFranqueada.length === 2 ) {
            this.sala_franqueada = this.listaSalasFranqueada[1]
          }

        })

     // this.listarFuncionarios(true);
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink()
      this.SET_PARAMETROS(parametros)
      this.listar();
    },
     converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        turma: form.turma ? form.turma : null,
        livro: form.livro ? form.livro : null,
        sala: form.sala_franqueada ? form.sala_franqueada.id : null,   
        instrutor: form.instrutor ? form.instrutor : null   
      
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
.tabela-alunos-por-turma >>> tr > th,
.tabela-alunos-por-turma >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-alunos-por-turma >>> table thead {
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
#tabela-alunos-por-turma {
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