<template>
  <div>
    <upload v-if="!readingFile" :change="inputUploadChange" accept="excel" text="Selecione um arquivo para importar" />

    <!-- <div v-if="true" class="process-spinner">
      <font-awesome-icon icon="spinner" spin /> Lendo arquivo...
    </div> -->
    <div v-if="readingFile" class="process-spinner">
      <load-placeholder :loading="readingFile" :color-a="'#7d7e7f'" :color-b="'#7d7e7f'" />
    </div>
  </div>
</template>

<script>
import {mapState, mapMutations} from 'vuex'
import Upload from '../../../components/Upload'
// import Excel from 'xlsx'

export default {
  components: {
    upload: Upload
  },
  data () {
    return {}
  },
  computed: mapState('importador', ['arquivoSelecionado', 'readingFile']),
  methods: {
    ...mapMutations('importador', ['setArquivoSelecionado', 'setReadingFile', 'setWorkbookData', 'setStep']),

    inputUploadChange ($event) {
      if (!$event.target.files) {
        this.setReadingFile(false)
        this.setStep(0)
        return
      }

      this.setReadingFile(true)

      const file = $event.target.files[0]
      this.setArquivoSelecionado(file)

      const reader = new FileReader()
      reader.onload = this.fileLoaded
      reader.readAsBinaryString(this.arquivoSelecionado)
    },

    fileLoaded (event) {
      /*
      this.setReadingFile(false)
      const workbook = Excel.read(event.target.result, {type: 'binary'})
      const sheets = workbook.Sheets
      const keys = Object.keys(sheets)
      const finalData = []

      keys.map(sheetName => {
        const sheetLength = this.getSheetLength(sheets[sheetName])
        finalData.push({nome: sheetName, quantidadeRegistros: sheetLength})
      })

      this.setWorkbookData(finalData)
      this.setStep(1)
      */
    },

    getSheetLength (sheet) {
      const split = sheet['!ref'].split(':')
      return split[1].replace(/\D*/, '')
    }
  }
}
</script>
