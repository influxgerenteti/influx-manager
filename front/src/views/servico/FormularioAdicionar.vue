<template>
  <div>
    <b-modal id="formAdicionar" ref="formAdicionar" v-model="formAdicionar" title="Adicionar serviço" size="lg" stacking centered no-close-on-backdrop hide-header hide-footer @hidden="hiddenCallback">
      <div class="animated fadeIn">
        <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
          <div v-if="salvando" class="form-loading">
            <load-placeholder :loading="estaCarregando || salvando" />
          </div>
          <div class="form-group">
            <b-row>
              <b-col md="6">
                <label v-help-hint="'form-adicionar_servico_aluno'" for="aluno" class="col-form-label">Aluno *</label>
                <template v-if="!isEdit">
                  <typeahead id="aluno" ref="refFormularioAluno" :item-hit="setNomeAluno" source-path="/api/aluno/buscar_nome_com_contrato" key-name="pessoa.nome_contato" required/>
                </template>
                <template v-else>
                  <input v-if="isEdit" id="aluno" :value="alunoTemporario" :disabled="isEdit" type="text" class="form-control" >
                </template>
              </b-col>

              <div class="col-md-6">
                <b-row>
                  <b-col md="6">
                    <label v-help-hint="'form-adicionar_servico_data_solicitacao'" for="data_solicitacao" class="col-form-label">Data solicitação *</label>
                    <g-datepicker
                      :element-id="'data_solicitacao'"
                      :value="dataSolicitacao"
                      :selected="setDataSolicitacao"
                      required />
                  </b-col>
                  <b-col md="6">
                    <label v-help-hint="'form-adicionar_servico_data_prevista'" for="data_prevista" class="col-form-label">Data prevista</label>
                    <g-datepicker
                      :element-id="'data_prevista'"
                      :value="dataPrevista"
                      :selected="setDataPrevista"
                    />
                  </b-col>
                </b-row>
                <div v-if="dataFiltroInvalida(dataSolicitacao,dataPrevista)" class="floating-message bg-danger">
                  Data inicial deve ser menor que a data final!
                </div>
              </div>
            </b-row>

            <b-row>
              <b-col md="6">
                <label v-help-hint="'form-adicionar_servico'" for="servico" class="col-form-label">Serviço *</label>
                <g-select
                  id="servico"
                  :value="servico"
                  :options="listaDeItem"
                  :select="setServico"
                  :disabled="isEdit"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id"
                  required
                />
              </b-col>
              <b-col md="3">
                <label v-help-hint="'form-adicionar_servico_quantidade'" for="quantidade" class="col-form-label">Quantidade *</label>
                <input v-mask="'########'" id="quantidade" v-model="quantidade" class="form-control" required @input="setCusto">
              </b-col>
              <b-col md="3">
                <label v-help-hint="'form-adicionar_servico_custo'" for="custo" class="col-form-label">Custo</label>
                <vue-numeric id="custo" :precision="2" :empty-value="null" v-model="custoCalculado" :max="9999999.99" separator="." class="form-control" disabled />
              </b-col>
            </b-row>

            <b-row>
              <b-col md="6">
                <label v-help-hint="'form-adicionar_servico_responsavel'" for="responsavel" class="col-form-label">Respónsavel pela execulção *</label>
                <g-select
                  id="responsavel"
                  :value="responsavel"
                  :options="listaDeFuncionario"
                  :select="setResponsavelPelaExecucao"
                  class="multiselect-truncate"
                  label="apelido"
                  track-by="id"
                  required/>
              </b-col>
              <b-col md="6">
                <label v-help-hint="'form-adicionar_servico_situacao'" for="situacao" class="col-form-label">Situação</label>
                <g-select
                  id="situacao"
                  :value="situacao"
                  :options="opcoesSituacao"
                  :select="setSituacao"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="value"
                  disabled/>
              </b-col>
            </b-row>
          </div>

          <div v-if="isEdit">
            <label for="historicoDeServico" class="col-form-label">Histórico de serviço</label>
            <b-form-textarea
              id="historicoDeServico"
              :value="historicoServico"
              class="full-textarea"
              readonly
              rows="6"
            />
          </div>

          <div>
            <label for="observacao" class="col-form-label">Observação *</label>
            <b-form-textarea
              id="observacao"
              v-model="observacao"
              class="full-textarea"
              rows="6"
              required
            />
          </div>

          <div class="form-group row">
            <div class="col-md-4">
              <label for="formaPagamento" class="col-form-label">Forma pagamento</label>
              <g-select
                id="formaPagamento"
                :value="formaPagameto"
                :select="setFormaPagamento"
                :options="listaDeFormaPagamentoRequisicao"
                class="multiselect-truncate"
                label="descricao"
                track-id="id"
              />
            </div>
          </div>

          <div v-if="!formaPagameto" class="list-group-item list-group-item-accent-info list-group-item-info border-0">
            <font-awesome-icon icon="info-circle" /> Para concluir o serviço selecione uma forma de pagamento.
          </div>

          <div class="row">
            <div class="col-md-6 form-group pt-2">
              <b-btn :disabled="salvando" type="submit" variant="verde" @click="bSalvarESair = false">{{ salvando ? "Salvando..." : "Salvar" }}</b-btn>
              <b-btn :disabled="salvando" type="submit" variant="verde" @click="bSalvarESair = true">{{ salvando ? "Salvando..." : "Salvar e sair" }}</b-btn>
              <b-btn :disabled="!formaPagameto || salvando" variant="primary" @click="concluir()">Concluir</b-btn>
              <b-btn :disabled="salvando" variant="link" @click="cancelar()">Cancelar</b-btn>

            </div>
            <div class="col-md-6 form-group pt-2">
              <b-btn v-if="isEdit && itemSelecionado.situacao !== 'CAN'" class="d-flex ml-auto" variant="outline-danger" @click="cancelamento()">Cancelar serviço</b-btn>
            </div>
          </div>
        </form>

      </div>
    </b-modal>

    <!-- Modal de confirmação do concluir -->
    <b-modal id="confirmar-concluir" ref="confirmar-concluir" v-model="confirmarConcluir" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center">
        <p>Serviço foi realmente concluído?</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn class="mt-3 mr-3" variant="outline-success" block @click="confirmarConcluir = false, concluido = true, salvar()">Confirmar</b-btn>
        <b-btn class="mt-3" variant="outline-danger" block @click="confirmarConcluir = false, formAdicionar = true">Cancelar</b-btn>
      </div>
    </b-modal>

    <!-- Modal de confirmação do cancelar serviço -->
    <b-modal id="modalCancelarServico" ref="modalCancelarServico" v-model="modalCancelarServico" size="sm" title="Cancelamento de serviço" hide-footer no-close-on-backdrop>
      <div class="d-block text-center">
        <p>Deseja cancelar este serviço?</p>
        <p>(ação irreversível)</p>
      </div>

      <div class="d-flex justify-content-center">
        <b-btn :disabled="salvando" variant="vermelho" @click="modalCancelarServico = false, confirmarCancelarServico = true, salvar()">Confirmar</b-btn><!-- enviar valor de cancelar para a requisição ao salvar um cancelamento de serviço -->
        <button type="button" class="btn btn-link" @click="modalCancelarServico = false, formAdicionar = true">Cancelar</button><!-- @click="estaValido = true" -->
      </div>
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import {dateToString, stringToISODate, dateToCompare} from '../../utils/date'
import EventBus from '../../utils/event-bus'

export default {
  data () {
    return {
      salvando: false,
      bSalvarESair: false,
      isValid: true,
      isEdit: false,
      concluido: false,
      formaPagameto: null,
      alunoTemporario: null,
      dataSolicitacao: '',
      dataPrevista: '',
      servico: null,
      responsavel: null,
      quantidade: null,
      custo: null,
      modalCancelarServico: false,
      situacao: {id: 1, descricao: 'Em andamento', value: 'EA'},
      opcoesSituacao: [
        {id: 1, descricao: 'Em andamento', value: 'EA'},
        {id: 2, descricao: 'Concluído', value: 'C'},
        {id: 3, descricao: 'Cancelado', value: 'CAN'}
      ],
      observacao: '',
      confirmarConcluir: false,
      confirmarCancelarServico: false,
      formAdicionar: false,
      custoCalculado: 0
    }
  },
  computed: {
    ...mapState('servico', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('item', {listaDeItemRequisicao: 'lista'}),
    ...mapState('funcionario', {listaDeFuncionario: 'lista'}),
    ...mapState('formaPagamento', {listaDeFormaPagamentoRequisicao: 'lista'}),

    listaDeItem: {
      get () {
        return [...this.listaDeItemRequisicao.filter(item => item.tipo_item.tipo === 'S')]
      }
    },

    historicoServico: {
      get () {
        let stringFinal = ''
        const novaLinha = '\n'
        let arrayDeServicos = this.itemSelecionado.servicoHistoricos
        if (arrayDeServicos) {
          let novo
          let data
          let horas
          let minutos
          let funcionario
          let descricao
          arrayDeServicos.forEach(element => {
            novo = ''
            data = dateToString(new Date(element.data_criacao))
            horas = new Date(element.data_criacao).getHours()
            minutos = new Date(element.data_criacao).getMinutes()
            funcionario = element.funcionario.apelido
            descricao = element.descricao
            data = data + '-' + horas + ':' + minutos
            novo = data + '-' + funcionario + novaLinha + descricao + novaLinha
            stringFinal += novo
          })
        }
        return stringFinal
      }
    }
  },

  mounted () {
    this.$store.commit('item/SET_PAGINA_ATUAL', 1)
    this.$store.commit('formaPagamento/SET_PAGINA_ATUAL', 1)
    this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)

    this.$store.dispatch('item/getLista')
    this.listarFuncionario()
    this.listarFormaPagamento()

    EventBus.$on('form-incluir:abrir', () => {
      this.custoCalculado = 0

      this.buscarDadosFormulario()
    })
  },

  validations: {
    alunoTemporario: {required},
    dataSolicitacao: {required},
    servico: {required},
    responsavel: {required},
    quantidade: {required},
    observacao: {required}

  },

  methods: {
    ...mapMutations('servico', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_CONCLUIDO', 'SET_CANCELAMENTO', 'SET_ALUNO', 'SET_DATA_SOLICITACAO', 'SET_DATA_PREVISTA', 'SET_SERVICO', 'SET_QUANTIDADE', 'SET_FUNCIONARIO', 'SET_DESCRICAO', 'SET_FORMA_PAGAMENTO', 'SET_VALOR', 'SET_ESTA_CARREGANDO']),
    ...mapActions('servico', ['buscar', 'criar', 'atualizar']),
    ...mapMutations('item', {setfiltroTipoItem: 'SET_FILTRO_TIPO_ITEM'}),
    ...mapActions('item', {listarItem: 'getLista'}),
    ...mapActions('funcionario', {listarFuncionario: 'listar'}),
    ...mapActions('formaPagamento', {listarFormaPagamento: 'getLista'}),

    dateToString: dateToString,
    stringToISODate: stringToISODate,

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== ''
    },

    cancelar () {
      this.isValid = true
      this.limparCamposESair()
      // this.isEdit = false
      // this.formAdicionar = false
    },

    limparCampos () {
      this.alunoTemporario = null
      this.dataSolicitacao = ''
      this.dataPrevista = ''
      this.servico = null
      this.custo = null
      this.quantidade = null
      this.responsavel = null
      this.observacao = null
      this.concluido = false
      this.confirmarCancelarServico = false
      this.formaPagameto = null
      this.custoCalculado = 0

      if (this.isEdit === false) {
        this.$refs.refFormularioAluno.resetSelection()
      }
    },

    buscarDadosFormulario () {
      this.formAdicionar = true
      const id = this.itemSelecionadoID
      this.$store.commit('servico/LIMPAR_ITEM_SELECIONADO')
      if (id) {
        this.isEdit = true
        this.buscar(id)
          .then(response => {
            this.alunoTemporario = this.itemSelecionado.aluno.pessoa.nome_contato ? this.itemSelecionado.aluno.pessoa.nome_contato : null
            this.dataSolicitacao = this.itemSelecionado.data_solicitacao ? dateToString(new Date(this.itemSelecionado.data_solicitacao)) : ''
            this.dataPrevista = this.itemSelecionado.data_conclusao ? dateToString(new Date(this.itemSelecionado.data_conclusao)) : ''
            this.servico = this.itemSelecionado.item ? this.itemSelecionado.item : null
            this.quantidade = this.itemSelecionado.quantidade ? this.itemSelecionado.quantidade : null
            this.responsavel = this.itemSelecionado.funcionario ? this.itemSelecionado.funcionario : null
            this.formaPagameto = this.itemSelecionado.forma_cobranca ? this.itemSelecionado.forma_cobranca : null

            this.setNomeAluno(this.alunoTemporario)
            this.setDataSolicitacao(this.dataSolicitacao)
            this.setDataPrevista(this.dataPrevista)
            this.setServico(this.servico)
            this.setFormaPagamento(this.formaPagameto)
            this.setResponsavelPelaExecucao(this.responsavel)
          })
      }
    },

    setNomeAluno (value) {
      this.alunoTemporario = value
    },

    setDataSolicitacao (value) {
      this.dataSolicitacao = value
    },

    setDataPrevista (value) {
      this.dataPrevista = value
    },

    setServico (value) {
      this.custo = value.valor_venda
      this.custoCalculado = this.quantidade * this.custo
      this.servico = value
    },

    setResponsavelPelaExecucao (value) {
      this.responsavel = value
    },

    setSituacao (value) {
      this.situacao = value
    },

    setFormaPagamento (value) {
      this.formaPagameto = value
    },

    setCusto (value) {
      this.custoCalculado = this.quantidade * this.custo
    },

    montarParamentros () {
      let alunoId = this.alunoTemporario ? this.alunoTemporario.id : null
      let dataSolicitacao = this.dataSolicitacao ? stringToISODate(this.dataSolicitacao) : null
      let dataPrevista = this.dataPrevista ? stringToISODate(this.dataPrevista) : null
      let servico = this.servico ? this.servico.id : null
      let quantidade = this.quantidade ? this.quantidade : null
      let funcionario = this.responsavel ? this.responsavel.id : null
      let descricao = this.observacao ? this.observacao : null
      let formaPagamento = this.formaPagameto ? this.formaPagameto.id : null
      let valor = this.custoCalculado ? this.custoCalculado : null

      this.SET_ALUNO(alunoId)
      this.SET_DATA_SOLICITACAO(dataSolicitacao)
      this.SET_DATA_PREVISTA(dataPrevista)
      this.SET_SERVICO(servico)
      this.SET_QUANTIDADE(quantidade)
      this.SET_FUNCIONARIO(funcionario)
      this.SET_DESCRICAO(descricao)
      this.SET_FORMA_PAGAMENTO(formaPagamento)
      this.SET_VALOR(valor)
    },

    montarParamentrosEdicao () {
      let quantidade = this.quantidade ? this.quantidade : null
      let funcionario = this.responsavel ? this.responsavel.id : null
      let dataSolicitacao = this.dataSolicitacao ? stringToISODate(this.dataSolicitacao) : null
      let dataPrevista = this.dataPrevista ? stringToISODate(this.dataPrevista) : null
      let descricao = this.observacao ? this.observacao : null
      let formaPagamento = this.formaPagameto ? this.formaPagameto.id : null
      let valor = this.custoCalculado ? this.custoCalculado : null

      this.SET_QUANTIDADE(quantidade)
      this.SET_FUNCIONARIO(funcionario)
      this.SET_DATA_SOLICITACAO(dataSolicitacao)
      this.SET_DATA_PREVISTA(dataPrevista)
      this.SET_DESCRICAO(descricao)
      this.SET_FORMA_PAGAMENTO(formaPagamento)
      this.SET_VALOR(valor)
    },

    limparCamposESair () {
      this.limparCampos()
      this.isEdit = false
      this.formAdicionar = false
      this.$emit('filtrar')
    },

    // voltar () {
    //   this.LIMPAR_ITEM_SELECIONADO()
    //   this.$router.push('/academico/servico')
    // },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }
      this.isValid = true
      this.SET_CONCLUIDO(this.concluido)
      this.SET_CANCELAMENTO(this.confirmarCancelarServico)
      this.salvando = true

      if (this.itemSelecionadoID) {
        this.montarParamentrosEdicao()
        this.atualizar().then(() => {
          if (this.bSalvarESair || this.concluido || this.confirmarCancelarServico) {
            this.limparCamposESair()
          } else {
            this.limparCampos()
            this.buscarDadosFormulario()
          }
        }).finally(() => {
          this.salvando = false
        }).catch(() => {
          this.limparCamposESair()
        })
      } else {
        this.montarParamentros()
        this.criar().then(() => {
          if (this.bSalvarESair || this.concluido) {
            this.limparCamposESair()
          } else {
            this.limparCampos()
          }
        }).finally(() => {
          this.salvando = false
        })
      }
    },

    hiddenCallback () {
      this.$emit('hidden')
    },

    concluir () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.isValid = true
      this.confirmarConcluir = true
    },

    cancelamento () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.isValid = true
      this.modalCancelarServico = true
    }

  }
}
</script>
