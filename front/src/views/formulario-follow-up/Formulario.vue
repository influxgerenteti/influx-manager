<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="carregandoCampos" />
      </div>
      <div class="form-group row">
        <div class="col-md-4">
          <label for="formulario" class="col-form-label">Nome do Formulário *</label>
          <input id="formulario" v-model="descricao_formulario" type="text" class="form-control" required maxlength="100">
          <div class="invalid-feedback">Preencha o nome do formulário!</div>
        </div>

        <div class="col-md-4">
          <label v-help-hint="'form-follow_up_tipo_formulario'" for="tipo_formulario" class="col-form-label">Tipo formulário *</label>
          <g-select
            id="tipo_formulario"
            :value="tipoFormulario"
            :select="setTipoFormulario"
            :options="listaDeTipoFormulario"
            :class="!isValid && !tipoFormulario ? 'invalid-input' : 'valid-input'"
            class="multiselect-truncate"
            required
            label="descricao"
            track-by="id" />
        </div>
        <div class="col-md-4">
          <label class="col-form-label d-block">&nbsp;</label>
          <b-form-checkbox v-help-hint="'form-formulario_follow_up_inicial'" v-model="follow_up_inicial" :checked="follow_up_inicial" name="follow_up_inicial">Follow-up Inicial</b-form-checkbox>
        </div>
        <!-- <div class="col-md-2 mb-3">
          <label class="col-form-label d-block">&nbsp;</label>
          <b-form-checkbox v-help-hint="'form-formulario_follow_up_inicial'" :disabled="formulario_convenio" v-model="follow_up_inicial" :checked="follow_up_inicial" name="follow_up_inicial">Follow-up Inicial</b-form-checkbox>
        </div>
        <div class="col-md-3 mb-3">
          <label class="col-form-label d-block">&nbsp;</label>
          <b-form-checkbox v-help-hint="'form-formulario_followup_cadastro_convenio'" v-model="formulario_convenio" :checked="formulario_convenio" name="formulario_convenio" @change="follow_up_inicial = false">Cadastro de convênio</b-form-checkbox>
        </div> -->
      </div>
      <div class="form-group row">
        <div class="col-md-12">
          <button v-b-modal.novoCampo type="button" class="btn btn-azul">
            Adicionar campo
          </button>
        </div>
      </div>
      <div class="table-responsive-sm">
        <g-table>
          <thead>
            <tr>
              <th>Campos</th>
              <th class="coluna-icones"></th>
            </tr>
          </thead>
          <tbody>
            <perfect-scrollbar>
              <div v-if="formularioCampos.length === 0 && formularioCamposTemporarios.length === 0" class="busca-vazia">
                <p>Nenhum campo adicionado.</p>
              </div>
              <tr v-for="campo in formularioCampos" :key="campo.id">
                <td data-label="Campos">{{ campo.nome_campo }}</td>
                <td class="coluna-icones">
                  <a href="javascript:void(0)" title="Excluir campo" class="icone-link text-muted" @click.prevent="removerCampo(campo, campo.formulario_follow_up.id)">
                    <font-awesome-icon icon="trash-alt" />
                  </a>
                </td>
              </tr>
              <tr v-for="(campo, index) in formularioCamposTemporarios" :key="index">
                <td data-label="Campos">{{ campo.nome_campo }}</td>
                <td class="coluna-icones">
                  <a href="javascript:void(0)" title="Excluir campo" class="icone-link text-muted" @click.prevent="removerCampoTemporario(index)">
                    <font-awesome-icon icon="trash-alt" />
                  </a>
                </td>
              </tr>
            </perfect-scrollbar>
          </tbody>
        </g-table>
      </div>
      <div class="form-group pt-2">
        <b-btn :disabled="!descricao_formulario.length || isEnviandoFormulario" type="submit" variant="verde">{{ isEnviandoFormulario ? 'Salvando...' : 'Salvar' }}</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>

    <b-modal id="novoCampo" ref="novoCampo" size="md" centered no-close-on-backdrop hide-header hide-footer>
      <form @submit.prevent="adicionarNovoCampo()">
        <div class="form-group">
          <label for="novo_campo" class="col-form-label">Nome do campo</label>
          <textarea id="novo_campo" v-model="novo_campo_formulario" class="form-control" rows="2" maxlength="255"></textarea>
          <span class="text-secondary">Limite de caracteres: {{ 255 - novo_campo_formulario.length }}</span>
        </div>

        <b-form-checkbox v-help-hint="'form-formulario_followup_texto_longo'" v-model="long_text" :checked="long_text" name="long_text">Texto longo</b-form-checkbox>

        <div class="p-3 d-flex justify-content-center">
          <b-btn :disabled="!novo_campo_formulario || isEnviandoCampo" type="submit" variant="verde">Salvar</b-btn>
          <b-btn type="button" variant="link" @click="finalizar()">Cancelar</b-btn>
        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'

export default {
  data () {
    return {
      carregandoCampos: true,
      isValid: true,
      isEdit: false,
      isEnviandoCampo: false,
      isEnviandoFormulario: false,
      follow_up_inicial: false,
      // formulario_convenio: false,
      long_text: false,
      descricao_formulario: '',
      novo_campo_formulario: '',
      formularioCampos: [],
      formularioCamposTemporarios: [],
      tipoFormulario: null,
      listaDeTipoFormulario: [
        {id: 1, descricao: 'Contato Ativo', value: 'CA'},
        {id: 2, descricao: 'Contato Receptivo', value: 'CR'},
        {id: 3, descricao: 'Negociação de parceria', value: 'NP'},
        {id: 4, descricao: 'Nivelamento', value: 'NI'}
      ]
    }
  },
  computed: {
    ...mapState('formularioFollowUp', ['objFormularioFollowUp', 'estaCarregando', 'itemSelecionadoID'])
  },
  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO(id)
      this.buscar()
        .then(response => {
          this.descricao_formulario = response.descricao_formulario
          this.follow_up_inicial = response.follow_up_inicial
          this.tipoFormulario = this.listaDeTipoFormulario.find(item => item.value === response.tipo_formulario)
          this.listarCampos(id)
        })
    }
  },
  validations: {
    descricao_formulario: {required},
    tipoFormulario: {required}
  },
  methods: {
    ...mapActions('formularioFollowUp', ['buscar', 'atualizar', 'criar']),
    ...mapActions('formularioFollowUpCampos', {listarCamposPorFormulario: 'listarPorFormulario', criarCampoComFormulario: 'criarCampoFormulario', removerCampoTabela: 'remover'}),
    ...mapMutations('formularioFollowUp', ['SET_ITEM_SELECIONADO', 'SET_DETALHES_ITEM_SELECIONADO', 'SET_SITUACAO', 'SET_PAGINA_ATUAL', 'LIMPAR_ITEM_SELECIONADO']),

    setTipoFormulario (value) {
      this.tipoFormulario = value
    },

    removerCampoTemporario (index) {
      this.formularioCamposTemporarios.splice(index, 1)
    },

    listarCampos (formularioId) {
      this.listarCamposPorFormulario(formularioId)
        .then(dados => {
          this.carregandoCampos = false
          this.formularioCampos = dados
        })
    },

    adicionarNovoCampo () {
      this.isEnviandoCampo = true
      const id = this.$route.params.id
      if (id) {
        let objBanco = {
          formulario_follow_up: id,
          nome_campo: this.novo_campo_formulario,
          texto_longo: this.long_text
        }
        this.criarCampoComFormulario(objBanco)
          .then(response => {
            this.isEnviandoCampo = false
            this.listarCampos(id)
            this.finalizar()
          })
      } else {
        this.formularioCamposTemporarios.push({
          nome_campo: this.novo_campo_formulario,
          texto_longo: this.long_text
        })
        this.isEnviandoCampo = false
        this.finalizar()
      }
    },

    finalizar (action = 'cancel') {
      this.$refs.novoCampo.hide()
      this.novo_campo_formulario = ''
    },

    removerCampo (campo, formularioId) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.removerCampoTabela(campo.id)
            .then(() => {
              this.listarCampos(formularioId)
            })
        }
      }, `Deseja excluir o campo "${campo.nome_campo}"?`)
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.isEnviandoFormulario = false
      this.$router.push('/cadastros/formulario-follow-up')
    },

    salvar () {
      this.isEnviandoFormulario = true
      if (this.$v.$invalid) {
        this.isValid = false
        this.isEnviandoFormulario = false
        return
      }
      this.objFormularioFollowUp.descricao_formulario = this.descricao_formulario
      this.objFormularioFollowUp.tipo_formulario = this.tipoFormulario.value
      this.objFormularioFollowUp.follow_up_inicial = this.follow_up_inicial

      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(console.error)
      } else {
        this.objFormularioFollowUp.formulario_follow_up_campos = {}
        this.objFormularioFollowUp.formulario_follow_up_campos = this.formularioCamposTemporarios
        this.criar().then(this.voltar).catch(console.error)
      }
    }
  }
}
</script>
<style scoped>
.table-scroll {
  height: calc(100vh - 360px);
  height: -webkit-calc(100vh - 360px);
  height: -moz-calc(100vh - 360px);
  margin-bottom: 0;
}
</style>
