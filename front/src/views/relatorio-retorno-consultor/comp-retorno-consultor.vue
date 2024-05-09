<template>
    <div class="tabela-wrapper">
    <b-table
      class="tabela-retorno-consultor"
      id="tabela-retorno-consultor"
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
    >
    <template #cell(data_cadastro)="data">
        <span>{{ data.value | formatarData }}</span>
      </template>
      <template #cell(situacao)="data">
        <span>{{ converterSituacao(data.value) }}</span>
      </template>
      <template #cell(tipo_lead)="data">
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
  name: "CompRetornoConsultor",
  data() {
    return {
      sortBy: "Consultor",
      sortDesc: false,
      cabecalho: [
      
        { key:'nome_contato', label: 'Consultor', sortable: true, class: 'no-break right-border font-weight-bold' },
                    { key:'data_cadastro', label: 'Data', sortable: true, class: 'no-break right-border' },
                    { key:'nome_contato', label: 'Nome Contato', sortable: true, class: 'no-break right-border' },
                    { key:'nome', label: 'Nome', sortable: true, class: 'no-break right-border' },
                    { key:'situacao', label: 'Situação', sortable: true, class: 'no-break right-border' },
                    { key:'tipo_lead', label: 'Tipo', sortable: true, class: 'no-break right-border' }, 
                 
      ],
    };
  },

  computed: {
    ...mapState("relatorioRetornoConsultor", ["lista"]),
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
    }

</script>

<style scoped>


.table {
  margin-bottom: 0 !important;
}
.tabela-wrapper {
  overflow: scroll;
  max-height: 55vh;
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
.tabela-retorno-consultor >>> tr > th,
.tabela-retorno-consultor >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-retorno-consultor >>> table thead {
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
#tabela-retorno-consultor {
  overflow: visible;
}
@media (max-width: 992px) {
  .tabela-wrapper {
    margin-bottom: 8%;
}
}

@media print {
  #tabela {
    overflow: visible;
  }
  .tabela-retorno >>> thead {
    position: relative;
  }
}
</style>
