<template>
     <div class="wrapper-table-scroll">
        <b-table
            class="tabela-nota-agrupado-turma"
            id="tabela-nota-agrupado-turma"
            :items="Object.values(resumo)"
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
      sticky-header >
            
            <template #cell(retornosEfetivos)=data>
                {{ data.value + ' %'}}
            </template>

            <template #empty>
                <h4>Nenhum registro a ser exibido.</h4>
            </template>
        </b-table>
    </div>
 </template>

<script>
    import { mapState } from 'vuex';
    export default {
        name: "CompResumoConsultor",
        data() {
            return {
                sortBy: 'Consultor',
                sortDesc: false,
                cabecalho: [
                    { key:'consultor', label: 'Consultor', sortable: true, class: 'no-break right-border font-weight-bold' },
                    { key:'efetivos', label: 'Efetivos', sortable: true, class: 'no-break right-border' },
                    { key:'retornos', label: 'Retornos', sortable: true, class: 'no-break right-border' },
                    { key:'retornosEfetivos', label: 'Retornos Efetivos %', sortable: true, class: 'no-break right-border' },
                ],
            }
        },

        computed: {
            ...mapState("relatorioVisitas", ['resumo']),
        },

        mounted() {
        },

        methods: {
        },
    }
</script>

<style scoped>
   .table {
  margin-bottom: 0 !important;
}
.tabela-wrapper {
  overflow-y: scroll;
  min-height: auto !important;
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


    @media print {
        #tabela {
            overflow: visible;
        }
        .tabela-notas >>> thead{
            position: relative;
        }
    }

</style>
