<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-cadastro_de_servico_nome_servico'" for="nome_servico" class="col-form-label">Nome do serviço *</label>
          <input id="nome_servico" v-model="itemSelecionado.descricao" type="text" class="form-control" maxlength="80" required>
        </div>

        <div class="col-md-6">
          <label v-help-hint="'form-cadastro_de_servico_tipo'" for="tipo" class="col-form-label">Tipo do serviço *</label>
          <g-select id="tipoServico"
                    :value="tipo_servico"
                    :options="listaTipoServico"
                    :select="setTipoServico"
                    :class="!isValid && !itemSelecionado.tipo_item ? 'invalid-input' : 'valid-input'"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id"/>

        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-cadastro_de_servico_status'" for="status" class="col-form-label">Status *</label>
          <g-select id="status"
                    :class="!isValid && !itemSelecionado.situacao ? 'invalid-input' : 'valid-input'"
                    :value="situacaoSelecionada"
                    :select="setSituacaoSelecionada"
                    :options="listaDeStatus"
                    label="text"
                    required/>
        </div>

        <div class="col-md-6">
          <label v-help-hint="'form-cadastro_de_servico_plano_conta'" for="planoContaSelecionado" class="col-form-label">Plano de Contas *</label>
          <g-treeselect
            id="planoContaSelecionado"

            :value="planoContaSelecionado"
            :select="setPlanoConta"
            :options="listaPlanosConta"
            :invalid="!isValid && !planoContaSelecionado"
            required
          />

          <div v-if="!isValid && !planoContaSelecionado" class="multiselect-invalid">
            Selecione uma opção
          </div>

        </div>
      </div>

      <div class="form-group pt-2">
        <b-btn type="submit" variant="verde">Salvar</b-btn>
        <b-btn type="submit" variant="verde" @click="salvarESair = true">Salvar e sair</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>

    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import { required } from 'vuelidate/lib/validators'

export default {
  name: 'FormularioCadastroServico',
  data () {
    return {
      isValid: true,
      isEdit: false,

      salvarESair: false,

      listaDeStatus: [
        {text: 'Ativo', value: 'A'},
        {text: 'Inativo ', value: 'I'}
      ],
      tipo_servico: null,
      situacaoSelecionada: null,
      planoContaSelecionado: null
    }
  },
  computed: {
    ...mapState('cadastroServico', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('tipoItem', { listaTipoItemRequisicao: 'lista' }),
    ...mapState('planoConta', {listaPlanosConta: 'selectReceitas'}),

    listaTipoServico: {
      get () {
        return this.listaTipoItemRequisicao
      }
    }
  },
  mounted () {
    this.isValid = true
    this.LIMPAR_ITEM_SELECIONADO()
    this.setSituacaoSelecionada({text: 'Ativo', value: 'A'})
    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar().then(this.montarParamentrosEdicao)
    }

    this.listarCamposSelects()
  },
  validations: {
    itemSelecionado: {
      descricao: {required},
      situacao: {required},
      plano_conta: {required}
    },
    tipo_servico: {required}
  },
  methods: {
    ...mapMutations('cadastroServico', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('cadastroServico', ['buscar', 'criar', 'atualizar']),

    montarParamentrosEdicao () {
      this.itemSelecionado.situacao = this.listaDeStatus.find(situacao => situacao.value === this.itemSelecionado.situacao)
      this.setSituacaoSelecionada(this.itemSelecionado.situacao)
      this.setTipoServico(this.itemSelecionado.tipo_item)
      this.setPlanoConta(this.itemSelecionado.plano_conta)
    },

    listarCamposSelects () {
      this.$store.commit('tipoItem/SET_PAGINA_ATUAL', 1)
      this.$store.commit('planoConta/SET_PAGINA_ATUAL', 1)
      this.$store.commit('tipoItem/SET_FILTROS', {tipo: ['V', 'S', 'M', 'VP32', 'VP48', 'VP64', 'AE', 'R', 'MT', 'MF', 'RM', 'RF', 'VPA']})
      this.$store.dispatch('tipoItem/listar')
      this.$store.dispatch('planoConta/listar')
    },

    setTipoServico (value) {
      this.tipo_servico = value
      this.itemSelecionado.tipo_item = value.id
    },

    setSituacaoSelecionada (value) {
      this.situacaoSelecionada = value
      this.itemSelecionado.situacao = value.value
    },

    setPlanoConta (value) {
      this.planoContaSelecionado = value
      this.itemSelecionado.plano_conta = value.id ? value.id : null
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push(this.$route.matched[0].path)
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.isValid = true
      if (this.itemSelecionadoID) {
        if (this.salvarESair) {
          this.atualizar().then(this.voltar).catch(console.error)
        } else {
          this.atualizar()
            .then(() => {
              this.buscar().then(this.montarParamentrosEdicao)
            })
            .catch(() => {})
        }
      } else {
        if (this.salvarESair) {
          this.criar().then(this.voltar).catch(console.error)
        } else {
          this.criar()
            .then(() => {
              this.LIMPAR_ITEM_SELECIONADO()
              this.setSituacaoSelecionada({text: 'Ativo', value: 'A'}
              )
            })
            .catch(() => {

            })
        }
      }

      this.salvarESair = false
      this.tipo_servico = null
      this.planoContaSelecionado = null
    }
  }
}
</script>
