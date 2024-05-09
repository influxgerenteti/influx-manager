<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="form-group row mb-3">
        <div class="col-md-6">
          <label v-help-hint="'form-planejamento-licao_descricao'" for="descricao" class="col-form-label">Descrição *</label>
          <input id="descricao" v-model="descricao" type="text" class="form-control" required maxlength="50">
          <div class="invalid-feedback planejamento-licao">Preencha a descrição!</div>
        </div>
      </div>

      <div class="table-responsive-sm mb-3">
        <div class="content-sector-extra p-2 box-scroll">
          <div v-if="listLoading" class="d-flex form-loading">
            <load-placeholder :loading="listLoading" />
          </div>

          <h5 class="title-module mb-2 px-2">Lições *</h5>

          <b-row class="header-card-list mx-2 mb-0">
            <b-col md="1">
              <label class="col-form-label">#</label>
            </b-col>
            <b-col md="">
              <label class="col-form-label">Descrição *</label>
            </b-col>
            <b-col md="2">
              <label class="col-form-label">Modalidade *</label>
            </b-col>
            <b-col md="2"/>
          </b-row>

          <div class="row data-scroll">
            <perfect-scrollbar class="scroller col-12">
              <b-row v-for="(item, index) in lista" :key="index" class="body-card-list mx-2">
                <b-col :num="item.numero = index + 1" :plan="!itemSelecionadoID ? null : item.planejamento_licao = itemSelecionadoID" md="1" data-header="#">{{ index + 1 }}</b-col>
                <b-col md="" data-header="Descrição *">
                  <input v-model="item.descricao" type="text" class="form-control descricao-input" maxlength="50" required>
                </b-col>
                <b-col md="2" data-header="Modalidade *">
                  <b-form-radio-group
                    :id="'modalidade_' + index"
                    v-model="item.modalidade"
                    :options="listaModalidades"
                    buttons
                    button-variant="cinza"
                    class="checkbtn-line"
                    name="modalidade"
                    required
                  />
                </b-col>
                <b-col md="2">

                  <b-btn v-if="!item.id && item.descricao !== '' && isEdit" variant="verde" class="btn-40" @click="saveLine(item)">
                    <font-awesome-icon icon="check" />
                  </b-btn>

                  <b-btn v-if="lista.length > 1 && descricao !== ''" variant="light" class="btn-40" @click="excluir(index, item)">
                    <font-awesome-icon icon="minus" />
                  </b-btn>

                  <b-btn v-if="index === (lista.length - 1)" variant="azul" class="btn-40" @click="listaAdd()">
                    <font-awesome-icon icon="plus" />
                  </b-btn>

                </b-col>
              </b-row>
            </perfect-scrollbar>
          </div>
        </div>

      </div>

      <div v-if="isEdit && lista.filter(item => !item.id).length > 0" class="list-group-item list-group-item-warning border-0 mb-3">
        Existem novos itens na lista de Lições que devem ser salvos.
      </div>

      <div class="row align-items-center table-actions">
        <div class="col-md-12">
          <b-btn :disabled="isEdit && lista.filter(item => !item.id).length > 0" type="submit" variant="verde">Salvar</b-btn>
          <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'
import { setTimeout } from 'timers'

export default {
  data () {
    return {
      isValid: true,
      isEdit: false,
      editado: false,
      listLoading: false,
      listaModalidades: [
        {text: 'Ao vivo', value: 'V'},
        {text: 'Plataforma', value: 'P'}
      ]
    }
  },
  computed: {
    ...mapState('planejamentoLicao', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),

    descricao: {
      get () {
        return this.itemSelecionado.descricao
      },
      set (value) {
        if (this.isEdit) {
          this.editado = true
        }
        this.SET_DESCRICAO(value)
      }
    },

    lista: {
      get () {
        return this.itemSelecionado.licaos
      },
      set (value) {
        this.SET_LICAO(value)
      }
    }

  },
  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
    } else {
      this.lista.push({
        descricao: '',
        modalidade: ''
      })
    }
  },
  validations: {
    descricao: {required}
  },
  methods: {
    ...mapActions('planejamentoLicao', ['buscar', 'criar', 'atualizar']),
    ...mapActions('licao', { licaoCriar: 'criar', licaoExcluir: 'excluir' }),
    ...mapMutations('planejamentoLicao', ['SET_DESCRICAO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO', 'SET_LICAO']),
    ...mapMutations('licao', { licaoSelecionado: 'SET_ITEM_SELECIONADO', licaoDescricao: 'SET_DESCRICAO', licaoSelecionadoID: 'SET_ITEM_SELECIONADO_ID', licaoLimpar: 'LIMPAR_ITEM_SELECIONADO', licaoNumero: 'SET_NUMERO' }),

    listaAdd () {
      this.lista.push({descricao: ''})
      setTimeout(() => {
        const campoCriado = document.getElementsByClassName('descricao-input')[this.lista.length - 1]
        campoCriado.focus()
      }, 100)
    },

    limpar (item) {
      item.descricao = ''
    },

    excluir (index, item) {
      if (this.isEdit && !!item.id) {
        EventBus.$emit('chamarModal', {
          resolve: success => {
            this.listLoading = true
            this.licaoSelecionadoID(item.id)
            this.licaoExcluir().then(() => {
              this.lista.splice(index, 1)
              if (!this.editado) {
                delete this.itemSelecionado.descricao
              }
              setTimeout(() => {
                this.atualizar().then(() => (this.listLoading = false))
              }, 100)
            })
          }
        }, `Lição "${item.descricao}" será excluída permanetemente. Deseja prosseguir?`)
      } else {
        this.lista.splice(index, 1)
      }
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/planejamento-licao')
    },

    saveLine (item) {
      this.listLoading = true
      this.licaoSelecionado(item)

      this.licaoCriar()
        .then(() => (this.buscar()
          .then(() => (this.listLoading = false))))
    },

    salvar () {
      let isCamposDescricaoPreenchidos = true
      let isCamposModalidadePreenchidos = true
      this.lista.forEach(item => {
        if (item.descricao.trim().length === 0) {
          isCamposDescricaoPreenchidos = false
        }
        if (item.modalidade === '' || item.modalidade === undefined) {
          isCamposModalidadePreenchidos = false
        }
      })

      if (this.$v.$invalid || !isCamposDescricaoPreenchidos || !isCamposModalidadePreenchidos) {
        if (!isCamposDescricaoPreenchidos) {
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: 'Todos os campos descrição das lições devem ser preenchidos.'
          })
        }
        if (!isCamposModalidadePreenchidos) {
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: 'Todos os campos modalidade das lições devem ser preenchidos.'
          })
        }
        this.isValid = false
        return
      }

      if (this.itemSelecionadoID) {
        if (!this.editado) {
          delete this.itemSelecionado.descricao
        }
        this.atualizar().then(this.voltar).catch(console.error)
      } else {
        this.criar().then(this.voltar).catch(console.error)
      }
    }

  }
}
</script>
