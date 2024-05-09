<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !wasValidated }" class="needs-validation" novalidate @submit.prevent="submit($event)">
      <b-row v-if="sacadoPessoa" class="form-group">
        <b-col md="4">
          <label class="col-form-label">Sacado</label>
          <input :value="sacadoPessoa.nome_contato" :disabled="true" type="text" class="form-control">
        </b-col>

        <b-col md="4">
          <label for="vendedor_funcionario" class="col-form-label">Vendedor *</label>
          <sync-select v-model="vendedorFuncionario" :was-validated="wasValidated" :field="{ targetEntity: 'App\\Entity\\Principal\\Funcionario', name: 'vendedor_funcionario', descriptionColumn: 'apelido', valueColumn: 'id', where: [{field: 'situacao', criteria: '=', value: 'A'}] }" required />
        </b-col>
      </b-row>

      <g-table style="height: auto;">
        <thead class="text-dark">
          <tr>
            <th title="Contrato" class="d-block text-truncate">Contrato</th>
            <th title="Nº Parcelas" class="d-block text-truncate">Nº Parcela</th>
            <th title="Vencimento" class="d-block text-truncate">Vencimento</th>
            <th title="Valor Devedor" class="d-block text-truncate">Valor Devedor</th>
            <th title="Juros e Multas" class="d-block text-truncate">Juros e Multas</th>
            <th title="Forma de Cob." class="d-block text-truncate">Forma de Cob.</th>
            <th title="Descrição" class="d-block text-truncate">Descrição</th>
            <th title="Situação" class="d-block text-truncate coluna-situacao">Situação</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="item in titulosRenegociar" :key="item.id">
            <td class="d-block text-truncate">
              <template v-if="item.conta_receber.contrato">{{ `${item.aluno.id}/${item.conta_receber.contrato.sequencia_contrato}` }}</template>
            </td>
            <td class="d-block text-truncate">{{ item.numero_parcela_documento }}</td>
            <td class="d-block text-truncate">{{ item.data_prorrogacao | formatarData }}</td>
            <td class="d-block text-truncate">{{ item.valor_saldo_devedor | formatarMoeda }}</td>
            <td class="d-block text-truncate">{{ item.valor_multa + item.valor_juros | formatarMoeda }}</td>
            <td class="d-block text-truncate">{{ item.forma_cobranca.descricao }}</td>
            <td v-b-tooltip :title="item.observacao" class="d-block text-truncate">{{ item.observacao }}</td>
            <td class="d-block text-truncate coluna-situacao">
              <span :class="`circle-badge-${item.situacao.toLowerCase()}`" class="circle-badge"></span>
            </td>
          </tr>
        </tbody>
      </g-table>

      <b-row class="form-group">
        <b-col md="6"/>
        <b-col md="2">
          <label class="col-form-label">Multa</label>
          <span class="form-control form-control-disabled">{{ valorMulta | formatarMoeda }}</span>
        </b-col>
        <b-col md="2">
          <label class="col-form-label">Juros</label>
          <span class="form-control form-control-disabled">{{ valorJuros | formatarMoeda }}</span>
        </b-col>
        <b-col md="2">
          <label class="col-form-label">Total</label>
          <span class="form-control form-control-disabled">{{ valorTotal | formatarMoeda }}</span>
        </b-col>
      </b-row>

      <hr class="d-block w-100">

      <div class="body-sector">
        <div class="p-2">
          <b-row>
            <b-col md="3" class="form-group">
              <label for="forma_cobranca" class="col-form-label">Forma de Cobrança *</label>
              <sync-select v-model="parametrosParcelamento.forma_cobranca" :was-validated="wasValidated" :field="{ targetEntity: 'App\\Entity\\Principal\\FormaPagamento', name: 'forma_cobranca', descriptionColumn: 'descricao', valueColumn: 'id' }" required />
            </b-col>

            <b-col md="2" class="form-group">
              <label for="data_vencimento" class="col-form-label">Vencimento *</label>
              <g-datepicker v-model="parametrosParcelamento.data_vencimento" element-id="data_vencimento" />
            </b-col>

            <b-col md="2" class="form-group">
              <label for="dias_subsequentes" class="col-form-label">Dias Subsequentes {{ require_dias_subsequentes ? '*' : '' }}</label>
              <sync-select v-model="parametrosParcelamento.dias_subsequentes" :was-validated="wasValidated" :field="{ targetEntity: 'App\\Entity\\Principal\\DiasSubsequentes', name: 'dias_subsequentes', descriptionColumn: 'descricao', valueColumn: 'id', with: ['franqueada'] }" :required="require_dias_subsequentes" />
            </b-col>

            <b-col md="2" class="form-group">
              <label for="numero_parcelas" class="col-form-label">Nº Parcelas *</label>
              <input v-mask="'#'" v-model.number="parametrosParcelamento.numero_parcelas" type="text" class="form-control" required @change="atualizarValores()">
            </b-col>

            <b-col md="2" class="form-group">
              <label for="valor_desconto" class="col-form-label">Desconto</label>
              <g-numeric v-model="parametrosParcelamento.valor_desconto" class="form-control" @input="atualizarValores()" />
            </b-col>
          </b-row>

          <b-row>
            <b-col md="3" class="form-group">
              <label for="plano_conta" class="col-form-label">Plano de Contas *</label>
              <g-treeselect
                id="plano_conta"
                :value="parametrosParcelamento.plano_conta"
                :input="setPlanoConta"
                :extra-param="1"
                :options="listaPlanosConta"
                required
              />
            </b-col>

            <b-col md="2" class="form-group">
              <label class="col-form-label">Valor Total com Desconto</label>
              <span class="form-control form-control-disabled">{{ parametrosParcelamento.valor_total | formatarMoeda }}</span>
  
            </b-col>

            <b-col md="2" class="form-group">
              <label class="col-form-label">Valor das Parcelas</label>
              <span class="form-control form-control-disabled">{{ parametrosParcelamento.valor_parcela | formatarMoeda }}</span>
            </b-col>
          </b-row>
        </div>

        <titulos-conta-receber :esta-valido="wasValidated" :btn-imprimir-boletos="true" @gerar-parcelas="prepararParametros" />
      </div>

      <div class="form-group">
        <b-btn :disabled="submiting" type="submit" variant="verde">Salvar</b-btn>
        <b-btn :disabled="submiting" type="button" variant="link" @click="limparState(true)">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import {dateToString, stringToISODate} from '../../utils/date'
import FormView from '../FormView'
import TitulosContaReceber from '../contas-receber/TitulosContaReceber.vue'

export default {
  components: {
    TitulosContaReceber
  },

  extends: FormView,

  data () {
    return {
      wasValidated: true,
      sacadoPessoa: null,
      vendedorFuncionario: null,
      titulosRenegociar: [],
      valorMulta: 0,
      valorJuros: 0,
      valorTotal: 0,
      parametrosParcelamento: {
        forma_cobranca: null,
        plano_conta: null,
        numero_parcelas: 1,
        data_vencimento: dateToString(new Date()),
        dias_subsequentes: null,
        valor_parcela: 0,
        valor_desconto: 0,
        valor_total: 0,
        observacao: ''
      }
    }
  },

  computed: {
    ...mapState('planoConta', {listaPlanosConta: 'selectReceitas'}),
    require_dias_subsequentes () {
      return this.parametrosParcelamento.numero_parcelas > 1
    }
  },

  mounted () {
    this.limparState()
    this.buscarPlanoConta().then(this.selecionarPlanoTurmas)

    let titulos = this.$route.query.titulos
    if (typeof titulos === 'string') {
      titulos = [titulos]
    }

    Request.get('/titulo_receber/consulta_renegociacao', { titulo_receber: titulos })
      .then(response => {
        this.titulosRenegociar = response.body.corpo.titulos
 
        if (this.titulosRenegociar.length === 0) {
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: 'Os títulos selecionados não podem ser renegociados.'
          })
   
          this.$router.push('/financeiro/contas-receber')
          return
        }

        this.sacadoPessoa = response.body.corpo.sacado_pessoa
        this.aluno = response.body.corpo.id
        this.valorMulta = response.body.corpo.total_multa
        this.valorJuros = response.body.corpo.total_juros
        this.valorTotal = response.body.corpo.valor_total
        this.parametrosParcelamento.valor_total = this.valorTotal
        this.atualizarValores()
      
      })
  },

  methods: {
    selecionarPlanoTurmas () {
      this.listaPlanosConta.forEach(planoPai => {
        planoPai.filhos.forEach(plano => {
          if (plano.id === 41) {
            this.parametrosParcelamento.plano_conta = plano
          }
        })
      })
    },
    ...mapActions('planoConta', {buscarPlanoConta: 'listar'}),
    atualizarValores () {
      if (this.parametrosParcelamento.numero_parcelas < 1) {
        this.parametrosParcelamento.numero_parcelas = 1
      }

      if (this.parametrosParcelamento.numero_parcelas > 6) {
        this.parametrosParcelamento.numero_parcelas = 6
      }

      this.parametrosParcelamento.valor_total = ((this.valorTotal - this.parametrosParcelamento.valor_desconto).toFixed(2)) * 1
  
      this.parametrosParcelamento.valor_parcela = this.parametrosParcelamento.valor_total / this.parametrosParcelamento.numero_parcelas

    },

    prepararParametros (callback) {
      this.wasValidated = false

      if (!this.parametrosParcelamento.plano_conta || (this.parametrosParcelamento.numero_parcelas > 1 && !this.parametrosParcelamento.dias_subsequentes)) {
        return
      }

      this.parametrosParcelamento.observacao = this.parametrosParcelamento.plano_conta.descricao

      callback(this.parametrosParcelamento)
    },

    setPlanoConta (value, index) {
      this.parametrosParcelamento.plano_conta = value || null
    },

    submit ($event) {
      this.submiting = true
      this.wasValidated = false

      const titulosGerados = this.$store.state.contrato.titulosReceber.map(tit => {
        const titulo = {...tit}
        titulo.forma_cobranca = titulo.forma_cobranca.id
        titulo.forma_recebimento = titulo.forma_cobranca
        titulo.data_vencimento = stringToISODate(titulo.data_vencimento, true)

        if (titulo.transacao_cartao) {
          titulo.transacao_cartao = {...titulo.transacao_cartao}
          if (titulo.transacao_cartao.operadora_cartao) {
            titulo.transacao_cartao.operadora_cartao = titulo.transacao_cartao.operadora_cartao.id
            titulo.transacao_cartao.parcelamento_operadora_cartao = titulo.transacao_cartao.parcelamento_operadora_cartao.id
          }

          titulo.transacao_cartao.valor_liquido = titulo.valor_saldo_devedor
          titulo.transacao_cartao.data_pagamento = titulo.data_vencimento
        }

        if (titulo.boleto) {
          titulo.boleto.data_vencimento = titulo.data_vencimento
        }

        if (titulo.cheque) {
          titulo.cheque = {...titulo.cheque}
          titulo.cheque.valor = titulo.valor_saldo_devedor
          titulo.cheque.data_bom_para = titulo.data_vencimento
        }

        return titulo
      })
      
      if (!this.vendedorFuncionario || !titulosGerados.length) {
        this.submiting = false
        return
      }

      const data = {

        responsavel_financeiro_pessoa: this.sacadoPessoa.id,
        aluno: this.aluno_id,
        vendedor_funcionario: this.vendedorFuncionario.id,
        contas_receber: [],
        titulos_receber: titulosGerados,
        titulos_receber_renegociados: this.titulosRenegociar.map(titulo => titulo.id)
      }

     
   
      Request.post('/renegociacao/criar', data)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: response.body.mensagem
          })

          this.limparState(true)
        })
        .catch(error => {
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: `Erro ao efetuar renegociação: ${error.body.mensagem}`
          })
        })
        .finally(() => {
          this.submiting = false
        })
    },

    limparState (redirect = false) {
      this.$store.commit('contrato/SET_TITULOS_RECEBER', [])
      this.$store.commit('contrato/SET_VALOR_TOTAL_ITENS', 0)
      this.$store.commit('contrato/SET_VALOR_TOTAL_PARCELAS', 0)
      this.$store.commit('planoConta/SET_PAGINA_ATUAL', 1)

      if (redirect === true) {
        this.$router.push('/financeiro/contas-receber')
      }
    }
  }
}
</script>
