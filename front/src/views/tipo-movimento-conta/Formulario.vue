<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-tipo-movimento-conta_descricao'" for="descricao" class="col-form-label">Descrição *</label>
          <input id="descricao" v-model="descricao" type="text" class="form-control" placeholder="" required maxlength="30">
          <div class="invalid-feedback">Informe a descrição!</div>
        </div>
        <div class="col-md-6">
          <label v-help-hint="'form-tipo-movimento-conta_operacao'" class="col-form-label" for="tipo_operacao">Operação *</label>
          <select id="tipo_operacao" v-model="tipo_operacao" class="custom-select form-control" required>
            <option value>Nenhum</option>
            <option value="C">Crédito</option>
            <option value="D">Débito</option>
            <option value="T">Transferência</option>
          </select>
          <div class="invalid-feedback">Selecione uma opção!</div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-8">
          <button type="submit" class="btn btn-verde">Salvar</button>
          <router-link to="/configuracoes/tipo-movimento-conta" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {

  name: 'FormularioTipoMovimentoConta',
  data () {
    return {
      isValid: true,
      errorMsg: '',
      descricao: '',
      tipo_operacao: '',
      situacao: 'A',
      reservado: 0,
      isEdit: false
    }
  },
  validations: {
    descricao: {required},
    tipo_operacao: {required}
  },
  computed: {
    ...mapState('tipoMovimentoConta', ['lista', 'item', 'estaCarregando'])
  },
  watch: {
    item (value) {
      this.descricao = value.descricao
      this.tipo_operacao = value.tipo_operacao
      this.reservado = value.reservado
      this.situacao = value.situacao === 'A'
      this.SET_ESTA_CARREGANDO(false)
    },

    descricao (value) {
      this.SET_DESCRICAO(value)
    },

    tipo_operacao (value) {
      this.SET_OPERACAO(value)
    },

    reservado (value) {
      this.SET_RESERVADO(value)
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
    ...mapActions('tipoMovimentoConta', ['getLista', 'getItem', 'criar', 'atualizar']),
    ...mapMutations('tipoMovimentoConta', ['SET_ITEM', 'LIMPAR_ITEM', 'SET_SELECIONADO', 'SET_DESCRICAO', 'SET_OPERACAO', 'SET_RESERVADO', 'SET_SITUACAO', 'SET_ESTA_CARREGANDO']),

    voltar () {
      this.LIMPAR_ITEM()
      this.$router.push('/configuracoes/tipo-movimento-conta')
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
