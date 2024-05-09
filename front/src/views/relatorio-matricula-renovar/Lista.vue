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
                <b-col md="6">
                      <label for="tipo_turma" class="col-form-label">Tipo de turma</label>
                      <div>
                        <b-form-checkbox-group 
                            id="tipo_turma" 
                            v-model="tipo_turma"
                            :options="listaTipoTurma" 
                            buttons 
                            button-variant="cinza" 
                            name="tipo_turma" 
                            class="checkbtn-line pb-1"
                        />
                      </div>
                    </b-col>
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
                    name="relatorio-matriculas"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
                </div>
                <div class="col-md-2">
                  <b-btn
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
      striped hover
      id="tabela-matriculas-renovar"
      class="tabela-matriculas-renovar"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="lista"
    >
    <template #cell(data_contrato)="data">
      {{ data.value | formatarData }}
    </template>
    <template #cell(data_inicio_contrato)="data">
      {{ data.value | formatarData }}
    </template>
    <template #cell(data_termino_contrato)="data">
      {{ data.value | formatarData }}
    </template>
    
      <!-- <thead>
        <tr>
          <th>Num Contrato</th>
          <th>Nome Contato</th>
          <th>Telefone</th>
          <th>E-mail</th>
          <th>Turma</th>
          <th>Contrato Feito</th>
          <th>Contrato Início</th>
          <th>Contrato Fim</th>
        </tr>
      </thead>
      <tbody>
        <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
          <p>Nenhum resultado encontrado.</p>
        </div>
        <tr
          v-for="item in lista"
          :key="item.contrato_id"
          class="item-matricula"
        >
            <td>
              <span>{{ item.contrato }}</span>
            </td>
            <td>
              <span>{{ item.nome_contato }}</span>
            </td>
            <td>
              <span>{{ item.telefone_preferencial }}</span>
            </td>
            <td>
              <span>{{ item.email }}</span>
            </td>
            <td>
              <span>{{ item.turma }}</span>
            </td>
            <td>
              <span>{{ item.data_contrato }}</span>
            </td>
            <td>
              <span>{{ item.data_inicio_contrato }}</span>
            </td>
            <td>
              <span>{{ item.data_termino_contrato }}</span>
            </td>
        </tr>
      </tbody> -->
    </b-table>
  </div>
  </div>
</template>

<script>
import relatorioTurma from '@/store/relatorio-turma';
import { mapActions, mapMutations, mapState } from "vuex";
import open from "../../utils/open";

export default {
  name: "ListaRelatorioMatriculaRenovar",

  data() {
    return {
      sortBy: 'data_contrato',
      sortDesc: false,
      fields: [
        { key: 'contrato', sortable: true },
        { key: 'nome_contato', sortable: true },
        { key: 'telefone_preferencial', sortable: false },
        { key: 'email', sortable: false },
        { key: 'turma', sortable: true },
        { key: 'data_contrato', sortable: true },
        { key: 'data_inicio_contrato', sortable: true },
        { key: 'data_termino_contrato', sortable: true }
      ],
      tipo_turma: [],
      transProps: {
        // Transition name
        name: "flip-list",
      },
      items: [
        { a: 2, b: "Two", c: "Moose" },
        { a: 1, b: "Three", c: "Dog" },
        { a: 3, b: "Four", c: "Cat" },
        { a: 4, b: "One", c: "Mouse" },
      ],
      // fields: [
      //   { key: "a", sortable: true },
      //   { key: "b", sortable: true },
      //   { key: "c", sortable: true },
      // ],
      listaTipoTurma: [
        { value: "HYB", text: "Hybrid" },
        { value: "TUR", text: "Regular" },
        { value: "VIP", text: "VIP" },
        { value: "PER", text: "Personal" },
      ],
      exportFields: {
          'Num Contrato' : 'contrato',
          'Nome' : 'nome_contato',
          'Telefone' : 'telefone_preferencial',
          'E-mail' : 'email',
          'Turma' : 'turma',
          'Contrato' : 'data_contrato',
          'Contrato Início' : 'data_inicio_contrato',
          'Contrato Fim' : 'data_termino_contrato'
      },
      filtroVisivel: true,
    };
  },

  computed: {
    ...mapState("relatorioTurma", ["filtros"]),
    ...mapState("relatorioMatriculaRenovar", ["lista", "estaCarregando"]),
  },

  mounted() {},

  methods: {
    ...mapActions("relatorioMatriculaRenovar", { listarMatriculas: "listar" }),
    ...mapMutations("relatorioMatriculaRenovar", ["SET_PARAMETROS"]),

    podeGerarRelatorio() {
      return this.lista.length;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros)
      this.listarMatriculas();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        tipo_turma: this.tipo_turma ? this.tipo_turma : null,
        situacaoMatricula: form.situacaoMatricula ? form.situacaoMatricula : null

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

.tabela-matriculas-renovar >>> tr > th,
.tabela-matriculas-renovar >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}

.tabela-matriculas-renovar >>> table thead {
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

#tabela-matriculas-renovar {
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
