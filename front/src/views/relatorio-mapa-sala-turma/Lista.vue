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
                    <div class="col-md-6">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <g-data
                        :periodo="'mes_anterior'"
                        @dataDe="filtros.dataDe = $event"
                        @dataAte="filtros.dataAte = $event"
                      >
                      </g-data>
                    </div>
                    <div class="col-md-3">
                      <label for="turmas" class="col-form-label">
                        Turmas
                      </label>
                      <g-select-turma id="turmas" v-model="filtros.turma">
                      </g-select-turma>
                    </div>

                    <div class="col-md-3">
                      <label
                        v-help-hint="'filtro-turma_sala_franqueada'"
                        for="sala_franqueada"
                        class="col-form-label"
                        >Sala</label
                      >
                      <g-select
                        id="sala_franqueada"
                        v-model="filtros.sala"
                        :options="listaSalasFranqueada"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </div>
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
                    name="relatorio-matriculas"
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
      small
      id="tabela-mapa-sala-turma"
      class="tabela-mapa-sala-turma"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :items="lista"
    >
    </b-table>

  </div>
  <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
    <p>Nenhum resultado encontrado.</p>
</div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToCompare } from "../../utils/date";

export default {
  name: "ListaRelatorioMapaSalaTurma",
  data() {
    return {
      aviso: "",
      data_inicial: "",
      data_final: "",
      filtroVisivel: false,
      listaItens: {},
      sortDesc: false,
      fields: [
        { key: "horario_inicio", label: "Horário", sortable: true },
        { key: "dia_semana", label: "Dia da Semana", sortable: true },
        { key: "sala", label: "Sala", sortable: true },
        { key: "turma", label: "Turma", sortable: true },
      ],
      exportFields: {
        "Horário Inicio": "horario_inicio",
        "Dia da Semana": "dia_semana",
        Sala: "sala",
        Turma: "turma",
      },
    };
  },

  computed: {
    ...mapState("relatorioMapaSalaTurma", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),

    ...mapState("salaFranqueada", { listaSalasFranqueada: "lista" }),

    listaSalasFranqueada: {
      get() {
        return [{ id: null, descricao: "Selecione" }].concat(
          this.$store.state.salaFranqueada.lista.filter((item) => {
            let arrDescricao = item.descricao.split(" ");

            if (arrDescricao.indexOf("Personal") >= 0) {
              return false;
            }

            return true;
          })
        );
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.$store.commit("salaFranqueada/SET_PAGINA_ATUAL", 1);
    this.$store.commit("salaFranqueada/SET_FILTRO_APENAS_SALA_ATIVA", true);
    this.$store.dispatch("salaFranqueada/listar");
  },

  methods: {
    dateToCompare: dateToCompare,

    ...mapActions("relatorioMapaSalaTurma", { MapaSala: "listar" }),
    ...mapMutations("relatorioMapaSalaTurma", ["SET_LISTA", "SET_PARAMETROS"]),

    podeGerarRelatorio() {
      return true;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);

      this.MapaSala();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        sala: form.sala ? form.sala.id : null,
        turma: form.turma || null,
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

.tabela-mapa-sala-turma >>> tr > th,
.tabela-mapa-sala-turma >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
}

.tabela-mapa-sala-turma >>> table thead {
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

#tabela-mapa-sala-turma {
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
