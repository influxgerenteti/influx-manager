<template>

  <div class="editor">
    <editor-menu-bar v-if="!disabled" :editor="editor">
      <div slot-scope="{ commands, isActive }" class="menubar">

        <button
          :class="{ 'is-active': isActive.bold() }"
          type="button"
          class="menubar__button"
          @click="commands.bold"
        >
          <font-awesome-icon icon="bold" />
        </button>

        <button
          :class="{ 'is-active': isActive.italic() }"
          type="button"
          class="menubar__button"
          @click="commands.italic"
        >
          <font-awesome-icon icon="italic" />
        </button>

        <button
          :class="{ 'is-active': isActive.strike() }"
          type="button"
          class="menubar__button"
          @click="commands.strike"
        >
          <font-awesome-icon icon="strikethrough"/>
        </button>

        <button
          :class="{ 'is-active': isActive.underline() }"
          type="button"
          class="menubar__button"
          @click="commands.underline"
        >
          <font-awesome-icon icon="underline"/>
        </button>

        <button
          :class="{ 'is-active': isActive.code() }"
          type="button"
          class="menubar__button"
          @click="commands.code"
        >
          <font-awesome-icon icon="code"/>
        </button>

        <button
          :class="{ 'is-active': isActive.paragraph() }"
          type="button"
          class="menubar__button"
          @click="commands.paragraph"
        >
          <font-awesome-icon icon="paragraph"/>
        </button>

        <button
          :class="{ 'is-active': isActive.heading({ level: 1 }) }"
          type="button"
          class="menubar__button"
          @click="commands.heading({ level: 1 })"
        >
          H1
        </button>

        <button
          :class="{ 'is-active': isActive.heading({ level: 2 }) }"
          type="button"
          class="menubar__button"
          @click="commands.heading({ level: 2 })"
        >
          H2
        </button>

        <button
          :class="{ 'is-active': isActive.heading({ level: 3 }) }"
          type="button"
          class="menubar__button"
          @click="commands.heading({ level: 3 })"
        >
          H3
        </button>

        <button
          :class="{ 'is-active': isActive.bullet_list() }"
          type="button"
          class="menubar__button"
          @click="commands.bullet_list"
        >
          <font-awesome-icon icon="list-ul"/>
        </button>

        <button
          :class="{ 'is-active': isActive.ordered_list() }"
          type="button"
          class="menubar__button"
          @click="commands.ordered_list"
        >
          <font-awesome-icon icon="list-ol"/>
        </button>

        <button
          :class="{ 'is-active': isActive.blockquote() }"
          type="button"
          class="menubar__button"
          @click="commands.blockquote"
        >
          <font-awesome-icon icon="quote-right"/>
        </button>

        <button
          :class="{ 'is-active': isActive.code_block() }"
          type="button"
          class="menubar__button"
          @click="commands.code_block"
        >
          <font-awesome-icon icon="code"/>
        </button>

        <button
          type="button"
          class="menubar__button"
          @click="commands.horizontal_rule"
        >
          -
        </button>

        <button
          type="button"
          class="menubar__button"
          @click="commands.undo"
        >
          <font-awesome-icon icon="undo"/>
        </button>

        <button
          type="button"
          class="menubar__button"
          @click="commands.redo"
        >
          <font-awesome-icon icon="redo"/>
        </button>

      </div>
    </editor-menu-bar>

    <editor-content :editor="editor" class="editor__content" />
  </div>

</template>

<script>
import { Editor, EditorContent, EditorMenuBar } from 'tiptap'
import {
  Blockquote,
  CodeBlock,
  HardBreak,
  Heading,
  HorizontalRule,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Code,
  Italic,
  Link,
  Strike,
  Underline,
  History
} from 'tiptap-extensions'

export default {
  components: {
    EditorContent,
    EditorMenuBar
  },

  props: {
    value: {
      type: String,
      required: false,
      default: ''
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      editor: null,
      loaded: false,
      html: 'Update content to see changes'
    }
  },

  watch: {
    value (val) {
      if (this.loaded === false) {
        this.setContent()
        this.loaded = true
      }
    }
  },

  mounted () {
    this.loaded = false

    this.editor = new Editor({
      extensions: [
        new Blockquote(),
        new BulletList(),
        new CodeBlock(),
        new HardBreak(),
        new Heading({ levels: [1, 2, 3] }),
        new HorizontalRule(),
        new ListItem(),
        new OrderedList(),
        new TodoItem(),
        new TodoList(),
        new Link(),
        new Bold(),
        new Code(),
        new Italic(),
        new Strike(),
        new Underline(),
        new History()
      ],
      editable: !this.disabled,
      content: this.value,
      onUpdate: ({ getJSON, getHTML }) => {
        this.$emit('input', getHTML())
      }
    })
  },

  methods: {
    setContent () {
      this.editor.setContent(this.value)
    },

    beforeDestroy () {
      this.editor.destroy()
    }
  }
}
</script>

<style>
.editor {
  margin-top: 1rem;
  position: relative;
}

.menubar {
  margin-bottom: 0.25rem;
  -webkit-transition: visibility .2s .4s,opacity .2s .4s;
  transition: visibility .2s .4s,opacity .2s .4s;
}

.menubar__button {
  font-weight: 700;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    background: transparent;
    border: 0;
    color: #000;
    padding: .2rem .5rem;
    margin-right: .2rem;
    border-radius: 3px;
    cursor: pointer;
}

.menubar__button:hover {
  background:rgba(0,0,0,.05);
}

.editor__content {
  padding: 10px;
  border: 0;
  height: 400px;
  background-color: #ececec;
}

.editor__content div {
  height: 100%;
  overflow: auto;
}
</style>
