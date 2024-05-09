<template>
  <form @submit.prevent="salvar()">
    <div v-if="titulos.length" class="mb-4">
      <div v-for="(titulo, indexTitulo) in titulos" :key="indexTitulo" class="mb-3" style="border: 2px dashed #ccc">
        <b-row class="my-2 mx-2 align-items-center">
          <b-col md="1" class="text-center">
            <span class="col-form-label">Parcela</span>
            <div>{{ titulo.titulo_receber.numero_parcela_documento }}</div>
          </b-col>

          <b-col md="2">
            <span class="col-form-label">Valor</span>
            <div>{{ titulo.titulo_receber.valor_saldo_devedor | formatarMoeda }}</div>
          </b-col>

          <b-col md="2">
            <span class="col-form-label">Vencimento</span>
            <div>{{ titulo.titulo_receber.data_prorrogacao | formatarData }}</div>
          </b-col>

          <b-col md="2">
            <span class="col-form-label">Forma de Cobrança</span>
            <div>{{ titulo.titulo_receber.forma_cobranca.descricao }}</div>
          </b-col>

          <b-col md="2">
            <span class="col-form-label">Sacado</span>
            <div>{{ titulo.sacado_pessoa.nome_contato }}</div>
          </b-col>
          
          <b-col md="3">
            <div v-if="titulo.titulo_receber.situacao == 'CAN'">
              <span class="col-form-label">Cancelado</span>
              <p><span v-html="titulo.titulo_receber.observacao"></span></p>
            </div>
            <div v-if="titulo.titulo_receber.situacao == 'SUB'">
              <span class="col-form-label">Renegociado</span>
              <p><span v-html="titulo.titulo_receber.observacao"></span></p>
            </div>
          </b-col>

          <b-col md="2" class="text-right"/>
        </b-row>

        <template v-if="titulo.titulo_receber.movimento_conta.length">
          <div class="p-2" style="background: #ececec;">
            <h5 class="mb-2">Histórico de movimentos</h5>

            <b-row v-for="movimento in titulo.titulo_receber.movimento_conta" :key="movimento.id" class="mb-1">
              <b-col>
                <span class="col-form-label">Data</span>
                <div>{{ movimento.data_movimento | formatarDataHora }}</div>
              </b-col>
              <b-col>
                <span class="col-form-label">Montante</span>
                <div>{{ movimento.valor_lancamento | formatarMoeda }}</div>
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
              <b-col>
                <span class="col-form-label">Forma de pagamento</span>
                <div>{{ movimento.forma_pagamento.descricao }}</div>
              </b-col>
              <b-col>
                <span class="col-form-label">Conta</span>
                <div>{{ movimento.conta.descricao }}</div>
              </b-col>
              <b-col>
                <span class="col-form-label">Número do documento</span>
                <div>{{ movimento.numero_documento }}</div>
              </b-col>
              <template v-if="movimento.transacao_cartao">
                <b-col title="4 ultimos digitos do cartão">
                  <span class="col-form-label">Identificador</span>
                  <div>{{ movimento.transacao_cartao.identificador }}</div>
                </b-col>
                <b-col v-if="movimento.transacao_cartao.operadora_cartao">
                  <span class="col-form-label">Bandeira</span>
                  <div>{{ movimento.transacao_cartao.operadora_cartao.descricao }}</div>
                </b-col>
                <b-col v-if="movimento.transacao_cartao.parcelamento_operadora_cartao">
                  <span class="col-form-label">Parcelamento</span>
                  <div>{{ movimento.transacao_cartao.parcelamento_operadora_cartao.descricao }}</div>
                </b-col>
              </template>
              <template v-else-if="movimento.cheque">
                <b-col>
                  <span class="col-form-label">Titular</span>
                  <div>{{ movimento.cheque.titular }}</div>
                </b-col>
                <b-col>
                  <span class="col-form-label">Banco</span>
                  <div>{{ movimento.cheque.banco }}</div>
                </b-col>
                <b-col>
                  <span class="col-form-label">Agencia</span>
                  <div>{{ movimento.cheque.agencia }}</div>
                </b-col>
                <b-col>
                  <span class="col-form-label">Conta</span>
                  <div>{{ movimento.cheque.conta }}</div>
                </b-col>
              </template>
              <template v-else-if="movimento.transferencia_bancaria">
                <b-col>
                  <span class="col-form-label">Agencia</span>
                  <div>{{ movimento.transferencia_bancaria.agencia }}</div>
                </b-col>
                <b-col>
                  <span class="col-form-label">Conta</span>
                  <div>{{ movimento.transferencia_bancaria.conta }}</div>
                </b-col>
              </template>
            </b-row>
          </div>
        </template>
      </div>
    </div>

    <div>
      <b-btn type="button" variant="link" @click="cancelar()">Fechar</b-btn>
    </div>
  </form>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import EventBus from '../../utils/event-bus'
import {toNumber} from '../../utils/number'
import {dateToString} from '../../utils/date'
import {defaultData} from '../titulo-receber/titulo'

export default {
  data () {
    return {
      estaValido: true,
      salvando: false,
      titulos: []
    }
  },

  computed: {
    ...mapState('root', ['franqueadaSelecionada']),
    ...mapState('tituloReceber', ['titulosQuitar']),

    listaFormasPagamento: {
      get () {
        return this.$store.state.formaPagamento.lista
      }
    },

    franqueada: {
      get () {
        return this.$store.state.franqueadas.objFranqueada
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
    EventBus.$on('ver-modal:abrir', (atualizarTitulos = true) => {
      if (atualizarTitulos === true) {
        this.titulos = []
        setTimeout(this.processarTitulos, 50)
      } else {
        this.processarTitulos()
      }
    })
  },

  methods: {
    ...mapActions('tituloReceber', ['receber']),

    dateToString: dateToString,

    processarTitulos () {
      let calcularMultaJuros = false
      this.titulos = this.titulosQuitar.map((t, index) => {
        const titulo = {...t}
        titulo.titulo_receber = {...titulo.titulo_receber}
        const pagamentos = []

        if (titulo.titulo_receber.boletos.length > 0) {
          titulo.titulo_receber.boletos.forEach(boleto => {
            if (boleto.situacao_cobranca !== 'PEN' && boleto.situacao_cobranca !== 'ENV') {
              return
            }

            const pagamento = {}
            pagamento.bloqueado = true
            pagamento.boleto = {...boleto}
            pagamento.data_recebimento = dateToString(new Date(titulo.titulo_receber.data_prorrogacao))
            pagamento.forma_recebimento = this.listaFormasPagamento.find(value => value.forma_boleto === true)
            pagamento.valor_montante = toNumber(titulo.titulo_receber.valor_saldo_devedor)
            pagamento.valor_lancamento = pagamento.valor_montante
            pagamento.valor_desconto = 0
            pagamento.valor_multa = 0
            pagamento.valor_juros = 0
            pagamento.valor_diferenca_baixa = 0
            pagamento.valor_total = toNumber(titulo.titulo_receber.valor_saldo_devedor)

            pagamentos.push(pagamento)
          })
        }

        if (titulo.titulo_receber.cheques.length > 0) {
          titulo.titulo_receber.cheques.forEach(cheque => {
            if (cheque.situacao !== 'P') {
              return
            }

            const pagamento = {}
            pagamento.bloqueado = true
            pagamento.cheque = {...cheque}
            pagamento.data_recebimento = dateToString(new Date(cheque.data_bom_para))
            pagamento.forma_recebimento = this.listaFormasPagamento.find(value => value.forma_cheque === true)
            pagamento.valor_montante = toNumber(cheque.valor)
            pagamento.valor_lancamento = pagamento.valor_montante
            pagamento.valor_desconto = 0
            pagamento.valor_multa = 0
            pagamento.valor_juros = 0
            pagamento.valor_diferenca_baixa = 0
            pagamento.valor_total = toNumber(cheque.valor)

            pagamentos.push(pagamento)
          })
        }

        if (titulo.titulo_receber.transacoes_cartao.length > 0) {
          titulo.titulo_receber.transacoes_cartao.forEach(transacao => {
            if (transacao.situacao !== 'PEN') {
              return
            }

            const pagamento = {}
            pagamento.bloqueado = true
            pagamento.transacao_cartao = {...transacao}
            pagamento.data_recebimento = dateToString(new Date(transacao.previsao_repasse))
            pagamento.forma_recebimento = this.listaFormasPagamento.find(value =>
              value.forma_cartao === true && value.forma_cartao_debito === (transacao.tipo_transacao === 'D'))

            pagamento.valor_montante = toNumber(transacao.valor_liquido)
            pagamento.valor_lancamento = pagamento.valor_montante
            pagamento.valor_desconto = 0
            pagamento.valor_multa = 0
            pagamento.valor_juros = 0
            pagamento.valor_diferenca_baixa = 0
            pagamento.valor_total = toNumber(transacao.valor_liquido)

            pagamentos.push(pagamento)
          })
        }

        if (pagamentos.length === 0) {
          const pagamento = {}
          pagamento.data_recebimento = dateToString(new Date())
          pagamento.forma_recebimento = titulo.titulo_receber.forma_cobranca
          pagamento.valor_montante = toNumber(titulo.titulo_receber.valor_saldo_devedor)
          pagamento.valor_lancamento = pagamento.valor_montante
          pagamento.valor_desconto = 0
          pagamento.valor_multa = 0
          pagamento.valor_juros = 0
          pagamento.valor_diferenca_baixa = 0
          pagamento.valor_total = toNumber(titulo.titulo_receber.valor_saldo_devedor)

          if (pagamento.forma_recebimento.forma_cartao === true) {
            pagamento.transacao_cartao = {...defaultData.transacao_cartao}
          }

          if (pagamento.forma_recebimento.forma_cheque === true) {
            pagamento.cheque = {...defaultData.cheque}
          }

          if (pagamento.forma_recebimento.liquidacao_imediata === true) {
            pagamento.conciliar = true
          }

          if (pagamento.valor_montante === 0) {
            pagamento.bloqueado = true
          }

          pagamentos.push(pagamento)
        }

        titulo.pagamentos = pagamentos

        titulo.valor_montante_total = titulo.pagamentos.reduce((acc, curr) => acc + curr.valor_montante, 0)
        titulo.valor_saldo_pos_operacao = titulo.titulo_receber.valor_saldo_devedor - titulo.valor_montante_total

        return titulo
      })

      if (calcularMultaJuros === true) {
        this.calcularValorMultaJuros()
      }
    },

    cancelar () {
      this.$emit('cancelar')
    }
  }
}
</script>
