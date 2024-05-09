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
                    <div class="col-md-4">
                      <label
                        v-help-hint="''"
                        for="nome_funcionario"
                        class="col-form-label"
                        >Funcionário</label
                      >
                      <g-select
                        :value="filtros.funcionario"
                        :select="setFuncionario"
                        :options="listaFuncionario"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
                      />
                    </div>
                    <div class="col-md-4">
                      <label for="cargo" class="col-form-label">Cargo</label>
                      <g-select
                        :value="filtros.cargo"
                        :select="setCargo"
                        :options="listaDeCargo"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
                      />
                    </div>
                    <div class="col-md-4">
                      <label
                        v-help-hint="''"
                        for="situacao"
                        class="col-form-label"
                        >Situação</label
                      >
                      <div class="d-block">
                        <b-form-checkbox-group
                          id="situacao"
                          v-model="filtros.situacao"
                          :options="listaSituacao"
                          buttons
                          button-variant="cinza"
                          class="checkbtn-line"
                          @change="setSituacao"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-4">
                      <label
                        v-help-hint="''"
                        for="data_de_cadastro"
                        class="col-form-label"
                        >Data de Cadastro</label
                      >
                      <g-data
                        :periodo="'mes_anterior'"
                        @dataDe="filtros.data_inicial = $event"
                        @dataAte="filtros.data_final = $event"
                      />
                    </div>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end">
                <div class="col-md-auto" v-if="lista && lista.length">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="lista && lista.length">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="lista"
                    :fields="exportFields"
                    type="xls"
                    name="relatorio-informacoes-funcionarios"
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
      id="tabela-informacoes-funcionarios"
      class="tabela-informacoes-funcionarios"
      v-if="lista && !estaCarregando"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :items="lista"
      
    >
    <template #empty>
          <h6>Nenhum registro a ser exibido.</h6>
        </template>
      <template #cell(data_cadastramento)="data">
        {{ data.value | formatarData }}
      </template>
      <template #cell(estado_civil)="row">
        <span>{{ converterEstadoCivil(row.value) }}</span>
      </template>
      <template #cell(situacao)="row">
        <span>{{ converterSituacao(row.value) }}</span>
      </template>
      <template #cell(sexo)="data"  >
        <span v-b-tooltip.hover :title="data.value === 'M' ? 'Masculino' : (data.value === 'F' ? 'Feminino' : 'Não definido')">
        {{ data.value }}
      </span>
      </template>
       <template #cell(nome_contato)="data">
        {{ data.value + (data.item.nome ? " / " + data.item.nome : "") }}
      </template>
    </b-table>
  </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  name: "ListaInformacoesFuncionario",
  data() {
    return {
      sortBy: "nome_contato",
      sortDesc: false,
      filtroVisivel: true,
      listaSituacao: [
        { text: "Todos", value: "T" },
        { text: "Ativos", value: "A" },
        { text: "Inativos", value: "I" },
      ],
      fields: [
        { key: "nome_contato", sortable: true, label: "Nome/Usuário" },
        { key: "cargo_funcionario", sortable: true, label: "Cargo" },
        { key: "sexo", sortable: false, label: "Sexo" },
        { key: "cnpj_cpf", sortable: true, label: "CPF" },
        { key: "numero_identidade", sortable: true, label: "RG" },
        { key: "estado_civil", sortable: true, label: "Estado Civil" },
        { key: "telefone_preferencial", sortable: true, label: "Telefone" },
        { key: "endereco_completo", sortable: true, label: "Endereço" },
        { key: "situacao", sortable: true, label: "Situação" },
        { key: "data_cadastramento", sortable: true, label: "Data Cadastro" },
      ],
      exportFields: {
        'Nome': 'nome_contato',
        'Usuário': 'nome',
        'Sexo': {
          field: 'sexo',
          callback: (value) => value == 'F' ? 'Feminino' : 'Masculino'
        },
        'CPF': 'cnpj_cpf',
        'RG': 'numero_identidade',
        "Estado Civil": {
          field : 'estado_civil',
          callback: (value) => value == 'S' ? 'Solteiro' : (value == 'C' ? 'Casado' : (value == 'D' ? 'Divorciado' : ''))
        },
        'Telefone': 'telefone_preferencial',
        'Endereço': 'endereco_completo',
        'situacao': {
          field: 'situacao',
          callback: (value) => value == 'A' ? 'Ativo' : 'Inativo'
        },
        'Data Cadastramento' : 'data_cadastramento'
      },
    };
  },

  computed: {
    ...mapState("relatorioInformacoesFuncionarios", {
      lista: "lista",
      estaCarregando: "estaCarregando",
      totalItens: "totalItens",
      filtros: "filtros",
    }),
    ...mapState("funcionario", { listaFuncionarioRequisicao: "lista" }),
    ...mapState("cargo", { listaCargoRequisicao: "lista" }),

    listaFuncionario: {
      get() {
        let funcionarios = [...this.listaFuncionarioRequisicao].sort((a, b) => (a.apelido > b.apelido ? 1 : -1))
        return [
          { id: null, apelido: "Selecione" },
          ...funcionarios,
        ];
      },
    },

    listaDeCargo: {
      get() {
        return [
          { id: null, descricao: "Selecione" },
          ...this.listaCargoRequisicao,
        ];
      },
    },
  },

  mounted() {
    this.listarCamposSelects();
    this.SET_LISTA([]);
  },

  methods: {
    ...mapActions("relatorioInformacoesFuncionarios", { listar: "listar" }),
    ...mapMutations("relatorioInformacoesFuncionarios", [
      "SET_LISTA",
      "SET_PAGINA_ATUAL",
      "SET_ITEM_SELECIONADO",
      "SET_PARAMETROS",
    ]),
    ...mapActions("funcionario", { listarFuncionario: "listar" }),
    ...mapActions("cargo", { listarCargo: "listar" }),

    converterSituacao(situacao) {
      const valores = {
        A: "Ativo",
        I: "Inativo",
      };
      return valores[situacao];
    },
       
    converterEstadoCivil(estadoCivil) {
      const valores = {
        S: "Solteiro(a)",
        C: "Casado(a)",
        D: "Divorciado(a)",
        N: "Não informado",
      };
      return valores[estadoCivil];
    },
    listarCamposSelects() {
      this.listarFuncionario();
      this.listarCargo();
    },

    setFuncionario(value) {
      this.filtros.funcionario = value;
    },

    setCargo(value) {
      this.filtros.cargo = value;
    },

    setSituacao(value) {
      this.filtros.situacao = value;
    },

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
        funcionario: form.funcionario ? form.funcionario.id : null,
        cargo: form.cargo ? form.cargo.id : null,
        situacao: form.situacao.length > 0 ? form.situacao : null,
        data_inicial: form.data_inicial || null,
        data_final: form.data_final || null
      };

      let dadosArray = [];
      for (let key in dados) {
        if (dados[key] !== null) {
          dadosArray.push(`${key}=${dados[key]}`);
        }
      }

      return dadosArray.join("&");
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
.tabela-informacoes-funcionarios >>> tr > th,
.tabela-informacoes-funcionarios >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-informacoes-funcionarios >>> table thead {
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
#tabela-informacoes-funcionarios {
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