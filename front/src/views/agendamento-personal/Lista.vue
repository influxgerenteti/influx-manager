<template>
  <div class="animated fadeIn">

    <button @click="abrirModal" class="btn btn-green">Abrir</button>

    <modal-formulario ref="modalAgendaPersonal"/>
    
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'
import ModalFormulario from './ModalFormulario.vue'

export default {

  name: 'AgendamentoPersonal',
  components: {
    ModalFormulario
  },

  data () {
    return {
    }
  },

  computed: {
    // ...mapState('agendamento-personal', ['permissoes'])
  },

  mounted () {
    this.abrirModal()
  },

  methods: {
    abrirModal() {
      this.$refs.modalAgendaPersonal.visible = true;
    }
  }
}
</script>

<style scoped>

</style>
