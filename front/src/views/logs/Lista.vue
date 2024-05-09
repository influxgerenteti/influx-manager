<template>
  <div class="animated fadeIn">

    <div class="table-responsive-sm">
      <g-table id="listaUsuarios" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="logs.data">Data</th>
            <th data-column="logs.usuario">Usuário</th>
            <th data-column="logs.ip_origem">IP de Origem</th>
            <th data-column="logs.tipo_evento">Instância</th>
            <th data-column="logs.tipo_evento">Tipo de Evento</th>
            <th data-column="logs.tipo_evento">Informações</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>

        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="log in listaLogs" :key="log.id">
              <td data-label="Data">{{ log.data | formatarDataHora }}</td>

              <!-- <td data-label="Usuário">{{ log.usuario ? log.usuario.nome + ' (' + log.usuario.id + ')' : '--' }}</td> -->
              <td data-label="Usuário">{{ log.usuario }}</td>

              <td data-label="IP de Origem">{{ log.ip_origem }}</td>

              <td data-label="Instância">{{ log.id_franqueada || '--' }}</td>

              <td data-label="Tipo de Evento">{{ log.tipo_evento | tipoEvento }}</td>

              <td v-b-tooltip.top :title="formatTooltipLog(log.info_evento)" data-label="Informações" style="display: inline-block;">
               
                  {{formatLog(log.info_evento)}}
                
              </td>

              <td class="d-flex coluna-icones">
                <a href="javascript:void(0)" title="Informações" class="icone-link" @click.prevent="mostrarLog(log)">
                  <font-awesome-icon icon="eye" />
                </a>
              </td>
            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaLogs.length && !estaCarregando" class="busca-vazia">
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

export default {
  name: 'ListaLog',
  computed: {
    ...mapState('logs', ['listaLogs', 'estaCarregando', 'todosItensCarregados', 'totalItens']),
    permitirCarregarMais: {
      get () {
        return !!this.listaLogs.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listar()
  },
  methods: {
    ...mapActions('logs', {listar: 'getListaLogs', getLog: 'getLog'}),
    ...mapMutations('logs', ['setLog', 'setLogSelecionado', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listar()
    },

    mostrarLog (log) {
      this.$router.push(`/configuracoes/log/info/${log.id}`)
    },
    formatLog(log){
      return JSON.parse(log).URL
    },
    formatTooltipLog(log){
      let logTemp = JSON.parse(log);
      const objTemp = {
        Módulo: logTemp.Módulo ? logTemp.Módulo : '',
        URL: logTemp.URL ? logTemp.URL : '',
        Parâmetros: logTemp.Parâmetros ? logTemp.Parâmetros : {}
      }
      return JSON.stringify(objTemp)  
    }
  }
}
</script>
