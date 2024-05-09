<template>
  <!-- //!Atenção, foi trocado para buscar de sacado para buscar Aluno, mas o nome das variáveis não foi trocado  -->
  <div class="filtro-avancado body-sector">
    <div class="d-flex justify-content-between filtro-header head-content-sector">
      <div>
        <div :class="{'filtro-selecionado': filtroRapido === true}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, trocaClasse(filtroRapido ? 'rapido-open' : null), filtroSelecionado = 1">Filtro Rápido</div>
        <!-- <div :class="{'filtro-selecionado': filtroAvancado === true}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, trocaClasse(filtroAvancado ? 'filtro-open' : null), filtroSelecionado = 2">Avançado</div> -->
      </div>
    </div>

    <b-collapse id="filtros-rapidos" v-model="filtroRapido">
      <form class="p-2">
        <b-row>
          <b-col md="3">
            <label v-help-hint="'data do vencimento ou movimento conforme seleção'" for="tipo_data" class="col-form-label d-block">Considerar data de:</label>
            <b-form-radio-group id="tipo_data" v-model="filtros.tipo_data" :options="tipos_data" buttons button-variant="cinza" name="tipo_data" class="checkbtn-line" @change="setTipoData"/>
          </b-col>
        
          <!-- <b-col md="3"
            <label v-help-hint="'filtroRapido-contas-receber_sacado'" for="buscaSacadoRapido" class="col-form-label">Aluno</label>
            <typeahead id="buscaSacadoRapido" :value="filtros.sacado_pessoa" :item-hit="setSacadoRapido" :source-path="sourcePath" key-name="nome_contato" />
          </b-col> -->
       
            <b-col md="3" v-if="filtros.tipo_data == tipos_data[0]">
              <label v-help-hint="'Mes do vencimento'" for="mes_rapido" class="col-form-label">Mês</label>
              <g-select
                id="mes_rapido"
                :value="filtros.mes"
                :select="setMes"
                :options="meses"
                class="multiselect-truncate"
                label="text"
              />
            </b-col>

            <b-col md="3" v-if="filtros.tipo_data == tipos_data[0]">
              <label v-help-hint="'Ano do vencimento'" for="ano_rapido" class="col-form-label d-block">Ano</label>
              <b-form-radio-group id="ano_rapido" v-model="filtros.ano" :options="anos" buttons button-variant="cinza" name="ano_rapido" class="checkbtn-line" @change="setAno"/>
            </b-col>


          
            <b-col md="6" v-if="filtros.tipo_data == tipos_data[1]">
                    <label
                        v-help-hint="
                          'data efetiva do movimento'
                        "
                        for="data_movimento"
                        class="col-form-label"
                        >Data do movimento:</label
                      >
                      <g-data
                        :dataDeInicial="getDataInicio()"
                        :dataAteInicial="getDataFim()"
                        @dataDe="filtros.data_inicio = $event"
                        @dataAte="filtros.data_fim = $event"
                      />
                      
                      <!-- <g-data
                        @dataDe="filtros.data_inicio = $event"
                        @dataAte="filtros.data_fim = $event"
                      /> -->
              </b-col>
         
        </b-row>

        <b-row>
          <b-col md="4" v-if="filtros.tipo_data == tipos_data[0]">
              <label v-help-hint="'filtroRapido-contas-receber_forma_cobranca'" for="buscaFormaCobranca" class="col-form-label">Forma de Pagamento</label>
              <g-select
                id="buscaFormaCobranca"
                :value="filtros.forma_cobranca"
                :select="setFormaCobranca"
                :options="listaFormasPagamento"
                class="multiselect-truncate"
                label="descricao"
                @input="filtrar()"
              />
            </b-col>
            <div class="col-md-auto" v-if="filtros.tipo_data == tipos_data[0]">
            <label v-help-hint="'filtroRapido-contas-receber_situacao'" for="situacao_rapido" class="col-form-label d-block">Situação</label>
            <b-form-checkbox-group id="situacao_rapido" v-model="filtros.situacao" :options="situacoes" buttons button-variant="cinza" name="situacao_rapido" @change="filtrar()" class="checkbtn-line fill-width"/>
          </div>
           
        </b-row>

        <b-row>
          <b-col md="9">
            <label  for="campoBuscar" class="col-form-label">Buscar</label>            
            <input id="campoBuscar" v-model="filtros.busca" type="text" name="busca" class="form-control">
          </b-col>
          <b-col md="2">
            <label  for="btnBuscar" class="col-form-label"> '</label>     
            <button id="btnBuscar" type="button" class="btn btn-primary btn-block text-uppercase" @click="buscarRegistros">Buscar</button>       
            
          </b-col>
          
        </b-row>
      </form>
    </b-collapse>

    <b-collapse id="filtros-avancados" v-model="filtroAvancado">
      <form class="p-2" @submit.prevent="buscaAvancada = true, filtrar()">

        <b-row class="form-group">
          <div class="col-md-3">
            <label v-help-hint="'filtroAvancado-contas-receber_vencimento'" class="col-form-label">Vencimento</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">De</div>
                  </div>
                  <g-datepicker :element-id="'data_inicial_vencimento'" :value="filtros.data_inicial_vencimento" :selected="setDataInicialVencimento"/>
                </div>
              </div>
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">à</div>
                  </div>
                  <g-datepicker :element-id="'data_final_vencimento'" :value="filtros.data_final_vencimento" :selected="setDataFinalVencimento"/>
                </div>
              </div>
            </div>

            <div v-if="dateToCompare(filtros.data_inicial_vencimento) > dateToCompare(filtros.data_final_vencimento) && filtros.data_final_vencimento !== ''" class="floating-message bg-danger">
              Data inicial deve ser menor que a data final!
            </div>
          </div>

          <div class="col-md-3">
            <label v-help-hint="'filtroAvancado-contas-receber_saldo'" class="col-form-label">Saldo devedor</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">Min</div>
                  </div>
                  <vue-numeric id="valor_inicial" :precision="2" :empty-value="null" v-model="filtros.valor_inicial" :max="9999999.99" separator="." class="form-control" />
                </div>
              </div>

              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">Max</div>
                  </div>
                  <vue-numeric id="valor_final" :precision="2" :empty-value="null" v-model="filtros.valor_final" :max="9999999.99" separator="." class="form-control" />
                </div>
              </div>
            </div>

            <div v-if="filtros.valor_inicial > filtros.valor_final && !!filtros.valor_final" class="floating-message bg-danger">
              Saldo mínimo deve ser menor que o máximo!
            </div>
          </div>

          <div class="col-md-auto">
            <label v-help-hint="'filtroAvancado-contas-receber_situacao'" for="situacao_avancado" class="col-form-label d-block">Situação</label>
            <b-form-checkbox-group id="situacao_avancado" v-model="filtros.situacao" :options="situacoes" buttons button-variant="cinza" name="situacao_avancado" class="checkbtn-line"/>
          </div>
        </b-row>

        <b-row class="form-group">
          <b-col md="2">
            <label v-help-hint="'filtroAvancado-contas-receber_sacado'" for="buscaSacado" class="col-form-label">Sacado</label>
            <typeahead id="buscaSacado" :item-hit="setSacado" :source-path="sourcePath" key-name="nome_contato" />
          </b-col>

          <b-col md="2">
            <label v-help-hint="'filtroAvancado-contas-receber_item'" for="buscaItem" class="col-form-label">Item</label>
            <typeahead id="buscaItem" :item-hit="setItem" source-path="/api/item/buscar_descricao" key-name="descricao" />
          </b-col>

          <b-col md="2">
            <label v-help-hint="'filtroAvancado-contas-receber_forma_cobranca'" for="buscaFormaCobrancab" class="col-form-label">Forma Cobrança</label>
            <g-select
              id="buscaFormaCobrancab"
              :value="filtros.forma_cobranca"
              :select="setFormaCobranca"
              :options="listaFormasPagamento"
              class="multiselect-truncate"
              label="descricao"
            />
          </b-col>

          <b-col md="2">
            <label v-help-hint="'filtroAvancado-contas-receber_turma'" for="buscaTurma" class="col-form-label">Turma</label>
            <g-select
              id="buscaTurma"
              :value="filtros.turma"
              :select="setTurma"
              :options="listaTurmas"
              class="multiselect-truncate"
              label="turmaDescricao"
              track-by="turmaId"
            />
          </b-col>

          <b-col md="2">
            <label v-help-hint="'filtroAvancado-contas-receber_contrato'" for="filtroContrato" class="col-form-label">Contrato</label>
            <input id="filtroContrato" v-model="filtros.contrato" type="text" name="filtroContrato" class="form-control">
          </b-col>

          <b-col md="2">
            <label v-help-hint="'filtroAvancado-contas-receber_nosso_numero'" for="filtroBoleto" class="col-form-label">Nosso Nº</label>
            <input id="filtroBoleto" v-model="filtros.nosso_numero" type="text" name="filtroBoleto" class="form-control">
          </b-col>
        </b-row>

        <b-row class="form-group">
          <b-col md="2">
            <label v-help-hint="'filtroAvancado-contas-receber_conta'" for="filtroConta" class="col-form-label">Conta (transferência)</label>
            <input id="filtroConta" v-model="filtros.conta" type="text" name="filtroConta" class="form-control">
          </b-col>

          <b-col md="2">
            <label v-help-hint="'filtroAvancado-contas-receber_agencia'" for="filtroAgencia" class="col-form-label">Agência (transferência)</label>
            <input id="filtroAgencia" v-model="filtros.agencia" type="text" name="filtroAgencia" class="form-control">
          </b-col>
        </b-row>

        <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, trocaClasse()">Buscar</button>
      </form>
    </b-collapse>
  </div>
</template>

<script>
import {mapState} from 'vuex'
import {dateToCompare} from '../../utils/date'
import Typeahead from '../../components/Typeahead.vue'
import {firstDayOfWeek, lastDayOfWeek, nextWeek, previousWeek, formatarDataPadraoBrasileiro, formatarDataPadraoBancoDados} from '../../utils/date'

export default {
  components: {
    Typeahead
  },

  props: {
    situacoes: {
      required: false,
      default: null,
      type: Array
    },

    trocaClasseTabela: {
      required: false,
      default: null,
      type: Function
    },

    pessoasBuscar: {
      required: false,
      default: () => {
        return {
          aluno: true
        }
      },
      type: Object
    }
  },

  data () {
    this.$store.commit('contasReceber/LIMPAR_FILTROS', this.filtroRapido)
    return {
      filtroSelecionado: 1,
      filtroRapido: true,
      buscaRapida: true,
      filtroAvancado: false,
      buscaAvancada: false,
      formato_impressao: '',
      anos: [],
      tipos_data: ['VENCIMENTO','MOVIMENTO'],
      meses: [
        {text: 'Todos', value: null},
        {text: 'Janeiro', value: 0},
        {text: 'Fevereiro', value: 1},
        {text: 'Março', value: 2},
        {text: 'Abril', value: 3},
        {text: 'Maio', value: 4},
        {text: 'Junho', value: 5},
        {text: 'Julho', value: 6},
        {text: 'Agosto', value: 7},
        {text: 'Setembro', value: 8},
        {text: 'Outubro', value: 9},
        {text: 'Novembro', value: 10},
        {text: 'Dezembro', value: 11}
      ]
    }
  },

  computed: {
    ...mapState('contasReceber', ['filtros']),

    listaFormasPagamento: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.formaPagamento.lista)
      }
    },

    listaTurmas: {
      get () {
        return [{id: null, turmaDescricao: 'Selecione'}].concat(this.$store.state.turma.lista)
      }
    },

    sourcePath: {
      get () {
        if (this.pessoasBuscar.aluno && this.pessoasBuscar.aluno === true) {
          if (this.pessoasBuscar.sacado && this.pessoasBuscar.sacado === true) {
            return '/api/titulo_receber/buscar_pessoas_aluno_ou_sacado'
          } else {
            return '/api/pessoa/buscar_nome_contato_com_contrato'
          }
        }
        alert('ATENÇÃO DESENVOLVEDOR: Caso não tratado para busca de pessoa no filtro de contas-receber')
        return ''
      }
    }
  },

  mounted () {
    const thisYear = (new Date()).getFullYear()
    for (let year = thisYear - 2, endYear = thisYear + 2; year <= endYear; year++) {
      this.anos.push(year)
    }
    this.filtros.tipo_data = this.tipos_data[0]

    this.$store.commit('turma/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('turma/listar')

    const contratoParam = this.$route.query.contrato
    if (contratoParam !== undefined) {
      this.trocaClasse()
      this.buscaAvancada = true
      this.filtroSelecionado = 2
      this.filtroRapido = false
      this.filtroAvancado = false
      this.filtros.contrato = contratoParam
    }
  },

  methods: {
    dateToCompare: dateToCompare,

    setMes (value) {
      this.filtros.mes = value
      this.filtrar()
    },
    setAno (value) {
      this.filtros.ano = value
      this.filtrar()
    },
    setTipoData (value) {
      this.filtros.tipo_data = value      
      if (this.filtros.tipo_data == this.tipos_data[0]) {
        this.filtros.data_inicio = '';
        this.filtros.data_fim = '';    
        this.filtros.situacao = ['VEN']  
      }
      if (this.filtros.tipo_data == this.tipos_data[1]) {
        this.filtros.forma_cobranca = null;
        this.filtros.situacao = ['LIQ','REC'];
        
      }
       
      this.$forceUpdate()
      this.filtrar()
    },

    setSituacao (value) {
      this.filtros.situacao = value
      // this.filtrar()
    },
    getDataInicio(){
      const primeiroDiaDoMes = new Date();
      primeiroDiaDoMes.setDate(1); // Ajusta o dia para o primeiro do mês
      // return formatarDataPadraoBrasileiro(primeiroDiaDoMes);
      console.log(primeiroDiaDoMes);
      return primeiroDiaDoMes;
    },

    getDataFim(){
      const ultimoDiaDoMes = new Date();
      ultimoDiaDoMes.setMonth(ultimoDiaDoMes.getMonth() + 1);
      ultimoDiaDoMes.setDate(0); // ...e então ajustamos o dia para o dia anterior (0), que é o último dia do mês corrente
      // return formatarDataPadraoBrasileiro(ultimoDiaDoMes);
      return ultimoDiaDoMes;
    },

     

    // setBusca (event) {
    //   this.filtros.busca = event.target.value
    //   // console.log(this.filtros.busca)
    // },

    buscarRegistros(value) {
      
      this.filtrar()
    },


    setDataInicialVencimento (value) {
      this.filtros.data_inicial_vencimento = value
    },

    setDataFinalVencimento (value) {
      this.filtros.data_final_vencimento = value
    },

    setDataInicialPagamento (value) {
      this.filtros.data_inicial_pagamento = value
    },

    setDataFinalPagamento (value) {
      this.filtros.data_final_pagamento = value
    },

    setSacado (value) {
      this.filtros.sacado_pessoa = value
    },

    setSacadoRapido (value) {
      this.filtros.sacado_pessoa = value
      //this.filtrar()
    },

    setItem (value) {
      this.filtros.item = value
    },

    setFormaCobranca (value) {
      this.filtros.forma_cobranca = value
    },

    setTurma (value) {
      this.filtros.turma = value
    },

    trocaClasse (classe = null) {
      this.trocaClasseTabela(classe)
    },

    filtrar () {
   
      //  this.$store.commit('contasReceber/LIMPAR_FILTROS', this.filtroRapido)
     

      
      this.clear()
        this.$emit('filtrar')
      }
      ,
      clear () {
        this.$store.commit('contasReceber/LIMPAR_DADOS',null)
      }
    }
}
</script>
