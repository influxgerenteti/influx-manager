<!-- eslint-disable no-constant-condition -->
<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limpaFiltros()">Filtro Rápido</div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2, limpaFiltros()">Avançado</div>
        </div>
        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/cadastros/interessados/adicionar" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false, filtrar()">
          <div class="form-group row mb-0">
            <div class="col-md-3">
              <label for="nome_filtro_rapido" class="col-form-label">Nome ou Telefone</label>
              <typeahead id="nome1" ref="nome1" v-model="nomeFiltroRapido" :key-name="['nome', 'telefone_contato']" :item-hit="setNome" :tempo="600" source-path="/api/interessado/buscar-nome-telefone" selected-key="nome"/>
            </div>
            <div class="col-md-3">
              <label for="situacao_interessado_rapido" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group
                  id="situacao_interessado_rapido"
                  v-model="filtros.situacaoSelecionada" 
                  :options="situacaoOpcoes"
                  :select="setSituacao"
                  buttons
                  :class="{ 'readonly': estaCarregando }"
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="situacao_interessado_rapido"
                  @change="setSituacao"
                />
              </div>
            </div>
          </div>
        </form>
      </b-collapse>
      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaAvancada = true, filtrar()">
          <div class="form-group row mb-0">
            <div class="col-md-3">
              <label for="nome_filtro_avancado" class="col-form-label">Nome ou Telefone</label>
              <typeahead id="nome_filtro_avancado" ref="nome_filtro_avancado" 
              v-model="filtros.nome" :key-name="['nome', 'telefone_contato']" 
              :item-hit="setNome"
              :tempo="600" source-path="/api/interessado/buscar-nome-telefone" selected-key="nome"/>
            </div>

            <div class="col-md-3">
              <label for="idade_filtro_rapido" class="col-form-label">Idade</label>
              <g-select
                id="idade_filtro_rapido"
                v-model="filtros.idade"
                :select="setIdadeFiltroRapido"
                :options="opcoesIdade"
                class="multiselect-truncate"
                label="text"
                track-by="id"/>
            </div>
            <div class="col-md-3">
              <label for="idioma_filtro_rapido" class="col-form-label">Idioma</label>
              <g-select
                id="idioma_filtro_rapido"
                v-model="filtros.idioma"
                :select="setIdiomaFiltroRapido"
                :options="listaIdiomas"
                class="multiselect-truncate valid-input"
                label="descricao"
                track-by="id" />
            </div>
            <div class="col-md-3">
              <label for="periodo_pretendido_filtro_rapido" class="col-form-label">Período pretendido</label>
              <g-select
                id="periodo_pretendido_filtro_rapido"
                v-model="filtros.periodoPretendido"
                :select="setPeriodoPretendido"
                :options="listaPeriodoPretendido"
                class="multiselect-truncate valid-input"
                label="descricao"
                track-by="id" />
            </div>
          </div>
          <div class="form-group row mb-0">
            <div class="col-md-3">
              <label for="email_filtro_avancado" class="col-form-label">Email</label>
              <input id="email_filtro_avancado" v-model="filtros.email"  @input="setEmail" type="text" class="form-control" maxlength="150" >
            </div>
            <div class="col-md-3">
              <label for="consultor_funcionario_filtro_rapido" class="col-form-label">Consultor</label>
              <g-select
                id="consultor_funcionario_filtro_rapido"
                v-model="filtros.consultor"
                :select="setConsultorFiltroRapido"
                :options="listaFuncionarios"
                class="multiselect-truncate valid-input"
                label="apelido"
                track-by="id" />
            </div>
            <div class="col-md-3">
              <label for="etapa_do_funil_filtro_rapido" class="col-form-label">Etapa do funil</label>
              <g-select
                id="etapa_do_funil_filtro_rapido"
                v-model="filtros.etapaFunilFiltroRapido"
                :select="setEtapaFunil"
                :options="listaWorkflow"
                class="multiselect-truncate valid-input"
                label="descricao"
                track-by="id" />
            </div>
            <div class="col-md-3">
              <label for="motivo_matricula_perdida_filtro_rapido" class="col-form-label">Motivo matrícula perdida</label>
              <g-select
                id="motivo_matricula_perdida_filtro_rapido"
                v-model="filtros.motivoMatriculaPerdidaFiltroRapido"
                :select="setMotivoMatriculaPerdida"
                :options="listaMotivoMatriculaPerdida"
                class="multiselect-truncate valid-input"
                label="descricao"
                track-by="id" />
            </div>
          </div>

          <div class="form-group row mb-2">
            <div class="col-md-3">
              <label for="tipo_lead_filtro_rapido" class="col-form-label">Tipo de contato</label>
              <div class="d-block">
                <b-form-radio-group
                  id="tipo_lead_filtro_rapido"
                  v-model="tipoLead" 
                  :options="tipoLeadOpcoes"
                  :select="setTipoLeadOpcoes"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="tipo_lead_filtro_rapido"
                  @change="setTipoLead"
                />
              </div>
            </div>

           
            <div class="col-md-3">
              <label for="forma_contato_filtro_rapido" class="col-form-label">Forma do contato</label>
              <g-select
                id="forma_contato_filtro_rapido"
                v-model="filtros.formaContatoFiltroRapido"
                :select="setFormaContato"
                :options="listaFormaContato"
                :disabled="setFormaContato.length === 0 || setFormaContato.length === 2"
                :label="tipoLead[0] === 'A' ? 'descricao' : 'nome'"
                class="multiselect-truncate"
                track-by="id"/>
            </div>
            <div class="col-md-3">
              <label for="situacao_interessado" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group
                  id="situacao_interessado"
                  v-model="filtros.situacaoSelecionada" 
                  :options="situacaoOpcoes"
                  :select="setSituacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="situacao_interessado"
                  @change="setSituacao"
                />
              </div>
            </div>
          </div>
          <div class="form-group row mb-0">
            <div class="col-md-6">
              <label class="col-form-label">Data de cadastro</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                    :element-id="'data_cadastro_de_temporario'" 
                    v-model="data_cadastro_de" 
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
                    v-model="data_cadastro_ate" 
                    :selected="setDataCadastroAteTemporario"/>
                  </div>
                </div>
              </div>
              <div v-if="dateToCompare(data_cadastro_de) > dateToCompare(data_cadastro_ate) && data_cadastro_ate !== ''" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>
            <div class="col-md-6">
              <label class="col-form-label">Validade da promoção</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker 
                    :element-id="'validade_promocao_de'" 
                    v-model="data_validade_promocao_de"
                    :selected="setDataValidadePromocaoDe"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker :element-id="'data_validade_promocao_ate'" v-model="data_validade_promocao_ate" :selected="setDataValidadePromocaoAte"/>
                  </div>
                </div>
              </div>
              <div v-if="dateToCompare(data_validade_promocao_de) > dateToCompare(data_validade_promocao_ate) && data_validade_promocao_ate !== ''" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>
          </div>

          <div class="form-group row mb-0">
            <div class="col-md-6">
              <label for="grau_interesse" class="col-form-label">Grau de Interesse</label>
              <div class="d-block">
                <b-form-checkbox-group
                  id="grau_interesse"
                  v-model="grauInteresseSelecionado"
                  :options="opcoesGrausInteresse"
                  :select="setGrauInteresse"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="grau-interesse"
                  @change="setGrauInteresse"
                />
              </div>
            </div>
          </div>

          <h6 class="title-module mb-0">Próximo contato</h6>
          <div class="form-group row mb-0">

            <div class="col-md-6">
              <label class="col-form-label">Data</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker 
                    :element-id="'data_proximo_contato_de'" 
                    v-model="data_proximo_contato_de" 
                    :selected="setDataProximoContatoDeTemporario"/>

                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker 
                    :element-id="'data_proximo_contato_ate'" 
                    v-model="data_proximo_contato_ate" 
                    :selected="setDataProximoContatoAteTemporario"/>
                  </div>
                </div>
              </div>
              <div v-if="dateToCompare(data_proximo_contato_de) > dateToCompare(data_proximo_contato_ate) && data_proximo_contato_ate !== ''" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-6">
              <label class="col-form-label">Período</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <input v-mask="'##:##'" 
                    v-model="horario_proximo_contato_de"
                     @change="setHorarioDe"  
                     :class="horario_proximo_contato_de.length === 5 && $v.horario_proximo_contato_de.$invalid ? 'is-invalid' : null" 
                     type="text" 
                     class="form-control" 
                     maxlength="5" >
                    <div class="invalid-feedback">Horário inválido</div>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <input v-mask="'##:##'" v-model="horario_proximo_contato_ate" @input="setHorarioAte" :class="horario_proximo_contato_ate.length === 5 && $v.horario_proximo_contato_ate.$invalid ? 'is-invalid' : null" type="text" class="form-control" maxlength="5" >
                    <div class="invalid-feedback">Horário inválido</div>
                  </div>
                </div>
              </div>
              <div v-if="horaAMaiorHoraB(horario_proximo_contato_de, horario_proximo_contato_ate) && horario_proximo_contato_ate !== ''" class="floating-message bg-danger">
                Hora inicial deve ser menor que a hora final!
              </div>
            </div>

          </div>
          <b-form-checkbox
          id="check-manter-filtro"
          v-model="filtros.manterFiltros"
          :unchecked-value="false"
          name="check-limpar-filtros"
          @change="atualizarLimparFiltros"
        >
              Manter dados do filtro
        
            </b-form-checkbox>
      
          <button :disabled="dataFiltroInvalida() || horarioFiltroInvalido()" type="submit" class="btn btn-cinza btn-block text-uppercase mt-3" @click="filtroAvancado=false">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="it.nome">Nome</th>
            <th data-column="it.telefone_contato, it.telefone_secundario" class="size-115">Telefone</th>
            <th data-column="cu.descricao" class="size-150">Curso</th>
            <th class="size-60">Idioma</th>
            <th data-column="it.tipo_lead" class="size-150">Tipo de Contato</th>
            <th v-b-tooltip.top title="Próximo contato" data-column="agc.data_agendamento" class="d-block text-truncate size-150">Próximo contato</th>
            <th v-b-tooltip.top title="Data Cadastro" data-column="it.data_cadastro" class="d-block text-truncate size-175">Data Cadastro</th>
            <th class="size-115">Etapa Funil</th>
            <th data-column="it.situacao" class="size-75">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()"> 
            <div v-if="estaCarregando && !estaCarregandoScroll" class="form-loading">
              <load-placeholder :loading="estaCarregando && !estaCarregandoScroll" />
            </div>
            <div v-if="!listaItens.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <tr v-for="item in listaItens" :key="item.id" @dblclick="alterarInteressado(item)" :item="item">
              <td data-label="Nome">
               <!-- {{ item.nome }} {{ item.telefone_contato ? item.telefone_contato.substr(-4) : (item.telefone_secundario ? '('+item.telefone_secundario.substr(-4) +')' : '') }}</td> -->
                {{ item.nome }} </td>
              <td data-label="Telefone" class="size-115">
                {{ item | getTelefone }}
              </td>
              <td v-b-tooltip.top :title="( item.curso ? item.curso.descricao : '' )" data-label="Curso" class="truncate size-150">
                <span>{{ item.curso ? item.curso.descricao : '' }}</span>
              </td>
              <td data-label="Idioma" class="size-60">
                {{ item.idiomas.map(i => i.descricao).join(', ') }}
              </td>         

              <td data-label="Idade" class="size-60">
                {{ item.idade }}
              </td>

              <td data-label="Tipo contato" class="size-150">
                {{ item.tipo_lead === 'A' ? 'Ativo' : (item.tipo_lead === 'R' ? 'Receptivo' : '') }}
              </td>
              <td data-label="Próximo contato" class="text-truncate size-150">
                {{ formataAgendaComercial(item.agendaComerciais) }}
              </td>
              <td data-label="Data Cadastro" class="text-truncate size-150">
                {{ formatDataCadastro(item.data_cadastro)}}
              </td>
              <td data-label="Etapa Funil" class="size-150">
                {{ item.workflow ? item.workflow.descricao : '' }}
              </td>
              <td data-label="Situação" class="size-60">
                {{ item.situacao ? retornaSituacao(item.situacao) : '' }}
              </td>
              <td class="d-flex coluna-icones">
             
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="javascript:void(0)" title="Atualizar" class="icone-link" @click.prevent="alterarInteressado(item)">
                  <font-awesome-icon icon="pen" />
                </a>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
        <tfoot>
          <div v-if="estaCarregandoScroll" class="form-loading scroll-loading" >
            <load-placeholder :loading="estaCarregandoScroll" />
          </div>
        </tfoot>
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
import {validateHour} from '../../utils/validators'
import {beginOfDay, endOfDay, dateToCompare, horaAMaiorHoraB} from '../../utils/date'

export default {
  name: 'ListaInteressados',
  filters: {
    getTelefone (item) {
      if (item.telefone_contato) {
        return item.telefone_contato
      }

      if (item) {
        return item.telefone_secundario
      }
    },
   
  },
 
  data () {
    return {
           
      className: 'rapido-open',
      estaCarregandoScroll: false,
      nomeFiltroRapido: '',
      emailFiltroRapido: '',
      telefoneFiltroRapido: '',
      idadeFiltroRapido: '',
      formaContatoFiltroRapido: '',
      data_proximo_contato_de_temporario: '',
      data_proximo_contato_ate_temporario: '',
      data_validade_promocao_de: '',
      data_validade_promocao_ate: '',
      data_proximo_contato_de: '',
      data_proximo_contato_ate: '',
      horario_proximo_contato_de: '',
      horario_proximo_contato_ate:'',
      data_cadastro_de_temporario_temporario: '',
      data_cadastro_de: '',
      data_cadastro_ate: '',
      periodoPretendidoFiltroRapido: '',
      etapaFunilFiltroRapido: '',
      motivoMatriculaPerdidaFiltroRapido: '',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: false,
      filtroSelecionado: null,
      idiomaFiltroRapido: {id: null, descricao: 'Selecione'},
      consultorFiltroRapido: {id: null, apelido: 'Selecione'},
      tipoLead: [],
      situacaoSelecionada: [],
      grauInteresseSelecionado: [],
      opcoesGrausInteresse: [
        {value: 'L', text: 'Lead'},
        {value: 'I', text: 'Interessado'},
        {value: 'H', text: 'Hotlist'}
      ],
      tipoLeadOpcoes: [
        {text: 'Ativo', value: 'A'},
        {text: 'Receptivo', value: 'R'}
      ],
      situacaoOpcoes: [
        {text: 'Aberto', value: 'A'},
        {text: 'Convertido', value: 'C'},
        {text: 'Perdido', value: 'P'}
      ],
      opcoesIdade: [
        {id: '', text: 'Selecione', value: ''},
        {id: 1, text: '6 anos', value: 6},
        {id: 2, text: '7 anos', value: 7},
        {id: 3, text: '8 anos', value: 8},
        {id: 4, text: '9 anos', value: 9},
        {id: 5, text: '10 anos', value: 10},
        {id: 6, text: '11 anos', value: 11},
        {id: 7, text: '12 anos', value: 12},
        {id: 8, text: '13 ou 14 anos', value: 13},
        {id: 9, text: '15 ou 16 anos', value: 15},
        {id: 10, text: ' Acima de 17 anos', value: 17}
      ],
      listaPeriodoPretendido: [
        {id: '', descricao: 'Selecione', value: ''},
        {id: 1, descricao: 'Manhã', value: 'M'},
        {id: 2, descricao: 'Tarde', value: 'T'},
        {id: 3, descricao: 'Noite', value: 'N'},
        {id: 4, descricao: 'Sábado', value: 'S'}
      ]
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('interessados', {listaItens: 'lista', estaCarregando: 'estaCarregando', filtros: 'filtros', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    ...mapState('idioma', {listaIdiomasRequisicao: 'lista'}),
    ...mapState('tipoContato', {listaTipoContatoReceptivo: 'lista'}),
    ...mapState('prospeccao', {listaTipoContatoAtivo: 'lista'}),
    ...mapState('motivosMatriculaPerdida', {listaMotivoMatriculaPerdidaRequisicao: 'lista'}),
    ...mapState('workflow', {workflowRequisicao: 'lista'}),
     
    listaFuncionarios: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionariosRequisicao]
      }
    },

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaIdiomas: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaIdiomasRequisicao]
      }
    },

    listaMotivoMatriculaPerdida: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaMotivoMatriculaPerdidaRequisicao]
      }
    },

    listaWorkflow: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.workflowRequisicao]
      }
    },

    listaFormaContato () {
          
      if (this.tipoLead && this.tipoLead[0] === 'A') {
   
        return [{id: null, descricao: 'Selecione'}, ...this.listaTipoContatoAtivo]
      }

      if (this.tipoLead &&  this.tipoLead[0] === 'R') {
     
        return [{id: null, nome: 'Selecione'}, ...this.listaTipoContatoReceptivo]
      }

      return []
    }
  },
 
  mounted () {
     this.selected = 0
     this.situacaoSelecionada = ['A']
     this.SET_PAGINA_ATUAL(1)
     this.filtrar()  
     this.listarCamposSelects()
  },

  validations: {
    horario_proximo_contato_de: {validateHour},
    horario_proximo_contato_ate: {validateHour}
  },
  methods: {
    ...mapActions('interessados', {listarItems: 'listar'}),
    ...mapActions('idioma', {listarIdiomas: 'listar'}),
    ...mapMutations('interessados', ['SET_PAGINA_ATUAL', 'SET_MANTER_FILTROS', 'SET_LISTA', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_FILTRO_NOME',
      'SET_FILTRO_TELEFONE', 'SET_FILTRO_IDADE', 'SET_FILTRO_IDIOMA', 'SET_FILTRO_CONSULTOR', 'SET_FILTRO_DATA_CADASTRO_ATE_TEMPORARIO','SET_FILTRO_GRAU_INTERESSE', 'SET_FILTRO_DATA_CADASTRO_DE_TEMPORARIO', 'SET_FILTRO_DATA_CADASTRO_ATE',  
      'SET_FILTRO_VALIDADE_PROMOCAO_DE', 'SET_FILTRO_VALIDADE_PROMOCAO_ATE', 'SET_FILTRO_PROXIMO_CONTATO_HORARIO_DE', 'SET_FILTRO_PROXIMO_CONTATO_HORARIO_ATE',
      'SET_FILTRO_TIPO_LEAD','SET_FILTRO_DATA_PROXIMO_CONTATO_DE_TEMPORARIO', 'SET_FILTRO_DATA_PROXIMO_CONTATO_ATE_TEMPORARIO', 'SET_FILTRO_INTERESSADO', 'SET_FILTRO_EMAIL', 'SET_FILTRO_TIPO_PROSPECCAO', 'SET_FILTRO_TIPO_CONTATO', 'SET_FILTRO_PERIODO_PRETENDIDO', 'SET_FILTRO_ETAPA_FUNIL', 'SET_FILTRO_MOTIVO_MATRICULA_PERDIDA',
      'SET_FILTRO_SITUACAO', 'SET_ORDER_BY']),

    dateToCompare: dateToCompare,
    horaAMaiorHoraB: horaAMaiorHoraB,
    
    atualizarLimparFiltros(valor) {
      this.SET_MANTER_FILTROS(valor)
    },


   carregarMais () {
      this.estaCarregandoScroll = true;
      this.listarItems().then((res) => {
        this.estaCarregandoScroll = false;
      })
    },

   

    setFormaContato (value) {
      this.SET_FILTRO_TIPO_CONTATO(null)
      this.SET_FILTRO_TIPO_PROSPECCAO(null)
      this.formaContatoFiltroRapido = value
    },

    formataAgendaComercial (agendaComerciais) {
      if (agendaComerciais !== undefined) {
        if (agendaComerciais.length > 0) {
          let dataHoraProximoContatoString = agendaComerciais[0].data_agendamento
          let dataHoraProximoContatoArray = dataHoraProximoContatoString.split('T')
          let horarioArray = dataHoraProximoContatoArray[1].split('+')
          if (horarioArray.length === 1) {
            horarioArray = dataHoraProximoContatoArray[1].split('-')
          }
          let dataArray = dataHoraProximoContatoArray[0].split('-')
          return dataArray[2] + '/' + dataArray[1] + '/' + dataArray[0] + ' ' + horarioArray[0]
        }
      }
      return 'Não cadastrado.'
    },

   formatDataCadastro (value) {

      if (value !== undefined) {
        if (value.length > 0) {
          let dataHoraCadastroString = value
          let dataHoraCadastrotoArray = dataHoraCadastroString.split('T')
          let horarioArray = dataHoraCadastrotoArray[1].split('+')
          if (horarioArray.length === 1) {
            horarioArray = dataHoraCadastrotoArray[1].split('-')
          }
          let dataArray = dataHoraCadastrotoArray[0].split('-')
          return dataArray[2] + '/' + dataArray[1] + '/' + dataArray[0] + ' ' + horarioArray[0]
        }
      }
      return 'Não cadastrado.'
    },


    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItems()
    },

    dataFiltroInvalida () {
      let dataProximoContatoInvalida = dateToCompare(this.data_proximo_contato_de_temporario) > dateToCompare(this.data_proximo_contato_ate_temporario) && this.data_proximo_contato_ate_temporario !== ''
      let dataCadastroInvalida = dateToCompare(this.data_cadastro_de_temporario_temporario) > dateToCompare(this.data_cadastro_ate) && this.data_cadastro_ate !== ''
      return (dataProximoContatoInvalida === true) || (dataCadastroInvalida === true)
    },

    horarioFiltroInvalido () {
      return horaAMaiorHoraB(this.horario_proximo_contato_de, this.horario_proximo_contato_ate)
    },

    retornaSituacao (situacao) {
      let sitDesc = ''
      switch (situacao) {
        case 'A':
          sitDesc = 'Aberto'
          break
        case 'I':
          sitDesc = 'Inativo'
          break
        case 'C':
          sitDesc = 'Convertido'
          break
        case 'P':
          sitDesc = 'Perdido'
          break
      }
      return sitDesc
    },

    setHorarioDe(value){
      console.log(value)
    this.SET_FILTRO_PROXIMO_CONTATO_HORARIO_DE(value.target._value)
    },
    setHorarioAte(value){
    this.SET_FILTRO_PROXIMO_CONTATO_HORARIO_ATE(value)
    },
    setDataProximoContatoDeTemporario (value) {
      this.SET_FILTRO_DATA_PROXIMO_CONTATO_DE_TEMPORARIO(value)
    },

    setDataProximoContatoAteTemporario (value) {
      this.SET_FILTRO_DATA_PROXIMO_CONTATO_ATE_TEMPORARIO(value)
    },

    setDataCadastroDeTemporario (value) {
       this.SET_FILTRO_DATA_CADASTRO_DE_TEMPORARIO(value)
    },

    setDataCadastroAteTemporario (value) {
        this.SET_FILTRO_DATA_CADASTRO_ATE_TEMPORARIO(value)
    },

    setDataValidadePromocaoDe (value) {
      this.SET_FILTRO_VALIDADE_PROMOCAO_DE(value)
    },

    setDataValidadePromocaoAte (value) {
      this.SET_FILTRO_VALIDADE_PROMOCAO_ATE(value)
    },

    alterarInteressado (interessadoObj) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.SET_ITEM_SELECIONADO(interessadoObj)
        this.SET_ITEM_SELECIONADO_ID(interessadoObj.id)
        this.$router.push(`${this.$route.path}/atualizar/${interessadoObj.id}`)
      }
    
    },

    /* direcionarFollowup (interessadoObj) {
      this.SET_ITEM_SELECIONADO(interessadoObj)
      this.SET_ITEM_SELECIONADO_ID(interessadoObj.id)
      this.$router.push(`/cadastros/interessados/followup/${interessadoObj.id}`)
    }, */

    listarCamposSelects () {
    
      if(!this.filtros.manterFiltros){
      this.$store.commit('tipoContato/SET_PAGINA_ATUAL', 1)
      this.$store.commit('tipoContato/SET_LISTA', [])
      this.$store.dispatch('tipoContato/listar')

      this.$store.commit('prospeccao/SET_PAGINA_ATUAL', 1)
      this.$store.commit('prospeccao/SET_LISTA', [])
      this.$store.dispatch('prospeccao/listar')

      this.$store.commit('workflow/SET_PAGINA_ATUAL', 1)
      this.$store.commit('workflow/SET_LISTA', [])
      this.$store.dispatch('workflow/listar')

      this.$store.commit('motivosMatriculaPerdida/SET_PAGINA_ATUAL', 1)
    
      this.$store.commit('motivosMatriculaPerdida/SET_LISTA', [])
      this.$store.dispatch('motivosMatriculaPerdida/listar')

      this.$store.commit('idioma/SET_PAGINA_ATUAL', 1)
      this.listarIdiomas()

      this.$store.dispatch('funcionario/buscarConsultores')}

    },

    setTipoLead (value) {
       this.SET_FILTRO_TIPO_LEAD(value)
    },

    setGrauInteresse (value) {
      this.SET_FILTRO_GRAU_INTERESSE(value)
    },

    setSituacao (value) {
      this.situacaoSelecionada = value
      if (this.filtroSelecionado === 1) {
        this.SET_FILTRO_SITUACAO(value)
        this.filtrar()
      }
      this.SET_FILTRO_SITUACAO(value)
    },
    
    setNome(value) {
      this.nomeFiltroRapido = value === null ? {id: null} : value
      if (this.filtroRapido) {
        this.filtrar()
      }
      this.SET_FILTRO_NOME(value)
     },

    setMotivoMatriculaPerdida (value) {
      this.SET_FILTRO_MOTIVO_MATRICULA_PERDIDA(value)   
    },

    setIdiomaFiltroRapido (value) {
      this.SET_FILTRO_IDIOMA(value)
    },

    setTipoLeadOpcoes(value){
   
      this.SET_FILTRO_TIPO_LEAD([value])
    },

    setConsultorFiltroRapido (value) {
  
      this.SET_FILTRO_CONSULTOR(value)
    },

    setTipoContato(value){
      this.SET_FILTRO_TIPO_CONTATO(value)
    },

    setIdadeFiltroRapido (value) {
     this.idadeFiltroRapido = value.id === null ? this.opcoesIdade[0] : value
     this.SET_FILTRO_IDADE(value)
    },


    setPeriodoPretendido (value) {
    //  this.periodoPretendidoFiltroRapido = value
     this.SET_FILTRO_PERIODO_PRETENDIDO(value)
    },

    setEmail(value){
    this.SET_FILTRO_EMAIL(value)
    },


    setEtapaFunil (value) {
      this. SET_FILTRO_ETAPA_FUNIL(value)
    },

   
    limparStateAnterior () {
      if(!this.filtros.manterFiltros){
      // this.SET_FILTRO_NOME(null)
      this.SET_FILTRO_EMAIL(null)
      this.SET_FILTRO_INTERESSADO(null)
      this.SET_FILTRO_TELEFONE(null)
      this.SET_FILTRO_IDADE(null)
      this.SET_FILTRO_IDIOMA(null)
      this.SET_FILTRO_CONSULTOR(null)
      this.SET_FILTRO_GRAU_INTERESSE(null)
      this.SET_FILTRO_DATA_CADASTRO_DE_TEMPORARIO(null)
      this.SET_FILTRO_DATA_CADASTRO_ATE_TEMPORARIO(null)
      // this.SET_FILTRO_PROXIMO_CONTATO_DE(null)
      // this.SET_FILTRO_PROXIMO_CONTATO_ATE(null)
      this.SET_FILTRO_VALIDADE_PROMOCAO_DE(null)
      this.SET_FILTRO_VALIDADE_PROMOCAO_ATE(null)
      this.SET_FILTRO_PROXIMO_CONTATO_HORARIO_DE(null)
      this.SET_FILTRO_PROXIMO_CONTATO_HORARIO_ATE(null)
      this.SET_FILTRO_TIPO_LEAD(null)
      this.SET_FILTRO_SITUACAO(null)
      this.SET_FILTRO_TIPO_PROSPECCAO(null)
      this.SET_FILTRO_TIPO_CONTATO(null)
      this.SET_FILTRO_PERIODO_PRETENDIDO(null)
      this.SET_FILTRO_ETAPA_FUNIL(null)
      this.SET_FILTRO_MOTIVO_MATRICULA_PERDIDA(null)}
    
    },

    limpaFiltros () {

      if( !this.filtros.manterFiltros){
      this.nomeFiltroRapido = ''
      this.emailFiltroRapido = ''
      this.telefoneFiltroRapido = ''
      this.idadeFiltroRapido = this.opcoesIdade[0]
      this.data_proximo_contato_de = ''
      this.data_proximo_contato_ate = ''
      this.data_cadastro_de_temporario_temporario = ''
      this.data_cadastro_ate = ''
      this.data_validade_promocao_de = ''
      this.data_validade_promocao_ate = ''
      this.horario_proximo_contato_de = ''
      this.horario_proximo_contato_ate = ''
      this.idiomaFiltroRapido = {id: null, descricao: 'Selecione'}
      this.consultorFiltroRapido = {id: null, apelido: 'Selecione'}
      this.formaContatoFiltroRapido = {id: null, descricao: 'Selecione', nome: 'Selecione'}
      this.tipoLead = []
      this.grauInteresseSelecionado = []
      this.periodoPretendidoFiltroRapido = this.listaPeriodoPretendido[0]
      this.etapaFunilFiltroRapido = {id: null, descricao: 'Selecione'}
      this.motivoMatriculaPerdidaFiltroRapido = {id: null, descricao: 'Selecione'}
      }
     
    },

    executaFiltroRapido () {


      
    //   let dataCadastroIni = (this.data_cadastro_de_temporario_temporario ? beginOfDay(this.data_cadastro_de_temporario_temporario) : null)
    //   let dataCadastroFim = (this.data_cadastro_ate_temporario ? endOfDay(this.data_cadastro_ate_temporario) : null)
    //   let dataValidadePromocaoDe = (this.data_validade_promocao_de ? beginOfDay(this.data_validade_promocao_de) : null)
    //   let dataValidadePromocaoAte = (this.data_validade_promocao_ate ? endOfDay(this.data_validade_promocao_ate) : null)
    //   let dataIni = (this.data_proximo_contato_de_temporario ? beginOfDay(this.data_proximo_contato_de_temporario) : null)
    //   let dataFim = (this.data_proximo_contato_ate_temporario ? endOfDay(this.data_proximo_contato_ate_temporario) : null)
    //   let horaIni = (this.horario_proximo_contato_de.length === 2 ? this.horario_proximo_contato_de + ':00' : this.horario_proximo_contato_de)
    //   let horaFim = (this.horario_proximo_contato_ate.length === 2 ? this.horario_proximo_contato_ate + ':00' : this.horario_proximo_contato_ate)
    //   // this.SET_FILTRO_NOME(this.nomeFiltroRapido)
    //   this.SET_FILTRO_EMAIL(this.emailFiltroRapido)
    //   this.SET_FILTRO_INTERESSADO(this.nomeFiltroRapido.id)
    //   this.SET_FILTRO_TELEFONE(this.telefoneFiltroRapido)
    // //  this.SET_FILTRO_IDADE(this.idadeFiltroRapido.value)
    //   //this.SET_FILTRO_IDIOMA(this.idiomaFiltroRapido.id)
    //   //this.SET_FILTRO_CONSULTOR(this.consultorFiltroRapido.id)
    //   this.SET_FILTRO_GRAU_INTERESSE(this.grauInteresseSelecionado)
    //   this.SET_FILTRO_data_cadastro_de_temporario(dataCadastroIni)
    //   this.SET_FILTRO_DATA_CADASTRO_ATE(dataCadastroFim)
    //   this.SET_FILTRO_PROXIMO_CONTATO_DATA_DE(dataIni)
    //   this.SET_FILTRO_PROXIMO_CONTATO_DATA_ATE(dataFim)
    //   this.SET_FILTRO_VALIDADE_PROMOCAO_DE(dataValidadePromocaoDe)
    //   this.SET_FILTRO_VALIDADE_PROMOCAO_ATE(dataValidadePromocaoAte)
    //   this.SET_FILTRO_PROXIMO_CONTATO_HORARIO_DE(horaIni)
    //   this.SET_FILTRO_PROXIMO_CONTATO_HORARIO_ATE(horaFim)
    //   this.SET_FILTRO_TIPO_LEAD(this.tipoLead)
    //   this.SET_FILTRO_SITUACAO(this.situacaoSelecionada)
    //  // this.SET_FILTRO_PERIODO_PRETENDIDO(this.periodoPretendidoFiltroRapido.value)
    //   this.SET_FILTRO_ETAPA_FUNIL(this.etapaFunilFiltroRapido.id)
    //   this.SET_FILTRO_MOTIVO_MATRICULA_PERDIDA(this.motivoMatriculaPerdidaFiltroRapido.id)

    //   if (this.tipoLead === 'A') {
    //     this.SET_FILTRO_TIPO_PROSPECCAO(this.formaContatoFiltroRapido.id)
    //   }
    //   if (this.tipoLead === 'R') {
    //     this.SET_FILTRO_TIPO_CONTATO(this.formaContatoFiltroRapido.id)
    //   }
    },
     
  filtrar () {
    this.executaFiltroRapido(); 
    this.SET_PAGINA_ATUAL(1);
    this.SET_LISTA([]);
    this.listarItems();
    this.limparStateAnterior(); 
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
.size-215 {
  min-width: 215px;
}

.readonly {
  pointer-events: none;
  opacity: 0.5;
}

.form-loading.scroll-loading {
  bottom: 0;
  height: 4rem;
  top:inherit;
}
</style>
