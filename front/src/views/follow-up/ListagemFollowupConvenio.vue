<template>
  <div class="table-responsive-sm">
    <g-table :sort="sortTable">
      <thead class="text-dark">
        <tr>
          <th data-column="">Empresa</th>
          <th v-b-tooltip.viewport.down data-column="" title="Pessoa de Contato" class="d-block text-truncate">Pessoa de Contato</th>
          <th data-column="">Telefone</th>
          <th data-column="">Segmento</th>
          <th data-column="">Consultor</th>
          <th v-b-tooltip data-column="" title="Próximo contato" class="coluna-data d-block text-truncate">Próximo contato</th>
          <th data-column="">Estado atual</th>
          <th data-column="" class="coluna-situacao">Situação</th>
          <th class="coluna-icones"></th>
        </tr>
      </thead>
      <tbody ref="scroll-wrap">
        <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
          <div v-if="estaCarregando" class="form-loading">
            <load-placeholder :loading="estaCarregando" />
          </div>
          <div v-if="!listaItens.length && !estaCarregando" class="busca-vazia">
            <p>Nenhum resultado encontrado.</p>
          </div>

          <tr v-for="item in listaItens" :key="item.id" @dblclick="followup(item)">
            <td data-label="Empresa">
              {{ item.pessoa.nome_contato }}
            </td>
            <td v-b-tooltip :title="item.nome_contato" data-label="Pessoa de Contato" class="d-block text-truncate">
              {{ item.nome_contato }}
            </td>
            <td data-label="Telefone">
              {{ item.telefone_contato ? (item.telefone_contato_secundario ? item.telefone_contato + '/' + item.telefone_contato_secundario : item.telefone_contato) : '' }}
            </td>
            <td data-label="Segmento">
              {{ item.segmento_empresa_convenio ? item.segmento_empresa_convenio.descricao : '' }}
            </td>
            <td data-label="Consultor">
              {{ item.consultor_funcionario ? item.consultor_funcionario.apelido : '' }}
            </td>
            <td data-label="Próximo contato" class="coluna-data">
              {{ item.data_proximo_contato | formatarData }} {{ (item.horario_proximo_contato ? item.horario_proximo_contato : "") | formatarHoraDB }}
            </td>
            <td data-label="Estado atual">
              {{ item.etapas_convenio ? item.etapas_convenio.descricao : '' }}
            </td>
            <td data-label="Situação" class="coluna-situacao">
              {{ item.situacao ? retornaSituacao(item.situacao) : '' }}
            </td>
            <td class="d-flex coluna-icones">
              <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="javascript:void(0)" title="Atualizar" class="icone-link" @click.prevent="followup(item)">
                <font-awesome-icon icon="pen" />
              </a>
            </td>
          </tr>
        </perfect-scrollbar>
      </tbody>
    </g-table>
  </div>
</template>
<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'ListagemFollowupConvenioComponent',
  props: {
    listaItens: {
      type: [Object, Array],
      required: true,
      default: () => ([])
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('followUp', {estaCarregando: 'estaCarregando', todosItensCarregadosConvenios: 'todosItensCarregadosConvenios'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregadosConvenios
      }
    }
  },
  methods: {
    ...mapActions('followUp', {listarItens: 'listar'}),
    ...mapMutations('followUp', ['SET_LISTA_CONVENIO', 'SET_PAGINA_ATUAL', 'SET_ORDER_BY']),

    followup (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.$router.push({ path: `/cadastros/convenio/followup/${item.id}`, query: { bVeioListagemFollowup: true } })
      }
    },

    carregarMais () {
      this.listarItens()
    },

    retornaSituacao (situacao) {
      let sitDesc = ''
      switch (situacao) {
        case 'A':
          sitDesc = 'Ativo'
          break
        case 'I':
          sitDesc = 'Inativo'
          break
        case 'PV':
          sitDesc = 'Pendente Validação Franqueadora'
          break
        case 'EN':
          sitDesc = 'Em Negociação'
          break
        case 'NE':
          sitDesc = 'Negado'
          break
        case 'RF':
          sitDesc = 'Retornar Futuramente'
          break
        case 'SC':
          sitDesc = 'Sem Convênio'
          break
      }
      return sitDesc
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA_CONVENIO([])
      this.listarItens()
    }
  }
}
</script>
