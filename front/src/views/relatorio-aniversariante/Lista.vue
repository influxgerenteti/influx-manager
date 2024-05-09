<template>
  <div class="animated fadeIn wrapper-table-scroll" >
    
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
                      <div class="col-md-auto">
                        <label
                          for="situacao_rapido"
                          class="col-form-label d-block"
                          >Mês</label
                        >
                        <b-form-radio-group
                          id="mes"
                          v-model="filtros.mes"
                          :options="mesOpcoes"
                          buttons
                          button-variant="cinza"
                          class="checkbtn-line"
                          name="mes"
                        />
                      </div>
                      <div class="col-md-3">
                        <label for="turma" class="col-form-label">Turma</label>
                        <g-select-turma id="turma" v-model="filtros.turma" />
                      </div>
                      <div class="col-md-auto">
                        <label
                          for="situacao_rapido"
                          class="col-form-label d-block"
                          >Situação do alunos</label
                        >
                        <b-form-radio-group
                          id="situacao_interessado"
                          v-model="filtros.situacao_selecionada"
                          :options="situacaoOpcoes"
                          buttons
                          button-variant="cinza"
                          class="checkbtn-line"
                          name="situacao_selecionada"
                          selected="0"
                        />
                      </div>
                    </div>
                    <br />
                    <div class="form-group row">
                      <div class="col-md-6">
                        <b-form-group label="Opções de impressão">
                          <b-form-checkbox
                            v-model="filtros.mostrar_representantes"
                            name="check-button"
                            >Mostrar Representantes</b-form-checkbox
                          >
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
                      :data="lista"
                      :fields="exportFields"
                      type="xls"
                      name="relatorio-aniversariantes"
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
      <!-- <div v-if="estaCarregando" class="d-flex h-100">
        <load-placeholder :loading="estaCarregando" />
      </div> -->
      <div v-if="estaCarregando" class="d-flex h-100">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="tabela-wrapper">
            <b-table
            v-if="!estaCarregando"
            class="tabela-aniversariantes"
            :items="lista"
            :fields="cabecalho"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            small
            hover
            outlined
            striped
            show-empty
          
            sort-icon-right
          >
            <template #empty>
              <h6>Nenhum registro a ser exibido.</h6>
            </template>

            <template #table-busy>
              <div class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Carregando Dados...</strong>
              </div>
            </template>

            <template #cell(data_nascimento)="data">
              {{ data.value.date | formatarData }}
            </template>
          </b-table>
        
      </div>
   
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import moment from "moment";

export default {
  name: "ListaRelatorioAniversariante",
  data() {
    return {
      mes: [],
      mes_selected: 1,
      sortBy: "data_nascimento",
      sortDesc: false,
      filtroVisivel: true,
      exportFields: {
        Nome: "nome_contato",
        "Data Nascimento": {
          field: "data_nascimento.date",
          callback: (value) => moment(value).format("MM/DD/YYYY"),
        },
      },
      situacaoOpcoes: [
        { text: "Ativo", value: "0" },
        { text: "Inativo", value: "1" },
        { text: "Todos" },
      ],
      mesOpcoes: [
        { text: "Mes Atual", value: "0" },
        { text: "Mes Seguinte", value: "1" },
        { text: "12 Meses", value: "3" },
      ],
      situacao_selecionada: 0,
      cabecalho: [
        { key: "id", label: "ID", sortable: false },
        { key: "nome_contato", label: "Nome", sortable: true },
        { key: "data_nascimento", label: "Data Nascimento", sortable: true },
      ],
    };
  },

  computed: {
    ...mapState("relatorioAniversariante", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
    this.filtros.mes = "0";
    this.filtros.situacao_selecionada = "0";
  },

  methods: {
    ...mapActions("relatorioAniversariante", ["listar"]),
    ...mapMutations("relatorioAniversariante", ["SET_LISTA", "SET_PARAMETROS"]),

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
        mes: form.mes || null,
        situacao: form.situacao_selecionada || null,
        turma: form.turma ? form.turma : null,
        mostrar_representantes: form.mostrar_representantes === true ? 1 : 0,
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
.tabela-aniversariantes >>> tr > th,
.tabela-aniversariantes >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-aniversariantes >>> table thead {
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
#tabela-aniversariantes {
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
