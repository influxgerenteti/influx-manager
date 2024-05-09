<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-curso_descricao'" for="descricao" class="col-form-label">Descrição *</label>
          <!-- <input id="descricao" v-model="itemSelecionado.descricao" type="text" class="form-control" required maxlength="255"> -->

          <textarea id="descricao" v-model="itemSelecionado.descricao" class="form-control full-textarea" rows="6" maxLength="255" required></textarea>
          <div class="invalid-feedback">Preencha a descrição!</div>

          <span class="text-secondary">Limite de caracteres: {{ 255 - (itemSelecionado.descricao || '').length }}</span>

        </div>
      </div>

      <div class="form-group pt-2">
        <b-btn type="submit" variant="verde">Salvar</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {
  name: 'FormularioMotivoNaoFechamentoConvenio',
  data () {
    return {
      isValid: true,
      isEdit: false
    }
  },
  computed: {
    ...mapState('motivoNaoFechamentoConvenio', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando'])
  },
  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
    }
  },
  validations: {
    itemSelecionado: {
      descricao: {required}
    }
  },
  methods: {
    ...mapMutations('motivoNaoFechamentoConvenio', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('motivoNaoFechamentoConvenio', ['buscar', 'criar', 'atualizar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/motivos-convenio-perdido')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(console.error)
      } else {
        this.criar().then(this.voltar).catch(console.error)
      }
    }
  }
}
</script>
