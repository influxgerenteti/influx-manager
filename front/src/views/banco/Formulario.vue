<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-banco_codigo'" for="codigo" class="col-form-label">Código *</label>
          <input id="codigo" v-model="codigo" type="text" class="form-control" required maxlength="3">
          <div class="invalid-feedback">Preencha o código!</div>
        </div>

        <div class="col-md-6">
          <label v-help-hint="'form-banco_descricao'" for="descricao" class="col-form-label">Descrição *</label>
          <input id="descricao" v-model="descricao" type="text" class="form-control" required maxlength="100">
          <div class="invalid-feedback">Preencha a descrição!</div>
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
  data () {
    return {
      isValid: true,
      isEdit: false
    }
  },
  computed: {
    ...mapState('banco', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),

    codigo: {
      get () {
        return this.itemSelecionado.codigo
      },
      set (value) {
        this.SET_CODIGO(value)
      }
    },
    descricao: {
      get () {
        return this.itemSelecionado.descricao
      },
      set (value) {
        this.SET_DESCRICAO(value)
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
    }
  },
  validations: {
    codigo: {required},
    descricao: {required}
  },
  methods: {
    ...mapMutations('banco', ['SET_CODIGO', 'SET_DESCRICAO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('banco', ['buscar', 'criar', 'atualizar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/banco')
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
