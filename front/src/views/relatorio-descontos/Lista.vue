<template>
  <div class="animated fadeIn wrapper-table-scroll">
    <div class="no-print mb-2">
      <b-card no-body>
        <b-tabs card>
          <b-tab
            title="Filtros"
            :class="filtroVisivel ? null : 'collapsed'"
            :aria-expanded="filtroVisivel ? 'true' : 'false'"
            aria-controls="collapse-4"
            @click="filtroVisivel = !filtroVisivel"
            active>
            <b-card-text>
              <div class="filtro-avancado">
                <b-collapse id="collapse-4" v-model="filtroVisivel" class="mt-2">
                  <b-card class="filtro-avancado">
                    <b-form-group md="auto" label="Filtros de Aluno">
                      <b-row class="form-group">
                        <b-col md="auto" class="d-flex flex-column justify-content-end">
                          <label v-help-hint="'filtro-relatorio-contas-pagar_modalidade'" for="modalidade" class="form-label d-block">Modalidade</label>
                          <b-form-checkbox-group id="modalidade" v-model="filtros.modalidade" :options="modalidade" buttons button-variant="cinza" name="modalidade" class="checkbtn-line"/>
                        </b-col>
                        <b-col md="auto" class="d-flex flex-column justify-content-end">
                          <label v-help-hint="'filtro-relatorio-contas-pagar_situacao'" for="situacao" class="form-label d-block">Situação</label>
                          <b-form-checkbox-group id="situacao" v-model="filtros.situacao" :options="situacao" buttons button-variant="cinza" name="situacao" class="checkbtn-line"/>
                        </b-col>
                      </b-row>
                    </b-form-group>

                    <hr>

                    <b-form-group md="auto" label="Opções do Relatório">
                      <b-row class="form-group">

                        <b-col md="2">
                          <b-form-group label="Semestre">
                            <g-select-semestre v-model="filtros.semestre" value-field="id">
                            </g-select-semestre>
                          </b-form-group>
                        </b-col>

                        <b-col md="auto">
                          <b-form-group label="Forma de Pagamento">
                            <b-form-select v-model="filtros.formaPagamento" :options="formasPagamento" value-field="id" text-field="descricao">
                              <template #first>
                                <b-form-select-option :value="null">Forma de Pagamento</b-form-select-option>
                              </template>
                            </b-form-select>
                          </b-form-group>
                        </b-col>
                        
                      </b-row>
                      <b-row>
                        <b-col md="auto">
                          <b-form-checkbox v-model="filtros.compararSemestre" name="check-button">Comparar com Semestre Anterior</b-form-checkbox>
                        </b-col>
                      </b-row>
                    </b-form-group>
                  </b-card>
                </b-collapse>
                <div class="mb-2 d-flex justify-content-end">
                  <div class="col-md-2" v-if="lista.length">
                    <g-print></g-print>
                  </div>
                  <b-col md="auto">
                    <g-excel
                      v-if="lista.length"
                      class="btn btn-cinza btn-block text-uppercase"
                      :data="lista"
                      :fields="exportFields"
                      type="xls"
                      name="relatorio-descontos">
                      <font-awesome-icon icon="file-code" />
                      Exportar para Excel
                    </g-excel>

                    
                  </b-col>
                  <div class="col-md-2">
                      <b-btn
                      class="btn btn-cinza btn-block text-uppercase"
                      @click="abrirRelatorio()">
                      Gerar relatório
                    </b-btn>
                  </div>
                </div>
              </div>
            </b-card-text>
          </b-tab>
        </b-tabs>
      </b-card>
    </div>
    <b-table
      class="tabela-descontos w-full"
      :busy="estaCarregando"
      :items="lista"
      :fields="cabecalho"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      small hover outlined striped show-empty sticky-header="80vh" fixed-header sort-icon-right>

      <template #empty>
        <h4>Nenhum registro a ser exibido.</h4>
      </template>

      <template #table-busy>
        <div class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Carregando Dados...</strong>
        </div>
      </template>

      <template #cell(nome)="data">
        <div class="text-left hover" v-b-tooltip.hover title="Consultar Aluno" @click="acessarAluno(data.item.aluno_id)">
          <span>{{data.value}}</span>
          <div style="display: flex; justify-content: space-evenly;">
            <span>{{data.item.situacao}}</span>
            <span>{{data.item.modalidade}}</span>
          </div>
        </div>
      </template>
      <template #cell(valor_final)="data">
          <span class="text-right d-block w-full">{{
            data.value ?
            Number.parseFloat(data.value).toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g, ".") :
            'Não Consta' }}
          </span>
      </template>
      <template #cell(valor_original)="data">
          <span class="text-right d-block w-full">{{
            data.value ? Number.parseFloat(data.value).toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g, ".") :
            'Não Consta' }}
          </span>
      </template>
      <template #cell(desconto)="data">
        <div style="padding: 0px 5px;">
          <div
            class="hover"
            style="display: flex; justify-content: space-between;"
            v-b-tooltip.hover title="Consultar Contrato"
            @click="acessarContrato(data.item.contrato_id)">
            <span>{{data.item.semestre}}</span>
            <span class="text-right d-block w-full">{{
              data.value ?
              Number.parseFloat(data.value).toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g, ".") :
              '0,00'
              }}
            </span>
          </div>
          <div
            v-if="data.item.sub_semestre"
            class="hover"
            style="display: flex; justify-content: space-between;"
            v-b-tooltip.hover title="Consultar Contrato Anterior"
            @click="acessarContrato(data.item.sub_contrato_id)">
            <span>{{data.item.sub_semestre}}</span>
            <span>{{
              data.item.sub_desconto ?
              Number.parseFloat(data.item.sub_desconto).toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g, ".") :
              '0,00'
              }}</span>
          </div>
        </div>
      </template>
    </b-table>

    <b-card v-if="lista.length && resumo.alunosPorDesconto && resumo.alunosPorDesconto.semestreFiltrado" id='resumo'>

      <div class="desconto-medio">
        <h3>Desconto Médio</h3>
        <b-card class="alert-green mb-0">
          <span>Semestre Filtrado</span>
          <span>{{resumo.descontoMedio.toFixed(2).replace('.',',')}} %</span>
        </b-card>
        <b-card v-if="resumo.descontoMedioAnterior" class="alert-green mb-0">
          <span>Semestre Anterior</span>
          <span>{{resumo.descontoMedioAnterior.toFixed(2).replace('.',',')}} %</span>
        </b-card>
      </div>

      <hr>
        
      <div v-if="resumo.alunosPorDesconto">
        <h3>Alunos Por Faixa de Desconto</h3>
        <b-table-simple
          class="tabela-resumo"
          :items="resumo.alunosPorDesconto"
          small hover outlined striped sort-icon-right>
          <b-thead>
            <b-tr>
              <b-th>Semestre</b-th>
              <b-th>1 - 10%</b-th>
              <b-th>11 - 20%</b-th>
              <b-th>21 - 30%</b-th>
              <b-th>31 - 40%</b-th>
              <b-th>41 - 50%</b-th>
              <b-th>51 - 60%</b-th>
              <b-th>61 - 70%</b-th>
              <b-th>71 - 80%</b-th>
              <b-th>81 - 90%</b-th>
              <b-th>91 - 100%</b-th>
              <b-th>Total</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <b-tr v-for="(value, key) in resumo.alunosPorDesconto" :key="key">
              <b-td>{{ key == 'semestreFiltrado' ? 'Filtrado' : 'Anterior' }}</b-td>
              <b-td v-for="(valor, chave) in value" :key="chave">
                {{valor}}
              </b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
      </div>
    </b-card>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'FormularioRelatorioDescontos',
  data () {
    return {
      filtroVisivel: true,
      sortBy: 'Nome',
      sortDesc: false,
      modalidade: [
        { text: 'Regulares', value: '0' },
        { text: 'Bolsistas', value: '1' }
      ],
      situacao: [
        { text: 'Ativo', value: 'ATI' },
        { text: 'Inativo', value: 'INA' }
      ],
      cabecalho: [
        { key:'nome', label: 'Nome', sortable: true },
        { key:'valor_original', label: 'Valor Original R$', sortable: true },
        { key:'valor_final', label: 'Valor Final R$', sortable: true },
        { key:'desconto', label: 'Desconto %', sortable: true },
        { key:'forma_pagamento', label: 'Pagamento', sortable: true }
      ],
      exportFields: {
        'Identificador do Aluno': 'aluno_id',
        'Aluno': "nome",
        'Situação': 'situacao',
        'Modalidade': 'modalidade',
        'Semestre': 'semestre',
        'Contrato': 'contrato_id',
        'Valor Original R$': "valor_original",
        'Valor com Desconto R$': 'valor_final',
        'Desconto %': 'desconto',
        'Forma de Pagamento': 'forma_pagamento',
        'Semestre Anterior': 'sub_semestre',
        'Contrato Anterior': 'sub_contrato_id',
        'Desconto no Semestre Anterior': 'sub_desconto'
      }
    }
  },

  computed: {
    ...mapState('relatorioDescontos', ['filtros', 'lista', 'estaCarregando', 'semestres', 'formasPagamento', 'resumo']),
  },

  created () {
    this.listarSemestres()
    this.listarFormaPagamento()
  },

  mounted() {
    this.SET_LISTA([])
  },

  methods: {
    ...mapActions('relatorioDescontos', ['listar', 'listarSemestres', 'listarFormaPagamento', 'atualizarFiltros']),
    ...mapMutations('relatorioDescontos', ['SET_FILTROS', 'SET_LISTA']),

    abrirRelatorio() {
      this.SET_FILTROS(this.filtros)
      this.listar()
    },

    acessarAluno(id) {
      window.open('/academico/aluno/atualizar/' + id, '_blank')
    },

    acessarContrato(id){
      window.open('/cadastros/contrato/atualizar/' + id, '_blank')
    }
  }
}
</script>

<style scoped>

  .tabela-descontos >>> tr > th, .tabela-descontos >>> tr > td, .tabela-resumo >>> tr > td, .tabela-resumo >>> tr > th {
    vertical-align: middle;
    text-align: center;
    display: table-cell;
  }

  .hover {
    cursor: pointer;
  }

  .tabela-descontos >>> table thead {
    position: sticky;
    top: 	-1px;
  }

  .desconto-medio {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
  }

  .desconto-medio >>> div {
    display: flex;
    flex-direction: column-reverse;
    align-items: center;
  }
  .desconto-medio >>> div > span:nth-child(2) {
    font-weight: bold;
  }

  @media print{
    .tabela-descontos >>> table {
      font-size: 12px;
      margin-top: -5px;
    }
    .tabela-descontos >>> table :after {
      content: '';
      height: 10px;
    }
    .tabela-descontos >>> table thead {
      display: contents;
    }

    #resumo :before {
      content: '';
      page-break-inside: avoid;
    }

  }


  
</style>