<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="!isEdit" class="form-loading screen-load">
        <load-placeholder :loading="carregandoTipoMovimento" />
      </div>

      <div class="body-sector">
        <div class="p-3">
          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-franqueada_nome'" for="nome" class="col-form-label">Nome *</label>
              <input id="nome" v-model="nome" type="text" class="form-control" required maxlength="80">
              <div class="invalid-feedback">Preencha o nome!</div>
            </div>

            <div class="col-md-6">
              <label v-help-hint="'form-franqueada_cnpj'" for="cnpj" class="col-form-label">CNPJ *</label>
              <input v-mask="'##.###.###/####-##'" id="cnpj" v-model="cnpj" :class="{ 'is-invalid' : !isValid && $v.cnpj.$invalid }" type="text" class="form-control" required>
              <div v-if="!isValid && $v.cnpj.$invalid" class="input-invalid">Preencha corretamente o CNPJ!</div>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-franqueada_razao_social'" for="razao_social" class="col-form-label">Razão social</label>
              <input id="razao_social" v-model="razao_social" type="text" class="form-control" maxlength="120">
            </div>

            <div class="col-md-6">
              <label v-help-hint="'form-franqueada_inscricao_estadual'" for="inscricao_estadual" class="col-form-label">Inscrição estadual</label>
              <input id="inscricao_estadual" v-model="inscricao_estadual" type="text" class="form-control" maxlength="10">
            </div>
          </div>
        </div>

        <g-form-endereco :cep-data="cep_data" :callback-cep-data="callbackCepData" />

        <div class="p-3">
          <h5 class="title-module mb-2">Contatos</h5>
          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-franqueada_email_direcao'" for="email_direcao" class="col-form-label">E-mail direção *</label>
              <input id="email_direcao" v-model="email_direcao" type="email" class="form-control" maxlength="50" required>
              <div class="invalid-feedback">Preencha corretamente o e-mail!</div>
            </div>

            <div class="col-md-6">
              <label v-help-hint="'form-franqueada_email_comercial'" for="email_comercial" class="col-form-label">E-mail escola *</label>
              <input id="email_comercial" v-model="email_comercial" type="email" class="form-control" maxlength="50" required>
              <div class="invalid-feedback">Preencha corretamente o e-mail!</div>
            </div>

          </div>
          <div class="form-group row">
            <!-- Cliente solicitou suprimir campos da tela, remover em outro memento -->
            <!-- <div class="col-md-4">
              <label v-help-hint="'form-franqueada_email'" for="email" class="col-form-label">E-mail Franqueada</label>
              <input id="email" v-model="email" type="email" class="form-control" maxlength="50">
              <div class="invalid-feedback">Preencha corretamente o e-mail!</div>
            </div> -->

            <div class="col-md-6">
              <label v-help-hint="'form-franqueada_telefone'" for="telefone" class="col-form-label">Telefone</label>
              <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone" v-model="telefone" type="text" class="form-control">
            </div>

            <!-- Cliente solicitou suprimir campos da tela, remover em outro memento -->
            <!-- <div class="col-md-4">
              <label v-help-hint="'form-franqueada_telefone_secundario'" for="telefone_secundario" class="col-form-label">Telefone Secundário</label>
              <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_secundario" v-model="telefone_secundario" type="text" class="form-control">
            </div> -->
          </div>
        </div>

        <div class="content-sector sector-secondary p-3"> 
          <h5 class="title-module mb-2">Movimentações</h5> 
          <div class="form-group row "> 
        <!--     <div class="col-md-4"> -->
        <!--       <label v-help-hint="'form-franqueada_dias_em_abertos_movimentos'" for="dias_em_abertos_movimentos" class="col-form-label">Dias em aberto</label> -->
        <!--       <input v-mask="'#########'" id="dias_em_abertos_movimentos" v-model="dias_em_abertos_movimentos" type="text" class="form-control" maxlength="9"> -->
        <!--     </div> -->

        <!--     <div class="col-md-4"> -->
        <!--       <label v-help-hint="'form-franqueada_dias_lembrete_cobranca'" for="dias_lembrete_cobranca" class="col-form-label">Dias para enviar lembrete de vencimento de parcela *</label> -->
        <!--       <input v-mask="'#########'" id="dias_lembrete_cobranca" v-model="dias_lembrete_cobranca" type="text" class="form-control" required maxlength="9"> -->
        <!--     </div> -->

        <!--     <div class="col-md-4"> -->
        <!--       <label v-help-hint="'form-franqueada_dias_para_negativacao'" for="franqueada_dias_para_negativacao" class="col-form-label">Dias para negativação *</label> -->
        <!--       <input v-mask="'#########'" id="franqueada_dias_para_negativacao" v-model="dias_para_negativacao" type="text" class="form-control" required maxlength="9"> -->
        <!--     </div> -->
        <!--   </div> -->

        <!--   <div class="form-group row "> -->
        <!--     <div class="col-md-4"> -->
        <!--       <label v-help-hint="'form-franqueada_tipo_movimento_conta_receber'" for="tipo_movimento_conta_receber" class="col-form-label">Contas a Receber</label> -->
        <!--       <select id="tipo_movimento_conta_receber" v-model="tipo_movimento_conta_receber" class="custom-select form-control"> -->
        <!--         <option value>Nenhum</option> -->
        <!--         <template v-for="(conta_receber, index) in listaTipoMovimento"> -->
        <!--           <option v-if="conta_receber.tipo_operacao == 'C'" :key="index" :value="conta_receber.id">{{ conta_receber.descricao }}</option> -->
        <!--         </template> -->
        <!--       </select> -->
        <!--     </div> -->

        <!--     <div class="col-md-4"> -->
        <!--       <label v-help-hint="'form-franqueada_tipo_movimento_conta_pagar'" for="tipo_movimento_conta_pagar" class="col-form-label">Contas a Pagar</label> -->
        <!--       <select id="tipo_movimento_conta_pagar" v-model="tipo_movimento_conta_pagar" class="custom-select form-control"> -->
        <!--         <option value>Nenhum</option> -->
        <!--         <template v-for="(conta_pagar, index) in listaTipoMovimento"> -->
        <!--           <option v-if="conta_pagar.tipo_operacao == 'D'" :key="index" :value="conta_pagar.id">{{ conta_pagar.descricao }}</option> -->
        <!--         </template> -->
        <!--       </select> -->
        <!--     </div> -->

        <!--     <div class="col-md-4"> -->
        <!--       <label v-help-hint="'form-franqueada_sabado_dia_util'" class="col-form-label">Sábado como dia útil</label> -->
        <!--       <div class="custom-control custom-checkbox"> -->
        <!--         <input id="sabado_dia_util" v-model="sabado_dia_util" type="checkbox" class="custom-control-input"> -->
        <!--         <label class="custom-control-label" for="sabado_dia_util">Considerar</label> -->
        <!--       </div> -->
        <!--     </div> -->
        <!--   </div> -->

        <!--   <div class="form-group row"> -->

        <!--     <div class="input-group col-md-3"> -->
        <!--       <div class="custom-control custom-checkbox"> -->
        <!--         <input id="desconto_super_amigos_ativo" v-model="desconto_super_amigos_ativo" type="checkbox" class="custom-control-input"> -->
        <!--         <label class="custom-control-label col-form-label" for="desconto_super_amigos_ativo">Desconto Super Amigos</label> -->
        <!--       </div> -->

        <!--       <div class="input-group mb-3"> -->
        <!--         <div class="input-group-prepend pre-icon"><span id="pre-icon-desconto" class="input-group-text">R$</span></div> -->
        <!--         <input v-money="moeda" id="desconto_super_amigos" v-model="desconto_super_amigos" :disabled="!desconto_super_amigos_ativo" v-model.lazy="desconto_super_amigos" type="text" class="form-control" placeholder="Valor" aria-describedby="pre-icon-desconto" maxlength="9"> -->
        <!--       </div> -->

        <!--     </div> -->

        <!--     <div class="input-group col-md-3"> -->

        <!--       <div class="custom-control custom-checkbox"> -->
        <!--         <input id="desconto_super_amigos_turbinado_ativo" v-model="desconto_super_amigos_turbinado_ativo" type="checkbox" class="custom-control-input"> -->
        <!--         <label class="custom-control-label col-form-label" for="desconto_super_amigos_turbinado_ativo">Desconto Super Amigos Turbinado</label> -->
        <!--       </div> -->

        <!--       <div class="input-group mb-3"> -->
        <!--         <div class="input-group-prepend pre-icon"><span id="pre-icon-desconto" class="input-group-text">R$</span></div> -->
        <!--         <input v-money="moeda" id="desconto_super_amigos_turbinado" v-model="desconto_super_amigos_turbinado" :disabled="!desconto_super_amigos_turbinado_ativo" v-model.lazy="desconto_super_amigos_turbinado" type="text" class="form-control" placeholder="Valor" aria-describedby="pre-icon-desconto" maxlength="9"> -->
        <!--       </div> -->

        <!--     </div> -->

        <!--     <div class="input-group col-md-3"> -->
        <!--       <label v-help-hint="'form-franqueada_desconto_avista'" class="col-form-label" for="desconto_avista">Desconto à vista dia</label> -->
        <!--       <div class="input-group mb-3"> -->
        <!--         <div class="input-group-prepend pre-icon"><span id="pre-icon-desconto" class="input-group-text">R$</span></div> -->
        <!--         <input v-money="moeda" id="desconto_avista" v-model="desconto_avista" type="text" class="form-control" placeholder="Valor" aria-describedby="pre-icon-desconto" maxlength="6"> -->
        <!--       </div> -->
        <!--     </div> -->

          <div class="input-group col-md-3"> 
            <label v-help-hint="'form-franqueada_percentual_juro_dia'" class="col-form-label" for="percentual_juro_dia">Percentual de Juros por dia</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend pre-icon"><span id="pre-icon-desconto" class="input-group-text">%</span></div>
                <input v-money="moedaJurosDia" id="percentual_juro_dia" v-model="percentual_juro_dia" v-model.lazy="percentual_juro_dia" type="text" class="form-control" placeholder="Valor" aria-describedby="pre-icon-desconto" maxlength="6">
            </div>
            </div>

            <div class="input-group col-md-3">
                <label v-help-hint="'form-franqueada_percentual_multa'" class="col-form-label" for="percentual_multa">Percentual de Multa aplicado</label>
                <div class="input-group mb-3"> 
                    <div class="input-group-prepend pre-icon"><span id="pre-icon-desconto" class="input-group-text">%</span></div> 
                    <input v-money="moeda" id="percentual_multa" v-model="percentual_multa" v-model.lazy="percentual_multa" type="text" class="form-control" placeholder="Valor" aria-describedby="pre-icon-desconto" maxlength="6">
                </div> 
            </div> 

            <!-- Mensagem de Atenção -->
                <div v-if="mostrarMensagemAtencao === true" class="alert alert-warning" role="alert">
                  "Atenção: Detectamos que as taxas de juros estão atualmente acima do limite legal. 
                            Recomendamos revisar e ajustar conforme a legislação para evitar possíveis 
                            consequências no futuro."
                </div>
          
      </div> 
    </div> 

        <div v-if="isEdit === false" class="p-3">
          <h5 class="title-module mb-2">Usuário Padrão</h5>
          <div class="form-group row">
            <div class="col-md-4">
              <label v-help-hint="'form-franqueada_cpf_usuario_franqueada'" for="cpf_usuario_franqueada" class="col-form-label">CPF do usuário padrão para a franqueada *</label>
              <input v-mask="'###.###.###-##'" id="cpf_usuario_franqueada" v-model="cpf" :class="{ 'is-invalid' : !isValid && $v.cpf.$invalid }" type="text" class="form-control" required>
              <div v-if="!isValid && $v.cpf.$invalid" class="input-invalid">CPF informado é inválido!</div>
              <div v-else class="invalid-feedback">Preencha o CPF do usuário</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-verde">Salvar</button>
          <router-link :to="$route.matched[0].path" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import EventBus from '../../utils/event-bus'
import {mapState, mapActions, mapMutations} from 'vuex'
import { required, requiredIf, email } from 'vuelidate/lib/validators'
import {isCpfValido, isCnpjValido} from '../../utils/format'

const verificaCpf = (value, vm) => {
  if (typeof value === 'string' && value.length > 0) {
    return isCpfValido(value)
  }

  return true
}
const verificaCnpj = (value, vm) => {
  if (typeof value === 'string' && value.length > 0) {
    return isCnpjValido(value)
  }

  return true
}

export default {
  name: 'FormularioFranqueada',
  data () {
    return {
      isValid: true,
      isEdit: false,
      errorMsg: '',
      nome: '',
      cnpj: '',
      cpf: '',
      dias_em_abertos_movimentos: null,
      dias_lembrete_cobranca: null,
      percentual_juro_dia: 0, // Valor do Percentual de Juros
      percentual_multa: 0, // Valor do Percentual de Multa
      mostrarMensagemAtencao: false, // Flag para mostrar a mensagem de atenção
      sabado_dia_util: 0,
      dias_para_negativacao: null,
      razao_social: '',
      endereco: '',
      inscricao_estadual: '',
      telefone: '',
      telefone_secundario: '',
      email: '',
      email_direcao: '',
      email_comercial: '',
      tipo_movimento_conta_receber: '',
      tipo_movimento_conta_pagar: '',
      desconto_super_amigos: '',
      desconto_super_amigos_turbinado: '',
      desconto_avista: '',
      situacao: true,
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: false
      },
      moedaJurosDia: {
        decimal: '.',
        precision: 3,
        masked: false
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
      carregandoTipoMovimento: true,
      desconto_super_amigos_ativo: false,
      desconto_super_amigos_turbinado_ativo: false,
      desconto_avista_ativo: false
    }
  },

  validations: {
    nome: {required},
    cnpj: {required, verificaCnpj},
    // dias_para_negativacao: {required},
    // dias_lembrete_cobranca: {required},
    cpf: {required: requiredIf('!isEdit'), verificaCpf},
    email_direcao: {required, email},
    email_comercial: {required, email},
    email: {email}
  },

  computed: {
    ...mapState('franqueadas', ['objFranqueada', 'estaCarregando']),
    ...mapState('tipoMovimentoConta', {listaTipoMovimento: 'lista'})
  },

  watch: {
    nome (value) {
      this.setNome(value)
    },

    cnpj (value) {
      this.setCNPJ(value)
    },

    cpf (value) {
      this.setCPF(value)
    },

    dias_em_abertos_movimentos (value) {
      this.setDiasEmAberto(value === '' ? 0 : value)
    },

    dias_para_negativacao (value) {
      this.setDiasParaNegativacao(value === '' ? 0 : value)
    },

    dias_lembrete_cobranca (value) {
      this.setDiasLembreteCobranca(value === '' ? 0 : value)
    },

    sabado_dia_util (value) {
      this.setSabadoDiasUteis(value === true ? 1 : 0)
    },

    desconto_turbinado_ativo (value) {
      this.setDescontoTurbinadoAtivo(value === true ? 1 : 0)
    },

    razao_social (value) {
      this.setRazaoSocial(value)
    },

    endereco (value) {
      this.setEndereco(value)
    },

    inscricao_estadual (value) {
      this.setInscricaoEstadual(value)
    },

    telefone (value) {
      this.setTelefone(value)
    },

    telefone_secundario (value) {
      this.setTelefoneSecundario(value)
    },

    email (value) {
      this.setEmail(value)
    },

    email_direcao (value) {
      this.setEmailDirecao(value)
    },

    email_comercial (value) {
      this.setEmailComercial(value)
    },

    desconto_super_amigos (value) {
      this.setDescontoSuperAmigos(value)
    },

    desconto_super_amigos_turbinado (value) {
      this.setDescontoSuperAmigosTurbinado(value)
    },

    desconto_super_amigos_ativo (value) {
      this.setDescontoSuperAmigosAtivo(value)
    },

    desconto_super_amigos_turbinado_ativo (value) {
      this.setDescontoTurbinadoAtivo(value)
    },

    desconto_avista (value) {
      this.setDescontoAvista(value)
    },

    percentual_multa (value) {
      this.setPercentualMulta(value)
      this.verificarValorExcedente(value, 2.00);
    },

    percentual_juro_dia (value) {
      this.setPercentualJuroDia(value)
      this.verificarValorExcedente(value, 0.033);
    },

    tipo_movimento_conta_receber (value) {
      this.setMovimentoContasReceber(value)
    },

    tipo_movimento_conta_pagar (value) {
      this.setMovimentoContasPagar(value)
    },

    situacao (value) {
      this.setSituacao(value === true ? 'A' : 'I')
    },

    objFranqueada (value) {
      this.nome = value.nome
      this.cnpj = value.cnpj
      this.cpf = value.cpf
      this.dias_em_abertos_movimentos = value.dias_em_abertos_movimentos
      this.dias_para_negativacao = value.dias_para_negativacao
      this.dias_lembrete_cobranca = value.dias_lembrete_cobranca
      this.sabado_dia_util = value.sabado_dia_util
      this.razao_social = value.razao_social
      this.endereco = value.endereco
      this.inscricao_estadual = value.inscricao_estadual
      this.telefone = value.telefone
      this.telefone_secundario = value.telefone_secundario
      this.email = value.email
      this.email_direcao = value.email_direcao
      this.email_comercial = value.email_comercial
      this.tipo_movimento_conta_receber = value.tipo_movimento_conta_receber ? value.tipo_movimento_conta_receber.id : null
      this.tipo_movimento_conta_pagar = value.tipo_movimento_conta_pagar ? value.tipo_movimento_conta_pagar.id : null
      this.desconto_super_amigos = value.desconto_super_amigos
      this.desconto_super_amigos_turbinado = value.desconto_super_amigos_turbinado
      this.desconto_super_amigos_ativo = value.desconto_super_amigos_ativo
      this.desconto_super_amigos_turbinado_ativo = value.desconto_super_amigos_turbinado_ativo
      this.percentual_juro_dia = value.percentual_juro_dia
      this.percentual_multa = value.percentual_multa
      this.situacao = value.situacao === 'A'
      this.desconto_avista = value.valor_desconto_avista ? value.valor_desconto_avista : 0
    }
  },
  created () {
    this.carregandoTipoMovimento = true
    this.getLista().then(() => { this.carregandoTipoMovimento = false })
    this.limparFranqueada()

    if (this.$route.params.id) {
      this.isEdit = true
      this.setFranqueadaSelecionada(this.$route.params.id)
      this.getFranqueada()
        .then(() => {
          this.cep_data = {
            endereco: this.objFranqueada.endereco,
            numero_endereco: this.objFranqueada.numero_endereco,
            complemento_endereco: this.objFranqueada.complemento_endereco,
            bairro_endereco: this.objFranqueada.bairro_endereco,
            cep_endereco: this.objFranqueada.cep_endereco,
            estado: this.objFranqueada.estado,
            cidade: this.objFranqueada.cidade
          }
        })
    }
  },

  methods: {
    ...mapActions('franqueadas', ['getListaFranqueada', 'getFranqueada', 'criarFranqueada', 'atualizarFranqueada']),
    ...mapActions('tipoMovimentoConta', ['getLista']),
    ...mapMutations('franqueadas', ['setFranqueada', 'setNome', 'setCNPJ', 'setCPF', 'setDiasParaNegativacao', 'setDiasEmAberto', 'setDiasLembreteCobranca', 'setSabadoDiasUteis',
      'setRazaoSocial', 'setEndereco', 'setInscricaoEstadual', 'setTelefone', 'setTelefoneSecundario', 'setEmail', 'setEmailDirecao', 'setEmailComercial',
      'setDescontoSuperAmigos', 'setDescontoSuperAmigosTurbinado', 'setDescontoSuperAmigosAtivo', 'setDescontoTurbinadoAtivo', 'setDescontoAvista', 'setPercentualJuroDia', 'setPercentualMulta', 'setMovimentoContasReceber', 'setMovimentoContasPagar',
      'setSituacao', 'setFranqueadaSelecionada', 'limparFranqueada', 'SET_ESTA_CARREGANDO']),

    voltar () {
      this.setFranqueadaSelecionada(null)
      this.limparFranqueada()
      this.$router.push(this.$route.matched[0].path)
    },

       // Verifica se o valor excede o limite e atualiza a flag para mostrar a mensagem de atenção
    verificarValorExcedente(valor, limite) {
      var valorCorrigido = valor.replace(',', '.'); // Substituir vírgula por ponto
      var valorPorcentagem = parseFloat(valorCorrigido);
     
      if (valorPorcentagem  > limite) {
        this.mostrarMensagemAtencao = true;
      } else {
        this.mostrarMensagemAtencao = false;
      }
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.isEdit) {
        this.atualizarFranqueada()
          .then(() => {
            this.voltar()
            EventBus.$emit('usuario-logado::validar-acesso')
          })
      } else {
        this.criarFranqueada()
          .then(() => {
            this.voltar()
            EventBus.$emit('usuario-logado::validar-acesso')
          })
      }
    },

    callbackCepData (data) {
      this.cep_data = data
      this.objFranqueada.endereco = data.endereco
      this.objFranqueada.numero_endereco = data.numero_endereco
      this.objFranqueada.complemento_endereco = data.complemento_endereco
      this.objFranqueada.bairro_endereco = data.bairro_endereco
      this.objFranqueada.cep_endereco = data.cep_endereco
      this.objFranqueada.estado = data.estado
      this.objFranqueada.cidade = data.cidade
    }
  }
}
</script>
