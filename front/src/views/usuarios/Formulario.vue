<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar(true)">
      <!-- <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div> -->
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="body-sector">
        <div class="content-sector sector-verde-c p-2">
          <div class="form-group row">
            <div class="col-md-4">
              <label v-help-hint="'form-usuario_nome'" for="nome" class="col-form-label">Nome *</label>
              <input id="nome" v-model="nome" type="text" class="form-control" required maxlength="255">
              <div class="invalid-feedback">Preencha o nome!</div>
            </div>

            <div class="col-md-4">
              <label v-help-hint="'form-usuario_cpf'" for="cpf" class="col-form-label">CPF *</label>
              <input v-mask="'###.###.###-##'" id="cpf" v-model="cpf" :readonly="objUsuario && objUsuario.id" type="text" class="form-control" required>
              <div class="invalid-feedback">Preencha o CPF corretamente!</div>
            </div>

            <div class="col-md-4">
              <label v-help-hint="'form-usuario_email'" for="email" class="col-form-label">E-mail</label>
              <input id="email" v-model="email" type="email" class="form-control" maxlength="255">
              <div class="invalid-feedback">Preencha o e-mail corretamente!</div>
            </div>
          </div>
        </div>

        <div class="content-sector sector-azul p-2">
          <div class="form-group row">
            <div class="col-md-4">
              <h5 v-help-hint="'form-usuario_buscar'" class="title-module">Acesso à franqueadas *</h5>
              <input v-model="buscar" type="text" class="form-control" placeholder="Buscar..." maxlength="80" @input="filtrar()">
            </div>

            <div class="col-md-4">
              <label v-help-hint="'form-usuario_opcao_lista'"></label>
              <b-form-radio-group v-model="opcao_lista" :options="tipo_lista" buttons button-variant="cinza" class="checkbtn-line mt-2" @input="filtrar()"/>
            </div>
          </div>

          <div class="table-responsive-sm form-group">
            <g-table :class="{ 'franqueada-danger':!isValid && !objUsuario.franqueada_padrao || !isValid && selected.length === 0 }">
              <thead>
                <tr>
                  <th class="coluna-checkbox">
                    <b-form-checkbox :disabled="!listaFranqueadaFiltrada.length" v-model="checkAll" :indeterminate="indeterminate" class="m-0 p-0" aria-describedby="selected" aria-controls="selected" @change="toggleAll"/>
                  </th>
                  <th>Franqueada</th>
                  <th>Telefone</th>
                  <th>CNPJ</th>
                </tr>
              </thead>

              <tbody ref="scroll-wrap">
                <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
                  <tr v-for="item in listaFranqueadaFiltrada" :key="item.id" :lista="list = listaFranqueadaFiltrada.map(item => item.id)">
                    <td class="coluna-checkbox" data-label="Selecione">
                      <b-form-checkbox v-model="selected" :value="item.id" :required="!(selected.length > 0)" class="m-0" />
                    </td>
                    <td data-label="Franqueada">{{ item.nome }}</td>
                    <td data-label="Telefone">{{ item.telefone | formatarTelefone }}</td>
                    <td data-label="CNPJ">{{ item.cnpj | formatarCNPJ }}</td>
                  </tr>
                  <div v-if="!listaFranqueadaFiltrada.length && !carregando" class="busca-vazia">
                    <p>Nenhum resultado encontrado.</p>
                  </div>
                </perfect-scrollbar>
              </tbody>
            </g-table>
          </div>

          <div>
            <label v-help-hint="'form-usuario_franqueado_padrao'" class="col-form-label" for="franqueada_padrao">Franqueada Padrão *</label>
            <g-select id="franqueada_padrao" :class="!isValid && !objUsuario.franqueada_padrao ? 'invalid-input' : 'valid-input'"
                      :select="setFranqueadoraPadrao"
                      :value="objUsuario.franqueada_padrao"
                      :options="listaFranqueada.filter(item => selected.includes(item.id))"
                      class="multiselect-truncate"
                      label="nome"
                      track-by="id"
            />
          </div>

          <div v-if="!isValid && !objUsuario.franqueada_padrao || !isValid && selected.length === 0" class="list-group-item list-group-item-accent-danger list-group-item-danger border-0 mt-3">
            Selecione as franqueadas de acesso e a franqueada padrão do usuário!
          </div>
        </div>

        <div class="content-sector sector-verde p-2">
          <div v-if="papeisLista.length > 0">
            <h5 v-help-hint="'form-usuario_titulo_papeis'" class="title-module">Papéis</h5>
            <div class="row">
              <div v-for="papel, key in papeisLista" :key="key" class="col-md-3">
                <b-form-checkbox
                  v-model="papeisSelecionados"
                  :value="papel.id"
                  :inline="true"
                  :name="'papel_'+papel.id"
                  class="inline-checkbox"
                  @change="setPermissaoDoPapel">
                  {{ papel.descricao }}
                </b-form-checkbox>

                <font-awesome-icon v-b-tooltip.viewport.right.hover title="Lista de permissões" class="btn-lista-permissoes" icon="clipboard-list" @click="infoPapel(papel)"/>
              </div>
            </div>
          </div>
        </div>

        <div class="content-sector sector-verde ">
          <h5 v-help-hint="'form-usuario_titulo_permissoes'" class="title-module pl-2">
            Permissões do Usuário
            <b-btn v-if="isEdit === true" variant="link" @click="rollbackPermissoes()">Restaurar permissões</b-btn>
            <b-btn v-else variant="link" @click="rollbackPermissoes()">Limpar permissões</b-btn>
          </h5>
          <div class="table-responsive-sm min-height-300">
            <div v-if="arvoreItens && arvoreItens.length" class="table-scroll w-100">
              <perfect-scrollbar>
                <tree v-for="item in arvoreItens" ref="componenteArvorePermissao" :key="item.id" :usuario-id="objUsuario.id || null" :item="item" />
              </perfect-scrollbar>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!objUsuario.id" class="form-group list-group-accent">
        <div class="list-group-item list-group-item-accent-info list-group-item-info border-0">
          <font-awesome-icon icon="info-circle" /> A senha padrão do novo usuário é composta pelos 3 (três) últimos dígitos do CPF.
        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-12">
          <b-btn :disabled="isEnviando" variant="verde" @click="salvar(false)">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
          <b-btn :disabled="isEnviando" type="submit" variant="verde">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn>

          <router-link to="/configuracoes/usuario" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>
    </form>

    <b-modal id="modal-permissoes" v-model="moduloInfo" size="md" centered hide-header hide-footer @hide="infoPapel()">
      <div id="papel-container" class="row">
        <div v-for="(item, key) in listaObj" :key="key" :id="`papel-${item.id}`" class="col-12 papel-permissoes">
          <h5 class="title-module mb-3">Permissões de {{ item.descricao }}</h5>

          <template v-if="item.permissoes.length">
            <template v-for="(modulo, index) in item.permissoes">
              <div v-if="modulo.permissoes.length" :key="index" class="modulo">
                <label class="d-block">{{ modulo.nome }}</label>

                <div>
                  {{ modulo.permissoes.map(acao => acao.permissao_descricao || acao.descricao).join(', ') }}
                </div>
              </div>
            </template>
          </template>

          <template v-else>
            <div class="empty-rules">
              <p class="mb-3">Não há regras definidas para este papel.</p>
            </div>
          </template>
        </div>
      </div>

      <button type="button" class="btn btn-link" @click="moduloInfo = false">Fechar</button>
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required, email} from 'vuelidate/lib/validators'
import {converterParaEnvio, montarArvore} from '../../utils/permissao'
import Tree from './Tree'

export default {
  name: 'FormularioUsuario',
  components: {
    Tree
  },

  data () {
    return {
      isValid: true,
      isEnviando: false,
      errorMsg: '',
      nome: '',
      email: '',
      cpf: '',
      permissoesCarregadasDoUsuario: [],
      checkAll: false,
      indeterminate: false,
      selected: [],
      franqueadas: [],
      papeisSelecionados: [],
      listaRetorno: [],
      list: [],
      moduloInfo: false,
      isEdit: false,
      buscar: '',
      listaFranqueadaFiltrada: [],
      mensagemErro: [],
      modulosSelecionados: [],
      papeisLista: [],
      listaObj: {},
      opcao_lista: 1,
      tipo_lista: [
        {text: 'Todos', value: 1},
        {text: 'Selecionadas', value: 2},
        {text: 'Não selecionadas', value: 3}
      ]
    }
  },

  validations: {
    nome: { required },
    email: { email },
    cpf: { required },
    selected: { required },
    franqueada_padrao: { required }
  },

  computed: {
    ...mapState('usuarios', ['listaUsuarios', 'objUsuario', 'listaModulosPais', 'estaCarregando']),
    ...mapState('franqueadas', { listaFranqueada: 'listaFranqueada', totalItens: 'totalItens', carregando: 'estaCarregando', todosItensCarregados: 'todosItensCarregados' }),

    ...mapState('modulos', ['permissoes']),
    ...mapState('permissao', ['listaPermissao', 'arvoreItens', 'todosItensCarregados']),

    franqueada_padrao: {
      get () {
        return this.objUsuario.franqueada_padrao
      },
      set (value) {
        this.setFranqueadoraPadrao(value)
      }
    },

    permitirCarregarMais: {
      get () {
        return !!this.listaFranqueada.length && !this.carregando && !this.todosItensCarregados
      }
    }
  },

  watch: {
    objUsuario (value) {
      this.nome = value.nome
      this.email = value.email
      this.cpf = value.cpf
      this.situacao = value.situacao

      this.selected.splice(0, this.selected.length)
      this.selected = value.franqueadas.map(item => item.id)

      this.franqueada_padrao = value.franqueada_padrao
    },

    nome (value) {
      this.setNome(value)
    },

    cpf (value) {
      this.setCpf(value)
    },

    email (value) {
      this.setEmail(value)
    },

    selected (value, oldVal) {
      // Handle changes in individual checkboxes
      if (value.length === 0) {
        this.indeterminate = false
        this.checkAll = false
      } else if (value.length === this.list.length) {
        this.indeterminate = false
        this.checkAll = true
      } else {
        this.indeterminate = true
        this.checkAll = false
      }

      this.SET_FRANQUEADAS(value)

      if (oldVal.length > value.length && this.objUsuario.franqueada_padrao !== '') {
        let isDeselected = true
        for (let i = value.length; i--;) {
          if (value[i] === this.objUsuario.franqueada_padrao.id) {
            isDeselected = false
          }
        }
        if (isDeselected) {
          this.objUsuario.franqueada_padrao = ''
        }
      }
    }
  },

  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.listaObj = {}

    this.setPagina(1)
    this.limparUsuario()

    this.carregarMais()

    this.buscarPapeis()
      .then(papeisRetorno => {
        this.papeisLista = papeisRetorno
        console.log('Console',this.papeisLista)

        papeisRetorno.map(item => {
          this.SET_FILTROS({papel: item.id})
          this.listarPermissao({efetuarCommit: false})
            .then(listaPermissoes => {
              const permissoesModulo = listaPermissoes.map(modulo => {
                modulo.permissoes = modulo.acaoSistemas.map(acao => {
                  if (modulo.modulo_papel_acao !== undefined && modulo.modulo_papel_acao.find(mpa => mpa.acao_sistema_id === acao.id) !== undefined) {
                    return acao
                  }
                }).filter(acao => acao !== undefined)

                return modulo
              }).filter(modulo => modulo.permissoes.length > 0)

              this.listaObj[item.id] = {
                ...item,
                permissoes: permissoesModulo
              }

              this.listaObj = {...this.listaObj}

              this.SET_PAGINA_ATUAL(1)
            })
        })
      })

    let idUsuario = this.$route.params.id

    const parametros = {
      usuario: idUsuario
    }

    this.SET_FILTROS(parametros)
    this.SET_LISTA([])
    this.listarPermissao()

    if (idUsuario) {
      this.isEdit = true
      this.setUsuarioSelecionado(idUsuario)
      this.getUsuario()
        .then(response => {
          if (this.objUsuario.papels !== undefined) {
            this.objUsuario.papels.map((item) => {
              return item.id
            })
            this.papeisSelecionados = this.objUsuario.papels
            this.permissoesCarregadasDoUsuario = JSON.parse(JSON.stringify(this.arvoreItens))
          }
        }).catch(console.error)
    }
  },

  methods: {
    ...mapActions('usuarios', ['getUsuario', 'criarUsuario', 'atualizarUsuario']),
    ...mapActions('franqueadas', {listar: 'getListaFranqueada'}),
    ...mapActions('papel', ['buscarPapeis']),
    ...mapActions('permissao', {buscarPermissaoPorUsuario: 'buscarPermissaoPorUsuario', listarPermissao: 'listar', atualizar: 'atualizar'}),
    ...mapMutations('franqueadas', {setPagina: 'SET_PAGINA_ATUAL', setOrderBy:'SET_ORDER_BY'}),
    ...mapMutations('permissao', ['SET_PERMISSAO', 'SET_FILTROS', 'SET_PAGINA_ATUAL', 'SET_LISTA', 'SET_ARVORE_ITENS']),
    ...mapMutations('usuarios', ['setUsuario', 'limparUsuario', 'setUsuarioSelecionado', 'setNome', 'setEmail', 'setFranqueadoraPadrao', 'setSituacao', 'setCpf', 'SET_FRANQUEADAS', 'SET_ESTA_CARREGANDO', 'SET_PERMISSOES_PARAMETROS', 'SET_PAPEIS_PARAMETROS']),

    toggleAll (checked) {
      this.selected = checked ? this.list.slice() : []
    },

    filtrar () {
      if (this.opcao_lista === 2) {
        this.listaFranqueadaFiltrada = this.listaFranqueada.filter(item => this.selected.includes(item.id))
      } else if (this.opcao_lista === 3) {
        this.listaFranqueadaFiltrada = this.listaFranqueada.filter(item => !this.selected.includes(item.id))
      } else {
        this.listaFranqueadaFiltrada = this.listaFranqueada
      }
      this.listaFranqueadaFiltrada = this.listaFranqueadaFiltrada.filter(item => item.cnpj.includes(this.buscar) || item.nome.toUpperCase().includes(this.buscar.toUpperCase()))
    },

    voltar () {
      this.setUsuarioSelecionado(null)
      this.limparUsuario()
      this.$router.push('/configuracoes/usuario')
    },

    tratativaErro () {
      console.info('Ocorreu um erro')
      this.isEnviando = false
    },

    setarPermissaoNaArvore (arvoreMontadaComFilhos, bAdiciona) {
      let primeiroTreeComponente = this.$refs.componenteArvorePermissao[0]
      arvoreMontadaComFilhos.forEach((moduloPai) => {
        if (moduloPai.filhos.length > 0) {
          moduloPai.filhos.forEach((filho) => {
            // if(filho.length > 0) {
            this.setarPermissaoNaArvore([filho], bAdiciona)
            /* }
            if(filho.permissoes) {
              filho.permissoes.forEach((acao) => {
                primeiroTreeComponente.change(bAdiciona, {id: filho.id}, {id: acao.id})
              })
            } else if(filho.moduloUsuarioAcaos) {
              filho.moduloUsuarioAcaos.forEach((acao) => {
                primeiroTreeComponente.change(bAdiciona, {id: filho.id}, {id: acao.acao_sistema_id})
              })
            } */
          })
          if (moduloPai.permissoes) {
            moduloPai.permissoes.forEach((acao) => {
              primeiroTreeComponente.change(bAdiciona, {id: moduloPai.id}, {id: acao.id})
            })
          } else if (moduloPai.moduloUsuarioAcaos) {
            moduloPai.moduloUsuarioAcaos.forEach((acao) => {
              primeiroTreeComponente.change(bAdiciona, {id: moduloPai.id}, {id: acao.acao_sistema_id})
            })
          }
        } else {
          if (moduloPai.permissoes) {
            moduloPai.permissoes.forEach((acao) => {
              primeiroTreeComponente.change(bAdiciona, {id: moduloPai.id}, {id: acao.id})
            })
          } else if (moduloPai.moduloUsuarioAcaos) {
            moduloPai.moduloUsuarioAcaos.forEach((acao) => {
              primeiroTreeComponente.change(bAdiciona, {id: moduloPai.id}, {id: acao.acao_sistema_id})
            })
          }
        }
      })
    },

    rollbackPermissoes () {
      this.setarPermissaoNaArvore(this.arvoreItens, false)
      if (this.isEdit) {
        let tempArray = JSON.parse(JSON.stringify(this.permissoesCarregadasDoUsuario))
        this.setarPermissaoNaArvore(tempArray, true)
      }
    },

    setPermissaoDoPapel (value) {
      let papelObject = this.listaObj[value]
      if (papelObject) {
        let permissoesDoPapel = papelObject.permissoes
        let arvoreMontadaPermissao = montarArvore(permissoesDoPapel)
        this.setarPermissaoNaArvore(arvoreMontadaPermissao, true)
      }
    },

    infoPapel (papel) {
      if (papel) {
        if (document.getElementById(`papel-${papel.id}`)) {
          document.getElementById(`papel-${papel.id}`).classList.add('show')
        }
        this.moduloInfo = true
      } else {
        if (document.getElementsByClassName('papel-permissoes show')[0]) {
          document.getElementsByClassName('papel-permissoes show')[0].classList.toggle('show')
        }
      }
    },

    carregarMais () {
      this.setOrderBy({order:"fran.nome", direcao:"ASC"})
      this.listar()
        .then(() => {
          this.listaFranqueadaFiltrada = this.listaFranqueada
        })
    },

    salvar (sair) {
      this.mensagemErro = []
      this.isEnviando = true
      if (this.$v.$invalid) {
        this.isValid = false
        this.isEnviando = false
        return
      }

      const dadosPermissao = converterParaEnvio(this.listaPermissao)

      this.SET_PERMISSOES_PARAMETROS(dadosPermissao)
      this.SET_PAPEIS_PARAMETROS(this.papeisSelecionados)
      if (this.isEdit) {
        this.atualizarUsuario().then(() => {
          if (sair) {
            this.voltar()
            return
          }
          setTimeout(() => { this.$router.go() }, 300)
        }).catch(this.tratativaErro)
      } else {
        this.criarUsuario().then(() => {
          if (sair) {
            this.voltar()
            return
          }
          setTimeout(() => { this.$router.go() }, 300)
        }).catch(this.tratativaErro)
      }
    }
  }
}
</script>

<style scoped>
.checkbtn-line {
  display: block;
}

.d-flex.custom-control{
  padding-left: 0 !important;
}

.inline-checkbox.custom-control.custom-checkbox > label.custom-control-label {
  line-height: inherit;
}

.check-papel {
  max-width: 80%;
}

.min-height-300 {
  min-height: 300px !important;
}
</style>
