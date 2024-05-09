<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="body-sector ">
        <div class="animated fadeIn p-3">
          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'form-modelo_contrato_descricao'" for="descricao" class="col-form-label">Descrição *</label>
              <input id="descricao" :readonly="!podeEditar" v-model="itemSelecionado.descricao" type="text" class="form-control" required maxlength="100">
              <div class="invalid-feedback">Preencha a descrição!</div>
            </div>
            <div class="col-md-6">
              <label v-help-hint="'formulario-modelo_template_tipo'" class="col-form-label" for="tipo">Tipo documento: *</label>
              <g-select id="tipo"
                        :value="tipo"
                        :select="setTipo"
                        :options="tipoOpcoes"
                        :required="true"
                        :disabled="!podeEditar"
                        class="multiselect-truncate"
                        label="label"
                        track-by="id"
              />
            </div>
          </div>

          <div class="form-group">
            <editor-texto :disabled="!podeEditar" v-model="modelo_html"/>
          </div>

          <div class="form-group row">
            <div class="col-md-12">
              <!-- <div class="col-md-2 ml-auto"> -->
              <b-btn v-b-toggle.usoVariaveis.c-franqueada variant="link">Variáveis</b-btn>
              <!-- </div> -->
              <b-collapse id="usoVariaveis">
                <b-card>
                  <b-col md="12">
                    <h3 class="card-text">Variáveis para formatação do modelo de contrato</h3>
                  </b-col>
                  <b-col md="12">

                    <g-table class="table-scroll mobile-cards table b-table table-hover table-borderless">
                      <thead class="text-dark">
                        <tr>
                          <th data-column="">Sobre</th>
                          <th data-column="">Variávies</th>
                          <th data-column="">Como usar?</th>
                          <th class="coluna-icones"></th>
                        </tr>
                      </thead>
                      <tbody ref="scroll-wrap">
                        <perfect-scrollbar>
                          <tr v-b-tooltip.viewport.left.hover v-for="(i, index) in item" :key="index" title="Copiar" @click="copy(i)">
                            <td data-label="Sobre">{{ i.sobre }}</td>
                            <td data-label="Variávies">{{ i.valor }}</td>
                            <td data-label="Como usar?">{{ i.chave }}</td>
                            <td data-label="Copiar" class="d-flex coluna-icones">
                              <a>
                                <font-awesome-icon icon="copy" />
                              </a>
                            </td>
                          </tr>
                        </perfect-scrollbar>
                      </tbody>
                    </g-table>

                  </b-col>
                </b-card>
              </b-collapse>
            </div>
          </div>

        </div>
      </div>

      <div class="form-group pt-2">
        <b-btn v-if="podeEditar" :disabled="contratoVazio" type="submit" variant="verde">Salvar</b-btn>
        <b-btn variant="link" @click="voltar()">{{ podeEditar ? 'Cancelar' : 'Voltar' }}</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'

export default {
  name: 'FormularioModeloContrato',
  data () {
    return {
      isValid: true,
      isEdit: false,
      contratoVazio: true,
      tipo: null,
      tipoOpcoes: [
        {id: null, label: 'Selecione', value: null},
        {id: 1, label: 'Contrato', value: 'CO'},
        {id: 2, label: 'Carta de cobrança', value: 'CC'},
        {id: 3, label: 'Recibos', value: 'RE'}
      ],
      item: [
        {sobre: 'Franqueada', valor: 'Razão Social', chave: '{{ franqueada.razao-social }}'},
        {sobre: 'Franqueada', valor: 'Nome', chave: '{{ franqueada.nome }}'},
        {sobre: 'Franqueada', valor: 'CNPJ ', chave: '{{ franqueada.cnpj }}'},
        {sobre: 'Franqueada', valor: 'Inscrição estadual', chave: '{{ franqueada.inscricao-estadual }}'},
        {sobre: 'Franqueada', valor: 'Endereço', chave: '{{ franqueada.endereco }}'},
        {sobre: 'Franqueada', valor: 'Endereço número', chave: '{{ franqueada.numero-endereco }}'},
        {sobre: 'Franqueada', valor: 'Endereço cidade', chave: '{{ franqueada.cidade }}'},
        {sobre: 'Franqueada', valor: 'Endereço bairro', chave: '{{ franqueada.bairro }}'},
        {sobre: 'Franqueada', valor: 'Endereço estado', chave: '{{ franqueada.estado }}'},
        {sobre: 'Franqueada', valor: 'Endereço complemento', chave: '{{ franqueada.complemento-endereco }}'},
        {sobre: 'Franqueada', valor: 'Endereço cep', chave: '{{ franqueada.cep }}'},
        {sobre: 'Franqueada', valor: 'Endereço combinado', chave: '{{ franqueada.endereco-combinado }}'},
        {sobre: 'Franqueada', valor: 'Telefone', chave: '{{ franqueada.telefone }}'},
        {sobre: 'Franqueada', valor: 'Telefone secundario', chave: '{{ franqueada.telefone-secundario }}'},
        {sobre: 'Franqueada', valor: 'Telefone combinado', chave: '{{ franqueada.telefone-combinado }}'},
        {sobre: 'Responsável Financeiro', valor: 'Nome do Responsável', chave: '{{ resp-financeiro.nome }}'},
        {sobre: 'Responsável Financeiro', valor: 'E-mail do Responsável', chave: '{{ resp-financeiro.email }}'},
        {sobre: 'Responsável Financeiro', valor: 'RG', chave: '{{ resp-financeiro.numero-identidade }}'},
        {sobre: 'Responsável Financeiro', valor: 'CPF/CNPJ', chave: '{{ resp-financeiro.cnpj-cpf }}'},
        {sobre: 'Responsável Financeiro', valor: 'Endereço', chave: '{{ resp-financeiro.endereco }}'},
        {sobre: 'Responsável Financeiro', valor: 'Endereço número', chave: '{{ resp-financeiro.numero-endereco }}'},
        {sobre: 'Responsável Financeiro', valor: 'Endereço cidade', chave: '{{ resp-financeiro.cidade }}'},
        {sobre: 'Responsável Financeiro', valor: 'Endereço estado', chave: '{{ resp-financeiro.estado }}'},
        {sobre: 'Responsável Financeiro', valor: 'Endereço complemento', chave: '{{ resp-financeiro.complemento-endereco }}'},
        {sobre: 'Responsável Financeiro', valor: 'Endereço cep', chave: '{{ resp-financeiro.cep }}'},
        {sobre: 'Responsável Financeiro', valor: 'Endereço combinado', chave: '{{ resp-financeiro.endereco-combinado }}'},
        {sobre: 'Responsável Financeiro', valor: 'Telefone preferencial', chave: '{{ resp-financeiro.telefone-preferencial }}'},
        {sobre: 'Responsável Financeiro', valor: 'Telefone contato', chave: '{{ resp-financeiro.telefone-adicional }}'},
        {sobre: 'Responsável Financeiro', valor: 'Telefone profissional', chave: '{{ resp-financeiro.telefone-profissional }}'},
        {sobre: 'Responsável Financeiro', valor: 'Telefone combinado', chave: '{{ resp-financeiro.telefone-combinado }}'},
        {sobre: 'Contrato', valor: 'Matrícula', chave: '{{ contrato.matricula }}'},
        {sobre: 'Contrato', valor: 'Data da Matrícula', chave: '{{ contrato.data-matricula }}'},
        {sobre: 'Contrato', valor: 'Parcelas geradas no contrato', chave: '{{ parcelas }}'},
        {sobre: 'Contrato', valor: 'Forma de pagamento com desconto até vencimento', chave: '{{ contrato.forma-pagamento }}'},
        {sobre: 'Contrato', valor: 'Valor do desconto até vencimento', chave: '{{ contrato.valor-desconto }}'},
        {sobre: 'Matrícula', valor: 'Valor da taxa de matrícula', chave: '{{ matricula.taxa }}'},
        {sobre: 'Material', valor: 'Valor do material didático', chave: '{{ material.valor-total }}'},
        {sobre: 'Material', valor: 'Nome do material didático', chave: '{{ material.nome }}'},
        {sobre: 'Material', valor: 'Quantidade de parcelas do material didático', chave: '{{ material.quantidade-parcelas }}'},
        {sobre: 'Material', valor: 'Valor das parcelas do material didático', chave: '{{ material.valor-parcelas }}'},
        {sobre: 'Material', valor: 'Dia de vencimento das parcelas do material didático', chave: '{{ material.dia-vencimento }}'},
        {sobre: 'Módulo', valor: 'Valor do módulo', chave: '{{ modulo.valor-total }}'},
        {sobre: 'Módulo', valor: 'Quantidade de parcelas do módulo', chave: '{{ modulo.quantidade-parcelas }}'},
        {sobre: 'Módulo', valor: 'Valor das parcelas do módulo', chave: '{{ modulo.valor-parcelas-sem-desconto }}'},
        {sobre: 'Módulo', valor: 'Dia de vencimento das parcelas do módulo', chave: '{{ modulo.dia-vencimento }}'},
        {sobre: 'Aluno', valor: 'Nome do aluno', chave: '{{ aluno.nome }}'},
        {sobre: 'Aluno', valor: 'E-mail do aluno', chave: '{{ aluno.email }}'},
        {sobre: 'Aluno', valor: 'Data de Nascimento', chave: '{{ aluno.data-nascimento }}'},
        {sobre: 'Aluno', valor: 'RG', chave: '{{ aluno.numero-identidade }}'},
        {sobre: 'Aluno', valor: 'CPF/CNPJ', chave: '{{ aluno.cnpj-cpf }}'},
        {sobre: 'Aluno', valor: 'Telefone preferencial', chave: '{{ aluno.telefone-preferencial }}'},
        {sobre: 'Aluno', valor: 'Telefone contato', chave: '{{ aluno.telefone-adicional }}'},
        {sobre: 'Aluno', valor: 'Telefone profissional', chave: '{{ aluno.telefone-profissional }}'},
        {sobre: 'Aluno', valor: 'Telefone combinado', chave: '{{ aluno.telefone-combinado }}'},
        {sobre: 'Aluno', valor: 'Endereço', chave: '{{ aluno.endereco }}'},
        {sobre: 'Aluno', valor: 'Endereço número', chave: '{{ aluno.numero-endereco }}'},
        {sobre: 'Aluno', valor: 'Endereço bairro', chave: '{{ aluno.bairro }}'},
        {sobre: 'Aluno', valor: 'Endereço cidade', chave: '{{ aluno.cidade }}'},
        {sobre: 'Aluno', valor: 'Endereço estado', chave: '{{ aluno.estado }}'},
        {sobre: 'Aluno', valor: 'Endereço cep', chave: '{{ aluno.cep }}'},
        {sobre: 'Aluno', valor: 'Endereço combinado', chave: '{{ aluno.endereco-combinado }}'},
        {sobre: 'Turma', valor: 'Descrição da Turma', chave: '{{ turma.descricao }}'},
        {sobre: 'Turma', valor: 'Horário da Turma', chave: '{{ turma.horario }}'},
        {sobre: 'Turma', valor: 'Data início', chave: '{{ turma.data-inicio }}'},
        {sobre: 'Turma', valor: 'Data fim', chave: '{{ turma.data-fim }}'},
        {sobre: 'Personal', valor: 'Quantidade de créditos', chave: '{{ creditos_personal.quantidade }}'},
        {sobre: 'Personal', valor: 'Dia e hora das aulas personal do contrato', chave: '{{ personal.dia-hora-aulas }}'},
        {sobre: 'Personal', valor: 'Data de início das aulas do personal', chave: '{{ personal.data-inicio }}'},
        {sobre: 'Personal', valor: 'Data final das aulas do personal', chave: '{{ personal.data-fim }}'}
      ]
    }
  },

  computed: {
    ...mapState('modeloTemplate', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('modulos', ['permissoes']),

    modelo_html: {
      get () {
        return this.itemSelecionado.modelo_html
      },
      set (value) {
        this.contratoVazio = false
        this.SET_MODELO_HTML(value)
      }
    },

    usuarioLogadoFranqueadora: {
      get () {
        return this.$store.state.root.usuarioLogado.logadoFranqueadora
      }
    },
    podeEditar: {
      get () {
        return this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true) && this.usuarioLogadoFranqueadora
      }
    }
  },
  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
        .then((modelo) => {
          this.modelo_html = modelo.modelo_html
          this.tipo = this.tipoOpcoes.find(tipo => tipo.value === modelo.tipo_template)
        })
        .catch((error) => {
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Modelo de contrato não encontrado na base de dados.'
          })

          this.$router.replace('/configuracoes/modelo-contrato')
        })
    }
  },
  validations: {
    itemSelecionado: {
      descricao: {required}
    },
    tipo: {required}
  },
  methods: {
    ...mapMutations('modeloTemplate', ['SET_ITEM_SELECIONADO_ID', 'SET_DESCRICAO', 'SET_MODELO_HTML', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('modeloTemplate', ['buscar', 'criar', 'atualizar']),

    copy (i) {
      this.$copyText(i.chave).then(function (e) {
        let mensagem = "Variável: '" + e.text + "' copiada!"
        EventBus.$emit('criarAlerta', {
          tipo: 'S',
          mensagem: mensagem
        })
      }, function (e) {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: 'Não copiado'
        })
      })
    },

    setTipo (value) {
      this.tipo = value.id === null ? null : value
      if (this.tipo) {
        this.$store.commit('modeloTemplate/SET_TIPO', this.tipo.value)
      } else {
        this.$store.commit('modeloTemplate/SET_TIPO', null)
      }
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/modelo-template')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.itemSelecionadoID) {
        this.atualizar().then(() => {
          this.voltar()
        }).catch(console.error)
      } else {
        this.criar().then(() => {
          this.voltar()
        }).catch(console.error)
      }
    }
  }
}
</script>

<style scoped>
#usoVariaveis .card-body {
  overflow: auto;
}

td:hover{
  cursor: pointer
}
</style>
