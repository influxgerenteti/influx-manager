<template>
  <div>
    <b-modal id="formularioOcorrenciaAcademica" ref="formularioOcorrenciaAcademica" v-model="visibleFormularioOcorrenciaAcademica" size="lg" centered no-close-on-backdrop hide-header hide-footer>
      <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="adicionarNovaOcorrecia()">
      <!--  <div class="form-loading screen-load">
          <load-placeholder :loading="estaCarrengandoModal || salvando" />
        </div> -->

        <h5 class="title-module mb-2">Ocorrência acadêmica</h5>
        <div class="form-group row">
          <b-col md="6">
            <label for="formulario_nome_do_aluno" class="col-form-label">Aluno *</label>
            <template v-if="!isEdit">
              <typeahead id="formulario_nome_do_aluno" ref="refFormularioAluno" :item-hit="setNomeAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" required/>
            </template>
            <template v-else>
              <input id="formulario_nome_do_aluno" :value="aluno" :disabled="isEdit" type="text" class="form-control" >
            </template>
          </b-col>
          <b-col md="4">
            <label for="numero_de_matricula" class="col-form-label">Matricula</label>
            <input id="numero_de_matricula" :value="numeroDeMatricula" type="text" class="form-control" disabled >
          </b-col>
        </div>

        <div class="form-group row">
          <b-col md="6">
            <label for="formulario_tipo_ocorrencia" class="col-form-label">Tipo ocorrência *</label>
            <g-select id="formulario_tipo_ocorrencia"
                      :select="setTipoOcorrencia"
                      :value="tipoOcorrencia"
                      :options="listaDeTipoOcorrencia"
                      :disabled="isEdit"
                      :invalid="!isValid && ($v.tipoOcorrencia.$invalid || tipoOcorrencia.id === null)"
                      class="multiselect-truncate"
                      label="descricao"
                      track-by="id"
                      required
            />
            <div v-if="!isValid && ($v.tipoOcorrencia.$invalid || tipoOcorrencia.id === null)" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </b-col>
          <b-col v-if="!readOnly" md="6">
            <label for="formulario_funcionario" class="col-form-label">Funcionário *</label>
            <g-select id="formulario_funcionario"
                      :select="setFuncionario"
                      :value="funcionario"
                      :options="listaDeFuncionarios"
                      :invalid="!isValid && ($v.funcionario.$invalid || funcionario.id === null)"
                      class="multiselect-truncate"
                      label="apelido"
                      track-by="id"
                      required
            />
            <div v-if="!isValid && ($v.funcionario.$invalid || funcionario.id === null)" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </b-col>

          <b-col md="3">
            <label for="formulario_ocorrencia_academica_data" class="col-form-label">Data do próximo contato</label>
            <div v-if="readOnly" class="form-control form-control-disabled truncate">{{ data }}</div>
            <g-datepicker v-else
                          :element-id="'formulario_ocorrencia_academica_data'"
                          :value="data"
                          :selected="setData"
                          :readonly="readOnly"
            />
          </b-col>

          <b-col md="3">
            <label for="formulario_ocorrencia_academica_hora" class="col-form-label">Horário próximo contato</label>
            <input v-mask="'##:##'" id="formulario_atividade_horario_inicio" v-model="horario" :class="!$v.horario.validateHour ? 'is-invalid' : null" :readonly="readOnly" type="text" class="form-control" maxlength="5">
            <div v-if="!$v.horario.validateHour" class="invalid-feedback">
              {{ 'Horário inválido' }}
            </div>
          </b-col>

          <b-col md="2">
            <label for="formulario_ocorrencia_academica_situacao" class="col-form-label">Situação</label>
            <input id="formulario_ocorrencia_academica_situacao" :value="situcaoOcorrencia" type="text" class="form-control" readonly >
          </b-col>
        </div>

        <div v-if="isEdit" class="textarea-historico">
          <b-form-textarea
            id="historicoOcorrecias"
            :value="historicoOcorrecias"
            class="full-textarea"
            readonly
            rows="6"
          />
        </div>

        <div v-if="!readOnly">
          <label for="obsevacao" class="col-form-label">Descrição</label>
          <b-form-textarea
            id="obsevacao"
            v-model="obsevacao"
            class="full-textarea"
            rows="3"
          />
        </div>

        <div class="form-group pt-2">
          <template v-if="!readOnly">

            <!-- <b-btn :disabled="salvando" type="submit" variant="verde" @click="bSalvar = false" >{{ salvando ? "Salvando..." : "Salvar" }}</b-btn> -->
            <b-btn :disabled="salvando" type="submit" variant="verde" @click="bSalvarESair = true" >{{ salvando ? "Salvando..." : "Salvar e sair" }} </b-btn>
            <b-btn :disabled="salvando" type="button" variant="primary" @click="abrirModalConcluir()">Concluir</b-btn>
          </template>

          <b-btn type="button" variant="link" @click="limparCamposFormulario(), cancelarDados()">{{ !readOnly ? 'Cancelar' : 'Fechar' }}</b-btn>
        </div>
      </form>
    </b-modal>

    <!-- Modal de confirmação do concluir -->
    <b-modal id="confirmar-concluir" ref="confirmar-concluir" v-model="modalConfirmarConcluir" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center">
        <p>Deseja concluir a ocorrência?</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn class="mt-3 mr-3" variant="outline-success" block @click="modalConfirmarConcluir = false, selecetOcorreciaFechada = true, adicionarNovaOcorrecia()">Confirmar</b-btn>
        <b-btn class="mt-3" variant="outline-danger" block @click="modalConfirmarConcluir = false, visibleFormularioOcorrenciaAcademica = true">Cancelar</b-btn>
      </div>
    </b-modal>
  </div>
</template>
<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {dateToString, stringToISODate} from '../../utils/date'
import {validateHour} from '../../utils/validators'
import {required} from 'vuelidate/lib/validators'

export default {
  name: 'ModalOcorrencia',
  props: {
    listaDeTipoOcorrencia: {
      required: true,
      type: Array
    },

    listaDeFuncionarios: {
      required: true,
      type: Array
    },

    cancelarDados: {
      required: true,
      type: Function,
      default: null
    },

    usuarioLogado: {
      required: true,
      type: Object,
      default: null
    },

    readOnly: {
      type: Boolean,
      default: false,
      required: false
    }
  },

  data () {
    return {
      isEdit: false,
      isValid: true,
      bSalvarESair: false,
      bSalvar: false,
      copiaId: null,
      salvando: false,
      visibleFormularioOcorrenciaAcademica: false,
      selecetOcorreciaFechada: false,
      numeroDeMatricula: null,
      modalConfirmarConcluir: false,
      tipoOcorrencia: null,
      funcionario: null,
      obsevacao: null,
      aluno: null,
      horario: '',
      data: '',
      objetoRetorno: null,
      situcao: [
        {descricao: 'Aberto', value: 'A'},
        {descricao: 'Fechado', value: 'F'}
      ]

    }
  },
  computed: {
    ...mapState('ocorrenciaAcademica', {itemSelecionado: 'itemSelecionado', itemSelecionadoID: 'itemSelecionadoID', estaCarrengandoModal: 'estaCarregando'}),
    dateToString: dateToString,
    situcaoOcorrencia: {
      get () {
        if (this.itemSelecionado.situacao === 'F') {
          return 'Fechado'
        }
        return 'Aberto'
      }
    },
    historicoOcorrecias: {
      get () {
        let stringFinal = ''
        const novaLinha = '\n'
        if (this.itemSelecionado.ocorrenciaAcademicaDetalhes) {
          let arrayDeOcorrenciasAcademicas = this.itemSelecionado.ocorrenciaAcademicaDetalhes

          arrayDeOcorrenciasAcademicas.forEach(element => {
            let novo
            let data = dateToString(new Date(element.data_criacao))
            let horas = (new Date(element.data_criacao).getHours()<10?'0':'') + new Date(element.data_criacao).getHours()
            let minutos = (new Date(element.data_criacao).getMinutes()<10?'0':'') + new Date(element.data_criacao).getMinutes()
            let funcionario = element.funcionario.apelido
            let texto = element.texto
            data = data + '-' + horas + ':' + minutos
            novo = data + '-' + funcionario + novaLinha + texto + novaLinha
            stringFinal += novo
          })
        }

        return stringFinal
      }
    }
  },
  validations: {
    horario: {validateHour},
    aluno: {required},
    tipoOcorrencia: {required},
    funcionario: {required}
  },
  methods: {
    ...mapActions('ocorrenciaAcademica', {criarOcorrencia: 'criar', atualizarOcorrencia: 'atualizar', buscarPorId: 'buscar'}),
    ...mapMutations('ocorrenciaAcademica', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ALUNO_ID', 'SET_TIPO_OCORRENCIA_ID', 'SET_USUARIO_ID', 'SET_FUNCIONARIO_ID', 'SET_DATA_CONCLUSAO', 'SET_FRANQUEADA_ID', 'SET_SITUACAO', 'SET_TEXTO', 'SET_DATA_PROXIMO_CONTATO', 'SET_HORARIO']),

    limparCamposFormulario () {
      this.copiaId = null
      this.numeroDeMatricula = null
      this.tipoOcorrencia = null
      this.funcionario = null
      this.obsevacao = null
      this.aluno = null
      this.selecetOcorreciaFechada = false
      this.data = ''
      this.horario = ''
      this.isValid = true
      this.isEdit = false
      if (this.$refs.refFormularioAluno) {
        this.$refs.refFormularioAluno.resetSelection()
      }
    },

    buscarDadosFormulario () {
      this.isEdit = true
      this.copiaId = this.itemSelecionado.id
      this.buscarPorId(this.itemSelecionado.id)
        .then(response => {
          this.setNomeAlunoEdicao(this.itemSelecionado.aluno)
          this.setTipoOcorrencia(this.itemSelecionado.tipo_ocorrencia)
          this.setFuncionario(this.itemSelecionado.funcionario)

          this.data = this.itemSelecionado.data_proximo_contato ? dateToString(new Date(this.itemSelecionado.data_proximo_contato)) : ''
          if (this.data) {
            let horario = this.itemSelecionado.data_proximo_contato.split('T')[1]
            this.horario = horario.match(/(\d{2,2}):(\d{2,2})/)[0]
          }
        })
    },

    montaParametos () {
      let alunoId = this.aluno ? this.aluno.id : null
      let tipoOcorrenciaId = this.tipoOcorrencia ? this.tipoOcorrencia.id : null
      let funcionarioId = this.funcionario ? this.funcionario.id : null
      let usuarioId = this.usuarioLogado ? this.usuarioLogado.id : null
      let obsevacao = this.obsevacao ? this.obsevacao : null
      let data = this.data ? stringToISODate(this.data) : null
      let horario = this.horario ? this.horario : null
      let situcao = null

      if (this.selecetOcorreciaFechada === true) {
        situcao = 'F'
      }

      this.SET_ALUNO_ID(alunoId)
      this.SET_TIPO_OCORRENCIA_ID(tipoOcorrenciaId)
      this.SET_USUARIO_ID(usuarioId)
      this.SET_FUNCIONARIO_ID(funcionarioId)
      this.SET_SITUACAO(situcao)
      this.SET_DATA_PROXIMO_CONTATO(data)
      this.SET_HORARIO(horario)
      this.SET_TEXTO(obsevacao)
    },

    montaParametrosEdicao () {
      let funcionarioId = this.funcionario ? this.funcionario.id : null
      let obsevacao = this.obsevacao ? this.obsevacao : null
      let data = this.data ? stringToISODate(this.data) : null
      let horario = this.horario ? this.horario : null
      let situcao = null

      if (this.selecetOcorreciaFechada === true) {
        situcao = 'F'
      }

      this.SET_FUNCIONARIO_ID(funcionarioId)
      this.SET_SITUACAO(situcao)
      this.SET_DATA_PROXIMO_CONTATO(data)
      this.SET_HORARIO(horario)
      this.SET_TEXTO(obsevacao)
    },

    limparDadosESair () {
      this.limparCamposFormulario()
      this.$emit('filtrar')
      this.visibleFormularioOcorrenciaAcademica = false

      if (this.isEdit) {
        this.isEdit = false
      }
    },

    adicionarNovaOcorrecia () {
      this.salvando = true
      this.isValid = true

      if (this.$v.$invalid) {
        this.isValid = false
        this.salvando = false
        return
      }

      if (this.isEdit || this.bSalvar) {
        this.montaParametrosEdicao()
        this.atualizarOcorrencia().then(() => {
          if (this.bSalvarESair) {
            this.limparDadosESair()
          } else {
            if (this.selecetOcorreciaFechada) {
              this.limparDadosESair()
            } else {
             // this.limparCamposFormulario()
              this.buscarPorId(this.copiaId).then(response => {
                this.setNomeAlunoEdicao(this.itemSelecionado.aluno)
                this.setTipoOcorrencia(this.itemSelecionado.tipo_ocorrencia)
                this.setFuncionario(this.itemSelecionado.funcionario)

                this.data = this.itemSelecionado.data_proximo_contato ? dateToString(new Date(this.itemSelecionado.data_proximo_contato)) : ''
                if (this.data) {
                  let horario = this.itemSelecionado.data_proximo_contato.split('T')[1]
                  this.horario = horario.match(/(\d{2,2}):(\d{2,2})/)[0]
                }
              })
            }
          }
        }).finally(() => {
          this.salvando = false
        })
      } else {
        this.montaParametos()
        this.criarOcorrencia().then(() => {
          if (this.bSalvarESair) {
            this.limparDadosESair()
          } else if (this.selecetOcorreciaFechada) {
            this.limparDadosESair()
          } else {
          //  this.limparCamposFormulario()
          }
        })
          .finally(() => {
            this.salvando = false
          })
      }
    },

    pegarNumeroDeMatricula (aluno) {
      this.numeroDeMatricula = null
      if (aluno == null) {
        return
      }

      if (aluno.contratos) {
        let alunoId = aluno.id
        if (aluno.contratos[0]) {
          let contrato = aluno.contratos[0]
          this.numeroDeMatricula = `${alunoId}/${contrato.sequencia_contrato}`
        }
      }
      
    },

    setNomeAlunoEdicao (aluno) {
      this.aluno = aluno.pessoa.nome_contato
      this.pegarNumeroDeMatricula(aluno)
    },

    setNomeAluno (value) {
      this.aluno = value
      this.pegarNumeroDeMatricula(value)
    },

    setTipoOcorrencia (value) {
      this.tipoOcorrencia = value
    },

    setFuncionario (value) {
      this.funcionario = value
    },

    setData (value) {
      this.data = value
    },

    abrirModalConcluir () {
      this.isValid = true
      if (this.$v.$invalid) {
        this.isValid = false
      } else {
        this.modalConfirmarConcluir = true
        this.vemDeModal = true
      }
    }

  }
}
</script>
<style scoped>
.textarea-historico textarea {
  max-height: 200px;
}
</style>
