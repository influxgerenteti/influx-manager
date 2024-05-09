<template>
  <div>

    <b-row v-if="pagamentoObjeto.valor_montante !== 0" class="mx-2">
      <b-col md="2">
        <label v-help-hint="'form-quitar-conta-receber_recebimento'" :for="`forma_recebimento`" class="col-form-label">Será recebido com</label>
        <g-select :id="`forma_recebimento`"
                  :class="!estaValidoForm && !formaRecebimento ? 'invalid-input' : 'valid-input'"
                  :value="pagamentoObjeto.forma_recebimento"
                  :select="setFormaRecebimento"
                  :options="opcoesPagamentos"
                  label="descricao"
                  track-by="id"
                  @change="operadoraCartaoSelecionada = null, operadoraCartaoParcelamentoSelecionada = null"
                  @input="atualizaValores()" />
      </b-col>

      <b-col md="2">
        <label v-help-hint="'form-quitar-conta-receber_data_recebimento'" :for="`data_recebimento`" class="col-form-label">Data do recebimento</label>
        <g-datepicker
                      :element-id="`data_recebimento`"
                      :value="pagamentoObjeto.data_recebimento"
                      :selected="setDataRecebimento"/>

        <!-- <g-datepicker v-input-locker="{permissao: permissoes['ALTERAR_DATA_RECEBIMENTO']}"
                      :id="`data_recebimento`"
                      :value="pagamentoObjeto.data_recebimento"
                      :selected="setDataRecebimento"
                      :extra-param="{indexTituloForm, indexPagamentoForm}"
                      @input="atualizaValores()"
                      @blur="atualizaValores()"
                      :max-date="dateToString(new Date())"
                      />  -->
                      <!-- :max-date="dateToString(new Date())"-->
      </b-col>

      <b-col md="2">
        <label v-help-hint="'form-quitar-conta-receber_conta_recebimento'" :for="`conta_recebimento`" class="col-form-label">Conta destino</label>

        <g-select :id="`conta_recebimento`"
                  :class="!estaValidoForm && !contaRecebimento ? 'invalid-input' : 'valid-input'"
                  :value="pagamentoObjeto.conta"
                  :select="setConta"
          
                  :options="listaContas"
                  label="descricao"
                  track-by="id"/>
      </b-col>

      <b-col v-if="pagamentoObjeto.forma_recebimento && pagamentoObjeto.forma_recebimento.forma_cheque === true" md="6">
        <b-row>
          <b-col md="2">
            <label v-help-hint="'form-quitar-conta-receber_numero_cheque'" :for="`cheque_numero`" class="col-form-label">Nº Cheque</label>
            <input v-number :id="`cheque_numero`" :disabled="pagamentoObjeto.bloqueado" v-model="pagamentoObjeto.cheque.numero" type="text" class="form-control" maxlength="9">
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-quitar-conta-receber_titular_cheque'" :for="`cheque_titular`" class="col-form-label">Titular</label>
            <input v-model="pagamentoObjeto.cheque.titular" :id="`cheque_titular`" :disabled="pagamentoObjeto.bloqueado" class="form-control" type="text" maxlength="50">
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-quitar-conta-receber_banco_cheque'" :for="`cheque_banco`" class="col-form-label">Banco</label>
            <input v-model="pagamentoObjeto.cheque.banco" :id="`cheque_banco`" :disabled="pagamentoObjeto.bloqueado" class="form-control" type="text" maxlength="50">
          </b-col>

          <b-col md="2">
            <label v-help-hint="'form-quitar-conta-receber_agencia_cheque'" :for="`cheque_agencia`" class="col-form-label">Agência</label>
            <input v-model="pagamentoObjeto.cheque.agencia" :id="`cheque_agencia`" :disabled="pagamentoObjeto.bloqueado" class="form-control" type="text" maxlength="10">
          </b-col>

          <b-col md="2">
            <label v-help-hint="'form-quitar-conta-receber_conta_cheque'" :for="`cheque_conta`" class="col-form-label">Conta</label>
            <input v-model="pagamentoObjeto.cheque.conta" :id="`cheque_conta`" :disabled="pagamentoObjeto.bloqueado" class="form-control" type="text" maxlength="20">
          </b-col>
        </b-row>
      </b-col>

      <b-col v-else-if="pagamentoObjeto.forma_recebimento && pagamentoObjeto.forma_recebimento.forma_cartao === true" md="6">
        <b-row>
          <b-col md="3">
            <label v-help-hint="'form-quitar-conta-receber_digitos_cartao'" :for="`idCartao`" class="col-form-label">Dígitos do cartão</label>
            <input v-number :id="`idCartao`" :disabled="pagamentoObjeto.bloqueado" v-model="pagamentoObjeto.transacao_cartao.identificador" type="text" class="form-control" maxlength="4">
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-quitar-conta-receber_transacao_cartao'" :for="`transacaoCartao`" class="col-form-label">Transação</label>
            <template v-if="pagamentoObjeto.bloqueado">
              <span class="form-control form-control-disabled">{{ pagamentoObjeto.transacao_cartao.numero_lancamento }}</span>
            </template>
            <input v-else v-model="pagamentoObjeto.transacao_cartao.numero_lancamento" :id="`transacaoCartao`" class="form-control" type="text" maxlength="20">
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-quitar-conta-receber_operadora_cartao'" :for="`operadora_cartao`" class="col-form-label">Operadora</label>
            <template v-if="pagamentoObjeto.bloqueado">
              <span v-if="pagamentoObjeto.transacao_cartao.operadora_cartao" class="form-control form-control-disabled text-truncate">{{ pagamentoObjeto.transacao_cartao.operadora_cartao.descricao }}</span>
            </template>

            <template v-else>
              <g-select
                :value="operadoraCartaoSelecionada"
                :select="setOperadoraCartaoPagamento"
                :options="listaOperadorasValidas(pagamentoObjeto)"
                :id="`operadora_cartao`"
                :form-valido="estaValidoForm"
                label="descricao"
                track-by="id" />
            </template>
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-quitar-conta-receber_parcelamento_operadora_cartao_cartao'" :for="`parcelamento_operadora_cartao`" class="col-form-label">Parcelamento</label>
            <template v-if="pagamentoObjeto.bloqueado">
              <span v-if="pagamentoObjeto.transacao_cartao.parcelamento_operadora_cartao" class="form-control form-control-disabled text-truncate">{{ pagamentoObjeto.transacao_cartao.parcelamento_operadora_cartao.descricao }}</span>
            </template>

            <template v-else>
              <g-select
                v-if="operadoraCartaoSelecionada"
                :value="operadoraCartaoParcelamentoSelecionada"
                :select="setParcelamentoOperadoraCartaoPagamento"
                :options="listaParcelamentoOperadoraCartao(pagamentoObjeto)"
                :id="`parcelamento_operadora_cartao`"
                :form-valido="estaValidoForm"
                label="descricao"
                track-by="id" />
              <span v-else class="form-control form-control-disabled">&nbsp;</span>
            </template>
          </b-col>
        </b-row>
      </b-col>

      <b-col v-else-if="pagamentoObjeto.forma_recebimento && pagamentoObjeto.forma_recebimento.forma_transferencia === true" md="6">
        <b-row>
          <b-col md="3">
            <label v-help-hint="'form-quitar-conta-receber_agencia_transferencia'" :for="`transferenciaAgencia`" class="col-form-label">Agência</label>
            <input v-number :id="`transferenciaAgencia`" v-model="pagamentoObjeto.transferencia_bancaria.agencia" type="text" class="form-control">
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-quitar-conta-receber_conta_transferencia'" :for="`transferenciaConta`" class="col-form-label">Conta</label>
            <input v-number :id="`transferenciaConta`" v-model="pagamentoObjeto.transferencia_bancaria.conta" type="text" class="form-control">
          </b-col>
        </b-row>
      </b-col>
    </b-row>

    <b-row class="mx-2">
      <b-col md="10">
        <b-row>
          <b-col md="2" class="celula">
            <label v-help-hint="'form-quitar-conta-receber_valor'" :for="`quitar_lancamento`" class="col-form-label">Valor principal</label>
            <vue-numeric v-input-locker="{permissao: permissoes['VALOR_PRINCIPAL']}" :id="`quitar_lancamento`" v-model="pagamentoObjeto.valor_montante" :precision="2" :max="9999999.99" :disabled="true" separator="." class="form-control text-right" @blur="atualizaValores()" />
          </b-col>

          <b-col md="2" class="celula">
            <label v-help-hint="'form-quitar-conta-receber_multa'" :for="`quitar_multa`" class="col-form-label">Multa</label>
            <vue-numeric :id="`quitar_multa`" v-model="pagamentoObjeto.valor_multa" :precision="2" :max="9999.99" :disabled="true" separator="." class="form-control text-right"/>
          </b-col>

          <b-col md="2" class="celula">
            <label v-help-hint="'form-quitar-conta-receber_juros'" :for="`quitar_juros`" class="col-form-label">Juros</label>
            <vue-numeric :id="`quitar_juros`" v-model="pagamentoObjeto.valor_juros" :precision="2" :max="9999.99" :disabled="true" separator="." class="form-control text-right"/>
          </b-col>


         
        </b-row>
        <b-row>
          <b-col md="2" class="celula">
            <label v-help-hint="'form-quitar-conta-receber_desconto'" :for="`quitar_desconto`" class="col-form-label">Desconto Antecipação</label>
            <vue-numeric :id="`quitar_desconto`" v-model="pagamentoObjeto.desconto_antecipacao" :disabled="true" :precision="2" :max="pagamentoObjeto.valor_montante" separator="." class="form-control text-right" @blur="atualizaValores()"/>
          </b-col>

          <b-col md="2" class="celula">
            <label v-help-hint="'form-quitar-conta-receber_desconto_amigos'" :for="`quitar_desconto_amigos`" class="col-form-label">Desc. Super Amigos</label>
            <vue-numeric :id="`quitar_desconto_amigos`" v-model="pagamentoObjeto.valor_desconto_super_amigo" :precision="2" :max="9999999.99" :disabled="true" separator="." class="form-control text-right" />
          </b-col>

          <b-col md="2" class="celula">
            <label v-help-hint="'form-quitar-conta-receber_desconto_manual'" :for="`quitar_desconto_manual`" class="col-form-label">Desc. Manual</label>
            <vue-numeric :id="`quitar_desconto_manual`" v-model="pagamentoObjeto.valor_desconto_manual" :precision="2" :max="9999999.99" :disabled="true" separator="." class="form-control text-right" />
          </b-col>
          <b-col md="2" class="celula">
            <label v-help-hint="'Aplica desconto de forma manual no titulo'" :for="`desconto_manual`" class="col-form-label"></label>
   
            <button :id="`desconto_manual`" :disabled="false" type="button" class="btn btn-blue btn-block text-uppercase mt-3" @click="openDialogModalDesconto()">Desconto</button>
   
          </b-col>

          <b-col md="2" class="celula">
            <label v-help-hint="'form-quitar-conta-receber_total_receber'" class="col-form-label">Total recebido</label>
            <div class="form-control form-control-disabled text-right">{{ pagamentoObjeto.valor_item | formatarNumero(2) }}</div>
          </b-col>
        </b-row>
      </b-col>

      <b-col md="2">
        <b-row>
          <b-col md="5" />

          <b-col md="2">
            <label class="col-form-label">&nbsp;</label>

            <template v-if="pagamentoObjeto.bloqueado && pagamentoObjeto.forma_recebimento">
              <div class="d-flex flex-column align-items-end">
                <template v-if="pagamentoObjeto.forma_recebimento.forma_boleto === true && pagamentoObjeto.boleto">
                  <span v-b-tooltip.viewport.left.hover :class="`circle-badge-${pagamentoObjeto.boleto.situacao_cobranca.toLowerCase()}`" :title="situacoesTitleForm[pagamentoObjeto.boleto.situacao_cobranca]" class="circle-badge"></span>
                </template>

                <template v-else-if="pagamentoObjeto.forma_recebimento.forma_cheque === true && pagamentoObjeto.cheque">
                  <span v-b-tooltip.viewport.left.hover :class="`circle-badge-${pagamentoObjeto.cheque.situacao.toLowerCase()}`" :title="situacoesTitleForm[pagamentoObjeto.cheque.situacao]" class="circle-badge"></span>
                </template>

                <template v-else-if="pagamentoObjeto.forma_recebimento.forma_cartao === true && pagamentoObjeto.transacao_cartao">
                  <span v-b-tooltip.viewport.left.hover v-if="pagamentoObjeto.transacao_cartao.id" :class="`circle-badge-${pagamentoObjeto.transacao_cartao.situacao.toLowerCase()}`" :title="situacoesTitleForm[pagamentoObjeto.transacao_cartao.situacao]" class="circle-badge"></span>
                </template>
              </div>
            </template>
          </b-col>

          <b-col md="2">
            <!-- <label class="col-form-label d-block">&nbsp;</label>
            <b-btn v-b-tooltip.viewport.left.hover :disabled="pagamentoObjeto.bloqueado || indexPagamentoForm === 0" variant="danger" size="sm" class="w-100 btn-min-width-sm" title="Remover pagamento" @click.prevent="listaObjetosFuncoesCallback.removerPagamento(indexTituloForm, indexPagamentoForm)">
              <font-awesome-icon icon="trash-alt" />
            </b-btn> -->
          </b-col>
        </b-row>
      </b-col>
    </b-row>
    <b-row>
      <b-col md="3">
              <label class="col-form-label">Saldo após a operação</label>
              <div><big><b>{{ valorSaldoPosOperacao () | formatarMoeda }}</b></big></div>
      </b-col>
     

    </b-row>
  </div>
</template>
<script>
import {mapState,mapActions} from 'vuex'
import moment from 'moment'
import {stringToISODate, dateToStringConvert, stringToDateConvert, dateToString,ISOToString} from '../../utils/date'
import {defaultData} from '../titulo-receber/titulo'
import {round} from '../../utils/number'
import franqueadas from '@/store/franqueadas'
import { DialogModalBus } from '../../eventBus';
import DialogDescontoManual from './DialogDescontoManual.vue';

export default {
  name: 'PagamentoForm',
  props: {
    opcoesPagamentos: {
      type: Array,
      required: true
    },
    situacoesTitleForm: {
      type: Object,
      required: true
    },
    listaObjetosFuncoesCallback: {
      type: Object,
      required: true
    },
    pgto: {
      type: Object,
      required: true
    },
    titulo: {
      type: Object,
      required: true
    },
    estaValidoForm: {
      type: Boolean,
      required: true
    }
  },
  data () { 
    return {
      descontoAntecipacaoOriginal:0,
      pagamentoObjeto:{},
      formaRecebimento: null,
      operadoraCartaoSelecionada: null,
      operadoraCartaoParcelamentoSelecionada: null
    }
  },
  computed: {
    ...mapState('conta', {listaContas: 'lista'}),
    ...mapState('modulos', ['permissoes']),
    ...mapActions('tituloReceber', ['aplicarDescontoManual']),
    ...mapState('operadoraCartao', {listaOperadoras: 'lista'})
  },
  mounted () {
    this.inicializarValores()
    DialogModalBus.$on('onAplicar', (data) => {
      this.pagamentoObjeto.valor_desconto_manual = data.desconto;
      this.pagamentoObjeto.motivo_desconto_manual = data.motivo;
      this.calcularValorMultaJuros ();
      // console.log("close:",data)
      // console.log(this.pagamentoObjeto)
    })
  },
  methods: {
    setOperadoraCartaoPagamento (value) {
      this.operadoraCartaoSelecionada = {...value}
      this.pagamentoObjeto.transacao_cartao.operadora_cartao = this.operadoraCartaoSelecionada
      this.atualizaValores()
    },

    setParcelamentoOperadoraCartaoPagamento (value) {
      this.operadoraCartaoParcelamentoSelecionada = {...value}
      this.pagamentoObjeto.transacao_cartao.parcelamento_operadora_cartao = this.operadoraCartaoParcelamentoSelecionada
      this.atualizaValores()
    },

    setDataRecebimento (value) {
      this.pagamentoObjeto.data_recebimento = value
      this.atualizaValores()
    },

    setFormaRecebimento (value) {
      // console.log("FORM")
      // console.log(value)
      this.pagamentoObjeto.forma_recebimento = value
      // console.log("FORM")
      // console.log(indexTituloForm)
      // console.log(indexPagamentoForm)
      // this.listaObjetosFuncoesCallback.setFormaRecebimento(value, {indexTituloForm, indexPagamentoForm})
      this.atualizaFormaDeRecebimento();
      this.atualizaValores()
      this.$forceUpdate()
      
    },
    atualizaFormaDeRecebimento(){
      if (this.pagamentoObjeto.forma_recebimento.forma_boleto) {
        if (this.pagamentoObjeto.boleto == null) {
          //cria boleto
          this.pagamentoObjeto.boleto = {...defaultData.boleto}
          
          
        }
      }
      if (this.pagamentoObjeto.forma_recebimento.forma_cartao || this.pagamentoObjeto.forma_recebimento.forma_cartao_debito) {
        if (this.pagamentoObjeto.transacao_cartao == null) {
          //cria transacao
          this.pagamentoObjeto.transacao_cartao = {...defaultData.transacao_cartao}
         
        }
      }
    
      if (this.pagamentoObjeto.forma_recebimento.forma_cheque) {
        if (this.pagamentoObjeto.cheque == null) {
          //cria cheque
          this.pagamentoObjeto.cheque = {...defaultData.cheque}
         
          
        }
      }
      if (this.pagamentoObjeto.forma_recebimento.forma_transferencia) {
        if (this.pagamentoObjeto.transferencia_bancaria == null) {
          //cria transferencia
          this.pagamentoObjeto.transferencia_bancaria = {...defaultData.transferencia_bancaria}
          
          
        }
      }

    },

    setConta (value) {
      this.pagamentoObjeto.conta = value
      // this.listaObjetosFuncoesCallback.setConta(value, {indexTituloForm, indexPagamentoForm})
      this.atualizaValores()
    },
    openDialogModalDesconto () {
      console.log("abrir");
      // let saldo = valor_saldo_devedor - descontos;
      let data = {valor:this.saldoComDesconto(),desconto:this.pagamentoObjeto.valor_desconto_manual,motivo:this.pagamentoObjeto.motivo_desconto_manual,titulo:this.titulo} ;
      console.log(this.titulo)
      DialogModalBus.$emit('open', { component: DialogDescontoManual, title: 'Aplicar Desconto Manual', props:data, closeOnClick: true })
    },

    // aplicaDescontoManual (valor,motivo) {
    //   this.aplicaDescontoManual(valor,motivo)
    //   EventBus.$emit('chamarModal', {
    //   resolve: success => {
    //     this.aplicaDescontoManual({titulo: this.titulo.id, valor: this.titulo.valor_desconto_manual, motivo:this.titulo.mo}).then(() => {
    //       this.filtrar()
    //       // TODO: Fazer com que uma vez que foi cancelado uma vez, o gerente tenha que colocar a senha novamente, caso o usuário não possua a permissão
    //       // this.gerentePermitiuCancelar = false
    //     }, err => {
    //       const mensagem = err.mensagem ? err.mensagem : 'Erro ao cancelar titulos.'
    //       EventBus.$emit('criarAlerta', {
    //         tipo: 'a',
    //         mensagem: mensagem
    //       })
    //     })
    //   }
    // }, `Tem certeza que deseja cancelar os titulos selecionados?`, 'Sim', 'Não')
    // },

    atualizaValores (){
      // envia o objeto
      // calcula os juros
      // atualiza o objeto local

      this.validaDataRecebimento()

      this.calcularValorMultaJuros()
      this.listaObjetosFuncoesCallback.setPagamento(this.pagamentoObjeto)

    },
    validaDataRecebimento(){

      const amanha = new Date();
      amanha.setDate(amanha.getDate() + 1);
      amanha.setHours(0, 0, 0, 0);

      let dataRecebimento = stringToDateConvert(this.pagamentoObjeto.data_recebimento);
      // let tmpRemake = dateToStringConvert(dataRecebimento)
      // console.log(tmpRemake)
      // console.log( typeof tmpRemake);
      // let tmpRemakeDo = stringToDateConvert(tmpRemake)
      // console.log(tmpRemakeDo)
      // console.log( typeof tmpRemakeDo);
   
      var hoje = new Date();
      hoje.setHours(12, 0, 0, 0);
      let hojeStr =dateToStringConvert(hoje)

      if (dataRecebimento >= amanha) {              
        this.pagamentoObjeto.data_recebimento = hojeStr;
        this.data_pagamento = hoje;

        if (this.pagamentoObjeto.cheque ) {
          this.pagamentoObjeto.cheque.data_recebimento = hojeStr;                  
        }
        if (this.pagamentoObjeto.boleto ) {
          this.pagamentoObjeto.boleto.data_recebimento = hojeStr;                  
        }
        if (this.pagamentoObjeto.transacao_cartao ) {
          this.pagamentoObjeto.transacao_cartao.data_recebimento = hojeStr;                  
        }
        if (this.pagamentoObjeto.transferencia_bancaria ) {
          this.pagamentoObjeto.transferencia_bancaria.data_recebimento = hojeStr;                  
        }

      }
      else{
        let dataRecebimentoStr = dateToStringConvert(dataRecebimento)
        this.pagamentoObjeto.data_recebimento = dataRecebimentoStr;
      }

      
      let dataRecebimentoStr = dateToStringConvert(dataRecebimento)
      this.pagamentoObjeto.data_pagamento = dataRecebimentoStr;

      // if (this.data_pagamento >= amanha) {
      //   const hoje = new Date(); 
      //   hoje.setHours(12, 0, 0, 0);
      //   this.data_pagamento = hoje;
      // }
    },

   
    listaOperadorasValidas (pagamentoObjeto) {
      return this.listaOperadoras.filter(op => {
        const ehPagamentoDebito = pagamentoObjeto.forma_recebimento.forma_cartao_debito === true
        const ehPagamentoCredito = pagamentoObjeto.forma_recebimento.forma_cartao_debito === false && pagamentoObjeto.forma_recebimento.forma_cartao === true
        return (ehPagamentoDebito && op.tipo_operacao === 'D') || (ehPagamentoCredito && op.tipo_operacao === 'C')
      })
    },

    listaParcelamentoOperadoraCartao (pagamentoObjeto) {
      if (!pagamentoObjeto.transacao_cartao || !pagamentoObjeto.transacao_cartao.operadora_cartao || !pagamentoObjeto.transacao_cartao.operadora_cartao.parcelamentoOperadoraCartaos) {
        return []
      }
      return pagamentoObjeto.transacao_cartao.operadora_cartao.parcelamentoOperadoraCartaos
    },

    inicializarValores () {     
      this.pagamentoObjeto = JSON.parse(JSON.stringify(this.pgto));

      this.descontoAntecipacaoOriginal = this.pagamentoObjeto.desconto_antecipacao;
      this.titulo.desconto_antecipacao = 0 // ver se esta certo zerar //to-do
          
         // this.setFormaRecebimento(this.pagamentoObjeto.forma_recebimento)
      
      if (this.pagamentoObjeto.transacao_cartao){
        if (this.pagamentoObjeto.transacao_cartao.operadora_cartao) {
          this.setOperadoraCartaoPagamento(this.pagamentoObjeto.transacao_cartao.operadora_cartao)
        }
        if (this.pagamentoObjeto.transacao_cartao.parcelamento_operadora_cartao) {
          this.setParcelamentoOperadoraCartaoPagamento(this.pagamentoObjeto.transacao_cartao.parcelamento_operadora_cartao)
        }
        this.pagamentoObjeto.transacao_cartao.id = this.pagamentoObjeto.id
        
      }
      if (this.pagamentoObjeto.boleto){
        this.pagamentoObjeto.boleto.id = this.pagamentoObjeto.id
      }
      if (this.pagamentoObjeto.cheque){
        this.pagamentoObjeto.cheque.id = this.pagamentoObjeto.id
      }
      if (this.pagamentoObjeto.transferencia_bancaria){
        this.pagamentoObjeto.transferencia_bancaria.id = this.pagamentoObjeto.id
      }

    },
    valorSaldoPosOperacao () {
      return round(this.titulo.valor_saldo_devedor) - round(this.pagamentoObjeto.valor_montante)
    },
    atrasado () {
      let dataVencimento = new Date(this.titulo.data_vencimento)
          dataVencimento = moment(dataVencimento, 'DD/MM/YYYY')
      let dataRecebimento = moment(this.pagamentoObjeto.data_recebimento,'DD/MM/YYYY')

      return dataVencimento < dataRecebimento
    },

    saldoComDesconto(){
       return this.pagamentoObjeto.valor_montante - this.pagamentoObjeto.desconto_antecipacao;
    },
   
    calcularValorMultaJuros () {
      console.log("calculo juros")
      // console.log(this.pagamentoObjeto)
      var taxa_multa = parseFloat(this.pagamentoObjeto.conta.franqueada.percentual_multa) * 1
      var taxa_juro_dia = parseFloat(this.pagamentoObjeto.conta.franqueada.percentual_juro_dia) * 1
      // parseFloat(this.pagamentoObjeto.conta.franqueada.percentual_juro_dia) *1
     let dataVencimento = new Date(this.titulo.data_vencimento)

          dataVencimento = moment(dataVencimento, 'DD/MM/YYYY')
          // let dataVencimento = moment(this.titulo.data_vencimento,'DD/MM/YYYY')
          // console.log(dataVencimento)
          let dataRecebimento = moment(this.pagamentoObjeto.data_recebimento,'DD/MM/YYYY')

          // const dataRecebimento = moment(this.pagamentoObjeto.data_recebimento, 'DD/MM/YYYY')

          this.$store.commit('calendario/SET_DATA',dataVencimento)
          this.$store.dispatch('calendario/verificaFeriadoBancario').then((result) => {
            const dataMaximaSemMulta = moment(result, 'YYYY-MM-DD')

            const diferencaDias = dataRecebimento.diff(dataMaximaSemMulta , 'days')
            const ehPagamentoCartao = this.pagamentoObjeto.forma_recebimento && this.pagamentoObjeto.forma_recebimento.forma_cartao === true
           
            console.log("É Cartao:",ehPagamentoCartao)
            if (diferencaDias > 0) {
              this.pagamentoObjeto.desconto_antecipacao = 0
            } else {
              this.pagamentoObjeto.desconto_antecipacao = this.descontoAntecipacaoOriginal
              if (!ehPagamentoCartao) {
                // APENAS PAGAMENTOS REALIZADO COM CARTÃO RECEBEM DESCONTO DE ANTECIPAÇAO, PELA LOGICA PIX TBM PODERIA ENTRAR AQUI
                this.pagamentoObjeto.desconto_antecipacao = 0
              }
            }
            
            // if (diferencaDias > 0 && !ehPagamentoCartao) {
            //   // APENAS PAGAMENTOS NO CARTÃO RECEBEM DESCONTO DE ANTECIPAÇAO
            //   this.pagamentoObjeto.desconto_antecipacao = 0
            // } else if (diferencaDias <= 0 && this.pagamentoObjeto.desconto_antecipacao === 0) {
            //   this.pagamentoObjeto.desconto_antecipacao = this.descontoAntecipacaoOriginal
            // }

            const formaPagamentoGeraMulta = this.pagamentoObjeto.forma_recebimento &&
            this.pagamentoObjeto.forma_recebimento.forma_cartao === false
            
            if (diferencaDias > 0 && formaPagamentoGeraMulta) {
              if (taxa_multa > 0) {
                this.pagamentoObjeto.valor_multa = this.pagamentoObjeto.valor_montante * (taxa_multa / 100)
              } else {
                this.pagamentoObjeto.valor_multa = 0
              }
              if (taxa_juro_dia > 0) {
                this.pagamentoObjeto.valor_juros = (this.pagamentoObjeto.valor_montante + this.pagamentoObjeto.valor_multa) * (taxa_juro_dia / 100) * diferencaDias
              } else {
                this.pagamentoObjeto.valor_juros = 0
              }
            } else {
              this.pagamentoObjeto.valor_multa = 0
              this.pagamentoObjeto.valor_juros = 0
            }

            console.log("multa:",this.pagamentoObjeto.valor_multa)
            console.log("juros:",this.pagamentoObjeto.valor_juros)            
            console.log("desconto antecipacao:",this.pagamentoObjeto.desconto_antecipacao)
            console.log("desconto manual:",this.pagamentoObjeto.valor_desconto_manual)
            
        
            this.pagamentoObjeto.valor_item = round(this.pagamentoObjeto.valor_montante ?? 0) + round(this.pagamentoObjeto.valor_multa ?? 0) + round(this.pagamentoObjeto.valor_juros ?? 0) - round(this.pagamentoObjeto.desconto_antecipacao ?? 0) - round(this.pagamentoObjeto.valor_desconto_manual ?? 0);
            this.pagamentoObjeto.valor_item = round(this.pagamentoObjeto.valor_item)
            
            console.log("valor:",this.pagamentoObjeto.valor_item )

              console.log('recalculado',this.pagamentoObjeto);

            this.$forceUpdate()
            // this.pagamentoObjeto.valor_desconto = this.pagamentoObjeto.desconto_antecipacao
          })

          


        // const date = new Date()
        // const limitePagamentoSemJuros = new Date(this.titulo.data_vencimento)
        // limitePagamentoSemJuros.setHours(23)
        // limitePagamentoSemJuros.setMinutes(59)
        // limitePagamentoSemJuros.setSeconds(59)
        // const diferencaDias = Math.ceil((date - limitePagamentoSemJuros) / (1000 * 60 * 60 * 24))
        // if (diferencaDias > 0) {
          
        //   if (taxa_multa > 0) {
        //     this.titulo.valor_multa = this.titulo.valor_montante * (taxa_multa / 100)
        //   }

        //   if (taxa_juro_dia > 0) {
        //     const valorJuros = taxa_juro_dia / 100 * diferencaDias
        //     this.titulo.valor_juros = this.titulo.valor_montante * valorJuros
        //   }
        // }
    }

  }
}
</script>
<style>
  .celula{
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
</style>
