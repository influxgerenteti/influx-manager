<template>
  <div class="animated fadeIn wrapper-table-scroll" style="overflow: hidden;">
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
                    <b-col class="col-md-auto">
                      <label
                        v-help-hint="'filtro-relatorio-retorno-consultor'"
                        for="tipo"
                        class="col-form-label d-block"
                        >Tipo de Contato</label
                      >
                      <b-form-checkbox-group
                        id="tipo"
                        v-model="filtros.tipo_contato"
                        :options="listaTipo"
                        buttons
                        button-variant="cinza"
                        name="tipo"
                        class="checkbtn-line"
                      />
                    </b-col>

                    <b-col md="3">
                      <label for="consultor" class="col-form-label">
                        Consultor
                      </label>
                      <g-select-consultor
                        id="consultor"
                        v-model="filtros.consultor"
                      >
                      </g-select-consultor>
                    </b-col>

                    <b-col class="col-md-6">
                      <label
                        v-help-hint="
                          'filtro_avancado_relatorio_retorno_consultor'
                        "
                        for="data_inicial"
                        class="col-form-label"
                      >
                        Período
                      </label>
                      <g-data
                        @dataDe="filtros.data_de = $event"
                        @dataAte="filtros.data_ate = $event"
                        @dataValida="dataValida = $event"
                      />
                    </b-col>
                  </div>


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
                         <div class="mb-2 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="selectedCheck == 'resumido' ? resumo : lista"
                    :fields="(selectedCheck == 'resumido' ? exportFieldsResumido :
                      (selectedCheck == 'por_consultor' ? exportFields : {}))"
                    type="xls"
                    name="relatorio-retorno-consultor"
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
             </div>
            </b-card-text>
          </b-tab>
        </b-tabs>
      </b-card>
    </div>
    <div v-if="estaCarregando" class="d-flex h-100">
      <load-placeholder :loading="estaCarregando" />
    </div>
    <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
      <p>Nenhum resultado encontrado.</p>
    </div>
    <div v-if="lista.length || resumo.length">
      <div v-if="selectedCheck == 'por_consultor'">
        <comp-retorno-consultor></comp-retorno-consultor>
      </div>
         <div v-if="selectedCheck == 'resumido'">
     
        <comp-retorno-resumido></comp-retorno-resumido>
      </div>
      
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import CompRetornoResumido from "./comp-retorno-resumido.vue";
import CompRetornoConsultor from "./comp-retorno-consultor.vue";
import Vue from "vue";
import moment from "moment";

Vue.component("comp-retorno-consultor", CompRetornoConsultor)
Vue.component("comp-retorno-resumido", CompRetornoResumido);
export default {
  name: "ListaRelatorioRetornoConsultor",
  data() {
    return {
      filtroVisivel: true,
      dataValida: true,
     
      listaTipo: [
        { text: "Ativo", value: "A" },
        { text: "Receptivo", value: "R" },
      ],
      selectedCheck: "por_consultor",
      options: [
        { text: "Por Consultor", value: "por_consultor" },
        { text: "Resumido", value: "resumido" },
   
      ],
   
      exportFieldsResumido: {
        Consultor: "nome_contato",
        "Retornos Agendados": 'efetivo',
         Retornos: 'retorno',
        "Retorno efetivo %": 'retornosEfetivo'
      },
    
      exportFields: {
        Consultor: "nome_contato",
        Data: {
          field: "data_cadastro",
          callback: (value) => moment(value).format("DD/MM/YYYY"),
        },
        Nome: "nome",
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
          field: "tipo_contato",
          callback: (value) =>
            value == "A" ? "Ativo" : "Receptivo" ,
        },
      },


    };
  },

  computed: {
    ...mapState("relatorioRetornoConsultor", [
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
    ...mapActions("relatorioRetornoConsultor", { listarRetorno: "listar" }),
    ...mapMutations("relatorioRetornoConsultor", [
    "SET_LISTA",
      "SET_PARAMETROS",
      "SET_RESUMO",
    ]),


    podeGerarRelatorio() {
      if (!this.dataValida) {
        return false;
      }
      return true;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listarRetorno();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        data_inicial: form.data_de ? form.data_de : null,
        data_final: form.data_ate ? form.data_ate : null,
        consultor: form.consultor || null,
        tipo_contato: form.tipo_contato || null,
  
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

</style>
