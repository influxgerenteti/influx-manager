<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-7">
          <label for="descricao_filtro" class="col-form-label">Descrição *</label>
          <input id="descricao_filtro" v-model="descricaoTexto" type="text" class="form-control" maxlength="255" required>
        </div>
      </div>

      <div class="form-group pt-2">
        <b-btn :disabled="isSalvando || (descricaoTexto.trim().length === 0)" type="submit" variant="verde">{{ isSalvando ? 'Salvando...' : 'Salvar' }}</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import { required } from 'vuelidate/lib/validators'

export default {
  name: 'FormularioSegmentoEmpresaConvenio',
  data () {
    return {
      descricaoTexto: '',
      isSalvando: false,
      isValid: true,
      isEdit: false
    }
  },
  computed: {
    ...mapState('segmentoEmpresaConvenio', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando'])
  },
  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
        .then(response => {
          this.descricaoTexto = this.itemSelecionado.descricao
        })
    }
  },
  validations: {
    descricaoTexto: {required}
  },
  methods: {
    ...mapMutations('segmentoEmpresaConvenio', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('segmentoEmpresaConvenio', ['buscar', 'criar', 'atualizar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/cadastros/segmento-empresa-convenio')
    },

    salvar () {
      this.isSalvando = true
      if (this.$v.$invalid) {
        this.isSalvando = false
        this.isValid = false
        return
      }
      this.itemSelecionado.descricao = this.descricaoTexto

      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(console.error)
      } else {
        this.criar().then(this.voltar).catch(console.error)
      }
    }
  }
}
</script>
