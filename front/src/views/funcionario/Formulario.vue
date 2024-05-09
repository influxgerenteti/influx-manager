<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !estaValido }" class="needs-validation" novalidate @submit.prevent="submit()">
      <div v-if="paginaEditar" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="!paginaEditar" class="form-loading screen-load">
        <load-placeholder :loading="verificaCarregamento(loadCount,4)" />
      </div>

      <div class="body-sector">
        <div v-if="!paginaEditar" class="head-content-sector p-3">
          <div class="row">
            <div class="col-md-4">
              <label v-help-hint="'form-funcionario_buscar_cpf'" for="busca_cpf" class="col-form-label">
                Buscar pessoa por CPF
                <br><small>Buscaremos esse dado em nosso banco de pessoas.</small>
              </label>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <typeahead id="busca_cpf" :item-hit="setPessoaSelecionada" source-path="/api/pessoa/buscar" />
            </div>
          </div>
        </div>
        <div class="content-sector sector-roxo-c p-3">
          <div class="form-group row">
            <div class="col-md-6">
              <label for="nome_contato" class="col-form-label">Nome *</label>
              <input v-help-hint="'form-funcionario_nome_contato'" id="nome_contato" v-model="pessoaSelecionada.nome_contato" type="text" class="form-control" required maxlength="255">
              <div class="invalid-feedback">Preencha o nome!</div>
            </div>
            <div class="col-md-6">
              <label v-help-hint="'form-funcionario_apelido'" for="apelido" class="col-form-label">Apelido *</label>
              <input id="apelido" v-model="itemSelecionado.apelido" type="text" class="form-control" required maxlength="25">
              <div class="invalid-feedback">Preencha o apelido!</div>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-funcionario_cnpj_cpf_pessoa'" for="cnpj_cpf_pessoa" class="col-form-label">CPF</label>
              <input v-mask="'###.###.###-##'" id="cnpj_cpf_pessoa" v-model="cnpj_cpf_pessoa" :class="{ 'is-invalid' : cpfInvalido }" type="text" class="form-control" @blur="cpfInvalido = $v.cnpj_cpf_pessoa.$invalid">
              <div v-if="cpfInvalido" class="input-invalid">CPF informado é inválido!</div>
            </div>

            <div class="col-md-3">
              <label v-help-hint="'form-funcionario_identidade'" for="numero_identidade" class="col-form-label">Identidade (RG)</label>
              <input id="numero_identidade" v-model="pessoaSelecionada.numero_identidade" type="text" class="form-control" maxlength="15">
            </div>
            <div class="col-md-3">
              <label v-help-hint="'form-funcionario_orgao_emissor'" for="orgao_emissor" class="col-form-label">Órgão Emissor</label>
              <input id="orgao_emissor" v-model="pessoaSelecionada.orgao_emissor" type="text" class="form-control" placeholder="ex.: SSP" maxlength="10">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label v-help-hint="'form-funcionario_data_nascimento'" for="data_nascimento" class="col-form-label">Data de nascimento *</label>
              <g-datepicker :value="pessoaSelecionada.data_nascimento" :class="{'valid-input' : !estaValido}" :selected="setDataNascimento" element-id="data_nascimento" maxlength="10" required/>
              <div v-if="!estaValido && $v.pessoaSelecionada.data_nascimento.$invalid" class="multiselect-invalid">
                Informe a data de nascimento!
              </div>
            </div>

            <div class="col-md-3">
              <label v-help-hint="'form-funcionario_sexo'" for="sexo" class="col-form-label">Sexo</label>
              <g-select id="sexo"
                        :select="setSexo"
                        :value="pessoaSelecionada.sexo"
                        :options="listaSexo"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="value"
              />
            </div>
            <div class="col-md-3">
              <label v-help-hint="'form-funcionario_estado_civil'" for="estado_civil" class="col-form-label">Estado Civil</label>
              <g-select id="estado_civil"
                        :select="setEstadoCivil"
                        :value="pessoaSelecionada.estado_civil"
                        :options="listaEstadoCivil"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="value"
              />
            </div>
          </div>
        </div>

        <div class="content-sector sector-azul p-3">
          <h5 class="title-module mb-2">Contatos</h5>
          <div class="form-group row">
            <div class="col-md-4">
              <label v-help-hint="'form-funcionario_telefone_preferencial'" for="telefone_preferencial" class="col-form-label">Telefone Celular</label>
              <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_preferencial" v-model="pessoaSelecionada.telefone_preferencial" type="text" class="form-control" >
              <div v-if="!estaValido && $v.pessoaSelecionada.telefone_preferencial.$invalid" class="input-invalid">Preencha corretamente!</div>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'form-funcionario_telefone_contato'" for="telefone_contato" class="col-form-label">Telefone Fixo</label>
              <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_contato" v-model="pessoaSelecionada.telefone_contato" type="text" class="form-control">
              <div v-if="!estaValido && $v.pessoaSelecionada.telefone_contato.$invalid" class="input-invalid">Preencha corretamente!</div>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'form-funcionario_email_preferencial'" for="email_preferencial" class="col-form-label">E-mail</label>
              <input id="email_preferencial" v-model="pessoaSelecionada.email_preferencial" type="text" class="form-control" maxlength="50">
              <div class="invalid-feedback">Preencha corretamente o e-mail!</div>
            </div>
          </div>
        </div>

        <g-form-endereco :cep-data="cep_data" :callback-cep-data="setCepData" />

        <div class="content-sector sector-roxo-c p-3">
          <h5 class="title-module mb-2">Informações Bancárias</h5>

          <div class="form-group row">
            <div class="col-md-3">
              <label v-help-hint="'form-funcionario_banco'" for="banco" class="col-form-label">Banco *</label>
              <g-select id="banco"
                        :class="!estaValido && !itemSelecionado.banco ? 'invalid-input' : 'valid-input'"
                        :select="setBanco"
                        :value="itemSelecionado.banco"
                        :options="listaBancos"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
              <div v-if="!estaValido && !itemSelecionado.banco" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_agencia'" for="agencia" class="col-form-label">Agência</label>
              <input id="agencia" v-model="itemSelecionado.agencia" type="text" class="form-control" maxlength="10">
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_digito_agencia'" for="digito_agencia" class="col-form-label">Dígito</label>
              <input id="digito_agencia" v-model="itemSelecionado.digito_agencia" type="text" class="form-control" maxlength="3">
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_conta_corrente'" for="conta_corrente" class="col-form-label">Conta</label>
              <input id="conta_corrente" v-model="itemSelecionado.conta_corrente" type="text" class="form-control" maxlength="15">
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_digito_conta_corrente'" for="digito_conta_corrente" class="col-form-label">Dígito</label>
              <input id="digito_conta_corrente" v-model="itemSelecionado.digito_conta_corrente" type="text" class="form-control" maxlength="3">
            </div>
          </div>
        </div>

        <div class="content-sector sector-azul p-3">

          <h5 class="title-module mb-2">Profissional</h5>

          <div class="form-group row">
            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_data_admissao'" for="data_admissao" class="col-form-label">Data de admissão</label>
              <g-datepicker :value="itemSelecionado.data_admissao" :selected="setDataAdmissao" element-id="data_admissao" maxlength="10" />
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_data_demissao'" for="data_demissao" class="col-form-label">Data de demissão</label>
              <g-datepicker :value="itemSelecionado.data_demissao" :selected="setDataDemissao" element-id="data_demissao" maxlength="10" />
              <div v-if="itemSelecionado.data_demissao && (dateToCompare(itemSelecionado.data_admissao) > dateToCompare(itemSelecionado.data_demissao))" class="multiselect-invalid">
                Data de demissão deve ser posterior a data de admissão!
              </div>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-funcionario_cargo'" for="cargo" class="col-form-label">Cargo *</label>
              <g-select id="cargo"
                        :class="!estaValido && !itemSelecionado.cargo ? 'invalid-input' : 'valid-input'"
                        :select="setCargo"
                        :value="itemSelecionado.cargo"
                        :options="listaCargos"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
              <div v-if="!estaValido && !itemSelecionado.cargo" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </div>

            <div class="col-md-6">
              <label v-help-hint="'form-funcionario_tipo_pagamento'" for="tipo_pagamento" class="col-form-label">Tipo de pagamento</label>
              <b-form-radio-group v-model="itemSelecionado.tipo_pagamento"
                                  :options="[{text: 'Autônomo', value: 'H'}, {text: 'Registrado', value: 'M'}]"
                                  name="tipo_pagamento" />
            </div>
          </div>

          <div class="form-group row mb-3">

            <div class="col-md-4">
              <label v-help-hint="'form-funcionario_gestor_comercial_funcionario'" for="gestor_comercial_funcionario" class="col-form-label">Gestor Comercial do Funcionário</label>
              <g-select id="gestor_comercial_funcionario"
                        :disabled="itemSelecionado.gestor_comercial"
                        :select="setGestorComercialFuncionario"
                        :value="itemSelecionado.gestor_comercial_funcionario"
                        :options="listaDeGestorComercial"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_gestor_comercial'" for="gestor_comercial" class="col-form-label">Gestor Comercial</label>
              <div class="custom-checkbox">
                <b-form-checkbox id="gestor_comercial" v-model="itemSelecionado.gestor_comercial" @input="setFlagGestor">Sim</b-form-checkbox>
              </div>
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_coordenador_pedagogico'" for="coordenador_pedagogico" class="col-form-label">Coordenador Pedagógico</label>
              <div class="custom-checkbox">
                <b-form-checkbox id="coordenador_pedagogico" v-model="itemSelecionado.coordenador_pedagogico">Sim</b-form-checkbox>
              </div>
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_consultor'" for="consultor" class="col-form-label">Consultor</label>
              <div class="custom-checkbox">
                <b-form-checkbox id="consultor" v-model="itemSelecionado.consultor">Sim</b-form-checkbox>
              </div>
            </div>

            <div class="col-md-2">
              <label v-help-hint="'form-funcionario_atendente'" for="atendente" class="col-form-label">Atendente</label>
              <div class="custom-checkbox">
                <b-form-checkbox id="atendente" v-model="itemSelecionado.atendente">Sim</b-form-checkbox>
              </div>
            </div>

          </div>

          <div class="content-sector-extra p-3 position-relative">
            <div v-if="itemSelecionado.nivel_instrutor !== null" class="form-loading">
              <load-placeholder :loading="loadState && itemSelecionado.nivel_instrutor !== null" />
            </div>

            <b-alert :show="(selected !== selectedId) && initState && notEmpty" variant="warning">
              Ao desabilitar o instrutor ou selecionar um nível de instrutor diferente, os valores diferenciados de "{{ nivel_instrutor_info }}" serão perdidos.
            </b-alert>

            <h5 v-help-hint="'form-funcionario_instrutor'" class="title-module mb-2">Instrutor</h5>

            <div class="custom-control custom-checkbox">
              <input v-b-toggle.instrutor-toggle id="instrutor" v-model="itemSelecionado.instrutor" type="checkbox" class="custom-control-input">
              <label class="custom-control-label" for="instrutor">Sim</label>
            </div>

            <b-collapse id="instrutor-toggle" :visible="itemSelecionado.instrutor === true" class=" position-relative">
              <div class="form-group row mb-0">

                <div class="col-md-2">
                  <label v-help-hint="'form-funcionario_instrutor_personal'" for="instrutor_personal" class="col-form-label">Instrutor Personal</label>
                  <div class="custom-checkbox">
                    <b-form-checkbox id="instrutor_personal" v-model="itemSelecionado.instrutor_personal">Sim</b-form-checkbox>
                  </div>
                </div>

                <div v-if="!!itemSelecionado.instrutor" class="col-md-2 mb-3">

                  <label v-help-hint="'form-funcionario_nivel_instrutor'" for="nivel_instrutor" class="col-form-label">Nível de instrutor</label>
                  <g-select id="nivel_instrutor_funcionario"
                            :select="setNivelInstrutor"
                            :value="itemSelecionado.nivel_instrutor"
                            :options="listaNiveisInstrutor"
                            class="multiselect-truncate"
                            label="descricao"
                            track-by="id"
                  />
                </div>
              </div>

              <b-alert :show="itemSelecionado.nivel_instrutor !== null && 0 >= Object.keys(listaFuncionarioValorHora).length" variant="warning" class="mb-0">
                Este nível de instrutor não possui valores por hora definidos.
              </b-alert>

              <b-row v-show="Object.keys(listaFuncionarioValorHora).length" class="header-card-list">
                <b-col md="3"><label class="col-form-label">Tipo de valor por hora</label></b-col>
                <b-col md="4"><label class="col-form-label">Valor Padrão/Extra</label></b-col>
                <b-col md="2"><label class="col-form-label">Valor Diferenciado</label></b-col>
                <b-col md="2"><label class="col-form-label">Valor Extra Diferenciado</label></b-col>
              </b-row>

              <b-row v-for="(item, index) in listaFuncionarioValorHora" :key="index" class="body-card-list">
                <b-col md="3" data-header="Tipo de valor por hora">{{ item.descricao_valor_hora }}</b-col>
                <b-col md="4" data-header="Valor Padrão/Extra/Bônus">{{ item.valor_padrao | formatarMoeda }} / {{ item.valor_extra_padrao | formatarMoeda }}</b-col>
                <b-col md="2" data-header="Valor Diferenciado">
                  <input v-money="moeda" v-wipe="{func: setValorDiferenciado, propName: 'valor_diferenciado', index}" :id="`valor-${item.id}`" v-model="item.valor_diferenciado" type="text" class="form-control" maxlength="9" @keydown.enter.prevent="">
                </b-col>

                <b-col md="2" data-header="Valor Extra Diferenciado">
                  <input v-money="moeda" v-wipe="{func: setValorDiferenciado, propName: 'valor_extra_diferenciado', index}" :id="`valor-extra-${item.id}`" v-model="item.valor_extra_diferenciado" type="text" class="form-control" maxlength="9" @keydown.enter.prevent="">
                </b-col>

              </b-row>
            </b-collapse>
          </div>

          <div class="content-sector-extra p-3 position-relative mt-3">
            <h5 class="title-module mb-2">Disponibilidade</h5>

            <div class="row data-scroll">
              <perfect-scrollbar class="scroller col-12">
                <b-row v-for="(item, index) in itemSelecionado.disponibilidades" :key="index" class="body-card-list mx-2 mb-2">

                  <b-col md="2" data-header="Data">
                    <label v-help-hint="'form-funcionario_dia_disponibilidades'" for="dia_disponibilidades" class="col-form-label">Dia</label>
                    <select v-model="itemSelecionado.disponibilidades[index].dia_semana" class="custom-select form-control">
                      <option :value="null">Selecione</option>
                      <option value="DOM">Domingo</option>
                      <option value="SEG">Segunda</option>
                      <option value="TER">Terça</option>
                      <option value="QUA">Quarta</option>
                      <option value="QUI">Quinta</option>
                      <option value="SEX">Sexta</option>
                      <option value="SAB">Sábado</option>
                    </select>
                  </b-col>

                  <b-col md="2" data-header="Início">
                    <label v-help-hint="'form-funcionario_dia_disponibilidades'" for="dia_disponibilidades" class="col-form-label">Início {{ itemSelecionado.disponibilidades[index].dia_semana ? "*": '' }}</label>
                    <input v-mask="'##:##'" id="form-funcionario_horario_inicio" v-model="itemSelecionado.disponibilidades[index].hora_inicial" :required="itemSelecionado.disponibilidades[index].dia_semana" :class="!$v.itemSelecionado.disponibilidades.$each[index].hora_inicial.validarHorario ? 'is-invalid' : null" type="text" class="form-control" maxlength="5" placeholder="hh:mm">
                    <div class="invalid-feedback">
                      <template v-if="itemSelecionado.disponibilidades[index].msgHorarioInvalido">
                        {{ itemSelecionado.disponibilidades[index].msgHorarioInvalido }}
                      </template>
                    </div>
                  </b-col>

                  <b-col md="2" data-header="Término">
                    <label v-help-hint="'form-funcionario_dia_disponibilidades'" for="dia_disponibilidades" class="col-form-label">Término {{ itemSelecionado.disponibilidades[index].dia_semana ? "*": '' }}</label>
                    <input v-mask="'##:##'" id="form-funcionario_horario_final" v-model="itemSelecionado.disponibilidades[index].hora_final" :required="itemSelecionado.disponibilidades[index].dia_semana" :class="!$v.itemSelecionado.disponibilidades.$each[index].hora_final.validarHorario ? 'is-invalid' : null" type="text" class="form-control" maxlength="5" placeholder="hh:mm">
                    <div class="invalid-feedback">
                      <template v-if="itemSelecionado.disponibilidades[index].msgHorarioInvalido">
                        {{ itemSelecionado.disponibilidades[index].msgHorarioInvalido }}
                      </template>
                    </div>
                  </b-col>

                  <b-col md="4">
                    <b-btn v-if="itemSelecionado.disponibilidades.length > 1" variant="light" class="btn-40 mt-4" @click.prevent="excluir(index)">
                      <font-awesome-icon icon="minus" />
                    </b-btn>

                    <b-btn variant="azul" class="btn-40 mt-4" @click.prevent="listaAdd()">
                      <font-awesome-icon icon="plus" />
                    </b-btn>
                  </b-col>

                </b-row>
              </perfect-scrollbar>
            </div>
          </div>
        </div>

        <div class="content-sector sector-roxo-c p-3">

          <h5 class="title-module mb-2">Outros</h5>
          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-funcionario_usuario'" for="usuario" class="col-form-label">Usuário do sistema</label>
              <g-select id="usuario"
                        :select="setUsuario"
                        :value="itemSelecionado.usuario"
                        :options="listaUsuariosSelect"
                        class="multiselect-truncate"
                        label="nome"
                        track-by="id"
              />
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-12">
              <label v-help-hint="'form-funcionario_observacao'" for="observacao" class="col-form-label">Observações</label>
              <textarea id="observacao" v-model="pessoaSelecionada.observacao" class="form-control full-textarea" rows="6" maxLength="5000"></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - (pessoaSelecionada.observacao || '').length }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-12">
          <b-btn :disabled="isSubmiting" type="submit" class="btn btn-verde" variant="primary">
            {{ isSubmiting ? 'Salvando...' : 'Salvar' }}
          </b-btn>
          <b-btn variant="link" @click="voltar()">Voltar</b-btn>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapMutations, mapActions, mapState} from 'vuex'
import {required, minLength, requiredIf, helpers} from 'vuelidate/lib/validators'
import {currencyToNumber} from '../../utils/number'
import Typeahead from '../../components/Typeahead.vue'
import {stringToISODate, dateToString, dateToCompare, converteHorarioParaBanco} from '../../utils/date'
import {isCpfValido} from '../../utils/format'
import {validateHour} from '../../utils/validators'

const tipoDocumento = (value) => !helpers.req(value) || isCpfValido(value)

const validarHorario = (value, vm) => {
  vm.msgHorarioInvalido = 'Informe horário'

  if (!validateHour(value)) {
    vm.msgHorarioInvalido = 'Horário inválido'

    return false
  }

  if (vm.hora_final !== '' && vm.hora_inicial !== '' && vm.hora_inicial > vm.hora_final) {
    vm.msgHorarioInvalido = 'Horário final inválido'

    return false
  }

  return true
}

export default {
  components: {
    Typeahead
  },

  props: {
    isModal: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      loadCount: 0,
      nivel_instrutor_info: '',
      cnpj_cpf_pessoa: '',
      estaValido: true,
      notEmpty: true,
      paginaEditar: false,
      isSubmiting: false,
      cpfInvalido: false,
      instrutor_bloco: false,
      sameValue: false,
      initState: false,
      loadState: false,
      selected: null,
      selectedId: null,
      listaValorHora: [],
      listaEstadoCivil: [
        {value: null, descricao: 'Selecione'},
        {value: 'N', descricao: 'Não Informar'},
        {value: 'S', descricao: 'Solteiro(a)'},
        {value: 'C', descricao: 'Casado(a)'},
        {value: 'D', descricao: 'Divorciado'}
      ],
      listaSexo: [
        {value: null, descricao: 'Selecione'},
        {value: 'N', descricao: 'Não Informar'},
        {value: 'M', descricao: 'Masculino'},
        {value: 'F', descricao: 'Feminino'},
        {value: 'O', descricao: 'Outro'}
      ],
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: true
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
      selectedValues: {},
      listaFuncionarioValorHora: {},

      msgHorarioInvalido: ''
    }
  },

  validations: {
    pessoaSelecionada: {
      telefone_preferencial: {minLength: minLength(14)},
      telefone_contato: {minLength: minLength(14)},
      telefone_profissional: {minLength: minLength(14)},
      data_nascimento: {required}
    },
    itemSelecionado: {
      cargo: {required},
      banco: {required},
      tipo_pagamento: {required},
      apelido: {required},
      // nivel_instrutor: {
      //   required: requiredIf(function () {
      //     return this.itemSelecionado.instrutor
      //   })
      // },
      disponibilidades: {
        $each: {
          hora_inicial: {required: requiredIf(function ($each) {
            return $each.dia_semana
          }),
          validarHorario},
          hora_final: {required: requiredIf(function ($each) {
            return $each.dia_semana
          }),
          validarHorario}
        }
      }
    },
    cnpj_cpf_pessoa: {tipoDocumento}
  },

  computed: {
    ...mapState('funcionario', ['lista', 'itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('pessoas', { pessoaSelecionada: 'objPessoa' }),
    ...mapState('cargo', { listaCargos: 'lista' }),
    ...mapState('nivelInstrutor', { listaNiveis: 'lista' }),
    ...mapState('usuarios', ['listaUsuarios']),
    ...mapState('banco', {listaBancos: 'lista'}),
    ...mapState('valorHora', {listaValores: 'lista'}),

    listaFuncionarios: {
      get () {
        return [{apelido: 'Nenhum', id: ''}].concat(this.lista.filter(func =>
          this.itemSelecionadoID ? this.itemSelecionadoID !== func.id : true
        ))
      }
    },

    listaDeGestorComercial: {
      get () {
        return [{apelido: 'Nenhum', id: ''}].concat(this.lista.filter((func) => (func.gestor_comercial === true)))
      }
    },

    listaNiveisInstrutor: {
      get () {
        return this.listaNiveis
        // return [{descricao: 'Nenhum', id: ''}].concat(this.listaNiveis)
      }
    },

    listaUsuariosSelect: {
      get () {
        return [{nome: 'Selecione', id: null}].concat(this.listaUsuarios)
      }
    }
  },

  watch: {
    pessoaSelecionada (value) {
      this.cep_data = {
        cep_endereco: value.cep_endereco,
        endereco: value.endereco,
        numero_endereco: value.numero_endereco,
        complemento_endereco: value.complemento_endereco,
        bairro_endereco: value.bairro_endereco,
        estado: value.estado,
        cidade: value.cidade
      }
    },

    cnpj_cpf_pessoa (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
      this.pessoaSelecionada.cnpj_cpf = value
    }
  },

  created () {
    this.LIMPAR_ITEM_SELECIONADO()
    this.limparPessoa()

    if (!this.$store.state.funcionario.estaCarregando) {
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('funcionario/listar')
    }

    this.$store.commit('cargo/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('cargo/listar').then(this.countCarregamento)

    this.$store.commit('nivelInstrutor/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('nivelInstrutor/listar').then(this.countCarregamento)

    this.$store.commit('usuarios/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('usuarios/getListaUsuarios').then(this.countCarregamento)

    this.$store.commit('banco/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('banco/listar').then(this.countCarregamento)

    const id = this.$route.params.id
    if (id && !this.isModal) {
      this.paginaEditar = true
      this.SET_ITEM_SELECIONADO_ID(Number(id))
      this.buscar()
        .then(pessoa => {
          if (this.itemSelecionado.disponibilidades.length === 0) {
            this.itemSelecionado.disponibilidades.push({id: null, dia_semana: null, hora_inicial: '', hora_final: ''})
          }

          if (pessoa.sexo !== undefined) {
            pessoa.sexo = this.listaSexo.filter(obj => { return obj.value === pessoa.sexo })[0]
          }
          if (pessoa.estado_civil !== undefined) {
            pessoa.estado_civil = this.listaEstadoCivil.filter(item => { return item.value === pessoa.estado_civil })[0]
          }

          this.setPessoaSelecionada(pessoa)
          this.initState = this.itemSelecionado.instrutor
          if (this.itemSelecionado.nivel_instrutor !== null) {
            this.selected = this.itemSelecionado.nivel_instrutor.id
            this.selectedId = this.selected
            this.nivel_instrutor_info = this.itemSelecionado.nivel_instrutor.descricao
            this.setNivelInstrutor(this.itemSelecionado.nivel_instrutor)
          }
        })
    }
  },

  methods: {
    ...mapMutations('funcionario', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_PESSOA']),
    ...mapMutations('pessoas', ['setPessoa', 'limparPessoa']),
    ...mapMutations('valorHora', ['SET_FILTROS_NIVEL_INSTRUTOR']),
    ...mapActions('funcionario', ['buscar', 'criar', 'atualizar']),
    ...mapActions('pessoas', ['criarPessoa', 'atualizarPessoa']),
    ...mapActions('valorHora', {listarValorHora: 'listar'}),

    dateToCompare: dateToCompare,

    setSexo (value) {
      this.pessoaSelecionada.sexo = value
    },

    setEstadoCivil (value) {
      this.pessoaSelecionada.estado_civil = value
    },

    setCepData (value) {
      this.cep_data = value
    },

    desabilitaNivelInstrutor () {
      if (!this.itemSelecionado.instrutor) {
        this.setNivelInstrutor(null)
        this.itemSelecionado.nivel_instrutor = null
        this.itemSelecionado.instrutor_personal = false
        return true
      }
      return false
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

    setPessoaSelecionada (pessoa) {
      if (pessoa) {
        this.setPessoa(pessoa)
        this.pessoaSelecionada.data_nascimento = pessoa.data_nascimento ? dateToString(new Date(pessoa.data_nascimento)) : ''
        this.cnpj_cpf_pessoa = pessoa.cnpj_cpf
        this.cep_endereco_pessoa = pessoa.cep_endereco
      }
    },

    setCargo (value) {
      this.itemSelecionado.cargo = value
    },

    setBanco (value) {
      this.itemSelecionado.banco = value
    },

    setGestorComercialFuncionario (value) {
      this.itemSelecionado.gestor_comercial_funcionario = value
      if (this.itemSelecionado.gestor_comercial_funcionario.id !== '') {
        this.itemSelecionado.gestor_comercial = false
      }
    },

    setFlagGestor () {
      if (this.itemSelecionado.gestor_comercial) {
        this.itemSelecionado.gestor_comercial_funcionario = null
      }
    },

    setNivelInstrutor (value) {
      if (this.itemSelecionado.nivel_instrutor !== value && Object.keys(this.listaFuncionarioValorHora).length) {
        this.listaFuncionarioValorHora = {}
      }

      this.itemSelecionado.nivel_instrutor = value
      if (value != null) {
        this.selectedId = value.id
      }

      if (value) {
        this.loadState = Object.keys(this.listaFuncionarioValorHora).length <= 0
        this.$store.commit('valorHora/SET_PAGINA_ATUAL', 1)
        this.$store.commit('valorHora/SET_FILTROS', { nivel_instrutor: value.id, tipo_pagamento: this.itemSelecionado.tipo_pagamento })

        this.listarValorHora()
          .then(() => {
            const obj = {}
            this.listaValores.map(item => {
              if (obj[item.id] === undefined) {
                obj[item.id] = {}
              }

              obj[item.id].valor_hora = item.id
              obj[item.id].descricao_valor_hora = item.valor_hora_linhas.descricao
              obj[item.id].valor_padrao = item.valor
              obj[item.id].valor_extra_padrao = item.valor_extra
              obj[item.id].valor_bonus_padrao = item.valor_bonus

              if (obj[item.id].valor_diferenciado === undefined) {
                obj[item.id].valor_diferenciado = 0
              }

              if (obj[item.id].valor_extra_diferenciado === undefined) {
                obj[item.id].valor_extra_diferenciado = 0
              }

              if (obj[item.id].valor_bonus_diferenciado === undefined) {
                obj[item.id].valor_bonus_diferenciado = 0
              }
            })

            if (this.itemSelecionado.funcionarioValorHoras !== undefined) {
              this.itemSelecionado.funcionarioValorHoras.map(item => {
                if (this.itemSelecionado.nivel_instrutor && item.valor_hora.nivel_instrutor.id === this.itemSelecionado.nivel_instrutor.id) {
                  if (obj[item.valor_hora.id] === undefined) {
                    obj[item.valor_hora.id] = {}
                  }

                  obj[item.valor_hora.id].id = item.id
                  obj[item.valor_hora.id].valor_diferenciado = item.valor
                  obj[item.valor_hora.id].valor_extra_diferenciado = item.valor_extra
                  obj[item.valor_hora.id].valor_bonus_diferenciado = item.valor_bonus
                }
              })
            }

            this.listaFuncionarioValorHora = Object.assign({}, obj)
            this.loadState = Object.keys(this.listaFuncionarioValorHora).length <= 0

            if (!Object.keys(this.listaFuncionarioValorHora).length) {
              this.loadState = false
              if (this.selected === this.selectedId) {
                this.notEmpty = false
              }
            }
          })
      }
    },

    setUsuario (value) {
      this.itemSelecionado.usuario = value
    },

    setDataAdmissao (value) {
      this.itemSelecionado.data_admissao = value
    },

    setDataDemissao (value) {
      this.itemSelecionado.data_demissao = value
    },

    setDataNascimento (value) {
      this.pessoaSelecionada.data_nascimento = value
    },

    salvarPessoa (callback) {
      this.atualizaDadosCepPessoa()
      if (this.pessoaSelecionada.id) {
        this.atualizarPessoa(true).then(callback).catch(this.finalizouRequisicao)
      } else {
        this.criarPessoa(true).then(callback).catch(this.finalizouRequisicao)
      }
    },

    voltar (idFuncionario) {
      this.finalizouRequisicao()
      this.LIMPAR_ITEM_SELECIONADO()
      this.limparPessoa()

      if (this.isModal) {
        this.$emit('resolve', idFuncionario)
      } else {
        this.$router.push('/cadastros/funcionario')
      }
    },

    finalizouRequisicao (param) {
      this.isSubmiting = false
    },

    salvarFuncionario (pessoaResponse) {
      this.SET_PESSOA(pessoaResponse.pessoa)

      this.itemSelecionado.funcionarioValorHoras = null
      const val = Object.values(this.listaFuncionarioValorHora)
      this.itemSelecionado.funcionario_valor_horas = val.map(item => {
        const valor = currencyToNumber(item.valor_diferenciado)
        const valorExtra = currencyToNumber(item.valor_extra_diferenciado)
        const valorBonus = currencyToNumber(item.valor_bonus_diferenciado)
        if (valor !== 0 || valorExtra !== 0 || valorBonus !== 0) {
          return {
            valor_hora: item.valor_hora,
            valor: valor,
            valor_extra: valorExtra,
            valor_bonus: valorBonus,
            id: item.id || null
          }
        } else {
          return null
        }
      }).filter(item => item !== null)

      if (this.itemSelecionado.disponibilidades.length > 0) {
        this.itemSelecionado.disponibilidades = this.itemSelecionado.disponibilidades.map((item) => {
          if (item.hora_inicial.length > 0) {
            item.hora_inicial = converteHorarioParaBanco(item.hora_inicial)
          }
          if (item.hora_final.length > 0) {
            item.hora_final = converteHorarioParaBanco(item.hora_final)
          }
          return item
        })
      }

      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(this.finalizouRequisicao)
      } else {
        this.criar().then(this.voltar).catch(this.finalizouRequisicao)
      }
    },

    setValorDiferenciado ({index, propName}) {
      this.listaFuncionarioValorHora[index][propName] = 0
    },

    submit () {
      if (this.$v.$invalid) {
        this.estaValido = false
        return
      }

      this.isSubmiting = true
      this.estaValido = true
      this.pessoaSelecionada.data_nascimento = this.pessoaSelecionada.data_nascimento ? stringToISODate(this.pessoaSelecionada.data_nascimento, true) : null
      this.salvarPessoa(this.salvarFuncionario)
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

    listaAdd () {
      this.itemSelecionado.disponibilidades.push({id: null, dia_semana: null, hora_inicial: '', hora_final: ''})
      this.focar()
    },

    excluir (index) {
      this.itemSelecionado.disponibilidades.splice(index, 1)
      this.focar()
    },

    focar () {
      setTimeout(() => {
        document.getElementsByTagName('select')[this.itemSelecionado.disponibilidades.length - 1].focus()
      }, 100)
    }

  }
}
</script>
<style scoped>
.full-textarea {
  height: auto;
}
</style>
