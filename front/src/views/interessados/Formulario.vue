<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar(true)">
      <div v-if="isEdit" class="form-loading screen-load">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="!isEdit" class="form-loading screen-load">
        <load-placeholder :loading="verificaCarregamento(loadCount,1)" />
      </div>
      <div class="p-3">
        <h5 class="title-module mb-2">Interessado</h5>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="nome" class="col-form-label">Nome *</label>
            <input id="nome" v-model="objInteressado.nome" type="text" class="form-control" required maxlength="150">
            <div class="invalid-feedback">Preencha o nome!</div>
          </div>

          <div class="col-md-3">
            <label for="idade" class="col-form-label">Idade</label>
            <g-select
              id="idade"
              :value="idade"
              :select="setIdade"
              :options="opcoesIdade"
              class="multiselect-truncate"
              label="text"
              track-by="id" />
          </div>

          <div class="col-md-3">
            <b-form-group label="Sexo">
              <b-form-radio-group
                id="radio_sexo"
                :options="opcoesSexo"
                v-model="objInteressado.sexo"
                name="lista-sexo"
              />
            </b-form-group>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="telefone" class="col-form-label">Telefone</label>
            <input id="telefone" v-model="objInteressado.telefone_contato" type="text" class="form-control" maxlength="20" required>
            <div v-if="!isValid && objInteressado.telefone_contato" class="input-invalid">Preencha corretamente!</div>
          </div>

          <div class="col-md-6">
            <label for="telefone_secundario" class="col-form-label">Telefone Secundário</label>
            <input id="telefone_secundario" v-model="objInteressado.telefone_secundario" type="text" class="form-control" maxlength="20">
            <div v-if="!isValid && $v.objInteressado.telefone_secundario.$invalid" class="input-invalid">Preencha corretamente!</div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="e-mail" class="col-form-label">E-mail</label>
            <input id="e-mail" v-model="objInteressado.email_contato" type="email" class="form-control" maxlength="50">
            <!-- <div class="invalid-feedback">Preencha corretamente o e-mail!</div> -->
          </div>

          <div class="col-md-6">
            <label for="e-mail-secundario" class="col-form-label">E-mail Secundário</label>
            <input id="e-mail-secundario" v-model="objInteressado.email_secundario" type="email" class="form-control" maxlength="50">
            <div class="invalid-feedback">Preencha corretamente o e-mail!</div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="consultor_pessoa" class="col-form-label">Consultor (1º Atendimento) *</label>
            <g-select
              v-input-locker="verificaPermissao()"
              id="consultor_pessoa"
              :value="consultorFuncionario"
              :select="setConsultorFuncionario"
              :options="listaFuncionarios"
              :invalid="!isValid && (consultorFuncionario.id === null)"
              label="apelido"
              track-by="id"
              required />
            <div v-if="!isValid && (consultorFuncionario.id === null)" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <button v-if="isEdit" type="button" class="btn btn-verde" @click="adicionarNivelamento">
              Nivelamento
            </button>
          </div>
        </div>
      </div>

      <div v-if="listaNivelamentos.length > 0" class="content-sector sector-laranja-p p-3">
        <div class="content-sector-extra p-2">
          <h5 v-b-toggle.outros-nivelamentos class="title-module d-flex collapse-toggle">Nivelamentos realizados ({{ listaNivelamentos.length }})<font-awesome-icon icon="caret-up" class="ml-auto my-auto collapse-toggle-state" /></h5>

          <b-collapse id="outros-nivelamentos" visible>
            <b-row class="header-card-list mt-2">
              <b-col md="2">
                <label class="col-form-label">Data Agendamento</label>
              </b-col>
              <b-col md="2">
                <label class="col-form-label">Data Conclusão</label>
              </b-col>
              <b-col md="3">
                <label class="col-form-label">Responsável</label>
              </b-col>
              <b-col md="3">
                <label class="col-form-label">Resultado</label>
              </b-col>
              <b-col md="2">
                <label class="col-form-label"></label>
              </b-col>
            </b-row>

            <b-row v-for="(nivelamento, index) in listaNivelamentos" :key="index" class="body-card-list">
              <b-col md="2" data-header="Data Agendamento">{{ nivelamento.data_hora_inicio | formatarData }}</b-col>
              <b-col md="2" data-header="Data Conclusão">{{ nivelamento.data_hora_fim | formatarData }}</b-col>
              <b-col md="3" data-header="Responsável">{{ nivelamento.responsaveis_execucacao && nivelamento.responsaveis_execucacao.length > 0 ? nivelamento.responsaveis_execucacao[0].apelido : 'Não informado' }}</b-col>
              <b-col md="3" data-header="Resultado">{{ nivelamento.livro_descricao ? nivelamento.livro_descricao : 'Ainda não realizado' }}</b-col>
              <b-col md="2">
                <a v-b-tooltip.viewport.left.hover v-if="nivelamento.situacao === 'P'" class="icone-link" title="Atualizar" @click.prevent="alterar(nivelamento)">
                  <font-awesome-icon icon="pen" />
                </a>
                <a v-b-tooltip.viewport.left.hover v-else class="icone-link" title="Ver" @click.prevent="ver(nivelamento)">
                  <font-awesome-icon icon="eye" />
                </a>
              </b-col>
            </b-row>
          </b-collapse>
        </div>
      </div>

      <div class="form-group pt-2">
        <!-- <b-btn :disabled="isEnviando" variant="verde" @click="salvar(false)">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
        <b-btn :disabled="isEnviando" type="submit" variant="verde">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn> -->

        <!-- Adicionar Follow Up -->
        <b-btn v-if="alunoConvertidoId === null" :disabled="isEnviando" variant="roxo" @click="adicionarFollowUp()">Follow Up </b-btn>

        <!-- Matricular -->
        <b-btn v-if="alunoConvertidoId === null && objInteressado.situacao === 'A' && objInteressado.workflow && objInteressado.workflow.tipo_workflow === 'WRMC'" :disabled="isEnviando" variant="roxo" @click="matricularInteressado()" >Iniciar matrícula </b-btn>

        <router-link v-if="isEdit && (alunoConvertidoId !== null)" :to="`/academico/aluno/atualizar/`+alunoConvertidoId" class="btn btn-azul">
          Dados cadastrais do aluno
        </router-link>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>

    <!-- Modal Matricular -->
    <g-modal id="matricularInteressado" ref="matricularInteressado" v-model="visibleMatriculaModal" size="md" centered no-close-on-backdrop hide-header hide-footer>
      <form :class="{ 'was-validated': !isValid }" class="needs-validation p-3" novalidate @submit.prevent="salvarMatricular()">
        <div class="form-group row">
          <div class="col-md-12">
            <label for="busca_cpf">Busca CPF</label>
            <typeahead id="busca_cpf" :item-hit="setAlunoSelecionado" source-path="/api/aluno/buscar-cpf" max-length="11" key-name="aluno.pessoa.nome_contato" @input="value => buscaCpf = value" />
            <div v-if="buscaCpf.length === 11 && !isCpfValido(buscaCpf)" class="multiselect-invalid">CPF informado é inválido!</div>
          </div>
        </div>

        <div class="d-flex justify-content-center">
          <b-btn :disabled="isMatriculando || !isCpfValido(buscaCpf)" type="submit" variant="verde">{{ isMatriculando ? 'Salvando...': 'Salvar e matricular' }}</b-btn>
          <b-btn variant="link" @click="finalizar()">Cancelar</b-btn>
        </div>
      </form>
    </g-modal>

    <!-- Nivelamento -->
    <formulario-nivelamento ref="modalFormularioDeNivelamento" @hide="resetarComponentes()"/>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required, email, minLength} from 'vuelidate/lib/validators'
import Typeahead from '../../components/Typeahead.vue'
import FormularioNivelamento from '../nivelamento/FormularioNivelamento'
import {isCpfValido} from '../../utils/format'
import EventBus from '../../utils/event-bus'

export default {
  name: 'FormularioInteressados',
  components: {
    Typeahead,
    FormularioNivelamento
  },
  data () {
    return {
      loadCount: 0,
      isValid: true,
      isEdit: false,
      isEnviando: false,
      isMatriculando: false,
      isNewFollowUp: false,
      visibleMatriculaModal: false,
      buscaCpf: '',
      alunoId: null,
      alunoConvertidoId: null,
      consultorFuncionario: {id: null, apelido: 'Selecione'},
      idade: {id: null, text: 'Selecione', value: null},
      listaNivelamentos: [],
      opcoesSexo: [
        {text: 'Masculino', value: 'M'},
        {text: 'Feminino', value: 'F'}
      ],
      opcoesIdade: [
        {id: null, text: 'Selecione', value: null},
        {id: 1, text: '6 anos', value: 6},
        {id: 2, text: '7 anos', value: 7},
        {id: 3, text: '8 anos', value: 8},
        {id: 4, text: '9 anos', value: 9},
        {id: 5, text: '10 anos', value: 10},
        {id: 6, text: '11 anos', value: 11},
        {id: 7, text: '12 anos', value: 12},
        {id: 8, text: '13 ou 14 anos', value: 13},
        {id: 9, text: '15 ou 16 anos', value: 15},
        {id: 10, text: ' Acima de 17 anos', value: 17}
      ]
    }
  },

  computed: {
    ...mapState('interessados', {objInteressado: 'itemSelecionado', itemSelecionadoID: 'itemSelecionadoID', estaCarregando: 'estaCarregando'}),
    ...mapState('funcionario', {listaFuncionarios: 'lista'}),
    ...mapState('modulos', ['permissoes'])
  },

  mounted () {
    this.resetarComponentes()
  },

  validations: {
    objInteressado: {
      nome: {required},
      email_contato: {email},
      email_secundario: {email},
      telefone_contato: {minLength: minLength(8)},
      telefone_secundario: {minLength: minLength(8)}
    },
    consultorFuncionario: {
      id: {required}
    }
  },

  methods: {
    ...mapMutations('interessados', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO', 'SET_TIPO_CONTATO', 'SET_TIPO_PROSPECCAO']),
    ...mapActions('interessados', ['buscar', 'criar', 'atualizar', 'checarTelefonesCadastrados']),

    resetarComponentes () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.listaCamposDinamicos()

      const id = this.$route.params.id
      if (id) {
        this.isEdit = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscar()
          .then(item => {
            if (this.objInteressado.aluno !== undefined) {
              this.alunoConvertidoId = this.objInteressado.aluno.id
            }
            
            if (this.objInteressado.idade !== undefined) {

              this.idade = this.opcoesIdade.find(i => i.value === this.objInteressado.idade)
             

              this.idade = this.opcoesIdade.find(i => i.value === Number(this.objInteressado.idade))

            }

            this.consultorFuncionario = { id: item.consultor_funcionario_id, apelido: item.consultor_funcionario_apelido }

            if (this.objInteressado.interessadoAtividadeExtras.length > 0) {
              this.objInteressado.interessadoAtividadeExtras.filter((atividadeExtra) => {
                if (atividadeExtra.item_tipo === 'SN') {
                  // atividadeExtra.atividade_extra. = atividadeExtra.livro || null
                  this.listaNivelamentos.push(atividadeExtra)
                }
              })
            }
          })
      }
    },

    alterar (item) {
      this.$refs.modalFormularioDeNivelamento.visibilidadeModalAtividadeNivelamento = true
      this.$refs.modalFormularioDeNivelamento.openEdit(item.id)
    },

    ver (item) {
      this.$refs.modalFormularioDeNivelamento.visibilidadeModalAtividadeNivelamento = true
      this.$refs.modalFormularioDeNivelamento.isReadOnly = true
      this.$refs.modalFormularioDeNivelamento.openEdit(item.id)
    },

    setAlunoSelecionado (aluno) {
      if (aluno) {
        this.alunoId = aluno.id
        this.buscaCpf = aluno.pessoa.cnpj_cpf
      } else {
        this.alunoId = null
        this.buscaCpf = ''
      }
    },

    isCpfValido: isCpfValido,

    adicionarFollowUp () {
      this.isNewFollowUp = true
      const telefonesCadastrados = []
      if (this.objInteressado.telefone_secundario) {
        telefonesCadastrados.push(this.objInteressado.telefone_secundario)
      }
      if (this.objInteressado.telefone_contato) {
        telefonesCadastrados.push(this.objInteressado.telefone_contato)
      }
      if (telefonesCadastrados.length === 0) {
        this.salvar(true)
        return
      }
      this.checarTelefonesCadastrados(telefonesCadastrados).then(res => {
        if (!res.telefones_cadastrados || !res.telefones_cadastrados.length) {
          this.salvar(true)
          return
        }
        let mensagem = ''
        if (res.telefones_cadastrados.length === 2) {
          mensagem = 'Ambos os telefones informados ja possuem interessado vinculado.'
        } else {
          mensagem = `O telefone ${res.telefones_cadastrados[0]} ja possui interessado vinculado.`
        }
        mensagem += ' Deseja prosseguir?'
        EventBus.$emit('chamarModal', {
          resolve: success => {
            this.salvar(true)
           
          }
        }, mensagem)
      }, console.log)
    },

    salvarMatricular () {
      this.isMatriculando = true
      this.salvar(true)
    },

    finalizar (action = 'cancel') {
      this.$refs.matricularInteressado.hide()
    },

    listaCamposDinamicos () {
      this.listaNivelamentos = []
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_LISTA', [])
      this.$store.dispatch('funcionario/buscarConsultores')
        .then(() => {
          if (!this.consultorFuncionario.id) {
            const usuarioLogado = this.$store.state.root.usuarioLogado

            this.listaFuncionarios.map(funcionario => {
              if (funcionario.usuario && funcionario.usuario.id === usuarioLogado.id) {
                this.setConsultorFuncionario(funcionario)
              }
            })
          }
          this.countCarregamento()
        })
    },

    setConsultorFuncionario (value) {
      this.consultorFuncionario = value
    },

    setIdade (value) {
      this.idade = value.id === null ? this.opcoesIdade[0] : value
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/cadastros/interessados')
    },

    adicionarNovoInteressado () {
      // this.LIMPAR_ITEM_SELECIONADO()
      this.$router.go()
    },

    adicionarNivelamento () {
      this.$refs.modalFormularioDeNivelamento.interessado = this.objInteressado
      this.$refs.modalFormularioDeNivelamento.visibilidadeModalAtividadeNivelamento = true
    },

    finalizaRequisicao () {
      this.isNewFollowUp = false
      this.isMatriculando = false
      this.isEnviando = false
    },

    montaParametros () {
      this.objInteressado.consultor_funcionario = this.consultorFuncionario
      this.objInteressado.idade = this.idade.value

      // Apagar paramentros não usados

      // delete this.objInteressado.agendaComerciais
      // delete this.objInteressado.consultor_responsavel_funcionario
      // delete this.objInteressado.followupComercials
      // delete this.objInteressado.grau_interesse
      // delete this.objInteressado.idiomas
      // delete this.objInteressado.tipo_lead
      // delete this.objInteressado.tipo_prospeccao
    },

    redirecionar () {
      if (this.alunoId) {
        this.$router.push(`/academico/aluno/atualizar/${this.alunoId}?matriculando=true&interessado=${this.itemSelecionadoID}&cpf=${this.buscaCpf}`)
      } else {
        this.$router.replace(`/cadastros/interessados/atualizar/${this.itemSelecionadoID}`)
        this.$router.push(`/academico/aluno/adicionar?matriculando=true&interessado=${this.itemSelecionadoID}&cpf=${this.buscaCpf}`)
      }
    },

    redirecionarAdicinarFollowUp () {
      this.$router.push(`/cadastros/interessados/followup/${this.itemSelecionadoID}`)
    },

    salvar (bSalvarESair) {
      this.isEnviando = true

      if (this.$v.$invalid) {
        this.isValid = false

        this.finalizaRequisicao()
        return
      }

      this.montaParametros()

      if (this.itemSelecionadoID) {
        this.atualizar().then(() => {
          if (this.isNewFollowUp) {
            this.redirecionarAdicinarFollowUp()
            return
          }
          if (this.isMatriculando) {
            this.redirecionar()
            return
          }
          if (bSalvarESair === true) {
            this.voltar()
          } else {
            this.adicionarNovoInteressado()
          }

          this.finalizaRequisicao()
        })
      } else {
        this.criar().then(() => {
          if (this.isNewFollowUp) {
            this.redirecionarAdicinarFollowUp()
            return
          }
          if (this.isMatriculando) {
            this.buscar().then(this.redirecionar)
            return
          }

          if (bSalvarESair === true) {
            this.voltar()
          } else {
            this.adicionarNovoInteressado()
          }

          this.finalizaRequisicao()
        })
      }
    },

    countCarregamento () {
      this.loadCount++
    },

    verificaCarregamento (numeroDeRequisicoesFeitas, requisicoes) {
      if (numeroDeRequisicoesFeitas !== requisicoes) {
        return true
      } else {
        return false
      }
    },

    matricularInteressado () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }
      this.isValid = true
      this.$refs.matricularInteressado.show()
    },

    verificaPermissao () {
      if (this.isEdit) {
        return {permissao: this.permissoes['CONSULTOR_FUNCIONARIO']}
      }
    },
    resetCpf () {
      this.buscaCpf = ''
    }
  }
}
</script>

<style>

#observacao_follow_up {
  height: 350px;
}

div.typeahead-container div.resultContainer{
  position: relative!important;
}

</style>
