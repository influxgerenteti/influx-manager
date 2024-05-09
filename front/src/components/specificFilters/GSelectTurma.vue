<template>
  <div>
    <multiselect
      :id="id"
      v-model="selectedItem"
      label="turmaDescricao"
      track-by="turmaDescricao"
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
      @remove="removeItem"
    >
      <template slot="noResult">Nenhum resultado.</template>
      <template slot="noOptions">Selecione um livro.</template>
    </multiselect>
  </div>
</template>

<script>
  import Request from '../../utils/request'
export default {
  name: 'GSelectTurma',
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
        let turmasOptions = !this.multiTag ? [{id: null, turmaDescricao: 'Escolha uma Turma'}].concat(this.options) : this.options
        return turmasOptions
      },
      set (newValue) {}
    },
  },

  beforeMount() {
    Request.get('/turma/listar').then(
      data => {
        this.options = data.body.corpo.itens
console.log(this.options.turmaDescricao)
        //Ordena em ordem alfabética
        this.options.sort((a, b) => {
        if (a.turmaDescricao < b.turmaDescricao) return -1
        if (a.turmaDescricao > b.turmaDescricao) return 1
       
        return 0
      })
    }
    )
  },

  methods: {
    selectItem(item) {
      
      let selection = [];
      if (this.multiTag) {
        if (this.selectedItem) {
          this.selectedItem.forEach((select) => {
            
            if (item.id != select.turmaId) {
              selection.push(select.turmaId);
            }
          });
          if (this.selectedItem.findIndex((obj) => obj.id == item.id) == -1) {
            selection.push(item.id);
          }
        } else {
          selection.push(item.id);
        }
      } else {
        selection = item.turmaId;
    
      }
      this.select(selection, this.extraParam);
      this.$emit("input", selection, this.extraParam);
    },
    removeItem() {
      this.selectedItem = [];
      this.select([], this.extraParam)
      this.$emit('input', null, this.extraParam)
    }
  }
}
</script>
