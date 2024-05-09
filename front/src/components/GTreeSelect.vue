<template>
  <treeselect
    :id="id"
    :options="optionsArray"

    :value="value"
    :value-format="valueFormat"

    :placeholder="placeholder"
    :multiple="multiTag"

    :required="required"
    :disabled="disabled"
    :class="invalid === true ? 'invalid-input' : 'valid-input'"
    :disable-branch-nodes="true"

    :clearable="clearable"
    class="g-treeselect"
    no-results-text="Nenhum resultado."

    @select="select"
    @input="inputItem"
  />
</template>

<script>
export default {
  name: 'GTreeSelect',
  props: {
    id: {
      type: String,
      required: false,
      default: null
    },
    label: {
      type: String,
      required: false,
      default: 'descricao'
    },
    options: {
      type: Array,
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
    input: {
      type: Function,
      required: false,
      default: function () {}
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
    clearable: {
      type: Boolean,
      required: false,
      default: false
    },
    valueFormat: {
      type: String,
      required: false,
      default: 'object'
    },
    extraParam: {
      type: [String, Number, Object, Array, Boolean],
      required: false,
      default: null
    }
  },

  computed: {

    optionsArray: {
      get () {
        return this.childrenStructure(this.options)
      }
    }
  },

  methods: {
    inputItem (item) {
      this.input(item, this.extraParam)
    },

    childrenStructure (filhos) {
      let list = []
      filhos.map(filho => {
        let child = {
          ...filho,
          label: filho[this.label]
        }

        if (filho.filhos && filho.filhos.length) { child.children = this.childrenStructure(filho.filhos) }

        list.push(child)
      })
      return list
    }

  }
}
</script>
