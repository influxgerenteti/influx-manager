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

    <b-modal id="formularioOcorrenciaAcademica" ref="formularioOcorrenciaAcademica" v-model="visibleFormularioOcorrenciaAcademica" size="lg" centered no-close-on-backdrop hide-header hide-footer>
      <modal-ocorrencia ref="parcelamentoOperadoraCartaoComponente" @hide="visibleFormularioOcorrenciaAcademica = false"/>
    </b-modal>
  </div>
</template>

<script>

export default {
  name: 'FormularioOcorrenciaAcademica',
  data () {
    return {
      isValid: true,
      isEdit: false
    }
  },
  methods: {
    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/academico/ocorrencia-academica')
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
