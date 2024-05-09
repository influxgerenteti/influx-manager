<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroRapidoAberto === true}" class="btn" aria-controls="filtros-avancados" aria-expanded="false" @click="toggleFiltroRapido()">Filtro Rápido</div>
        </div>
        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/cadastros/pessoa/adicionar" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-avancados" v-model="filtroRapidoAberto">
        <form class="p-2" @submit.prevent="buscarFiltroRapido()">
          <div class="form-group row mb-0">

            <!-- <div class="col-md-3"> -->
            <b-col md="3">
              <b-form-group label="Tipo Pessoa">
                <b-form-radio-group
                  id="radio_tipo_pessoa"
                  :options="opcoesTipoPessoa"
                  v-model="filtroRapido.tipoPessoa"
                  buttons
                  button-variant="cinza"
                  name="radio_tipo_pessoa"
                />
              </b-form-group>
            </b-col>
            <!-- </div> -->

            <b-col v-if="filtroRapido.tipoPessoa === 'J'" md="3">
              <label v-help-hint="'filtroRapido-pessoa_CNPJ'" for="filtro_cnpj" class="col-form-label">CNPJ</label>
              <input v-mask="['##.###.###/####-##']" id="filtro_cnpj" v-model="filtroRapido.cnpj" type="text" class="form-control">
            </b-col>

            <b-col v-if="filtroRapido.tipoPessoa === 'F'" md="3">
              <label v-help-hint="'filtroRapido-pessoa_CPF'" for="filtro_cpf" class="col-form-label">CPF</label>
              <input v-mask="['###.###.###-##']" id="filtro_cpf" v-model="filtroRapido.cpf" type="text" class="form-control">
            </b-col>

            <b-col md="3">
              <label v-help-hint="'filtroRapido-pessoa_nome'" for="pessoa_nome" class="col-form-label">Nome</label>
              <input id="pessoa_nome" v-model="filtroRapido.nome" type="text" class="form-control">
            </b-col>

            <b-col md="3">
              <label v-help-hint="'filtroRapido-pessoa_CPF_CNPJ'" for="telefone_preferencial" class="col-form-label">Telefone(celular)</label>
              <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_preferencial" v-model="filtroRapido.telefone" type="text" class="form-control">
            </b-col>

          </div>
          <button :disabled="!podeBuscarFiltroRapido" type="submit" class="btn btn-cinza btn-block text-uppercase mt-3">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table id="listaPessoas" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="p.nome_contato;p.nome_fantasia">Nome</th>
            <th data-column="p.tipo_pessoa">Tipo</th>
            <th data-column="p.telefone_preferencial">Telefone</th>
            <th data-column="p.email_preferencial">E-mail</th>
            <th data-column="p.data_cadastramento">Criado em</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaPessoas.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <tr v-for="pessoa in listaPessoas" :key="pessoa.id" :class="pessoa.negativado ? 'lista-pessoa-negativado' : ''" @dblclick="editar(pessoa)">
              <td data-label="Nome">{{ pessoa.nome_contato || pessoa.nome_fantasia }}</td>
              <td data-label="Tipo">{{ pessoa.tipo_pessoa === 'F' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</td>
              <td data-label="Telefone">{{ pessoa.telefone_preferencial | formatarTelefone }}</td>
              <td data-label="E-mail">{{ pessoa.email_preferencial || '--' }}</td>
              <td data-label="Criado em">{{ pessoa.data_cadastramento | formatarData }}</td>
              <td class="d-flex coluna-icones">
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="alterarPessoa(pessoa)">
                  <font-awesome-icon icon="pen" />
                </a>
                <!-- <a href="javascript:void(0)" title="Remover" class="icone-link text-muted" @click.prevent="inativarPessoa(pessoa.id)">
                  <font-awesome-icon icon="trash-alt" />
                </a> -->
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div></div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'ListaPessoa',
  data () {
    return {
      filtroRapido: {
        tipoPessoa: 'F',
        nome: '',
        telefone: '',
        cpf: '',
        cnpj: ''
      },
      opcoesTipoPessoa: [
        {text: 'Física', value: 'F'},
        {text: 'Jurídica', value: 'J'}
      ],
      filtroRapidoAberto: false
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('pessoas', ['listaPessoas', 'objPessoa', 'estaCarregando', 'todosItensCarregados', 'totalItens']),
    permitirCarregarMais: {
      get () {
        return !!this.listaPessoas.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },
    podeBuscarFiltroRapido: {
      get () {
        const qtdeCaracteresNome = this.filtroRapido.nome.length
        const qtdeCaracteresTelefone = this.filtroRapido.telefone.length
        const qtdeCaracteresCpf = this.filtroRapido.cpf.length
        const qtdeCaracteresCnpj = this.filtroRapido.cnpj.length
        const tipoPessoa = this.filtroRapido.tipoPessoa

        if (qtdeCaracteresNome >= 3) {
          return true
        }
        if (qtdeCaracteresTelefone >= 7) {
          return true
        }
        if (tipoPessoa === 'F' && qtdeCaracteresCpf === 14) {
          return true
        }
        if (tipoPessoa === 'J' && qtdeCaracteresCnpj === 18) {
          return true
        }
        if (qtdeCaracteresNome === 0 && qtdeCaracteresTelefone === 0 && qtdeCaracteresCpf === 0 && qtdeCaracteresCnpj === 0) {
          return true
        }
        return false
      }
    }
  },
  watch: {
    'filtroRapido.tipoPessoa': {
      handler: function (after, before) {
        if (after === 'J') {
          this.filtroRapido.cpf = ''
        } else if (after === 'F') {
          this.filtroRapido.cnpj = ''
        }
      },
      deep: true
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.setListaPessoas([])
    this.listar()
  },

  methods: {
    ...mapActions('pessoas', {listar: 'getListaPessoas', removerPessoa: 'remover'}),
    ...mapMutations('pessoas', ['setPessoa', 'setPessoaSelecionado', 'setListaPessoas', 'SET_PAGINA_ATUAL', 'SET_ORDER_BY', 'SET_FILTRO_ALUNO', 'SET_LISTA', 'SET_FILTRO_RAPIDO']),

    carregarMais () {
      this.listar()
    },

    alterarPessoa (pessoa) {
      this.setPessoa(pessoa)
      this.$router.push(`/cadastros/pessoa/atualizar/${pessoa.id}`)
    },

    editar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.$router.push(`/cadastros/pessoa/atualizar/${item.id}`)
      }
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    toggleFiltroRapido () {
      this.filtroRapidoAberto = !this.filtroRapidoAberto
    },

    buscarFiltroRapido () {
      this.SET_FILTRO_RAPIDO(this.filtroRapido)
      this.SET_PAGINA_ATUAL(1)
      this.listar()
    }
  }
}
</script>
