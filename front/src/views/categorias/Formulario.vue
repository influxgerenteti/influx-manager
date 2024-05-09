<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label for="nome" class="col-form-label">Nome *</label>
          <input id="nome" v-model="nome" type="text" class="form-control" required maxlength="40">
          <div class="invalid-feedback">Preencha o nome!</div>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-8">
          <button type="submit" class="btn btn-verde">Salvar</button>
          <router-link to="/cadastros/categoria" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {

  name: 'FormularioCategoria',
  data () {
    return {
      isValid: true,
      errorMsg: '',
      nome: '',
      isEdit: false
    }
  },
  validations: {
    nome: {
      required
    }
  },
  computed: {
    ...mapState('categorias', ['listaCategorias', 'objCategoria', 'listaModulosPais', 'estaCarregando'])
  },
  watch: {
    objCategoria (value) {
      this.nome = value.nome
    },

    nome (value) {
      this.setNome(value)
    }
  },
  created () {
    if (this.$route.params.id) {
      this.isEdit = true
      this.setCategoriaSelecionada(this.$route.params.id)
      this.getCategoria()
    }
  },
  methods: {
    ...mapActions('categorias', ['getListaCategorias', 'getCategoria', 'criarCategoria', 'atualizarCategoria']),
    ...mapMutations('categorias', ['setCategoria', 'limparCategoria', 'setCategoriaSelecionada', 'setNome', 'SET_ESTA_CARREGANDO']),

    voltar () {
      this.setCategoriaSelecionada(null)
      this.limparCategoria()
      this.$router.push('/cadastros/categoria')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.isEdit) {
        this.atualizarCategoria()
          .then(() => {
            this.voltar()
          })
      } else {
        this.criarCategoria()
          .then(() => {
            this.voltar()
          })
      }
    }
  }
}
</script>
