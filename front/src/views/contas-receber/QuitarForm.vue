<template>
  <form @submit.prevent="salvar()">
    <p>
      Segue informaçõe do titulo para baixa <br>
    </p>

    <div v-if="titulo">
      <template>
          <div class="container mt-4">
              <form class="form-receber">
                <b-row>
                  <b-col md="1" class="col-form-label">
                        <span class="col-form-label">Parcela</span>
                        <div>{{ titulo.numero_parcela_documento }}</div>
                    </b-col>

                    <b-col md="3">
                    <span class="col-form-label">Nome</span>
                    <div>{{ titulo.cliente_nome }}</div>                    
                    </b-col>
                    <b-col md="6">
                    <span class="col-form-label">Observação</span>
                    <div>{{ titulo.observacao }}</div>                    
                    </b-col>
                  </b-row>

                  <b-row>
                    

                    <b-col md="2">
                      <span class="col-form-label">Valor</span>
                      <div>{{ titulo.valor_saldo_devedor | formatarMoeda }}</div>
                    </b-col>
                   
                    <b-col md="2">
                    <span class="col-form-label">Vencimento</span>
                    <div>{{ titulo.data_vencimento | formatarData }}</div>
                    </b-col>

                    <b-col md="4">
                    <span class="col-form-label">Forma Pagamento</span>
                    <div>{{ titulo.forma_pagamento  }}</div>
                    </b-col>
                    <b-col md="2" class="text-right">

                    <!-- <b-btn class="btn-history" v-b-toggle.show-history v-b-tooltip.viewport.left.hover variant="default" title="Histórico de movimentos">
                      <font-awesome-icon icon="history" />
                    </b-btn>                 -->
                  </b-col>
                  </b-row>

                  <template >
                    <b-collapse id="show-history" class="p-2" style="background: #ffffff;">
                      <h5 class="mb-2">Histórico de movimentos</h5>

                      <!-- <b-row v-for="movimento in titulo.movimento_conta" :key="movimento.id" class="mb-1">
                        <b-col>
                          <span class="col-form-label">Data</span>
                          <div>{{ movimento.data_movimento | formatarDataHora }}</div>
                        </b-col>
                        <b-col>
                          <span class="col-form-label">Montante</span>
                          <div>{{ valorMontanteMovimento(movimento) | formatarMoeda }}</div>
                        </b-col>
                        <b-col>
                          <span class="col-form-label">Multa</span>
                          <div>{{ movimento.valor_multa | formatarMoeda }}</div>
                        </b-col>
                        <b-col>
                          <span class="col-form-label">Juros</span>
                          <div>{{ movimento.valor_juros | formatarMoeda }}</div>
                        </b-col>
                        <b-col>
                          <span class="col-form-label">Desconto</span>
                          <div>{{ movimento.valor_desconto | formatarMoeda }}</div>
                        </b-col>
                        <b-col>
                          <span class="col-form-label">&nbsp;</span>
                          <div v-if="movimento.desconto_super_amigos">{{ movimento.desconto_super_amigos.nome_desconto }}</div>
                          <div v-else>{{ movimento.observacao }}</div>
                        </b-col>
                      </b-row> -->
                    </b-collapse>
                  </template>

                <!-- <b-row>
                  <b-col md="2" class="text-right">
                    <b-btn v-b-tooltip.viewport.left.hover  variant="roxo" title="Adicionar pagamento">
                      <font-awesome-icon icon="plus" />
                    </b-btn>
                  </b-col>
                </b-row> -->

                <b-row>
                
                </b-row>

                  
              </form>
             <div class="pagamentos">
              Dados para pagamento
              <div v-if="pagamento.valor_montante" > 
              <!-- <div v-for="(pagamento, indexPagamento) in titulo.pagamentos" :key="`pagamento_${indexPagamento}`" class="py-2 bg-light"> -->
                  <pagamento-form
                    :id="'pagamento_form'"
                    :situacoes-title-form="situacoesTitle"
                    :pgto="pagamento"
                    :titulo="titulo"
                    :lista-objetos-funcoes-callback="listaFuncoesObjetos"
                    :esta-valido-form="estaValido"
                    :opcoes-pagamentos="listaFormasPagamento"
                  />
                </div>
              </div>
          </div>
      </template>

    </div>

    <!-- <div v-if="titulos.length" class="mb-4"> -->
      <!-- <div v-for="(titulo, indexTitulo) in titulos" :key="indexTitulo" class="mb-3" style="border: 2px dashed #ccc"> -->
          
        
        <!-- <b-row class="my-2 mx-2 align-items-center">
          <b-col md="1" class="text-center">
            <span class="col-form-label">Parcela</span>
            <div>{{ titulo.numero_parcela_documento }}</div>
          </b-col>

          <b-col md="2">
            <span class="col-form-label">Valor</span>
            <div>{{ titulo.valor_saldo_devedor | formatarMoeda }}</div>
          </b-col>

          <b-col md="2">
            <span class="col-form-label">Vencimento</span>
            <div>{{ titulo.data_vencimento | formatarData }}</div>
          </b-col>

          <b-col md="2">
            <span class="col-form-label">Forma de Cobrança</span>
            <div>{{ titulo.forma_cobranca.descricao }}</div>
          </b-col>

          <b-col md="3">
            <span class="col-form-label">Sacado</span>
            <div>{{ titulo.nome_contato }}</div>
          </b-col>

          <b-col md="2" class="text-right">
            <b-btn v-b-toggle.show-history v-b-tooltip.viewport.left.hover :disabled="titulo.movimento_conta.length === 0" variant="default" title="Histórico de movimentos">
              <font-awesome-icon icon="history" />
            </b-btn>

            <b-btn v-b-tooltip.viewport.left.hover :disabled="valorSaldoPosOperacao(titulo) === 0" variant="roxo" title="Adicionar pagamento" @click.prevent="adicionarPagamento(indexTitulo)">
              <font-awesome-icon icon="plus" />
            </b-btn>
          </b-col>
        </b-row>

        <template v-if="titulo.movimento_conta.length">
          <b-collapse id="show-history" class="p-2" style="background: #ececec;">
            <h5 class="mb-2">Histórico de movimentos</h5>

            <b-row v-for="movimento in titulo.movimento_conta" :key="movimento.id" class="mb-1">
              <b-col>
                <span class="col-form-label">Data</span>
                <div>{{ movimento.data_movimento | formatarDataHora }}</div>
              </b-col>
              <b-col>
                <span class="col-form-label">Montante</span>
                <div>{{ valorMontanteMovimento(movimento) | formatarMoeda }}</div>
              </b-col>
              <b-col>
                <span class="col-form-label">Multa</span>
                <div>{{ movimento.valor_multa | formatarMoeda }}</div>
              </b-col>
              <b-col>
                <span class="col-form-label">Juros</span>
                <div>{{ movimento.valor_juros | formatarMoeda }}</div>
              </b-col>
              <b-col>
                <span class="col-form-label">Desconto</span>
                <div>{{ movimento.valor_desconto | formatarMoeda }}</div>
              </b-col>
              <b-col>
                <span class="col-form-label">&nbsp;</span>
                <div v-if="movimento.desconto_super_amigos">{{ movimento.desconto_super_amigos.nome_desconto }}</div>
                <div v-else>{{ movimento.observacao }}</div>
              </b-col>
            </b-row>
          </b-collapse>
        </template>

        <div v-for="(pagamento, indexPagamento) in titulo.pagamentos" :key="`pagamento_${indexPagamento}`" class="py-2 bg-light">
          <pagamento-form
            :id="'pagamento_form_${indexTitulo}_${indexPagamento}'"
            :index-titulo-form="indexTitulo"
            :index-pagamento-form="indexPagamento"
            :situacoes-title-form="situacoesTitle"
            :pagamento-objeto="pagamento"
            :lista-objetos-funcoes-callback="listaFuncoesObjetos"
            :esta-valido-form="estaValido"
            :opcoes-pagamentos="listaFormasPagamento"
          />
        </div>

        <b-row class="mx-2 py-2 align-items-center">
        // <b-col md="2">
        //    <label class="col-form-label">Total a recebido</label>
        //    <div><big><b>{{ valorMontanteTotal(titulo) | formatarMoeda }}</b></big></div>
        //  </b-col> 

          <b-col md="3">
            <label class="col-form-label">Saldo após a operação</label>
            <div><big><b>{{ valorSaldoPosOperacao(titulo) | formatarMoeda }}</b></big></div>
          </b-col>
          <b-col md="9">
            <div v-if="valorSaldoPosOperacao(titulo) < 0" class="m-0 p-2 alert alert-danger">
              A soma dos valores principais não podem ser superiores ao valor da conta a receber.
            </div>
          </b-col>
        </b-row> -->
      <!-- </div> -->
    <!-- </div> -->

      <save-button :processing="salvando">Confirmar</save-button>
      <b-btn type="button" variant="link" @click="cancelar()">Cancelar</b-btn>
    </div>
  </form>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import moment from 'moment'
import EventBus from '../../utils/event-bus'
import {round} from '../../utils/number'
import {stringToDateConvert, dateToString,dateToDBStringConvert} from '../../utils/date'
import {defaultData} from '../titulo-receber/titulo'
import PagamentoForm from './PagamentoForm'

export default {
  components: {
    PagamentoForm
  },
  data () {
    return {
      estaValido: true,
      salvando: false,
      dataRecebimento: '',
      situacoesTitle: {
        P: 'Pendente',
        PEN: 'Pendente',
        LIQ: 'Quitado',
        CAN: 'Cancelado',
        VEN: 'Vencido',
        'LIQ-PEN': 'Quitação pendente'
      },
      listaFuncoesObjetos: {
        setPagamento: this.setPagamento,
      },
      situacoes: [
        {text: 'Pendente', value: 'PEN'},
        {text: 'Quitado', value: 'LIQ'},
        {text: 'Cancelado', value: 'CAN'},
        {text: 'Vencido', value: 'VEN'}
      ],
      TIPO_MOVIMENTO_CONTA_CREDITO: 2,
      titulos: [],
      dadosEnvio:{},
      pagamento:{}

    }
  },

  computed: {
    ...mapState('root', ['franqueadaSelecionada', 'usuarioLogado']),
    ...mapState('tituloReceber', ['titulosQuitar','titulo','tituloMovimentos','tituloPagamentos']),
    ...mapState('conta', {listaContas: 'lista'}),

    listaFormasPagamento: {
      get () {
        return this.$store.state.formaPagamento.lista
      }
    },

    franqueada: {
      get () {
        const franqueadaObject = this.usuarioLogado.franqueadas.find(f => (f.id === this.usuarioLogado.franqueadaSelecionada))
        return franqueadaObject
      }
    }
  },

  watch: {
    franqueadaSelecionada () {
      this.$store.commit('franqueadas/setFranqueadaSelecionada', this.franqueadaSelecionada)
      this.$store.dispatch('franqueadas/getFranqueada')
    }
  },

  mounted () {

    this.titulo.pagamentos = []
    

    this.carregarDados()

    // EventBus.$on('quitar-modal:abrir', (atualizarTitulos = true) => {
    //   if (atualizarTitulos === true) {
    //     this.titulos = []
    //     setTimeout(this.processarTitulos, 50)
    //   } else {
    //     this.processarTitulos()
    //   }
    // })
    // this.getListaConta()
  },
  methods: {
    ...mapActions('tituloReceber', ['consultarDetalhesTitulo','consultarMovimentosTitulo']),
    ...mapActions('tituloReceber', ['receber','receber_novo','aplicarDescontoManual']),
    ...mapActions('calendario', ['buscar']),
    ...mapActions('conta', {getListaConta: 'getLista'}),

    async buscaInformacoes(id) {
      try {
        await this.consultarDetalhesTitulo(id);
        await this.consultarMovimentosTitulo(id);
        
       
        // const pagamentos = this.gerarInformacoesPagamentosAnteriores(this.titulo)
        
        var pagamentos = this.tituloPagamentos.filter((pagamento) => {
            if (pagamento.situacao !== 'PEN' && pagamento.situacao !== 'P') {
              return false
            }
            return true
          })

  

          
         if (pagamentos.length === 0) {

          const pagamento = {
            data_recebimento: dateToString(new Date()),
            valor_montante: round(this.titulo.valor_saldo_devedor),
            // forma_recebimento: titulo.forma_cobranca
            }

          pagamento.valor_multa = 0
          pagamento.valor_juros = 0
          pagamento.valor_diferenca_baixa = 0
      
          
          
          pagamento.valor_item = round(this.titulo.valor_item)
          pagamento.valor_parcela = round(this.titulo.valor_parcela)

          pagamento.forma_recebimento = this.listaFormasPagamento.find(value => value.forma_cartao === true)
          
          if (pagamento.forma_recebimento.forma_cartao === true) {
            pagamento.transacao_cartao = {...defaultData.transacao_cartao}
          }

          if (pagamento.forma_recebimento.forma_cheque === true) {
            pagamento.cheque = {...defaultData.cheque}
          }

          if (pagamento.forma_recebimento.forma_transferencia === true) {
            pagamento.transferencia_bancaria = {...defaultData.transferencia_bancaria}
          }
          if (pagamento.forma_recebimento.forma_boleto === true) {
            pagamento.boleto = {...defaultData.boleto}
          }
          pagamento.situacao = 'PEN'
          pagamentos.push(pagamento)
        }
        this.pagamento = pagamentos[pagamentos.length - 1]
            //var pagamentos = [pagamentos[pagamentos.length - 1]];        

        this.pagamento.valor_montante = round(this.titulo.valor_saldo_devedor)

        // this.titulo.pagamentos = pagamentos.map(pagamento => {
        
          
          this.pagamento.valor_multa = 0
          this.pagamento.valor_juros = 0
          this.pagamento.valor_diferenca_baixa = 0
          if(this.titulo.conta_id != null){
            this.pagamento.conta = this.listaContas.find(c => (c.id == this.titulo.conta_id))
            // console.log(this.listaContas)
          }

          this.pagamento.valor_item = round(this.titulo.valor_item)
          this.pagamento.valor_parcela = round(this.titulo.valor_parcela)

          
          this.pagamento.data_recebimento = dateToString(new Date(this.titulo.data_vencimento))


          if(this.pagamento.tipo === 'CARTAO'){
            if (!this.pagamento.transacao_cartao  ) {
                this.pagamento.transacao_cartao = {...defaultData.transacao_cartao}
            }
            this.pagamento.forma_recebimento = this.listaFormasPagamento.find(value => value.forma_cartao === true)
              // pagamento.transacao_cartao.id = pagamento.id
          }
          if(this.pagamento.tipo === 'BOLETO'){
            if (!this.pagamento.boleto  ) {
                this.pagamento.boleto = {...defaultData.boleto}
            }
            this.pagamento.forma_recebimento = this.listaFormasPagamento.find(value => value.forma_boleto === true)
            this.pagamento.data_recebimento = dateToString(new Date(this.titulo.data_prorrogacao))
              // pagamento.boleto.id = pagamento.id

          }
          if(this.pagamento.tipo === 'CHEQUE'){
            if (!this.pagamento.cheque  ) {
                this.pagamento.cheque = {...defaultData.cheque}
                this.pagamento.cheque.conta = this.pagamento.conta_corrente
                this.pagamento.cheque.banco = this.pagamento.banco
                this.pagamento.cheque.numero = this.pagamento.numero
                this.pagamento.cheque.titular = this.pagamento.titular
                this.pagamento.cheque.agencia = this.pagamento.agencia
            }
            this.pagamento.forma_recebimento = this.listaFormasPagamento.find(value => value.forma_cheque === true)
              // pagamento.cheque.id = pagamento.id
          }
          if(this.pagamento.tipo === 'TRANSFERENCIA'){
            if (!this.pagamento.transferencia_bancaria  ) {
                this.pagamento.transferencia_bancaria = {...defaultData.transferencia_bancaria}
            }
            this.pagamento.forma_recebimento = this.listaFormasPagamento.find(value => value.forma_transferencia === true)
              // pagamento.transferencia_bancaria.id = pagamento.id
          }
          
          this.pagamento.desconto_antecipacao =  round(this.titulo.desconto_antecipacao) 
         
      
          this.pagamento.bloqueado = this.pagamento.valor_montante === 0
          
        //   return pagamento
        // })

        // this.calcularValorMultaJuros(true)

        // this.atualizaValores()
        
      } catch (error) {
        console.error('Erro ao baixar dados:', error);
      }
    },

    atualizaValores (){
      setTimeout(() => {
        this.$forceUpdate()
      }, 10)

    },

    dateToString: dateToString,

    carregarDados (){
          this.buscaInformacoes(this.titulo.id)
      },

    // processarTitulos () {
    //   this.titulos = this.titulosQuitar.map((t, index) => {
    //     const titulo = {...t}
    //     titulo = {...titulo}

    //     const pagamentos = this.gerarInformacoesPagamentosAnteriores(titulo)

        // if (pagamentos.length === 0) {
        //   const pagamento = {
        //     data_recebimento: dateToString(new Date()),
        //     valor_montante: round(titulo.valor_saldo_devedor),
        //     forma_recebimento: titulo.forma_cobranca
        //   }

        //   if (pagamento.forma_recebimento.forma_cartao === true) {
        //     pagamento.transacao_cartao = {...defaultData.transacao_cartao}
        //   }

        //   if (pagamento.forma_recebimento.forma_cheque === true) {
        //     pagamento.cheque = {...defaultData.cheque}
        //   }

        //   if (pagamento.forma_recebimento.forma_transferencia === true) {
        //     pagamento.transferencia_bancaria = {...defaultData.transferencia_bancaria}
        //   }

        //   pagamentos.push(pagamento)
        // }

    //     if (pagamentos.length === 1) {
    //       pagamentos[0].valor_montante = round(titulo.valor_saldo_devedor)
    //     }

        // Setando os valores default pra pagamentos, que são iguais pra qualquer tipo de pagamento
        // titulo.pagamentos = pagamentos.map(pagamento => {
        //   pagamento.valor_multa = 0
        //   pagamento.valor_juros = 0
        //   pagamento.valor_diferenca_baixa = 0
        //   pagamento.conta = titulo.conta
        //   pagamento.valor_item = round(titulo.valor_item)
        //   pagamento.valor_parcela = round(titulo.valor_parcela)
        //   const movimentosContaNaoEstornados = titulo.movimento_conta.filter((movimento) => {
        //     if (movimento.movimento_estorno || movimento.estornado) {
        //       return false
        //     }
        //     return true
        //   })
        //   if (movimentosContaNaoEstornados.length > 0) {
        //     pagamento.desconto_antecipacao = 0
        //   } else {
        //     pagamento.desconto_antecipacao = round(titulo.desconto_antecipacao) || 0
        //   }
        //   pagamento.valor_desconto_super_amigo = round(titulo.valor_desconto_super_amigo) || 0
        //   pagamento.bloqueado = pagamento.valor_montante === 0
        //   return pagamento
        // })

    //     return titulo
    //   })

    //   this.calcularValorMultaJuros(true)
    // },

    // valorMontanteMovimento (movimento) {
    //   let valorLancamento = parseFloat(movimento.valor_lancamento)
    //   if (movimento.transacao_cartao && movimento.transacao_cartao.taxa) {
    //     valorLancamento += parseFloat(movimento.transacao_cartao.taxa)
    //   }
    //   return valorLancamento
    // },

    // gerarInformacoesPagamentosAnteriores (titulo) {
    //   const pagamentos = []
    //   if (titulo.boletos.length > 0) {
    //     titulo.boletos.forEach(boleto => {
    //       if (boleto.situacao_cobranca !== 'PEN' && boleto.situacao_cobranca !== 'ENV') {
    //         return
    //       }
    //       const pagamento = {
    //         boleto: {...boleto},
    //         data_recebimento: dateToString(new Date(titulo.data_prorrogacao)),
    //         valor_montante: round(titulo.valor_saldo_devedor),
    //         forma_recebimento: this.listaFormasPagamento.find(value => value.forma_boleto === true)
    //       }
    //       pagamentos.push(pagamento)
    //     })
    //   }

    //   if (titulo.cheques.length > 0) {
    //     titulo.cheques.forEach(cheque => {
    //       if (cheque.situacao !== 'P') {
    //         return
    //       }
    //       const pagamento = {
    //         cheque: {...cheque},
    //         data_recebimento: dateToString(new Date(cheque.data_bom_para)),
    //         valor_montante: round(cheque.valor),
    //         forma_recebimento: this.listaFormasPagamento.find(value => value.forma_cheque === true)
    //       }
    //       pagamentos.push(pagamento)
    //     })
    //   }

    //   if (titulo.transacoes_cartao.length > 0) {
    //     titulo.transacoes_cartao.forEach(transacao => {
    //       if (transacao.situacao !== 'PEN') {
    //         return
    //       }
    //       const dataBaseRecebimento = transacao.data_pagamento ? transacao.data_pagamento : transacao.previsao_repasse
    //       const pagamento = {
    //         transacao_cartao: {...transacao},
    //         data_recebimento: dateToString(new Date(dataBaseRecebimento)),
    //         valor_montante: round(transacao.valor_liquido),
    //         forma_recebimento: this.listaFormasPagamento.find(value =>
    //           value.forma_cartao === true && value.forma_cartao_debito === (transacao.tipo_transacao === 'D')
    //         )
    //       }
    //       pagamentos.push(pagamento)
    //     })
    //   }

    //   if (titulo.transferencias_bancarias.length > 0) {
    //     titulo.transferencias_bancarias.forEach(transferencia => {
    //       if (transferencia.situacao !== 'PEN') {
    //         return
    //       }
    //       const pagamento = {
    //         transferencia_bancaria: {...transferencia},
    //         data_recebimento: dateToString(new Date()),
    //         valor_montante: round(transferencia.valor),
    //         forma_recebimento: this.listaFormasPagamento.find(value => value.forma_transferencia === true)
    //       }
    //       pagamentos.push(pagamento)
    //     })
    //   }
    //   return pagamentos
    // },

    cancelar () {
      this.$emit('cancelar')
    },

    salvar () {
      this.salvando = true

       
       let possuiValorNegativo = false
     // this.titulos.forEach(titulo => {
        // this.titulo.pagamentos.forEach(pagamento => {
        //   if (this.valorLancamento(pagamento, this.titulo) < 0) {
        //     possuiValorNegativo = true
        //   }
        //   movimentosConta.push(this.gerarDadosMovimentoConta(pagamento, this.titulo))
        // })
     // })

      if (possuiValorNegativo) {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: 'O total a receber de um pagamento não pode ser negativo.'
        })
        this.salvando = false
        return false
      }

      // this.receber(movimentosConta)
      //   .then(() => {
      //     this.$emit('reload-list')
      //     this.cancelar()
      //   })
      //   .finally(() => {
      //     this.salvando = false
      //   })


      this.pagamento = this.titulo.pagamentos[this.titulo.pagamentos.length -1]

      if ( !this.pagamento) {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: 'Ocorreu um problema ao processar o pagaemnto. Tente novamente.'
        })
        this.salvando = false
        return false
      }
       


      
        this.dadosEnvio = this.montaDados(this.titulo)
       this.receber_novo(this.dadosEnvio)
        .then(() => {
          this.$emit('reload-list')
          this.cancelar()
        })
        .finally(() => {
          this.salvando = false
        })
      this.salvando = false
    },

    montaDados(titulo){
      console.log('montar dados',this.pagamento)
      const dados = {
        titulo_id: titulo.id,
        conta_id:this.pagamento.conta.id,
        forma_pagamento_id: this.pagamento.forma_recebimento.id,
        data_recebimento:dateToDBStringConvert(stringToDateConvert(this.pagamento.data_recebimento)),
        valor_lancamento:Math.round(Number(this.pagamento.valor_item) * 100) / 100,
        valor_juros:Math.round(Number(this.pagamento.valor_juros) * 100) / 100,
        valor_multa:Math.round(Number(this.pagamento.valor_multa) * 100) / 100,
        valor_desconto:Math.round(Number(this.pagamento.desconto_antecipacao) * 100 + Number(this.pagamento.valor_desconto_manual) * 100) / 100,        
        valor_desconto_antecipacao:Math.round(Number(this.pagamento.desconto_antecipacao)  * 100) / 100,        
        valor_desconto_manual:Math.round(Number(this.pagamento.valor_desconto_manual) * 100) / 100,        
        motivo_desconto_manual:this.pagamento.motivo_desconto_manual,        
        observacao:'',
        cheque:[],
        boleto:[],
        transacao_cartao:[],
        transferencia_bancaria:[]
      }
      let isCartao = false;
      let isCartaoDebito = false;
      // console.log(this.pagamento.forma_recebimento);
      let forma = null;
      if ( this.pagamento.forma_recebimento ){
        forma = this.pagamento.forma_recebimento;          
      } 
      else
      if (this.pagamento.forma_pagamento ){
        forma = this.pagamento.forma_pagamento;     
      } 
      
      if(forma.forma_cartao === true || 
        forma.forma_cartao_debito === true ){
            isCartao = true;
            if (forma.forma_cartao_debito === true ) {
              isCartaoDebito = true;
            }
      }

     


      if (isCartao === true ) {
        if (this.pagamento.transacao_cartao) {
          this.pagamento.transacao_cartao.valor_liquido = dados.valor_lancamento
          this.pagamento.transacao_cartao.valor_desconto = dados.valor_desconto          
          dados.transacao_cartao.data_recebimento = dados.data_recebimento
       
        }

        dados.transacao_cartao = this.pagamento.transacao_cartao
        if (dados.transacao_cartao.operadora_cartao && isCartaoDebito === false) {
          dados.transacao_cartao.operadora_cartao = dados.transacao_cartao.operadora_cartao.id
        }

          if (dados.transacao_cartao.parcelamento_operadora_cartao && isCartaoDebito === false && isCartaoDebito === false) {
             dados.transacao_cartao.parcelamento_operadora_cartao = dados.transacao_cartao.parcelamento_operadora_cartao.id
          }
          dados.transacao_cartao.data_pagamento = dados.data_recebimento
          if (this.pagamento.transacao_cartao.id === undefined) {
            this.pagamento.transacao_cartao.id = null
          }
      }

      if (forma.forma_transferencia === true) {
        dados.transferencia_bancaria = this.pagamento.transferencia_bancaria
        dados.transferencia_bancaria.data_recebimento = dados.data_recebimento

      }

      if (forma.forma_cheque === true) {
        this.pagamento.cheque.valor = dados.valor_lancamento
        this.pagamento.cheque.valor_desconto = dados.valor_desconto
        dados.cheque = this.pagamento.cheque
        dados.cheque.data_recebimento = dados.data_recebimento
      }

      if (forma.forma_boleto === true) {
        dados.boleto = this.pagamento.boleto
        dados.boleto.data_recebimento = dados.data_recebimento
      }
                
      return dados
    },

    // gerarDadosMovimentoConta (pagamento, titulo) {
    //   const movimentoConta = {
    //     conta: pagamento.conta.id,
    //     titulo_receber: titulo.id,
    //     tipo_movimento_conta: this.TIPO_MOVIMENTO_CONTA_CREDITO,
    //     forma_pagamento: pagamento.forma_recebimento.id,
    //     data_recebimento: stringToISODate(pagamento.data_recebimento, true),
    //     valor_montante: round(pagamento.valor_montante),
    //     valor_lancamento: round(this.valorLancamento(pagamento, titulo)),
    //     valor_juros: round(pagamento.valor_juros),
    //     valor_multa: round(pagamento.valor_multa),
    //     valor_desconto: round(pagamento.desconto_antecipacao),
    //     desconto_antecipacao: round(pagamento.desconto_antecipacao),
    //     valor_diferenca_baixa: round(pagamento.valor_diferenca_baixa),
    //     conciliado: pagamento.forma_recebimento.liquidacao_imediata === true ? 'S' : 'N',
    //     pode_editar_contas_receber: true
    //   }

    //   if ((pagamento.forma_recebimento && pagamento.forma_recebimento.forma_cartao === true) ||
    //   (pagamento.forma_pagamento && pagamento.forma_pagamento.forma_cartao === true)) {
    //     if (pagamento.transacao_cartao) {
    //       pagamento.transacao_cartao.valor_liquido = movimentoConta.valor_lancamento
    //       pagamento.transacao_cartao.valor_desconto = movimentoConta.valor_desconto
    //     }
    //     movimentoConta.transacao_cartao = {...pagamento.transacao_cartao}
    //     if (movimentoConta.transacao_cartao.operadora_cartao) {
    //       movimentoConta.transacao_cartao.operadora_cartao = movimentoConta.transacao_cartao.operadora_cartao.id
    //     }

    //     if (movimentoConta.transacao_cartao.parcelamento_operadora_cartao) {
    //       movimentoConta.transacao_cartao.parcelamento_operadora_cartao = movimentoConta.transacao_cartao.parcelamento_operadora_cartao.id
    //     }
    //     movimentoConta.transacao_cartao.data_pagamento = movimentoConta.data_recebimento
    //   }

    //   if (pagamento.forma_recebimento.forma_transferencia === true) {
    //     movimentoConta.transferencia_bancaria = {...pagamento.transferencia_bancaria}
    //   }

    //   if (pagamento.forma_recebimento.forma_cheque === true) {
    //     pagamento.cheque.valor = movimentoConta.valor_lancamento
    //     pagamento.cheque.valor_desconto = movimentoConta.valor_desconto
    //     movimentoConta.cheque = {...pagamento.cheque}
    //   }

    //   if (pagamento.forma_recebimento.forma_boleto === true) {
    //     movimentoConta.boleto = {...pagamento.boleto}
    //   }
    //   return movimentoConta
    // },

    setPagamento (value) {
      this.titulo.pagamentos[this.titulo.pagamentos.length -1] = value
      

      this.atualizaValores()
      // pagamento = {...value}

      // if (value.forma_cartao === true && !pagamento.transacao_cartao) {
      //   pagamento.transacao_cartao = {...defaultData.transacao_cartao}
      // } else if (value.forma_cheque === true && !pagamento.cheque) {
      //   pagamento.cheque = {...defaultData.cheque}
      // } else if (value.forma_transferencia === true && ! pagamento.transferencia_bancaria)  {
      //   pagamento.transferencia_bancaria = {...defaultData.transferencia_bancaria}
      // }
      // else if (value.forma_boleto === true  && !pagamento.boleto) {
      //   pagamento.boleto = {...defaultData.boleto}
      // }

     
    },

    // atualizaDadosPreenchidos (value,  indexPagamentoForm) {
    //   this.titulo.pagamentos[indexPagamentoForm]  = value
    // },


    // setConta (value, {indexTituloForm, indexPagamentoForm}) {
    //   const pagamento = this.titulo.pagamentos[indexPagamentoForm]
    //   pagamento.conta = value
    // },

    valorSaldoPosOperacao (titulo) {
      return round(titulo.valor_saldo_devedor) - this.valorMontanteTotal(titulo)
    },

    valorMontanteTotal (titulo) {
      return round(this.pagamento.valor_montante )
      return round(titulo.pagamentos.reduce((acc, curr) => acc + curr.valor_montante, 0))
    },

    // valorLancamento (pagamento) {
    //   let valorLancamento = round(pagamento.valor_montante) + round(pagamento.valor_multa) + round(pagamento.valor_juros) - round(pagamento.desconto_antecipacao)
    //   return round(valorLancamento)
    // },

    // calcularValorMultaJuros (onMount) {
    //   var taxa_multa = 2.00
    //   var taxa_juro_dia = 0.033
    //   console.log("calculando multa juros e descontos")
    //     this.titulo.pagamentos.forEach(pagamento => {
    //       if (onMount) {
    //         pagamento.desconto_antecipacao_original = pagamento.desconto_antecipacao
    //       }

    //       let dataVencimento = dateToString(new Date(this.titulo.data_vencimento))
    //       dataVencimento = moment(dataVencimento, 'DD/MM/YYYY')
    //       const dataRecebimento = moment(pagamento.data_recebimento, 'DD/MM/YYYY')

    //       this.$store.commit('calendario/SET_DATA', dataVencimento.toDate())
    //       this.$store.dispatch('calendario/verificaFeriadoBancario').then((result) => {
    //         const dataMaximaSemMulta = moment(result, 'YYYY-MM-DD')

    //         const diferencaDias = dataRecebimento.diff(dataMaximaSemMulta, 'days')
    //         const ehPagamentoCartao = pagamento.forma_recebimento && pagamento.forma_recebimento.forma_cartao === true

    //         if (diferencaDias > 0 && !ehPagamentoCartao) {
    //           pagamento.desconto_antecipacao = 0
    //         } else if (diferencaDias <= 0 && pagamento.desconto_antecipacao === 0) {
    //           pagamento.desconto_antecipacao = pagamento.desconto_antecipacao_original
    //         }

    //         const formaPagamentoGeraMulta = pagamento.forma_recebimento &&
    //                                         pagamento.forma_recebimento.forma_cartao === false

    //         if (diferencaDias > 0 && formaPagamentoGeraMulta) {
    //           if (taxa_multa > 0) {
    //             pagamento.valor_multa = pagamento.valor_montante * (taxa_multa / 100)
    //           } else {
    //             pagamento.valor_multa = 0
    //           }

    //           if (taxa_juro_dia > 0) {
    //             const valorJuros = (taxa_juro_dia / 100) * diferencaDias
    //             pagamento.valor_juros = pagamento.valor_montante * valorJuros
    //           } else {
    //             pagamento.valor_juros = 0
    //           }
    //         } else {
    //           pagamento.valor_multa = 0
    //           pagamento.valor_juros = 0
    //         }
    //         pagamento.valor_desconto = pagamento.desconto_antecipacao
    //       })
    //     })
      

    //   // this.titulosQuitar.map((titulo, index) => {
    //     const date = new Date()
    //     const limitePagamentoSemJuros = new Date(this.titulo.data_vencimento)
    //     limitePagamentoSemJuros.setHours(23)
    //     limitePagamentoSemJuros.setMinutes(59)
    //     limitePagamentoSemJuros.setSeconds(59)
    //     const diferencaDias = Math.ceil((date - limitePagamentoSemJuros) / (1000 * 60 * 60 * 24))
    //     if (diferencaDias > 0) {
    //       if (onMount) {
    //         this.titulo.desconto_antecipacao = 0
    //       }

    //       if (taxa_multa > 0) {
    //         this.titulo.valor_multa = this.titulo.valor_montante * (taxa_multa / 100)
    //       }

    //       if (taxa_juro_dia > 0) {
    //         const valorJuros = taxa_juro_dia / 100 * diferencaDias
    //         this.titulo.valor_juros = this.titulo.valor_montante * valorJuros
    //       }
    //     }
    //   // })
    // },

    // adicionarPagamento (indexTitulo) {
    //   this.titulos[indexTitulo].pagamentos.push({
    //     forma_recebimento: null,
    //     data_recebimento: dateToString(new Date()),
    //     valor_montante: 0,
    //     desconto_antecipacao: 0,
    //     desconto_antecipacao_original: 0,
    //     valor_multa: 0,
    //     valor_juros: 0,
    //     valor_diferenca_baixa: 0
    //   })
    // },

    // removerPagamento (indexTitulo, indexPagamento) {
    //   this.titulos[indexTitulo].pagamentos.splice(indexPagamento, 1)
    // }
  }
}
</script>

<style scoped>
.table.table-scroll {
  height: auto;
}
.form-receber{
  background-color: lavender;
    padding: 15px;
    margin-bottom: 30px;
}
.pagamentos{
  background-color: lavender;
  margin-top: 10px;
  margin-bottom: 30px;
}
.btn-history{
  background-color: #dedede;
}
.container{
  border: 2px dashed rgb(204, 204, 204);
  margin-top: 5px;
  margin-bottom: 10px;
  padding: 5px;
}
</style>
