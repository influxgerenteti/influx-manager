<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div class="btn filtro-selecionado" aria-controls="filtros-rapidos" aria-expanded="false">Filtro Rápido</div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">

          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label for="filtro_rapido_situacao_cobranca" class="col-form-label">Situação cobrança</label>
              <g-select id="filtro_rapido_situacao_cobranca"
                        :select="setSituacaoCobranca"
                        :value="situacaoCobrancaFiltroRapido"
                        :options="situacao"
                        label="text"
                        track-by="id"
              />
            </div>
            <div class="col-md-4">
              <label for="filtro_rapido_vencimento_entre" class="col-form-label">Vencimento entre</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_vencimento_de'"
                      :value="vencimento_de_temp"
                      :selected="setDataVencimentoDe"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_vencimento_ate'"
                      :value="vencimento_ate_temp"
                      :selected="setDataVencimentoAte"/>
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(vencimento_de_temp, vencimento_ate_temp)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>
            <div class="col-md-4">
              <label for="aluno_filtro_avacado" class="col-form-label">Aluno</label>
              <typeahead id="aluno_filtro_avacado" :value="aluno_temp" :item-hit="setAluno" :extra-param="true" source-path="/api/titulo_receber/buscar_pessoas_aluno_ou_sacado" key-name="nome_contato" />
            </div>
            <div class="col-md-4">
              <label for="geracao_filtro_avacado" class="col-form-label">Geração</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_geracao_de'"
                      :value="data_emissao_de_temp"
                      :selected="setDataEmissaoDe"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_geracao_ate'"
                      :value="data_emissao_ate_temp"
                      :selected="setDataEmissaoAte"/>
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_emissao_de_temp, data_emissao_ate_temp)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

          </div>
        </form>
      </b-collapse>

    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="">Contrato</th>
            <th data-column="">Aluno (sacado)</th>
            <th data-column="">Dt. venc</th>
            <th data-column="">Valor Título</th>
            <th data-column="">Categoria</th>
            <th data-column="">Sit. Cobrança</th>
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-if="!listaItens.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="item in listaItens" :key="item.id" @dblclick="editar(item.id)">
              <td data-label="Contrato">
                <b-form-checkbox v-if="validCheck(item)" :id="`boleto-${item.id}`" v-model="selecteds" :value="item" class="m-0"/>
                <div >
                  {{ getNumeroContrato(item) }}
                </div>
              </td>
              <td :title="nomeAlunoDisplay(item)" data-label="Aluno (Sacado)">{{ nomeAlunoDisplay(item) }}</td>
              <td data-label="Dt. venc">{{ item.data_vencimento | formatarData }}</td>
              <td data-label="Valor Título">R$ {{ item.valor | formatarNumero }}</td>
              <td data-label="Categoria">{{ item.titulo_receber.observacao }}</td>
              <td data-label="Sit. Cobrança">
                <!-- Situação -->
                {{ situacao.find(situacao => situacao.value === item.situacao_cobranca)['text'] }}
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="d-flex">
        <div class="info-btn">
          <b-btn :disabled="listaItens.length === 0 || selecteds.length === 0" type="button" variant="verde" target="_blank" @click="urlExportacao()">Exportar para banco</b-btn>
        </div>

        <div class="info-btn">
          <b-btn type="button" variant="azul" @click="uploadFile()">Importar Retorno</b-btn>
        </div>
      </div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small>
            <template v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</template>
            <template v-else>Nenhum item encontrado</template>
          </small>
        </div>

        <div>
          <small>
            <template v-if="selecteds.length >= 1">{{ selecteds.length }} ite{{ selecteds.length > 1 ? 'ns' : 'm' }} selecionado{{ selecteds.length > 1 ? 's' : '' }}</template>
            <template v-else>Nenhum item selecionado</template>
          </small>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import open from '../../utils/open'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'

export default {
  name: 'ListaImportacaoBoleto',
  filters: {
    getParcel (obs) {
      const regex = /\(\d+\/\d+\)/gm
      const matches = obs.match(regex)
      obs = ''

      if (matches) {
        return matches.join(' ')
      }
      return obs
    }
  },
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroRapido: true,
      selected: 0,
      selecteds: [],
      arquivo: {
        name: ''
      },
      vencimento_de_temp: '',
      vencimento_ate_temp: '',
      data_emissao_de_temp: '',
      data_emissao_ate_temp: '',
      aluno_temp: '',
      situacaoCobrancaFiltroRapido: '',
      situacao: [
        {id: 1, text: 'Pendente de Envio', value: 'PEN'},
        {id: 2, text: 'Enviado', value: 'ENV'},
        {id: 3, text: 'Confirmado', value: 'CON'},
        {id: 4, text: 'Rejeitado', value: 'REJ'},
        {id: 5, text: 'Recebimento', value: 'REC'},
        {id: 6, text: 'Devolvido', value: 'DEV'},
        {id: 7, text: 'Cancelado', value: 'CAN'}
      ]
    }
  },
  computed: {
    ...mapState('importacaoBoleto', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados', filtros: 'filtros'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.selected = 0
    this.selecionarSituacaoPendente()
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.filtrar()
    // this.listarCamposSelects()
  },
  methods: {
    ...mapActions('importacaoBoleto', {listarItens: 'listar', importarArquivo: 'importar'}),
    ...mapMutations('importacaoBoleto', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY', 'SET_ARQUIVO']),
    selecionarSituacaoPendente () {
      this.situacaoCobrancaFiltroRapido = this.situacao.find(sit => {
        return sit.value === 'PEN'
      })
    },

    getNumeroContrato (item) {
      if (item.titulo_receber) {
        if (item.titulo_receber.conta_receber) {
          if (item.titulo_receber.conta_receber.contrato) {
            return item.titulo_receber.conta_receber.contrato.id
          }
        }
      }
    },

    nomeAlunoDisplay (item) {
      const titulo = item.titulo_receber
      let nomeAluno = titulo.aluno ? titulo.aluno.pessoa.nome_contato + ' ' : ''
      if (!nomeAluno || item.titulo_receber.aluno.pessoa.id !== item.titulo_receber.sacado_pessoa.id) {
        nomeAluno += `(${item.titulo_receber.sacado_pessoa.nome_contato})`
      }
      return nomeAluno
    },

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== ''
    },

    setSituacaoCobranca (value) {
      this.situacaoCobrancaFiltroRapido = value
      this.filtros.situacao_cobranca = value.value
      this.filtrar()
    },

    setDataEmissaoDe (value) {
      this.data_emissao_de_temp = value
      this.filtros.data_emissao_de = value ? beginOfDay(value) : ''
      this.filtrar()
    },

    setDataEmissaoAte (value) {
      this.data_emissao_ate_temp = value
      this.filtros.data_emissao_ate = value ? endOfDay(value) : ''
      this.filtrar()
    },

    setAluno (value) {
      this.aluno_temp = value
      this.filtros.pessoa_aluno = value && value.id ? value.id : ''
      this.filtrar()
    },

    setDataVencimentoDe (value) {
      this.vencimento_de_temp = value
      this.filtros.vencimento_de = value ? beginOfDay(value) : ''
      this.filtrar()
    },

    setDataVencimentoAte (value) {
      this.vencimento_ate_temp = value
      this.filtros.vencimento_ate = value ? endOfDay(value) : ''
      this.filtrar()
    },

    salvarImportacaoRetorno () {

    },

    finalizar (action = 'cancel') {
      this.$refs.importarRetornoModal.hide()

      this.arquivo = {
        name: ''
      }
    },

    importar () {
      this.SET_ARQUIVO(this.arquivo)
      this.importarArquivo()
        .then(() => {
          this.$refs.importarRetornoModal.hide()
          this.filtrar()
        })
        .catch((error) => {
          console.log(error)
        })
    },

    uploadFile (irParaRetorno = true) {
      if (irParaRetorno) {
        this.$router.push(`${this.$route.matched[0].path}/retorno`)
        return
      }
      var input = document.createElement('input')
      input.type = 'file'

      input.onchange = e => {
        var file = e.target.files[0]
        var reader = new FileReader()
        reader.readAsText(file, 'UTF-8')

        reader.onload = readerEvent => {
          this.arquivo = file
        }
      }

      input.click()
    },

    urlExportacao () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const filtrosRelatorio = this.converterDadosParaLink()

      open(`/api/remessa/imprimir?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${filtrosRelatorio}`)

      setTimeout(() => {
        this.SET_PAGINA_ATUAL(1)
        this.listarItens()
      }, 50)
    },

    converterDadosParaLink () {
      const ids = this.selecteds.map(item => item.id)
      let dados = []

      for (let index in ids) {
        dados.push(`boletos[]=${ids[index]}`)
      }

      return dados.join('&')
    },

    carregarMais () {
      this.listarItens()
    },

    validCheck (item) {
      let list = ['PEN', 'ENV', 'CAN']
      if (list.indexOf(item.situacao_cobranca) >= 0) {
        return true
      }

      return false
    },

    setSituacao (value) {
      this.selected = value
      this.filtrar()
    },

    limparStateAnterior () {
      this.$store.commit('importacaoBoleto/LIMPAR_ITEM_SELECIONADO')
    },

    filtrar () {
      this.SET_PAGINA_ATUAL(1)
      this.listarItens(true)
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    editar (id) {

    }
  }
}
</script>
<style scoped>
span.badge {
  font-size: 95%;
}

#filtros-rapidos,
#filtros-avancados {
  transition: all .1s;
}

.filtro-avancado .form-group {
  margin-bottom: 1rem;
}

.filtro-header {
  color: #4a4a4a;
}

.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151B1E;
  background-color: #fff;
}

.filtro-avancado .input-group-text {
  border: 0;
  background-color: #E5E5E5;
}

.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

.datepicker {
  padding: 0;
}

.table-actions {
  padding-top: .5rem;
  padding-bottom: .5rem;
  color: #3e515b;
  border-top: 1px solid #EBECF0;
}
</style>
