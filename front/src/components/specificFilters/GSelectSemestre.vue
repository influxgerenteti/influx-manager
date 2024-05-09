<template>
  <div>
    <multiselect
      :id="id"
      v-model="selectedItem"
      label="descricao"
      track-by="descricao"
      :allow-empty="true"
      :custom-label="customLabel"
      :disabled="disabled"
      :required="required"
      :options="computedValue"
      :class="invalid === true ? 'invalid-input' : 'valid-input'"
      v-bind="override"
      :placeholder="placeholder"
      :multiple="false || multiTag"
      :taggable="false || multiTag"
      selected-label=""
      select-label=""
      deselect-label=""
      tag-placeholder=""
      class="multiselect-truncate"
      @select="selectItem"
      @remove="selectItem"
    >
      <template slot="noResult">Nenhum resultado.</template>
      <template slot="noOptions">Selecione um livro.</template>
    </multiselect>
  </div>
</template>

<script>
  import Request from '../../utils/request'
export default {
  name: 'GSelectSemestre',
  data() {
    return {
      options : [],
      selectedItem : null
    }
  },
  props: {
    id: {
      type: String,
      required: false,
      default: null
    },
    customLabel: {
      type: Function,
      required: false,
      default: undefined
    },
    value: {
      type: [String, Number, Object, Array],
      required: false,
      default: ''
    },
    placeholder: {
      type: String,
      required: false,
      default: 'Selecione'
    },
    select: {
      type: Function,
      required: false,
      default: function () {}
    },
    extraParam: {
      type: [String, Number, Object, Array, Boolean],
      required: false,
      default: null
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    required: {
      type: Boolean,
      default: false,
      required: false
    },
    // Determina se será possível escolher multiplas opções
    multiTag: {
      type: Boolean,
      required: false,
      default: false
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false
    },
    forceNullable: {
      type: Boolean,
      required: false,
      default: false
    },
    preventNullable: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  computed: {
    override () {
      return {
        tabIndex: 0
      }
    },

    computedValue: {
      get () {
        let semesterOptions = !this.multiTag ? [{id: null, descricao: 'Escolha uma Semestre'}].concat(this.options) : this.options
        return semesterOptions
      },
      set (newValue) {}
    },
  },

  beforeMount() {
    Request.get('/semestre/listar').then(
      data => {
        this.options = data.body.corpo.itens
        let today = (new Date()).getFullYear() + '/' + (parseInt((new Date()).getMonth()) <= 6 ? '01' : '02')
        let currentSemester = this.options.filter((semester) => semester.descricao == today)
        this.selectItem(currentSemester[0])
      }
    )
  },

  methods: {
    selectItem (item) {
      this.selectedItem = item
      this.select(item.id, this.extraParam)
      this.$emit('input', item.id, this.extraParam)
    },
  }
}
</script>
