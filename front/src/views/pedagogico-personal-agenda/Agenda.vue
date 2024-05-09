<template>
  
<vue-cal :time="false" hide-weekends />
</template>
<style>
#flex-container {
  width: 100%;
  height: 100%;
  grid-template-columns: repeat(3, 1fr);

  display: grid;
}
.item {
  height:auto;
  /* border: 1px solid blue; */
  margin: 5px 5px;
  min-width: 150px;
  text-align: center;
  background-color: #ffffff;
}
.categoria-title{
    display: flex;
    height: 46px;
    width: 100%;
    background-color: #e4e7e5;
    align-items: center;
    justify-content: center;
}
.relatorio-item{
  padding-left: 15px;
  padding-right: 15px;
  padding-top: 10px;
  display: flex;
  text-align: center;
  width: 100%;
  justify-content :space-between;

}
.relatorio-span{
  height: 30px;  

}
.relatorio-icon{
    height: 30px;  
    width: 30px;
}
</style>

<script>
import {required} from 'vuelidate/lib/validators'
import {mapState, mapMutations, mapActions} from 'vuex'
import {dateToString, stringToISODate} from '../../utils/date'
import moment from 'moment'
import VueCal from 'vue-cal'
import 'vue-cal/dist/i18n/pt-br.cjs.js'
import 'vue-cal/dist/vuecal.css'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'


export default {
  name: 'AgendaPersonal',
  components: {
    VueCal
  },
  data () {
    return {
      isLoading: false,
      count: 0,
      items:[
        { title:"PEDAGÓGICO",
          relatorios:[
          {nome:"Class Record"},{nome:"Alunos - Situação"},{nome:"Turmas"}
        ]},
        { title:"COMERCIAL",
          relatorios:[
         
        ]},
        { title:"FINANCEIRO",
          relatorios:[
          {nome:"Contratos - Situação"},{nome:"Contratos - Mala Direta"},{nome:"Cheques"},{nome:"Contas a Pagar"},{nome:"Inadimplência"},{nome:"Balancete"},{nome:"Contas a Receber - Títulos"}
        ]},
        { title:"MARKETING",
          relatorios:[
          
        ]},
        { title:"GERAIS",
          relatorios:[
          
        ]},        
        { title:"RH",
          relatorios:[
          {nome:"Funcionarios"}
        ]}
        
       
      ]
    }
  },
  computed: {
   
  },
  mounted () {
   
  },
  validations: {
    
  },
  methods: {
  
  }
}
</script>


</style>
