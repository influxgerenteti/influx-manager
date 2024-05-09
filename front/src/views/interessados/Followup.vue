<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar(true)">
      <div v-if="isEdit" class="form-loading screen-load">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="content-sector sector-primary p-2">
        <b-row class="form-group">
          <h5 class="title-module col-md-8">Follow-Up</h5>

          <b-col md="4" class="d-flex align-items-center">
            <label v-help-hint="" for="formulario-followup-data-primeiro-atendimento" class="col-form-label flex-grow-1 text-right mr-1">Data 1º Atendimento</label>

            <template v-if="!objInteressado.data_primeiro_atendimento">
              <g-datepicker :element-id="'formulario-followup-data-primeiro-atendimento'" :value="dataPrimeiroAtendimento" :selected="setDateFirstCall" placeholder="Data" class="text-center" style="width: 100px" />
            </template>
            <template v-else>
              <input v-model="dataPrimeiroAtendimento" type="text" class="form-control text-center" readonly style="width: 100px;">
            </template>
          </b-col>
        </b-row>

        <b-row>
          <b-col md="4">
            <label v-help-hint="" for="formulario-followup-nome" class="col-form-label">Nome</label>
            <template v-if="isEdit">
              <input v-model="interessado.nome" type="text" class="form-control" required>
            </template>
            <template v-else>
              <typeahead id="nome_aluno" :item-hit="setNomeInteressado" source-path="/api/interessado/buscar-nome" key-name="nome" />
            </template>
          </b-col>

          <b-col md="4">
            <label for="telefone" class="col-form-label">Telefone</label>
            <input id="telefone" v-model="objInteressado.telefone_contato" type="text" class="form-control" maxlength="20" required>
            <div v-if="!isValid" class="input-invalid">Preencha corretamente!</div>
            <!-- <div v-if="objInteressado.telefone_contato != null && objInteressado.telefone_contato.length <= 13">Telefone invalido!</div> -->
                    <!-- <div class="invalid-feedback">
                  {{ ($v.objInteressado.telefone_contato.$invalid) ? 'telefone inválido' : 'Campo obrigatório' }}
                </div> -->
          </b-col>


           <b-col md="4">
           <label for="e-mail" class="col-form-label">E-mail</label>
            <input id="e-mail" v-model="objInteressado.email_contato" type="email" class="form-control" maxlength="50" >
            <!-- <div class="invalid-feedback">Preencha corretamente o e-mail!</div> -->
           </b-col>
        </b-row>
      </div>

      <div class="content-sector sector-laranja-p p-2">
        <div class="form-group row">
          <div class="col-md-6">
            <b-form-group label="Tipo de contato *">
              <b-form-radio-group
                id="radio_tipo_contato"
                :options="tipoContato"
                v-model="objInteressado.tipo_lead"
                name="lista-tipo-contato"
                required
                @input="limparTipoContatoSelect(objInteressado.tipo_lead)"
              />
            </b-form-group>
            <div v-if="!isValid && !objInteressado.tipo_lead" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Forma do contato *</label>
            <g-select
              id="select_tipo_contato"
              :value="tipoContatoSelect"
              :select="setTipoContatoSelect"
              :options="objInteressado.tipo_lead === 'A' ? listaTipoContatoAtivo : objInteressado.tipo_lead === 'R' ? listaTipoContatoReceptivo : [tipoContatoSelect]"
              :label="objInteressado.tipo_lead === 'A' ? 'descricao' : 'nome'"
              :invalid="!isValid && ($v.tipoContatoSelect.$invalid || tipoContatoSelect.id === null)"
              track-by="id"
              required
            />
            <div v-if="!isValid && ($v.tipoContatoSelect.$invalid || tipoContatoSelect.id === null)" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <b-form-group label="Grau de interesse *">
              <b-form-radio-group v-model="grauInteresseSelecionado" required>
                <b-form-radio :disabled="objInteressado.grau_interesse && objInteressado.grau_interesse !== 'L'" value="L">Lead</b-form-radio>
                <b-form-radio value="I">Interessado</b-form-radio>
                <b-form-radio value="H">Hotlist</b-form-radio>
              </b-form-radio-group>
            </b-form-group>
            <div v-if="!isValid && !grauInteresseSelecionado" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </div>

          <div class="col-md-3">
            <label for="curso_oferecido" class="col-form-label">Curso oferecido</label>
            <g-select
              id="curso_oferecido"
              :value="cursoOferecido"
              :select="setCursoOferecido"
              :options="listaCurso"
              class="multiselect-truncate valid-input"
              label="descricao"
              track-by="id" />
          </div>

          <b-form-group label="Idioma pretendido" class="col-md-3">
            <b-form-checkbox-group
              id="checkboxes_idiomas"
              :options="listaIdiomas"
              v-model="idiomasSelecionados"
              name="checkbox-idiomas"
              text-field="descricao"
              value-field="id"
            />
          </b-form-group>
        </div>

        <div class="form-group row">
          <div class="col-md-3">
            <label for="validade_promocao" class="col-form-label">Validade da promoção</label>
            <g-datepicker :element-id="'validade_promocao'" :value="dataValidadePromocao" :selected="setValidadeDaPromocao" placeholder="Data"/>
          </div>
          <div class="col-md-3">
            <label for="periodo_pretendido" class="col-form-label">Período pretendido</label>
            <g-select
              id="periodo_pretendido"
              :value="periodoPretendido"
              :select="setPeriodoPretendido"
              :options="listaPeriodoPretendido"
              class="multiselect-truncate valid-input"
              label="descricao"
              track-by="id" />
          </div>

          <div class="col-md-6">
            <label for="proximo_contato" class="col-form-label">Próximo contato {{ $v.proximo_contato_temporario.isNotRequired === false && $v.workflowAcaoSelecionado.descricao === 'Não compareceu e precisaremos reagendar' || proximo_contato_temporario && horario_proximo_contato ? '*' : '' }}</label>
            <div class="row">
              <div class="col-md-6">
                <g-datepicker :element-id="'proximo_contato_temporario'"
                              :value="proximo_contato_temporario"
                              :class="!isValid && $v.proximo_contato_temporario.isNotRequired === false && !proximo_contato_temporario ? 'invalid-input' : 'valid-input'"
                              :selected="setProximoContatoTemporario"
                              :required="$v.proximo_contato_temporario.isNotRequired === false && !proximo_contato_temporario && $v.workflowAcaoSelecionado.descricao === 'Não compareceu e precisaremos reagendar' "
                              placeholder="Data" />
              </div>

              <div class="col-md-6">
                <input v-mask="'##:##'" v-model="horario_proximo_contato" :class="!$v.horario_proximo_contato.validateHour ? 'is-invalid' : null" :required="$v.horario_proximo_contato.isNotRequired === false " type="text" class="form-control" maxlength="5" placeholder="Horario">
                <div class="invalid-feedback">
                  {{ (!$v.horario_proximo_contato.validateHour) ? 'Horário inválido' : 'Campo obrigatório' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="consultor_responsavel_pessoa" class="col-form-label">Consultor responsável (próximo atendimento) {{ $v.consultorResponsavelFuncionario.isNotRequired === false && $v.workflowAcaoSelecionado.descricao === 'Não compareceu e precisaremos reagendar' || consultorResponsavelFuncionario.id ? '*' : '' }}</label>
            <g-select
              id="consultor_responsavel_pessoa"
              v-model="consultorResponsavelFuncionario"
              :options="listaFuncionarios"
              :invalid="!isValid && $v.consultorResponsavelFuncionario.$invalid"
              :required="$v.consultorResponsavelFuncionario.isNotRequired === false"
              :force-nullable="true"
              label="apelido"
              track-by="id" />
          </div>
            <!-- //beto -->
            <div class="col-md-6">
              <label v-help-hint="'form-pessoa_input_pessoa_indicou'" for="input_pessoa_indicou" class="col-form-label">Quem indicou</label>
               <template v-if ="objInteressado.pessoa_indicou_nome == null" >
                <typeahead id="'input_pessoa_indicou'"
                           :item-hit="setPessoaIndicou"
                           source-path="/api/pessoa/buscar_nome_contato_com_contrato" 
                           key-name="nome_contato"/>
              </template>
              <template v-else>
                <div class="d-flex">
                  <span class="form-control form-control-disabled flex-grow">
                    {{ interessado.pessoa_indicou_nome }}
                  </span>
                  <b-btn variant="link" @click="setPessoaIndicou(null)">Limpar</b-btn>
                </div>
              </template>

            </div>
            <!-- //beto -->
            
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <button v-if="isEdit" type="button" class="btn btn-verde" @click="adicionarNivelamento">
              Nivelamento
            </button>
          </div>
        </div>
      </div>

      <div class="content-sector sector-secondary p-2">
        <div class="form-group row">
          <b-col>
            <label for="formulario-followup-formulario" class="col-form-label">Formulário</label>
            <g-select
              id="formulario-followup-formulario"
              :value="objFormularioFollowUp"
              :select="setFormularioFollowUp"
              :options="listaFormularios"
              :disabled="true"
              label="descricao_formulario"
              track-by="id"/>
          </b-col>
        </div>

        <template v-if="objFormularioFollowUp">
          <div class="form-group row">
            <template v-if="camposDoFormularioFollowUp.length === 0">
              <div class="list-group-item list-group-item-accent-warning  list-group-item-warning border-0 aviso">
                Nenhum campo cadastrado para o formulário selecionado.
              </div>
            </template>
            <template v-else>
              <div v-for="campo in camposDoFormularioFollowUp" :key="campo.id" class="col-md-12">
                <label :for="campo.nome_campo + '_campo_dinamico'" class="col-form-label">{{ campo.nome_campo }}</label>
                <template v-if="campo.texto_longo">
                  <b-form-textarea
                    :id="campo.nome_campo + '_campo_dinamico'"
                    v-model="dadosFollowUps[campo.id]"
                    rows="3"
                  />
                </template>
                <template v-else>
                  <input :id="campo.nome_campo + '_campo_dinamico'" v-model="dadosFollowUps[campo.id]" type="text" class="form-control">
                </template>
              </div>
            </template>
          </div>
        </template>

        <hr>

        <div v-if="isEdit && followUpsAdicionados" class="form-group">
          <textarea id="observacao_follow_up" v-model="followUpsAdicionados" class="form-control" rows="4" readonly></textarea>
        </div>

         <!-- <template v-if="isEdit && workflowDescricao.length > 0"> -->
        <template >
          <div class="form-group row">
            <div class="col-md-4">
              <label for="etapa_funil" class="col-form-label">Etapa do funil</label>
              <g-select
                id="etapa_funil"
                :value="workflowSelecionado"
                :select="setWorkflowSelecionado"
                :options="listaWorkflowsDisponiveis"
                :disabled="bloquearWorkflow"
                label="descricao"
                track-by="id"/>
            </div>

            <div class="col-md-4">
              <label for="resultado_contato" class="col-form-label">Resultado do Contato {{ $v.workflowAcaoSelecionado.isNotRequired === false || workflowAcaoSelecionado.id ? '*' : '' }}</label>
              <g-select
                id="resultado_contato"
                :value="workflowAcaoSelecionado"
                :select="setWorkflowAcaoSelecionado"
                :options="listaWorkflowAcao"
                :class="!isValid && (!$v.workflowAcaoSelecionado.$model || !$v.workflowAcaoSelecionado.$model.id) && $v.workflowAcaoSelecionado.$invalid ? 'invalid-input' : 'valid-input'"
                label="descricao"
                track-by="id"/>
              <div v-if="!isValid && (!$v.workflowAcaoSelecionado.$model || !$v.workflowAcaoSelecionado.$model.id) && $v.workflowAcaoSelecionado.$invalid" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </div>
            <div class="col-md-4">
              <label for="motivo_matricula_perdida" class="col-form-label">Motivo matricula perdida {{ validaRequiredCampoMotivoMatriculaPerdida() ? '*' : '' }}</label>
              <g-select
                id="motivo_matricula_perdida"
                :value="motivoMatriculaPerdidaSelecionado"
                :select="setMotivoMatriculaPerdidaSelecionado"
                :options="listaMotivoMatriculaPerdida"
                :required="validaRequiredCampoMotivoMatriculaPerdida()"
                :invalid="validaRequiredCampoMotivoMatriculaPerdida() ? (!motivoMatriculaPerdidaSelecionado || !motivoMatriculaPerdidaSelecionado.id) : false"
                :disabled="validaRequiredCampoMotivoMatriculaPerdida() === false"
                label="descricao"
                track-by="id"/>
              <div v-if="!isValid && (validaRequiredCampoMotivoMatriculaPerdida() ? (!motivoMatriculaPerdidaSelecionado || !motivoMatriculaPerdidaSelecionado.id) : false)" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </div>
          </div>
        </template>

        <!-- ALTERAR AQUI -->
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
      </div>

      <div class="form-group pt-2">
        <b-btn :disabled="isEnviando" variant="verde" @click="salvar(false)">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
        <b-btn :disabled="isEnviando" type="submit" variant="verde">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn>

        <!-- Matricular -->
        <b-btn v-if="visibilidadeMatricula()" :disabled="isEnviando" variant="roxo" @click="matricularInteressado()">Iniciar matrícula</b-btn>

        <router-link v-if="isEdit && (alunoConvertidoId !== null)" :to="`/academico/aluno/atualizar/`+alunoConvertidoId" class="btn btn-azul">
          Dados cadastrais do aluno
        </router-link>
        <b-btn v-if="isModal" variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>

    <!-- Modal Matricular -->
    <g-modal id="matricularInteressado" ref="matricularInteressado" centered no-close-on-backdrop hide-header hide-footer>
      <form :class="{ 'was-validated': !isValid }" class="p-2 needs-validation" novalidate @submit.prevent="salvarMatricular()">
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
    <formulario-nivelamento ref="modalFormularioDeNivelamento" />
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required, email, minLength} from 'vuelidate/lib/validators'
import Typeahead from '../../components/Typeahead.vue'
import FormularioNivelamento from '../nivelamento/FormularioNivelamento'
import {stringToISODate, dateToString} from '../../utils/date'
import {validateHour} from '../../utils/validators'
import {isCpfValido} from '../../utils/format'
import EventBus from '../../utils/event-bus'

function isNotRequired (value, vm) {

  //verifica se é uma matricula ou ...
  if(this.isEdit && vm.workflowSelecionado && (vm.workflowSelecionado.tipo_workflow ===  'WRMC' || vm.workflowSelecionado.tipo_workflow ===  'WRMP')){
    return true;
  }
   //verifica se o proximo passo é uma matricula ou ...
  if (this.isEdit && vm.workflowAcaoSelecionado.destino_workflow) {
    if ((vm.workflowAcaoSelecionado.destino_workflow.tipo_workflow === 'WRMP') ||
      (vm.workflowAcaoSelecionado.destino_workflow.tipo_workflow === 'WRMC')) {
      return true
    }
  }

  if(vm.workflowAcaoSelecionado.descricao === 'Não compareceu e precisaremos reagendar') {
    return true
  }

  return ((!vm.proximo_contato_temporario && !vm.horario_proximo_contato && (!this.isEdit || (this.isEdit && !vm.workflowAcaoSelecionado.id)) && !vm.consultorResponsavelFuncionario.id) === true) ||
  ((!!vm.proximo_contato_temporario && !!vm.horario_proximo_contato && (!this.isEdit || (this.isEdit && !!vm.workflowAcaoSelecionado.id)) && !!vm.consultorResponsavelFuncionario.id) === true)
}

export default {
  name: 'FormularioFollowUp',
  components: {
    Typeahead,
    FormularioNivelamento
  },

  props: {
    isModal: {
      required: false,
      type: Boolean,
      default: false
    },
    loaded: {
      required: false,
      type: Function,
      default: null
    },
    back: {
      required: false,
      type: Function,
      default: null
    }
  },

  data () {
    return {
      isValid: true,
      bloquearWorkflow: true,
      isEdit: false,
      isEnviando: false,
      isMatriculando: false,
      visibleMatriculaModal: false,
      bVeioListagemFollowup: false,
      followUpsAdicionados: '',
      proximo_contato_temporario: '',
      dataValidadePromocao: '',
      dataPrimeiroAtendimento: '',
      horario_proximo_contato: '',
      buscaCpf: '',
      rotaDeBusca: '',
      objFormularioFollowUp: null,
      interessadoId: null,
      alunoId: null,
      alunoConvertidoId: null,
      grauInteresseSelecionado: null,
      pessoa_indicou: null, //Beto
      pessoa_indicou_nome: null, //Beto
      interessado: {},
      workflowSelecionado: {},
      tipoContatoSelect: {id: null, nome: 'Selecione'},
      periodoPretendido: {id: null, descricao: 'Selecione', value: null},
      cursoOferecido: {id: null, descricao: 'Selecione'},
      consultorFuncionario: {id: null, apelido: 'Selecione'},
      consultorResponsavelFuncionario: {id: null, apelido: 'Selecione'},
      workflowAcaoSelecionado: {id: null, descricao: 'Selecione'},
      motivoMatriculaPerdidaSelecionado: {id: null, descricao: 'Selecione'},
      formulario: {id: null, descricao_formulario: 'Selecione'},
      formularioDestino: {id: null, descricao: 'Selecione'},
      listaFormularios: [],
      camposDoFormularioFollowUp: [],
      dadosFollowUps: [],
      idiomasSelecionados: [],
      listaWorkflowsDisponiveis: [],
      listaDeDestinos: [
        {id: 1, descricao: 'Ex aluno', value: 'ALU'},
        {id: 2, descricao: 'Interssado', value: 'INT'},
        {id: 3, descricao: 'Empresa', value: 'CON'}
      ],
      listaDeRotas: [
        {id: 1, rota: '/api/interessado/buscar-nome', value: 'ALU'},
        {id: 2, rota: '/api/interessado/buscar-nome', value: 'INT'},
        {id: 3, rota: '/api/interessado/buscar-nome', value: 'CON'}
      ],
      tipoContato: [
        {text: 'Contato Ativo', value: 'A'},
        {text: 'Contato Receptivo', value: 'R'}
      ],
      listaPeriodoPretendido: [
        {id: null, descricao: 'Selecione', value: null},
        {id: 1, descricao: 'Manhã', value: 'M'},
        {id: 2, descricao: 'Tarde', value: 'T'},
        {id: 3, descricao: 'Noite', value: 'N'},
        {id: 4, descricao: 'Sábado', value: 'S'}
      ]
    }
  },

  computed: {
    ...mapState('root', ['usuarioLogado']),
    ...mapState('interessados', {objInteressado: 'itemSelecionado', itemSelecionadoID: 'itemSelecionadoID', estaCarregando: 'estaCarregando'}),
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    ...mapState('workflowAcao', {listaWorkflowAcao: 'lista'}),
    ...mapState('idioma', {listaIdiomas: 'lista'}),
    ...mapState('curso', {listaCursoRequisicao: 'lista'}),
    ...mapState('tipoContato', {listaTipoContatoReceptivo: 'lista'}),
    ...mapState('prospeccao', {listaTipoContatoAtivo: 'lista'}),
    ...mapState('motivosMatriculaPerdida', {listaMotivoMatriculaPerdidaRequisicao: 'lista'}),
    ...mapState('modulos', ['permissoes']),

    listaFuncionarios: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionariosRequisicao.filter(funcionario => (funcionario.consultor === true) || (funcionario.cargo.tipo === 'GER'))]
      }
    },

    listaMotivoMatriculaPerdida: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaMotivoMatriculaPerdidaRequisicao]
      }
    },

    listaCurso: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaCursoRequisicao]
      }
    }
  },

  mounted () {
   
    if(this.loaded != null){
      this.loaded()
    }
    
    if (this.$route.query.bVeioListagemFollowup) {
      this.bVeioListagemFollowup = true
    }
    this.LIMPAR_ITEM_SELECIONADO()
    this.limparfiltrosCamposDinamicos()
    this.resetData(false)
    this.listarCamposDinamicos()
    this.criarTela()
  },
  validations: {
    objInteressado: {
      nome: {required},
      tipo_lead: {required},
      email_contato: {email},
      telefone_contato: {minLength: minLength(8)},
    },
    tipoContatoSelect: {required},
    grauInteresseSelecionado: {required},
    consultorFuncionario: {
      id: {required}
    },
    workflowSelecionado: {required},
    workflowAcaoSelecionado: { isNotRequired },
    consultorResponsavelFuncionario: { isNotRequired },
    proximo_contato_temporario: { isNotRequired },
    horario_proximo_contato: { validateHour, isNotRequired }
  },
                                                                                            //beto ->
  methods: {
    ...mapMutations('interessados', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_PESSOA_INDICOU','SET_ESTA_CARREGANDO', 'SET_TIPO_CONTATO', 'SET_TIPO_PROSPECCAO']),
    ...mapActions('interessados', ['buscar', 'criar', 'atualizar', 'atualizarDadosFollowup']),
    ...mapActions('funcionario', {listarFuncionario: 'listar'}),
    ...mapActions('workflow', {listarWorkflow: 'listar'}),
    ...mapActions('workflowAcao', {listaWorkflowAcaoPorWorkflowSelecionado: 'buscarPorWorkflowId'}),

    isCpfValido: isCpfValido,

    visibilidadeMatricula () {
      if (this.workflowSelecionado.tipo_workflow === 'WRMC') {
        return true
      }

      return (this.alunoConvertidoId === null) && ((this.workflowSelecionado.tipo_workflow === 'WRAP') && (this.workflowAcaoSelecionado.descricao === 'Compareceu e fechou'))
    },

    validaRequiredCampoMotivoMatriculaPerdida () {
      if (this.workflowAcaoSelecionado.destino_workflow) {
        return this.workflowAcaoSelecionado.destino_workflow.tipo_workflow === 'WRMP'
      }
      return false
    },
    
    //beto
    setPessoaIndicou (value) {
      this.$store.commit('interessados/SET_PESSOA_INDICOU', value)
      this.interessado.pessoa_indicou_nome = this.objInteressado.pessoa_indicou_nome 
      this.interessado.pessoa_indicou = this.objInteressado.pessoa_indicou
    },
    //------

    resetData (manterListaWorkflowsDisponiveis) {
      
      this.isValid = true
      this.pessoa_indicou = null
      this.pessoa_indicou_nome = null
      this.isEnviando = false
      this.isMatriculando = false
      this.visibleMatriculaModal = false
      this.followUpsAdicionados = ''
      this.proximo_contato_temporario = ''
      this.dataValidadePromocao = ''
      this.dataPrimeiroAtendimento = ''
      this.horario_proximo_contato = ''
      this.workflowDescricao = ''
      this.buscaCpf = ''
      this.alunoId = null
      this.alunoConvertidoId = null
      this.grauInteresseSelecionado = null
      this.consultorFuncionario = {id: null, apelido: 'Selecione'}
      this.workflowAcaoSelecionado = {id: null, descricao: 'Selecione'}
      this.tipoContatoSelect = {id: null, nome: 'Selecione'}
      this.periodoPretendido = {id: null, descricao: 'Selecione', value: null}
      this.cursoOferecido = {id: null, descricao: 'Selecione'}
      this.consultorResponsavelFuncionario = {id: null, apelido: 'Selecione'}
      this.formulario = {id: null, descricao_formulario: 'Selecione'}
      this.formularioDestino = {id: null, descricao: 'Selecione'}
      this.rotaDeBusca = ''
      this.idiomasSelecionados = []
      if (!manterListaWorkflowsDisponiveis) {
        this.listaWorkflowsDisponiveis = []
      }
      this.listaDeDestinos = [
        {id: 1, descricao: 'Ex aluno', value: 'ALU'},
        {id: 2, descricao: 'Interssado', value: 'INT'},
        {id: 3, descricao: 'Empresa', value: 'CON'}
      ]
      this.tipoContato = [
        {text: 'Contato Ativo', value: 'A'},
        {text: 'Contato Receptivo', value: 'R'}
      ]
      this.listaPeriodoPretendido = [
        {id: null, descricao: 'Selecione', value: null},
        {id: 1, descricao: 'Manhã', value: 'M'},
        {id: 2, descricao: 'Tarde', value: 'T'},
        {id: 3, descricao: 'Noite', value: 'N'},
        {id: 4, descricao: 'Sábado', value: 'S'}
      ]
      this.interessado = {}
      this.objFormularioFollowUp = null
      this.camposDoFormularioFollowUp = []
      this.dadosFollowUps = []
    },

    criarTela () {
      this.dataPrimeiroAtendimento = dateToString(new Date())
      const id = this.$route.params.id ? this.$route.params.id : this.interessadoId

      if (id) {
        this.isEdit = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscar()
          .then(item => {
            this.formularioDestino = this.listaDeDestinos.find(formularioDestino => formularioDestino.value === 'INT')
            this.interessado.nome = this.objInteressado.nome
            
            this.interessado.pessoa_indicou = this.objInteressado.pessoa_indicou
            this.interessado.pessoa_indicou_nome = this.objInteressado.pessoa_indicou_nome

            this.dataPrimeiroAtendimento = this.objInteressado.data_primeiro_atendimento ? dateToString(new Date(this.objInteressado.data_primeiro_atendimento)) : this.dataPrimeiroAtendimento
            this.dataValidadePromocao = this.objInteressado.data_validade_promocao ? dateToString(new Date(this.objInteressado.data_validade_promocao)) : ''
            this.proximo_contato_temporario = this.objInteressado.data_proximo_contato ? dateToString(new Date(this.objInteressado.data_proximo_contato)) : ''

            this.horario_proximo_contato = this.objInteressado.horario_proximo_contato ? this.objInteressado.horario_proximo_contato.match(/(\d{2,2}):(\d{2,2})/)[0] : ''
            this.alunoConvertidoId = this.objInteressado.aluno ? this.objInteressado.aluno.id : null
            //this.cursoOferecido = this.objInteressado.curso ? this.objInteressado.curso : {id: null, descricao: 'Selecione'}
            if (this.objInteressado.curso) {
              this.cursoOferecido = this.objInteressado.curso ? this.objInteressado.curso : {id: null, descricao: 'Selecione'}
             
              } else {
                this.cursoOferecido = {
                  id: this.objInteressado.curso_id,
                  descricao: this.objInteressado.curso_descricao
                }
              }             
         
           // this.consultorFuncionario = this.objInteressado.consultor_funcionario_id ? this.objInteressado.consultor_funcionario : {id: null, apelido: 'Selecione'}
            this.consultorFuncionario = this.objInteressado.consultor_funcionario_id
              ? { id: this.objInteressado.consultor_funcionario_id, apelido: this.objInteressado.consultor_funcionario_apelido }
              : {id: null, apelido: 'Selecione'}
            // this.idiomasSelecionados = this.objInteressado.idiomas.map(i => i.id)
            this.idiomasSelecionados = []
            if(this.objInteressado.idiomas != null){
                this.idiomasSelecionados = this.objInteressado.idiomas.split(',')
            }
            
            this.periodoPretendido = this.objInteressado.periodo_pretendido ? this.listaPeriodoPretendido.find(periodo => periodo.value === this.objInteressado.periodo_pretendido) : {id: null, descricao: 'Selecione', value: null}

            this.grauInteresseSelecionado = this.objInteressado.grau_interesse ? this.objInteressado.grau_interesse : null

            if (this.objInteressado.tipo_lead) {
              if (this.objInteressado.tipo_prospeccao) {
                this.tipoContatoSelect = this.objInteressado.tipo_prospeccao
              } else if (this.objInteressado.tipo_contato_id) {
                this.tipoContatoSelect = {
                  id: this.objInteressado.tipo_contato_id,
                  nome: this.objInteressado.tipo_contato_nome,
                  tipo: this.objInteressado.tipo_contato_tipo
                }
              }
              this.setTipoContatoSelect(this.tipoContatoSelect)
            }

            if (this.objInteressado.motivo_nao_fechamento) {
              this.motivoMatriculaPerdidaSelecionado = this.objInteressado.motivo_nao_fechamento
            }

            if (this.objInteressado.workflow_id) {
              let WorkflowUsuario = {
                id: this.objInteressado.workflow_id,
                descricao: this.objInteressado.workflow_descricao,
                situacao: this.objInteressado.workflow_situacao,
                tipo_workflow: this.objInteressado.workflow_tipo_workflow
              }
              this.listaWorkflowsDisponiveis.push(WorkflowUsuario)
              this.setWorkflowSelecionado(WorkflowUsuario)
              this.workflowDescricao = this.objInteressado.workflow_descricao
            }

            if (this.objInteressado.followupComercials !== undefined) {
              let arrayFollowUpComercial = this.objInteressado.followupComercials
              this.followUpsAdicionados = ''
              this.objInteressado.followupComercials = null
              arrayFollowUpComercial.sort((a, b) => {
                return a.id > b.id
              })
              arrayFollowUpComercial.forEach((item) => {
                this.adicionarNovoFollowUp(item.followup)
              })
            }

            if (this.followUpsAdicionados) {
              this.carregarFormularioFollowUp(false)
            }
          })
      }
    },

    // setFormularioDestino (value) {
    //   this.formularioDestino = value
    //   const destino = this.formularioDestino.value
    //   this.rotaDeBusca = this.listaDeRotas.find(rota => rota.value === destino).rota
    // },

    getFormFields () {
      if (this.objFormularioFollowUp) {
        this.camposDoFormularioFollowUp = this.objFormularioFollowUp.formularioFollowUpCampos ? this.objFormularioFollowUp.formularioFollowUpCampos : []
      }
    },

    setNomeInteressado (value) {
      this.interessado = value
      if (this.interessado) {
        this.interessadoId = this.interessado.id
        this.criarTela()
      }
    },

    setFormularioFollowUp (value) {
      this.resetFields()
      this.objFormularioFollowUp = value.id === null ? null : value
      this.getFormFields()
    },

    setAlunoSelecionado (aluno) {
      if (aluno) {
        this.alunoId = aluno.id
        this.buscaCpf = aluno.pessoa.cnpj_cpf
      } else {
        this.alunoId = null
      }
    },

    salvarMatricular () {
      this.isMatriculando = true
      this.salvar(true)
    },

    setValidadeDaPromocao (value) {
      this.dataValidadePromocao = value
    },

    setDateFirstCall (value) {
      this.dataPrimeiroAtendimento = value
    },

    setProximoContatoTemporario (value) {
      this.proximo_contato_temporario = value
    },

    setCursoOferecido (value) {
      this.cursoOferecido = value
    },

    setMotivoMatriculaPerdidaSelecionado (value) {
      this.motivoMatriculaPerdidaSelecionado = value
    },

    setPeriodoPretendido (value) {
      this.periodoPretendido = value
    },

    executaModalInicial () {
      this.$refs.ffInicial.executaRequisicoes()
    },

    executaModalGeral () {
      this.$refs.ffGeral.executaRequisicoes()
    },

    adicionarNovoFollowUp (novoFollowUp) {
      if (novoFollowUp !== undefined) {
        if (this.followUpsAdicionados.length > 0) {
          novoFollowUp += '\n' + this.followUpsAdicionados
        } else {
          novoFollowUp += this.followUpsAdicionados
        }
        this.followUpsAdicionados = novoFollowUp
      }
    },

    finalizar (action = 'cancel') {
      this.$refs.matricularInteressado.hide()
    },

    limparfiltrosCamposDinamicos () {
      this.$store.commit('idioma/SET_PAGINA_ATUAL', 1)
      this.$store.commit('idioma/SET_LISTA', [])

      this.$store.commit('workflow/SET_PAGINA_ATUAL', 1)
      this.$store.commit('workflow/SET_LISTA', [])

      this.$store.commit('curso/SET_PAGINA_ATUAL', 1)
      this.$store.commit('curso/SET_LISTA', [])

      this.$store.commit('prospeccao/SET_PAGINA_ATUAL', 1)
      this.$store.commit('prospeccao/SET_LISTA', [])

      this.$store.commit('tipoContato/SET_PAGINA_ATUAL', 1)
      this.$store.commit('tipoContato/SET_LISTA', [])

      this.$store.commit('formularioFollowUp/SET_PAGINA_ATUAL', 1)
      this.$store.commit('formularioFollowUp/SET_LISTA', [])

      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_LISTA', [])

      this.$store.commit('motivosMatriculaPerdida/SET_PAGINA_ATUAL', 1)
      this.$store.commit('motivosMatriculaPerdida/SET_LISTA', [])
    },

    listarCamposDinamicos () {
      this.listarFuncionario()
      this.listarWorkflow()
        .then(response => {
          let apresentacaoPessoalORM = response.find(item => (item.tipo_workflow === 'WRAP'))
          this.listaWorkflowsDisponiveis.push(Object.assign({}, apresentacaoPessoalORM))
        })
      this.$store.dispatch('idioma/listar')
      this.$store.dispatch('curso/listar')
      this.$store.dispatch('tipoContato/listar')
      this.$store.dispatch('prospeccao/listar')
      this.$store.dispatch('formularioFollowUp/listar')
        .then(({ itens }) => {
          this.listaFormularios = [{id: null, descricao_formulario: 'Selecione'}, ...itens]
        })

      this.$store.dispatch('motivosMatriculaPerdida/listar')
      this.$store.dispatch('funcionario/listar').then(() => {
        let usuarioFuncionarioLogado = this.usuarioLogado.funcionarios ? this.usuarioLogado.funcionarios[0] : {id: null, apelido: 'Selecione'}
        let funcionario = [...this.listaFuncionariosRequisicao.filter(funcionario => (funcionario.consultor === true) || (funcionario.cargo.tipo === 'GER'))].find(funcionario => funcionario.id === usuarioFuncionarioLogado.id)
        this.consultorResponsavelFuncionario = funcionario || {id: null, apelido: 'Selecione'}
      })
    },

    setConsultorFuncionario (value) {
      this.consultorFuncionario = value.id === null ? null : value
    },

    setWorkflowSelecionado (value) {
      this.workflowSelecionado = value
      this.setWorkflowAcaoSelecionado({id: null, descricao: 'Selecione'})
      if (value && value.id) {
        this.listaWorkflowAcaoPorWorkflowSelecionado(value.id)
      }
    },

    setWorkflowAcaoSelecionado (value) {
      this.workflowAcaoSelecionado = value
      this.objInteressado.workflow_acao = value.id
      if (this.objInteressado.motivo_nao_fechamento && !value.id) {
        this.setMotivoMatriculaPerdidaSelecionado(this.objInteressado.motivo_nao_fechamento)
      } else {
        this.setMotivoMatriculaPerdidaSelecionado(this.listaMotivoMatriculaPerdida.find((item) => (item.id === null)))
      }
    },

    setTipoContatoSelect (value) {
      if (!value) {
        return
      }

      if (this.objInteressado.tipo_lead === 'A') {
        this.tipoContatoSelect = value
        this.SET_TIPO_PROSPECCAO(value.id)
        this.SET_TIPO_CONTATO(null)
      } else {
        this.tipoContatoSelect = value
        this.SET_TIPO_CONTATO(value.id)
        this.SET_TIPO_PROSPECCAO(null)
      }

      if ((this.objInteressado.tipo_lead === 'R') && (this.tipoContatoSelect.tipo === 'CPSSL')) {
        let WorkflowSelecionado = this.listaWorkflowsDisponiveis.find(item => (item.tipo_workflow === 'WRAP'))
        this.setWorkflowSelecionado(WorkflowSelecionado)
      } else {
        if (this.objInteressado.workflow_id !== this.workflowSelecionado.id) {
          this.setWorkflowSelecionado({
            id: this.objInteressado.workflow_id,
            descricao: this.objInteressado.workflow_descricao,
            situacao: this.objInteressado.workflow_situacao,
            tipo_workflow: this.objInteressado.workflow_tipo_workflow
          })
        }
      }
    },

    limparTipoContatoSelect (tipo) {
      this.tipoContatoSelect = tipo === 'A' ? {id: null, descricao: 'Selecione', tipo: ''} : {id: null, nome: 'Selecione', tipo: ''}
      if ((tipo === 'R') && (this.tipoContatoSelect.tipo === 'CPSSL')) {
        let WorkflowSelecionado = this.listaWorkflowsDisponiveis.find(item => (item.tipo_workflow === 'WRAP'))
        this.setWorkflowSelecionado(WorkflowSelecionado)
      } else {
        if (this.objInteressado.workflow_id !== this.workflowSelecionado.id) {
          this.setWorkflowSelecionado({
            id: this.objInteressado.workflow_id,
            descricao: this.objInteressado.workflow_descricao,
            situacao: this.objInteressado.workflow_situacao,
            tipo_workflow: this.objInteressado.workflow_tipo_workflow
          })
        }
      }

      this.carregarFormularioFollowUp()
    },

    carregarFormularioFollowUp (isInicial = true) {
      const listaDeTipoContato = {
        'R': 'CR',
        'A': 'CA'
      }
      const tipoFormulario = listaDeTipoContato[this.objInteressado.tipo_lead]
      
      const objForm = this.listaFormularios.find(item => (item.tipo_formulario === tipoFormulario) && item.follow_up_inicial === isInicial)
      if (objForm) {
        this.setFormularioFollowUp(objForm)
      }
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      if (this.bVeioListagemFollowup === true) {
        this.$router.push('/comercial/follow-up')
        return
      }
      if (this.isModal) {
        this.back()
        this.$emit('hide')
        this.resetData(true)
        return
      }

      this.$router.push(this.$route.matched[0].path)
    },

    adicionarNovoInteressado () {
      if (this.isModal) {
        this.LIMPAR_ITEM_SELECIONADO()
        this.resetData(true)
        this.criarTela()

        return
      }

      this.$router.go()
    },

    adicionarNivelamento () {
      this.$refs.modalFormularioDeNivelamento.interessado = this.objInteressado
      this.$refs.modalFormularioDeNivelamento.visibilidadeModalAtividadeNivelamento = true
    },

    finalizaRequisicao () {
      this.isMatriculando = false
      this.isEnviando = false
    },

    montaParametros () {
      const dataFormatada = this.proximo_contato_temporario ? stringToISODate(this.proximo_contato_temporario, true) : null
      const dataFormatadaValidadePromocao = this.dataValidadePromocao ? stringToISODate(this.dataValidadePromocao, true) : null
      const dataPrimeiroAtendimentoFormatada = this.dataPrimeiroAtendimento ? stringToISODate(this.dataPrimeiroAtendimento, true) : null

      this.objInteressado.nome = this.interessado.nome
      this.objInteressado.pessoa_indicou_nome = this.interessado.pessoa_indicou_nome

      this.objInteressado.consultor_funcionario = this.consultorFuncionario.id
      this.objInteressado.consultor_responsavel_funcionario = this.consultorResponsavelFuncionario.id
      this.objInteressado.data_primeiro_atendimento = dataPrimeiroAtendimentoFormatada
      this.objInteressado.data_proximo_contato = dataFormatada
      this.objInteressado.horario_proximo_contato = this.horario_proximo_contato
      this.objInteressado.grau_interesse = this.grauInteresseSelecionado

      this.objInteressado.periodo_pretendido = this.periodoPretendido.value
      this.objInteressado.curso = this.cursoOferecido.id
      this.objInteressado.data_validade_promocao = dataFormatadaValidadePromocao
      this.objInteressado.idiomas = (this.idiomasSelecionados.length > 0 ? this.idiomasSelecionados : null)
      this.objInteressado.follow_ups = []
      this.camposDoFormularioFollowUp.forEach(campo => {
        if (this.dadosFollowUps[campo.id]) {
          this.objInteressado.follow_ups.push(this.factoryFollowUp(campo.nome_campo, this.dadosFollowUps[campo.id]))
        }
      })
      this.objInteressado.formulario_follow_up = this.objFormularioFollowUp ? this.objFormularioFollowUp.id : null
      if (this.objInteressado.formulario_follow_up === null) {
        delete this.objInteressado.formulario_follow_up
      }

      this.objInteressado.motivo_nao_fechamento = this.motivoMatriculaPerdidaSelecionado && this.motivoMatriculaPerdidaSelecionado.id !== null ? this.motivoMatriculaPerdidaSelecionado.id : null

      
      delete this.objInteressado.agendaComerciais
    },

    redirecionar () {
      if (this.alunoId) {
        this.$router.push(`/academico/aluno/atualizar/${this.alunoId}?matriculando=true&interessado=${this.itemSelecionadoID}&cpf=${this.buscaCpf}`)
      } else {
        this.$router.replace(`/cadastros/interessados/atualizar/${this.itemSelecionadoID}`)
        this.$router.push(`/academico/aluno/adicionar?matriculando=true&interessado=${this.itemSelecionadoID}&cpf=${this.buscaCpf}`)
      }
    },

    salvar (bSalvarESair) {

      this.montaParametros()
      this.isEnviando = true
      this.isValid = true;
       
      if(this.$v.$invalid){
        this.isValid = false;
        this.isEnviando = false;
        EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: "Formulário inválido verifique os campos!"
          })
        
      }
      if(this.validaRequiredCampoMotivoMatriculaPerdida()){
         if(!this.motivoMatriculaPerdidaSelecionado || !this.motivoMatriculaPerdidaSelecionado.id){
          this.isValid = false;
                  EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: "Formulário invalido, necessário preencher o motivo da matricula"
          })

          
        }
      }

      //verifica se precisa validar validaRequiredCampoMotivoMatriculaPerdida
      if (!this.isValid) {
    

        this.finalizaRequisicao()
        this.isEnviando = false
 
        return
      }



      if (this.itemSelecionadoID) {
      setTimeout(() => {
        this.atualizarDadosFollowup().then(() => {
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
          .finally(() => {
           
            this.isEnviando = false
          })
           }, 50)
      } else {
       setTimeout(() => {
        this.atualizarDadosFollowup().then(() => {
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
          .finally(() => {
           
            this.isEnviando = false
          })
      }, 50)
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

    factoryFollowUp (label, dado) {
      return Object.assign({'label': label, 'dado': dado || ''})
    },

    resetFields (resetForm = false) {
      this.camposDoFormularioFollowUp = []
      this.dadosFollowUps = []
    }
  }
}
</script>

<style>

#observacao_follow_up {
  height: 250px;
}

.aviso {
  width: 100%;
  text-align: center
}

div.typeahead-container div.resultContainer{
  position: relative!important;
}

</style>
