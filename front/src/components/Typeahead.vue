<template>
  <div class="typeahead-container">
    <input :id="id"
           :required="required"
           :masked="masked"
           v-model="query"
           :class="invalid === true ? 'is-invalid' : 'valid-typeahead'"
           :maxlength="maxLength"
           class="form-control typeahead"
           type="text"
           selectedKey="selected-key"
           placeholder="Buscar"
           autocomplete="off"
           selectedObject="selectedObject"
           @focus="inputHasFocus = true"
           @keydown.down="down"
           @keydown.up="up"
           @keydown.enter.prevent="prevHit"
           @keydown.esc="resetSelection"
           @blur="blurCallback"
           @input="onInput()">

    <div class="icon">
      <i v-if="loading" class="fa fa-spinner fa-spin"></i>
      <template v-else>
        <i v-show="isEmpty" class="fa fa-search"></i>
        <i v-show="isDirty" class="fa fa-times" @click="resetSelection"></i>
      </template>
    </div>

    <div v-show="inputHasFocus === true && (isDirty || hasItems)" class="resultContainer">
      <ul class="resultList">
        <li v-if="loading && !hasItems">
          <span>Carregando...</span>
        </li>

        <li v-if="!loading && !hasItems">
          <span>Nenhum resultado encontrado.</span>
        </li>

        <li v-for="(item, $item) in items" :class="activeClass($item)" :key="$item" @mousedown="hit" @mousemove="setActive($item)">
          <template v-if="typeof keyName !== 'string'">
            <span>{{ outKeys(item) }}</span>
          </template>
          <template v-else>
            <span v-if="keyName === 'cnpj_cpf'">{{ item.cnpj_cpf }} - {{ item.nome_contato }}</span>
            <span v-else>{{ parseKeys(item) }}</span>
          </template>
        </li>
      </ul>

      <div v-if="isDirty && actions !== undefined" class="actions d-flex">
        <b-btn v-for="(button, key) in actions" :key="key" :variant="button.variant || 'primary'" @mousedown="button.action(query), resetSelection()">
          <font-awesome-icon v-if="button.icon" :icon="button.icon" />
          {{ button.text }}
        </b-btn>
      </div>
    </div>
  </div>
</template>

<script>
import VueTypeahead from 'vue-typeahead'
import {mapState} from 'vuex'
// import host from '../utils/host-url'

var host = process.env.VUE_APP_HOST;


export default {
  name: 'Typeahead',
  extends: VueTypeahead,
  props: {
    sourcePath: {
      type: String,
      default: ''
    },

    itemHit: {
      type: Function,
      default: null
    },

    minChars: {
      type: Number,
      default: 1
    },

    id: {
      type: String,
      default: ''
    },

    limit: {
      type: Number,
      default: 25,
      required: false
    },

    keyName: {
      type: [String, Array],
      default: 'cnpj_cpf'
    },

    invalid: {
      type: Boolean,
      required: false,
      default: false
    },

    selectedKey: {
      type: [String, Array],
      default: ''
    },

    extraParam: {
      type: [String, Boolean, Object, Array],
      default: undefined,
      required: false
    },

    masked: {
      type: Boolean,
      default: false
    },

    maxLength: {
      type: String,
      default: undefined,
      required: false
    },

    required: {
      type: Boolean,
      default: false,
      required: false
    },

    additionalData: {
      type: Object,
      default: undefined,
      required: false
    },

    actions: {
      type: Array,
      default: undefined,
      required: false
    },

    tempo: {
      type: Number,
      default: 600,
      required: false
    }
  },

  data () {
    
    return {
      src: this. montaUrl(this.sourcePath),
      selectFirst: true,
      queryParamName: false,
      selectedObject: null,
      inputHasFocus: false,
      timer: null
    }
  },

  computed: {
    ...mapState('root', ['franqueadaSelecionada', 'usuarioLogado']),

    data: {
      get () {
        const obj = {
          franqueada: this.selectFranqueada(this.franqueadaSelecionada, this.usuarioLogado)
        }

        if (this.additionalData) {
          obj[this.additionalData.name] = this.additionalData.data
        }

        return obj
      }
    }
  },

  methods: {
    selectFranqueada (franqueadaSelecionada, usuario) {
      if (franqueadaSelecionada) {
        return franqueadaSelecionada
      }

      if (usuario) {
        return usuario.franqueadaSelecionada || usuario.franqueada_padrao.id
      }

      return null
    },

    prevHit () {
      if (this.items.length > 0) {
        this.hit()
      } else {
        // TODO: corrigir funcionalidade
        /* if (typeof this.itemHit === 'function') {
          this.itemHit(null)
        } */
      }
    },

    onHit (item) {
      if (!this.selectedObject && item !== undefined && this.selectedObject !== item) {
        this.selectedObject = item
      }

      if (typeof this.selectedKey === 'object') {
        this.query = ''
        this.selectedKey.map(key => {
          if (this.query) {
            this.query += ' - '
          }
          this.query += this.outSpan(item, key)
        })
      } else {
        this.query = typeof this.keyName !== 'string' ? this.outSpan(item, this.selectedKey) : this.parseKeys(item)
      }

      if (this.masked) {
        this.query = this.query.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
      }

      this.items = []

      if (typeof this.itemHit === 'function') {
        this.itemHit(item, this.extraParam)
      }
    },

    prepareResponseData (data) {
      return data.corpo
    },

    resetSelection () {
  
      this.reset()
      if (typeof this.itemHit === 'function') {
        this.itemHit(null)
      }
    },

    blurCallback () {
      this.inputHasFocus = false
      this.$emit('blur')
    },

    parseKeys (item) {
      let display = item

      const chaves = this.keyName.split('.')
      chaves.map(chave => {
        if (display[chave]) {
          display = display[chave]
        }
      })

      return display
    },

    outKeys (item, selected) {
      let span = ''

      this.keyName.map(key => {
        span += ' - ' + this.outSpan(item, key)
      })

      return span.replace(' - undefined', '').replace(/^( - )/g, '')
    },

    outSpan (item, key) {
      let span = item

      const partes = key.split('.')
      partes.map(parte => {
        if (span[parte]) {
          span = span[parte]
        }
      })

      return typeof span !== 'object' ? span : undefined
    },

    onInput () {
      if (this.tempo) {
        clearTimeout(this.timer)

        this.timer = setTimeout(this.input, this.tempo)
      } else {
        this.input()
      }
    },

    input () {
      if (this.query === '') {
        this.resetSelection()
      } else {
        this.update()
      }

      this.$emit('input', this.query)
    },
    montaUrl(url){
      // console.log('DEBUGX')
      // console.log(host)
      return `${host}${url}/`    
    }
  }
}
</script>

<style scoped>
.typeahead-container {
  position: relative;
}

.icon {
  position: absolute;
  top: 0;
  right: 0;
  cursor: pointer;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.resultContainer {
  position: absolute;
  padding: 3px;
  margin: 2px 0;
  width: 100%;
  background: #fff;
  border-radius: 3px;
  box-shadow: 0px 1px 8px #aaa;
  z-index: 2;
}

.resultList {
  list-style-type: none;
  padding: 3px;
  margin: 2px 0;
  width: 100%;
  max-height: 190px;
  overflow-y: auto;
}

.resultList li:not(.actions) {
  color: #888;
  padding: 2px 5px;
}

.resultList li.active {
  color: #666;
  background: #eaeaea;
}

.actions {
  margin-top: 5px;
  padding-top: 5px;
  border-top: 1px solid #EBECF0;
}

.was-validated .valid-typeahead {
  -webkit-box-shadow: inset 0 0 0 1px #23d160;
  -moz-box-shadow: inset 0 0 0 1px #23d160;
  box-shadow: inset 0 0 0 1px #23d160;
  background-color: #fff;
}

</style>
