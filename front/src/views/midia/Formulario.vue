<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="form-group row">
        <div class="col-md-4">
          <label v-help-hint="'form-pesquisa-visibilidade_nome'" for="nome" class="col-form-label">Nome *</label>
          <input id="nome_contato" v-model="itemSelecionado.descricao" type="text" class="form-control" maxlength="150" required>
          <div class="invalid-feedback">Preencha o nome!</div>
        </div>
        <div class="col-md-4">
          <label v-help-hint="'form-pesquisa-visibilidade_tipo_midia'" for="tipo_midia" class="col-form-label">Tipo mídia *</label>
          <g-select id="tipo_midia"
                    v-model="itemSelecionado.tipo"
                    :options="listaTipoMidia"
                    :invalid="!isValid && (!itemSelecionado.tipo||!itemSelecionado.tipo.id)"
                    label="descricao"
                    track-by="value" />
          <div v-if="!isValid && (!itemSelecionado.tipo||!itemSelecionado.tipo.id)" class="multiselect-invalid">
            Selecione uma opção!
          </div>
        </div>

        <div class="col-md-4">
          <label v-help-hint="'form-pesquisa-visibilidade_situacao'" for="situacao" class="col-form-label">Situação *</label>
          <g-select id="situacao"
                    v-model="itemSelecionado.situacao"
                    :options="listaSituacao"
                    :invalid="!isValid && (!itemSelecionado.situacao || !itemSelecionado.situacao.id)"
                    label="descricao"
                    track-by="value" />
          <div v-if="!isValid && (!itemSelecionado.situacao || !itemSelecionado.situacao.id)" class="multiselect-invalid">
            Selecione uma opção!
          </div>
        </div>
      </div>

      <div class="form-group pt-2">
        <b-btn type="submit" variant="verde">Salvar</b-btn>
        <b-btn type="submit" variant="verde" @click="bSalvarESair = true">Salvar e sair</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import { required } from 'vuelidate/lib/validators'

const validSelect = (value) => {
  if (value && value.id !== null) {
    return true
  }

  return false
}

export default {
  name: 'FormularioMidia',
  data () {
    return {
      isValid: true,
      isEdit: false,
      bSalvarESair: false,
      listaSituacao: [
        {id: null, descricao: 'Selecione', value: null},
        {id: 1, descricao: 'Ativo', value: 'A'},
        {id: 2, descricao: 'Inativo', value: 'I'}
      ],
      listaTipoMidia: [
        {id: null, descricao: 'Selecione', value: null},
        {id: 1, descricao: 'Mídia Offline', value: 'MOF'},
        {id: 2, descricao: 'Mídia Online', value: 'MON'},
        {id: 3, descricao: 'Mídia Local', value: 'MLOC'}
      ]
    }
  },
  computed: {
    ...mapState('midia', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando'])
  },
  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar().then(this.montarParamentrosEdicao)
    }
  },
  validations: {
    itemSelecionado: {
      descricao: {required},
      tipo: {validSelect},
      situacao: {validSelect}
    }
  },
  methods: {
    ...mapMutations('midia', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('midia', ['buscar', 'criar', 'atualizar']),

    montarParamentrosEdicao () {
      this.itemSelecionado.tipo = this.listaTipoMidia.find(midia => midia.value === this.itemSelecionado.tipo)
      this.itemSelecionado.situacao = this.listaSituacao.find(situacao => situacao.value === this.itemSelecionado.situacao)
    },

    voltar () {
      this.isEdit = false
      this.isValid = true
      this.bSalvarESair = false

      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/midia')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.itemSelecionadoID) {
        const id = this.itemSelecionadoID

        if (this.bSalvarESair) {
          this.atualizar().then(this.voltar).catch(console.error)
        } else {
          this.atualizar().then(() => {
            this.bSalvarESair = false
            this.LIMPAR_ITEM_SELECIONADO()
            if (id) {
              this.isEdit = true
              this.SET_ITEM_SELECIONADO_ID(id)
              this.buscar().then(this.montarParamentrosEdicao)
            }
          }).catch(console.error)
        }
      } else {
        if (this.bSalvarESair) {
          this.criar().then(this.voltar).catch(console.error)
        } else {
          this.criar().then(() => {
            this.bSalvarESair = false
            this.LIMPAR_ITEM_SELECIONADO()
          }).catch(console.error)
        }
      }
    }
  }
}
</script>
