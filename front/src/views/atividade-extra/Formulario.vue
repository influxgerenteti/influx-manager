<template>
  <div>
    <b-modal id="formularioAtividadeExtra" ref="formularioAtividadeExtra" v-model="visibilidadeModalAtividadeExtra" size="lg" centered no-close-on-backdrop hide-header hide-footer @hide="limparCamposDoData()">
      <div class="animated fadeIn">
        <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
          <div v-if="isEdit" class="form-loading">
            <load-placeholder :loading="estaCarregando" />
          </div>

          <h5 class="title-module mb-2">Atividade Extra e Retake</h5>
          <div class="form-group">
            <formulario-atividade :formulario="'atividade_extra'" :read-only="isReadOnly" :retorno-objetos="formularioData" :edit="isEdit" @verificarLista="verificarLista"/>
          </div>

          <div class="form-group">
            <div class="content-sector-extra p-2 box-scroll">
              <div v-if="!isReadOnly" class="row mb-3">
                <div class="col-md-6">
                  <label v-help-hint="''" for="formulario_atividade_extra_nome_aluno" class="col-form-label">Aluno</label>
                  <typeahead id="formulario_atividade_extra_nome_aluno" ref="refFormularioAluno" :item-hit="setNomeAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato"/>
                </div>
              </div>

              <template v-if="listaDeAlunosParticipantes.length > 0">
                <b-row class="header-card-list mb-0">
                  <b-col md="6">
                    <label class="col-form-label">Nome</label>
                  </b-col>
                  <b-col md="5">
                    <label class="col-form-label">Frequência</label>
                  </b-col>
                  <b-col md="1"/>
                </b-row>

                <div class="row data-scroll">
                  <perfect-scrollbar class="scroller col-12">
                    <template v-for="(aluno) in listaDeAlunosParticipantes">
                      <b-row v-if="!aluno.removido" :key="aluno.id" class="body-card-list">
                        <b-col md="6" class="truncate" data-header="Nome">{{ aluno.nome }}</b-col>
                        <b-col md="5">
                          <b-form-checkbox-group :id="`presenca-aluno-${aluno.id}`" v-model="aluno.presenca" name="presenca" class="check-presenca" @change="setPresenca($event, aluno.presenca)">
                            <b-form-checkbox :disabled="isReadOnly || aluno.presenca[0] === 'P'" value="P">P</b-form-checkbox>
                            <b-form-checkbox :disabled="isReadOnly || aluno.presenca[0] === 'F'" value="F">F</b-form-checkbox>
                          </b-form-checkbox-group>
                        </b-col>
                        <b-col v-if="!isReadOnly" md="1">
                          <a v-b-tooltip.viewport.left.hover class="icone-link" title="Remover" @click="removerAlunoDaLista(aluno)">
                            <font-awesome-icon icon="trash" />
                          </a>
                        </b-col>
                      </b-row>
                    </template>
                  </perfect-scrollbar>
                </div>
              </template>
            </div>
            <!--
            <div class="row mt-2">
              <div class="col-md-12">
                <div v-if="mensagem && listaDeAlunosParticipantes.length" class="form-group list-group-accent">
                  <div class="list-group-item list-group-item-accent-warning  list-group-item-warning border-0">
                    {{ mensagem }}.
                  </div>
                </div>
              </div>
            </div> -->

          </div>

          <div class="content-sector-extra p-2 box-scroll">
            <h5 class="title-module mb-2 px-2">Convidados</h5>

            <b-row class="header-card-list mx-2 mb-0">
              <b-col md="4">
                <label class="col-form-label">Nome *</label>
              </b-col>
              <b-col md="4">
                <label class="col-form-label">Telefone</label>
              </b-col>
              <b-col md="2">
                <label class="col-form-label">Compareceu</label>
              </b-col>
              <b-col md="1"/>
              <b-col md="1"/>
            </b-row>

            <div class="row data-scroll">
              <perfect-scrollbar class="scroller col-12">
                <b-row v-for="(item, index) in listaDeConvidados" :key="index" class="body-card-list mx-2">
                  <b-col md="4" data-header="Nome">
                    <input :id="`nome-convidado-${index}`" v-model="item.nome" :class="{ 'invalid-list' : !isValid && $v.listaDeConvidados.$each[index].$invalid }" type="text" class="form-control">
                  </b-col>
                  <b-col md="4" data-header="Telefone *">
                    <input v-mask="['(##) #####-####']" :id="`telefone_convidado_${index}`" v-model="item.telefone" type="text" class="form-control">
                  </b-col>
                  <b-col md="2">
                    <b-form-checkbox-group :id="`presenca-convidado-${index}`" v-model="item.presenca" name="presenca" class="check-presenca">
                      <b-form-checkbox :disabled="item.presenca === 'P'" value="P">S</b-form-checkbox>
                      <b-form-checkbox :disabled="item.presenca === 'F'" value="F">N</b-form-checkbox>
                    </b-form-checkbox-group>
                  </b-col>
                  <b-col md="1">
                    <a v-b-tooltip.viewport.left.hover class="icone-link" title="Remover" @click="excluirConvidado(index, item)">
                      <font-awesome-icon icon="trash" />
                    </a>
                  </b-col>
                  <b-col md="1">
                    <b-btn v-if="index === (listaDeConvidados.length - 1)" variant="azul" class="btn-40" @click="adicionarConvidado()">
                      <font-awesome-icon icon="plus" />
                    </b-btn>
                  </b-col>
                </b-row>
              </perfect-scrollbar>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-12">
              <div v-if="mensagem && (listaDeAlunosParticipantes.length || listaDeConvidados.length) " class="form-group list-group-accent">
                <div class="list-group-item list-group-item-accent-warning  list-group-item-warning border-0">
                  {{ mensagem }}.
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <b-form-checkbox v-help-hint="''" :disabled="isReadOnly" v-model="isento">Atividade Isenta</b-form-checkbox>
              </div>
            </div>
            <div v-if="!isento" class="row">
              <div class="col-md-6">
                <label v-help-hint="''" for="formulario_atividade_forma_cobranca" class="col-form-label">Forma de cobrança{{ !isento ? '*' : '' }}</label>
                <g-select
                  id="formulario_atividade_forma_cobranca"
                  :select="setFormaDeCobranca"
                  :value="forma_cobranca"
                  :options="listaDeFormaCobranca"
                  :required="true"
                  :class="$v.forma_cobranca.$invalid ? 'invalid-input' : 'valid-input'"
                  :disabled="isReadOnly"
                  label="descricao"
                  track-by="id"
                />
              </div>
              <div class="col-md-2">
                <label v-help-hint="''" for="formulario_atividade_valor_do_item" class="col-form-label">Valor</label>
                <vue-numeric id="formulario_atividade_valor_do_item" :precision="2" :empty-value="null" v-model="formularioData.valor" :max="9999999.99" :disabled="isReadOnly" separator="." class="form-control"/>
              </div>
            </div>
          </div>

          <template v-if="!ver">
            <div class="form-group row pt-2">
              <div class="col-md-6">
                <b-btn v-if="!isReadOnly" :disabled="salvando || isReadOnly" type="submit" variant="verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</b-btn>
                <b-btn v-if="!isReadOnly" :disabled="salvando || isReadOnly" type="submit" variant="verde" @click="salvarESair = true">{{ salvando ? 'Salvando...' : 'Salvar e Sair' }}</b-btn>
                <!-- <b-btn v-if="!isReadOnly" :disabled="salvando || isReadOnly" type="button" variant="primary" @click="abrirModalConcluir()">Concluir</b-btn> -->
                <b-btn :disabled="salvando" variant="link" @click="cancelar()">Cancelar</b-btn>
              </div>
              <div v-if="!isReadOnly" class="col-md-6">
                <b-btn v-if="isEdit" :disabled="salvando || isReadOnly" class="float-right" variant="outline-danger" @click="abrirModalCancelar()">Cancelar atividade extra</b-btn>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="form-group row pt-2">
              <div class="col-md-6">
                <b-btn :disabled="salvando " variant="link" @click="cancelar()">Cancelar</b-btn>
              </div>
            </div>
          </template>

        </form>
      </div>
    </b-modal>

    <!-- Modal de confirmação do concluir -->
    <b-modal id="confirmar-concluir" ref="confirmar-concluir" v-model="modalConfirmarConcluir" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center">
        <p>Atividade extra foi realmente concluída?</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn class="mt-3 mr-3" variant="outline-success" block @click="modalConfirmarConcluir = false, concluido = true, salvar()">Confirmar</b-btn>
        <b-btn class="mt-3" variant="outline-danger" block @click="modalConfirmarConcluir = false, visibilidadeModalAtividadeExtra = true">{{ ver ? "Fechar" : "Cancelar" }}</b-btn>
      </div>
    </b-modal>

    <!-- Modal de confirmação do cancelar -->
    <b-modal id="modalCancelamentoAtividadeExtra" ref="modalCancelamentoAtividadeExtra" v-model="modalCancelamentoAtividadeExtra" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center">
        <p>Deseja cancelar atividade extra ?</p>
        <p>Esta ação não poderá ser desfeita</p>
      </div>

      <div class="d-flex justify-content-center">
        <b-btn :disabled="salvando" variant="vermelho" @click="modalCancelamentoAtividadeExtra = false, confirmarCancelamentoAtividadeExtra = true, salvar()">Confirmar</b-btn>
        <button type="button" class="btn btn-link" @click="modalCancelamentoAtividadeExtra = false,visibilidadeModalAtividadeExtra = true">Cancelar</button>
      </div>
    </b-modal>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import FormularioAtividade from './FormularioAtividade'
import {required, requiredIf} from 'vuelidate/lib/validators'
import { validateHour } from '../../utils/validators'
import {stringToISODate, converteHorarioParaBanco, converteHorarioBancoParaInputText, dateToString} from '../../utils/date'

export default {
  name: 'FormularioAtiviadeExtra',
  components: {
    FormularioAtividade
  },
  data () {
    return {
      idTempo: null,
      isReadOnly: false,
      isValid: true,
      isEdit: false,
      salvando: false,
      salvarESair: false,
      ver: false,
      vemDeModal: false,
      visibilidadeModalAtividadeExtra: false,
      modalCancelamentoAtividadeExtra: false,
      confirmarCancelamentoAtividadeExtra: false,
      modalConfirmarConcluir: false,
      concluido: false,
      isento: false,
      aluno: null,
      listaDeAlunosParticipantes: [],
      listaDeConvidados: [
        {
          nome: '',
          telefone: '',
          presenca: 'P'
        }
      ],
      mensagem: null,
      situacaPresenca: [
        {value: 'F', text: 'F'},
        {value: 'P', text: 'P'}
      ],
      formularioData: {
        item: null,
        data: '',
        horario_de_inicio: '',
        horario_de_termino: '',
        sala: null,
        max_aluno: null,
        usuario: null,
        responsavel: [],
        descricao: null,
        valor: 0
      },
      forma_cobranca: null,
      usuario: null
    }
  },
  computed: {
    ...mapState('atividadeExtra', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('formaPagamento', {listaDeFormaPagamentoRequisicao: 'lista'}),
    ...mapState('root', {usuarioLogado: 'usuarioLogado'}),

    listaDeFormaCobranca: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaDeFormaPagamentoRequisicao]
      }
    }
  },
  watch: {
    listaDeAlunosParticipantes (value) {
      let listaDeAlunos = value.filter(aluno => aluno.removido === false)

      this.mensagem = null
      if (this.formularioData.max_aluno) {
        if (listaDeAlunos.length + this.listaDeConvidados.length > this.formularioData.max_aluno) {
          this.mensagem = `Ultrapassado o limite máxima (${this.formularioData.max_aluno}) de alunos para atividade`
        }
      }
    },

    listaDeConvidados (value) {
      let listaDeAlunos = this.listaDeAlunosParticipantes.filter(aluno => aluno.removido === false)

      this.mensagem = null
      if (this.formularioData.max_aluno) {
        if (listaDeAlunos.length + this.listaDeConvidados.length > this.formularioData.max_aluno) {
          this.mensagem = `Ultrapassado o limite máxima (${this.formularioData.max_aluno}) de alunos para atividade`
        }
      }
    }
  },
  mounted () {
    this.listarCamposSelects()
    this.formularioData.usuario = this.usuarioLogado.nome
  },
  validations: {
    formularioData: {
      item: {required},
      data: {required},
      horario_de_inicio: {validateHour},
      horario_de_termino: {validateHour},
      sala: {required},
      // max_aluno: {required},
      responsavel: {required}
    },
    forma_cobranca: {
      required: requiredIf(function () {
        return this.isento === false || this.isento === null
      })
    },
    listaDeConvidados: {
      $each: {
        nome: {
          required: requiredIf(function ($each) {
            return this.notNull($each.telefone) === true && this.notNull($each.nome) === false
          })
        }
      }
    }
  },
  methods: {
    ...mapMutations('atividadeExtra', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('atividadeExtra', ['buscar', 'criar', 'atualizar']),

    listarCamposSelects () {
      this.isento = true
      this.$store.commit('formaPagamento/SET_PAGINA_ATUAL', 1)
      this.$store.commit('formaPagamento/SET_LISTA', [])
      this.$store.dispatch('formaPagamento/getLista')
    },

    limparMensagem () {
      this.mensagem = null
    },

    openEdit (id) {
      if (id) {
        // Editar
        this.isEdit = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscar().then(() => {
          let horarioInicio = converteHorarioBancoParaInputText(this.itemSelecionado.data_hora_inicio)
          let horarioFinal = converteHorarioBancoParaInputText(this.itemSelecionado.data_hora_fim)
          this.usuario = this.itemSelecionado.usuario_solicitante

          this.formularioData = {
            item: this.itemSelecionado.item,
            data: dateToString(new Date(this.itemSelecionado.data_hora_inicio)),
            horario_de_inicio: horarioInicio,
            horario_de_termino: horarioFinal,
            sala: this.itemSelecionado.sala_franqueada.sala,
            max_aluno: this.itemSelecionado.quantidade_maxima_alunos ? this.itemSelecionado.quantidade_maxima_alunos : null,
            usuario: this.itemSelecionado.usuario_solicitante.nome,
            responsavel: this.itemSelecionado.responsaveis_execucacao,
            descricao: this.itemSelecionado.descricao_atividade,
            valor: this.itemSelecionado.valor ? this.itemSelecionado.valor * 1 : 0
          }
          this.forma_cobranca = this.itemSelecionado.forma_cobranca ? this.itemSelecionado.forma_cobranca : null
          this.isento = this.itemSelecionado.isenta ? this.itemSelecionado.isenta : null

          this.listaDeAlunosParticipantes = this.itemSelecionado.alunoAtividadeExtras.map(aluno => {
            let obj = {}
            obj.atividadeExtraId = aluno.id
            obj.id = aluno.aluno.id
            obj.nome = aluno.aluno.pessoa.nome_contato
            obj.presenca = aluno.presenca ? [aluno.presenca] : null
            obj.removido = aluno.removido
            return obj
          })

          this.listaDeConvidados = this.itemSelecionado.convidadoAtividadeExtras.length > 0 ? this.itemSelecionado.convidadoAtividadeExtras.map((convidado) => {
            return convidado
          }) : [{nome: '', telefone: '', presenca: 'P'}]
        })
      }
    },

    setNomeAluno (value) {
      if (value) {
        let aluno = {}
        aluno.id = value.id
        aluno.nome = value.pessoa.nome_contato
        aluno.presenca = ['P']
        aluno.contratos = value.contratos
        aluno.removido = false

        if (this.alunoExisteNaLista(aluno.id) === false) {
          this.listaDeAlunosParticipantes.push(aluno)
        }

        this.$refs.refFormularioAluno.resetSelection()
      }
    },

    alunoExisteNaLista (id) {
      let alunoExisteNaLista = false

      this.listaDeAlunosParticipantes.forEach(aluno => {
        if (aluno.id === id) {
          if (this.isEdit) {
            aluno.removido = false
          }
          alunoExisteNaLista = true
        }
      })

      return alunoExisteNaLista
    },

    removerAlunoDaLista (aluno) {
      if (this.isEdit) {
        aluno.removido = true
      } else {
        const idParaRemover = aluno.id
        this.listaDeAlunosParticipantes = this.listaDeAlunosParticipantes.filter(aluno => aluno.id !== idParaRemover)
        this.limparMensagem()
      }

      this.verificarLista()
    },

    setPresenca (value, oldVal) {
      if (oldVal && oldVal.length) {
        value.splice(oldVal[0], 1)
      }
    },

    setFormaDeCobranca (value) {
      this.forma_cobranca = value.id === null ? null : value
    },

    limparCamposDoData () {
      if (this.vemDeModal) {
        this.vemDeModal = false
        return
      }

      this.modalCancelamentoAtividadeExtra = false
      this.confirmarCancelamentoAtividadeExtra = false
      this.modalConfirmarConcluir = false
      this.concluido = false
      this.isValid = true
      this.isEdit = false
      this.salvando = false
      this.salvarESair = false
      this.ver = false
      this.isento = true
      this.aluno = null
      this.listaDeAlunosParticipantes = []
      this.listaDeConvidados = [{nome: '', telefone: '', presenca: 'P'}]
      this.mensagem = null
      this.formularioData = {
        item: null,
        data: '',
        horario_de_inicio: '',
        horario_de_termino: '',
        sala: null,
        max_aluno: null,
        usuario: this.usuarioLogado.nome,
        responsavel: [],
        descricao: null,
        valor: 0
      }
      this.forma_cobranca = null
      this.usuario = null
    },

    cancelar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.limparCamposDoData()

      this.$emit('cancelar')
      this.$emit('filtra')
    },

    montarParametros () {
      if (this.itemSelecionadoID) {
        // Editar
        const itemId = this.formularioData.item ? this.formularioData.item.id : null

        const data = this.formularioData.data ? stringToISODate(this.formularioData.data) : null
        const mInicio = converteHorarioParaBanco(this.formularioData.horario_de_inicio)
        const mTermino = converteHorarioParaBanco(this.formularioData.horario_de_termino)

        const salaId = this.formularioData.sala ? (this.formularioData.sala.salaFranqueadaId ? this.formularioData.sala.salaFranqueadaId : (this.itemSelecionado.sala_franqueada.id ? this.itemSelecionado.sala_franqueada.id : null)) : null
        const maxAlunos = this.formularioData.max_aluno ? this.formularioData.max_aluno : null
        const usuarioId = this.usuario ? this.usuario.id : null

        const listaDeFuncionarioId = this.formularioData.responsavel ? this.formularioData.responsavel.map(responsavel => responsavel.id) : null
        const descricaoAtividade = this.formularioData.descricao ? this.formularioData.descricao : null

        let listaDeAlunosParam = []
        if (this.listaDeAlunosParticipantes.length > 0) {
          listaDeAlunosParam = this.listaDeAlunosParticipantes.map(aluno => {
            let obj = {
              aluno: aluno.id,
              atividade_extra: this.itemSelecionadoID,
              presenca: aluno.presenca[0],
              removido: aluno.removido ? 1 : 0
            }

            if (aluno.atividadeExtraId) {
              obj.id = aluno.atividadeExtraId
            }

            return obj
          })
        }

        let listaDeConvidados = this.listaDeConvidados.filter((convidado) => {
          return this.notNull(convidado.nome)
        })

        const formaPagamento = this.forma_cobranca ? this.forma_cobranca.id : null
        const valor = this.formularioData.valor ? this.formularioData.valor : null

        let atividadeExtraId = this.itemSelecionadoID
        this.LIMPAR_ITEM_SELECIONADO()
        this.SET_ITEM_SELECIONADO_ID(atividadeExtraId)

        this.$store.commit('atividadeExtra/SET_ITEM_ID', atividadeExtraId)
        this.$store.commit('atividadeExtra/SET_ITEM', itemId)
        this.$store.commit('atividadeExtra/SET_DATA', data)
        this.$store.commit('atividadeExtra/SET_HORARIO_INICIO', mInicio)
        this.$store.commit('atividadeExtra/SET_HORARIO_FINAL', mTermino)
        this.$store.commit('atividadeExtra/SET_SALA_FRANQUEADA', salaId)
        this.$store.commit('atividadeExtra/SET_MAXIMO_DE_ALUNOS', maxAlunos)
        this.$store.commit('atividadeExtra/SET_USUARIO', usuarioId)
        this.$store.commit('atividadeExtra/SET_RESPONSAVEL', listaDeFuncionarioId)
        this.$store.commit('atividadeExtra/SET_DESCRICAO_ATIVIDADE', descricaoAtividade)
        this.$store.commit('atividadeExtra/SET_DADOS_ALUNO', listaDeAlunosParam)
        this.$store.commit('atividadeExtra/SET_DADOS_CONVIDADOS', listaDeConvidados)
        this.$store.commit('atividadeExtra/SET_ISENTA', this.isento)
        this.$store.commit('atividadeExtra/SET_FORMA_COBRANCA', formaPagamento)
        this.$store.commit('atividadeExtra/SET_VALOR', valor)
        this.$store.commit('atividadeExtra/SET_CONCLUIDO', this.concluido)
        this.$store.commit('atividadeExtra/SET_CANCELAMENTO', this.confirmarCancelamentoAtividadeExtra)
      } else {
        const itemId = this.formularioData.item ? this.formularioData.item.id : null
        const usuarioId = this.usuarioLogado ? this.usuarioLogado.id : null

        const data = this.formularioData.data ? stringToISODate(this.formularioData.data) : null
        const mInicio = converteHorarioParaBanco(this.formularioData.horario_de_inicio)
        const mTermino = converteHorarioParaBanco(this.formularioData.horario_de_termino)

        const salaId = this.formularioData.sala ? this.formularioData.sala.salaFranqueadaId : null
        const maxAlunos = this.formularioData.max_aluno ? this.formularioData.max_aluno : null
        const descricaoAtividade = this.formularioData.descricao ? this.formularioData.descricao : null

        const listaDeFuncionarioId = this.formularioData.responsavel.map(responsavel => responsavel.id)

        let listaDeAlunosParam = []
        if (this.listaDeAlunosParticipantes.length > 0) {
          listaDeAlunosParam = this.listaDeAlunosParticipantes.map(aluno => {
            let obj = {}
            obj.aluno = aluno.id
            obj.presenca = aluno.presenca[0]
            return obj
          })
        }

        let listaDeConvidados = this.listaDeConvidados.filter((convidado) => {
          return this.notNull(convidado.nome)
        })

        const formaPagamento = this.forma_cobranca ? this.forma_cobranca.id : null
        const valor = this.formularioData.valor ? this.formularioData.valor : null

        // Paramentros atividade Extra
        this.$store.commit('atividadeExtra/SET_ITEM', itemId)
        this.$store.commit('atividadeExtra/SET_DATA', data)
        this.$store.commit('atividadeExtra/SET_HORARIO_INICIO', mInicio)
        this.$store.commit('atividadeExtra/SET_HORARIO_FINAL', mTermino)
        this.$store.commit('atividadeExtra/SET_SALA_FRANQUEADA', salaId)
        this.$store.commit('atividadeExtra/SET_MAXIMO_DE_ALUNOS', maxAlunos)
        this.$store.commit('atividadeExtra/SET_USUARIO', usuarioId)
        this.$store.commit('atividadeExtra/SET_RESPONSAVEL', listaDeFuncionarioId)
        this.$store.commit('atividadeExtra/SET_DESCRICAO_ATIVIDADE', descricaoAtividade)
        this.$store.commit('atividadeExtra/SET_DADOS_ALUNO', listaDeAlunosParam)
        this.$store.commit('atividadeExtra/SET_DADOS_CONVIDADOS', listaDeConvidados)
        this.$store.commit('atividadeExtra/SET_ISENTA', this.isento)
        this.$store.commit('atividadeExtra/SET_FORMA_COBRANCA', formaPagamento)
        this.$store.commit('atividadeExtra/SET_VALOR', valor)
        this.$store.commit('atividadeExtra/SET_CONCLUIDO', this.concluido)
      }
    },

    notNull (a) {
      if (a !== '' && !(a.match(/^\s+$/))) {
        return true
      } else {
        return false
      }
    },

    salvar () {
      if (this.$v.$invalid || (this.itemSelecionado.situacao !== 'P' && this.isReadOnly)) {
        this.isValid = false
        return
      }

      this.salvando = true
      this.montarParametros()

      if (this.itemSelecionadoID) {
        this.atualizar().then(() => {
          if (this.salvarESair || this.concluido || this.confirmarCancelamentoAtividadeExtra) {
            this.cancelar()
          } else {
            this.limparCamposDoData()
            this.openEdit(this.itemSelecionadoID)
          }
          this.salvando = false
        }).catch(() => {
          this.salvando = false
          this.cancelar()
        })
      } else {
        this.criar().then(() => {
          if (this.salvarESair || this.concluido) {
            this.cancelar()
          } else {
            this.LIMPAR_ITEM_SELECIONADO()
            this.limparCamposDoData()
          }
          this.salvando = false
        }).catch(() => {
          this.cancelar()
          this.salvando = false
        })
      }
    },

    abrirModalConcluir () {
      if (this.$v.$invalid) {
        this.isValid = false
      } else {
        this.modalConfirmarConcluir = true
        this.vemDeModal = true
      }
    },

    abrirModalCancelar () {
      if (this.$v.$invalid) {
        this.isValid = false
      } else {
        this.modalCancelamentoAtividadeExtra = true
        this.vemDeModal = true
      }
    },

    // exportaListaDeAlunos () {
    //   console.log("Exporta listas de alunos")

    //   const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
    //   const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
    //   const rota = this.$route.matched[0].path
    //   const filtroAlunoAtividadeExtra = `atividade_extra=${this.itemSelecionadoID}`

    //   console.log(filtroAlunoAtividadeExtra)
    //   // return `/api/relatorios/gerar_lista_aluno_atividade_extra?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${filtroAlunoAtividadeExtra}`
    // }

    verificarLista () {
      let listaDeAlunos = this.listaDeAlunosParticipantes.filter(aluno => aluno.removido === false)

      this.mensagem = null

      if (this.formularioData.max_aluno) {
        if (listaDeAlunos.length > this.formularioData.max_aluno) {
          this.mensagem = `Ultrapassado o limite máxima (${this.formularioData.max_aluno}) de alunos para atividade`
        }
      }
    },

    adicionarConvidado () {
      this.listaDeConvidados.push({
        nome: '',
        telefone: '',
        presenca: 'P'
      })
    },

    excluirConvidado (index, item) {
      if (this.listaDeConvidados.length === 1) {
        this.listaDeConvidados[index].nome = ''
        this.listaDeConvidados[index].telefone = ''
        this.listaDeConvidados[index].presenca = 'P'

        return
      }

      this.listaDeConvidados.splice(index, 1)
    }

  }
}
</script>

<style scoped>
td[data-label="Turma"]{
  overflow:visible;
}
.table-responsive-sm {
  flex-grow: 1;
  position: relative;
  min-height: 200px !important;
}
.scroller {
  overflow: visible !important;
}
</style>
