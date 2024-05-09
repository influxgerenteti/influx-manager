<template>
  <div  class="tabela-wrapper">
    <b-table
      class="tabela-nota-agrupado-turma"
      id="tabela-nota-agrupado-turma"
      :items="lista"
      :fields="cabecalho"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      small
      hover
      outlined
      bordered
      striped
      show-empty
      fixed-header
      sort-icon-right
      sticky-header
    >
      <template #cell(situacao)="data">
        <span>{{ converterSituacao(data.value) }}</span>
      </template>

      <template #cell(tipo)="data">
        <span>{{ data.value == "R" ? "Receptivo" : "Ativo" }}</span>
      </template>

      <template #empty>
        <h4>Nenhum registro a ser exibido.</h4>
      </template>
    </b-table>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  name: "CompVisitasResumido",
  data() {
    return {
      sortBy: "Consultor",
      sortDesc: false,
      cabecalho: [
        {
          key: "consultor",
          label: "Consultor",
          sortable: true,
          class: "no-break right-border font-weight-bold",
        },
        {
          key: "nome_contato",
          label: "Nome Contato",
          sortable: true,
          class: "no-break right-border",
        },
        {
          key: "situacao",
          label: "Situação",
          sortable: true,
          class: "no-break right-border",
        },
        {
          key: "tipo",
          label: "Tipo",
          sortable: true,
          class: "no-break right-border",
        },
      ],
    };
  },

  computed: {
    ...mapState("relatorioVisitas", ["lista"]),
  },

  mounted() {},

  methods: {
    converterSituacao(situacao) {
      const valores = {
        A: "Aberto",
        C: "Convertido",
        I: "Inativo",
        P: "Perdido",
      };
      return valores[situacao];
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
.tabela-nota-agrupado-turma >>> tr > th,
.tabela-nota-agrupado-turma >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-nota-agrupado-turma >>> table thead {
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
#tabela-nota-agrupado-turma {
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
  #tabela {
    overflow: visible;
  }
  .tabela-notas >>> thead {
    position: relative;
  }
}
</style>
