<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !is_valid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div id="secao-fornecedor" class="body-sector">
        <div class="p-2">
          <b-row>
            <div class="col-md">
              <label v-help-hint="'form-contas-pagar_destino'" for="fornecedor" class="col-form-label">Destino *</label>
            </div>
          </b-row>
          <template v-if="!estaEditando">
            <div>
              <b-form-checkbox v-model="todosFornecedores" class="mb-2">Todos</b-form-checkbox>
              <b-form-checkbox v-for="categoria in opcoesCategoriaPessoa"
                               :disabled="todosFornecedores"
                               :key="categoria.value"
                               v-model="tipoFornecedor"
                               :value="categoria.value"
                               class="mb-2">{{ categoria.text }}</b-form-checkbox>
            </div>

            <b-row>
              <div class="d-flex col-md-12">
                <typeahead v-if="itemSelecionado.fornecedor_pessoa === null"
                           id="buscaDestino"
                           :item-hit="setFornecedor"
                           :additional-data="{name: 'tipo_fornecedor', data: tipoFornecedor}"
                           :required="true"
                           class="w-100"
                           source-path="/api/pessoa/buscar_nome_contato"
                           key-name="nome_contato"
                />
                <template v-else>
                  <span class="form-control form-control-disabled">{{ itemSelecionado.fornecedor_pessoa.nome_contato }}</span>
                  <div>
                    <b-btn variant="link" @click="itemSelecionado.fornecedor_pessoa = null">Limpar</b-btn>
                  </div>
                </template>

                <b-btn variant="roxo" @click="$emit('mostrarFormularioPessoa')"><font-awesome-icon icon="plus" /></b-btn>
              </div>
            </b-row>
            <div v-if="!is_valid && !itemSelecionado.fornecedor_pessoa" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </template>
          <span v-else class="form-control form-control-disabled">{{ itemSelecionado.fornecedor_pessoa.razao_social || itemSelecionado.fornecedor_pessoa.nome_contato }}</span>
        </div>

        <div id="secao-cobranca" class="p-2 content-sector sector-roxo-c">
          <h5 class="title-module mb-2">Cobrança</h5>

          <b-row>
            <b-col lg="4">
              <label v-help-hint="'form-conta-pagar_forma_cobranca'" for="forma_cobranca" class="col-form-label">Forma de cobrança *</label>
              <template v-if="!estaEditando || !itemSelecionado.possui_titulos_pagos">
                <g-select
                  v-model="itemSelecionado.forma_cobranca"
                  :invalid="!is_valid && !itemSelecionado.forma_cobranca"
                  :options="listaFormaPagamento"
                  label="descricao"
                  track-by="id" />
                <div v-if="!is_valid && !itemSelecionado.forma_cobranca" class="multiselect-invalid">
                  Selecione uma opção!
                </div>
              </template>
              <span v-else-if="itemSelecionado.forma_cobranca" class="form-control form-control-disabled">{{ itemSelecionado.forma_cobranca.descricao }}</span>
              <span v-else class="form-control form-control-disabled">Nenhuma</span>
            </b-col>

            <b-col md="6" lg="4">
              <label v-help-hint="'form-conta-pagar_conta'" for="conta" class="col-form-label">Conta Débito *</label>
              <template v-if="!estaEditando || !itemSelecionado.possui_titulos_pagos">
                <g-select
                  v-model="itemSelecionado.conta"
                  :invalid="!is_valid && !itemSelecionado.conta"
                  :options="listaContas"
                  label="descricao"
                  track-by="id" />
                <div v-if="!is_valid && !itemSelecionado.conta" class="multiselect-invalid">
                  Selecione uma opção!
                </div>
              </template>
              <span v-else-if="itemSelecionado.conta" class="form-control form-control-disabled">{{ itemSelecionado.conta.descricao }}</span>
              <span v-else class="form-control form-control-disabled">Nenhuma</span>
            </b-col>

            <b-col md="6" lg="4">
              <label v-help-hint="'form-conta-pagar_data_vencimento'" for="data_vencimento" class="col-form-label">Data de vencimento *</label>
              <template v-if="!estaEditando || !itemSelecionado.possui_titulos_pagos">
                <g-datepicker :value="itemSelecionado.data_vencimento" :selected="selectVencimento" maxlength="10" />
                <div v-if="$v.itemSelecionado.data_vencimento.$invalid" class="multiselect-invalid">
                  Campo obrigatório!
                </div>
              </template>
              <span v-else-if="itemSelecionado.data_vencimento" class="form-control form-control-disabled">{{ itemSelecionado.data_vencimento }}</span>
              <span v-else class="form-control form-control-disabled">Nenhuma</span>
            </b-col>
          </b-row>

          <b-row class="pt-2">
            <b-col md="6" lg="4">
              <label v-help-hint="'form-conta-pagar_valor_parcela'" for="valor_parcela" class="col-form-label">Valor da Parcela *</label>
              <template v-if="!estaEditando || !itemSelecionado.possui_titulos_pagos">
                <vue-numeric id="valor_parcela" :precision="2" :empty-value="null" v-model="itemSelecionado.valor_parcela" :max="9999999.99" :class="!is_valid && !itemSelecionado.valor_parcela ? 'invalid-input' : 'valid-input'" separator="." class="form-control" required @input="calcularParcelas('valor_parcela')" />
              </template>
              <span v-else class="form-control form-control-disabled">{{ itemSelecionado.valor_parcela | formatarNumero }}</span>
              <div v-if="!is_valid && !itemSelecionado.valor_parcela" class="multiselect-invalid">
                Informe um valor!
              </div>
            </b-col>

            <b-col md="6" lg="4">
              <label v-help-hint="'form-conta-pagar_numero_parcelas'" for="numero_parcelas" class="col-form-label">Número de Parcelas *</label>
              <template v-if="!estaEditando || !itemSelecionado.possui_titulos_pagos">
                <vue-numeric id="numero_parcelas" :precision="0" :empty-value="null" v-model="itemSelecionado.numero_parcelas" :min="1" separator="." class="form-control" required @input="calcularParcelas('numero_parcelas')" />
              </template>
              <span v-else class="form-control form-control-disabled">{{ itemSelecionado.numero_parcelas }}</span>
            </b-col>

            <b-col lg="4">
              <label v-help-hint="'form-conta-pagar_valor_total'" for="valor_total" class="col-form-label"><b>Valor Total *</b></label>
              <template v-if="!estaEditando || !itemSelecionado.possui_titulos_pagos">
                <vue-numeric id="valor_total" :precision="2" :empty-value="null" v-model="itemSelecionado.valor_total" :max="9999999.99" separator="." class="form-control" required @input="calcularParcelas('valor_total')" />
              </template>
              <span v-else class="form-control form-control-disabled">{{ itemSelecionado.valor_total | formatarNumero }}</span>
            </b-col>
          </b-row>
        </div>

        <div id="secao-plano-contas" class="p-2 content-sector sector-azul">
          <h5 class="title-module d-flex justify-content-between">
            Plano de Contas
            <b-btn v-if="!estaEditando" variant="azul" title="Adicionar plano de conta" @click="adicionarPlanoConta()">
              <font-awesome-icon icon="plus" />
            </b-btn>
          </h5>

          <div v-if="!is_valid && erroPlanoContas" class="bg-danger p-2 mb-1 error-special-margin">{{ erroPlanoContas }}</div>

          <b-row v-for="(item, index) in itemSelecionado.plano_conta" :key="index" class="mt-2">
            <b-col md="6" lg="6">
              <label v-help-hint="'form-conta-pagar_categoria'" :for="`plano_conta[${index}][categoria]`" class="col-form-label">Categoria *</label>
              <template v-if="!estaEditando || !itemSelecionado.possui_titulos_pagos">
                <g-treeselect
                  :id="`plano_conta[${index}][categoria]`"

                  :value="item.plano_conta"
                  :input="setPlanoContaCategoria"
                  :extra-param="index"

                  :options="planoConta"
                  :invalid="!is_valid && (!item.plano_conta || !!(item.plano_conta.filhos && item.plano_conta.filhos.length))"
                  required
                />

                <span v-if="!is_valid && (!item.plano_conta || (item.plano_conta.filhos && item.plano_conta.filhos.length))" class="multiselect-invalid">
                  <template v-if="!item.plano_conta">Campo obrigatório</template>
                  <template v-else>Selecione uma categoria de último nível</template>
              </span> </template>
              <span v-else-if="item && item.plano_conta" class="form-control form-control-disabled text-truncate">{{ item.plano_conta.descricao }}</span>
              <span v-else class="form-control form-control-disabled">Nenhuma</span>
            </b-col>

            <b-col md="6" lg="2">
              <label v-help-hint="'form-conta-pagar_valor'" :for="`plano_conta[${index}][valor]`" class="col-form-label">Valor *</label>
              <vue-numeric :id="`plano_conta[${index}][valor]`" :precision="2" :empty-value="null" v-model="item.valor" :max="9999999.99" :disabled="estaEditando || itemSelecionado.possui_titulos_pagos" separator="." class="form-control" required style="padding-right: 6px; padding-left: 6px;" @blur="setPlanoContaValor($event, index)" />
            </b-col>

            <b-col md="10" lg="3">
              <label v-help-hint="'form-conta-pagar_complemento'" :for="`plano_conta[${index}][complemento]`" class="col-form-label">Complemento</label>
              <input :id="`plano_conta[${index}][complemento]`" :value="item.complemento" :disabled="estaEditando || itemSelecionado.possui_titulos_pagos" type="text" class="form-control" maxlength="255" @input="setPlanoContaComplemento($event, index)">
            </b-col>

            <b-col v-if="!itemSelecionado.possui_titulos_pagos" md="2" lg="1">
              <label v-help-hint="'form-conta-pagar_remove_plano'" class="col-form-label d-block">&nbsp;</label>
              <b-btn :disabled="itemSelecionado.plano_conta.length === 1" variant="clear" title="Adicionar plano de conta" style="min-width: 100%; width: 100%; padding-right: 0; padding-left: 0;" @click="removerPlanoConta(index)">
                <font-awesome-icon icon="minus" />
              </b-btn>
            </b-col>
          </b-row>
        </div>

        <div id="secao-parcelas" class="p-2 head-content-sector">
          <div>
            <b-btn v-if="!itemSelecionado.possui_titulos_pagos" :disabled="itemSelecionado.forma_cobranca === null || itemSelecionado.valor_parcela === 0 " variant="roxo" title="Atualizar Parcelas" class="mr-1" @click="atualizarParcelas()">
              {{ Object.keys(itemSelecionado.parcelas).length > 0 ? 'Atualizar Parcelas' : 'Gerar Parcelas' }}
            </b-btn>

            <b-btn :disabled="Object.keys(itemSelecionado.parcelas).length === 0" variant="azul" title="Incluir Parcela" class="mr-1" @click="incluirParcela()">
              Incluir Parcela
            </b-btn>

            <b-form-checkbox v-if="!estaEditando" v-model="itemSelecionado.quitar_primeira_parcela" :value="true">
              Quitar primeira parcela
            </b-form-checkbox>
          </div>

          <span v-if="!is_valid && erroParcelasCobranca" class="d-block bg-danger p-1 mt-2">{{ erroParcelasCobranca }}</span>
        </div>

        <div v-for="(parcela, index) in itemSelecionado.parcelas" :key="index" :class="{'highlight': parcela.id === highlightId}" class="p-2 list-separator">
          <b-row class="align-items-center">
            <b-col lg="1" class="text-center">
              <label class="col-form-label">#</label>
              <span class="d-block text-muted"><b>{{ parcela.numero_parcela_documento }}</b></span>
            </b-col>

            <b-col lg="3">
              <label v-help-hint="'form-conta-pagar_narrativa'" :for="`parcela[${index}][narrativa_plano_conta]`" class="col-form-label">Narrativa do Plano *</label>
              <template v-if="!estaEditando || !parcela.movimento_conta || !parcela.movimento_conta.length">
                <input :id="`parcela[${index}][narrativa_plano_conta]`" v-model="parcela.narrativa_plano_conta" class="form-control" type="text" required maxlength="255">
              </template>
              <span v-b-tooltip.viewport.left.hover v-else :title="parcela.narrativa_plano_conta" class="form-control form-control-disabled truncate">{{ parcela.narrativa_plano_conta }}</span>
              <div class="invalid-feedback">Preencha a narrativa!</div>
            </b-col>

            <b-col md="6" lg="3">
              <label v-help-hint="'form-conta-pagar_forma_cobranca'" :for="`parcela[${index}][forma_cobranca]`" class="col-form-label">Forma de Cobrança *</label>
              <template v-if="!estaEditando || !parcela.movimento_conta || !parcela.movimento_conta.length">
                <g-select
                  v-model="parcela.forma_cobranca"
                  :extra-param="index"
                  :options="listaFormaPagamento"
                  label="descricao"
                  track-by="id" />
              </template>
              <span v-else-if="parcela" class="form-control form-control-disabled text-truncate">{{ parcela.forma_cobranca.descricao }}</span>
              <span v-else class="form-control form-control-disabled">Nenhuma</span>
            </b-col>

            <b-col md="6" lg="2">
              <label v-help-hint="'form-conta-pagar_vencimento'" for="parcela.data_vencimento" class="col-form-label">Vencimento *</label>
              <template v-if="!estaEditando || !parcela.movimento_conta || !parcela.movimento_conta.length">
                <g-datepicker :value="parcela.data_vencimento" :selected="setParcelaDataVencimento" :extra-param="index" maxlength="10" />
              </template>
              <span v-else class="form-control form-control-disabled">{{ parcela.data_vencimento }}</span>
            </b-col>

            <b-col lg="2">
              <label v-help-hint="'form-conta-pagar_valor_original'" :for="`parcela[${index}][valor]`" class="col-form-label">Valor Original *</label>
              <template v-if="!estaEditando || !parcela.movimento_conta || !parcela.movimento_conta.length">
                <vue-numeric :id="`parcela[${index}][valor]`" :precision="2" :empty-value="null" :class="{'is-invalid': !parcela.valor_documento}" :value="parcela.valor_documento" :max="9999999.99" separator="." class="form-control" required @input="setParcelaValor($event, index)" />
                <div v-if="!parcela.valor_documento" class="input-invalid">Obrigatório</div>
              </template>
              <span v-else class="form-control form-control-disabled">{{ parcela.valor_documento | formatarNumero }}</span>
            </b-col>

            <b-col v-if="estaEditando" lg="1" class="d-flex flex-column">
              <span class="d-block col-form-label">&nbsp;</span>
              <span class="d-flex align-items-center m-auto ">
                <span v-b-tooltip.viewport.down.hover v-if="parcela.situacao" :class="`circle-badge-${parcela.situacao.toLowerCase()}`" :title="situacoes[parcela.situacao]" class="circle-badge"></span>
              </span>
            </b-col>
          </b-row>

          <b-row v-if="parcela.forma_cobranca.forma_cheque === true && parcela.cheque">
            <b-col md="5">
              <b-row>
                <b-col md="4">
                  <label v-help-hint="'form-contas-pagar_numero_cheque'" :for="`cheque_numero_${index}`" class="col-form-label">Nº Cheque *</label>
                  <input v-number v-model="parcela.cheque.numero" :id="`cheque_numero_${index}`" :disabled="parcela.cheque.id" class="form-control" type="text" maxlength="9" required>
                </b-col>

                <b-col md="8">
                  <label v-help-hint="'form-contas-pagar_titula_cheque'" :for="`cheque_titular_${index}`" class="col-form-label">Titular *</label>
                  <input v-model="parcela.cheque.titular" :id="`cheque_titular_${index}`" :disabled="parcela.cheque.id" class="form-control" type="text" maxlength="50" required>
                </b-col>
              </b-row>
            </b-col>

            <b-col lg="7">
              <b-row>
                <b-col md="3">
                  <label v-help-hint="'form-contas-pagar_banco_cheque'" :for="`cheque_banco_${index}`" class="col-form-label">Banco *</label>
                  <input v-model="parcela.cheque.banco" :id="`cheque_banco_${index}`" :disabled="parcela.cheque.id" class="form-control" type="text" maxlength="50" required>
                </b-col>

                <b-col md="3">
                  <label v-help-hint="'form-contas-pagar_agencia_cheque'" :for="`cheque_agencia_${index}`" class="col-form-label">Agência *</label>
                  <input v-model="parcela.cheque.agencia" :id="`cheque_agencia_${index}`" :disabled="parcela.cheque.id" class="form-control" type="text" maxlength="10" required>
                </b-col>

                <b-col md="3">
                  <label v-help-hint="'form-contas-pagar_conta_cheque'" :for="`cheque_conta_${index}`" class="col-form-label">Conta *</label>
                  <input v-model="parcela.cheque.conta" :id="`cheque_conta_${index}`" :disabled="parcela.cheque.id" class="form-control" type="text" maxlength="20" required>
                </b-col>

                <b-col v-if="!estaEditando" md="3">
                  <label class="d-block col-form-label">&nbsp;</label>
                  <b-button v-b-tooltip.hover.left block title="Aplicar estes dados nos próximos cheques" variant="verde" @click="aplicarDadosCheque(index)">Aplicar</b-button>
                </b-col>
              </b-row>
            </b-col>
          </b-row>

          <b-row v-for="(movimento, movIndex) in parcela.movimento_conta" :key="`${parcela.id}!!${movimento.id}`" :class="{'mt-2': movIndex === 0}" class="p-2 linha-movimento-conta detalhes-highlight">
            <b-col sm="1" md="1"/>

            <b-col lg="3">
              <label v-help-hint="'form-conta-pagar_forma_pagamento'" class="col-form-label">Forma de Pagamento</label>
              <span class="d-block">{{ movimento.forma_pagamento.descricao }}</span>
            </b-col>

            <b-col lg="3">
              <label v-help-hint="'form-conta-pagar_plano_conta'" class="col-form-label">Categoria/Observação</label>
              <span class="d-block">{{ movimento.observacao }}</span>
            </b-col>

            <b-col lg="2">
              <label v-help-hint="'form-conta-pagar_data_pagamento'" class="col-form-label">Pago em</label>
              <span class="d-block badge date-payment align-middle rounded">{{ movimento.data_movimento | formatarData }}</span>
            </b-col>

            <b-col lg="2">
              <label v-help-hint="'form-conta-pagar_valor_pagamento'" class="col-form-label">Valor</label>
              <span class="d-block">{{ movimento.valor_lancamento | formatarNumero }} {{ movimento.movimento_estorno ? '(-)' : '' }}</span>
            </b-col>
          </b-row>
        </div>

        <div :class="{'bg-danger': valorTotalTitulosCalculados !== itemSelecionado.valor_total}" class="p-2">
          <b-row class="align-items-center">
            <b-col md="8">
              <template v-if="valorTotalTitulosCalculados !== itemSelecionado.valor_total && Object.keys(itemSelecionado.parcelas).length > 0">
                Há diferença entre o valor total da conta e a soma das parcelas. Deseja assumir o total das parcelas como valor total da conta a pagar?
                <a href="javascript:void(0)" class="btn-confirm-parc" @click="atualizarValorTotal()">Sim, atualizar valor total da nota</a>
              </template>
            </b-col>

            <b-col md="4">
              <label v-help-hint="'form-conta-pagar_total_parcela'" class="col-form-label d-block">Total das parcelas</label>
              <big>{{ valorTotalTitulosCalculados | formatarMoeda }}</big>
            </b-col>
          </b-row>
        </div>

        <div class="p-2">
          <div class="row">
            <div class="col-md-12">
              <label v-help-hint="'form-conta-pagar_observacao'" for="observacao" class="col-form-label">Observação</label>
              <textarea id="observacao" v-model="itemSelecionado.observacao" class="form-control"></textarea>
            </div>
          </div>
        </div>
      </div>

      <div>
        <b-btn :disabled="salvando || Object.keys(itemSelecionado.parcelas).length === 0" type="submit" variant="verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</b-btn>
        <b-btn type="button" variant="link" @click="finalizar()">Cancelar</b-btn>
      </div>
    </form>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {dateToString, stringToISODate} from '../../utils/date'
import {currencyToNumber, round} from '../../utils/number'
import {formatarCPF, formatarCNPJ} from '../../utils/format'
import {required} from 'vuelidate/lib/validators'
import {defaultData} from '../titulo-pagar/titulo'

export default {
  props: {
    id: {
      type: Number,
      required: false,
      default: null
    },

    isModal: {
      type: Boolean,
      required: false,
      default: false
    },

    highlightId: {
      type: Number,
      required: false,
      default: null
    }
  },

  data () {
    return {
      is_valid: true,
      isEdit: false,
      mostrar_mais: false,
      busca_item: null,
      estaCalculandoTitulos: false,
      salvando: false,
      tipoFornecedor: [],
      todosFornecedores: false,
      erroPlanoContas: null,
      erroParcelasCobranca: null,
      valorTotalTitulosCalculados: 0,
      dataUltimaParcela: null,
      narrativa_plano_conta: '',
      listaNarrativa: [],
      ultimoNivel: [],
      situacoes: {
        'PEN': 'Pendente',
        'LIQ': 'Liquidado',
        'LIQ-PEN': 'Liquidação Pendente',
        'CAN': 'Cancelado',
        'BAI': 'Baixado',
        'SUB': 'Substituído',
        'VEN': 'Vencido'
      },
      opcoesCategoriaPessoa: [
        {text: 'Aluno', value: 'aluno'},
        {text: 'Fornecedor', value: 'fornecedor'},
        {text: 'Funcionário', value: 'funcionario'},
        {text: 'Instrutor', value: 'instrutor'}
      ]
    }
  },

  computed: {
    ...mapState('contaPagar', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('conta', {listaContas: 'lista'}),
    ...mapState('item', {listaItens: 'lista'}),
    ...mapState('planoConta', {planoConta: 'selectDespesas'}),
    ...mapState('formaPagamento', {listaFormaPagamento: 'lista'}),

    estaEditando: {
      get () {
        return !!this.itemSelecionado.id
      }
    },

    listaPlanoConta: {
      get () {
        return this.childrenStructure(this.planoConta)
      }
    }
  },

  watch: {
    id () {
      this.init()
    },

    tipoFornecedor (value, oldValue) {
      if (oldValue.length !== value.length && value.length === this.opcoesCategoriaPessoa.length) {
        this.todosFornecedores = true
      }
    },

    todosFornecedores (value) {
      this.marcarTodasCategoriasPessoas()
    },

    'itemSelecionado.valor_total' () {
      this.itemSelecionado.plano_conta.map((plano, index) => {
        const value = this.itemSelecionado.valor_total / this.itemSelecionado.plano_conta.length
        const event = { target: { value } }
        this.setPlanoContaValor(event, index)
        this.calcularTotalTitulos()
      })
    }
  },

  mounted () {
    this.$store.commit('item/SET_PAGINA_ATUAL', 1)
    this.$store.commit('formaPagamento/SET_PAGINA_ATUAL', 1)

    this.getListaItens()
    this.getListaFormaPagamento()

    this.todosFornecedores = true

    this.init()
  },

  validations: {
    itemSelecionado: {
      forma_cobranca: {required},
      data_vencimento: {required},
      conta: {required},
      valor_parcela: {required},
      valor_total: {required},
      numero_parcelas: {required},
      fornecedor_pessoa: {required}
    },
    narrativa_plano_conta: {required}
  },

  methods: {
    ...mapMutations('contaPagar', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ITENS', 'SET_ESTA_CARREGANDO']),
    ...mapActions('contaPagar', ['criar', 'buscar', 'atualizar']),
    ...mapActions('tituloPagar', {calcularTitulos: 'calcular'}),
    ...mapActions('conta', {getListaConta: 'getLista'}),
    ...mapActions('item', {getListaItens: 'getLista'}),
    ...mapActions('formaPagamento', {getListaFormaPagamento: 'getLista'}),

    init () {
      this.LIMPAR_ITEM_SELECIONADO()

      this.isEdit = false
      const id = this.id

      if (id) {
        this.isEdit = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscar()
          .then(item => {
            this.listaNarrativa = this.itemSelecionado.plano_conta.map(plano => {
              return plano.plano_conta.descricao
            })

            this.alterarNarrativa()
          })
      } else {
        const date = dateToString(new Date())
        this.$store.commit('contaPagar/SET_DATA_VENCIMENTO', date)
        this.$store.commit('tituloPagar/SET_DATA_VENCIMENTO', date)
      }
    },

    marcarTodasCategoriasPessoas () {
      this.tipoFornecedor = this.opcoesCategoriaPessoa.map(({value}) => value)
    },

    calcularParcelas (campo) {
      if (campo === 'valor_parcela' || campo === 'numero_parcelas') {
        if (!this.itemSelecionado.numero_parcelas) {
          this.itemSelecionado.numero_parcelas = 1
        }

        if (!this.itemSelecionado.valor_parcela) {
          this.itemSelecionado.valor_parcela = 0
        }

        this.itemSelecionado.valor_total = this.itemSelecionado.valor_parcela * this.itemSelecionado.numero_parcelas
      } else if (campo === 'valor_total') {
        if (!this.itemSelecionado.numero_parcelas) {
          this.itemSelecionado.numero_parcelas = 1
        }

        this.itemSelecionado.valor_parcela = this.itemSelecionado.valor_total / this.itemSelecionado.numero_parcelas
      }
    },

    selectVencimento (data) {
      this.$store.commit('contaPagar/SET_DATA_VENCIMENTO', data)
      this.$store.commit('tituloPagar/SET_DATA_VENCIMENTO', data)
    },

    setFornecedor (value) {
      this.$store.commit('contaPagar/SET_FORNECEDOR_PESSOA', value)
      if (!this.estaEditando) {
        if (value !== null && value.plano_conta !== undefined) {
          this.setPlanoContaCategoria(value.plano_conta, 0)
        }
      }
    },

    setPlanoContaCategoria (value, index) {
      if (!value) {
        return
      }

      const selecionado = Object.assign({}, value)
      this.$store.commit('contaPagar/SET_PLANO_CONTA_CATEGORIA', {index, value: selecionado})
      this.calcularTotalPlanosConta()

      if (value.filhos.length) {
        this.ultimoNivel[index] = 1
      } else if (this.ultimoNivel[index]) {
        this.ultimoNivel.splice(index, 1)
      }

      const descricao = value.descricao.replace(/(_ )*/, '')
      this.listaNarrativa[index] = descricao
      this.alterarNarrativa()
    },

    childrenStructure (filhos) {
      let list = []
      filhos.map(filho => {
        let child = {
          ...filho,
          label: filho.descricao
        }

        if (filho.filhos.length) { child.children = this.childrenStructure(filho.filhos) }

        list.push(child)
      })
      return list
    },

    setPlanoContaComplemento ({target}, index) {
      this.$store.commit('contaPagar/SET_PLANO_CONTA_COMPLEMENTO', {index, value: target.value})
    },

    setPlanoContaValor ({target}, index) {
      this.$store.commit('contaPagar/SET_PLANO_CONTA_VALOR', {index, value: currencyToNumber(target.value)})
      this.calcularTotalPlanosConta()
    },

    adicionarPlanoConta () {
      this.$store.commit('contaPagar/SET_ADICIONAR_PLANO_CONTA')
      this.calcularTotalPlanosConta()
    },

    removerPlanoConta (index) {
      this.listaNarrativa.splice(index, 1)

      this.alterarNarrativa()

      if (this.ultimoNivel[index]) {
        this.ultimoNivel.splice(index, 1)
      }

      this.$store.commit('contaPagar/SET_REMOVER_PLANO_CONTA', index)

      this.calcularTotalPlanosConta()
    },

    alterarNarrativa () {
      this.narrativa_plano_conta = this.listaNarrativa.join(', ')
    },

    calcularTotalPlanosConta () {
      let valorPlanoContas = 0
      let categoriaVazia = false
      this.itemSelecionado.plano_conta.map(item => {
        valorPlanoContas += item.valor || 0
        if (item.plano_conta === null) {
          // console.log('item.plano_conta', item.plano_conta)
          categoriaVazia = true
        }
      })

      this.erroPlanoContas = null

      if (valorPlanoContas !== this.itemSelecionado.valor_total) {
        this.erroPlanoContas = 'Confira os valores dos planos de conta, a soma dos valores deve ser igual ao valor da conta'
      } else if (categoriaVazia) {
        this.erroPlanoContas = 'Selecione as categorias para os planos de conta'
      }
    },

    calcularTotalTitulos () {
      let valor = 0
      const parcelas = this.itemSelecionado.parcelas
      for (var i in parcelas) {
        valor += parcelas[i].valor_documento
      }

      this.valorTotalTitulosCalculados = round(valor)
    },

    atualizarValorTotal () {
      this.itemSelecionado.valor_total = this.valorTotalTitulosCalculados
      this.itemSelecionado.valor_parcela = round(this.itemSelecionado.valor_total / this.itemSelecionado.numero_parcelas)
    },

    atualizarParcelas () {
      const contaPagar = Object.assign({}, this.itemSelecionado)

      if (!contaPagar.data_vencimento || !contaPagar.valor_total) {
        this.$scrollTo('#secao-cobranca')
        return false
      }

      const numeroParcelas = contaPagar.numero_parcelas
      const parcelas = {}
      const date = new Date(stringToISODate(contaPagar.data_vencimento, true))

      for (let index = 1; index <= numeroParcelas; index++) {
        if (index > 1) {
          date.setMonth(date.getMonth() + 1)
        }

        this.dataUltimaParcela = date

        const parcela = {
          numero_parcela_documento: index,
          data_vencimento: dateToString(date),
          data_pagamento: undefined,
          valor_documento: contaPagar.valor_parcela,
          forma_cobranca: contaPagar.forma_cobranca,
          verDetalhes: false,
          narrativa_plano_conta: this.narrativa_plano_conta
        }

        if (parcela.forma_cobranca.forma_cheque === true) {
          parcela.cheque = Object.assign({}, defaultData.cheque, {valor: parcela.valor_documento, data_bom_para: parcela.data_vencimento})
        }

        parcelas[index] = parcela
      }

      this.itemSelecionado.parcelas = parcelas
      this.calcularTotalTitulos()
    },

    incluirParcela () {
      if (!this.itemSelecionado.data_vencimento || !this.itemSelecionado.valor_total) {
        this.$scrollTo('#secao-cobranca')
        return false
      }

      const parcelas = Object.assign({}, this.itemSelecionado.parcelas)
      const numeroParcelas = Object.keys(parcelas).length

      let date = this.dataUltimaParcela
      if (!date) {
        date = new Date(stringToISODate(parcelas[numeroParcelas].data_vencimento))
      }

      date.setMonth(date.getMonth() + 1)

      let valor = 0
      if (this.valorTotalTitulosCalculados < this.itemSelecionado.valor_total) {
        valor = this.itemSelecionado.valor_total - this.valorTotalTitulosCalculados
      }

      const novaParcela = {
        numero_parcela_documento: numeroParcelas + 1,
        data_vencimento: dateToString(date),
        data_pagamento: undefined,
        valor_documento: valor,
        forma_cobranca: this.itemSelecionado.forma_cobranca,
        narrativa_plano_conta: this.narrativa_plano_conta
      }

      if (novaParcela.forma_cobranca.forma_cheque === true) {
        novaParcela.cheque = Object.assign({}, defaultData.cheque, {valor: novaParcela.valor_documento, data_bom_para: novaParcela.data_vencimento})
      }

      this.itemSelecionado.numero_parcelas += 1
      parcelas[novaParcela.numero_parcela_documento] = novaParcela

      this.itemSelecionado.parcelas = parcelas
      this.calcularTotalTitulos()
    },

    setParcelaDataVencimento (value, index) {
      this.$store.commit('contaPagar/SET_PARCELA_DATA_VENCIMENTO', {index, value})
    },

    setParcelaFormaCobranca (value, index) {
      this.$store.commit('contaPagar/SET_PARCELA_FORMA_COBRANCA', {index, value: Object.assign({}, value)})
    },

    setParcelaValor (value, index) {
      this.$store.commit('contaPagar/SET_PARCELA_VALOR', {index, value})
      this.calcularTotalTitulos()
    },

    camposValidos () {
      if (!this.itemSelecionado.fornecedor_pessoa) {
        this.$scrollTo('#secao-fornecedor')
        return false
      }

      if (!this.itemSelecionado.forma_cobranca) {
        this.$scrollTo('#secao-cobranca')
        return false
      }

      if (this.erroPlanoContas || this.ultimoNivel.length) {
        this.$scrollTo('#secao-plano-contas')
        return false
      }

      const parcelas = this.itemSelecionado.parcelas
      for (let i in parcelas) {
        if (!parcelas[i].forma_cobranca) {
          this.erroParcelasCobranca = 'Selecione as formas de cobrança para as parcelas.'
          this.$scrollTo('#secao-parcelas')
          return false
        }

        if (!parcelas[i].valor_documento) {
          this.erroParcelasCobranca = 'Os valores de parcela devem ser maiores que R$ 0.'
          this.$scrollTo('#secao-parcelas')
          return false
        }

        if (parcelas[i].forma_cobranca.forma_cheque === true) {
          if (!parcelas[i].cheque || !parcelas[i].cheque.numero || !parcelas[i].cheque.titular || !parcelas[i].cheque.banco || !parcelas[i].cheque.agencia || !parcelas[i].cheque.conta) {
            this.erroParcelasCobranca = 'Preencha as informações dos cheques.'
            this.$scrollTo('#secao-parcelas')
            return false
          }
        }
      }

      this.erroParcelasCobranca = null

      if (this.valorTotalTitulosCalculados !== this.itemSelecionado.valor_total) {
        return false
      }

      return true
    },

    selectOptionText (item) {
      const codigo = item.tipo_pessoa === 'F' ? formatarCPF(item.cnpj_cpf) : formatarCNPJ(item.cnpj_cpf)
      return `${item.nome_fantasia || item.razao_social || item.nome_contato} (${codigo})`
    },

    voltar () {
      this.$router.push('/financeiro/conta-pagar')
    },

    retornaDestinoFornecedorPessoa () {
      if (this.itemSelecionado.fornecedor_pessoa !== null) {
        return Object.assign({}, this.itemSelecionado.fornecedor_pessoa)
      }
      return null
    },

    finalizar (action = 'cancel') {
      this.is_valid = true
      this.LIMPAR_ITEM_SELECIONADO()

      this.selectVencimento(dateToString(new Date()))

      if (this.isModal) {
        this.$emit(action)
      } else {
        this.voltar()
      }
    },

    salvar () {
      if (this.$v.$invalid || !this.camposValidos()) {
        this.is_valid = false
        return
      }

      this.is_valid = true
      this.salvando = true

      if (this.itemSelecionadoID) {
        this.atualizar()
          .then(() => {
            this.salvando = false
            this.finalizar('resolve')
          })
          .catch(err => {
            this.salvando = false
            console.error(err)
          })
      } else {
        this.criar()
          .then(() => {
            this.salvando = false
            this.finalizar('resolve')
          })
          .catch(err => {
            this.salvando = false
            console.error(err)
          })
      }
    },

    aplicarDadosCheque (index) {
      const parcelas = this.itemSelecionado.parcelas
      let numeroCheque = 0
      for (let i in parcelas) {
        const titulo = parcelas[i]
        const indexTitulo = i

        if (indexTitulo === index) {
          numeroCheque = titulo.cheque.numero
        }

        if (indexTitulo > index) {
          if (titulo.forma_cobranca.forma_cheque === true) {
            ++numeroCheque
            titulo.cheque.numero = numeroCheque
            titulo.cheque.titular = parcelas[index].cheque.titular
            titulo.cheque.banco = parcelas[index].cheque.banco
            titulo.cheque.agencia = parcelas[index].cheque.agencia
            titulo.cheque.conta = parcelas[index].cheque.conta
          }
        }
      }
    }
  }
}
</script>

<style scoped>
.btn-confirm-parc {
  color: #FF3860 !important;
  background-color: #ffffff;
  cursor: pointer;
  margin-top: 0.5rem;
  display: block;
  text-align: center;
  white-space: nowrap;
  padding: 0.375rem 0.75rem;
}

.bg-danger label.col-form-label {
  color: #151b1e;
}

.error-special-margin {
  margin-left: -16px;
  margin-right: -16px;
}

/* .linha-movimento-conta {
  background-color: #fafafa;
} */

.linha-movimento-conta:first-child {
  margin-top: 10px;
}

.linha-movimento-conta + .linha-movimento-conta {
  border-top: 1px dashed #eee;
}

.highlight {
  background-color: rgba(235, 236, 240, 0.7);
}
.detalhes-highlight {
  background-color: #FAFAFA;
  border-radius: 4px;
}
</style>

<style>
.multiselect__option {
  font-size: 10px !important;
  padding: 5px !important;
  min-height: 26px !important;
}
</style>
