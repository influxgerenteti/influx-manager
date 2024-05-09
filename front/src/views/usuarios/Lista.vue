<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-4">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/configuracoes/usuario/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table id="listaUsuarios" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="u.nome">Nome</th>
            <th data-column="u.email">E-mail</th>
            <th data-column="u.data_criacao">Criado em</th>
            <th data-column="u.papels">Papel</th>
            <th data-column="u.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="usuario in listaUsuarios" :key="usuario.id" @dblclick="alterarUsuario(usuario)">
              <td data-label="Nome">{{ usuario.nome }}</td>
              <td data-label="E-mail">{{ usuario.email }}</td>
              <td data-label="Criado em">{{ usuario.data_criacao | formatarData }}</td>
              <td v-b-tooltip :title="usuario.papels.map(item=> { return item.descricao }).join(', ')" data-label="Papel" class="d-block">{{ usuario.papels.map(item=> { return item.descricao }).join(', ') }}</td>

              <td data-label="Situação">
                <div @click.prevent="inativarUsuario(usuario)">
                  <span v-if="usuario.situacao === 'A'" class="align-middle text-success" title="Ativo"><font-awesome-icon icon="check-square" /></span>
                  <span v-else class="align-middle icon-danger" title="Inativo"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

              <td class="d-flex coluna-icones">
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="alterarUsuario(usuario)">
                  <font-awesome-icon icon="pen" />
                </a>
              </td>
            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaUsuarios.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
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
import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaUsuario',

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('usuarios', ['listaUsuarios', 'estaCarregando', 'todosItensCarregados', 'totalItens']),

    permitirCarregarMais: {
      get () {
        return !!this.listaUsuarios.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },

  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listar()
  },

  methods: {
    ...mapActions('usuarios', {listar: 'getListaUsuarios', getUsuario: 'getUsuario', atualizarUsuario: 'atualizarUsuario'}),
    ...mapMutations('usuarios', ['setUsuario', 'setUsuarioSelecionado', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    mostrarUsuario (itemUsuario) {
      this.setUsuario(itemUsuario)
      this.$router.push(`/configuracoes/usuario/info/${itemUsuario.id}`)
    },

    alterarUsuario (itemUsuario) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.setUsuario(itemUsuario)
        this.$router.push(`/configuracoes/usuario/atualizar/${itemUsuario.id}`)
      }
    },

    inativarUsuario (usuario) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          const franqueadasId = []
          usuario.franqueadas.forEach((item) => {
            franqueadasId.push(item.id)
          })
          const data = {
            id: usuario.id,
            nome: usuario.nome,
            email: usuario.email,
            situacao: usuario.situacao,
            senha: usuario.senha,
            cpf: usuario.cpf,
            franqueada_padrao: usuario.franqueada_padrao,
            franqueadas: franqueadasId
          }
          data.situacao = data.situacao === 'A' ? 'I' : 'A'
          this.setUsuarioSelecionado(usuario.id)
          this.setUsuario(data)
          this.atualizarUsuario()
            .then(() => {
              usuario.situacao = data.situacao
            })
        }
      })
    }
  }
}
</script>
