<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">

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

export default {
  name: 'FormularioBonusClass',
  data () {
    return {
      isValid: true,
      isEdit: false
    }
  },
  computed: {
    ...mapState('bonusClass', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando'])
  },
  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
    }
  },
  validations: {
    // TODO: Adicionar campos obrigatorios
  },
  methods: {
    ...mapMutations('bonusClass', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('bonusClass', ['buscar', 'criar', 'atualizar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/academico/bonus-class')
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
