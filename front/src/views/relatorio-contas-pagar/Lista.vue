<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado">
      <b-row class="form-group">
        <b-col md="6">
          <div>
            <b-form-checkbox v-model="todosFornecedores" class="mb-2">Todos</b-form-checkbox>
            <b-form-checkbox v-for="categoria in opcoesCategoriaPessoa"
                             :disabled="todosFornecedores"
                             :key="categoria.value"
                             v-model="tipoFornecedor"
                             :value="categoria.value"
                             class="mb-2">{{ categoria.text }}</b-form-checkbox>
          </div>

          <div class="d-flex">
            <typeahead v-if="filtros.favorecido_pessoa === null"
                       id="buscaDestino"
                       :item-hit="setFavorecidoPessoa"
                       :additional-data="{name: 'tipo_fornecedor', data: tipoFornecedor}"
                       class="w-100"
                       source-path="/api/pessoa/buscar_nome_contato"
                       key-name="nome_contato"
            />

            <template v-else>
              <span class="form-control form-control-disabled">{{ filtros.favorecido_pessoa.nome_contato }}</span>
              <div>
                <b-btn variant="link" @click="filtros.favorecido_pessoa = null">Limpar</b-btn>
              </div>
            </template>
          </div>
        </b-col>

        <b-col md="auto" class="d-flex flex-column justify-content-end">
          <label v-help-hint="'filtro-relatorio-contas-pagar_situacao'" for="situacao" class="form-label d-block">Situação</label>
          <b-form-checkbox-group id="situacao" v-model="filtros.situacao" :options="listaSituacao" buttons button-variant="cinza" name="situacao" class="checkbtn-line"/>
        </b-col>
      </b-row>

      <div class="form-group row">
        <div class="col-md-3">
          <label v-help-hint="'filtro-relatorio-contas-pagar_data_inicial_vencimento'" for="data_inicial_vencimento" class="form-label">Vencimento entre</label>
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
          <label v-help-hint="'filtro-relatorio-contas-pagar_data_inicial_pagamento'" for="data_inicial_pagamento" class="form-label">Pagamento entre</label>
          <div class="row">
            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">De</div>
                </div>
                <g-datepicker :element-id="'data_inicial_pagamento'" :value="filtros.data_inicial_pagamento" :selected="setDataInicialPagamento"/>
              </div>
            </div>

            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">à</div>
                </div>
                <g-datepicker :element-id="'data_final_pagamento'" :value="filtros.data_final_pagamento" :selected="setDataFinalPagamento"/>
              </div>
            </div>
          </div>
          <div v-if="dateToCompare(filtros.data_inicial_pagamento) > dateToCompare(filtros.data_final_pagamento) && filtros.data_final_pagamento !== ''" class="floating-message bg-danger">
            Data inicial deve ser menor que a data final!
          </div>
        </div>

        <b-col md="3">
          <label v-help-hint="'filtro-relatorio-contas-pagar_valor_inicial'" for="valor_inicial" class="form-label">Valor entre</label>
          <b-row>
            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">De</div>
                </div>
                <vue-numeric id="valor_inicial" :precision="2" :empty-value="null" v-model="filtros.valor_inicial" :max="9999999.99" separator="." class="form-control" />
              </div>
            </div>

            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">à</div>
                </div>
                <vue-numeric id="valor_final" :precision="2" :empty-value="null" v-model="filtros.valor_final" :max="9999999.99" separator="." class="form-control" />
              </div>
            </div>
          </b-row>

          <div v-if="filtros.valor_inicial > filtros.valor_final && filtros.valor_final !== null" class="floating-message bg-danger">
            Valor inicial deve ser menor que o valor final!
          </div>
        </b-col>
      </div>

      <div class="form-group row">
        <b-col md="2">
          <label v-help-hint="'filtro-relatorio-contas-pagar_conta'" for="conta" class="form-label">Conta</label>
          <g-select id="conta"
                    :value="filtros.conta"
                    :select="setConta"
                    :options="listaContas"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id" />
        </b-col>

        <b-col md="2">
          <label v-help-hint="'filtro-relatorio-contas-pagar_forma_cobranca'" for="forma_cobranca" class="form-label">Forma de Cobrança</label>
          <g-select id="forma_cobranca"
                    :value="filtros.forma_cobranca"
                    :select="setFormaCobranca"
                    :options="listaFormasPagamento"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id" />
        </b-col>

        <b-col md="2">
          <label v-help-hint="'filtro-relatorio-contas-pagar_forma_pagamento'" for="forma_pagamento" class="form-label">Forma de Pagamento</label>
          <g-select id="forma_pagamento"
                    :value="filtros.forma_pagamento"
                    :select="setFormaPagamento"
                    :options="listaFormasPagamento"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id" />
        </b-col>

        <b-col md="2">
          <label v-help-hint="'filtro-relatorio-contas-pagar_categoria'" for="plano_conta" class="form-label">Categoria</label>
          <g-select id="plano_conta"
                    :value="filtros.plano_conta"
                    :select="setPlanoConta"
                    :options="listaPlanosConta"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id" />
        </b-col>
      </div>

      <b-row class="form-group">
        <b-col md="3">
          <label v-help-hint="'filtro-relatorio-contas-pagar_conta'" for="conta" class="form-label">Agrupar por</label>
          <g-select
            :value="filtros.agrupamento"
            :select="setAgrupamento"
            :options="listaAgrupamentos"
            class="multiselect-truncate"
            label="text"
            track-by="value" />
        </b-col>

        <b-col md="9">
          <b-form-group label="Opções de impressão">
            <!-- <b-form-checkbox v-model="filtros.apenas_folhas_pagamento" name="check-button">Apenas despesas referentes a folhas de pagamento</b-form-checkbox> -->
            <b-form-checkbox v-model="filtros.excel" name="check-button">Exportar para excel</b-form-checkbox>
          </b-form-group>
        </b-col>
      </b-row>

      <b-btn class="btn btn-cinza btn-block text-uppercase" @click="abrirRelatorio()">Gerar relatório</b-btn>
    </div>
  </div>
</template>

<script>
import {mapState} from 'vuex'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'
import open from '../../utils/open'

export default {
  name: 'ListaRelatorioContasPagar',
  data () {
    return {
      selected: 0,
      situacao: [],
      listaSituacao: [
        {text: 'Pendente', value: 'PEN'},
        {text: 'Liquidado', value: 'LIQ'},
        {text: 'Vencido', value: 'VEN'}
      ],
      listaAgrupamentos: [
        {text: 'Não agrupar', value: null},
        {text: 'Destino', value: 'destino'},
        {text: 'Situação', value: 'situacao'},
        {text: 'Categoria', value: 'categoria'},
        {text: 'Data de vencimento', value: 'data_vencimento'},
        {text: 'Data de pagamento', value: 'data_pagamento'}
      ],
      todosFornecedores: false,
      tipoFornecedor: [],
      opcoesCategoriaPessoa: [
        {text: 'Aluno', value: 'aluno'},
        {text: 'Fornecedor', value: 'fornecedor'},
        {text: 'Funcionário', value: 'funcionario'},
        {text: 'Instrutor', value: 'instrutor'}
      ]
    }
  },

  computed: {
    ...mapState('relatorioContasPagar', ['filtros']),

    listaContas: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.$store.state.conta.lista)
      }
    },

    listaFormasPagamento: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.$store.state.formaPagamento.lista)
      }
    },

    listaPlanosConta: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.$store.state.planoConta.selectDespesas)
      }
    }
  },

  mounted () {
    this.$store.commit('conta/SET_PAGINA_ATUAL', 1)
    this.$store.commit('formaPagamento/SET_PAGINA_ATUAL', 1)
    this.$store.commit('planoConta/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('conta/getLista')
    this.$store.dispatch('formaPagamento/getLista')
    this.$store.dispatch('planoConta/listar')
  },

  methods: {
    dateToCompare: dateToCompare,

    setFavorecidoPessoa (value) {
      this.filtros.favorecido_pessoa = value
    },

    setAgrupamento (value) {
      this.filtros.agrupamento = value
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

    setConta (value) {
      this.filtros.conta = value
    },

    setFormaCobranca (value) {
      this.filtros.forma_cobranca = value
    },

    setFormaPagamento (value) {
      this.filtros.forma_pagamento = value
    },

    setPlanoConta (value) {
      this.filtros.plano_conta = value
    },

    abrirRelatorio () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const filtrosRelatorio = this.converterDadosParaLink()
      open(`api/relatorios/contas_pagar/imprimir?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${filtrosRelatorio}`, '_blank')
    },

    converterDadosParaLink () {
      const form = {...this.filtros}

      const dados = {
        agrupamento: form.agrupamento ? form.agrupamento.value : null,
        excel: form.excel === true ? 1 : 0,
        apenas_folhas_pagamento: form.apenas_folhas_pagamento === true ? 1 : 0,
        situacao: form.situacao && form.situacao.length ? form.situacao : null,
        conta: form.conta ? form.conta.id : null,
        plano_conta: form.plano_conta ? form.plano_conta.id : null,
        favorecido_pessoa: form.favorecido_pessoa ? form.favorecido_pessoa.id : null,
        forma_cobranca: form.forma_cobranca ? form.forma_cobranca.id : null,
        forma_pagamento: form.forma_pagamento ? form.forma_pagamento.id : null,
        data_vencimento_inicial: form.data_inicial_vencimento ? beginOfDay(form.data_inicial_vencimento) : null,
        data_vencimento_final: form.data_final_vencimento ? endOfDay(form.data_final_vencimento) : null,
        data_pagamento_inicial: form.data_inicial_pagamento ? beginOfDay(form.data_inicial_pagamento) : null,
        data_pagamento_final: form.data_final_pagamento ? endOfDay(form.data_final_pagamento) : null,
        valor_inicial: form.valor_inicial ? form.valor_inicial : null,
        valor_final: form.valor_final ? form.valor_final : null
      }

      let dadosArray = []
      for (let key in dados) {
        if (dados[key] !== null) {
          dadosArray.push(`${key}=${dados[key]}`)
        }
      }

      return dadosArray.join('&')
    }
  }
}
</script>

<style scoped>
.filtro-header {
  color: #4a4a4a;
}
.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

.datepicker {
  padding: 0;
}

.table-actions {
  padding-top: .5rem;
  padding-bottom: .5rem;
  color: #3e515b;
  border-top: 1px solid #EBECF0;
}
</style>
