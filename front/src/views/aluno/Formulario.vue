<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="submit()">
      <div v-if="paginaEditar" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="true" class="form-loading screen-load">
        <load-placeholder :loading="verificaCarregamento(loadCount, 4)" />
      </div>
      

      <div class="body-sector">
        <div class="animated fadeIn p-3">
          <div class="row row-flex row-flex-wrap">
            <div class="col-md-2">
              <label v-help-hint="'form-aluno_imagem'" :for="preview ? '' : 'up-image'" :preview="preview = imageData.length > 0" class="upload-area">

                <div v-if="preview || itemSelecionado.foto" class="image-preview">
                  <div v-if="preview" class="img-container">
                    <img :src="imageData">
                  </div>
                  <div v-else-if="itemSelecionado.foto && itemSelecionado.foto.length" class="img-container">
                    <img :src="informacaoImagem[1]">
                  </div>

                  <div class="d-flex justify-content-around">
                    <label for="up-image" title="Alterar" class="icone-link">
                      <font-awesome-icon icon="pen" /> Alterar
                    </label>
                    <label title="Remover" class="icone-link text-muted" @click.prevent="cleanImage()">
                      <font-awesome-icon icon="trash-alt" /> Remover
                    </label>
                  </div>
                </div>

                <div v-else class="image-load">
                  <font-awesome-icon icon="user" />
                  <span>Selecione uma foto</span>
                </div>

                <input id="up-image" type="file" class="d-none" accept="image/*" @change="previewImage">
              </label>
            </div>

            <div class="col-md-10">
              <div class="row form-group">
                <div class="col-md-12">
                  <label v-help-hint="'form-aluno_nome_contato'" for="nome_contato" class="col-form-label">Nome *</label>
                  <input id="nome_contato" v-model="pessoaSelecionada.nome_contato" type="text" class="form-control" maxlength="150">
                  <div class="invalid-feedback">Preencha o nome!</div>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-6">
                  <label v-help-hint="'form-aluno_cnpj_cpf_pessoa'" for="cnpj_cpf_pessoa" class="col-form-label">CPF</label>
                  <input v-mask="'###.###.###-##'" id="cnpj_cpf_pessoa" v-model="pessoaSelecionada.cnpj_cpf" :class="{'is-invalid': cpfInvalido}" type="text" class="form-control" @blur="cpfInvalido = $v.pessoaSelecionada.cnpj_cpf.$invalid">
                  <div v-if="cpfInvalido" class="multiselect-invalid">CPF informado é inválido!</div>
                </div>

                <div class="col-md-3">
                  <label v-help-hint="'form-aluno_numero_identidade'" for="numero_identidade" class="col-form-label">Identidade (RG)</label>
                  <input id="numero_identidade" v-model="pessoaSelecionada.numero_identidade" type="text" class="form-control" maxlength="15">
                </div>
                <div class="col-md-3">
                  <label v-help-hint="'form-aluno_orgao_emissor'" for="orgao_emissor" class="col-form-label">Órgão Emissor</label>
                  <input id="orgao_emissor" v-model="pessoaSelecionada.orgao_emissor" type="text" class="form-control" placeholder="ex.: SSP" maxlength="10">
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-6">
                  <label v-help-hint="'form-aluno_estado_civil'" for="estado_civil" class="col-form-label">Estado Civil</label>
                  <g-select id="estado_civil" v-model="pessoaSelecionada.estado_civil" :options="listaEstadoCivil" label="descricao" track-by="value" />
                </div>

                <div class="col-md-6">
                  <label v-help-hint="'form-aluno_sexo'" for="sexo" class="col-form-label">Sexo</label>
                  <g-select id="sexo" v-model="pessoaSelecionada.sexo" :options="listaSexo" label="descricao" track-by="value" />
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-6">
                  <label for="data_nascimento_selecionado" class="col-form-label">Data de Nascimento *</label>
                  <g-datepicker :element-id="'data_nascimento_selecionado'" :class="{'valid-input' : !isValid}" :value="data_nascimento_selecionado" :selected="setDataNascimento" class="form-control" required/>
                  <div v-if="!isValid && $v.data_nascimento_selecionado.$invalid" class="multiselect-invalid">
                    Informe a data de nascimento!
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="col-form-label">Emancipado?</label>
                  <b-form-radio-group
                    v-model="emancipado"
                    :options="[{text: 'Sim', value: true}, {text: 'Não', value: false}]"
                    name="emancipado"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="content-sector sector-azul p-3">
          <h5 class="title-module mb-2">Contatos</h5>
          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-aluno_telefone_preferencial'" for="telefone_preferencial" class="col-form-label">Telefone (celular)</label>
              <input v-mask="['(##) #####-####']" id="telefone_preferencial" v-model="pessoaSelecionada.telefone_preferencial" :class="{ 'invalid-input' : $v.pessoaSelecionada.telefone_preferencial.$invalid }" type="text" class="form-control">
              <div v-if="!isValid && $v.pessoaSelecionada.telefone_preferencial.$invalid" class="input-invalid">Preencha corretamente!</div>
            </div>
            <div class="col-md-6">
              <label v-help-hint="'form-aluno_email_preferencial'" for="email_preferencial" class="col-form-label">E-mail</label>
              <input id="email_preferencial" v-model="pessoaSelecionada.email_preferencial" :class="{ 'is-invalid' : email_preferencialInvalido }" type="text" class="form-control" maxlength="50" @blur="email_preferencialInvalido = $v.pessoaSelecionada.email_preferencial.$invalid">
              <div v-if="email_preferencialInvalido" class="input-invalid">Preencha corretamente o e-mail!</div>
            </div>
          </div>

          <div class="content-sector-extra p-3">
            <div class="d-flex align-items-center">
              <a v-b-toggle.contatos-extra class="btn-contatos-extra align-self-center">Contatos adicionais <font-awesome-icon icon="plus" /></a>
            </div>
            <b-collapse id="contatos-extra" class="mt-2">
              <div class="form-group row">
                <div class="col-md-6">
                  <label v-help-hint="'form-aluno_telefone_contato'" for="telefone_contato" class="col-form-label">Telefone (contato)</label>
                  <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_contato" v-model="pessoaSelecionada.telefone_contato" type="text" class="form-control">
                  <div v-if="!isValid && $v.pessoaSelecionada.telefone_contato.$invalid" class="input-invalid">Preencha corretamente!</div>
                </div>
                <div class="col-md-6">
                  <label v-help-hint="'form-aluno_email_contato'" for="email_contato" class="col-form-label">E-mail (contato)</label>
                  <input id="email_contato" v-model="pessoaSelecionada.email_contato" :class="{ 'is-invalid' : email_contatoInvalido }" type="text" class="form-control" maxlength="50" @blur="email_contatoInvalido = $v.pessoaSelecionada.email_contato.$invalid">
                  <div v-if="email_contatoInvalido" class="input-invalid">Preencha corretamente o e-mail!</div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-6">
                  <label v-help-hint="'form-aluno_telefone_profissional'" for="telefone_profissional" class="col-form-label">Telefone (empresarial)</label>
                  <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_profissional" v-model="pessoaSelecionada.telefone_profissional" type="text" class="form-control">
                  <div v-if="!isValid && $v.pessoaSelecionada.telefone_profissional.$invalid" class="input-invalid">Preencha corretamente!</div>
                </div>
                <div class="col-md-6">
                  <label v-help-hint="'form-aluno_email_profissional'" for="email_profissional" class="col-form-label">E-mail (empresarial)</label>
                  <input id="email_profissional" v-model="pessoaSelecionada.email_profissional" :class="{ 'is-invalid' : email_profissionalInvalido }" type="text" class="form-control" maxlength="50" @blur="email_profissionalInvalido = $v.pessoaSelecionada.email_profissional.$invalid">
                  <div v-if="email_profissionalInvalido" class="input-invalid">Preencha corretamente o e-mail!</div>
                </div>
              </div>
            </b-collapse>
          </div>

        </div>

        <g-form-endereco id="form_aluno-endereco" :cep-data="cep_data" :callback-cep-data="setCepData" />

        <div class="content-sector sector-azul p-3">
          <div class="d-flex justify-content-between mb-2">
            <h5 class="title-module mb-2">Responsáveis</h5>
            <!-- <b-btn v-b-modal.responsavel variant="azul" type="button"><font-awesome-icon icon="plus" /></b-btn> -->
          </div>

          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-aluno_input_responsavel_financeiro'" for="input_responsavel_financeiro" class="col-form-label">Responsável Financeiro {{ obrigatorioRespFinanceiro ? '*' : '' }}</label>
              <div v-if="itemSelecionado.responsavel_financeiro_pessoa && itemSelecionado.responsavel_financeiro_pessoa.id" class="d-flex">
                <span class="form-control form-control-disabled flex-grow">
                  {{ itemSelecionado.responsavel_financeiro_pessoa.nome_contato || itemSelecionado.responsavel_financeiro_pessoa.nome_fantasia || itemSelecionado.responsavel_financeiro_pessoa.razao_social }}
                </span>
                <b-btn variant="link" @click="setResponsavelFinanceiro(null)">Limpar</b-btn>
              </div>
              <template v-else>
                <typeahead id="'input_responsavel_financeiro'"
                           :item-hit="setResponsavelFinanceiro"
                           :invalid="!isValid && $v.responsavelFinanceiro.$invalid"
                           :actions="[adicionarResponsavelAction]"
                           source-path="/api/pessoa/buscar_por_nome" />
              </template>
              <div v-if="!isValid && $v.responsavelFinanceiro.$invalid" class="multiselect-invalid">
                Selecione um responsável financeiro para o menor de idade!
              </div>
            </div>

            <div class="col-md-6">
              <label v-help-hint="'form-aluno_input_responsavel_financeiro_relacao'" for="input_responsavel_financeiro_relacao" class="col-form-label">Relacionamento</label>
              <g-select id="input_responsavel_financeiro_relacao"
                        v-model="itemSelecionado.responsavel_financeiro_relacionamento_aluno"
                        :options="listaRelacionamentosAluno"
                        label="descricao"
                        track-by="id"
              />
            </div>
          </div>

          <div v-if="menorSelecionado" class="list-group-item list-group-item-accent-info list-group-item-warning border-0">
            <font-awesome-icon icon="exclamation-triangle" /> O responsável financeiro não pode ser menor de idade.
          </div>

          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-aluno_input_responsavel_didatico'" for="input_responsavel_didatico" class="col-form-label">Responsável Didático</label>

              <div v-if="itemSelecionado.responsavel_didatico_pessoa" class="d-flex">
                <span class="form-control form-control-disabled flex-grow">
                  {{ itemSelecionado.responsavel_didatico_pessoa.nome_contato || itemSelecionado.responsavel_didatico_pessoa.nome_fantasia || itemSelecionado.responsavel_didatico_pessoa.razao_social }}
                </span>
                <b-btn variant="link" @click="setResponsavelDidatico(null)">Limpar</b-btn>
              </div>
              <template v-else>
                <typeahead id="'input_responsavel_didatico'"
                           :item-hit="setResponsavelDidatico"
                           :actions="[adicionarResponsavelAction]"
                           source-path="/api/pessoa/buscar_por_nome" />
              </template>

            </div>

            <div class="col-md-6">
              <label v-help-hint="'form-aluno_input_responsavel_didatico_relacao'" for="input_responsavel_didatico_relacao" class="col-form-label">Relacionamento</label>
              <g-select id="input_responsavel_didatico_relacao"
                        v-model="itemSelecionado.responsavel_didatico_relacionamento_aluno"
                        :options="listaRelacionamentosAluno"
                        label="descricao"
                        track-by="id"
              />
            </div>
          </div>

        </div>

        <div class="content-sector sector-roxo-c p-3">
          <div class="content-sector-extra p-3">
            <div class="d-flex align-items-center">
              <a v-b-toggle.influx-dollar class="btn-contatos-extra btn-influx-dollar-extra align-self-center">Saldo inFlux Dollar <font-awesome-icon icon="plus" /></a>
            </div>
            <b-collapse id="influx-dollar" class="mt-2">
              <b-row class="header-card-list mt-2">
                <b-col md="2">
                  <label class="col-form-label">Data</label>
                </b-col>
                <b-col md="2">
                  <label class="col-form-label">Operação</label>
                </b-col>
                <b-col md="3">
                  <label class="col-form-label">Descrição</label>
                </b-col>
                <b-col md="2">
                  <label class="col-form-label">Valor</label>
                </b-col>
                <b-col md="3">
                  <label class="col-form-label">Saldo</label>
                </b-col>
              </b-row>

              <b-row v-for="(item, index) in listaDollarInflux" :key="index" class="body-card-list">
                <b-col md="2" data-header="Data">{{ item.data_movimento | formatarData }}</b-col>
                <b-col :data-tipo="item.tipo_operacao" md="2" data-header="Operação" >{{ item.tipo_operacao === 'C' ? 'Crédito' : item.tipo_operacao === 'S' ? 'Saque' : 'Débito' }}</b-col>
                <b-col md="3" data-header="Descrição">{{ item.atividade_dollar ? item.atividade_dollar.descricao : 'Compra de Material' }}</b-col>
                <b-col md="2" data-header="Valor">{{ item.valor | formatarMoeda(false, true) }}</b-col>
                <b-col :data-saldo="item.saldo > 0 ? 'P' : 'N' " md="3" data-header="Saldo" >{{ item.saldo | formatarMoeda(false, true) }}</b-col>

              </b-row>

              <b-row class="header-card-list mt-2">
                <b-col md="2"/>
                <b-col md="2"/>
                <b-col md="3"/>
                <b-col md="2" class="d-flex">
                  <label class="col-form-label ml-auto">Total iU$</label>
                </b-col>
                <b-col md="3">
                  <!-- <vue-numeric id="saldo_dollar_influx" :empty-value="0" :max="9999999.99" v-model="saldo_dollar_influx" separator="." disabled type="text" class="form-control form-control-disabled"/> -->
                  <input id="saldo_dollar_influx" v-model="saldo_dollar_influx" disabled type="text" class="form-control form-control-disabled">
                </b-col>
              </b-row>
            </b-collapse>
          </div>

          <div class="row">
            <div class="col-md-4">
              <label v-help-hint="'form-aluno_input_escolaridade'" for="input_escolaridade" class="col-form-label">Escolaridade</label>
              <g-select id="input_escolaridade"
                        v-model="itemSelecionado.escolaridade"
                        :options="listaEscolaridades"
                        label="descricao"
                        track-by="id"
              />
            </div>

            <div class="col-md-4">
              <label v-help-hint="'form-aluno_input_tipo_visibilidade'" for="input_tipo_visibilidade" class="col-form-label">Pesquisa de visibilidade *</label>
              <g-select id="input_tipo_visibilidade"
                        :multi-tag="true"
                        :value="tagDePesquisaDeVisibilidade"
                        :select="setPesquisaDeVisibilidade"
                        :options="listaVisibilidade"
                        :invalid="!isValid && $v.itemSelecionado.tipo_visibilidade.$invalid"
                        label="descricao"
                        class="multiselect-truncate g-multiselect-tag"
                        track-by="id"
              />
              <div v-if="!isValid && $v.itemSelecionado.tipo_visibilidade.$invalid" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </div>

            <div class="col-md-4">
              <label v-help-hint="'form-aluno_input_classificacao_aluno'" for="input_classificacao_aluno" class="col-form-label">Classificação do aluno *</label>
              <g-select id="input_classificacao_aluno"
                        v-model="itemSelecionado.classificacao_aluno"
                        :options="listaClassificacaoAluno"
                        :invalid="!isValid && !itemSelecionado.classificacao_aluno"
                        label="nome"
                        track-by="id"
              />
              <div v-if="!isValid && !itemSelecionado.classificacao_aluno" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label v-help-hint="'form-aluno_observacao'" for="observacao" class="col-form-label">Observações</label>
              <textarea id="observacao" v-model="pessoaSelecionada.observacao" class="form-control full-textarea" rows="6" maxLength="5000"></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - (pessoaSelecionada.observacao || '').length }}</span>
            </div>
          </div>
        </div>

        <!-- FORM RESPONSAVEL -->
        <formulario-responsavel ref="responsavel" :load-categories="false" :is-modal="true" :modal-state="modal_state" @resolve="responsavelSalva" @reject="cancelarResponsavel()" />
      </div>

      <div class="form-group row">
        <div class="col-md-12">
          <b-btn v-if="!matriculando" :disabled="isSubmiting || isSubmitingMatricularJunto" type="submit" class="btn btn-verde" variant="verde">
            {{ isSubmiting ? 'Salvando...' : 'Salvar' }}
          </b-btn>
          <b-btn :disabled="isSubmitingMatricularJunto || isSubmiting" type="button" class="btn btn-roxo" variant="roxo" @click="isMatricular = true, submit()">

            <template v-if="isSubmitingMatricularJunto">Salvando...</template>
            <template v-else>{{ !matriculando ? 'Novo contrato' : 'Iniciar contrato' }}</template>

            <!-- {{ isSubmitingMatricularJunto ? 'Salvando...' : 'Iniciar contrato' }} -->
          </b-btn>
          <b-btn variant="link" @click="voltar()">Voltar</b-btn>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapMutations, mapActions, mapState} from 'vuex'
import {required, minLength, email, helpers, requiredIf} from 'vuelidate/lib/validators'
import Typeahead from '../../components/Typeahead.vue'
import FormularioResponsavel from '../responsavel/Formulario.vue'
import {dateToString, stringToISODate} from '../../utils/date'
import {isCpfValido} from '../../utils/format'
import EventBus from '../../utils/event-bus'
// import HostUrl from '../../utils/host-url'
import item from '@/store/item'
var host = process.env.VUE_APP_HOST;

const tipoDocumento = (value) => !helpers.req(value) || isCpfValido(value)

const precisaResponsavel = (value, vm) => {
  if (vm.verificaRespFin && (vm.itemSelecionado.responsavel_financeiro_pessoa === null || vm.itemSelecionado.responsavel_financeiro_pessoa === undefined)) {
    return false
  }
  if (vm.verificaRespFin && !!vm.itemSelecionado.responsavel_financeiro_pessoa && vm.itemSelecionado.responsavel_financeiro_pessoa.id === null) {
    return false
  }
  return true
}

export default {
  components: {
    Typeahead,
    'formulario-responsavel': FormularioResponsavel
  },

  data () {
    return {
      loadCount: 0,
      paginaEditar: false,
      isSubmiting: false,
      isSubmitingMatricularJunto: false,
      isValid: true,
      cpfInvalido: false,
      email_preferencialInvalido: false,
      email_contatoInvalido: false,
      email_profissionalInvalido: false,
      isMatricular: false,
      mostrarCamposResponsaveis: true,
      obrigatorioRespFinanceiro: false,
      interessadoId: null,
      buscaCpf: null,
      matriculando: false,
      data_nascimento_selecionado: '',
      imageData: '',
      listaDollarInflux: [],
      saldo_dollar_influx: 0,

      adicionarResponsavelAction: {
        text: 'Adicionar responsável',
        icon: 'plus',
        action: (cnpjCpf) => {
          this.$refs.responsavel.bPermiteUsarEnderecoAluno = true
          const cepObjeto = Object.assign({}, this.cep_data)
          this.$refs.responsavel.setEnderecoAlunoTemp(cepObjeto)
          this.$refs.responsavel.setCnpjCpf(cnpjCpf)
          this.$refs.responsavel.$refs.responsavel.show()
        }
      },
      cep_data: {
        cep_endereco: '',
        endereco: '',
        numero_endereco: '',
        complemento_endereco: '',
        bairro_endereco: '',
        estado: '',
        cidade: ''
      },
      informacaoImagem: [],
      listaEstadoCivil: [
        {value: 'N', descricao: 'Não Informar'},
        {value: 'S', descricao: 'Solteiro(a)'},
        {value: 'C', descricao: 'Casado(a)'},
        {value: 'D', descricao: 'Divorciado'}
      ],
      listaSexo: [
        {value: 'N', descricao: 'Não Informar'},
        {value: 'M', descricao: 'Masculino'},
        {value: 'F', descricao: 'Feminino'},
        {value: 'O', descricao: 'Outro'}
      ],
      preview: false,

      modal_state: false,

      menorSelecionado: false,

      tagDePesquisaDeVisibilidade: []
    }
  },
  validations: {
    itemSelecionado: {
      classificacao_aluno: {required},
      tipo_visibilidade: {required: requiredIf(function () {
        return this.tagDePesquisaDeVisibilidade.length === 0
      })}
    },
    pessoaSelecionada: {
      data_nascimento: {required},
      nome_contato: {required},
      telefone_preferencial: {minLength: minLength(15)},
      telefone_contato: {minLength: minLength(14)},
      telefone_profissional: {minLength: minLength(14)},
      email_preferencial: {email},
      email_contato: {email},
      email_profissional: {email},
      cnpj_cpf: {tipoDocumento}
    },
    responsavelFinanceiro: {precisaResponsavel},
    data_nascimento_selecionado: {required}
  },
  computed: {
    ...mapState('aluno', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('pessoas', { pessoaSelecionada: 'objPessoa' }),
    ...mapState('tipoVisibilidade', {listaVisibilidadeRequisicao: 'lista'}),
    ...mapState('midia', {listaMidiaRequisicao: 'lista'}),
    ...mapState('classificacaoAlunos', ['listaClassificacaoAluno']),
    ...mapState('escolaridade', {listaEscolaridades: 'lista'}),

    listaResponsaveis: {
      get () {
        return [{id: null, nome_contato: 'Aluno...'}].concat(this.$store.state.pessoas.listaPessoas)
      }
    },

    listaRelacionamentosAluno: {
      get () {
        return this.$store.state.relacionamentoAluno.lista
      }
    },

    listaVisibilidade: {
      get () {
        return this.listaMidiaRequisicao
      }
    },

    listaEscolaridades: {
      get () {
        return this.$store.state.escolaridade.lista
      }
    },

    verificaRespFin () {
      return (this.verificaMenorIdade())
    },

    obrigatorioResponsavel () {
      return this.obrigatorioRespFinanceiro
    },

    emancipado: {
      get () {
        return this.itemSelecionado.emancipado
      },

      set (value) {
        this.SET_EMANCIPADO(value)
      }
    }
  },

  watch: {
    cep_endereco_pessoa (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
    },

    emancipado (value) {
      this.verificaMenorIdade()
    }
  },

  mounted () {
    this.$store.commit('midia/SET_PAGINA_ATUAL', 1)
    this.$store.commit('pessoas/SET_PAGINA_ATUAL', 1)
    this.$store.commit('classificacaoAlunos/SET_PAGINA_ATUAL', 1)
    this.$store.commit('escolaridade/SET_PAGINA_ATUAL', 1)
    this.$store.commit('relacionamentoAluno/SET_PAGINA_ATUAL', 1)
    this.$store.commit('pessoas/setListaPessoas', [])
    this.$store.commit('classificacaoAlunos/SET_LISTA', [])
    this.$store.commit('escolaridade/SET_LISTA', [])
    this.$store.commit('relacionamentoAluno/SET_LISTA', [])
    this.getListaClassificacaoAluno()
    this.$store.dispatch('midia/listar')
    this.$store.dispatch('pessoas/getListaPessoas').then(this.countCarregamento)
    this.$store.dispatch('escolaridade/listar').then(this.countCarregamento)
    this.$store.dispatch('relacionamentoAluno/listar').then(this.countCarregamento)
    this.buscarTodosTiposVisibilidades().then(this.countCarregamento)

    this.LIMPAR_ITEM_SELECIONADO()
    this.limparPessoa()

    const interessadoPreConvertido = this.$route.query.interessado
    this.interessadoId = interessadoPreConvertido || null

    const cpfPreConvertido = this.$route.query.cpf
    this.pessoaSelecionada.cnpj_cpf = cpfPreConvertido || null

    const matriculandoPreConvertido = this.$route.query.matriculando
    this.matriculando = matriculandoPreConvertido || false

    const id = this.$route.params.id

    if (id) {
      this.paginaEditar = true
      this.SET_ITEM_SELECIONADO(id)
      this.buscar()
        .then(pessoa => {
          if ((this.itemSelecionado.foto !== undefined) && (this.itemSelecionado.foto !== '')) {
            let localArquivo = this.itemSelecionado.foto.replace('./../public', '')
            let nomeArquivo = this.itemSelecionado.foto.replace('./../public/uploads/', '')
            this.informacaoImagem[0] = nomeArquivo
            this.informacaoImagem[1] = host + localArquivo
          }
          this.setPessoaSelecionada(pessoa)
          this.mostraResponsaveis()
          if (interessadoPreConvertido) {
            this.configuraInteressado(interessadoPreConvertido)
          }
          this.configuraPesquisaDeVisibilidade()
          this.setEnderecoAluno()
        })
        .catch((error) => {
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Aluno não encontrado'
          })

          this.voltar()
        })
      this.listarDollarInflux(id)
        .then(response => {
          const lista = response.itens

          let itemSaldo = 0

          this.listaDollarInflux = lista.filter((item) => {
            const valor = item.valor * 1
            itemSaldo += valor
            item.saldo = itemSaldo
            return valor * 1 > 0
          })

          this.saldo_dollar_influx = response.saldo
        })
    } else {
      this.mostraResponsaveis()
      if (interessadoPreConvertido) {
        this.configuraInteressado(interessadoPreConvertido)
      }
    }
  },
  methods: {
    ...mapMutations('aluno', ['SET_ITEM_SELECIONADO', 'LIMPAR_ITEM_SELECIONADO', 'SET_CLASSIFICACAO_ALUNO', 'SET_EMANCIPADO', 'SET_PESSOA', 'SET_FOTO', 'SET_ESTA_CARREGANDO', 'SET_ESCOLARIDADE', 'SET_INTERESSADO_ID']),
    ...mapMutations('pessoas', ['setPessoa', 'limparPessoa', 'SET_CATEGORIA']),
    ...mapMutations('interessados', {setInteressadoId: 'SET_ITEM_SELECIONADO_ID', limparInteressadoSelecionado: 'LIMPAR_ITEM_SELECIONADO'}),
    ...mapActions('interessados', {buscarInteressado: 'buscar'}),
    ...mapActions('aluno', ['buscar', 'criar', 'atualizar']),
    ...mapActions('movimentoDollarInflux', {listarDollarInflux: 'listar'}),
    ...mapActions('pessoas', ['criarPessoa', 'atualizarPessoa', 'getListaPessoas']),
    ...mapActions('tipoVisibilidade', {buscarTodosTiposVisibilidades: 'buscarTodos'}),
    ...mapActions('classificacaoAlunos', ['getListaClassificacaoAluno']),

    setEnderecoAluno () {
      this.cep_data = {
        cep_endereco: this.pessoaSelecionada.cep_endereco,
        endereco: this.pessoaSelecionada.endereco,
        numero_endereco: this.pessoaSelecionada.numero_endereco,
        complemento_endereco: this.pessoaSelecionada.complemento_endereco,
        bairro_endereco: this.pessoaSelecionada.bairro_endereco,
        estado: this.pessoaSelecionada.estado,
        cidade: this.pessoaSelecionada.cidade
      }
    },

    configuraInteressado (id) {
      this.setInteressadoId(id)
       this.buscarInteressado()
        .then(item => {
          this.pessoaSelecionada.nome_contato = item.nome
          if (item.email_contato !== undefined) {
            this.pessoaSelecionada.email_preferencial = item.email_contato
          }
          if (item.email_secundario !== undefined) {
            this.pessoaSelecionada.email_contato = item.email_secundario
          }
          if (item.telefone_contato !== undefined) {
            this.pessoaSelecionada.telefone_preferencial = item.telefone_contato
          } 
          if (item.franqueada_id !== undefined) {
            this.pessoaSelecionada.franqueada_id = item.franqueada_id
          }
          if (item.telefone_secundario !== undefined) {
            this.pessoaSelecionada.telefone_contato = item.telefone_secundario
          }
          if (item.sexo !== undefined) {
            // this.pessoaSelecionada.sexo = item.sexo
            this.pessoaSelecionada.sexo = this.listaSexo.filter(obj => { return obj.value === item.sexo })[0]
          }
          this.setPessoa(this.pessoaSelecionada)
        })
    },

    configuraPesquisaDeVisibilidade () {
      if (this.itemSelecionado && this.itemSelecionado.tipo_visibilidade && this.itemSelecionado.tipo_visibilidade.length > 0) {
        this.itemSelecionado.tipo_visibilidade.forEach(element => {
          this.setPesquisaDeVisibilidade(this.listaVisibilidade.find(e => e.id === element.id))
        })
      }
    },

    setPesquisaDeVisibilidade (value) {
      const index = this.tagDePesquisaDeVisibilidade.indexOf(value)
      if (index === -1) {
        this.tagDePesquisaDeVisibilidade.push(value)
        this.itemSelecionado.tipo_visibilidade = this.tagDePesquisaDeVisibilidade
        return
      }

      this.tagDePesquisaDeVisibilidade.splice(index, 1)
      this.itemSelecionado.tipo_visibilidade = this.tagDePesquisaDeVisibilidade
    },

    setPessoaSelecionada (pessoa) {
      if (pessoa) {
        this.data_nascimento_selecionado = (pessoa.data_nascimento ? dateToString(new Date(pessoa.data_nascimento)) : '')
        pessoa.estado_civil = this.listaEstadoCivil.filter(item => { return item.value === pessoa.estado_civil })[0]
        pessoa.sexo = this.listaSexo.filter(item => { return item.value === pessoa.sexo })[0]
        this.SET_PESSOA(pessoa)
        this.setPessoa(pessoa)
      } else {
        this.SET_PESSOA(null)
        this.limparPessoa()
      }
    },

    verificaMenorIdade () {
      if (this.data_nascimento_selecionado !== '') {
        if ((this.getIdade(this.data_nascimento_selecionado) < 18) && (this.emancipado === false)) {
          if (this.itemSelecionado.responsavel_financeiro_pessoa && this.itemSelecionado.pessoa) {
            if (this.itemSelecionado.responsavel_financeiro_pessoa.id === this.itemSelecionado.pessoa.id) {
              this.setResponsavelFinanceiro(null)
              this.menorSelecionado = true
            }
          }

          this.obrigatorioRespFinanceiro = true
          this.mostrarCamposResponsaveis = true
          return true
        }
      }

      this.menorSelecionado = false
      this.obrigatorioRespFinanceiro = false
      this.mostrarCamposResponsaveis = false

      return false
    },

    setResponsavelFinanceiro (value) {
      if (value && this.itemSelecionado.id && value.id === this.itemSelecionado.pessoa.id && (this.getIdade(this.data_nascimento_selecionado) < 18) && (this.emancipado === false)) {
        setTimeout(() => {
          this.setResponsavelFinanceiro(null)
          this.menorSelecionado = true
        }, 100)
      }

      this.menorSelecionado = false
      this.$store.commit('aluno/SET_RESPONSAVEL_FINANCEIRO', value)
      // this.itemSelecionado.responsavel_financeiro_pessoa = value
    },

    setResponsavelDidatico (value) {
      this.$store.commit('aluno/SET_RESPONSAVEL_DIDATICO', value)
      // this.itemSelecionado.responsavel_didatico_pessoa = value
    },

    getIdade (nascimento) {
      const dataInformado = new Date(stringToISODate(nascimento, true))
      const diferencaMs = Date.now() - dataInformado.getTime()
      const idadeDateTime = new Date(diferencaMs)
      const idadeTotal = Math.abs(idadeDateTime.getUTCFullYear() - 1970)

      return idadeTotal
    },

    mostraResponsaveis () {
      this.verificaMenorIdade()
      if (((this.responsavelFinanceiro === null) || (this.responsavelFinanceiro === '')) && (this.obrigatorioRespFinanceiro === false)) {
        this.mostrarCamposResponsaveis = false
      } else {
        this.mostrarCamposResponsaveis = true
      }
    },

    setDataNascimento (value) {
      this.data_nascimento_selecionado = value
      this.$store.commit('pessoas/setDataNascimento', value ? stringToISODate(value, true) : '')
      this.verificaMenorIdade()
    },

    setCepData (value) {
      this.cep_data = value
    },

    atualizaDadosCepPessoa () {
      this.$store.commit('pessoas/SET_BAIRRO_ENDERECO', this.cep_data.bairro_endereco)
      this.$store.commit('pessoas/SET_COMPLEMENTO_ENDERECO', this.cep_data.complemento_endereco)
      this.$store.commit('pessoas/SET_CEP_ENDERECO', this.cep_data.cep_endereco)
      this.$store.commit('pessoas/setNumeroEndereco', this.cep_data.numero_endereco)
      this.$store.commit('pessoas/setEndereco', this.cep_data.endereco)
      if (this.cep_data.estado !== undefined) {
        this.$store.commit('pessoas/SET_ESTADO', this.cep_data.estado.id)
      }
      if (this.cep_data.cidade !== undefined) {
        this.$store.commit('pessoas/SET_CIDADE', this.cep_data.cidade.id)
      }
    },

    verficaCnpjCpfResponsavelFinanceiro () {
      let respFinanceiroCnpjCpf = null;
      let pessoaCnpjCpf = null;
      
      
      if (this.pessoaSelecionada.franqueada_id == 8) {
        return true
      }
      
      if(this.itemSelecionado != null){
        if(this.itemSelecionado.responsavel_financeiro_pessoa != null){
          respFinanceiroCnpjCpf = this.itemSelecionado.responsavel_financeiro_pessoa.cnpj_cpf
        }
      }

      if(this.pessoaSelecionada != null){
          pessoaCnpjCpf = this.pessoaSelecionada.cnpj_cpf;
      }
     
      if (respFinanceiroCnpjCpf !== null  || pessoaCnpjCpf !== null) {
        return true
      }
      return false
    },

    salvarPessoa (callback) {
      this.atualizaDadosCepPessoa()        
        if (this.verficaCnpjCpfResponsavelFinanceiro()) {
            if (this.pessoaSelecionada.id) {
              this.atualizarPessoa(true)
                .then(callback)
                .catch((response) => {
                  EventBus.$emit('criarAlerta', {
                    tipo: 'A',
                    mensagem: response.body.mensagem || 'Houve um erro no servidor'
                  })
                  this.finalizouRequisicao()
                })
            } else {
              this.criarPessoa(true)
                .then(callback)
                .catch((response) => {
                  EventBus.$emit('criarAlerta', {
                    tipo: 'A',
                    mensagem: response.body.mensagem || 'Houve um erro no servidor'
                  })
                  this.finalizouRequisicao()
                })
            }

        } else {
            EventBus.$emit('criarAlerta', {
              tipo: 'A',
              mensagem: 'Obrigatório CNPJ/CPF no Aluno ou no Responsável'
            })
            this.finalizouRequisicao()
                      
        }
    },

    // CHAMA APÓS SALVAR PESSOA
    responsavelSalva (pessoaID) {
      if (!pessoaID) {
        return
      }

      this.$store.commit('pessoas/SET_PAGINA_ATUAL', 1)
      this.getListaPessoas()
        .then(() => {
          this.listaResponsaveis.find(item => {
            if (item.id === pessoaID) {
              if ((this.getIdade(item.data_nascimento) < 18) && item.tipo_pessoa === 'F') {
                return
              }

              if (this.itemSelecionado.responsavel_financeiro_pessoa === null) {
                this.setResponsavelFinanceiro(item)
                return
              }

              if (this.itemSelecionado.responsavel_didatico_pessoa === null) {
                this.setResponsavelDidatico(item)
              }
            }
          })
        })

      this.$refs.responsavel.$refs.responsavel.hide()
    },

    cancelarResponsavel () {
      this.$refs.responsavel.alternaLimpaEndereco(false)
      this.cep_data = Object.assign({}, this.$refs.responsavel.dadosEnderecoAlunoTemp)
      this.$refs.responsavel.$refs.responsavel.hide()
    },

    voltar () {
      this.finalizouRequisicao()
      this.LIMPAR_ITEM_SELECIONADO()
      this.limparPessoa()

      if (this.$route.query.interessado) {
        this.$router.push(`/cadastros/interessados/atualizar/${this.$route.query.interessado}`)
      } else if (this.$router.length > 1) {
        this.$router.go(-1)
      } else {
        this.$router.push(this.$route.matched[0].path)
      }
    },

    finalizouRequisicao (value) {
      this.isSubmiting = false
      this.isSubmitingMatricularJunto = false
    },

    previewImage (event) {
      const input = event.target
      if (input.files && input.files[0]) {
        let reader = new FileReader()
        reader.onload = (e) => {
          this.imageData = e.target.result
          this.SET_FOTO(input.files[0])
        }

        reader.readAsDataURL(input.files[0])
      }
    },

    cleanImage () {
      this.imageData = ''
      this.itemSelecionado.foto = ''
      document.getElementById('up-image').value = ''
    },

    direcionarTelaMatricula (response) {
      this.finalizouRequisicao()
      let idAluno = null
      if (this.paginaEditar) {
        idAluno = this.$route.params.id
      } else {
        idAluno = response.body.corpo.aluno
      }

      this.$router.replace(`${this.$route.matched[0].path}/atualizar/${idAluno}`)
      this.$router.push(`/cadastros/contrato/adicionar?aluno=${idAluno}`)

      this.LIMPAR_ITEM_SELECIONADO()
      this.limparPessoa()
    },

    salvarAluno (pessoaResponse) {
      let pessoaNullObj = { id: null }
      if (typeof pessoaResponse.pessoa === 'number') {
        pessoaNullObj.id = pessoaResponse.pessoa
      } else {
        pessoaNullObj = pessoaResponse.pessoa
      }
      this.SET_PESSOA(pessoaNullObj)
      this.SET_INTERESSADO_ID(this.interessadoId)
      this.itemSelecionado.tipo_visibilidade = this.tagDePesquisaDeVisibilidade
      this.tagDePesquisaDeVisibilidade = []
      const redirecionamento = this.isMatricular === true ? this.direcionarTelaMatricula : this.voltar
      const callbackErro = () => {
        this.finalizouRequisicao()
        this.reverterDadosSalvarAluno()
      }
      if (this.itemSelecionadoID) {
        this.atualizar().then(redirecionamento).catch(callbackErro)
      } else {
        this.criar().then(redirecionamento).catch(callbackErro)
      }
    },

    reverterDadosSalvarAluno () {
      this.tagDePesquisaDeVisibilidade = this.itemSelecionado.tipo_visibilidade
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

    submit () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.isMatricular === true) {
        // TODO: ao salvar e matricular verificar a possibilidade de deixar um pouco escuro os botões quando estão 'disabled'
        this.isSubmitingMatricularJunto = true
      } else {
        this.isSubmiting = true
      }
      this.isValid = true
      this.salvarPessoa(this.salvarAluno)
    }

  }
}
</script>

<style scoped>
/* Image Uploader */
.upload-area {
  width: 100%;
  height: 100%;
  min-height: 190px;
  position: relative;
  text-align: center;
  background-color: #EBECF0;
  cursor: pointer;
  color: #7d7e7f;
  margin: 0;
}
.upload-area > div {
  display: flex;
  flex-direction: column;
  transition: all .2s;
  height: 100%;
}
.image-load {
  padding: 1rem;
  height: 100%;
}
.img-container {
  overflow: hidden;
  height: 100%;
  display: flex;
  justify-content: center;
  background-color: #fff;
  position: relative;
}
.image-preview {
  position: relative;
}
.image-preview img {
  /* height: 100%;
  top: 0; */
  position: absolute;
  display: flex;
  align-self: center;
  height: auto;
  max-width: 100%;
}
.image-preview label {
  margin: 0;
}
.image-preview div:last-child {
  transition: all .2s;
  opacity: 0;
  position: absolute;
  width: 100%;
  bottom: 0;
  padding: .5rem;
}
.image-preview div:last-child label {
  background-color: #EBECF0;
  width: 100%;
}
.image-preview:hover div {
  opacity: 1;
}
.image-load > svg {
  font-size: 5rem;
  margin: auto;
}
.image-load:hover {
  background: rgba(0, 0, 0, 0.2);
  color: #EBECF0;
  border-color: #EBECF0;
}
/*---------------------end-------------------------*/

.head-content-sector .form-control {
  background-color: #fff;
}
.head-content-sector i {
  color: #7d7e7f;
}

.body-sector {
  border-width: initial;
}

div[data-tipo="S"] {
  color: #FF3860;
}

div[data-tipo="C"] {
  color: #20c997;
}

div[data-tipo="D"] {
  color: #79caf9;
}

div[data-saldo="P"]{
  color: #20c997;
}

div[data-saldo="N"]{
  color: #FF3860;
}

</style>
