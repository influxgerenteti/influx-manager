<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado">
      <div class="form-group row">
        <div class="col-md-4">
          <label v-help-hint="''" for="nome_funcionario" class="col-form-label">Funcionário</label>
          <g-select
            :value="filtros.funcionario"
            :select="setFuncionario"
            :options="listaFuncionario"
            class="multiselect-truncate"
            label="apelido"
            track-by="id" />
        </div>

        <div class="col-md-4">
          <label for="cargo" class="col-form-label">Cargo</label>
          <g-select
            :value="filtros.cargo"
            :select="setCargo"
            :options="listaDeCargo"
            class="multiselect-truncate"
            label="descricao"
            track-by="id" />
        </div>

        <div class="col-md-4">
          <label v-help-hint="''" for="situacao" class="col-form-label">Situação</label>
          <div class="d-block">
            <b-form-checkbox-group
              id="situacao"
              v-model="filtros.situacao"
              :options="listaSituacao"
              buttons
              button-variant="cinza"
              class="checkbtn-line"
              @change="setSituacao"
            />
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-4">
          <label v-help-hint="''" for="data_entrada_aniversario" class="col-form-label">Aniversariando entre (dia/mês)</label>
          <div class="row">
            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">De</div>
                </div>
                <!-- <g-datepicker :element-id="'data_entrada_aniversario'" :value="filtros.data_aniversario_de" :selected="setDataAniversarioDe"/> -->
                <calendar
                  :element-id="'data_entrada_aniversario'"
                  v-model="filtros.data_aniversario_de"
                  :day-end-month="true"
                />
              </div>
            </div>

            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">à</div>
                </div>
                <!-- <g-datepicker :element-id="'data_final_aniversario'" :value="filtros.data_aniversario_ate" :selected="setDataAniversarioAte"/> -->
                <calendar
                  :element-id="'data_final_aniversario'"
                  v-model="filtros.data_aniversario_ate"
                  :day-end-month="true"
                />
              </div>
            </div>
          </div>
          <div v-if="dayAndMonthToCompare(filtros.data_aniversario_de) > dayAndMonthToCompare(filtros.data_aniversario_ate) && filtros.data_aniversario_ate !== undefined" class="floating-message bg-danger">
            Data inicial deve ser menor que a data final!
          </div>
        </div>

        <div class="col-md-4">
          <label v-help-hint="''" for="data_de_cadastro" class="col-form-label">Cadastrado</label>
          <div class="row">
            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">De</div>
                </div>
                <g-datepicker :element-id="'data_entrada_cadastro'" :value="filtros.data_cadastro_de" :selected="setDataCadastroDe"/>
              </div>
            </div>

            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">à</div>
                </div>
                <g-datepicker :element-id="'data_final_cadastro'" :value="filtros.data_cadastro_ate" :selected="setDataCadastroAte"/>
              </div>
            </div>
          </div>
          <div v-if="dateToCompare(filtros.data_cadastro_de) > dateToCompare(filtros.data_cadastro_ate) && filtros.data_cadastro_ate !== ''" class="floating-message bg-danger">
            Data inicial deve ser menor que a data final!
          </div>
        </div>
      </div>

      <b-btn class="btn btn-cinza btn-block text-uppercase" @click="abrirRelatorio()">Gerar relatório</b-btn>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {beginOfDay, endOfDay, dateToCompare, dayAndMonthToCompare} from '../../utils/date'
import open from '../../utils/open'

export default {
  name: 'ListaRelatorioFuncionario',
  data () {
    return {
      listaSituacao: [
        {text: 'Ativos', value: 'A'},
        {text: 'Inativos', value: 'I'}
      ]
    }
  },
  computed: {
    ...mapState('relatorioFuncionario', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', filtros: 'filtros'}),
    ...mapState('funcionario', {listaFuncionarioRequisicao: 'lista'}),
    ...mapState('cargo', {listaCargoRequisicao: 'lista'}),

    listaFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionarioRequisicao]
      }
    },

    listaDeCargo: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaCargoRequisicao]
      }
    }
  },
  mounted () {
    this.listarCamposSelects()
  },
  methods: {
    ...mapActions('relatorioFuncionario', {listarItens: 'listar'}),
    ...mapMutations('relatorioFuncionario', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY']),
    ...mapActions('funcionario', {listarFuncionario: 'listar'}),
    ...mapActions('cargo', {listarCargo: 'listar'}),

    dateToCompare: dateToCompare,
    dayAndMonthToCompare: dayAndMonthToCompare,

    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      this.listarFuncionario()
      this.listarCargo()
    },

    setFuncionario (value) {
      this.filtros.funcionario = value
    },

    setCargo (value) {
      this.filtros.cargo = value
    },

    setSituacao (value) {
      this.filtros.situacao = value
    },

    setDataAniversarioDe (value) {
      this.filtros.data_aniversario_de = value
    },

    setDataAniversarioAte (value) {
      this.filtros.data_aniversario_ate = value
    },

    setDataCadastroDe (value) {
      this.filtros.data_cadastro_de = value
    },

    setDataCadastroAte (value) {
      this.filtros.data_cadastro_ate = value
    },

    abrirRelatorio () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const filtrosRelatorio = this.converterDadosParaLink()
      open(`/api/relatorios/funcionario?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${filtrosRelatorio}`, '_blank')
    },

    converterDadosParaLink () {
      const form = {...this.filtros}

      const dados = {
        funcionario: form.funcionario ? form.funcionario.id : null,
        cargo: form.cargo ? form.cargo.id : null,
        situacao: form.situacao.length > 0 ? form.situacao : null,
        data_aniversario_de: form.data_aniversario_de ? form.data_aniversario_de : null,
        data_aniversario_ate: form.data_aniversario_ate ? form.data_aniversario_ate : null,
        data_cadastro_de: form.data_cadastro_de ? beginOfDay(form.data_cadastro_de) : null,
        data_cadastro_ate: form.data_cadastro_ate ? endOfDay(form.data_cadastro_ate) : null
      }

      let dadosArray = []
      for (let key in dados) {
        if (dados[key] !== null) {
          dadosArray.push(`${key}=${dados[key]}`)
        }
      }

      return dadosArray.join('&')
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
