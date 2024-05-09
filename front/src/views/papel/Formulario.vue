<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="submit()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="form-group">
        <label for="descricao_papel" class="form-label">Nome do perfil *</label>
        <input id="descricao_papel" v-model="itemSelecionado.descricao" type="text" class="form-control" maxlength="255" required>
      </div>

      <div>
        <b-btn :disabled="submiting" type="submit" variant="verde">{{ submiting === true ? 'Salvando...' : 'Salvar' }}</b-btn>
        <b-btn variant="link" @click="$emit('cancel')">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {
  name: 'FormularioPapel',
  data () {
    return {
      submiting: false,
      isValid: true,
      isEdit: false
    }
  },

  computed: {
    ...mapState('papel', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando'])
  },

  created () {
    this.$on('modal-papel:abrir', (id = null) => {
      this.LIMPAR_ITEM_SELECIONADO()

      if (id) {
        this.isEdit = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscarPapel(id)
      }
    })
  },

  validations: {
    itemSelecionado: {
      descricao: {required}
    }
  },

  methods: {
    ...mapMutations('papel', ['LIMPAR_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID']),
    ...mapActions('papel', ['buscarPapel', 'criar', 'atualizar']),

    submit () {
      this.isValid = false

      if (this.$v.$invalid) {
        return
      }

      this.submiting = true

      if (this.itemSelecionadoID) {
        this.atualizar()
          .then(() => {
            this.$emit('update')
          })
          .catch(console.error)
          .finally(() => {
            this.submiting = false
          })
      } else {
        this.criar()
          .then((id) => {
            this.$emit('update', id)
          })
          .catch(console.error)
          .finally(() => {
            this.submiting = false
          })
      }
    }
  }
}
</script>
