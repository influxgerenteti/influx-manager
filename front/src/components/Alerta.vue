<template>
  <div class="alertas">
    <b-alert
      v-for="(alerta, key) in listaAlertas"
      :id="'alert-id-' + key"
      :key="key"
      :variant="alerta.variante"
      :show="alerta.tempo"
      fade dismissible
      class="d-inline-flex p-0 border-0 alert-dismissible fade-in"
      @dismissed="alerta.tempo = 0"
    >
      <!-- @dismissed="alerta.tempo = 0" -->
      <div :class="alerta.icone" class="alerta-icone align-self-stretch">
        <font-awesome-icon :icon="alerta.icone" />
      </div>
      <div class="alerta-conteudo">
        <p class="font-weight-bold mb-1">{{ alerta.titulo }}</p>
        <p class="mb-0" v-html="alerta.mensagem"></p>
        <ul v-if="alerta.informacaoAdicional">
          <li v-for="(info, index) in alerta.informacaoAdicional" :key="index">{{ info }}</li>
        </ul>
      </div>
    </b-alert>
  </div>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import EventBus from '../utils/event-bus'

export default {
  name: 'Alerta',
  computed: {
    ...mapState('alertas', ['listaAlertas'])
  },

  created () {
    EventBus.$on('criarAlerta', alerta => {
      const id = this.listaAlertas.length

      this.criarAlerta(alerta)
      setTimeout(() => {
        if (document.querySelector('#alert-id-' + id)) {
          document.querySelector('#alert-id-' + id).classList.remove('fade-in')
        }
      }, 100)
    })
  },

  destroyed () {
    EventBus.$off('criarAlerta')
  },

  methods: {
    ...mapActions('alertas', ['criarAlerta']),

    testAdd () {
      EventBus.$emit('criarAlerta', {
        tipo: 'S',
        mensagem: 'Mensagem de texto para testar uma mensagem de texto para testar uma mensagem de texto.'
      })
    }

    /* teste () {
      document.querySelector('#alert-id-' + index).classList.add('fade-out')
      setTimeout(() => {
        toast.close = 0
      }, 170)
    } */
  }
}
</script>

<style scoped>
/*
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
*/

.alertas {
  top: 0;
  opacity: 1;

  position: fixed;
  z-index: 9999;
  right: 0;
  text-align: end;
  width: 0;
  height: 0;
  max-width: 40em;
  min-width: 30em;
  overflow: visible;
}

.alert {
  /* position: absolute;
  z-index: 9;
  right: 0; */
  /* color: #495057; */
  width: 0;
  max-width: 40em;
  min-width: 30em;
  /* -webkit-box-shadow: 0 1.5px 0px 1px #e4e4e4, 0 0 0 1px #e4e4e4;
  -moz-box-shadow: 0 1.5px 0px 1px #e4e4e4, 0 0 0 1px #e4e4e4;
  box-shadow: 0 1.5px 0px 1px #e4e4e4, 0 0 0 1px #e4e4e4; */
  transition: all .3s ease;
  left: 0;
}

.fade-in {
  opacity: 0;
  left: 50%;
}

.alert.fade-out {
  opacity: 0;
  transition: opacity .15s linear;
}

.alert.alert-info {
  background-color: #dbedff;
}

.alert.alert-success {
  background-color: #d6ffd8;
}

.alert.alert-danger {
  background-color: #ffdbdb;
}

.alert.alert-warning {
  background-color: #fff9db;
}

.alert.alert-light {
  background-color: #ececec;
  color: #495057;
}

.alerta-conteudo {
  text-align: left;
  padding: 1rem 1.5rem;
  padding-right: 3rem;
}

.alerta-icone {
  padding: 1.5rem 1rem;
  line-height: 0.75em;
  font-size: 16px;
  display: inline-block;
  width: 3.25em;
  text-align: center;
}

.alerta-icone.info {
  background-color: #66ceff;
  color: #0587fa;
}
.alerta-icone.check {
  background-color: #66ff79;
  color: #23d160;
}
.alerta-icone.times {
  background-color: #ff6674;
  color: #d60332;
}
.alerta-icone.exclamation {
  background-color: #ffe066;
  color: #fab005;
}
.alerta-icone.lightbulb {
  background-color: #a4b7c1;
  color: #536c79;
}
</style>
