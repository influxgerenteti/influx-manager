<template>
  <div class="toast">
    <b-alert
      v-for="toast in listaNotificacoesExibidas"
      :id="'toast-' + toast.id"
      :key="toast.id"
      :show="toast.mostrar === true"
      :variant="toast.class_css && (toast.class_css !== '') ? toast.class_css : 'toast'"
      class="border-0 alert-dismissible fade-in"
    >
      <!-- <b-alert
      v-for="(toast, key) in listaToast"
      :key="key"
      :variant="toast.variante"
      :show="toast.tempo"
      :fade="false"
      :dismissible="false"
      class="d-inline-flex p-0 border-0 alert-dismissible"
      @dismissed="toast.tempo = 0"
    > -->
      <!-- <div :class="toast.icone" class="toast-icone align-self-stretch">
        <font-awesome-icon :icon="toast.icone" />
      </div> -->
      <div class="toast-conteudo">
        <p class="font-weight-bold block-title mb-1">{{ toast.franqueada_nome }}</p>
        <!-- <p class="font-weight-bold mb-1">{{ data | formatarDataHora }}</p> -->
        <p class="mb-0 block-with-text">{{ toast.mensagem }}</p>
      </div>
      <div class="toast-btn-box">
        <!-- <button class="btn">CATCHAU</button> -->
        <!-- <button class="btn btn-vermelho" @click="adiar(index, toast)">Adiar</button>
        <button class="btn btn-black" @click="fechar(index, toast)">Fechar</button> -->
        <b-link class="" href="#" @click="fechar(toast, false)">Adiar</b-link>
        <b-link class="ml-3" href="#" @click="fechar(toast, true)">Marcar como lido</b-link>
      </div>
    </b-alert>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../utils/event-bus'

export default {
  name: 'Toast',
  computed: {
    ...mapState('toast', ['listaToast', 'listaNotificacoesExibidas'])
  },
  watch: {
    listaToast: function (value) {
      if (value.length > 0) {
        value.map(toast => {
          toast.mostrar = true
          let verificaExisteObj = this.listaNotificacoesExibidas.filter((item) => {
            return item.id === toast.id
          })
          if (verificaExisteObj.length === 0) {
            this.listaNotificacoesExibidas.push(toast)
          }
        })
        if (value.length > 0) {
          this.playSound()
        }
      }
    }
  },

  mounted () {
    EventBus.$on('toastNotificacoes', () => {
      setInterval(this.toastTime, 300000)
    })
  },

  updated: function () {
    this.$nextTick(function () {
      this.listaNotificacoesExibidas.map(toast => {
        let selector = document.querySelector('#toast-' + toast.id)
        if (selector) {
          if (selector.classList.contains('fade-in') === true) {
            selector.classList.remove('fade-in')
          }
        }
      })
    })
  },
  methods: {
    ...mapActions('toast', ['toastTime', 'atualizarNotificacao']),
    ...mapMutations('toast', ['SET_ITEM_SELECIONADO_ID', 'SET_LISTA_ITEMS_EXIBIDOS']),

    playSound () {
      const audio = new Audio('/audio/notification01.mp3')
      audio.play()
    },

    fechar (toast, bMarcaComoLido) {
      document.querySelector('#toast-' + toast.id).classList.add('fade-out')
      toast.mostrar = false
      this.SET_ITEM_SELECIONADO_ID(toast.id)
      let parametros = {}
      if (bMarcaComoLido === true) {
        parametros.is_lida = 1
      } else {
        let novaData = new Date()
        novaData.setMinutes(novaData.getMinutes() + 15)
        parametros.data_prorrogacao = novaData.toISOString()
      }
      this.atualizarNotificacao(parametros)
        .then(() => {
          this.SET_ITEM_SELECIONADO_ID(null)
          let index = this.listaNotificacoesExibidas.findIndex(item => item.id === toast.id)
          this.listaNotificacoesExibidas.splice(index, 1)
        })
        .catch((error) => {
          console.info(Object.assign({}, error))
          this.SET_ITEM_SELECIONADO_ID(null)
        })
    }
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

.toast {
  position: fixed;
  z-index: 9999;
  right: 0;
  text-align: end;
  height: 0;
  width: 100%;
  min-width: 30em;
  overflow: visible;
  /* word-break: break-word; */
}

.alert {
  /* position: absolute;
  z-index: 9;
  right: 0; */
  /* color: #495057; */
  top: 0;
  opacity: 1;
  /* display: flex; */
  padding: .5rem 1rem;
  /* padding-bottom: .5rem; */
  width: 0;
  max-width: 40em;
  min-width: 30em;
  left: 50%;
  position: relative;
  transform: translateX(-50%);
  border-radius: 4px;
  pointer-events: auto;
  /* background: #21336d; */
  /* background: #5666c9; */
  background: #4a69c5;
  color: #f5f5f5;
  min-height: 3em;
  -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12), 0 0 6px rgba(0,0,0,.04);
  -moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12), 0 0 6px rgba(0,0,0,.04);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12), 0 0 6px rgba(0,0,0,.04);
  transition: all .3s ease;
}
.alert:first-child {
  margin-top: 1rem;
}

.fade-in {
  opacity: 0;
  top: -20px;
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

.toast-conteudo {
  text-align: left;
  background-color: inherit;
  /* padding: 1rem 1.5rem;
  padding-right: 3rem; */
  flex-grow: 1;
}

.toast-icone {
  padding: 1.5rem 1rem;
  line-height: 0.75em;
  font-size: 16px;
  display: inline-block;
  width: 3.25em;
  text-align: center;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.toast-icone.info {
  background-color: #66ceff;
  color: #0587fa;
}
.toast-icone.check {
  background-color: #66ff79;
  color: #23d160;
}
.toast-icone.times {
  background-color: #ff6674;
  color: #d60332;
}
.toast-icone.exclamation {
  background-color: #ffe066;
  color: #fab005;
}
.toast-icone.lightbulb {
  background-color: #a4b7c1;
  color: #536c79;
}

.toast-btn-box {
  margin-top: 1rem;
  /* margin: auto;
  padding: 1rem;
  padding-right: 1.5rem; */
}

.btn-black {
  background-color: transparent;
  border: 1px solid #f5f5f5;
  color: #f5f5f5;
}
.btn.btn-black:hover {
  /* background-color: #2f2f2f; */
  background-color: #f5f5f5;
  border-color: transparent;
  color: #2f2f2f;
}

.btn-vermelho {
  color: #f5f5f5;
  background-color: #FF3860;
  border-color: transparent;
}
.btn.btn-vermelho:hover {
  background-color: rgb(226, 52, 87);
}

a:not(.btn):not(.link-footer):not(.disable-icon):not(.nav-link), a.btn-link, .btn-link {
  color: #ffffff;
}
a:not(.btn):hover, .btn-link:hover {
  color: #f5f5f5;
}

button.btn:focus {
  outline: none;
  box-shadow: none;
}
.block-title,
.block-with-text {
  overflow: hidden;
  position: relative;
  line-height: 1.2em;
  max-height: 7.1em;
  text-align: justify;
  word-break: break-word;
  margin-right: -1em;
  padding-right: 1em;
  background-color: inherit;
}
.block-title {
  max-height: 1.8rem;
}

.block-title:before,
.block-with-text:before {
  content: '...';
  position: absolute;
  /*right: 0;
  bottom: 0;*/
  right: 7px;
  bottom: 3px;
  width: 1em;
  height: 1em;
  background-color: inherit;
}
.block-title:after,
.block-with-text:after {
  content: '';
  position: absolute;
  right: 7px;
  width: 1em;
  height: 1em;
  margin-top: 0.2em;
  background-color: inherit;
}
</style>
