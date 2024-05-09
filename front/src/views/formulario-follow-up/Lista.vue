<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/cadastros/formulario-follow-up/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table>
        <thead>
          <tr>
            <th>Formulário</th>
            <th>Tipo formulário</th>
            <th>Situação</th>
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
            <tr v-for="item in lista" :key="item.id">
              <td data-label="Formulário">{{ item.descricao_formulario }}</td>
              <td data-label="Tipo formulário">{{ item.tipo_formulario ? listaDeTipoFormulario.find(tipo => tipo.value === item.tipo_formulario).descricao : '' }}</td>
              <td data-label="Ativo">
                <div @click.prevent="alterarStatus(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" class="text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

              <td class="d-flex coluna-icones">
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="javascript:void(0)" class="icone-link" title="Atualizar" @click.prevent="alterar(item)">
                  <font-awesome-icon icon="pen" />
                </a>
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
  data () {
    return {
      listaDeTipoFormulario: [
        {id: 1, descricao: 'Contato Ativo', value: 'CA'},
        {id: 2, descricao: 'Contato Receptivo', value: 'CR'},
        {id: 3, descricao: 'Negociação de parceria', value: 'NP'},
        {id: 4, descricao: 'Nivelamento', value: 'NI'}
      ]
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('formularioFollowUp', ['lista', 'estaCarregando', 'todosItensCarregados', 'totalItens', 'objFormularioFollowUp']),
    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },

  mounted () {
    this.SET_LIMPAR_FILTROS()
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listar()
  },

  methods: {
    ...mapActions('formularioFollowUp', ['listar', 'atualizar']),
    ...mapMutations('formularioFollowUp', ['SET_ITEM_SELECIONADO', 'SET_DETALHES_ITEM_SELECIONADO', 'SET_SITUACAO', 'SET_PAGINA_ATUAL', 'LIMPAR_ITEM_SELECIONADO', 'SET_LIMPAR_FILTROS', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.SET_DETALHES_ITEM_SELECIONADO(item)
        this.$router.push(`/cadastros/formulario-follow-up/atualizar/${item.id}`)
      }
    },

    alterarStatus (itItem) {
      let msgAtivo = itItem.situacao === 'A' ? 'inativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          let item = Object.assign({}, itItem)
          this.SET_ITEM_SELECIONADO(item.id)
          this.objFormularioFollowUp.descricao_formulario = item.descricao_formulario
          this.objFormularioFollowUp.follow_up_inicial = item.follow_up_inicial
          this.objFormularioFollowUp.tipo_formulario = item.tipo_formulario
          if (item.situacao === 'A') {
            this.objFormularioFollowUp.situacao = 'I'
          } else {
            this.objFormularioFollowUp.situacao = 'A'
          }
          this.atualizar()
            .then(response => {
              itItem.situacao = this.objFormularioFollowUp.situacao
              this.LIMPAR_ITEM_SELECIONADO()
            })
        }
      }, `Deseja ${msgAtivo} o formulario "${itItem.descricao_formulario}"?`)
    }
  }
}
</script>
