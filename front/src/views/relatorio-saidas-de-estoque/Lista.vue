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
                    <b-col md="4">
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
                    </b-col>
                    <b-col md="4">
                      <label
                        v-help-hint="'filtroRapido_busca_empresa'"
                        for="busca_empresa"
                        class="col-form-label"
                        >Empresa</label
                      >
                      <typeahead
                        id="busca_empresa"
                        :item-hit="setEmpresaId"
                        source-path="/api/pessoa/buscar/empresa"
                        key-name="nome_contato"
                      />
                    </b-col>
                    <b-col md="4">
                      <label
                        v-help-hint=""
                        for="data_saida"
                        class="col-form-label"
                        >Data Emissão</label
                      >
                      <g-data
                        @dataDe="filtros.data_saida_de = $event"
                        @dataAte="filtros.data_saida_ate = $event"
                      ></g-data>
                    </b-col>
                  </div>
                  <div class="form-group row">
                    <b-col md="4">
                      <label
                        v-help-hint=""
                        for="data_entrega"
                        class="col-form-label"
                        >Data de Entrega</label
                      >
                      <g-data
                        @dataDe="filtros.data_entrega_de = $event"
                        @dataAte="filtros.data_entrega_ate = $event"
                      ></g-data>
                    </b-col>
                    <b-col md="4">
                      <label
                        v-help-hint="'filtroAvancado-item_produto'"
                        for="item_produto"
                        class="col-form-label"
                        >Item</label
                      >
                      <typeahead
                        id="item_produto"
                        :item-hit="setItemDescricao"
                        source-path="/api/item_produto/buscar_descricao"
                        key-name="corpo.descricao"
                      />
                    </b-col>
                    <b-col md="4">
                      <label
                        v-help-hint="'filtroAvancado-item_produto'"
                        for="item_produto"
                        class="col-form-label"
                        >Usuário Entregador</label
                      >
                      <typeahead
                        id="item_produto"
                        :item-hit="setUsuarioDescricao"
                        source-path="/api/usuario/buscar_nome"
                        key-name="corpo.nome"
                      />
                    </b-col>
                  </div>
                  <div class="form-group row">
                    <b-col md="4">
                      <label
                        v-help-hint="'filtroRapido_busca_vendedor'"
                        for="busca_vendedor"
                        class="col-form-label"
                        >Funcionário/Vendedor</label
                      >
                      <typeahead
                        id="item_produto"
                        :item-hit="setFuncionarioId"
                        source-path="/api/funcionario/buscar_nome_contato"
                        key-name="pessoa.nome_contato"
                      />
                    </b-col>
                    <b-col md="4">
                      <label
                        v-help-hint="
                          'lista-entrega-material_situacao_filtro_avancado'
                        "
                        for="situacao_filtro_avancado"
                        class="col-form-label"
                        >Situação</label
                      >
                      <div class="d-block">
                        <b-form-checkbox-group
                          id="situacao_filtro_avancado"
                          v-model="selected"
                          :options="situacao"
                          buttons
                          button-variant="cinza"
                          class="checkbtn-line"
                          name="situacao_filtro_avancado"
                        />
                      </div>
                    </b-col>
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
                    name="relatorio-saidas-de-estoque"
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
        id="tabela-saida-estoque"
        class="tabela-saida-estoque"
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(data_emissao)="row">
          <span v-if="row.value">{{ row.value | formatarData }}</span>
          <span v-if="!row.value">{{ "" }}</span>
        </template>
        <template #cell(data_entrega)="row">
          <span v-if="row.value">{{ row.value | formatarData }}</span>
          <span v-if="!row.value">{{ "" }}</span>
        </template>
        <template #cell(valor)="row">
          <span v-if="row.value">{{ row.value | formatarMoeda(true) }}</span>
          <span v-if="!row.value">{{ "--" }}</span>
        </template>
        <template #cell(situacao_entrega)="row">
          <span v-if="row.value">{{ row.value == 'E' ? ('Entregue') :  row.value == 'C' ? 'Cancelado' : 'Não Entregue' }}</span>
          <span v-if="!row.value">{{ "--" }}</span>
        </template>
        <template #cell(usuario_entregue)="row">
          <span v-if="row.value">{{ row.value }}</span>
          <span v-if="!row.value">{{ "--" }}</span>
        </template>

      </b-table>
    </div>
    <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
      <p>Nenhum resultado encontrado.</p>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import moment from "moment";

export default {
  name: "ListaRelatorioSaidasDeEstoque",
  data() {
    return {
      filtroVisivel: true,
      selected: [],
      situacao: [
        { text: "Entregue", value: "E" },
        { text: "Não Entregue", value: "N" },
        { text: "Cancelado", value: "C" },
      ],
      sortBy: "Sacado",
      sortDesc: false,
      fields: [
        { key: "sacado", sortable: true, label: "Sacado" },
        { key: "item", sortable: true, label: "Item" },
        { key: "quantidade", sortable: true, label: "Quantidade" },
        { key: "valor", sortable: true, label: "Valor" },
        { key: "data_emissao", sortable: true, label: "Data Emissão" },
        { key: "data_entrega", sortable: true, label: "Data Entrega" },
        { key: "situacao_entrega", sortable: true, label: "Situação" },
        { key: "usuario_entregue", sortable: true, label: "Usuário Entregue" },
      ],
      exportFields: {
        Sacado: "sacado",
        Item: "item",
        Quantidade: "quantidade",
        Valor: "valor",
        "Data Emissão": {
          field: "data_emissao",
          callback: (value) => value != '' ? moment(value).format("DD/MM/YYYY") : '',
        },
        "Data Entrega": {
          field: "data_entrega",
          callback: (value) => value != '' ? moment(value).format("DD/MM/YYYY") : '',
        },
        Situação: { field: "situacao_entrega",
        callback: (value)  => (value == 'E' ? 'Entregue' : value == 'N' ? 'Não Entregue' : value == 'C' ? 'Cancelado' : '-')    
        },
        'Usuário Entregue': "usuario_entregue",
      },
    };
  },

  computed: {
    ...mapState("relatorioSaidasEstoque", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    ...mapActions("relatorioSaidasEstoque", ["listar"]),
    ...mapMutations("relatorioSaidasEstoque", ["SET_LISTA", "SET_PARAMETROS"]),

    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe
      // algo que impeça o relatório de ser gerado.
      // Exemplo: relatório de aluno só pode ser gerado quando o filtro de aluno não estiver vazio
      return true;
    },
    setNomeAluno(value) {
      this.alunoTemporario = value;
      if (this.filtroSelecionado === 1) {
        this.filtrar();
      }
    },

    setItemDescricao(value) {
      if (value) {
        this.descricaoItem = value;
      }
    },
    setUsuarioDescricao(value) {
      if (value) {
        this.nomeUsuario = value;
      }
    },

    setEmpresaId(value) {
      if (value) {
        this.empresaId = value.id;
        this.descricaoEmpresa = value.nome_contato;
      } else {
        this.empresaId = null;
        this.descricaoEmpresa = null;
      }
    },

    setFuncionarioId(value) {
      if (value) {
        this.apelido = value.id;
      } else {
        this.apelido = null;
      }
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        aluno: this.alunoTemporario ? this.alunoTemporario.id : null,
        empresa: this.empresaId ? this.empresaId : null,
        apelido: this.apelido ? this.apelido : null,
        data_saida_de: form.data_saida_de ? form.data_saida_de : null,
        data_saida_ate: form.data_saida_ate ? form.data_saida_ate : null,
        data_entrega_de: form.data_entrega_de ? form.data_entrega_de : null,
        data_entrega_ate: form.data_entrega_ate ? form.data_entrega_ate : null,
        item: this.descricaoItem ? this.descricaoItem.id : null,
        usuario: this.nomeUsuario ? this.nomeUsuario.id : null,
        situacao_entrega: this.selected ? this.selected : null,
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
.tabela-saida-estoque >>> tr > th,
.tabela-saida-estoque >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-saida-estoque >>> table thead {
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
#tabela-saida-estoque {
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