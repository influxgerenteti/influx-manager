<template>
    <div>
        <b-table
            class="tabela-notas w-full"
            :items="lista.data"
            :fields="cabecalho"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            small hover outlined bordered striped show-empty sticky-header="80vh" fixed-header sort-icon-right>

            <template #empty>
                <h4>Nenhum registro a ser exibido.</h4>
            </template>

            <template #head(mid_term_test)>
                    <div v-b-tooltip.hover.top="'Mid Term 1'">MD 1</div>
            </template>
            <template #head(mid_term_composition)>
                    <div v-b-tooltip.hover.top="'Mid Term 2'">MD 2</div>
            </template>
            <template #head(mid_term_retake)>
                    <div v-b-tooltip.hover.top="'Mid Term Retake'">MD R</div>
            </template>

            <template #head(final_test)>
                    <div v-b-tooltip.hover.top="'Final 1'">F 1</div>
            </template>
            <template #head(final_composition)>
                    <div v-b-tooltip.hover.top="'Final 2'">F 2</div>
            </template>
            <template #head(final_retake)>
                    <div v-b-tooltip.hover.top="'Final Retake'">F R</div>
            </template>
        </b-table>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        name: "CompNotaAlunos",
        data() {
            return {
                sortBy: 'Aluno',
                sortDesc: false,
                cabecalho: [
                    { key:'nome_aluno', label: 'Aluno', sortable: true, class: 'right-border' },
                    { key:'turma', label: 'Turma', sortable: true, class: 'right-border' },
                    { key:'professor', label: 'Professor', sortable: true, class: 'right-border' },
                    { key:'frequencia', label: 'Frequência', sortable: true, class: 'right-border' },
                    { key:'mid_term_test', label: 'Mid Term 1', sortable: true, class: 'right-border', formatter: value => value ? value : '--' },
                    { key:'mid_term_composition', label: 'Mid Term 2', sortable: true, class: 'right-border', formatter: value => value ? value : '--' },
                    { key:'mid_term_retake', label: 'Retake Mid Term', sortable: true, class: 'right-border', formatter: value => value ? value : '--' },
                    { key:'final_test', label: 'Final 1', sortable: true, class: 'right-border', formatter: value => value ? value : '--' },
                    { key:'final_composition', label: 'Final 2', sortable: true, class: 'right-border', formatter: value => value ? value : '--' },
                    { key:'final_retake', label: 'Retake Final', sortable: true, formatter: value => value ? value : '--' }
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
    .tabela-notas >>> tr > th, .tabela-notas >>> tr > td {
        vertical-align: middle;
        text-align: center;
        display: table-cell;
    }

    .tabela-notas >>> td.right-border {
        border-right: 1px dotted gray;
    }

    .tabela-notas >>> table thead {
        position: sticky;
        top: 	-1px;
    }

    @media print {
        .tabela-notas >>> table head {
            position: relative;
        }
    }
</style>
