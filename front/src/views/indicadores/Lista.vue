<template>
   <div class="fadeIn w-100">
    <div class="filtro-avancado body-sector">
      <div
        class="d-flex justify-content-between filtro-header head-content-sector"
      >
        <div>
          <div
            class="filtro-selecionado btn"
            aria-controls="filtros-rapidos"
            aria-expanded="false"
            @click="
              (filtroRapido = !filtroRapido),
                (className = filtroRapido ? 'rapido-open' : null)
            "
          >
            Filtros
          </div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida = true">
          <div class="form-group row mb-0">
            <b-col md="2">
              <label
                v-help-hint="'filtro_rapido-ano_indicadores'"
                for="ano"
                class="col-form-label"
                >Ano</label
              >
              <g-select
                id="ano"
                v-model="ano"
                :options="listaAnos"
                class="multiselect-truncate"
                label="text"
              />
            </b-col>

            <b-col md="2">
              <label
                v-help-hint="'filtro_rapido-mes_indicadores'"
                for="mes"
                class="col-form-label"
                >Mês</label
              >
              <g-select
                id="mes"
                v-model="mes"
                :options="listaMeses"
                class="multiselect-truncate"
                label="text"
              />
            </b-col>

            <b-col v-if="mostrandoFranqueadora" md="2">
              <label
                v-help-hint="'filtro_rapido-estado_indicadores'"
                for="grupo"
                class="col-form-label"
                >Estado</label
              >
              <g-select
                id="grupo"
                v-model="grupo"
                :options="listaGrupos"
                label="nome"
                track-by="id"
              />
            </b-col>

            <b-col v-if="mostrandoFranqueadora" md="2">
              <label
                v-help-hint="'filtro_rapido-franquia_indicadores'"
                for="franqueada"
                class="col-form-label"
                >Franquia</label
              >
              <g-select
                id="franqueada"
                v-model="franqueada"
                :options="listaFranqueadas"
                :disabled="grupo.id === null"
                label="nome"
                track-by="id"
                @input="changeFranquia"
              />
            </b-col>

            <b-col v-if="!mostrandoFranqueadora" md="2">
              <label
                v-help-hint="'filtro_rapido-funcionario_indicadores'"
                for="franqueada"
                class="col-form-label"
                >Consultor</label
              >
              <g-select
                id="funcionario"
                v-model="funcionario"
                :options="listaFuncionarios"
                :disabled="franqueada.id === null"
                label="apelido"
                track-by="id"
              />
            </b-col>

            <b-col sm="4" md="">
              <b-btn
                variant="roxo"
                class="mt-4 btn-block text-uppercase"
                @click="filtrar()"
                >Buscar</b-btn
              >
            </b-col>
          </div>
        </form>
      </b-collapse>
    </div>
    <div class="tabela-wrapper" >
      <div class="table-responsive-sm">
        <template v-if="Object.keys(listaIndicadores).length">
          <div class="d-flex" style="border">
            <div class="fixed">
              <!-- Primeira coluna - indicadores -->
              <div class="side-cell cell-title">Indicadores</div>
              <div
                v-b-tooltip="item.descricao"
                v-for="item in listaIndicadores"
                :key="item.id"
                :style="{ color: item.cor_texto, backgroundColor: item.cor }"
                class="side-cell"
              >
                {{ item.descricao }}
              </div>
            </div>

            <div class="">
              <div class="d-flex">
                <!-- Primeira linha -> Títulos das colunas (ex.: Totais, 01/Sep, 02/Sep, etc...) -->
                <div class="cell cell-title">Totais</div>
                <div
                  v-for="(data, index) in diasExibidos"
                  :key="index"
                  class="cell cell-title"
                >
                  {{ data.format("DD/MMM") }}
                </div>
              </div>

              <!-- Cada listaIndicadores é uma linha -->
              <div
                v-for="item in listaIndicadores"
                :key="item.id"
                class="d-flex"
              >
                <!-- Primeira célula da linha -> valor total -->
                <div class="cell">{{ item.total }}</div>
                <!-- Demais células da linha -->
                <div
                  v-for="(data, index) in diasExibidos"
                  :key="index"
                  class="cell"
                >
                  {{ item.datas[data.format("YYYY-MM-DD")] }}
                </div>
              </div>
            </div>
          </div>
        </template>

        <template v-else>
          <p class="p-3 text-center">
            Use os filtros acima para buscar os indicadores
          </p>
        </template>
      </div>
    </div>
  </div>
 
</template>

<script>
import { mapState, mapActions } from "vuex";
import moment from "moment";

export default {
  data() {
    return {
      className: "rapido-open",
      buscaRapida: false,
      filtroRapido: true,
      mostrandoFranqueadora: false,

      grupo: { id: null, nome: "Selecione" },
      franqueada: { id: null, nome: "Selecione" },
      funcionario: { id: null, apelido: "Todos" },

      ano: { text: "Selecione", value: null },
      mes: { text: "Selecione", value: null },

      diasExibidos: [],
    };
  },

  computed: {
    ...mapState("estado", { listaEstados: "lista" }),
    ...mapState("indicadores", {
      listaIndicadores: "lista",
      estaCarregando: "estaCarregando",
    }),

    listaFranqueadas: {
      get() {
        const franquias =
          this.$store.state.root.usuarioLogado.franqueadas.filter((f) => {
            if (this.grupo) {
              return f.estado && f.estado.id === this.grupo.id;
            }

            return true;
          });

        return [{ id: null, nome: "Selecione" }].concat(franquias);
      },
    },

    listaFuncionarios: {
      get() {
        return [
          { id: null, apelido: "Todos" },
          ...this.$store.state.funcionario.lista,
        ];
      },
    },

    listaGrupos: {
      get() {
        return [{ id: null, nome: "Selecione" }, ...this.listaEstados];
      },
    },

    listaAnos: {
      get() {
        const year = new Date().getFullYear();
        let content = [];
        for (let i = 10; i >= 0; i--) {
          const data = year - i;
          content.push({ text: data, value: data });
        }
        return content;
      },
    },

    listaMeses: {
      get() {
        let content = [];
        for (let i = 0; i <= 11; i++) {
          content.push({
            text: moment().month(i).locale("pt").format("MMMM"),
            value: i + 1,
          });
        }
        return content;
      },
    },
  },

  watch: {
    grupo(value) {
      if (value.id !== null) {
        this.franqueada = { id: null, nome: "Selecione" };
        this.funcionario = { id: null, apelido: "Selecione" };
      }
    },
  },

  mounted() {
    const m = moment();
    this.ano = this.listaAnos.find((e) => e.text === Number(m.format("YYYY")));
    this.mes = this.listaMeses.find(
      (e) => e.text === m.locale("pt").format("MMMM")
    );

    const franqueada = this.$store.state.root.usuarioLogado.franqueadas.find(
      (f) => f.id === this.$store.state.root.usuarioLogado.franqueadaSelecionada
    );
    this.mostrandoFranqueadora = !!franqueada.franqueadora;

    if (!this.mostrandoFranqueadora) {
      this.franqueada = franqueada;
      this.changeFranquia();
    } else {
      this.listaEstadosRequisicao();
    }
  },

  methods: {
    ...mapActions("estado", { listaEstadosRequisicao: "listar" }),

    filtrar() {
      const filtros = {
        estado: this.grupo ? this.grupo.id : null,
        ano: this.ano.value,
        mes: this.mes.value,
        franqueada_personalizada: this.franqueada ? this.franqueada.id : null,
        funcionario: this.funcionario ? this.funcionario.id : null,
      };

      this.diasExibidos = [];
      this.$store.dispatch("indicadores/listar", filtros).then((response) => {
        let diaCorrente = moment(response.periodo[0]);
        const numeroDias = moment(response.periodo[1]).diff(
          diaCorrente,
          "days"
        );
        const dias = [];
        let i = 0;

        while (i < numeroDias) {
          dias.push(moment(diaCorrente));
          diaCorrente.add(1, "days");
          i++;
        }

        this.diasExibidos = dias;
      });
    },

    sortTable(response) {
      this.SET_ORDER_BY(response.detail);
      this.filtrar();
    },

    changeFranquia() {
      this.funcionario = { id: null, apelido: "Todos" };

      this.$store.commit("funcionario/SET_PAGINA_ATUAL", 1);
      this.$store.commit(
        "funcionario/setFranqueadaPersonalizada",
        this.franqueada.id
      );
      this.$store.commit("funcionario/SET_FILTROS", {
        consultor_ou_gestor_comercial: true,
      });
      this.$store.dispatch("funcionario/listar");
    },
  },
};
</script>

<style scoped>
.animated.fadeIn {
  height: calc(100vh - 58px);
}


.tabela-wrapper {
  width: calc(100vw - 271x);
  height: calc(100vh - 191px);
  overflow-x: scroll;
  overflow-y: scroll;
}

.tabela-wrapper::-webkit-scrollbar {
  width: 5px;
  height: 5px;
}

.tabela-wrapper::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.tabela-wrapper::-webkit-scrollbar-thumb {
  background: #888;
}

.side-cell {
  min-width: 200px;
  width: 200px;
  padding: 6px;
  border-bottom: 1px solid #ccc;
  border-right: 1px solid #ccc;
  border-left: 1px solid #ccc;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}

.fixed {
  position: sticky !important; /* Adiciona a propriedade position: sticky */
  left: 0 !important; /* Fixa a coluna à esquerda */
  z-index: 1 !important; /* Garante que a coluna fixa fique sobre o conteúdo rolável */
  background-color: #ccc;
}

.cell {
  min-width: 60px;
  width: 60px;
  text-align: center;
  padding: 6px 0;
  border-bottom: 1px solid #ccc;
  border-right: 1px solid #ccc;
}

.cell:nth-child(0) {
  border-left: 1px;
}

.cell-title {
  font-weight: bold;
  border-top: 1px solid #ccc;
}

body {
  overflow: hidden;
}

</style>
