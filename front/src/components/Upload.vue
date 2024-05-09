<template>
  <label class="upload-area text-center">
    <font-awesome-icon icon="cloud-upload-alt" />
    {{ text }}
    <br>
    <span v-if="error" class="error-message bg-danger">{{ error }}</span>
    <input type="file" class="d-none" @change="onChange($event)">
  </label>
</template>

<script>
export default {
  name: 'Upload',
  props: {
    accept: {
      type: String,
      default: null
    },
    change: {
      type: Function,
      default: null
    },
    multiple: {
      type: Boolean,
      default: false
    },
    text: {
      type: String,
      default: null
    }
  },
  data () {
    return {
      error: null,
      errorSingleFile: 'Extensão de arquivo inválida',
      errorMultipleFiles: 'A extensão de um ou mais arquivos está inválida',
      mimetypes: {
        excel: [
          'application/vnd.ms-excel',
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]
      }
    }
  },
  methods: {
    onChange ($event) {
      if (this.accept) {
        const files = $event.target.files
        const filesLength = files.length
        for (let index = 0; index < filesLength; index++) {
          if (this.mimetypes[this.accept].includes(files[index].type) === false) {
            this.error = this.multiple ? this.errorMultipleFiles : this.errorSingleFile
            return
          }
        }
      }

      this.error = null

      if (this.change && typeof this.change === 'function') {
        this.change($event)
        return
      }

      throw new Error('Change event has not been set.')
    }
  }
}
</script>

<style scoped>
.upload-area {
  display: block;
  padding: 15px;
  border: 2px dashed #ccc;
  font-size: 18px;
  cursor: pointer;
  transition: background-color, .1s linear;
  margin-bottom: 0;
}
.upload-area:hover {
  background-color: #eee;
}
.upload-area:active {
  background-color: #ddd;
}

.error-message {
  display: inline-block;
  padding: 5px 10px;
  font-size: 13px;
}
</style>
