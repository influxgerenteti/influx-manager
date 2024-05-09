<template>
    <form class="">
        <b-row>
          <b-col md="6" class="celula">
            <label  :for="`valor_final`" class="col-form-label">Digite o Valor Final</label>
            <vue-numeric :id="`valor_final`" v-model="valor" :disabled="false"  :precision="2" :max="9999999.99"  separator="." class="form-control text-right"  />
          </b-col>

          <b-col md="6" class="celula">
            <label  :for="`valor_desconto`" >Valor do Desconto Aplicado</label>
            <span class="desconto text-right"  > R$ {{getDesconto()}}</span>
            <!-- <vue-numeric :id="`valor_desconto`" v-model="desconto" :disabled="true" :precision="2" :max="9999999.99"  separator="." class="form-control text-right"  /> -->
          </b-col>
          
        </b-row>

        <b-row>
          <b-col md="12" class="celula">
            <label  :for="`motivo_desconto`" class="col-form-label">Motivo Desconto * </label><span>(min 10 caracteres)</span>
            <textarea id="motivo_desconto" v-model="motivo"  class="form-control" maxlength="500" placeholder="Descreva aqui o motivo do desconto "></textarea>
        
   
          </b-col>
        </b-row>
      <div class="botoes ">
        <button :id="`cancelar`" type="button" :disabled="false" @click="cancelar" class="btn btn-gray btn-block text-uppercase my-1"  >Cancelar</button>
        <button :id="`aplicar`" type="button" :disabled="!validaForm()" @click="aplicarDesconto" class="btn btn-blue btn-block text-uppercase my-1"  >Aplicar</button>
   
      </div>
    </form>
  </template>

<script>
import {mapState} from 'vuex'
import { DialogModalBus } from '../../eventBus';
import { currencyToNumber, numberToCurrency } from "../../utils/number";

export default {
  props: {
        
  },

  data () {
    return {        
        valor: 0,
        desconto:0,
        motivo:''       
    }
  },
  watch: {
    valor: function(val, oldVal) {
        console.log(val)
            if(val > this.$attrs.valor ){
                this.valor = this.$attrs.valor
            }
            this.desconto = this.$attrs.valor - val ; 
           
        },
    motivo: function(val, oldVal) {
        console.log(val)
    }
    },
  computed: {
    ...mapState('contasReceber', ['titulo']),
  },
  mounted () {
    // this.xvalor = this.titulo.valor_original;
    
    
    this.valor = this.$attrs.valor - this.$attrs.desconto;
    this.desconto = this.$attrs.desconto;
    this.motivo = this.$attrs.motivo;
    console.log("valor recebido:",this.valor )
  
  },

  

  methods: {
    validaForm(){
      if(this.motivo && this.motivo.length > 10){
        return true
      }
      return false;
    },
   
    aplicarDesconto(){
      
      DialogModalBus.$emit('onAplicar', { motivo: this.motivo,desconto:this.desconto,valor:this.valor })
      DialogModalBus.$emit('onClose')
    },
    cancelar(){
            
      DialogModalBus.$emit('onClose')
    },
    getDesconto(){
      return numberToCurrency(this.desconto)
    }
  }
}
</script>
<style>
    .desconto{
      padding: 0 5px 8px 0;
      font-size: 1.2em;
      font-weight: 700;
    }
    .botoes{
      margin-top: 15px;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      gap: 30px;
    }
</style>