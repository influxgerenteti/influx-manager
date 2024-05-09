<template>
  <div>
    <multiselect
      :id="id"
      v-model="selectedItem"
      label="apelido"
      track-by="apelido"
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
      <template slot="noOptions">Nenhuma opção disponível.</template>
    </multiselect>
  </div>
</template>

<script>
import Request from "../../utils/request";
export default {
  name: "GSelectConsultor",
  data() {
    return {
      availableOptions: [],
      selectedItem: null,
    };
  },
  props: {
    id: {
      type: String,
      required: false,
      default: null,
    },
    customLabel: {
      type: Function,
      required: false,
      default: undefined,
    },
    value: {
      type: [String, Number, Object, Array],
      required: false,
      default: "",
    },
    placeholder: {
      type: String,
      required: false,
      default: "Selecione",
    },
    select: {
      type: Function,
      required: false,
      default: function () {},
    },
    extraParam: {
      type: [String, Number, Object, Array, Boolean],
      required: false,
      default: null,
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
    required: {
      type: Boolean,
      default: false,
      required: false,
    },
    // Determina se será possível escolher multiplas opções
    multiTag: {
      type: Boolean,
      required: false,
      default: false,
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false,
    },
    forceNullable: {
      type: Boolean,
      required: false,
      default: false,
    },
    preventNullable: {
      type: Boolean,
      required: false,
      default: false,
    },
  },

  computed: {
    override() {
      return {
        tabIndex: 0,
      };
    },

    computedValue: {
      get() {
        let languageOptions = !this.multiTag
          ? [{ id: null, apelido: "Escolha um Consultor" }].concat(
              this.availableOptions
            )
          : this.availableOptions;
        return languageOptions;
      },
      set(newValue) {},
    },
  },

  beforeMount() {
    this.getIdiomas();
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

    getIdiomas() {
      Request.get("/funcionario/listar?consultor=1").then((data) => {
        this.availableOptions = data.body.corpo.itens;
      });
    },

    reset(){
      this.selectedItem = []
      this.select([], this.extraParam);
      this.$emit("input", [], this.extraParam);
    
    }
  },
};
</script>
