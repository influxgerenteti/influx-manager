<template>
  <div>
    <multiselect
      :id="id"
      :livroId="livroId"
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
  name: 'GSelectLicao',
  data() {
    return {
      lessons : [],
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
    // Necessário passar o id do livro como prop para buscar as lições do livro
    livroId: {
      type: Number,
      required: true
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
        let lessonOptions = !this.multiTag ? [{id: null, descricao: 'Escolha uma lição'}].concat(this.lessons) : this.lessons
        return lessonOptions
      },
      set (newValue) {}
    },
  },

  watch: {
    livroId: function(newValue, oldValue) {
      this.getLessonsByBook(newValue)
    }
  },

  methods: {
    selectItem(item) {
      let selection = [];
      if (this.multiTag) {
        if (this.selectedItem) {
          this.selectedItem.forEach((select) => {
            if (item.id != select.id) {
              selection.push(select.id);
            }
          });
          if (this.selectedItem.findIndex((obj) => obj.id == item.id) == -1) {
            selection.push(item.id);
          }
        } else {
          selection.push(item.id);
        }
      } else {
        selection = item.id;
      }
      this.select(selection, this.extraParam);
      this.$emit("input", selection, this.extraParam);
    },

    getLessonsByBook(book) {
      if(!book){
        this.lessons = []
        return
      }
      Request.get('/licao/buscar_por_livro/' + book).then(
        data => {
          this.lessons = data.body.corpo
        }
      )
    }
  }
}
</script>
