<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/cadastros/curso/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead>
          <tr>
            <th data-column="curso.sigla">Sigla</th>
            <th data-column="curso.descricao">Descrição</th>
            <th data-column="i.descricao">Idioma</th>
            <th data-column="curso.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <tr v-for="item in lista" :key="item.id" @dblclick="alterar(item)">
              <td data-label="Sigla">{{ item.sigla }}</td>
              <td data-label="Descrição">{{ item.descricao }}</td>
              <td data-label="Idioma">{{ item.idioma.descricao }}</td>
              <td data-label="Situação">
                <div @click.prevent="inativar(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

              <td class="d-flex coluna-icones">
                <!-- <a href="javascript:void(0)" class="icone-link" title="Visualizar" @click.prevent="mostrar(item)">
                  <font-awesome-icon icon="eye" />
                </a> -->
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :class="item.situacao === 'A' ? null : 'disable-icon'" href="javascript:void(0)" class="icone-link" title="Atualizar" @click.prevent="alterar(item)">
                  <font-awesome-icon icon="pen" />
                </a>

                <!-- <a v-if="item.situacao === 'A'" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="inativar(item)">
                  <font-awesome-icon icon="ban" />
                </a>
                <a v-else href="javascript:void(0)" title="Ativar" class="icone-link text-success" @click.prevent="inativar(item)">
                  <font-awesome-icon icon="check-circle" />
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
import EventBus from '../../utils/event-bus'

export default {
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('curso', ['lista', 'estaCarregando', 'todosItensCarregados', 'totalItens']),
    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listar()
  },
  methods: {
    ...mapActions('curso', ['listar', 'atualizar']),
    ...mapMutations('curso', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    mostrar (item) {
      this.$router.push(`/cadastros/curso/visualizar/${item.id}`)
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          return
        }
        this.$router.push(`/cadastros/curso/atualizar/${item.id}`)
      }
    },

    inativar (item) {
      const mensagem = item.situacao === 'A' ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_ITEM_SELECIONADO_ID(item.id)

          const data = Object.assign({}, item)
          data.situacao = item.situacao === 'A' ? 'I' : 'A'
          this.SET_ITEM_SELECIONADO(data)

          this.atualizar(item)
            .then(() => {
              item.situacao = data.situacao
            })
            .catch(error => {
              console.error(error)
            })
        }
      }, `Deseja ${mensagem} ${item.descricao}?`)
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    }
  }
}
</script>
