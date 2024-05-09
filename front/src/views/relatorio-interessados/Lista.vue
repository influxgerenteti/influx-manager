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
                      <label for="periodo_pretendido" class="col-form-label"
                        >Período pretendido</label
                      >
                      <g-select
                        id="periodo_pretendido"
                        :select="setPeriodoPretendido"
                        :options="listaPeriodoPretendido"
                        v-model="filtros.periodoPretendido"
                        class="multiselect-truncate valid-input"
                        label="descricao"
                        track-by="id"
                      />
                    </div>

                    <div class="col-md-3">
                      <label for="idioma" class="col-form-label">Idioma</label>
                      <g-select-idioma v-model="filtros.idioma"/>
                    
                    </div>

                    <div class="col-md-3">
                      <label for="turma" class="col-form-label">Livro</label>
                     <g-select-livro v-model="filtros.livro"/>
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
                    name="relatorio-interessados"
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
                    >Gerar relatório</b-btn
                  >
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
    small
        hover
        outlined
        striped
        show-empty
        fixed-header
        sort-icon-right
      id="tabela-interessados"
      class="tabela-interessados"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :items="lista"
      >
      <template #empty >
          <h6 class="text-center">Nenhum registro a ser exibido.</h6>
        </template>
      <template #cell(nome)="data">
        {{ data.value }}
      </template>
      <template #cell(idade)="data">
        <div >
        {{ data.value ? data.value : "--" }}
        </div>
      </template>
      <template #cell(descricao)="data">
        {{ data.value ? data.value : "--" }}
      </template>
      <template #cell(periodo_pretendido)="data">
        {{ data.value ? (data.value == "M" ? "Manhã" : data.value == "T" ? "Tarde" : data.value == "N" ? "Noite" : "Sábado") : "--" }}
      </template>
      <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Carregando Dados...</strong>
          </div>
        </template>
    </b-table>
  </div>

</div>
</template>
<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToCompare } from "../../utils/date";

export default {
  name: "ListaRelatorioInteressados",
  data() {
    return {
      filtroVisivel: true,
      exportFields: {
        'Nome' : 'nome',
        'Idade' : 'idade',
        'Livro' : 'descricao',
        'Período' : {
          field : 'periodo_pretendido',
          callback: (value) => value ? (value == "M" ? "Manhã" : value == "T" ? "Tarde" : value == "N" ? "Noite" : "Sábado") : null
        }
      },
      sortDesc: false,
      periodoPretendidoFiltroRapido: "",
      listaPeriodoPretendido: [
        { id: null, descricao: "Selecione", value: null },
        { id: 1, descricao: "Manhã", value: "M" },
        { id: 2, descricao: "Tarde", value: "T" },
        { id: 3, descricao: "Noite", value: "N" },
        { id: 4, descricao: "Sábado", value: "S" },
      ],
      cabecalho : [
        { key : 'nome', label: 'Nome', sortable: true },
        { key : 'idade', label: 'Idade', sortable: true },
        { key : 'descricao', label: 'Livro', sortable: true },
        { key : 'periodo_pretendido', label: 'Período', sortable: true }
      ],
      fields: [
        { key: "nome", sortable: true },
        { key: "idade", sortable: true },
        { key: "descricao", label: 'Livro', sortable: true },
        { key: "periodo_pretendido", label: 'Período', sortable: true }
      ]      
    };
  },

  computed: {
    ...mapState("relatorioInteressados", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    dateToCompare: dateToCompare,
    ...mapMutations("relatorioInteressados", ["SET_LISTA"]),
    ...mapMutations("relatorioInteressados", ["SET_PARAMETROS"]),
    ...mapActions("relatorioInteressados", {listarInteressados: "listar"}),

    
    podeGerarRelatorio() {
      return true;
    },

    setPeriodoPretendido(value) {
      this.periodoPretendidoFiltroRapido = value;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink()
      this.SET_PARAMETROS(parametros)
      this.listarInteressados()
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        periodo_pretendido: form.periodoPretendido
          ? form.periodoPretendido.value
          : null,
        idioma: form.idioma ? form.idioma : null,
        livro: form.livro ? form.livro : null    
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


.table {
  margin-bottom: 0 !important;
}
.tabela-wrapper {
  overflow: scroll;

}
.tabela-wrapper::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
/* .fadeIn {
  max-height: 90vh;
  max-width: 100vw;

} */

.tabela-wrapper::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.tabela-wrapper::-webkit-scrollbar-thumb {
  background: #888;
}

.tabela-interessados >>> tr > th,
.tabela-interessados >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
}

.tabela-interessados >>> table thead {
  position: sticky;
  top: -1px;
}

.container-fluid{
  overflow: hidden;
}

@media (max-width: 992px) {
  .tabela-wrapper {
    margin-bottom: 10%;
}
}
@media print {
  #tabela-interessados {
    overflow: visible;
  }
  .table-area {
    max-height: 100%;
    overflow: visible;
  }
}

</style>