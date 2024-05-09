<template>
  <g-modal id="defaultModal" ref="defaultModal" size="sm" hide-header-close hide-footer no-close-on-backdrop>
    <div class="p-3">
      <div class="d-block text-center">
        <p>{{ mensagem }}</p>
      </div>
      <div class="d-flex justify-content-center">
        <template v-if="resolve === null && reject === null">
          <b-btn class="mt-3" variant="outline-info" block @click="hideModal()">Ok</b-btn>
        </template>
        <template v-else>
          <b-btn class="mt-3 mr-3" variant="outline-success" block @click="resolveModal()">{{btnConfirma}}</b-btn>
          <b-btn class="mt-3" variant="outline-danger" block @click="rejectModal()">{{btnCancela}}</b-btn>
        </template>
      </div>
    </div>
  </g-modal>
</template>

<script>
import EventBus from '../utils/event-bus'

export default {
  name: 'Modal',
  data () {
    return {
      resolve: null,
      reject: null,
      mensagemOriginal: 'Deseja confirmar ação?',
      mensagem: 'Deseja confirmar ação?',
      btnConfirma: 'Confirmar',
      btnCancela: 'Cancelar'
    }
  },
  created () {
    EventBus.$on('chamarModal', ({resolve, reject}, mensagem, btnConfirma, btnCancela) => {
      this.resolve = resolve || null
      this.reject = reject || null
      this.mensagem = mensagem || this.mensagemOriginal
      if (this.$refs.defaultModal) {
        this.$refs.defaultModal.show()
      }
      this.btnConfirma = btnConfirma ? btnConfirma : 'Confirmar'
      this.btnCancela = btnCancela ? btnCancela : 'Cancelar'     
    })
  },
  methods: {
    hideModal () {
      this.$refs.defaultModal.hide()
    },

    rejectModal () {
      this.$refs.defaultModal.hide()
      if (this.reject) {
        this.reject('ERRO')
      }
    },

    resolveModal () {
      this.$refs.defaultModal.hide()
      if (this.resolve) {
        this.resolve('SUCESSO')
      }
    }

    /* testAdd () {
      EventBus.$emit(
        'chamarModal',
        {
          resolve: success => console.log(success),
          reject: error => console.log(error)
        }
      )
    } */

  }
}
</script>
<style scoped>
#defaultModal {
  z-index: 1100 !important;
}

.btn-outline-success {
  color: #85d017;
  background-color: transparent;
  background-image: none;
  border-color: #85d017;
}
.btn-outline-success:hover {
  color: #fff;
  background-color: #85d017;
  border-color: #85d017;
}
.btn-outline-success:not(:disabled):not(.disabled):active, .btn-outline-success:not(:disabled):not(.disabled).active, .show > .btn-outline-success.dropdown-toggle {
  color: #fff;
  background-color: #6eac14;
  border-color: #6eac14;
}
.btn-outline-success:not(:disabled):not(.disabled):active:focus, .btn-outline-success:not(:disabled):not(.disabled).active:focus, .show > .btn-outline-success.dropdown-toggle:focus,
.btn-outline-success:focus, .btn-outline-success.focus {
  box-shadow: 0 0 0 0.2rem rgba(146, 189, 77, 0.5);
}

.btn-outline-danger {
  color: #FF3860;
  background-color: transparent;
  background-image: none;
  border-color: #FF3860;
}
.btn-outline-danger:hover {
  color: #fff;
  background-color: #FF3860;
  border-color: #FF3860;
}
.btn-outline-danger:not(:disabled):not(.disabled):active, .btn-outline-danger:not(:disabled):not(.disabled).active, .show > .btn-outline-danger.dropdown-toggle {
  color: #fff;
  background-color: #f86b8e;
  border-color: #f86b8e;
}
.btn-outline-danger:not(:disabled):not(.disabled):active:focus, .btn-outline-danger:not(:disabled):not(.disabled).active:focus, .show > .btn-outline-danger.dropdown-toggle:focus,
.btn-outline-danger:focus, .btn-outline-danger.focus {
  box-shadow: 0 0 0 0.2rem rgba(248, 107, 138, 0.5);
}

p {
  word-wrap: break-word;
}

</style>
