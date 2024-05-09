<template>
  <div class="content-sector sector-azul p-3">
    <div class="d-flex justify-content-between">
      <div>
        <h5 class="title-module mb-2">Plano de Pagamento</h5>

        <div v-if="!editando">
          <b-btn type="button" variant="roxo" @click="gerarParcelas()">Gerar Parcelas</b-btn>
          <b-btn :disabled="titulosReceber.length === 0 || valorTotalItens <= valorTotalParcelas" type="button" variant="primary" @click="incluirParcela()">Incluir Parcela</b-btn>
        </div>
      </div>

      <div :class="{'bg-danger': valorTotalItens !== valorTotalParcelas, 'bg-light': valorTotalItens === valorTotalParcelas}" class="d-flex px-2 text-right">
        <b-btn v-if="!editando && titulosReceber.length > 0 && valorTotalItens !== valorTotalParcelas" variant="link" class="text-white" @click="acertarValorParcelas()">Acertar a diferença na primeira parcela</b-btn>
        <div class="d-flex flex-column justify-content-center ml-2">
          <big class="mb-0">Total dos itens: <b>R${{ valorTotalItens | formatarNumero }}</b></big>
          <big class="mb-0">Total das parcelas: <b>R${{ valorTotalParcelas | formatarNumero }}</b></big>
          <big v-if="!editando && titulosReceber.length > 0 && valorTotalItens !== valorTotalParcelas" class="mb-0">Diferença: <b>R${{ valorTotalItens - valorTotalParcelas | formatarNumero }}</b></big>
        </div>
      </div>
    </div>

    <b-row v-if="titulosReceber.length" class="mt-3 form-group table-input-cards">
      <b-col md="4">
        <b-row>
          <b-col md="1" class="text-center"><b>#</b></b-col>
          <b-col md="7"><b v-help-hint="'form-conta-receber_forma_cobranca'">Forma de cobrança</b></b-col>
          <b-col md="4"><b v-help-hint="'form-conta-receber_vencimento'">Vencimento</b></b-col>
        </b-row>
      </b-col>
      <b-col md="8">
        <b-row>
          <b-col v-if="editando" md="2"><b v-help-hint="'form-conta-receber_valor'">Valor Bruto</b></b-col>
          <b-col md="2"><b v-help-hint="'form-conta-receber_valor'">Valor Líquido</b></b-col>
          <b-col :md="editando ? 5 : 7"><b v-help-hint="'form-conta-receber_observacoes'">Observação</b></b-col>
          <b-col md="3" />
        </b-row>
      </b-col>
    </b-row>

    <template v-for="(parcela, index) in titulosReceber">
      <b-row :key="index" class="form-group">
        <b-col md="4">
          <b-row class="align-items-center">
            <b-col md="1" class="text-center">{{ parcela.numero_parcela_documento }}</b-col>

            <b-col md="7">
              <g-select
                :value="parcela.forma_cobranca"
                :select="setFormaCobrancaParcela"
                :extra-param="index"
                :options="listaFormasPagamento"
                :disabled="editando"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
              <div v-if="!estaValido && !parcela.forma_cobranca" class="multiselect-invalid">
                Selecione uma opção
              </div>
            </b-col>

            <b-col md="4">
              <template v-if="parcela.data_vencimento">
                <div v-b-tooltip.viewport.left.hover v-if="editando" :title="parcela.data_vencimento" class="form-control form-control-disabled truncate">{{ ISOToString(parcela.data_vencimento) }}</div>
                <g-datepicker v-if="!editando" :id="`datepicker-${index}`" :class="!estaValido && !parcela.data_vencimento ? 'invalid-input' : 'valid-input'" :value="parcela.data_vencimento" :selected="setDataVencimentoParcela" :extra-param="index" maxlength="10" required />
                <div v-if="!estaValido && !parcela.data_vencimento" class="multiselect-invalid">
                  Selecione uma data
                </div>
              </template>
              <template v-else>
                <g-datepicker :id="`datepicker-${index}`" :class="!estaValido && !parcela.data_vencimento ? 'invalid-input' : 'valid-input'" :disabled="editando" :value="parcela.data_vencimento" :selected="setDataVencimentoParcela" :extra-param="index" maxlength="10" required />
                <div v-if="!estaValido && !parcela.data_vencimento" class="multiselect-invalid">
                  Selecione uma data
                </div>
              </template>
            </b-col>
          </b-row>
        </b-col>

        <b-col md="8">
          <b-row class="align-items-center">
            <b-col v-if="editando" md="2">
              <vue-numeric :precision="2" :empty-value="null" :value="parseFloat(parcela.valor_parcela_sem_desconto)" disabled separator="." class="form-control valid-input" />
            </b-col>

            <b-col md="2">
              <vue-numeric :disabled="editando" :class="!estaValido && !parcela.valor_original ? 'invalid-input' : 'valid-input'" :precision="2" :empty-value="null" v-model="parcela.valor_original" :max="9999999.99" separator="." class="form-control" required @blur="calcularTotalParcelas()" @input="setValorOriginal(parcela.valor_original, index)" />
              <div v-if="!estaValido && !parcela.valor_original" class="multiselect-invalid">
                Campo obrigatório
              </div>
            </b-col>

            <b-col :md="editando ? 5 : 7">
              <input v-model="parcela.observacao" :disabled="editando" class="form-control" type="text">
            </b-col>

            <b-col md="3">
              <b-btn v-b-tooltip v-if="!editando" class="btn-40 mt-3 mt-md-0 mt-lg-0 mt-xl-0" variant="light" title="Remover parcela" @click="removerParcela(index)">
                <font-awesome-icon icon="minus" />
              </b-btn>

              <b-btn v-b-toggle="`collapse-parcela-${index}`" v-b-tooltip v-if="parcela.forma_cobranca && (parcela.forma_cobranca.forma_cartao === true || parcela.forma_cobranca.forma_cartao_debito === true || parcela.forma_cobranca.forma_cheque === true || parcela.forma_cobranca.forma_transferencia === true)" :ref="`collapse-btn-parcela-${index}`" class="btn-40 mt-3 mt-md-0 mt-lg-0 mt-xl-0" variant="primary" title="Mostrar mais dados de recebimento">
                <font-awesome-icon icon="plus" /> Dados
              </b-btn>

              <b-btn v-b-tooltip v-if="parcela.boleto && parcela.boleto.situacao_cobranca === 'PEN'" :ref="`collapse-btn-parcela-${index}`" :href="chamarImpressao(parcela.boleto.id)" class="btn-40 mt-3 mt-md-0 mt-lg-0 mt-xl-0" variant="primary" target="_blank" title="Imprimir boleto">
                <font-awesome-icon icon="print" />
              </b-btn>
            </b-col>
          </b-row>
        </b-col>

        <b-col v-if="parcela.forma_cobranca && (parcela.forma_cobranca.forma_cartao === true || parcela.forma_cobranca.forma_cartao_debito === true || parcela.forma_cobranca.forma_cheque === true || parcela.forma_cobranca.forma_transferencia === true)" md="12">
          <b-collapse :id="`collapse-parcela-${index}`" :ref="`collapse-parcela-${index}`">
            <div class="pt-2 pb-4">
              <div class="p-2 bg-light">
                <template v-if="parcela.forma_cobranca.forma_cheque === true">
                  <b-row v-if="parcela.forma_recebimento && parcela.forma_recebimento.id !== parcela.forma_cobranca.id">
                    <b-col md="12">Parcela recebida com a forma de pagamento: {{ parcela.forma_recebimento.descricao }}</b-col>
                  </b-row>

                  <template v-else-if="parcela.cheques && parcela.cheques.length">
                    <b-row v-for="cheque in parcela.cheques" :key="cheque.id" class="form-group">
                      <b-col md="2">
                        <label v-help-hint="'form-contrato_cheque_numero'" :for="`cheque_numero_${index}`" class="form-label">Número do Cheque</label>
                        <input v-number v-model="cheque.numero" :disabled="editando" :id="`cheque_numero_${index}`" class="form-control" type="text" maxlength="9">
                      </b-col>

                      <b-col md="2">
                        <label v-help-hint="'form-contrato_cheque_titular'" :for="`cheque_titular_${index}`" class="form-label">Titular</label>
                        <input v-model="cheque.titular" :disabled="editando" :id="`cheque_titular_${index}`" class="form-control" type="text" maxlength="50">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_cheque_banco'" :for="`cheque_banco_${index}`" class="form-label">Banco</label>
                        <input v-model="cheque.banco" :disabled="editando" :id="`cheque_banco_${index}`" class="form-control" type="text" maxlength="50">
                      </b-col>

                      <b-col md="2">
                        <label v-help-hint="'form-contrato_cheque_agencia'" :for="`cheque_agencia_${index}`" class="form-label">Agência</label>
                        <input v-model="cheque.agencia" :disabled="editando" :id="`cheque_agencia_${index}`" class="form-control" type="text" maxlength="10">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_cheque_conta'" :for="`cheque_conta_${index}`" class="form-label">Conta</label>
                        <input v-model="cheque.conta" :disabled="editando" :id="`cheque_conta_${index}`" class="form-control" type="text" maxlength="20">
                      </b-col>
                    </b-row>
                  </template>

                  <template v-else-if="parcela.cheque">
                    <b-row class="form-group">
                      <b-col md="2">
                        <label v-help-hint="'form-contrato_cheque_numero'" :for="`cheque_numero_${index}`" class="form-label">Número do Cheque</label>
                        <input v-number v-model="parcela.cheque.numero" :disabled="editando" :id="`cheque_numero_${index}`" class="form-control" type="text" maxlength="9">
                      </b-col>

                      <b-col md="2">
                        <label v-help-hint="'form-contrato_cheque_titular'" :for="`cheque_titular_${index}`" class="form-label">Titular</label>
                        <input v-model="parcela.cheque.titular" :disabled="editando" :id="`cheque_titular_${index}`" class="form-control" type="text" maxlength="50">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_cheque_banco'" :for="`cheque_banco_${index}`" class="form-label">Banco</label>
                        <input v-model="parcela.cheque.banco" :disabled="editando" :id="`cheque_banco_${index}`" class="form-control" type="text" maxlength="50">
                      </b-col>

                      <b-col md="2">
                        <label v-help-hint="'form-contrato_cheque_agencia'" :for="`cheque_agencia_${index}`" class="form-label">Agência</label>
                        <input v-model="parcela.cheque.agencia" :disabled="editando" :id="`cheque_agencia_${index}`" class="form-control" type="text" maxlength="10">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_cheque_conta'" :for="`cheque_conta_${index}`" class="form-label">Conta</label>
                        <input v-model="parcela.cheque.conta" :disabled="editando" :id="`cheque_conta_${index}`" class="form-control" type="text" maxlength="20">
                      </b-col>
                    </b-row>
                  </template>

                  <b-row v-else>
                    <b-col>Informações desta forma de cobrança não informadas</b-col>
                  </b-row>
                </template>

                <template v-if="parcela.forma_cobranca.forma_transferencia === true">
                  <b-row v-if="parcela.forma_recebimento && parcela.forma_recebimento.id !== parcela.forma_cobranca.id">
                    <b-col md="12">Parcela recebida com a forma de pagamento: {{ parcela.forma_recebimento.descricao }}</b-col>
                  </b-row>

                  <template v-else-if="parcela.transferencias_bancarias && parcela.transferencias_bancarias.length">
                    <b-row v-for="transferencia in parcela.transferencias_bancarias" :key="transferencia.id" class="form-group">
                      <b-col md="3">
                        <label v-help-hint="'form-contrato_agencia_transferencia'" :for="`transferenciaAgencia${index}`" class="form-label">Agência</label>
                        <input v-number v-model="transferencia.agencia" :disabled="editando" :id="`transferenciaAgencia${index}`" class="form-control" type="text">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_conta_transferencia'" :for="`transferenciaConta${index}`" class="form-label">Conta</label>
                        <input v-number v-model="transferencia.conta" :disabled="editando" :id="`transferenciaConta${index}`" class="form-control" type="text">
                      </b-col>
                    </b-row>
                  </template>

                  <template v-else-if="parcela.transferencia_bancaria">
                    <b-row class="form-group">
                      <b-col md="3">
                        <label v-help-hint="'form-contrato_agencia_transferencia'" :for="`transferenciaAgencia${index}`" class="form-label">Agência</label>
                        <input v-number v-model="parcela.transferencia_bancaria.agencia" :disabled="editando" :id="`transferenciaAgencia${index}`" class="form-control" type="text" maxlength="255">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_conta_transferencia'" :for="`transferenciaConta${index}`" class="form-label">Conta</label>
                        <input v-number v-model="parcela.transferencia_bancaria.conta" :disabled="editando" :id="`transferenciaConta${index}`" class="form-control" type="text" maxlength="255">
                      </b-col>
                    </b-row>
                  </template>

                  <b-row v-else>
                    <b-col>Informações desta forma de cobrança não informadas</b-col>
                  </b-row>
                </template>

                <template v-if="parcela.forma_cobranca.forma_cartao === true || parcela.forma_cobranca.forma_cartao_debito === true">
                  <b-row v-if="parcela.forma_recebimento && parcela.forma_recebimento.id !== parcela.forma_cobranca.id">
                    <b-col md="12">Parcela recebida com a forma de pagamento: {{ parcela.forma_recebimento.descricao }}</b-col>
                  </b-row>

                  <template v-else-if="parcela.transacoes_cartao && parcela.transacoes_cartao.length">
                    <b-row v-for="cartao in parcela.transacoes_cartao" :key="cartao.id" class="form-group">
                      <b-col md="3">
                        <label v-help-hint="'form-contrato_id_cartao'" :for="`idCartao${index}`" class="form-label">Quatro últimos dígitos do cartão</label>
                        <input v-number v-model="cartao.identificador" :disabled="editando" :id="`idCartao${index}`" class="form-control" type="text" maxlength="4">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_transacao'" :for="`transacaoCartao${index}`" class="form-label">Transação</label>
                        <input v-model="cartao.numero_lancamento" :disabled="editando" :id="`transacaoCartao${index}`" class="form-control" type="text" maxlength="20">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_operadora_cartao'" :for="`operadora_cartao_${index}`" class="form-label">Operadora Cartão</label>
                        <g-select
                          v-if="!editando"
                          :value="cartao.operadora_cartao"
                          :select="setOperadoraCartaoParcela"
                          :extra-param="index"
                          :options="listaOperadoras.filter(op => (parcela.forma_cobranca.forma_cartao_debito === true && op.tipo_operacao === 'D') || (parcela.forma_cobranca.forma_cartao_debito === false && parcela.forma_cobranca.forma_cartao === true && op.tipo_operacao === 'C'))"
                          :id="`operadora_cartao_${index}`"
                          :form-valido="estaValido"
                          label="descricao"
                          track-by="id" />
                        <span v-else class="form-control form-control-disabled">{{ cartao.operadora_cartao.descricao }}</span>
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_parcelamento_operadora_cartao'" :for="`parcelamento_operadora_cartao_${index}`" class="form-label">Parcelamento</label>
                        <template v-if="!editando">
                          <g-select
                            v-if="cartao.operadora_cartao"
                            :value="cartao.parcelamento_operadora_cartao"
                            :select="setParcelamentoOperadoraCartaoParcela"
                            :extra-param="index"
                            :options="cartao.operadora_cartao.parcelamentoOperadoraCartaos"
                            :id="`parcelamento_operadora_cartao_${index}`"
                            :form-valido="estaValido"
                            label="descricao"
                            track-by="id" />
                          <span v-else class="form-control form-control-disabled">&nbsp;</span>
                        </template>
                        <span v-else class="form-control form-control-disabled">{{ cartao.parcelamento_operadora_cartao.descricao }}</span>
                      </b-col>
                    </b-row>
                  </template>

                  <template v-else-if="parcela.transacao_cartao">
                    <b-row class="form-group">
                      <b-col md="3">
                        <label v-help-hint="'form-contrato_id_cartao'" :for="`idCartao${index}`" class="form-label">Quatro últimos dígitos do cartão</label>
                        <input v-number v-model="parcela.transacao_cartao.identificador" :disabled="editando" :id="`idCartao${index}`" class="form-control" type="text" maxlength="4">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_transacao'" :for="`transacaoCartao${index}`" class="form-label">Transação</label>
                        <input v-model="parcela.transacao_cartao.numero_lancamento" :disabled="editando" :id="`transacaoCartao${index}`" class="form-control" type="text" maxlength="20">
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_operadora_cartao'" :for="`operadora_cartao_${index}`" class="form-label">Operadora Cartão</label>
                        <g-select
                          v-if="!editando"
                          :value="parcela.transacao_cartao.operadora_cartao"
                          :select="setOperadoraCartaoParcela"
                          :extra-param="index"
                          :options="listaOperadoras.filter(op => (parcela.forma_cobranca.forma_cartao_debito === true && op.tipo_operacao === 'D') || (parcela.forma_cobranca.forma_cartao_debito === false && parcela.forma_cobranca.forma_cartao === true && op.tipo_operacao === 'C'))"
                          :id="`operadora_cartao_${index}`"
                          :form-valido="estaValido"
                          label="descricao"
                          track-by="id" />
                        <span v-else class="form-control form-control-disabled">{{ parcela.transacao_cartao.operadora_cartao.descricao }}</span>
                      </b-col>

                      <b-col md="3">
                        <label v-help-hint="'form-contrato_parcelamento_operadora_cartao'" :for="`parcelamento_operadora_cartao_${index}`" class="form-label">Parcelamento</label>
                        <template v-if="!editando">
                          <g-select
                            v-if="parcela.transacao_cartao.operadora_cartao"
                            :value="parcela.transacao_cartao.parcelamento_operadora_cartao"
                            :select="setParcelamentoOperadoraCartaoParcela"
                            :extra-param="index"
                            :options="parcela.transacao_cartao.operadora_cartao.parcelamentoOperadoraCartaos"
                            :id="`parcelamento_operadora_cartao_${index}`"
                            :form-valido="estaValido"
                            label="descricao"
                            track-by="id" />
                          <span v-else class="form-control form-control-disabled">&nbsp;</span>
                        </template>
                        <span v-else class="form-control form-control-disabled">{{ parcela.transacao_cartao.parcelamento_operadora_cartao.descricao }}</span>
                      </b-col>
                    </b-row>
                  </template>

                  <b-row v-else>
                    <b-col>Informações desta forma de cobrança não informadas</b-col>
                  </b-row>
                </template>

                <div v-if="!editando">
                  <b-btn v-b-toggle="`collapse-parcela-${index}`" variant="verde" @click="salvarFormaPagamento(index)">Salvar</b-btn>
                </div>
              </div>
            </div>
          </b-collapse>
        </b-col>
      </b-row>
    </template>

    <div v-if="titulosReceber.length" class="mt-3 d-flex align-items-baseline justify-content-end">
      <div :class="{'bg-danger': valorTotalItens !== valorTotalParcelas, 'bg-light': valorTotalItens === valorTotalParcelas}" class="d-flex p-2 text-right">
        <b-btn v-if="!editando && valorTotalItens !== valorTotalParcelas" variant="link" class="text-white" @click="acertarValorParcelas()">Acertar a diferença na primeira parcela</b-btn>

        <b-btn v-if="editando && btnImprimirBoletos" :href="chamarImpressao()" variant="primary" class="m-auto" target="_blank">Imprimir boletos</b-btn>

        <div class="d-flex flex-column ml-2">
          <div><big class="mb-0">Total dos itens: <b>R${{ valorTotalItens | formatarNumero }}</b></big></div>
          <div><big class="mb-0">Total das parcelas: <b>R${{ valorTotalParcelas | formatarNumero }}</b></big></div>
          <big v-if="!editando && valorTotalItens !== valorTotalParcelas" class="mb-0">Diferença: <b>R${{ valorTotalItens - valorTotalParcelas | formatarNumero }}</b></big>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'
import {gerarParcelasParaCadaParcelamento, gerarTitulo, validarTitulosGerados, transformarParcelasEmTitulos} from './titulo'
import {dateToString, ISOToString} from '../../utils/date'
import open from '../../utils/open'

export default {
  props: {
    editando: {
      type: Boolean,
      default: false,
      required: false
    },

    estaValido: {
      type: Boolean,
      default: true,
      required: false
    },

    btnImprimirBoletos: {
      type: Boolean,
      default: true,
      required: false
    }
  },

  data () {
    return {
      listaBoletos: []
    }
  },

  computed: {
    ...mapState('contrato', ['parametrosParcelamento', 'valorTotalItens', 'valorTotalParcelas', 'titulosReceber']),
    ...mapState('formaPagamento', {listaFormasPagamento: 'lista'}),
    ...mapState('banco', {listaBancos: 'lista'}),
    ...mapState('operadoraCartao', {listaOperadoras: 'lista'})
  },

  mounted () {
    this.SET_VALOR_TOTAL_PARCELAS(0)

    EventBus.$on('calcularTotalParcelas', () => {
      this.calcularTotalParcelas()
    })

    EventBus.$on('validarParcelas', (callback) => {
      const { indexesInvalidas } = validarTitulosGerados(this.titulosReceber, this.parametrosParcelamento)

      if (indexesInvalidas.length === 0) {
        return callback()
      }

      indexesInvalidas.map(index => {
        if (this.$refs[`collapse-btn-parcela-${index}`] && this.$refs[`collapse-btn-parcela-${index}`].click) {
          this.$refs[`collapse-btn-parcela-${index}`].click()
        }
      })
    })

    EventBus.$on('imprimirBoleto', (listaBoletosImpressao, redirect) => {
      this.chamarImpressao(false, redirect, listaBoletosImpressao)
    })
  },

  methods: {
    ...mapMutations('contrato', ['SET_TITULOS_RECEBER', 'SET_TITULO_RECEBER', 'SET_VALOR_TOTAL_PARCELAS', 'REMOVER_TITULO_RECEBER']),
    ISOToString: ISOToString,

    chamarImpressao (boleto, redirect, listaBoletosImpressao) {
      if (boleto && !this.listaBoletos.includes(boleto)) {
        this.listaBoletos.push(boleto)
      }

      if (listaBoletosImpressao !== undefined) {
        this.listaBoletos = listaBoletosImpressao
      }

      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const url = `/api/boleto/imprimir-boletos?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&boletos[]=${boleto || [this.listaBoletos.join('&boletos[]=')].join('')}`

      if (redirect) {
        // open(url, '_blank')
        var host = process.env.VUE_APP_HOST;
        window.open(`${host}${url}`, '_blank')
        return
      }

      return url
    },

    incluirParcela () {
      const index = this.titulosReceber.length
      const novaParcela = {
        numero_parcela_documento: index + 1,
        data_vencimento: dateToString(new Date()),
        valor_original: 0
      }

      this.SET_TITULO_RECEBER({index, value: gerarTitulo(novaParcela)})
    },

    removerParcela (index) {
      this.REMOVER_TITULO_RECEBER(index)
      this.calcularTotalParcelas()
    },

    gerarParcelas () {
      // chama a função do Formulario.vue (Contrato) que prepara os parâmetros
      this.$emit('gerar-parcelas', () => {
        const parcelas = gerarParcelasParaCadaParcelamento(this.parametrosParcelamento)
        if (parcelas !== false) {
          const titulos = transformarParcelasEmTitulos(parcelas)
          validarTitulosGerados(titulos, this.parametrosParcelamento)

          this.SET_TITULOS_RECEBER(titulos)
          this.calcularTotalParcelas()
          if (!this.editando && this.valorTotalItens !== this.valorTotalParcelas) {
            this.acertarValorParcelas()
          }
        }
      })
    },

    calcularTotalParcelas (value, index) {
      const {valorTotal} = validarTitulosGerados(this.titulosReceber, this.parametrosParcelamento)
      this.SET_VALOR_TOTAL_PARCELAS(valorTotal)
    },

    setFormaCobrancaParcela (value, index) {
      const novaParcela = Object.assign({}, this.titulosReceber[index])
      novaParcela.forma_cobranca = value

      this.SET_TITULO_RECEBER({index, value: gerarTitulo(novaParcela)})
    },

    setValorOriginal (value, index) {
      if (parseFloat(this.titulosReceber[index].valor) !== parseFloat(value)) {
        this.titulosReceber[index].valorTituloGeradoAutomatico = false
      }
      this.titulosReceber[index].valor = value
      this.titulosReceber[index].valor_saldo_devedor = value
    },

    setDataVencimentoParcela (value, index) {
      this.titulosReceber[index].data_vencimento = value
    },

    setOperadoraCartaoParcela (value, index) {
      this.titulosReceber[index].transacao_cartao.operadora_cartao = value
    },

    setParcelamentoOperadoraCartaoParcela (value, index) {
      this.titulosReceber[index].transacao_cartao.parcelamento_operadora_cartao = value
    },

    setBancoParcela (value, index) {
      this.titulosReceber[index].cheque.banco = value
    },

    acertarValorParcelas () {
      let $index = 0

      for (let i = 0; i < this.titulosReceber.length; i++) {
        if (this.titulosReceber[i].observacao.indexOf("Parcela") !== -1) {
          $index = i
          break;
        } 
      }

      const diferenca = this.valorTotalItens - this.valorTotalParcelas

      if (diferenca < 0 && this.titulosReceber[$index].valor + diferenca <= 0) {
        EventBus.$emit('chamarModal', {}, 'A diferença é maior que o valor da primeira parcela')
        return
      }

      this.titulosReceber[$index].valor += diferenca
      this.titulosReceber[$index].valor_original = this.titulosReceber[$index].valor
      this.titulosReceber[$index].valor_saldo_devedor = this.titulosReceber[$index].valor
      this.calcularTotalParcelas()
    },

    salvarFormaPagamento (index) {
      let formaPagamento = null
      if (this.titulosReceber[index].forma_cobranca.forma_cheque === true) {
        formaPagamento = 'cheque'
      } else if (this.titulosReceber[index].forma_cobranca.forma_cartao_debito === true) {
        formaPagamento = 'cartao_debito'
      } else if (this.titulosReceber[index].forma_cobranca.forma_cartao === true) {
        formaPagamento = 'cartao'
      } else if (this.titulosReceber[index].forma_cobranca.forma_transferencia === true) {
        formaPagamento = 'transferência bancária'
      }

      let numeroCheque = formaPagamento === 'cheque' ? this.titulosReceber[index].cheque.numero : undefined

      const formasCobrancaIguais = (item) => {
        return item.forma_cobranca.id === this.titulosReceber[index].forma_cobranca.id
      }

      const quantidadeTitulosMesmaFormaCobranca = this.titulosReceber.reduce((acc, curr) =>
        acc + (formasCobrancaIguais(curr) ? 1 : 0), 0)

      if (quantidadeTitulosMesmaFormaCobranca <= 1) {
        return false
      }

      EventBus.$emit('chamarModal', {
        resolve: () => {
          this.titulosReceber.map((titulo, indexTitulo) => {
            if (indexTitulo > index) {
              if (titulo.forma_cobranca.forma_cheque === true && formaPagamento === 'cheque') {
                ++numeroCheque
                titulo.cheque.numero = numeroCheque
                titulo.cheque.titular = this.titulosReceber[index].cheque.titular
                titulo.cheque.banco = this.titulosReceber[index].cheque.banco
                titulo.cheque.agencia = this.titulosReceber[index].cheque.agencia
                titulo.cheque.conta = this.titulosReceber[index].cheque.conta
              } else if (titulo.forma_cobranca.forma_cartao_debito === true && formaPagamento === 'cartao_debito') {
                titulo.transacao_cartao.identificador = this.titulosReceber[index].transacao_cartao.identificador
                titulo.transacao_cartao.numero_lancamento = this.titulosReceber[index].transacao_cartao.numero_lancamento
                titulo.transacao_cartao.operadora_cartao = this.titulosReceber[index].transacao_cartao.operadora_cartao
                titulo.transacao_cartao.parcelamento_operadora_cartao = this.titulosReceber[index].transacao_cartao.parcelamento_operadora_cartao
              } else if (titulo.forma_cobranca.forma_cartao === true && titulo.forma_cobranca.forma_cartao_debito === false && formaPagamento === 'cartao') {
                titulo.transacao_cartao.identificador = this.titulosReceber[index].transacao_cartao.identificador
                titulo.transacao_cartao.numero_lancamento = this.titulosReceber[index].transacao_cartao.numero_lancamento
                titulo.transacao_cartao.operadora_cartao = this.titulosReceber[index].transacao_cartao.operadora_cartao
                titulo.transacao_cartao.parcelamento_operadora_cartao = this.titulosReceber[index].transacao_cartao.parcelamento_operadora_cartao
              } else if (titulo.forma_cobranca.forma_transferencia === true && formaPagamento === 'transferência bancária') {
                titulo.transferencia_bancaria.conta = this.titulosReceber[index].transferencia_bancaria.conta
                titulo.transferencia_bancaria.agencia = this.titulosReceber[index].transferencia_bancaria.agencia
              }
            }
          })
        }
      }, `Deseja aplicar estes dados de ${formaPagamento} para as próximas parcelas com a mesma forma de pagamento?`)
    }

  }
}
</script>
