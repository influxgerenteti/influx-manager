<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limpaFiltrosDeAte()">Filtro Rápido</div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">
            <div class="col-md-4">

              <label v-help-hint="'filtroRapido-cartao_aluno_filtro_rapido'" for="aluno_filtro_rapido" class="form-label">Aluno</label>
              <typeahead id="aluno_filtro_rapido" :item-hit="setAluno" source-path="/api/titulo_receber/buscar_pessoas_aluno_ou_sacado"
                         key-name="nome_contato"/>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-cartao_numero_lancamento'" for="numero_lancamento_filtro_rapido" class="form-label">Numero Lançamento</label>
              <input id="numero_lancamento_filtro_rapido" v-model="numero_lancamento_temporario" type="text" class="form-control" maxlength="9" @change="filtrar">
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-cartao_situacao'" for="situacao_filtro_rapido" class="form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group id="situacao_filtro_rapido" v-model="situacaoSelecionada" :options="situacao" buttons button-variant="cinza" name="situacao_rapido" class="checkbtn-line" @change="setSituacao"/>
              </div>
            </div>
          </div>
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-cartao_operadora_cartao'" for="operadora_cartao_rapido" class="form-label">Operadora Cartão</label>
              <g-select
                id="operadora_cartao_rapido"
                :value="operadora_cartao_temporaria"
                :select="setOperadoraCartao"
                :options="listaOperadoras"
                :extra-param="true"
                label="descricao"
                track-by="id" />
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-cartao_tipo_transacao'" for="tipo_transacao_rapido" class="form-label">Tipo Transação</label>
              <select id="tipo_transacao_rapido" v-model="tipo_transacao_temporaria" class="form-control custom-select" @change="filtrar">
                <option value="">Selecione</option>
                <option value="C">Crédito</option>
                <option value="D">Débito</option>
              </select>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-cartao_tipo_identificador'" for="identificador_filtro_rapido" class="form-label">Identificador</label>
              <input id="identificador_filtro_rapido" v-model="identificador_temporario" type="text" class="form-control" maxlength="255" @change="filtrar">
            </div>
          </div>
        </form>
      </b-collapse>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaRapida=false, buscaAvancada = true, filtrar()">
          <div class="form-group row">
            <div class="col-md-4">
              <label v-help-hint="'filtroAvancado-cartao_aluno'" for="aluno_filtro_avancado" class="form-label">Aluno</label>
              <typeahead id="aluno_filtro_avancado" :item-hit="setAluno" source-path="/api/titulo_receber/buscar_pessoas_aluno_ou_sacado"
                         key-name="nome_contato"/>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroAvancado-cartao_numero_lancamento'" for="numero_lancamento_filtro_avancado" class="form-label">Numero Lançamento</label>
              <input id="numero_lancamento_filtro_avancado" v-model="numero_lancamento_temporario" type="text" class="form-control" maxlength="9">
            </div>
            <div class="col-auto">
              <label v-help-hint="'filtroAvancado-cartao_situacao'" for="situacao_filtro_avancado" class="form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group id="situacao_filtro_avancado" v-model="situacaoSelecionada" :options="situacao" buttons button-variant="cinza" name="situacao_avancado" class="checkbtn-line" />
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-4">
              <label v-help-hint="'filtroAvancado-cartao_operadora_cartao'" for="operadora_cartao_avancado" class="form-label">Operadora Cartão</label>
              <g-select
                id="operadora_cartao_avancado"
                :value="operadora_cartao_temporaria"
                :select="setOperadoraCartao"
                :options="listaOperadoras"
                label="descricao"
                track-by="id" />
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroAvancado-cartao_tipo_transacao'" for="tipo_transacao_avancado" class="form-label">Tipo Transação</label>
              <select id="tipo_transacao_avancado" v-model="tipo_transacao_temporaria" class="form-control custom-select">
                <option value="">Selecione</option>
                <option value="C">Crédito</option>
                <option value="D">Débito</option>
              </select>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroAvancado-cartao_identificador'" for="identificador_avancado" class="form-label">Identificador</label>
              <input id="identificador_avancado" v-model="identificador_temporario" type="text" class="form-control" maxlength="255">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-4">
              <label v-help-hint="'filtroAvancado-cartao_valor_liquido'" class="form-label">Valor Liquido</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Min</div>
                    </div>
                    <input v-money="moeda" id="valor_liquido_inicial" v-model="valor_liquido_inicial_temporario" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Max</div>
                    </div>
                    <input v-money="moeda" id="valor_liquido_final" v-model="valor_liquido_final_temporario" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroAvancado-cartao_previsao_repasse'" class="form-label">Previsão Repasse</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'previsao_repasse_inicial_temporario'" :value="previsao_repasse_inicial_temporario" :selected="setPrevisaoRepasseInicial"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker :element-id="'previsao_repasse_final_temporario'" :value="previsao_repasse_final_temporario" :selected="setPrevisaoRepasseFinal"/>
                  </div>
                </div>
              </div>
              <div v-if="dateToCompare(previsao_repasse_inicial_temporario) > dateToCompare(previsao_repasse_final_temporario) && previsao_repasse_final_temporario !== ''" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroAvancado-cartao_data_estorno'" class="form-label">Data Estorno</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_estorno_inicial_temporario'" :value="data_estorno_inicial_temporario" :selected="setDataEstornoInicial"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker :element-id="'data_estorno_final_temporario'" :value="data_estorno_final_temporario" :selected="setDataEstornoFinal"/>
                  </div>
                </div>
              </div>
              <div v-if="dateToCompare(data_estorno_inicial_temporario) > dateToCompare(data_estorno_final_temporario) && data_estorno_final_temporario !== ''" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable" class="selectAll">
        <thead class="text-dark">
          <tr>
            <th data-label="Selecionar todos" class="coluna-checkbox">
              <b-form-checkbox :disabled="!listaCartao.length" v-model="checkAll" :indeterminate="indeterminate" aria-describedby="selected" aria-controls="selected" @change="toggleAll"/>
            </th>
            <th v-b-tooltip.viewport.down data-column="trc.identificador" title="Identificador" class="d-block text-truncate">Identificador</th>
            <th data-column="op.descricao" class="coluna-operadora-cartao d-block text-truncate">Operadora</th>
            <th v-b-tooltip.viewport.down data-column="tr.observacao" title="Observação" class="d-block text-truncate">Observação</th>
            <th data-column="sp.nome_contato">Aluno (Sacado)</th>
            <th data-column="trc.tipo_transacao">Tipo</th>
            <th v-b-tooltip.viewport.down data-column="trc.numero_lancamento" title="Nº Lançamento" class="d-block text-truncate">Nº Lançamento</th>
            <th v-b-tooltip.viewport.down data-column="trc.previsao_repasse" title="Previsão Repasse" class="d-block text-truncate coluna-data">Previsão Repasse</th>
            <th v-b-tooltip.viewport.down data-column="trc.data_estorno" title="Data Estorno" class="d-block text-truncate coluna-data">Data Estorno</th>
            <th v-b-tooltip.viewport.down data-column="trc.valor_liquido" title="Valor Líquido" class="d-block text-truncate coluna-valor">Valor Líquido</th>
            <th class="coluna-situacao-icone">Situação</th>
            <th class="coluna-icones">Ações</th>
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <perfect-scrollbar>
            <div v-if="estaCarregando" class="form-loading">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaCartao.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="cartao in listaCartao" :key="cartao.id">
              <td class="coluna-checkbox" data-label="Selecionar">
                <b-form-checkbox v-model="selected" :value="cartao" :disabled="cartao.situacao === 'EST'"/>
              </td>

              <td>{{ cartao.identificador }}</td>

              <td v-b-tooltip.viewport.down :title="cartao.operadora_cartao.descricao" class="coluna-operadora-cartao d-block text-truncate">{{ cartao.operadora_cartao.descricao }}</td>

              <td v-b-tooltip.viewport.down :title="cartao.titulo_receber.observacao" class="d-block text-truncate">{{ cartao.titulo_receber.observacao }}</td>

              <td v-b-tooltip.viewport.down :title="nomeAlunoDisplay(cartao)" class="d-block text-truncate">{{ nomeAlunoDisplay(cartao) }}</td>

              <td>{{ cartao.tipo_transacao === 'C' ? 'Crédito': 'Débito' }}</td>

              <td>{{ cartao.numero_lancamento }}</td>

              <td class="coluna-data">{{ cartao.previsao_repasse | formatarData }}</td>
              <td class="coluna-data">{{ cartao.data_estorno | formatarData }}</td>
              <td class="coluna-valor">{{ cartao.valor_liquido | formatarMoeda }}</td>

              <td data-label="Situação" class="coluna-situacao-icone">
                <PillSituation 
                    :situation="situacao.filter(item => item.value === cartao.situacao)[0].text" 
                    :situationClass="cartao.situacao.toLowerCase()" 
                    :textTooltip="situacao.filter(item => item.value === cartao.situacao)[0].text"
                  >
                  </PillSituation>
              </td>

              <td class="d-flex coluna-icones">
                <a v-if="cartao.situacao === 'PEN'" href="javascript:void(0)" title="Atualizar data de previsão" class="icone-link" @click.prevent="setCartaoSelecionado(cartao.id), $refs.alteraPrevisaoRepasseModal.show()">
                  <font-awesome-icon icon="pen" />
                </a>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="d-flex">
        <div class="info-btn">
          <button :disabled="!podeConciliar" type="button" class="btn btn-verde" @click="conciliarModal">Conciliar</button>
        </div>

        <div class="info-btn">
          <button :disabled="!podeEstornar" type="button" class="btn btn-verde" @click="confirmaEstorno()">Estornar</button>
        </div>
      </div>
      <div class="info-label">
        <div class="text-right">
          <small>
            {{ totalItens === 0 ? 'Nenhum' : totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}
            <br >
            {{ selected.length === 0 ? 'Nenhum' : selected.length }} ite{{ selected.length > 1 ? 'ns' : 'm' }} selecionado{{ selected.length > 1 ? 's' : '' }}
          </small>
        </div>
      </div>
    </div>

    <b-modal id="alteraPrevisaoRepasseModal" ref="alteraPrevisaoRepasseModal" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <form @submit.prevent="alterarDataPrevisao()">
        <div class="form-group">
          <label for="alterado_data_previsao_repasse">Nova data de Previsão de Repasse</label>
          <g-datepicker :element-id="'alterado_data_previsao_repasse'" :value="alterado_data_previsao_repasse_temporario" :selected="setDataPrevisaoRepasse"/>
        </div>

        <div class="d-flex justify-content-center">
          <b-btn :disabled="!alterado_data_previsao_repasse_temporario || (enviandoDataPrevisao === true)" type="submit" variant="verde">{{ enviandoDataPrevisao ? 'Salvando...':'Salvar' }}</b-btn>
          <b-btn type="button" variant="link" @click="finalizar()">Cancelar</b-btn>
        </div>
      </form>
    </b-modal>

    <b-modal ref="conciliarModal" title="Conciliar transações" size="md" centered no-close-on-backdrop hide-footer @hide="onModalConciliarHide()">
      <form @submit.prevent="conciliarCartoes()">
        <div class="form-group row">

          <div class="col-md-6">
            <label class="col-form-label" for="data_conciliacao">Data de conciliação *</label>
            <g-datepicker :class="dataConciliacaoValida ? 'valid-input' : 'invalid-input'" :element-id="'data_conciliacao'" :value="dataConciliacao" :selected="setDataConciliacao" label="Data de conciliação *"/>
            <div v-if="!dataConciliacaoValida" class="multiselect-invalid">
              Campo obrigatório
            </div>
          </div>

          <div class="col-md-6">
            <label class="col-form-label">Conta a creditar *</label>
            <g-select
              :class="contaCreditarConciliacaoValida ? 'valid-input' : 'invalid-input'"
              :value="contaCreditarConciliacao"
              :options="listaContas"
              :select="setContaCreditarConciliacao"
              label="descricao"
              track-by="id" />
            <div v-if="!contaCreditarConciliacaoValida" class="multiselect-invalid">
              Campo obrigatório
            </div>
          </div>

        </div>
        <div class="d-flex justify-content-center">
          <b-btn type="submit" variant="verde">Conciliar</b-btn>
          <b-btn type="button" variant="link" @click="fecharConciliar">Cancelar</b-btn>
        </div>
      </form>

    </b-modal>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {stringToISODate, endOfDay, getDateFromISO, dateToCompare, dateToString} from '../../utils/date'
import {currencyToNumber} from '../../utils/number'
import Typeahead from '../../components/Typeahead.vue'
import PillSituation from '../../components/PillSituation.vue'
import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaCartao',
  components: {
    Typeahead,
    PillSituation
  },
  data () {
    return {
      className: 'rapido-open',
      data_estorno_inicial_temporario: '',
      data_estorno_final_temporario: '',
      previsao_repasse_inicial_temporario: '',
      previsao_repasse_final_temporario: '',
      alterado_data_previsao_repasse_temporario: '',
      operadora_cartao_temporaria: '',
      identificador_temporario: '',
      tipo_transacao_temporaria: '',
      numero_lancamento_temporario: '',
      buscaAvancada: false,
      buscaRapida: false,
      checkAll: false,
      indeterminate: false,
      filtroAvancado: false,
      enviandoDataPrevisao: false,
      conciliandoDados: false,
      estornoDados: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      valor_liquido_inicial: null,
      valor_liquido_final: null,
      valor_liquido_inicial_temporario: null,
      valor_liquido_final_temporario: null,
      selected: [],
      situacaoSelecionada: ['PEN'],
      situacao: [
        {text: 'Pendente', value: 'PEN'},
        {text: 'Creditado', value: 'CRE'},
        {text: 'Estornado', value: 'EST'}
      ],
      tiposBuscaPorNome: ['aluno'],
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: true
      },
      number: {
        '#': {
          pattern: /\d/
        }
      },
      dataConciliacao: dateToString(new Date()),
      contaCreditarConciliacao: null,
      formularioConciliarFoiValidado: false
    }
  },

  computed: {
    ...mapState('pessoas', ['objPessoa']),
    ...mapState('cartao', ['filtros', 'cartaoSelecionadoId', 'objCartao', 'listaCartao', 'estaCarregando', 'todosItensCarregados', 'totalItens']),
    ...mapState('operadoraCartao', {listaOperadoras: 'lista'}),
    ...mapState('conta', {listaContas: 'lista'}),

    podeConciliar: {
      get () {
        return (this.selected.filter(item => item.situacao === 'PEN').length !== 0) && (this.conciliandoDados === false) && (this.selected.filter(item => item.situacao === 'CRE').length === 0)
      }
    },

    podeEstornar: {
      get () {
        return (this.selected.filter(item => item.situacao === 'PEN').length !== 0) || (this.selected.filter(item => item.situacao === 'CRE').length !== 0)
      }
    },

    listaTransacoesConciliar: {
      get () {
        return this.selected.filter((item) => {
          return item.situacao === 'PEN'
        })
      }
    },

    contaCreditarConciliacaoValida: {
      get () {
        return !this.formularioConciliarFoiValidado || !!this.contaCreditarConciliacao
      }
    },

    dataConciliacaoValida: {
      get () {
      // Checando se a data de baixa foi preenchida e se é uma data válida
        return !this.formularioConciliarFoiValidado || (this.dataConciliacao && new Date(this.dataConciliacao) instanceof Date)
      }
    }
  },

  mounted () {
    this.$store.commit('operadoraCartao/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('operadoraCartao/listar')

    this.SET_PAGINA_ATUAL(1)
    this.filtrar()
    this.listarContas()
  },

  methods: {
    ...mapActions('cartao', ['getListaCartao', 'getCartao', 'atualizarSituacao', 'atualizaDataRepasse', 'conciliar', 'estornar']),
    ...mapActions('tituloReceber', ['movimentarConta']),
    ...mapActions('conta', {listarContas: 'getLista'}),
    ...mapMutations('pessoas', ['setPessoa']),
    ...mapMutations('cartao', ['SET_FILTRO_ALUNO_ID', 'SET_FILTRO_OPERADORA_CARTAO', 'SET_FILTRO_TIPO_TRANSACAO', 'SET_FILTRO_NUMERO_LANCAMENTO', 'SET_FILTRO_IDENTIFICADOR', 'SET_FILTRO_SITUACAO', 'SET_FILTRO_DATA_ESTORNO_INICIO', 'SET_FILTRO_DATA_ESTORNO_FIM', 'SET_FILTRO_PREVISAO_REPASSE_INICIO', 'SET_FILTRO_PREVISAO_REPASSE_FIM', 'SET_FILTRO_VALOR_LIQUIDO_INICIO', 'SET_FILTRO_VALOR_LIQUIDO_FIM', 'setCartaoSelecionado', 'SET_PAGINA_ATUAL', 'SET_ORDER_BY']),

    getDateFromISO: getDateFromISO,

    dateToCompare: dateToCompare,

    nomeAlunoDisplay (cartao) {
      let nomeAluno = ''
      if (cartao.titulo_receber.aluno) {
        nomeAluno += cartao.titulo_receber.aluno.pessoa.nome_contato || cartao.titulo_receber.aluno.pessoa.razao_social
      }
      if (!nomeAluno || cartao.titulo_receber.aluno.pessoa.id !== cartao.titulo_receber.sacado_pessoa) {
        const nomeSacado = cartao.titulo_receber.sacado_pessoa.nome_contato || cartao.titulo_receber.sacado_pessoa.razao_social
        nomeAluno += '(' + nomeSacado + ')'
      }
      return nomeAluno
    },

    conciliarModal () {
      this.conciliandoDados = true
      this.$refs.conciliarModal.show()
    },

    fecharConciliar () {
      this.$refs.conciliarModal.hide()
    },

    onModalConciliarHide () {
      this.conciliandoDados = false
      this.resetarInfoConciliar()
    },

    resetarInfoConciliar () {
      this.dataConciliacao = dateToString(new Date())
      this.contaCreditarConciliacao = null
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.filtrar()
    },

    setSituacao (value) {
      this.situacaoSelecionada = value
      this.filtrar()
    },

    conciliarCartoes () {
      if (!this.validarFormularioConciliar()) {
        return false
      }
      const transacoes = this.listaTransacoesConciliar.map((transacao) => {
        return {
          id: transacao.id,
          data_conciliacao: stringToISODate(this.dataConciliacao, true),
          conta: this.contaCreditarConciliacao.id
        }
      })

      const params = {
        transacoes
      }

      this.conciliar(params)
        .finally(resposta => {
          this.conciliandoDados = false
          this.selected = []
          this.fecharConciliar()
          this.filtrar()
        })
    },

    validarFormularioConciliar () {
      this.formularioConciliarFoiValidado = true
      let formularioValido = true

      if (this.listaTransacoesConciliar < 1) {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: `Ao menos uma transação deve ser selecionada para fazer a conciliação.`
        })
        formularioValido = false
      }
      if (!this.contaCreditarConciliacaoValida) {
        formularioValido = false
      }
      if (!this.dataConciliacaoValida) {
        formularioValido = false
      }
      return formularioValido
    },

    confirmaEstorno () {
      this.estornoDados = true
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.estornarCartoes()
        },
        reject: () => {
          this.estornoDados = false
        }
      }, `Deseja realizar o estorno das transações selecionadas?`)
    },

    estornarCartoes () {
      const listaEstornos = this.selected.filter((item) => {
        return item.situacao === 'PEN' || item.situacao === 'CRE'
      })

      const transacoes = listaEstornos.map(cartao => ({ transacao_cartao: cartao.id }))

      this.estornar(transacoes)
        .finally(resposta => {
          this.estornoDados = false
          this.selected = []
          this.filtrar()
        })
    },

    alterarDataPrevisao () {
      this.enviandoDataPrevisao = true
      const dataPrevisao = this.alterado_data_previsao_repasse_temporario ? endOfDay(this.alterado_data_previsao_repasse_temporario) : null
      this.atualizaDataRepasse(dataPrevisao)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Previsão de Repasse alterado com sucesso!'
          })
          this.finalizar()
        })
        .catch(mensagem => {
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: `Erro ao atualizar a data de previsão de repasse. Mensagem:` + mensagem
          })
          this.enviandoDataPrevisao = false
        })
    },

    finalizar (action = 'cancel') {
      this.enviandoDataPrevisao = false
      this.$refs.alteraPrevisaoRepasseModal.hide()
      this.alterado_data_previsao_repasse_temporario = ''
      this.setCartaoSelecionado(null)
      this.filtrar()
    },

    limparFiltros () {
      this.data_estorno_inicial_temporario = ''
      this.data_estorno_final_temporario = ''
      this.previsao_repasse_inicial_temporario = ''
      this.previsao_repasse_final_temporario = ''
      this.operadora_cartao_temporaria = ''
      this.identificador_temporario = ''
      this.tipo_transacao_temporaria = ''
      this.numero_lancamento_temporario = ''
      this.valor_liquido_inicial_temporario = null
      this.valor_liquido_final_temporario = null
    },

    limpaFiltrosDeAte () {
      this.data_estorno_inicial_temporario = ''
      this.data_estorno_final_temporario = ''
      this.previsao_repasse_inicial_temporario = ''
      this.previsao_repasse_final_temporario = ''
      this.valor_liquido_inicial_temporario = null
      this.valor_liquido_final_temporario = null
    },

    toggleAll (checked) {
      if (checked) {
        this.selected = this.listaCartao.filter((item) => {
          return item.situacao === 'PEN' || item.situacao === 'CRE'
        })
        return
      }

      this.selected = []
    },

    limparStateAnterior () {
      this.SET_FILTRO_NUMERO_LANCAMENTO(null)
      this.SET_FILTRO_SITUACAO(null)
      this.SET_FILTRO_OPERADORA_CARTAO(null)
      this.SET_FILTRO_TIPO_TRANSACAO(null)
      this.SET_FILTRO_IDENTIFICADOR(null)
      this.SET_FILTRO_VALOR_LIQUIDO_INICIO(null)
      this.SET_FILTRO_VALOR_LIQUIDO_FIM(null)
      this.SET_FILTRO_PREVISAO_REPASSE_INICIO(null)
      this.SET_FILTRO_PREVISAO_REPASSE_FIM(null)
      this.SET_FILTRO_DATA_ESTORNO_INICIO(null)
      this.SET_FILTRO_DATA_ESTORNO_FIM(null)
    },

    executaFiltroRapido () {
      let pessoaId = this.objPessoa ? this.objPessoa.id : null
      this.SET_FILTRO_ALUNO_ID(pessoaId)
      this.SET_FILTRO_NUMERO_LANCAMENTO(this.numero_lancamento_temporario)
      this.SET_FILTRO_SITUACAO(this.situacaoSelecionada)
      this.SET_FILTRO_OPERADORA_CARTAO(this.operadora_cartao_temporaria)
      this.SET_FILTRO_TIPO_TRANSACAO(this.tipo_transacao_temporaria)
      this.SET_FILTRO_IDENTIFICADOR(this.identificador_temporario)
    },

    executaFiltroAvancado () {
      let pessoaId = this.objPessoa ? this.objPessoa.id : null
      this.SET_FILTRO_ALUNO_ID(pessoaId)
      this.SET_FILTRO_NUMERO_LANCAMENTO(this.numero_lancamento_temporario)
      this.SET_FILTRO_SITUACAO(this.situacaoSelecionada)
      this.SET_FILTRO_OPERADORA_CARTAO(this.operadora_cartao_temporaria)
      this.SET_FILTRO_TIPO_TRANSACAO(this.tipo_transacao_temporaria)
      this.SET_FILTRO_IDENTIFICADOR(this.identificador_temporario)

      const valorLiquidoInicial = this.valor_liquido_inicial_temporario ? currencyToNumber(this.valor_liquido_inicial_temporario) : null
      const valorLiquidoFinal = this.valor_liquido_final_temporario ? currencyToNumber(this.valor_liquido_final_temporario) : null
      this.SET_FILTRO_VALOR_LIQUIDO_INICIO(valorLiquidoInicial)
      this.SET_FILTRO_VALOR_LIQUIDO_FIM(valorLiquidoFinal)

      let dataPrevisaoRepasseInicio = this.previsao_repasse_inicial_temporario ? stringToISODate(this.previsao_repasse_inicial_temporario) : null
      let dataPrevisaoRepasseFim = this.previsao_repasse_final_temporario ? endOfDay(this.previsao_repasse_final_temporario) : null
      let dataEstornoInicio = this.data_estorno_inicial_temporario ? stringToISODate(this.data_estorno_inicial_temporario) : null
      let dataEstornoFim = this.data_estorno_final_temporario ? endOfDay(this.data_estorno_final_temporario) : null
      this.SET_FILTRO_PREVISAO_REPASSE_INICIO(dataPrevisaoRepasseInicio)
      this.SET_FILTRO_PREVISAO_REPASSE_FIM(dataPrevisaoRepasseFim)
      this.SET_FILTRO_DATA_ESTORNO_INICIO(dataEstornoInicio)
      this.SET_FILTRO_DATA_ESTORNO_FIM(dataEstornoFim)
    },

    filtrar () {
      this.toggleAll(false)
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else {
        this.executaFiltroAvancado()
      }
      this.SET_PAGINA_ATUAL(1)
      this.getListaCartao()
    },

    setAluno (objetoPessoa) {
      this.setPessoa(objetoPessoa)
      if (this.filtroSelecionado === 1) {
        this.filtrar()
      }
    },

    setPrevisaoRepasseInicial (objetoData) {
      this.previsao_repasse_inicial_temporario = objetoData
    },

    setPrevisaoRepasseFinal (objetoData) {
      this.previsao_repasse_final_temporario = objetoData
    },

    setDataEstornoInicial (objetoData) {
      this.data_estorno_inicial_temporario = objetoData
    },

    setDataEstornoFinal (objetoData) {
      this.data_estorno_final_temporario = objetoData
    },

    setDataPrevisaoRepasse (objetoData) {
      this.alterado_data_previsao_repasse_temporario = objetoData
    },

    setOperadoraCartao (value, rapido = false) {
      this.operadora_cartao_temporaria = value
      if (rapido === true) {
        this.filtrar()
      }
    },

    setContaCreditarConciliacao (value) {
      this.contaCreditarConciliacao = value
    },

    setDataConciliacao (value) {
      this.dataConciliacao = value
    }

  }
}

</script>
<style scoped>
#total-container {
  background-color: #EBECF0;
  font-size: 1rem;
}
#total-container span {
  color: #4a4a4a;
}
.info-label > div {
  display: inline-block;
}

.info-btn .btn + .btn {
  margin-top: .5rem;
}
</style>
