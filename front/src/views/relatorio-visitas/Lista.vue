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
                  <div class="row">
                    <div class="col-md-3">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_balancete_periodo'
                        "
                        for="data_inicial"
                        class="col-form-label"
                        >Período</label
                      >
                      <div class="row">
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">De</div>
                            </div>
                            <g-datepicker
                              v-model="data_inicial"
                              :element-id="'data_inicial'"
                              :value="filtros.data_inicial"
                              :selected="setPeriodoDe"
                            />
                          </div>
                        </div>
                        <div class="col">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">à</div>
                            </div>
                            <g-datepicker
                              v-model="data_final"
                              :element-id="'data_final'"
                              :value="filtros.data_final"
                              :selected="setPeriodoAte"
                            />
                          </div>
                        </div>
                      </div>

                      <div v-if="aviso" class="floating-message bg-danger">
                        {{ aviso }}
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div>
                        <label for="consultor" class="col-form-label"
                          >Consultor</label
                        >
                        <g-select-consultor
                          id="consultor"
                          v-model="filtros.consultor"
                          class="valid-input"
                        />
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="col-md-auto">
                        <label
                          v-help-hint="'filtro-relatorio-cheques_tipo'"
                          for="tipo"
                          class="col-form-label d-block"
                          >Tipo de Contato</label
                        >
                        <b-form-checkbox-group
                          id="tipo"
                          v-model="filtros.tipoContato"
                          :options="listaTipoContato"
                          buttons
                          button-variant="cinza"
                          name="tipo"
                          class="checkbtn-line"
                        />
                      </div>
                    </div>
                  </div>
                  <br />
                  <div class="form-group row">
                    <div class="col-md-6">
                      <b-form-group label="Opções de impressão">
                        <b-form-radio
                          v-for="option in options"
                          v-model="selectedCheck"
                          :key="option.value"
                          :value="option.value"
                        >
                          {{ option.text }}
                        </b-form-radio>
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
                    :data="selectedCheck == 'por_consultor' ? resumo : lista"
                    :fields="(selectedCheck == 'detalhado' ? exportFieldsDetalhada : (selectedCheck == 'resumido' ? exportFieldsResumido : (selectedCheck == 'por_consultor' ? exportFields : {})))"
                    type="xls"
                    name="relatorio-visitas"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
                </div>
                <!-- <div class="col-md-auto" v-if="selectedCheck == 'resumido'">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFieldsResumido"
                    type="xls"
                    name="relatorio-visitas"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
                </div>
                <div
                  class="col-md-auto"
                  v-if="selectedCheck == 'por_consultor'"
                >
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="resumo"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-visitas"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
                </div> -->
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
    <div class="wrapper-table-scroll">
    <div v-if="lista.length || resumo.length">
      <div v-if="selectedCheck == 'por_consultor'">
        <comp-resumo-consultor></comp-resumo-consultor>
      </div>
      <div v-if="selectedCheck == 'detalhado'">
        <comp-visitas-detalhado></comp-visitas-detalhado>
      </div>
      <div v-if="selectedCheck == 'resumido'">
        <comp-visitas-resumido></comp-visitas-resumido>
      </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToCompare } from "../../utils/date";
import CompResumoConsultorVue from "./comp-resumo-consultor.vue";
import CompVisitasDetalhadoVue from "./comp-visitas-detalhado.vue";
import CompVisitasRsumidoVue from "./comp-visitas-resumido.vue";
import moment from "moment";
import Vue from "vue";

Vue.component("comp-resumo-consultor", CompResumoConsultorVue);
Vue.component("comp-visitas-detalhado", CompVisitasDetalhadoVue);
Vue.component("comp-visitas-resumido", CompVisitasRsumidoVue);

export default {
  name: "ListaRelatorioVisitas",
  data() {
    return {
      selected: 0,
      aviso: "",
      data_final: "",
      data_inicial: "",
      filtroVisivel: true,
      tipoContato: [],
      listaTipoContato: [
        { text: "Ativo", value: "A" },
        { text: "Receptivo", value: "R" },
      ],
      selectedCheck: "por_consultor",
      options: [
        { text: "Por Consultor", value: "por_consultor" },
        { text: "Resumido", value: "resumido" },
        { text: "Detalhado", value: "detalhado" },
      ],
      exportFields: {
        Consultor: "consultor",
        Efetivos: "efetivos",
        Retornos: "retornos",
        "Retornos Efetivos": "retornosEfetivos" 
      },
      exportFieldsDetalhada: {
        Consultor: "consultor",
        Data: {
          field: "data",
          callback: (value) => moment(value).format("DD/MM/YYYY"),
        },
        Interessado: "interessado",
        Contato: "nome_contato",
        Situação: {
          field: "situacao",
          callback: (value) =>
            value == "A"
              ? "Aberto"
              : value == "C"
              ? "Convertido"
              : value == "I"
              ? "Inativo"
              : value == "P"
              ? "Perdido"
              : "",
        },
        Tipo: {
          field: "tipo",
          callback: (value) =>
            value == "A" ? "Ativo" : value == "R" ? "Recepitivo" : "--",
        },
      },
      exportFieldsResumido: {
        Consultor: "consultor",
        Contato: "nome_contato",
        Situação: {
          field: "situacao",
          callback: (value) =>
            value == "A"
              ? "Aberto"
              : value == "C"
              ? "Convertido"
              : value == "I"
              ? "Inativo"
              : value == "P"
              ? "Perdido"
              : "",
        },
        Tipo: {
          field: "tipo",
          callback: (value) =>
            value == "A" ? "Ativo" : value == "R" ? "Recepitivo" : "--",
        },
      },
    };
  },

  computed: {
    ...mapState("relatorioVisitas", [
      "filtros",
      "lista",
      "estaCarregando",
      "resumo",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
    this.SET_RESUMO([]);
  },

  methods: {
    ...mapActions("relatorioVisitas", { listarVisitas: "listar" }),
    ...mapMutations("relatorioVisitas", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "SET_RESUMO",
    ]),

    podeGerarRelatorio() {
      return true;
    },

    setPeriodoDe(value) {
      this.aviso = "";
      this.filtros.data_inicial = value;

      if (this.filtros.data_inicial !== "") {
        const arData = this.filtros.data_inicial.split("/");
        arData[2] = String(parseInt(arData[2]) + 1);

        let dataFinal = arData.join("/");
      }
    },
    setPeriodoAte(value) {
      this.aviso = "";
      this.filtros.data_final = value;

      if (dateToCompare(value) < dateToCompare(this.filtros.data_inicial)) {
        this.aviso = ` Data ${value} não pode ser colocada, data inicial deve ser menor que a data final!`;
      }

      if (value === "") {
        this.aviso = "";
      }
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listarVisitas();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null,
        consultor: form.consultor ? form.consultor : null,
        tipo_contato:
          form.tipoContato && form.tipoContato.length
            ? form.tipoContato.join(",")
            : null,
        // porConsultor: form.porConsultor === true ? 1 : 0,  DEVEM SER TRATADOS DENTRO DO FRONT
        // detalhado: form.detalhado === true ? 1 : 0,        DEVEM SER TRATADOS DENTRO DO FRONT
        // resumido: form.resumido === true ? 1 : 0,          DEVEM SER TRATADOS DENTRO DO FRONT
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
.wrapper-table-scroll{
  margin-bottom: 8%;
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
.table-scroll {
  overflow: hidden;
}
</style>
