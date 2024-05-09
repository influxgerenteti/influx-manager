<template>
  <div>
    <g-modal id="formularioAtividadeNivelamento" ref="formularioAtividadeNivelamento" v-model="visibilidadeModalAtividadeNivelamento" size="lg" centered no-close-on-backdrop hide-header hide-footer>
      <div class="animated fadeIn">
        <form :class="{ 'was-validated': !isValid }" class="needs-validation p-3" novalidate @submit.prevent="salvar()">
          <div v-if="estaCarregandoFormulario" class="form-loading">
            <load-placeholder :loading="estaCarregandoFormulario" />
          </div>

          <h5 class="title-module mb-2">Formulário de nivelamento</h5>
          <div class="form-group">
            <div class="row">

              <div class="col-md-6">
                <label v-help-hint="'formulario_atividade_tipo_atividade'" for="formulario_atividade_tipo_atividade" class="col-form-label"> Atividade *</label>
                <input id="formulario_atividade_tipo_atividade" :value="formularioData.item ? formularioData.item.descricao : ''" disabled="true" type="text" class="form-control" >
              </div>

              <div class="col-md-2">
                <label v-help-hint="'formulario_atividade_tipo_data'" for="formulario_atividade_tipo_data" class="col-form-label">Data *</label>
                <template v-if="isReadOnly">
                  <input id="formulario_atividade_tipo_data" v-model="formularioData.data" :disabled="isReadOnly" type="text" class="form-control">
                </template>
                <template v-else>
                  <g-datepicker
                    :element-id="'formulario_atividade_tipo_data'"
                    :value="formularioData.data"
                    :required="true"
                    :selected="setDataObjetoRetorno"
                  />
                </template>
              </div>
              <div class="col-md-4">
                <div class="row">
                  <div class="col-md-6">
                    <label v-help-hint="'formulario_atividade_tipo_hora_inicio'" for="formulario_atividade_tipo_hora_inicio" class="col-form-label">
                      H. Início *
                    </label>
                    <input v-mask="'##:##'" id="formulario_atividade_tipo_hora_inicio" v-model="formularioData.horario_de_inicio" :class="!$v.formularioData.horario_de_inicio.validateHour || !$v.formularioData.horario_de_termino.comparaHora ? 'is-invalid' : null" :disabled="isReadOnly" type="text" class="form-control" maxlength="5" required>
                    <div v-if="$v.formularioData.horario_de_termino.comparaHora" class="invalid-feedback">
                      {{ (!$v.formularioData.horario_de_inicio.validateHour) ? 'Horário inválido' : 'Campo obrigatório' }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label v-help-hint="'formulario_atividade_tipo_hora_termino'" for="formulario_atividade_tipo_hora_termino" class="col-form-label">
                      H. Término *
                    </label>
                    <input v-mask="'##:##'" id="formulario_atividade_tipo_hora_termino" v-model="formularioData.horario_de_termino" :class="!$v.formularioData.horario_de_termino.validateHour || !$v.formularioData.horario_de_termino.comparaHora ? 'is-invalid' : null" :disabled="isReadOnly" type="text" class="form-control" maxlength="5" required>
                    <div v-if="$v.formularioData.horario_de_termino.comparaHora" class="invalid-feedback">
                      {{ (!$v.formularioData.horario_de_termino.validateHour) ? 'Horário inválido' : 'Campo obrigatório' }}
                    </div>
                  </div>
                </div>
                <div v-if="!$v.formularioData.horario_de_termino.comparaHora" class="input-invalid">
                  Horário de término deve ser maior que horário de início.
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label v-help-hint="'formulario_atividade_sala'" for="formulario_atividade_sala" class="col-form-label">
                  Sala *
                </label>
                <g-select
                  id="formulario_atividade_sala"
                  :select="setSala"
                  :value="formularioData.sala"
                  :options="listaDeSala"
                  :required="true"
                  :disabled="isReadOnly"
                  :class="$v.formularioData.sala.$invalid ? 'invalid-input' : 'valid-input'"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id"
                />
              </div>
              <div class="col-md-6">
                <label v-help-hint="'formulario_atividade_usuario'" for="formulario_atividade_usuario" class="col-form-label">Agendado por</label>
                <input id="formulario_atividade_usuario" v-model="formularioData.usuario" disabled type="text" class="form-control" required>
              </div>
            </div>

            <div class="body-sector p-2 mt-3">
              <div class="row">
                <div class="col-md-6">
                  <label v-help-hint="''" for="formulario_nivelamento_nome_interessado" class="col-form-label">
                    Adicionar interessado *
                  </label>
                  <template v-if="bVeioNivelamento === true && !isEdit">
                    <typeahead id="formulario_nivelamento_nome_interessado" ref="typeaheadInteressado" :item-hit="setInteressadoObjeto" source-path="/api/interessado/buscar-nome" key-name="nome" required/>
                  </template>
                  <template v-else>
                    <input id="formulario_nivelamento_nome_interessado" v-model="interessado.nome" class="form-control" required disabled>
                  </template>
                </div>
              </div>
              <template v-if="interessado && interessado.id !== null">

                <div class="table-responsive-sm">

                  <b-row class="header-card-list mb-0">
                    <b-col md="2">
                      <label class="col-form-label">Telefone</label>
                    </b-col>
                    <b-col md="2">
                      <label class="col-form-label">Idade</label>
                    </b-col>
                    <b-col md="2">
                      <label class="col-form-label">Sexo</label>
                    </b-col>
                    <b-col md="6">
                      <label class="col-form-label">Etapa do Funil</label>
                    </b-col>
                  </b-row>

                  <b-row class="body-card-list">
                    <b-col md="2" data-header="Telefone" class="truncate">
                      <span>{{ interessado.telefone_contato ? interessado.telefone_contato : (interessado.telefone_secundario ? interessado.telefone_secundario : 'Não informado') | formatarTelefone }}</span>
                    </b-col>
                    <b-col md="2" data-header="Idade" class="truncate">
                      <span>{{ idadeSelecionada(interessado.idade) }}</span>
                    </b-col>
                    <b-col md="2" data-header="Sexo" class="truncate">
                      <span>{{ sexoSelecionado(interessado.sexo) }}</span>
                    </b-col>
                    <b-col md="6" data-header="Etapa do Funil" class="truncate">
                      <span>{{ interessado.workflow ? interessado.workflow.descricao : 'Não informado' }}</span>
                    </b-col>
                  </b-row>

                </div>

              </template>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <label v-help-hint="'formulario_atividade_responsavel'" for="formulario_atividade_responsavel" class="col-form-label">
                  Responsável pela avaliação *
                </label>
                <g-select
                  id="formulario_atividade_responsavel"
                  :value="formularioData.responsavel"
                  :select="setResponsavel"
                  :options="listaDeFuncionario"
                  :disabled="isReadOnly"
                  :class="$v.formularioData.responsavel.$invalid ? 'invalid-input' : 'valid-input'"
                  :required="true"
                  label="apelido"
                  track-by="id" />
              </div>

              <div class="col-md-6">
                <label v-help-hint="''" for="formulario_nivelamento_livro_interessado" class="col-form-label">
                  Livro
                </label>
                <g-select
                  id="formulario_nivelamento_livro_interessado"
                  :value="livro"
                  :select="setLivro"
                  :options="listaDeLivros"
                  :disabled="isReadOnly"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id" />
              </div>
            </div>
          </div>

          <div class="form-group">
            <div>
              <label v-help-hint="''" for="formulario_nivelamento_followup" class="col-form-label">Follow up *</label>
              <b-form-textarea
                id="formulario_nivelamento_followup "
                v-model="followUpsAdicionados"
                class="full-textarea text-area-height"
                rows="3"
                disabled
              />
            </div>
          </div>

          <div v-if="!isReadOnly" class="form-group row">
            <div class="col-md-12">
              <button type="button" class="btn btn-azul" @click="$refs.followUpInicialNivelamento.show()">
                Follow-up nivelamento
              </button>
            </div>
          </div>

          <div v-if="!isReadOnly" class="row pt-2">
            <div class="col-md-6">
              <b-btn :disabled="salvando" type="submit" variant="verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</b-btn>
              <b-btn :disabled="salvando" type="submit" variant="verde" @click="salvarESair = true">{{ salvando ? 'Salvando...' : 'Salvar e Sair' }}</b-btn>
              <!-- <b-btn :disabled="salvando||!livro" type="button" variant="primary" @click="abrirModalConcluir()">Concluir</b-btn> -->
              <b-btn :disabled="salvando" variant="link" @click="cancelar()">Cancelar</b-btn>
            </div>
            <div class="col-md-6">
              <b-btn v-if="isEdit" :disabled="salvando" class="float-right" variant="outline-danger" @click="abrirModalCancelamento">Cancelar nivelamento</b-btn>
            </div>
          </div>
          <b-btn v-else :disabled="salvando" variant="link" @click="cancelar()">Cancelar</b-btn>
        </form>
      </div>
    </g-modal>

    <!-- Modal Follow Up -->
    <g-modal id="followUpInicialNivelamento" ref="followUpInicialNivelamento" v-model="visibleFollowUpInicialNivelamento" size="lg" centered no-close-on-backdrop hide-header hide-footer @show="executaModalInicialNivelamento">
      <modal-follow-up ref="ffInicialNivelamento" :seta-dados-envio="configuraDadosFollowUpBackEnd" :seta-follow-up-callback="adicionarNovoFollowUp" :tipo-formulario="'NI'" :filtra-formularios="false" @hide="visibleFollowUpInicialNivelamento = false, $refs.followUpInicialNivelamento.hide(), visibilidadeModalAtividadeNivelamento = true"/>
    </g-modal>

    <!-- Modal de confirmação do concluir -->
    <g-modal id="confirmar-concluir" ref="confirmar-concluir" v-model="modalConfirmarConcluir" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="p-3">
        <div class="d-block text-center">
          <p>O nivelamento foi realmente concluído ?</p>
        </div>
        <div class="d-flex justify-content-center">
          <b-btn class="mt-3 mr-3" variant="outline-success" block @click="modalConfirmarConcluir = false, concluido = true, salvar()">Confirmar</b-btn>
          <b-btn class="mt-3" variant="outline-danger" block @click="modalConfirmarConcluir = false, visibilidadeModalAtividadeNivelamento = true">Cancelar</b-btn>
        </div>
      </div>
    </g-modal>

    <!-- Modal de confirmação do cancelar -->
    <g-modal id="modalCancelamentoAtividadeNivelamento" ref="modalCancelamentoAtividadeNivelamento" v-model="modalCancelamentoAtividadeNivelamento" size="sm" title="Cancelamento de atividade extra" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center pt-3">
        <p>Deseja cancelar o nivelamento?</p>
      </div>

      <div class="d-flex justify-content-center pb-3">
        <b-btn :disabled="salvando" variant="vermelho" @click="modalCancelamentoAtividadeNivelamento = false, confirmarCancelamentoAtividadeNivelamento = true, salvar()">Confirmar</b-btn>
        <button type="button" class="btn btn-link" @click="modalCancelamentoAtividadeNivelamento = false, visibilidadeModalAtividadeNivelamento = true">Cancelar</button>
      </div>
    </g-modal>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import { required } from 'vuelidate/lib/validators'
import { validateHour } from '../../utils/validators'
import {dateToString, converteHorarioParaBanco, converteHorarioBancoParaInputText, stringToISODate} from '../../utils/date'
import ModalFollowUp from '../interessados/ModalFollowUp.vue'


const comparaHora = (value, vm) => {
  if (vm.horario_de_termino !== '' && vm.horario_de_inicio !== '') {
    return vm.horario_de_inicio < vm.horario_de_termino
  }

  return true
}

export default {
  name: 'FormularioNivelamento',
  components: {
    ModalFollowUp
  },
  props: {
    bVeioNivelamento: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data () {
    return {
      className: 'rapido-open',
      isValid: true,
      isEdit: false,
      isReadOnly: false,
      salvando: false,
      salvarESair: false,
      visibleFollowUpInicialNivelamento: false,
      visibilidadeModalAtividadeNivelamento: false,
      modalCancelamentoAtividadeNivelamento: false,
      confirmarCancelamentoAtividadeNivelamento: false,
      modalConfirmarConcluir: false,
      concluido: false,
      dadosFollowUps: [],
      followUpsAdicionados: '',
      formularioData: {
        item: null,
        data: '',
        horario_de_inicio: '',
        horario_de_termino: '',
        sala: null,
        usuario: null,
        responsavel: null,
        descricao: 'Nivelamento'
      },
      usuario: null,
      interessado: {
        id: null,
        nome: null
      },
      livro: null,
      followUp: null,
      tipo_atividade: null
    }
  },

  computed: {
    ...mapState('cadastroServico', {listaDeItemRequisicao: 'lista'}),
    ...mapState('funcionario', {listaDeFuncionarioRequisicao: 'lista'}),
    ...mapState('sala', {listaDeSalaRequisicao: 'lista'}),
    ...mapState('nivelamento', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregandoFormulario']),
    ...mapState('livro', {listaDeLivrosRequisicao: 'lista'}),
    ...mapState('root', {usuarioLogado: 'usuarioLogado'}),

    listaDeItem: {
      get () {
        return [{id: null, tipo_item: { tipo: '' }, descricao: 'Selecione'}, ...this.listaDeItemRequisicao.filter(item => item.tipo_item.tipo === 'SN')]
      }
    },

    listaDeFuncionario: {
      get () {
        return this.listaDeFuncionarioRequisicao
      }
    },

    listaDeLivros: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaDeLivrosRequisicao]
      }
    },

    listaDeSala: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaDeSalaRequisicao]
      }
    }
  },

  mounted () {
    this.listarCamposSelects()
    this.formularioData.usuario = this.usuarioLogado ? this.usuarioLogado.nome : null
    this.setAtividadeNivelamento()
  },

  validations: {
    formularioData: {
      item: {required},
      data: {required},
      horario_de_inicio: {required, validateHour},
      horario_de_termino: {required, validateHour, comparaHora},
      sala: {required},
      responsavel: {required}
    },
    interessado: {
      id: {required},
      nome: {required}
    }
  },
  methods: {
    ...mapMutations('nivelamento', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO_FORMULARIO']),
    ...mapActions('nivelamento', {buscarNivelamento: 'buscar', criar: 'criar', atualizar: 'atualizar'}),

    setAtividadeNivelamento () {
      if (this.listaDeItem.length > 1) {
        this.formularioData.item = this.listaDeItem[1]
        return
      }

      setTimeout(() => { this.setAtividadeNivelamento() }, 100)
    },

    listarCamposSelects () {
      this.$store.commit('livro/SET_PAGINA_ATUAL', 1)
      this.$store.commit('livro/SET_LISTA', [])
      this.$store.dispatch('livro/listar')

      this.$store.commit('sala/SET_PAGINA_ATUAL', 1)
      this.$store.commit('sala/SET_LISTA', [])
      this.$store.dispatch('sala/listar', { sala_franqueada: true, apenas_sala_ativa: true })

      this.$store.commit('cadastroServico/SET_LISTA', [])
      this.$store.commit('cadastroServico/SET_PAGINA_ATUAL', 1)
      this.$store.commit('cadastroServico/SET_FILTRO_FRANQUEADA', [this.$store.state.root.usuarioLogado.franqueadaSelecionada])
      this.$store.dispatch('cadastroServico/listar').then((lista) => {
        this.setTipoAtividade(this.listaDeItem.find(item => item.tipo_item.tipo === 'SN'))
      })
    },

    idadeSelecionada (idade) {
      if ((idade === undefined) || (idade === null)) {
        return 'Não informado'
      } else if ((idade >= 6) && (idade <= 12)) {
        return idade + 'anos'
      } else if (idade === 13) {
        return idade + 'ou 14 anos'
      } else if (idade === 15) {
        return idade + 'ou 16 anos'
      } else if (idade === 17) {
        return 'Acima de 17 anos'
      }
    },

    removerInteressado () {
      this.interessado = null
      this.$refs.typeaheadInteressado.resetSelection()
    },

    sexoSelecionado (sexo) {
      switch (sexo) {
        case 'M':
          return 'Masculino'
        case 'F':
          return 'Feminino'
      }
    },

    setTipoAtividade (value) {
      this.formularioData.item = value && value.id == null ? null : value
    },

    setResponsavelPelaExecucao (value) {
      const index = this.formularioData.responsavel.indexOf(value)
      if (index === -1) {
        this.formularioData.responsavel.push(value)
        return
      }

      this.formularioData.responsavel.splice(index, 1)
    },

    setResponsavel (value) {
      this.formularioData.responsavel = value.id == null ? null : value
    },

    setSala (value) {
      this.formularioData.sala = value.id == null ? null : value
    },

    setDataObjetoRetorno (value) {
      this.formularioData.data = value
    },

    setInteressadoObjeto (objetoInteressado) {
      this.interessado = objetoInteressado
    },

    openEdit (id) {
      // Editar
      if (id) {
        this.isEdit = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscarNivelamento().then(() => {
          const data = dateToString(new Date(this.itemSelecionado.data_hora_inicio))
          let horarioInicio = converteHorarioBancoParaInputText(this.itemSelecionado.data_hora_inicio)
          let horarioFinal = converteHorarioBancoParaInputText(this.itemSelecionado.data_hora_fim)

          this.formularioData = {
            item: this.itemSelecionado.item,
            data: data,
            horario_de_inicio: horarioInicio,
            horario_de_termino: horarioFinal,
            sala: this.itemSelecionado.sala_franqueada.sala,
            usuario: this.itemSelecionado.usuario_solicitante.nome,
            responsavel: this.itemSelecionado.responsaveis_execucacao,
            descricao: this.itemSelecionado.descricao_atividade
          }
          this.livro = this.itemSelecionado.interessadoAtividadeExtra.livro ? this.itemSelecionado.interessadoAtividadeExtra.livro : null
          this.interessado = this.itemSelecionado.interessadoAtividadeExtra.interessado
          if (this.itemSelecionado.interessadoAtividadeExtra.interessado.followupComercials !== undefined) {
            let arrayFollowUpComercial = this.itemSelecionado.interessadoAtividadeExtra.interessado.followupComercials
            arrayFollowUpComercial.sort((a, b) => {
              return a.id > b.id
            })
            arrayFollowUpComercial.forEach((item) => {
              this.adicionarNovoFollowUp(item.followup)
            })
          }
        })
      }
    },

    setNomeInteressado (value) {
      this.interressado = value
    },

    setLivro (value) {
      this.livro = value.id == null ? null : value
    },

    limparCamposDoData () {
      this.isValid = true
      this.isEdit = false
      this.salvando = false
      this.salvarESair = false
      this.isReadOnly = false
      this.visibleFollowUpInicialNivelamento = false
      this.modalCancelamentoAtividadeNivelamento = false
      this.confirmarCancelamentoAtividadeNivelamento = false
      this.modalConfirmarConcluir = false
      this.concluido = false
      this.dadosFollowUps = []
      this.followUpsAdicionados = ''
      this.formularioData = {
        // item: null,
        data: '',
        horario_de_inicio: '',
        horario_de_termino: '',
        sala: null,
        usuario: this.usuarioLogado.nome,
        responsavel: null,
        descricao: 'Nivelamento'
      }
      this.usuario = null
      if (this.$refs.typeaheadInteressado) {
        this.$refs.typeaheadInteressado.resetSelection()
      }
      this.interessado = {
        id: null,
        nome: null
      }
      this.livro = null
      this.followUp = null
      this.setAtividadeNivelamento()
    },

    sair () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.limparCamposDoData()
      if (this.bVeioNivelamento === true) {
        this.$emit('callbacklistagem')
      }
    },

    cancelar () {
      this.visibilidadeModalAtividadeNivelamento = false
      this.LIMPAR_ITEM_SELECIONADO()
      this.limparCamposDoData()
    },

    montarParametros () {
      if (this.itemSelecionadoID) {
        // EDITAR NIVELAMENTO
        const itemId = this.formularioData.item ? this.formularioData.item.id : null
        const data = this.formularioData.data ? stringToISODate(this.formularioData.data) : null
        const mInicio = this.formularioData.horario_de_inicio ? converteHorarioParaBanco(this.formularioData.horario_de_inicio) : null
        const mTermino = this.formularioData.horario_de_termino ? converteHorarioParaBanco(this.formularioData.horario_de_termino) : null
        const salaObj = this.listaDeSala.find(item => (item.id === this.formularioData.sala.id))
        const salaId = salaObj ? salaObj.salaFranqueadaId : null
        const usuarioId = this.itemSelecionado.usuario_solicitante ? this.itemSelecionado.usuario_solicitante.id : null
        const interessadoId = this.interessado ? this.interessado.id : null
        const livroId = this.livro ? this.livro.id : null
        const listaDeFuncionarioId = this.formularioData.responsavel ? this.formularioData.responsavel.map(i => i.id) : null
        const descricaoAtividade = this.formularioData.descricao ? this.formularioData.descricao : null

        let nivelamentoId = this.itemSelecionadoID
        this.LIMPAR_ITEM_SELECIONADO()
        this.SET_ITEM_SELECIONADO_ID(nivelamentoId)

        let objetoNivelamento = {}
        objetoNivelamento.item = itemId
        objetoNivelamento.data = data
        objetoNivelamento.usuario = usuarioId
        objetoNivelamento.sala_franqueada = salaId
        objetoNivelamento.descricao_atividade = descricaoAtividade
        objetoNivelamento.hora_inicio = mInicio
        objetoNivelamento.hora_final = mTermino
        objetoNivelamento.concluido = this.concluido
        objetoNivelamento.cancelamento = this.confirmarCancelamentoAtividadeNivelamento
        objetoNivelamento.interessado = interessadoId
        objetoNivelamento.livro = livroId
        objetoNivelamento.responsaveis_execucao = listaDeFuncionarioId
        objetoNivelamento.follow_ups = this.dadosFollowUps ? this.dadosFollowUps : null

        this.$store.commit('nivelamento/SET_ITEM_SELECIONADO', objetoNivelamento)
      } else {
        const itemId = this.formularioData.item ? this.formularioData.item.id : null
        const data = this.formularioData.data ? stringToISODate(this.formularioData.data) : null
        const usuarioId = this.usuarioLogado ? this.usuarioLogado.id : null
        const mInicio = this.formularioData.horario_de_inicio ? converteHorarioParaBanco(this.formularioData.horario_de_inicio) : null
        const mTermino = this.formularioData.horario_de_termino ? converteHorarioParaBanco(this.formularioData.horario_de_termino) : null
        const salaId = this.formularioData.sala ? this.formularioData.sala.id : null
        const descricaoAtividade = this.formularioData.descricao ? this.formularioData.descricao : null
        const funcionarioId = this.formularioData.responsavel ? this.formularioData.responsavel.id : null
        const interressadoId = this.interessado ? this.interessado.id : null
        const livroId = this.livro ? this.livro.id : null

        // Paramentros para novo nivelameto
        let objetoNivelamento = {}
        objetoNivelamento.item = itemId
        objetoNivelamento.usuario = usuarioId
        objetoNivelamento.sala_franqueada = salaId
        objetoNivelamento.descricao_atividade = descricaoAtividade
        objetoNivelamento.data = data
        objetoNivelamento.hora_inicio = mInicio
        objetoNivelamento.hora_final = mTermino
        objetoNivelamento.concluido = this.concluido
        objetoNivelamento.interessado = interressadoId
        objetoNivelamento.livro = livroId
        objetoNivelamento.responsaveis_execucao = [funcionarioId]
        objetoNivelamento.follow_ups = this.dadosFollowUps ? this.dadosFollowUps : null

        this.$store.commit('nivelamento/SET_ITEM_SELECIONADO', objetoNivelamento)
      }
    },

    executaModalInicialNivelamento () {
      this.$refs.ffInicialNivelamento.executaRequisicoes()
    },

    configuraDadosFollowUpBackEnd (arrayInformacoes) {
      this.dadosFollowUps.push(arrayInformacoes)
    },

    adicionarNovoFollowUp (novoFollowUp) {
      if ((novoFollowUp != undefined) && (novoFollowUp != '')) {
        console.log(novoFollowUp)
        if (this.followUpsAdicionados.length > 0) {
          novoFollowUp += '\n' + this.followUpsAdicionados
        } else {
          novoFollowUp += this.followUpsAdicionados
        }
        this.followUpsAdicionados = novoFollowUp
      }
    },

    abrirModalConcluir () {
      if (this.$v.$invalid) {
        this.isValid = false
      } else {
        this.modalConfirmarConcluir = true
      }
    },

    abrirModalCancelamento () {
      if (this.$v.$invalid) {
        this.isValid = false
      } else {
        this.modalCancelamentoAtividadeNivelamento = true
      }
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.salvando = true
      this.montarParametros()

      if (this.itemSelecionadoID) {
        let id = this.itemSelecionadoID
        this.atualizar()
          .then(() => {
            if (this.salvarESair || this.concluido || this.confirmarCancelamentoAtividadeNivelamento) {
              this.$emit('hide')
              this.cancelar()
              if (this.bVeioNivelamento === true) {
                this.$emit('callbacklistagem')
              }
            } else {
              this.LIMPAR_ITEM_SELECIONADO()
              this.limparCamposDoData()

              if (this.bVeioNivelamento === true) {
                this.$emit('callbacklistagem')
              }
              this.openEdit(id)
            }

            this.salvando = false
          })
          .catch(() => {
            this.salvando = false
          })
      } else {
        this.criar()
          .then(() => {
            if (this.salvarESair || this.concluido) {
              this.$emit('hide')
              this.cancelar()
              this.$emit('filtra')
              if (this.bVeioNivelamento === true) {
                this.$emit('callbacklistagem')
              }
            } else {
              this.LIMPAR_ITEM_SELECIONADO()
              this.limparCamposDoData()
              if (this.bVeioNivelamento === true) {
                this.$emit('callbacklistagem')
              }
            }
          })
          .catch(() => {
            this.salvando = false
          })
      }
    }

  }
}
</script>
<style scoped>
.text-area-height {
  max-height: 120px;
}

.main .container-fluid .animated .table-responsive-sm, .main .container-fluid form .table-responsive-sm {
  min-height: auto;
}
</style>
