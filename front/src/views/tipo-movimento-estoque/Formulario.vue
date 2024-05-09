<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-tipo-movimento-estoque_descricao'" v for="descricao" class="col-form-label">Descrição *</label>
          <input id="descricao" v-model="descricao" type="text" class="form-control" placeholder="Nome" maxlength="35" required>
          <div class="invalid-feedback">Informe a descrição!</div>
        </div>
        <div class="col-md-6">
          <label v-help-hint="'form-tipo-movimento-estoque_tipo_movimento'" class="col-form-label" for="tipo_movimento">Tipo de Movimento *</label>
          <select id="tipo_movimento" v-model="tipo_movimento" class="custom-select form-control" required>
            <option value>Nenhum</option>
            <option value="E">Entrada</option>
            <option value="S">Saída</option>
          </select>
          <div class="invalid-feedback">Selecione uma opção!</div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-verde">Salvar</button>
          <router-link to="/configuracoes/tipo-movimento-estoque" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {

  name: 'FormularioTipoMovimentoEstoque',
  data () {
    return {
      isValid: true,
      errorMsg: '',
      descricao: '',
      situacao: 'A',
      tipo_movimento: '',
      isEdit: false
    }
  },
  validations: {
    descricao: {required},
    tipo_movimento: {required}
  },
  computed: {
    ...mapState('tipoMovimentoEstoque', ['lista', 'item', 'estaCarregando'])
  },
  watch: {
    item (value) {
      this.descricao = value.descricao
      this.tipo_movimento = value.tipo_movimento
      this.situacao = value.situacao === 'A'
      this.SET_ESTA_CARREGANDO(false)
    },

    descricao (value) {
      this.SET_DESCRICAO(value)
    },

    tipo_movimento (value) {
      this.SET_TIPO_MOVIMENTO(value)
    },

    situacao (value) {
      this.SET_SITUACAO(value ? 'A' : 'I')
    }
  },
  created () {
    if (this.$route.params.id) {
      this.isEdit = true
      this.SET_SELECIONADO(this.$route.params.id)
      this.getItem()
    }
  },
  methods: {
    ...mapActions('tipoMovimentoEstoque', ['getLista', 'getItem', 'criar', 'atualizar']),
    ...mapMutations('tipoMovimentoEstoque', ['SET_ITEM', 'LIMPAR_ITEM', 'SET_SELECIONADO', 'SET_DESCRICAO', 'SET_TIPO_MOVIMENTO', 'SET_SITUACAO', 'SET_ESTA_CARREGANDO']),

    voltar () {
      this.LIMPAR_ITEM()
      this.$router.push('/configuracoes/tipo-movimento-estoque')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.isEdit) {
        this.atualizar()
          .then(() => {
            this.voltar()
          })
      } else {
        this.criar()
          .then(() => {
            this.voltar()
          })
      }
    }
  }
}
</script>
