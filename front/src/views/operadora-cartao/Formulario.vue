<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="content-sector sector-primary p-3">
        <div class="form-group row">
          <div class="col-md-6">
            <label for="descricao_operadora" class="col-form-label">Descrição*</label>
            <input v-model="descricaoOperadora" type="text" class="form-control" required maxlength="255" >
          </div>
          <div class="col-md-6">
            <label for="tipo_operacao" class="col-form-label">Tipo de Operação *</label>
            <g-select
              id="tipo_operacao"
              :value="tipoOperacaoSelecionado"
              :select="setTipoOperacao"
              :options="tipoOperacaoOpcoes"
              :invalid="!isValid && !tipoOperacaoSelecionado"
              label="text"
              track-by="value" />
            <div v-if="!isValid && !tipoOperacaoSelecionado" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </div>
        </div>
      </div>
      <div class="content-sector sector-secondary p-3 mb-3">
        <div class="col-md-12 d-flex justify-content-between p-0">
          <h5 class="title-module mb-2">Parcelamentos</h5>
          <button type="button" class="btn btn-azul" @click="adicionarNovo">
            <font-awesome-icon icon="plus" />
          </button>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <g-table>
              <thead>
                <tr>
                  <th>Descrição</th>
                  <th class="coluna-icones"></th>
                </tr>
              </thead>
              <tbody>
                <perfect-scrollbar>
                  <div v-if="parcelamentoOperadorasCartao.length === 0" class="busca-vazia">
                    <p>Nenhuma parcela adicionada.</p>
                  </div>
                  <tr v-for="(parcelamentoOperadora, index) in parcelamentoOperadorasCartao" :key="index" @dblclick="editarParcela(parcelamentoOperadora, index)">
                    <td data-label="Descrição">{{ parcelamentoOperadora.descricao }}</td>
                    <td class="coluna-icones">
                      <a href="javascript:void(0)" class="icone-link" title="Atualizar" @click.prevent="editarParcela(parcelamentoOperadora, index)">
                        <font-awesome-icon icon="pen" />
                      </a>
                      <a href="javascript:void(0)" class="icone-link" title="Remover" @click.prevent="removerParcela(parcelamentoOperadora, index)">
                        <!--<font-awesome-icon icon="minus-square" />-->
                        <font-awesome-icon icon="trash-alt" />
                      </a>
                    </td>
                  </tr>
                </perfect-scrollbar>
              </tbody>
            </g-table>
          </div>
        </div>
      </div>

      <!--<div class="form-group list-group-accent">
        <div class="list-group-item list-group-item-accent-info list-group-item-info border-0">
          <font-awesome-icon icon="info-circle" /> As informações não salvas durante a adição/alteração, serão perdidas!
        </div>
      </div>-->

      <div class="form-group pt-2">
        <b-btn :disabled="isEnviando || !isCamposObrigatoriosPreenchidos()" type="submit" variant="verde">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>

    <b-modal id="parcelamentoOperadoraCartaoModal" ref="parcelamentoOperadoraCartaoModal" v-model="visibleParcelamentoOperadoraCartaoModal" size="lg" centered no-close-on-backdrop hide-header hide-footer @show='openModal()'>
      <modal-parcelamentos ref="parcelamentoOperadoraCartaoComponente" :parcelamento-opradora-cartao-objeto="parcelaSelecionada" :parcela-parcelamento-cartao-lista="listaParcelasDoParcelamento" :atualiza-dados-listagem="atualizaDadosListagem" :indice-parcela-operadora-cartao="indiceASerEditado" :cancelar-dados="cancelarModal" @hide="visibleParcelamentoOperadoraCartaoModal = false"/>
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import ModalParcelamentos from './ModalParcelamentos.vue'
import {required} from 'vuelidate/lib/validators'
import {toNumber} from '../../utils/number'

export default {
  name: 'FormularioOperadoraCartao',
  components: {
    ModalParcelamentos
  },
  data () {
    return {
      indiceASerEditado: 0,
      isValid: true,
      isEdit: false,
      isEnviando: false,
      visibleParcelamentoOperadoraCartaoModal: false,
      parcelaSelecionada: null,
      descricaoOperadora: '',
      tipoOperacaoSelecionado: {value: null, text: 'Selecione'},
      parcelamentosOperadorasEditados: [],
      parcelamentoOperadorasCartao: [],
      listaParcelasDoParcelamento: [],
      arrayItensDeletados: [],
      tipoOperacaoOpcoes: [
        {value: null, text: 'Selecione'},
        {value: 'C', text: 'Crédito'},
        {value: 'D', text: 'Débito'}
      ]
    }
  },
  computed: {
    ...mapState('operadoraCartao', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando'])
  },
  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()
    // this.$refs.parcelamentoOperadoraCartaoComponente.listaCamposDinamicos()
    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
        .then(item => {
          this.descricaoOperadora = this.itemSelecionado.descricao
          this.tipoOperacaoSelecionado = this.tipoOperacaoOpcoes.filter((item) => { return item.value === this.itemSelecionado.tipo_operacao })[0]
          if (item.parcelamentoOperadoraCartaos) {
            this.itemSelecionado.parcelamento_operadora_cartaos = item.parcelamentoOperadoraCartaos.map(parcelaOperadoraParcelmantoObj => {
              const novoObjeto = {...parcelaOperadoraParcelmantoObj}
              novoObjeto.plano_conta = novoObjeto.plano_conta
              novoObjeto.parcela_parcelamentos = novoObjeto.parcelaParcelamentos.map(p => {
                p.dias_repasse = toNumber(p.dias_repasse)
                p.taxa = toNumber(p.taxa)
                return p
              })
              delete novoObjeto.parcelaParcelamentos
              return novoObjeto
            })
            delete this.itemSelecionado.parcelamentoOperadoraCartaos
            this.parcelamentoOperadorasCartao = Object.assign([], this.itemSelecionado.parcelamento_operadora_cartaos)
            this.parcelamentosOperadorasEditados = Object.assign([], this.parcelamentoOperadorasCartao)
          }
        })
    }
  },
  validations: {
    descricaoOperadora: {required},
    tipoOperacaoSelecionado: {required}
  },
  methods: {
    ...mapMutations('operadoraCartao', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('operadoraCartao', ['buscar', 'criar', 'atualizar']),

    openModal() {
    //   this.$refs.parcelamentoOperadoraCartaoComponente.init()
    //  this.$refs.parcelamentoOperadoraCartaoComponente.listaCamposDinamicos()
    },
    isCamposObrigatoriosPreenchidos () {
      if ((this.descricaoOperadora.trim().length > 0) && (this.tipoOperacaoSelecionado.value !== null)) {
        return true
      }
      return false
    },

    setTipoOperacao (value) {
      this.tipoOperacaoSelecionado = value
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/cadastros/operadora-cartao')
    },

    finalizaRequisicao () {
  //    console.info('Erro')
      this.isEnviando = false
    },

    atualizaDadosListagem (indiceParcelaOperadoraCartao, novoObjeto, novoRegistro) {
      if (novoRegistro === true) {
        this.parcelamentosOperadorasEditados.push(novoObjeto)
        this.parcelamentoOperadorasCartao.push(novoObjeto)
      } else {
        this.parcelamentoOperadorasCartao[indiceParcelaOperadoraCartao].descricao = novoObjeto.descricao
        this.parcelamentosOperadorasEditados[indiceParcelaOperadoraCartao] = novoObjeto
      }
    },

    montaParametrosRequisicao () {
      this.itemSelecionado.descricao = this.descricaoOperadora
      this.itemSelecionado.tipo_operacao = this.tipoOperacaoSelecionado.value
      let novoArray = this.parcelamentoOperadorasCartao.concat(this.arrayItensDeletados)
      novoArray = novoArray.map(parcelaParcelamentoCartao => {
        if (parcelaParcelamentoCartao.id === null) {
          delete parcelaParcelamentoCartao.id
        }
        if (typeof parcelaParcelamentoCartao.plano_conta === 'object') {
          parcelaParcelamentoCartao.plano_conta = parcelaParcelamentoCartao.plano_conta.id
        }
        return parcelaParcelamentoCartao
      })
      this.itemSelecionado.parcelamento_operadora_cartaos = novoArray
    },

    adicionarNovo () {
      this.parcelaSelecionada = {
        id: null,
        descricao: null,
        plano_conta: null,
        parcela_parcelamentos: [{
          numero_parcela: null,
          dias_repasse: '',
          taxa: ''
        }]
      }
      this.indiceASerEditado = 0
      this.listaParcelasDoParcelamento = this.parcelaSelecionada.parcela_parcelamentos
      this.$refs.parcelamentoOperadoraCartaoModal.show()
      setTimeout(() => {
        this.$refs.parcelamentoOperadoraCartaoComponente.listaCamposDinamicos()
        this.$refs.parcelamentoOperadoraCartaoComponente.planoContaSelecionado = this.parcelaSelecionada.plano_conta
        this.$refs.parcelamentoOperadoraCartaoComponente.novoRegistro = true
      }, 100)
    },

    editarParcela (item, index) {
      this.indiceASerEditado = index
      this.parcelaSelecionada = item
      this.$refs.parcelamentoOperadoraCartaoModal.show()
      setTimeout(() => {
        this.$refs.parcelamentoOperadoraCartaoComponente.listaCamposDinamicos()
        this.listaParcelasDoParcelamento = item.parcela_parcelamentos
        this.$refs.parcelamentoOperadoraCartaoComponente.planoContaSelecionado = item.plano_conta
        let objetoOriginal = Object.assign({}, item)
        objetoOriginal.parcela_parcelamentos = objetoOriginal.parcela_parcelamentos.map(p => ({...p}))
        this.$refs.parcelamentoOperadoraCartaoComponente.objetoOriginal = objetoOriginal
        this.$refs.parcelamentoOperadoraCartaoComponente.novoRegistro = false
      }, 100)
    },

    cancelarModal () {
      let objetoOriginal = this.$refs.parcelamentoOperadoraCartaoComponente.objetoOriginal
      this.parcelaSelecionada.plano_conta = objetoOriginal.plano_conta
      this.parcelaSelecionada.parcela_parcelamentos = objetoOriginal.parcela_parcelamentos
      this.$refs.parcelamentoOperadoraCartaoModal.hide()
    },

    removerParcela (parcelamentoOperadorasCartao, index) {
      if (parcelamentoOperadorasCartao.id !== null) {
        let novoObjeto = Object.assign({}, parcelamentoOperadorasCartao)
        novoObjeto.deletado = 1
        this.arrayItensDeletados.push(novoObjeto)
      }
      this.parcelamentoOperadorasCartao.splice(index, 1)
    },

    salvar () {
      this.isEnviando = true

      if (this.$v.$invalid) {
        this.isValid = false
        this.isEnviando = false
        return
      }

      this.montaParametrosRequisicao()

      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(this.finalizaRequisicao)
      } else {
        this.criar().then(this.voltar).catch(this.finalizaRequisicao)
      }
    }
  }
}
</script>
<style scoped>
.table-scroll {
  height: calc(100vh - 300px);
  height: -webkit-calc(100vh - 300px);
  height: -moz-calc(100vh - 300px);
}
.table-scroll tbody {
  height: calc(100% - 1rem);
  height: -webkit-calc(100% - 1rem);
  height: -moz-calc(100% - 1rem);
}
.table-scroll tbody > div {
  height: 100%;
}
</style>
