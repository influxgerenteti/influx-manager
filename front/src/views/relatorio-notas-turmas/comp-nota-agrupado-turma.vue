<template>
    <div id="tabela">
        <b-table
            class="tabela-nota-agrupado-turma w-full"
            :items="Object.values(lista.data)"
            :fields="cabecalho"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            small hover outlined bordered striped show-empty fixed-header sort-icon-right>

            <template #empty>
                <h4>Nenhum registro a ser exibido.</h4>
            </template>
        </b-table>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        name: "CompNotaAgrupadoTurma",
        data() {
            return {
                sortBy: 'Aluno',
                sortDesc: false,
                cabecalho: [
                    { key:'turma', label: 'Turma', sortable: true, class: 'no-break right-border font-weight-bold' },
                    { key:'professor', label: 'Professor', sortable: true, class: 'no-break right-border' },
                    { key:'livro', label: 'Livro', sortable: true, class: 'no-break right-border' },
                    { key:'mediaFrequencia', label: 'Frequência', sortable: true, class: 'no-break right-border' },
                    { key:'mediaMidTermTest', label: 'Méda Mid Term', sortable: true, class: 'no-break right-border' },
                    { key:'mediaFinalTest', label: 'Média Final', sortable: true, class: 'no-break' },
                ],
            }
        },

        computed: {
            ...mapState("relatorioNotasTurmas", ['lista']),
        },

        mounted() {
        },

        methods: {
        },
    }
</script>

<style scoped>
    .tabela-nota-agrupado-turma >>> tr > th, .tabela-nota-agrupado-turma >>> tr > td {
        vertical-align: middle;
        text-align: center;
        display: table-cell;
    }

    .tabela-nota-agrupado-turma >>> td.right-border {
        border-right: 1px dotted gray;
    }

    .table-sm tbody tr {
        page-break-after: avoid;
    }

    #tabela {
        max-height: 80vh;
        overflow: auto;
    }

    @media print {
        #tabela {
            overflow: visible;
        }
    }

</style>
