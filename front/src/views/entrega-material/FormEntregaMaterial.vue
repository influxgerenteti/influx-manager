<template>
  <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="realizaEntregaMaterial()">
    <!-- <div class="form-group row">
      <div class="col-md-12">
        <label for="usuario_entrega_temporario" class="col-form-label">Usuário *</label>
        <div class="input-group">
          <g-select id="usuario_entrega_temporario"
          :select="setUsuarioTemporario"
          :value="usuario_entrega_temporario"
          :options="listaUsuariosSelect"
          class="multiselect-truncate"
          label="nome"
          track-by="id"
          />
        </div>
      </div>
    </div> -->

    <div class="form-group row mb-3">
      <div class="col-md-12">
        <label for="data_entrega_material" class="col-form-label">Data de entrega *</label>
        <date-picker id="data_entrega_material" v-model="data_entrega_material_temporario" required />
        <div v-if="!isValid && $v.data_entrega_material_temporario.$invalid" class="multiselect-invalid">
          Selecione uma data!
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-center">
      <b-btn :disabled="enviandoDataEntrega" type="submit" variant="verde">Confirmar</b-btn>
      <b-btn type="button" variant="link" @click="finalizar()">Cancelar</b-btn>
    </div>
  </form>
</template>
<script>
import {required} from 'vuelidate/lib/validators'
import DatePicker from '../../components/fields/DatePicker'
import moment from 'moment'

export default {
  name: 'FormEntregaMaterial',
  components: {
    DatePicker
  },

  data () {
    return {
      isValid: true,
      usuario_entrega_temporario: null,
      data_entrega_material_temporario: null,
      enviandoDataEntrega: false,
      alterarSituacaoEntrega: '',
      itemArrayOuObjeto: {},
      listaCallback: {
        salvar: null,
        cancelar: null
      }
    }
  },
  validations: {
    data_entrega_material_temporario: {required}
  },
  methods: {

    realizaEntregaMaterial () {
      this.enviandoDataEntrega = true
      if (this.$v.$invalid) {
        this.isValid = false
        this.enviandoDataEntrega = false
        return
      }

      this.enviandoDataEntrega = false
      if (typeof (this.itemArrayOuObjeto) === 'object') {
        this.listaCallback.salvar(moment(this.data_entrega_material_temporario).format('DD/MM/YYYY'), this.alterarSituacaoEntrega, this.usuario_entrega_temporario, this.itemArrayOuObjeto.id)
      } else {
        this.listaCallback.salvar(moment(this.data_entrega_material_temporario).format('DD/MM/YYYY'), this.alterarSituacaoEntrega, this.usuario_entrega_temporario)
      }

      this.isValid = true
      this.usuario_entrega_temporario = null
      this.data_entrega_material_temporario = null
   
     

    },

    finalizar () {
      this.isValid = true
      this.usuario_entrega_temporario = null
      this.data_entrega_material_temporario = null
      this.listaCallback.cancelar()
    },

  

    setUsuarioTemporario (value) {
      this.usuario_entrega_temporario = value
    }

  }
}
</script>
