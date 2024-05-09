<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida = true, filtrar()">
          <div class="form-group row mb-0">
            <div class="col-md-2">
              <label for="tipo_cheque_rapido" class="col-form-label">Tipo</label>
              <select id="tipo_cheque_rapido" v-model="tipo_cheque_rapido" class="custom-select form-control" @change="buscaRapida = true, filtrar()">
                <option value="1">A Pagar</option>
                <option value="2">A Receber</option>
              </select>
            </div>

            <div class="col-md-2">
              <label for="entrada_rapido" class="col-form-label">Entrada</label>
              <select id="entrada_rapido" v-model="entrada_rapido" class="custom-select form-control" @change="buscaRapida = true, filtrar()">
                <option value="0">Todos</option>
                <option value="1">Janeiro</option>
                <option value="2">Fevereiro</option>
                <option value="3">Março</option>
                <option value="4">Abril</option>
                <option value="5">Maio</option>
                <option value="6">Junho</option>
                <option value="7">Julho</option>
                <option value="8">Agosto</option>
                <option value="9">Setembro</option>
                <option value="10">Outubro</option>
                <option value="11">Novembro</option>
                <option value="12">Dezembro</option>
              </select>
            </div>

            <div class="col-md-2">
              <label for="bompara_rapido" class="col-form-label">Bom para</label>
              <select id="bompara_rapido" v-model="bompara_rapido" class="custom-select form-control" @change="buscaRapida = true, filtrar()">
                <option value="0">Todos</option>
                <option value="1">Janeiro</option>
                <option value="2">Fevereiro</option>
                <option value="3">Março</option>
                <option value="4">Abril</option>
                <option value="5">Maio</option>
                <option value="6">Junho</option>
                <option value="7">Julho</option>
                <option value="8">Agosto</option>
                <option value="9">Setembro</option>
                <option value="10">Outubro</option>
                <option value="11">Novembro</option>
                <option value="12">Dezembro</option>
              </select>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="col-md-6">
                  <label for="situacao_rapido" class="col-form-label">Situação</label>
                </div>
                <div class="col-md-6">
                  <b-form-checkbox-group id="situacao_rapido" v-model="selectedRapidos" :options="situacao" buttons button-variant="cinza" name="situacao_rapido" class="checkbtn-line" @input="buscaRapida = true, filtrar()"/>
                </div>
              </div>
            </div>

          </div>
        </form>
      </b-collapse>
      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaAvancada = true, filtrar()">
          <div class="row">
            <div class="col-md-6">
              <label for="aluno_filtro" class="col-form-label">Aluno</label>
              <typeahead id="aluno_filtro" v-model="aluno_filtro" source-path="/api/titulo_receber/buscar_pessoas_aluno_ou_sacado" key-name="pessoa.nome_contato" />
            </div>

            <div class="col-md-3">
              <label for="numero_cheque" class="col-form-label">Número do cheque</label>
              <input id="numero_cheque" v-model="numero_cheque" type="text" maxlength="9" class="form-control" @input="validaNumero('numero_cheque')">
            </div>

            <div class="col-md-3">
              <label for="tipo_cheque_avancado" class="col-form-label">Tipo</label>
              <select id="tipo_cheque_avancado" v-model="tipo_cheque_avancado" class="custom-select form-control">
                <option value="1">A Pagar</option>
                <option value="2">A Receber</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <label for="banco_avancado" class="col-form-label">Banco</label>
              <input id="banco_avancado" v-model="banco_avancado" type="text" class="form-control" maxlength="50">
            </div>

            <div class="col-md-3">
              <label for="conta_avancado" class="col-form-label">Conta</label>
              <input id="conta_avancado" v-model="conta_avancado" type="text" class="form-control" maxlength="20" @input="validaNumero('conta_avancado')">
            </div>

            <div class="col-md-6">
              <label class="col-form-label">Valor</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Min</div>
                    </div>
                    <!-- <input v-money="moeda" id="valor_inicial" v-model="valor_inicial_temporario" type="text" class="form-control" maxlength="9"> -->
                    <g-numeric id="valor_inicial" :value="valor_inicial_temporario" :input="setInicialTemporario" :max="99999999999" :is-wiped="true" class="form-control" />
                  </div>
                  <div v-if="valor_inicial_temporario > valor_final_temporario && valor_final_temporario > 0" class="input-invalid bg-danger">
                    Valor inicial deve ser menor que a valor final!
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Max</div>
                    </div>
                    <!-- <input v-money="moeda" id="valor_final" v-model="valor_final_temporario" type="text" class="form-control" maxlength="9"> -->
                    <g-numeric id="valor_final" :value="valor_final_temporario" :input="setFinalTemporario" :max="99999999999" :is-wiped="true" class="form-control" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label class="col-form-label">Entrada</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_inicial_entrada_temporario'" :value="data_inicial_entrada_temporario" :selected="setDataInicialEntrada"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker :element-id="'data_final_entrada_temporario'" :value="data_final_entrada_temporario" :selected="setDataFinalEntrada"/>
                  </div>
                </div>
              </div>
              <div v-if="dateToCompare(data_inicial_entrada_temporario) > dateToCompare(data_final_entrada_temporario) && data_final_entrada_temporario !== ''" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-6">
              <label class="col-form-label">Bom para</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_inicial_bomp_temporario'" :value="data_inicial_bomp_temporario" :selected="setDataInicialBomPara"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker :element-id="'data_final_bomp_temporario'" :value="data_final_bomp_temporario" :selected="setDataFinalBomPara"/>
                  </div>
                </div>
              </div>
              <div v-if="dateToCompare(data_inicial_bomp_temporario) > dateToCompare(data_final_bomp_temporario) && data_final_bomp_temporario !== ''" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>
          </div>

          <div class="row">

            <div class="col-md-6">
              <label class="col-form-label">Devolvido em</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_inicial_devolvido_temporario'" :value="data_inicial_devolvido_temporario" :selected="setDataInicialDevolvido"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker :element-id="'data_final_devolvido_temporario'" :value="data_final_devolvido_temporario" :selected="setDataFinalDevolvido"/>
                  </div>
                </div>
              </div>
              <div v-if="!datasDevolucaoFiltroAvancadoValidas" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-4">
              <label for="situacao_avancado" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group id="situacao_avancado" v-model="selectedAvancados" :options="situacao" buttons button-variant="cinza" name="situacao_avancado" class="checkbtn-line"/>
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
            <th data-label="Selecionar todos" class="coluna-checkbox pl-4">
              <b-form-checkbox :disabled="!listaChequesPagarReceber.length" v-model="checkAll" :indeterminate="indeterminate" aria-describedby="selected" aria-controls="selected" @change="toggleAll"/>
            </th>
            <th data-column="ch.numero" class="coluna-numero">Nº Cheque</th>
            <th v-b-tooltip.down title="Observação" class="d-block text-truncate">Observação</th>
            <th data-column="ch.titular">Titular</th>
            <th data-column="p.nome_contato">Aluno (resp. fin.)</th>
            <th data-column="ch.data_bom_para" class="coluna-data">Bom para</th>
            <th data-column="ch.data_baixa" class="coluna-data">Baixado em</th>
            <th data-column="ch.valor" class="coluna-valor">Valor</th>
            <th class="coluna-situacao-icone">Situação</th>
            <th class="coluna-icones">Ações</th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
          <perfect-scrollbar>
            <div v-if="estaCarregando" class="form-loading">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-else-if="!listaChequesPagarReceber.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <div v-else>
              <tr v-for="cheque in listaChequesPagarReceber" :key="cheque.id">
                <td class="coluna-checkbox pl-4" data-label="Selecionar">
                  <b-form-checkbox v-model="selected" :value="cheque"/>
                </td>
  
                <td data-label="Nº Cheque" class="coluna-numero">{{ cheque.numero }}</td>
  
                <td data-label="Observação" class="d-block text-truncate">
                  <template v-if="cheque.titulo_receber">{{ cheque.titulo_receber.observacao }}</template>
                  <template v-if="cheque.titulo_pagar">{{ cheque.titulo_pagar.narrativa_plano_conta }}</template>
                </td>
  
                <td data-label="Titular" class="d-block text-truncate">{{ cheque.titular }}</td>
  
                <td data-label="Aluno (resp. fin.)" class="d-block text-truncate">{{ nomeAlunoDisplay(cheque) }}</td>
  
                <td data-label="Bom Para" class="coluna-data">{{ cheque.data_bom_para | formatarData }}</td>
  
                <td data-label="Baixado Em" class="coluna-data">{{ cheque.data_baixa | formatarData }}</td>
  
                <td data-label="Valor" class="coluna-valor">{{ cheque.valor | formatarMoeda }}</td>
  
                <td data-label="Situação" class="coluna-situacao-icone">
                  <PillSituation 
                    :situation="situacao.filter(item => item.value === cheque.situacao)[0].text" 
                    :situationClass="cheque.situacao.toLowerCase()" 
                    :textTooltip="situacao.filter(item => item.value === cheque.situacao)[0].text"
                  >
                  </PillSituation>
                </td>
  
                <td class="d-flex coluna-icones">
                  <router-link v-if="cheque.situacao === 'P' || cheque.situacao === 'D'" :to="`${$route.path}/atualizar/${cheque.id}`" class="icone-link" title="Atualizar">
                    <font-awesome-icon icon="pen" />
                  </router-link>
  
                  <a v-if="cheque.situacao !=='B'" href="javascript:void(0)" title="Remover" class="icone-link text-muted" @click.prevent="remover(cheque)">
                    <font-awesome-icon icon="trash-alt" />
                  </a>
                </td>
              </tr>
            </div>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="d-flex">
        <div class="info-btn">
          <button :disabled="!habilitaBaixar()" type="button" class="btn btn-verde" @click="onClickBaixarCheques()">
            {{ tipoSelecionadoCheque === '1' ? 'Conciliar':'Depositar' }}
          </button>
        </div>

        <div class="info-btn">
          <button v-b-modal.devolverChequeModal :disabled="!habilitaDevolucao()" type="button" class="btn btn-cinza">
            Devolução
          </button>
        </div>

        <div class="info-btn">
          <button :disabled="!habilitaExcluir()" type="button" class="btn btn-cinza" @click="excluirCheques()">
            Excluir
          </button>
        </div>
      </div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small>
            <template v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</template>
            <template v-else>Nenhum item encontrado</template>
          </small>
        </div>

        <div>
          <small>
            <template v-if="selected.length >= 1">{{ selected.length }} ite{{ selected.length > 1 ? 'ns' : 'm' }} selecionado{{ selected.length > 1 ? 's' : '' }}</template>
            <template v-else>Nenhum item selecionado</template>
          </small>
        </div>
      </div>
    </div>

    <b-modal id="devolverChequeModal" ref="devolverChequeModal" size="md" centered no-close-on-backdrop hide-header hide-footer>
      <form @submit.prevent="devolverCheques()">
        <div class="form-group row">
          <div class="col-md-4">
            <label class="col-form-label" for="data_baixa">Data de devolução *</label>
            <g-datepicker :element-id="'data_baixa'" :value="dataDevolucaoCheques" :selected="setDataDevolucaoCheques" label="Data de devolução *"/>
          </div>
          <div class="col-md-8">
            <label for="motivo_devolucao_cheque" class="col-form-label">Motivo para devolução*</label>
            <g-select
              :value="motivoDevolucaoChequeSelecionado"
              :select="setMotivoDevolucaoChequeSelecionado"
              :options="motivosDevolucaoCheque"
              label="descricao"
              track-by="id" />
              <!-- <div v-if="!motivoDevolucaoChequeSelecionado" class="multiselect-invalid">
              Selecione uma opção!
            </div> -->
          </div>
        </div>
        <div class="d-flex justify-content-center">
          <b-btn :disabled="!podeSalvarDevolverCheques" type="submit" variant="verde">Salvar</b-btn>
          <b-btn type="button" variant="link" @click="finalizar()">Cancelar</b-btn>
        </div>
      </form>
    </b-modal>

    <b-modal ref="baixarChequesModal" title="Baixar cheques" size="md" centered no-close-on-backdrop hide-footer @hide="onHideModalBaixarCheques()">
      <form @submit.prevent="onBaixar()">
        <div class="form-group row">
          <div class="col-md-6">
            <label class="col-form-label" for="data_baixa">Data de baixa *</label>
            <g-datepicker :class="dataBaixaChequesValido ? 'valid-input' : 'invalid-input'" :element-id="'data_baixa'" :value="dataBaixaCheques" :selected="setDataBaixaCheques" label="Data de baixa *"/>
            <div v-if="!dataBaixaChequesValido" class="multiselect-invalid">
              Campo obrigatório
            </div>
          </div>

          <div class="col-md-6">
            <label class="col-form-label" for="valorTotalChequesBaixar">{{ textoValoresCheques }}</label>
            <input v-money="moeda" id="valorTotalChequesBaixar" :value="valorTotalChequesBaixarDisplay" type="text" class="form-control" readonly>
          </div>

          <div class="col-md-6">
          </div>
          <div class="col-md-6">
            <label class="col-form-label">Conta a creditar *</label>
            <g-select
              :class="contaCreditarChequesBaixadosValido ? 'valid-input' : 'invalid-input'"
              :value="contaCreditarChequesBaixados"
              :options="listaContas"
              :select="setContaCreditarChequesBaixados"
              label="descricao"
              track-by="id" />
            <div v-if="!contaCreditarChequesBaixadosValido" class="multiselect-invalid">
              Campo obrigatório
            </div>
          </div>

        </div>
        <div class="d-flex justify-content-center">
          <b-btn type="submit" variant="verde">Baixar</b-btn>
          <b-btn type="button" variant="link" @click="fecharBaixarCheques">Cancelar</b-btn>
        </div>
      </form>

    </b-modal>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {beginOfDay, endOfDay, getDateFromISO, dateToCompare, dateToString, stringToISODate} from '../../utils/date'
import {currencyToNumber} from '../../utils/number'
import EventBus from '../../utils/event-bus'
import PillSituation from '../../components/PillSituation.vue'

export default {
  name: 'ListaChequesPagarReceber',
  components: {
    PillSituation
  },
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      checkAll: false,
      indeterminate: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      tipoSelecionadoCheque: 2,
      entrada_rapido: '',
      bompara_rapido: '',
      selected: [],
      selectedRapidos: [],
      selectedAvancados: [],
      list: [],
      listaPendentesBaixados: [],
      listaPendentesDevolvidos: [],
      motivoDevolucaoChequeIdSelecionado: '',
      data_inicial_entrada: '',
      data_final_entrada: '',
      data_inicial_bomp: '',
      data_inicial_devolvido: '',
      data_final_bomp: '',
      data_final_devolvido: '',
      data_inicial_entrada_temporario: '',
      data_inicial_bomp_temporario: '',
      data_inicial_devolvido_temporario: '',
      data_final_entrada_temporario: '',
      data_final_bomp_temporario: '',
      data_final_devolvido_temporario: '',
      tipo_cheque_rapido: null,
      tipo_cheque_avancado: null,
      banco_avancado: '',
      conta_avancado: null,
      numero_cheque: null,
      aluno_filtro: null,
      valor_inicial: 0,
      valor_final: 0,
      valor_inicial_temporario: 0,
      valor_final_temporario: 0,
      itemSelecionado: null,
      motivoDevolucaoChequeSelecionado: null,
      situacao: [
        {text: 'Pendente', value: 'P'},
        {text: 'Baixado', value: 'B'},
        {text: 'Cancelado', value: 'C'},
        {text: 'Devolvido', value: 'D'}
      ],
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
      dataBaixaCheques: dateToString(new Date()),
      dataDevolucaoCheques: dateToString(new Date()),
      contaCreditarChequesBaixados: null,
      formularioBaixarChequesFoiValidado: false
    }
  },
  computed: {
    ...mapState('chequesPagarReceber', ['listaChequesPagarReceber', 'filtros', 'objCheque', 'estaCarregando', 'totalItens', 'todosItensCarregados']),
    ...mapState('motivoDevolucaoCheque', {motivosDevolucaoCheque: 'lista'}),
    ...mapState('conta', {listaContas: 'lista'}),

    datasDevolucaoFiltroAvancadoValidas: {
      get () {
        return !(dateToCompare(this.data_inicial_devolvido_temporario) > dateToCompare(this.data_final_devolvido_temporario) && this.data_final_devolvido_temporario !== '')
      }
    },

    listaChequesParaBaixar: {
      get () {
        return this.selected.filter((item) => {
          return item.situacao === 'P' || (item.situacao === 'D' && !item.data_segunda_devolucao)
        })
      }
    },

    textoValoresCheques: {
      get () {
        return `${this.listaChequesParaBaixar.length} Cheque(s) Valor total:`
      }
    },

    valorTotalChequesBaixar: {
      get () {
        let valorTotal = 0
        this.listaChequesParaBaixar.forEach((cheque) => {
          valorTotal += parseFloat(cheque.valor)
        })
        return valorTotal.toFixed(2)
      }
    },

    valorTotalChequesBaixarDisplay: {
      get () {
        return `R$ ${this.valorTotalChequesBaixar}`
      }
    },

    dataBaixaChequesValido: {
      get () {
        return !this.formularioBaixarChequesFoiValidado || (this.dataBaixaCheques && new Date(this.dataBaixaCheques) instanceof Date)
      }
    },

    podeSalvarDevolverCheques: {
      get () {
        return !!this.motivoDevolucaoChequeSelecionado && !!this.dataDevolucaoChequesValido
      }
    },

    dataDevolucaoChequesValido: {
      get () {
        return !this.dataDevolucaoCheques || new Date(this.dataDevolucaoCheques) instanceof Date
      }
    },

    contaCreditarChequesBaixadosValido: {
      get () {
        return !this.formularioBaixarChequesFoiValidado || !!this.contaCreditarChequesBaixados
      }
    }

  },
  watch: {
    filtroSelecionado (value) {
      this.filtrar()
    },
    selectedRapidos (selecionados) {
      this.selectedAvancados = selecionados
    },
    selectedAvancadosTemporario (selecionados) {
      this.selectedRapidos = selecionados
    },
    tipo_cheque_rapido (tipoSelecionado) {
      this.tipo_cheque_avancado = tipoSelecionado
      this.tipoSelecionadoCheque = tipoSelecionado
    },

    tipo_cheque_avancado (tipoSelecionado) {
      this.tipo_cheque_rapido = tipoSelecionado
      this.tipoSelecionadoCheque = tipoSelecionado
    }
  },

  mounted () {
    this.tipo_cheque_avancado = 2
    this.tipo_cheque_rapido = 2
    const mesAtual = ((new Date()).getMonth() + 1)
    this.entrada_rapido = mesAtual
    this.bompara_rapido = mesAtual

    this.SET_PAGINA_ATUAL(1)
    this.filtrar()
    this.listarCamposSelects()
    this.listarContas()
  },

  methods: {
    ...mapActions('chequesPagarReceber', {listar: 'getListaChequesPagarReceber', removerPorIdSelecionada: 'removerChequePagarReceber', devolverChequesMultiplos: 'devolverChequePagarReceber', baixarCheque: 'baixarChequePagarReceber', removerChequesSelecionados: 'removerChequesMultiplos'}),
    ...mapActions('motivoDevolucaoCheque', {listarMotivosDevolucaoCheque: 'listar'}),
    ...mapActions('conta', {listarContas: 'getLista'}),
    ...mapMutations('chequesPagarReceber', ['SET_CHEQUE', 'SET_CHEQUE_SELECIONADO', 'SET_ITEM', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY']),

    getDateFromISO: getDateFromISO,
    dateToString: dateToString,
    dateToCompare: dateToCompare,

    nomeAlunoDisplay (cheque) {
      let nomeAluno = ''
      if (cheque.titulo_receber.aluno) {
        nomeAluno += cheque.titulo_receber.aluno.pessoa.nome_contato || cheque.titulo_receber.aluno.pessoa.razao_social
      }
      if (!nomeAluno || cheque.titulo_receber.aluno.pessoa.id !== cheque.pessoa.id) {
        const nomeSacado = cheque.pessoa.nome_contato || cheque.pessoa.razao_social
        nomeAluno += ' (' + nomeSacado + ')'
      }
      return nomeAluno
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listar()
    },

    remover (item) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_CHEQUE_SELECIONADO(item.id)
          this.removerPorIdSelecionada()
            .then(() => {
              this.SET_PAGINA_ATUAL(1)
              this.listar()
            })
        }
      }, `Deseja excluir este cheque?`)
    },

    fecharBaixarCheques () {
      this.$refs.baixarChequesModal.hide()
    },

    onHideModalBaixarCheques () {
      this.limparCamposBaixarCheques()
    },

    limparCamposBaixarCheques () {
      this.dataBaixaCheques = dateToString(new Date())
      this.contaCreditarChequesBaixados = null
      this.formularioBaixarChequesFoiValidado = false
    },

    listarCamposSelects () {
      this.listarMotivosDevolucaoCheque()
    },

    validaNumero (campo) {
      this[campo] = this[campo].replace(/[^\d.]/g, '')
    },

    setDataInicialEntrada (value) {
      this.data_inicial_entrada_temporario = value
    },

    setDataFinalEntrada (value) {
      this.data_final_entrada_temporario = value
    },

    setDataInicialBomPara (value) {
      this.data_inicial_bomp_temporario = value
    },

    setDataFinalBomPara (value) {
      this.data_final_bomp_temporario = value
    },

    setDataInicialDevolvido (value) {
      this.data_inicial_devolvido_temporario = value
    },

    setDataFinalDevolvido (value) {
      this.data_final_devolvido_temporario = value
    },

    setDataBaixaCheques (value) {
      this.dataBaixaCheques = value
    },

    setDataDevolucaoCheques (value) {
      this.dataDevolucaoCheques = value
    },

    setContaCreditarChequesBaixados (value) {
      this.contaCreditarChequesBaixados = value
    },

    setInicialTemporario (value) {
      this.valor_inicial_temporario = value
    },

    setFinalTemporario (value) {
      this.valor_final_temporario = value
    },

    limparStateAnterior () {
      this.$store.commit('chequesPagarReceber/SET_FILTRO_TIPO', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_MES_ENTRADA', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_MES_BOM_PARA', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_ENTRADA_INICIAL', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_ENTRADA_FINAL', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_BOM_PARA_INICIAL', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_BOM_PARA_FINAL', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_DEVOLVIDO_INICIAL', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_DEVOLVIDO_FINAL', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_NUMERO_CHEQUE', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_ALUNO', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_CONTA', '')
      this.$store.commit('chequesPagarReceber/SET_FILTRO_BANCO', null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_VALOR_INICIAL', null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_VALOR_FINAL', null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_SITUACAO', ['P'])
    },

    executaFiltroRapido () {
      this.$store.commit('chequesPagarReceber/SET_FILTRO_TIPO', this.tipo_cheque_rapido)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_MES_ENTRADA', this.entrada_rapido)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_MES_BOM_PARA', this.bompara_rapido)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_SITUACAO', this.selectedRapidos)
    },

    executaFiltroAvancado () {
      this.$store.commit('chequesPagarReceber/SET_FILTRO_TIPO', this.tipo_cheque_avancado)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_ENTRADA_INICIAL', this.data_inicial_entrada_temporario ? beginOfDay(this.data_inicial_entrada_temporario) : null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_ENTRADA_FINAL', this.data_final_entrada_temporario ? endOfDay(this.data_final_entrada_temporario) : null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_BOM_PARA_INICIAL', this.data_inicial_bomp_temporario ? beginOfDay(this.data_inicial_bomp_temporario) : null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_BOM_PARA_FINAL', this.data_final_bomp_temporario ? endOfDay(this.data_final_bomp_temporario) : null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_DEVOLVIDO_INICIAL', this.data_inicial_devolvido_temporario ? beginOfDay(this.data_inicial_devolvido_temporario) : null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_DATA_DEVOLVIDO_FINAL', this.data_final_devolvido_temporario ? endOfDay(this.data_final_devolvido_temporario) : null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_NUMERO_CHEQUE', this.numero_cheque)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_ALUNO', this.aluno_filtro)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_CONTA', this.conta_avancado)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_BANCO', this.banco_avancado)
      const valorInicial = currencyToNumber(this.valor_inicial_temporario)
      const valorFinal = currencyToNumber(this.valor_final_temporario)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_VALOR_INICIAL', valorInicial || null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_VALOR_FINAL', valorFinal || null)
      this.$store.commit('chequesPagarReceber/SET_FILTRO_SITUACAO', this.selectedAvancados)
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
      this.listar()
    },

    limparFiltros () {
      this.data_inicial_entrada_temporario = this.data_inicial_entrada
      this.data_final_entrada_temporario = this.data_final_entrada
      this.data_inicial_bomp_temporario = this.data_inicial_bomp
      this.data_final_bomp_temporario = this.data_final_bomp
      this.data_inicial_devolvido_temporario = this.data_inicial_devolvido
      this.data_final_devolvido_temporario = this.data_final_devolvido
      this.valor_inicial_temporario = this.valor_inicial
      this.valor_final_temporario = this.valor_final
      this.banco_avancado = ''
      this.conta_avancado = ''
      this.numero_cheque = ''
      this.aluno_filtro = ''
    },

    habilitaExcluir () {
      return this.selected.filter((item) => {
        return item.situacao === 'P' || item.situacao === 'D'
      }).length !== 0
    },

    habilitaBaixar () {
      return this.selected.filter((item) => {
        return item.situacao === 'P' || (item.situacao === 'D' && !item.data_segunda_devolucao)
      }).length !== 0
    },

    finalizar (action = 'cancel') {
      this.$refs.devolverChequeModal.hide()
      this.motivoDevolucaoChequeSelecionado = null
      this.motivoDevolucaoChequeIdSelecionado = null
    },

    habilitaDevolucao () {
      return this.selected.filter((item) => {
        return item.situacao === 'P' || item.situacao === 'B'
      }).length !== 0
    },

    toggleAll (checked) {
      if (checked) {
        this.selected = this.listaChequesPagarReceber.slice()
        return
      }

      this.selected = []
    },

    setMotivoDevolucaoChequeSelecionado (value) {
      this.motivoDevolucaoChequeIdSelecionado = value.id
      this.motivoDevolucaoChequeSelecionado = value
    },

    onClickBaixarCheques () {
      this.$refs.baixarChequesModal.show()
    },

    onBaixar () {
      if (!this.validarFormularioBaixarCheques()) {
        return false
      }
      const dataBaixa = stringToISODate(this.dataBaixaCheques, true)
      const conta = this.contaCreditarChequesBaixados.id
      const cheques = this.listaChequesParaBaixar.map(cheque => {
        return {
          id: cheque.id,
          data_baixa: dataBaixa
        }
      })
      const params = {
        cheques,
        conta
      }
      this.baixarCheque(params)
        .finally(() => {
          this.filtrar()
          this.listarCamposSelects()
          this.fecharBaixarCheques()
        })
    },

    validarFormularioBaixarCheques () {
      this.formularioBaixarChequesFoiValidado = true
      let formularioValido = true

      if (!this.contaCreditarChequesBaixadosValido) {
        formularioValido = false
      }
      if (!this.dataBaixaChequesValido) {
        formularioValido = false
      }

      if (this.listaChequesParaBaixar < 1) {
        formularioValido = false
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: `Ao menos um cheque deve ser selecionado para fazer a baixa.`
        })
      }
      return formularioValido
    },

    devolverCheques () {
      const listaChequesParaDevolver = this.selected.filter((item) => {
        return item.situacao === 'P' || item.situacao === 'B'
      })
      const listaIds = []
      listaChequesParaDevolver.forEach((cheque) => {
        listaIds.push(cheque.id)
      })
      const motivoID = this.motivoDevolucaoChequeIdSelecionado
      const dataDevolucao = stringToISODate(this.dataDevolucaoCheques, true)
      const parametros = {
        ids: listaIds,
        motivo_devolucao_cheque: motivoID,
        data_devolucao: dataDevolucao
      }
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.devolverChequesMultiplos(parametros)
            .then(() => {
              this.filtrar()
              this.listarCamposSelects()
              this.$refs.devolverChequeModal.hide()
            })
        },
        reject: () => {
          this.$refs.devolverChequeModal.show()
        }
      }, `Deseja realizar a devolução dos cheques selecionados?`)
    },

    excluirCheques () {
      const listaChequesParaExcluir = this.selected.filter((item) => {
        return item.situacao === 'P' || item.situacao === 'D'
      })
      const listaIds = []
      listaChequesParaExcluir.forEach((cheque) => {
        listaIds.push(cheque.id)
      })
      const parametros = {
        ids: listaIds
      }
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.removerChequesSelecionados(parametros)
            .then(() => {
              this.filtrar()
              this.listarCamposSelects()
            })
        }
      }, `Deseja excluir os cheques selecionados?`)
    },

    mostrarCheque (id) {
      this.$refs.verCheque.hide()
      this.itemSelecionado = null
      this.itemSelecionado = id
    },

    cancelarModalVer () {
      this.$refs.verCheque.hide()
      setTimeout(() => {
        this.itemSelecionado = null
      }, 300)
    }
  }
}
</script>
<style scoped>
.custom-control {
  padding-left: 0;
}

span.badge {
  font-size: 95%;
}

#filtros-rapidos,
#filtros-avancados {
  transition: all .1s;
}

.filtro-avancado .form-group {
  margin-bottom: 1rem;
}
.filtro-header {
  color: #4a4a4a;
}
.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151B1E;
  background-color: #fff;
}
.filtro-avancado .input-group-text {
  border: 0;
  background-color: #E5E5E5;
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
