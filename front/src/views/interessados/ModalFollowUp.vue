<template>
  <div >
    <div v-if="carregandoFormulario" class="form-loading">
      <load-placeholder :loading="carregandoFormulario" />
    </div>
    <template v-if="possuiFormulario === true">
      <form class="p-3" @submit.prevent="adicionarNovoFollowUp()">
        <!-- <div v-if="true" class="form-loading">
          <load-placeholder :loading="listarCamposDoFormulario == null" />
        </div> -->
        <div class="alert alert-info">
          <p>
            Caso tenha preenchido algum campo e efetue a troca de formulario.
          </p>
          <p>
            Os dados não salvos, serão perdidos.
          </p>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="formularios_iniciais" class="col-form-label">Formulários</label>
            <select id="formularios_iniciais" v-model="formularioInicialSelecionado" class="custom-select form-control" @change="selecionaFormulario()">
              <option :value="null">Selecione</option>
              <option v-for="(item, index) in listaFormularios" :key="index" :value="item.id">
                {{ item.descricao_formulario }}
              </option>
            </select>
          </div>
        </div>

        <template v-if="formularioInicialSelecionado !== null">
          <div v-if="true" class="form-loading">
            <load-placeholder :loading="!retornoCampos" />
          </div>

          <div v-if="listarCamposDoFormulario.length === 0" class="text-center">
            <span>Nenhum campo cadastrado para o formulário selecionado.</span>
          </div>

          <div v-for="(campo, index2) in listarCamposDoFormulario" v-else :key="index2" class="form-group">
            <label :for="campo.nome_campo + '_campo_dinamico'" :data-set="labelsFormulario[formularioInicialSelecionado+'_'+campo.id] = campo.nome_campo" class="col-form-label">{{ campo.nome_campo }}</label>
            <template v-if="campo.texto_longo">
              <b-form-textarea
                :id="campo.nome_campo + '_campo_dinamico'"
                v-model="dadosFormulario[formularioInicialSelecionado+'_'+campo.id]"
                rows="3" />
            </template>
            <template v-else>
              <input :id="campo.nome_campo + '_campo_dinamico'" v-model="dadosFormulario[formularioInicialSelecionado+'_'+campo.id]" type="text" class="form-control" maxlength="255" >
            </template>
          </div>
        </template>

        <div class="d-flex justify-content-start">
          <b-btn v-if="listarCamposDoFormulario.length > 0" type="submit" variant="verde">Adicionar</b-btn>
          <b-btn type="button" variant="link" @click="resetaDados(), $emit('hide')">Cancelar</b-btn>
        </div>
      </form>
    </template>

    <template v-else>
      <div class="text-center">
        Nenhum formulario foi cadastrado para o Follow-Up{{ filtraFormularios ? (formularioInicialApenas ? ' Inicial.' : ' Geral.'): '.' }}
      </div>
      <div class="p-3 d-flex justify-content-center">
        <b-btn type="button" variant="link" @click="resetaDados(), $emit('hide')">Cancelar</b-btn>
      </div>
    </template>
  </div>
</template>

<script>
import {mapActions, mapState} from 'vuex'

export default {
  props: {
    setaFollowUpCallback: {
      required: true,
      type: Function
    },
    setaDadosEnvio: {
      required: true,
      type: Function
    },
    filtraFormularios: {
      required: true,
      type: Boolean
    },
    formularioConvenio: {
      required: false,
      type: Boolean,
      default: false
    },
    formularioInicialApenas: {
      required: false,
      type: Boolean,
      default: null
    },
    tipoFormulario: {
      required: false,
      type: [String, Array],
      // CA - CONTATO ATIVO, CR - CONTATO RECEPTIVO, NP - NEGOCIACAO DE PARCEIRIA, NI - NIVELAMENTO
      default: () => {
        return ['CA', 'CR', 'NP', 'NI', 'NI']
      }
    }
  },
  data () {
    return {
      carregandoFormulario: true,
      possuiFormulario: false,
      retornoCampos: false,
      formularioInicialSelecionado: null,
      listarCamposDoFormulario: [],
      labelsFormulario: [],
      dadosFormulario: []
    }
  },
  computed: {
    ...mapState('formularioFollowUp', {listaFormularios: 'lista', filtros: 'filtros'})
  },
  methods: {
    ...mapActions('formularioFollowUp', {listarFormulariosIniciais: 'listar'}),
    ...mapActions('formularioFollowUpCampos', {listarCamposPorFormulario: 'listarPorFormulario'}),

    executaRequisicoes () {
      this.resetaDados()
      this.listaCamposDinamicos()
    },

    listaCamposDinamicos () {
      this.filtros.situacao = ['A']
      this.filtros.tipo_formulario = this.validarTipoFormulario(this.tipoFormulario)
      if (this.filtraFormularios === true) {
        this.filtros.follow_up_inicial = this.formularioInicialApenas
      } else {
        this.filtros.follow_up_inicial = null
      }

      this.listarFormulariosIniciais().then(this.carregandoFormulario = true)
        .then(response => {
          this.carregandoFormulario = false
          if (response.itens.length > 0) {
            this.possuiFormulario = true
            this.formularioInicialSelecionado = response.itens[0].id
            this.selecionaFormulario()
          }
        })
    },

    validarTipoFormulario (tipoFormulario) {
      const arrTipoFormulario = ['CA', 'CR', 'NP', 'NI', 'NI']
      let arrReturn = []
      if ((typeof tipoFormulario) === 'string') {
        arrReturn = [tipoFormulario]
      } else if (Array.isArray(tipoFormulario)) {
        arrReturn = tipoFormulario
      }

      arrReturn = arrReturn.map(item => {
        if (arrTipoFormulario.indexOf(item) > -1) {
          return item
        }
      })

      return arrReturn
    },

    selecionaFormulario () {
      this.retornoCampos = false
      this.listarCamposDoFormulario = []
      if (this.formularioInicialSelecionado !== null) {
        this.listarCamposPorFormulario(this.formularioInicialSelecionado)
          .then(campos => {
            this.retornoCampos = true
            this.labelsFormulario = []
            this.dadosFormulario = []
            if (campos !== undefined) {
              this.listarCamposDoFormulario = campos
            }
          })
      }
    },

    resetaDados () {
      this.$store.commit('formularioFollowUp/SET_PAGINA_ATUAL', 1)
      this.$store.commit('formularioFollowUp/SET_LISTA', [])
      this.possuiFormulario = false
      this.retornoCampos = false

      this.labelsFormulario = []
      this.dadosFormulario = []
      this.listarCamposDoFormulario = []
    },

    adicionarNovoFollowUp () {
      let objetosLabels = Object.assign({}, this.labelsFormulario)
      let objetosDadosFormularios = Object.assign({}, this.dadosFormulario)
      let textoFollowUp = ''
      let arrayBackend = []
      Object.keys(objetosLabels).forEach((chave, index) => {
        let dataEnvio = {
          label: objetosLabels[chave],
          dado: null
        }
        textoFollowUp += objetosLabels[chave] + ' - (TEMPORÁRIO)\n'
        if (objetosDadosFormularios.hasOwnProperty(chave) === true) {
          textoFollowUp += objetosDadosFormularios[chave] + '\n'
          dataEnvio.dado = objetosDadosFormularios[chave]
        }
        arrayBackend.push(dataEnvio)
      })
      this.setaFollowUpCallback(textoFollowUp)
      // this.setaDadosEnvio({tipo_contato: this.tipoContatoSelecionado, follow_ups: arrayBackend})
      this.setaDadosEnvio({formulario_follow_up: this.formularioInicialSelecionado, follow_ups: arrayBackend})
      this.resetaDados()
      this.$emit('hide')
    }
  }
}
</script>
