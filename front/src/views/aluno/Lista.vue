<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros(), filtrar()">Filtro Rápido</div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2, limparFiltros(), filtrar()">Avançado</div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2">
          <div class="form-group row mb-0">

            <b-col md="4">
              <label v-help-hint="'filtroRapido-aluno_nome_aluno'" for="nome_aluno" class="col-form-label">Aluno</label>
              <typeahead id="nome_aluno" :item-hit="setNomeAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroRapido-aluno_telefone_preferencial'" for="telefone_preferencial" class="col-form-label">Telefone(celular)</label>
              <input v-mask="['(##) #####-####']" id="telefone_preferencial" v-model="telefonePreferencial" type="text" class="form-control" @change="filtrar()">
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroRapido-aluno_situacao_aluno'" for="situacao_aluno" class="col-form-label">Situação</label>
              <g-select id="situacao_aluno"
                        v-model="aluno_situacao"
                        :options="situacaoFiltro"
                        label="descricao"
                        track-by="value"
                        @input="filtrar()"
              />
            </b-col>
          </div>
        </form>
      </b-collapse>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaRapida=false, buscaAvancada=true ,filtrar()">
          <div class="form-group row mb-0">

            <b-col md="4">
              <label v-help-hint="'filtroAvancado-aluno_busca_cpf'" for="busca_cpf" class="col-form-label">Cpf</label>
              <typeahead v-mask="['###.###.###-##']" id="busca_cpf" :item-hit="setCpfAluno" masked source-path="/api/pessoa/buscar"/>
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroAvancado-aluno_sexo'" for="busca_sexo" class="col-form-label">Sexo</label>
              <g-select id="busca_sexo"
                        :select="setSexo"
                        :value="aluno_sexo"
                        :options="listaSexo"
                        label="descricao"
                        track-by="value"
              />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroAvancado-aluno_estado_civil'" for="busca_estado_civil" class="col-form-label">Estado Civil</label>
              <g-select id="busca_estado_civil"
                        :select="setEstadoCivil"
                        :value="aluno_estado_civil"
                        :options="listaEstadoCivil"
                        label="descricao"
                        track-by="value"
              />
            </b-col>

            <b-col md="6">
              <label v-help-hint="'filtroAvancado-aluno_responsavel_financeiro'" for="busca_responsavel_financeiro" class="col-form-label">Responsável Financeiro</label>
              <typeahead id="busca_responsavel_financeiro" :item-hit="setResponsavelFinanceiro" source-path="/api/pessoa/buscar_nome_contato" key-name="pessoa.nome_contato" />
            </b-col>

            <b-col md="6">
              <label v-help-hint="'filtroAvancado-aluno_responsavel_didatico'" for="busca_responsavel_didatico" class="col-form-label">Responsável Didático</label>
              <typeahead id="busca_responsavel_didatico" :item-hit="setResponsavelDidadtico" source-path="/api/pessoa/buscar_nome_contato" key-name="pessoa.nome_contato" />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroAvancado-aluno_classificacao'" for="busca_classificacao" class="col-form-label">Classificação Aluno</label>
              <g-select
                id="busca_classificacao"
                :options="listaClassificacaoAluno"
                v-model="classificacaoAluno"
                label="nome"
                track-by="id"
              />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroAvancado-aluno_curso'" for="busca_aluno_curso" class="col-form-label">Curso</label>
              <g-select
                id="busca_aluno_curso"
                :options="listaCurso"
                v-model="cursoAluno"
                label="descricao"
                track-by="id"
              />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroAvancado-aluno_emancipado'" for="busca_emancipado" class="col-form-label">Emancipado</label>
              <div>
                <b-form-checkbox-group id="busca_emancipado" v-model="emancipado" :options="situacaoEmancipado" buttons button-variant="cinza" name="situacao_filtro" class="checkbtn-line" @change="setEmancipado"/>
              </div>
            </b-col>

            <div class="col-md-6">
              <label v-help-hint="'filtroAvancado-aluno_data_cadastro'" class="col-form-label" for="data_cadastro_de" >Data cadastro</label>

              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_cadastro_de'"
                      :value="data_cadastro_de_temporario"
                      :selected="setDataCadastroDeTemporario"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_cadastro_ate'"
                      :value="data_cadastro_ate_temporario"
                      :selected="setDataCadastroAteTemporario"/>
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_cadastro_de_temporario, data_cadastro_ate_temporario)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-6">
              <label v-help-hint="'filtroAvancado-aluno_data_nascimento'" class="col-form-label" for="data_nascimento_de">Data Nascimento</label>

              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_nascimento_de'"
                      :value="data_nascimento_de_temporario"
                      :selected="setDataNascimetoDeTemporario" />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_nascimento_ate'"
                      :value="data_nascimento_ate_temporario"
                      :selected="setDataNascimetoAteTemporario"/>
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_nascimento_de_temporario, data_nascimento_ate_temporario)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

          </div>
          <button :disabled="dataFiltroInvalida(data_cadastro_de_temporario, data_cadastro_ate_temporario) || dataFiltroInvalida(data_nascimento_de_temporario, data_nascimento_ate_temporario)" type="submit" class="btn btn-cinza btn-block text-uppercase mt-3" @click="filtroAvancado=false">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead>
          <tr>
            <th data-column="p.nome_contato">Nome</th>
            <th data-column="p.cnpj_cpf">CPF</th>
            <th data-column="p.telefone_preferencial">Telefone</th>
            <th data-column="a.classificacao_aluno">Classificação</th>
            <th data-column="a.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="item in lista" :key="item.id" @dblclick="editar(item.id)">
              <td data-label="Nome">{{ item.pessoa.nome_contato }}</td>
              <td data-label="CPF">{{ item.pessoa.cnpj_cpf | formatarCPF }}</td>
              <td data-label="Telefone">{{ item.pessoa.telefone_preferencial | formatarTelefone }}</td>
              <td data-label="Classificação">
                <template v-if="item.classificacao_aluno">
                  <div>
                    <font-awesome-icon :icon="item.classificacao_aluno.icone" />
                    {{ item.classificacao_aluno.nome }}
                  </div>
                </template>
                <template v-else>--</template>
              </td>

              <td data-label="Situação">
                <PillSituation 
                    :situation="getTitle(item.situacao)" 
                    :situationClass="item.situacao.toLowerCase()" 
                    :textTooltip="getTitle(item.situacao)"
                  >
                  </PillSituation>
              </td>

              <td class="d-flex coluna-icones">
                <router-link v-b-tooltip.viewport.left.hover v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :to="`${$route.path}/atualizar/${item.id}`" class="icone-link" title="Atualizar">
                  <font-awesome-icon icon="pen" />
                </router-link>
                <b-link v-b-modal.reposicao-aula-avaliacao v-b-tooltip.viewport.left.hover v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="#" class="ml-2 disable" title="Reposição aula e avaliação" @click="alunoId = item.id"><font-awesome-icon icon="book-open"/></b-link>

                <a v-b-tooltip.left href="javascript: void(0)" class="ml-2 icone-link" title="Transferência entre turmas" @click="onClickTransferenciaTurma(item)">
                  <font-awesome-icon icon="exchange-alt" />
                </a>
                <!-- <b-link v-b-modal.lancamento-frequencia :disabled="!item.funcionario || !item.sala_franqueada || !item.turmaAulas.length" href="#" class="icone-link" title="Diário de classe" @click="turmaId = item.id"><font-awesome-icon icon="book-open" /></b-link> -->

              </td>
            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="info-btn">
        <b-btn :disabled="lista.length === 0" type="button" variant="azul" @click="gerarListaEmails()">Exportar lista de e-mails</b-btn>
      </div>

      <div class="info-label d-flex flex-column align-items-end">
        <div v-if="possuiPermissaoTotalAlunos">
          <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>
      </div>
    </div>

    <!-- Modal reposicao aula e avalicao -->
    <reposicao-aula-avaliacao :aluno-id="alunoId"/>

    <!-- Modal transferência de turma -->
    <g-transferencia-turma ref="transferenciaTurma" :aluno-id="alunoId" />

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'
import FormularioAvaliacao from '../reposicao-aula-avaliacao/Formulario'
import EventBus from '../../utils/event-bus'
import open from '../../utils/open'
import PillSituation from '../../components/PillSituation.vue'
export default {

  components: {
    'reposicao-aula-avaliacao': FormularioAvaliacao,
    PillSituation
  },

  data () {
    return {
      className: 'rapido-open',
      filtroRapido: false,
      filtroAvancado: false,
      filtroSelecionado: null,
      buscaAvancada: false,
      buscaRapida: false,
      situacaoEmancipado: [
        {value: '0', text: 'Não Emancipado'},
        {value: '1', text: 'Emancipado'}
      ],
      situacaoFiltro: [
        {value: null, descricao: 'Selecione'},
        {value: 'ATI', descricao: 'Ativo'},
        {value: 'INA', descricao: 'Inativo'},
        {value: 'TRA', descricao: 'Trancado'}
      ],
      listaEstadoCivil: [
        {value: null, descricao: 'Selecione'},
        {value: 'N', descricao: 'Não Informar'},
        {value: 'S', descricao: 'Solteiro(a)'},
        {value: 'C', descricao: 'Casado(a)'},
        {value: 'D', descricao: 'Divorciado'}
      ],
      listaSexo: [
        {value: null, descricao: 'Selecione'},
        {value: 'N', descricao: 'Não Informar'},
        {value: 'M', descricao: 'Masculino'},
        {value: 'F', descricao: 'Feminino'},
        {value: 'O', descricao: 'Outro'}
      ],
      alunoTemporario: null,
      telefonePreferencial: null,
      aluno_situacao: null,
      cpfTemporario: null,
      aluno_sexo: null,
      aluno_estado_civil: null,
      responsavelFinanceiroTemporario: null,
      responsavelDidaticoTemporario: null,
      emancipado: null,
      classificacaoAluno: null,
      cursoAluno: null,
      data_cadastro_de_temporario: '',
      data_cadastro_ate_temporario: '',
      data_nascimento_de_temporario: '',
      data_nascimento_ate_temporario: '',

      alunoId: null
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('aluno', ['estaCarregando', 'todosItensCarregados', 'totalItens', 'lista', 'variante', 'filtros']),
    ...mapState('classificacaoAlunos', {listaClassificacaoAlunoRequisicao: 'listaClassificacaoAluno'}),
    ...mapState('curso', {listaCursoRequisicao: 'lista'}),

    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaClassificacaoAluno: {
      get () {
        return [{id: null, nome: 'Selecione'}, ...this.listaClassificacaoAlunoRequisicao]
      }
    },

    listaCurso: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaCursoRequisicao]
      }
    },

    possuiPermissaoTotalAlunos: {
      get () {
      // console.log("quantidade alunos:",this.permissoes['VISUALIZAR_QUANTIDADE_ALUNOS'].possui_permissao)
      
        if (this.permissoes['VISUALIZAR_QUANTIDADE_ALUNOS'] && this.permissoes['VISUALIZAR_QUANTIDADE_ALUNOS'].possui_permissao === true) {
          return true
        } else {
          return false
        }
      }
    }

  },

  mounted () {
    this.filtrar()
    this.listarCamposSelects()
  },

  methods: {
    ...mapActions('aluno', ['listarHeader']),
    ...mapMutations('aluno', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA', 'SET_FILTRO_ALUNO', 'SET_FILTRO_TELEFONE', 'SET_FILTRO_SITUACAO',
      'SET_FILTRO_CNPJ_CPF', 'SET_FILTRO_PESSOA_SEXO', 'SET_FILTRO_PESSOA_ESTADO_CIVIL', 'SET_FILTRO_RESPONSAVEL_FINANCEIRO_PESSOA', 'SET_FILTRO_RESPONSAVEL_DIDATICO_PESSOA',
      'SET_FILTRO_EMANCIOPADO', 'SET_FILTRO_CLASSIFICACAO_ALUNO', 'SET_FILTRO_CURSO', 'SET_FILTRO_DATA_CADASTRO_INICIAL', 'SET_FILTRO_DATA_CADASTRO_FINAL',
      'SET_FILTRO_DATA_NASC_INICIAL', 'SET_FILTRO_DATA_NASC_FINAL']),

    dateToCompare: dateToCompare,

    onClickTransferenciaTurma (aluno) {
      const possuiContratoVigente = aluno.contratos.some(contrato => contrato.situacao === 'V')
      if (possuiContratoVigente) {
        this.alunoId = aluno.id
        this.$refs.transferenciaTurma.modalTransferenciaTurma = true
      } else {
        const mensagem = `${aluno.pessoa.nome_contato} não possui contratos vigentes para transferência.`
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: mensagem
        })
      }
    },

    carregarMais () {
      this.listarHeader()
    },

    editar (id) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        event.preventDefault()
        this.$router.push(`${this.$route.path}/atualizar/${id}`)
      }
    },

    getTitle (situacao) {
      // console.log(this.variante)
      return this.variante[situacao.toUpperCase()] ? this.variante[situacao.toUpperCase()].title : null
    },

    converterDadosParaLink () {
      let parametrosUrl = ''
      if (this.lista.length > 0) {
        const listaId = []
        this.lista.forEach(item => {
          listaId.push(`alunos[]=${item.id}`)
        })
        parametrosUrl = listaId.join('&')
      }
      return parametrosUrl
    },

    gerarListaEmails () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const listaIds = this.converterDadosParaLink()
      open(`/api/aluno/gerar_lista_emails?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${listaIds}`, '_blank')
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarHeader()
    },

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== ''
    },

    listarCamposSelects () {
      this.$store.commit('classificacaoAlunos/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('classificacaoAlunos/getListaClassificacaoAluno')
      this.$store.commit('curso/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('curso/listar')
    },

    setNomeAluno (value) {
      this.alunoTemporario = value
      if (this.filtroSelecionado === 1) {
        this.filtrar()
      }
    },

    setTelefone () {
      this.filtrar()
    },

    setSituacao (value) {
      this.aluno_situacao = value
      this.filtrar()
    },

    // avancado
    setCpfAluno (value) {
      this.cpfTemporario = value
    },

    setSexo (value) {
      this.aluno_sexo = value
    },

    setEstadoCivil (value) {
      this.aluno_estado_civil = value
    },

    setResponsavelFinanceiro (value) {
      this.responsavelFinanceiroTemporario = value
    },

    setResponsavelDidadtico (value) {
      this.responsavelDidaticoTemporario = value
    },

    setEmancipado (value) {
      this.emancipado = value
    },

    setClassificacaoAluno (value) {
      this.classificacaoAluno = value
    },

    setCurso (value) {
      this.cursoAluno = value
    },

    setDataCadastroDeTemporario (value) {
      this.data_cadastro_de_temporario = value
    },

    setDataCadastroAteTemporario (value) {
      this.data_cadastro_ate_temporario = value
    },

    setDataNascimetoDeTemporario (value) {
      this.data_nascimento_de_temporario = value
    },

    setDataNascimetoAteTemporario (value) {
      this.data_nascimento_ate_temporario = value
    },

    limparFiltros () {
      this.alunoTemporario = null
      this.telefonePreferencial = null
      this.aluno_situacao = null
      this.cpfTemporario = null
      this.aluno_sexo = null
      this.aluno_estado_civil = null
      this.responsavelFinanceiroTemporario = null
      this.responsavelDidaticoTemporario = null
      this.emancipado = null
      this.classificacaoAluno = null
      this.cursoAluno = null
      this.data_cadastro_de_temporario = ''
      this.data_cadastro_ate_temporario = ''
      this.data_nascimento_de_temporario = ''
      this.data_nascimento_ate_temporario = ''
    },

    limparStateAnterior () {
      this.SET_FILTRO_ALUNO(null)
      this.SET_FILTRO_TELEFONE(null)
      this.SET_FILTRO_SITUACAO(null)
      this.SET_FILTRO_CNPJ_CPF(null)
      this.SET_FILTRO_PESSOA_SEXO(null)
      this.SET_FILTRO_PESSOA_ESTADO_CIVIL(null)
      this.SET_FILTRO_RESPONSAVEL_FINANCEIRO_PESSOA(null)
      this.SET_FILTRO_RESPONSAVEL_DIDATICO_PESSOA(null)
      this.SET_FILTRO_EMANCIOPADO(null)
      this.SET_FILTRO_CLASSIFICACAO_ALUNO(null)
      this.SET_FILTRO_CURSO(null)
      this.SET_FILTRO_DATA_CADASTRO_INICIAL(null)
      this.SET_FILTRO_DATA_CADASTRO_FINAL(null)
      this.SET_FILTRO_DATA_NASC_INICIAL(null)
      this.SET_FILTRO_DATA_NASC_FINAL(null)
    },

    filtrar () {
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else if (this.filtroSelecionado === 2) {
        this.executaFiltroAvancado()
      }

      if (this.estaCarregando === false) {
        this.SET_PAGINA_ATUAL(1)
        this.SET_LISTA([])
        this.listarHeader()
      }
    },

    executaFiltroRapido () {
      let alunoId = this.alunoTemporario ? this.alunoTemporario.id : null
      let telefone = this.telefonePreferencial ? this.telefonePreferencial : null
      let situacao = this.aluno_situacao ? this.aluno_situacao.value : null

      this.SET_FILTRO_ALUNO(alunoId)
      this.SET_FILTRO_TELEFONE(telefone)
      this.SET_FILTRO_SITUACAO(situacao)
    },

    executaFiltroAvancado () {
      let cnpjCpf = this.cpfTemporario ? this.cpfTemporario.cnpj_cpf : null
      let sexo = this.aluno_sexo ? this.aluno_sexo.value : null
      let estadoCivil = this.aluno_estado_civil ? this.aluno_estado_civil.value : null
      let responsavelFinanceiroId = this.responsavelFinanceiroTemporario ? this.responsavelFinanceiroTemporario.id : null
      let responsavelDidaticoId = this.responsavelDidaticoTemporario ? this.responsavelDidaticoTemporario.id : null
      let situacao = this.emancipado ? this.emancipado : null
      let classificacaoAlunoId = this.classificacaoAluno ? this.classificacaoAluno.id : null
      let cursoId = this.cursoAluno ? this.cursoAluno.id : null

      let dataCadastroIni = (this.data_cadastro_de_temporario ? beginOfDay(this.data_cadastro_de_temporario) : null)
      let dataCadastroFim = (this.data_cadastro_ate_temporario ? endOfDay(this.data_cadastro_ate_temporario) : null)
      let dataNascIni = (this.data_nascimento_de_temporario ? beginOfDay(this.data_nascimento_de_temporario) : null)
      let dataNascFim = (this.data_nascimento_ate_temporario ? endOfDay(this.data_nascimento_ate_temporario) : null)

      this.SET_FILTRO_CNPJ_CPF(cnpjCpf)
      this.SET_FILTRO_PESSOA_SEXO(sexo)
      this.SET_FILTRO_PESSOA_ESTADO_CIVIL(estadoCivil)
      this.SET_FILTRO_RESPONSAVEL_FINANCEIRO_PESSOA(responsavelFinanceiroId)
      this.SET_FILTRO_RESPONSAVEL_DIDATICO_PESSOA(responsavelDidaticoId)
      this.SET_FILTRO_EMANCIOPADO(situacao)
      this.SET_FILTRO_CLASSIFICACAO_ALUNO(classificacaoAlunoId)
      this.SET_FILTRO_CURSO(cursoId)
      this.SET_FILTRO_DATA_CADASTRO_INICIAL(dataCadastroIni)
      this.SET_FILTRO_DATA_CADASTRO_FINAL(dataCadastroFim)
      this.SET_FILTRO_DATA_NASC_INICIAL(dataNascIni)
      this.SET_FILTRO_DATA_NASC_FINAL(dataNascFim)
    }

  }
}
</script>

<style scoped>
[data-label='Situação'] span {
  margin-left: 1.5rem;
}

.turma-origem-table {
  min-height: 85px !important;
}
.turma-origem-table .table-borderless.table-hover tbody tr:hover {
  background-color: #ffffff;
}

.body-card-list:not(:last-child) {
  border-bottom-width: 0;
  padding-bottom: 0;
}

td.coluna-icones{
  padding-right: 5rem!important;
}
</style>
