<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': wasValidated }" class="needs-validation" novalidate @submit.prevent="submit()">
      <div v-if="estaEditando && estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="body-sector p-3">

        <b-row class="form-group">
          <b-col md="3">
            <label v-help-hint="'form-modalidade_turma'" for="modalidade_turma" class="col-form-label">Modalidade *</label>
            <template v-if="!aplicadoAlgumaTurmaAula && !listaAlunos.length">
              <g-select
                id="modalidade_turma"
                v-model="itemSelecionado.modalidade_turma"
                :disabled="!!filtrarModalidade"
                :class="wasValidated && !itemSelecionado.modalidade_turma ? 'invalid-input' : 'valid-input'"
                :options="listaModalidadesTurma"
                label="descricao"
                track-by="id" />
              <div v-if="wasValidated && $v.itemSelecionado.modalidade_turma.$invalid" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </template>
            <template v-else>
              <input id="modalidade_turma" :value="itemSelecionado.modalidade_turma && itemSelecionado.modalidade_turma.descricao" disabled type="text" class="form-control form-control-disabled">
            </template>
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-turma_curso'" for="curso" class="col-form-label">Curso *</label>
            <template v-if="!aplicadoAlgumaTurmaAula && !listaAlunos.length">
              <g-select
                id="curso"
                v-model="itemSelecionado.curso"
                :class="wasValidated && !itemSelecionado.curso ? 'invalid-input' : 'valid-input'"
                :options="opcoesCursos"
                label="descricao"
                track-by="id"
                @input="setCurso()" />
              <div v-if="wasValidated && $v.itemSelecionado.curso.$invalid" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </template>
            <template v-else>
              <input id="curso" :value="itemSelecionado.curso && itemSelecionado.curso.descricao" disabled type="text" class="form-control form-control-disabled">
            </template>
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-turma_livro'" for="livro" class="col-form-label">Livro *</label>
            <template v-if="!aplicadoAlgumaTurmaAula && !listaAlunos.length">
              <g-select
                id="livro"
                v-model="itemSelecionado.livro"
                :class="wasValidated && !itemSelecionado.livro ? 'invalid-input' : 'valid-input'"
                :options="opcoesLivros"
                label="descricao"
                track-by="id"
                @input="setLivro" />
              <div v-if="wasValidated && $v.itemSelecionado.livro.$invalid" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </template>
            <template v-else>
              <input id="livro" :value="itemSelecionado.livro.descricao" disabled type="text" class="form-control form-control-disabled">
            </template>
          </b-col>

          <b-col v-if="!ehHybrid" md="3">
            <label v-help-hint="'form-turma_intensidade'" for="intensidadeTurma" class="col-form-label">Intensidade</label>
            <input id="intensidadeTurma" v-model="intensidadeTurma" disabled type="text" class="form-control form-control-disabled">
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-turma_sala_franqueada'" for="sala_franqueada" class="col-form-label">Sala</label>
            <g-select
              id="sala_franqueada"
              v-model="salaFranqueadaObj"
              :options="listaSalasFranqueada"
              label="descricao"
              track-by="id"
              @input="setSalaFranqueada" />
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-turma_funcionario'" for="funcionario" class="col-form-label">Instrutor</label>
            <div class="d-flex">
              <g-select id="funcionario"
                        v-model="itemSelecionado.funcionario"
                        :options="listaInstrutores"
                        label="apelido"
                        track-by="id"
                        @input="verificaDisponibilidade" />
              <b-btn v-b-toggle.collapseFuncionario variant="azul" type="button" style="min-width: 40px"><font-awesome-icon icon="plus" /></b-btn>
            </div>
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-turma_horario'" for="horario" class="col-form-label">Horário *</label>
            <div class="d-flex">
              <template v-if="!aplicadoAlgumaTurmaAula || this.$route.name === 'turma-reabertura'">
                <g-select id="horario"
                          v-model="itemSelecionado.horario"
                          :class="wasValidated && !itemSelecionado.horario ? 'invalid-input' : 'valid-input'"
                          :options="listaHorarios"
                          label="descricao"
                          track-by="id"
                          @input="setHorario(), verificaDisponibilidade()" />
                <b-btn v-b-toggle.collapseHorario variant="azul" type="button" style="min-width: 40px"><font-awesome-icon icon="plus" /></b-btn>
              </template>
              <template v-else>
                <input id="horario" :value="itemSelecionado.horario && itemSelecionado.horario.descricao" disabled type="text" class="form-control form-control-disabled">
              </template>
            </div>
            <div v-if="wasValidated && $v.itemSelecionado.horario.$invalid" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-turma_semestre'" for="semestre" class="col-form-label">Semestre *</label>
            <template v-if="!aplicadoAlgumaTurmaAula || this.$route.name === 'turma-reabertura'">
              <g-select id="semestre"
                        v-model="itemSelecionado.semestre"
                        :class="wasValidated && !itemSelecionado.semestre ? 'invalid-input' : 'valid-input'"
                        :invalid="wasValidated && !itemSelecionado.semestre"
                        :options="listaSemestres"
                        label="descricao"
                        track-by="id"
                        @input="setSemestre" />
              <div v-if="wasValidated && $v.itemSelecionado.semestre.$invalid" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </template>
            <template v-else>
              <input id="semestre" :value="itemSelecionado.semestre && itemSelecionado.semestre.descricao" disabled type="text" class="form-control form-control-disabled">
            </template>
          </b-col>
        </b-row>

        <div class="custom-collapse">
          <b-collapse id="collapseFuncionario" ref="collapseFuncionario" accordion="acc-2">
            <div class="custom-collapse--collapsible">
              <div class="custom-collapse--icon-wrapper animated fadeIn" style="left: 709px;"><div class="custom-collapse--icon"></div></div>
              <formulario-funcionario v-show="modalFuncionarioPessoa === 'funcionario'" :is-modal="true" class="custom-collapse--component" @resolve="selecionarFuncionario" @reject="cancelarFuncionario()" />
            </div>
          </b-collapse>
        </div>

        <div class="custom-collapse">
          <b-collapse id="collapseHorario" ref="collapseHorario" accordion="acc-2">
            <div class="custom-collapse--collapsible">
              <div class="custom-collapse--icon-wrapper animated fadeIn" style="left: 212px;"><div class="custom-collapse--icon"></div></div>
              <formulario-horario :is-modal="true" class="custom-collapse--component" @resolve="selecionarHorario" @reject="cancelarHorario()" />
            </div>
          </b-collapse>
        </div>

        <b-row class="form-group">

          <b-col v-if="!ehHybrid" md="3">
            <label v-help-hint="'form-turma_maximo_alunos'" for="maximo_alunos" class="col-form-label">Máximo de alunos *</label>
            <input id="maximo_alunos" v-model.number="itemSelecionado.maximo_alunos" :readonly="salaFranqueadaObj && salaFranqueadaObj.id" type="text" class="form-control" maxlength="3" required @input="validaNumeroMaximoAlunos()">
            <div v-if="wasValidated && $v.itemSelecionado.maximo_alunos.$invalid" class="multiselect-invalid">
              Campo não pode ser vazio!
            </div>
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-turma_data_inicio'" for="data_inicio" class="col-form-label">{{ ehHybrid ? 'Data primeira aula comunicativa *' : 'Data de início *' }}</label>
                <div class="datepicker-input">
                              <v-date-picker v-model="itemSelecionado.data_inicio" @input="setDataInicial">
                                <template v-if="this.$route.name !== 'turma-reabertura'" v-slot="{ inputValue, inputEvents }">
                                  <input class="form-control"
                                    :readonly="!itemSelecionado.horario"
                                    :class="wasValidated && !itemSelecionado.data_inicio ? 'invalid-input' : 'valid-input'"
                                    element-id="data_inicio"
                                    @input="setDataInicial"
                                    :value="inputValue"
                                    v-on="inputEvents"
                                    :disabled="aplicadoAlgumaTurmaAula"
                                  />
                                </template>
                                <template v-else v-slot="{ inputValue, inputEvents }">
                                  <input class="form-control"
                                    :readonly="!itemSelecionado.horario"
                                    :class="wasValidated && !itemSelecionado.data_inicio ? 'invalid-input' : 'valid-input'"
                                    element-id="data_inicio"
                                    @input="setDataInicial"
                                    :value="inputValue"
                                    v-on="inputEvents"
                                  />
                                </template>                                
                              </v-date-picker>
                            </div> 
           <div v-if="wasValidated && !itemSelecionado.data_inicio" class="multiselect-invalid">
              Selecione uma data!
            </div>
          </b-col>

          <b-col v-if="!ehHybrid" md="3">
            <label v-help-hint="'form-turma_data_previa'" class="col-form-label">Data de término (prev.)</label>
            <input :value="itemSelecionado.data_fim" class="form-control" readonly>
          </b-col>

          <b-col md="3">
            <label v-help-hint="'form-turma_descricao'" for="descricao-turma" class="col-form-label">Descrição *</label>
            <input id="descricao-turma" v-model="itemSelecionado.descricao" type="text" class="form-control" maxlength="255" required>
            <div v-if="wasValidated && $v.itemSelecionado.descricao.$invalid" class="multiselect-invalid">
              Campo não pode ser vazio!
            </div>
          </b-col>

        </b-row>

        <b-row>
          <b-col v-if="estaEditando" md="3">
            <label v-help-hint="'form-turma_situacao'" for="situacao" class="col-form-label">Situação da turma *</label>
            <g-select
              id="situacao"
              v-model="itemSelecionado.situacao"
              :class="wasValidated && !itemSelecionado.situacao ? 'invalid-input' : 'valid-input'"
              :options="situacoes"
              label="descricao"
              track-by="valor"
              @input="validaSituacaoLivro"/>
            <div v-if="wasValidated && $v.itemSelecionado.situacao.$invalid" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </b-col>

          <b-col :md="estaEditando ? 9 : 12">
            <label v-help-hint="'form-turma_observacao'" for="observacao" class="col-form-label">Observação</label>
            <textarea id="observacao-turma" v-model="itemSelecionado.observacao" class="form-control" maxlength="255"></textarea>
          </b-col>
        </b-row>
      </div>

      <b-row v-if="ehHybrid" class="form-group">
        <b-btn variant="roxo" type="button" style="min-width: 40px" @click="onGerarProgramacao">Gerar programação de lições</b-btn>
      </b-row>
      <div v-if="ehHybrid" class="custom-collapse p-3">
        <div :class="{ hidden: !visibilidadeProgramacao }" class="custom-collapse--collapsible">
          <div class="custom-collapse--icon-wrapper animated fadeIn" style="left: 709px;"></div>

          <h5 class="title-module mb-2 px-2" @click="toggleTabelaProgramacao">
            <font-awesome-icon v-if="!visibilidadeTabelaProgramacao" icon="plus"/>
            <font-awesome-icon v-else icon="minus"/>
            Programação de lições *</h5>

          <b-collapse id="collapseTabelaProgramacao" ref="collapseTabelaProgramacao" v-model="visibilidadeTabelaProgramacao" accordion="acc-2">
            <div class="custom-collapse--collapsible">
              <div class="custom-collapse--icon-wrapper animated fadeIn" style="left: 709px;"></div>
              <b-row class="header-card-list mx-2 mb-0">
                <b-col md="1">
                  <label class="col-form-label">#</label>
                </b-col>
                <b-col md="2">
                  <label class="col-form-label">Data *</label>
                </b-col>
                <b-col md="">
                  <label class="col-form-label">Descrição *</label>
                </b-col>
              </b-row>
              <div class="row data-scroll">
                <perfect-scrollbar class="scroller col-12">
                  <b-row v-for="(item, index) in listaTurmaAula" :key="item.licao.numero" class="body-card-list mx-2">
                    <b-col md="1" data-header="#">{{ index + 1 }}</b-col>
                    <b-col md="2" data-header="Data *">
                          <div class="datepicker-input">
                              <v-date-picker v-model="item.data_aula" >
                                <template v-slot="{ inputValue, inputEvents }">
                                  <input class="form-control"
                                    :class="wasValidatedProgramacao && !item.data_aula ? 'invalid-input' : 'valid-input'"
                                     @input="onSelectDataAula"
                                    :value="inputValue"
                                    v-on="inputEvents"
                                  />
                                </template>
                              </v-date-picker>
                            </div> 
                      <div v-if="wasValidatedProgramacao && !item.data_aula" class="multiselect-invalid">
                        Selecione uma data!
                      </div>
                    </b-col>
                    <b-col md="" data-header="Descrição *">
                      <input id="intensidadeTurma" v-model="item.licao.descricao" disabled type="text" class="form-control form-control-disabled">
                    </b-col>
                  </b-row>
                </perfect-scrollbar>
              </div>
            </div>
          </b-collapse>
        </div>
      </div>

      <!-- tabela aluno -->
      <div v-if="estaEditando" class="content-sector-extra p-2 mb-2">
        <h5 class="title-module d-flex collapse-toggle">Alunos</h5>

        <div class="table-responsive-sm">
          <g-table>
            <thead>
              <tr>
                <th class="size-85">Matrícula</th>
                <th>Nome</th>
                <th class="size-100">Idade</th>
                <th class="size-125">Data da matrícula</th>
                <th class="size-100">Tipo</th>
                <th class="size-100">Telefone</th>
                <th class="coluna-icones"></th>
              </tr>
            </thead>
            <tbody>
              <perfect-scrollbar>
                <tr v-for="item in listaAlunos" :key="item.id" @dblclick="editar(item.id)">
                  <td data-label="Matrícula" class="size-85">{{ `${item.id}/${item.sequencia_contrato}` }}</td>
                  <td data-label="Nome">{{ item.nome_contato }}</td>
                  <td data-label="Idade" class="size-100">{{ idade(item.data_nascimento) }}</td>
                  <td data-label="Data da matrícula" class="size-125">{{ item.data_matricula | formatarData }}</td>
                  <td data-label="Tipo" class="size-100">{{ tipoContrato[item.tipo_contrato] }}</td>
                  <td data-label="Telefone" class="size-100">{{ item.telefone_preferencial | formatarTelefone }}</td>

                  <td class="d-flex coluna-icones">
                    <a :href="`/academico/aluno/atualizar/${item.id}`" title="Atualizar" class="icone-link">
                      <font-awesome-icon icon="pen" />
                    </a>
                  </td>
                </tr>
                <div v-if="estaCarregando" class="d-flex h-100">
                  <load-placeholder :loading="estaCarregando" />
                </div>
                <div v-if="!listaAlunos.length && !estaCarregando" class="busca-vazia">
                  <p>Nenhum resultado encontrado.</p>
                </div>
              </perfect-scrollbar>
            </tbody>
          </g-table>
        </div>

        <div id="total-container" class="d-flex justify-content-between align-items-center">
          <div class="info-btn"></div>

          <div class="info-label d-flex flex-column align-items-end">
            <div>
              <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
              <small v-else>Nenhum item encontrado</small>
            </div>
          </div>
        </div>
      </div>
      <!-- fim tabela aluno -->

      <div class="form-group row">
        <div class="col-md-6">
          <b-btn :disabled="estaSalvando" type="submit" variant="verde">{{ textoBotao }}</b-btn>
          <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
        </div>
        <div class="col-md-6 text-right">
          <b-btn v-if="estaEditando" variant="primary" @click="reaberturaTurma()">Renovar Turma</b-btn>
          <b-btn v-if="estaEditando" variant="outline-danger" class="ml-2" @click="onExcluirTurma()">Excluir Turma</b-btn>
        </div>
      </div>
    </form>

    <!-- Modal de confirmação de excluir turma -->
    <b-modal id="modalExcluirTurma" ref="modalExcluirTurma" v-model="modalExcluirTurma" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <div class="d-block text-center">
        <p>Deseja excluir a turma?</p>
        <p>Esta ação não poderá ser desfeita</p>
      </div>

      <div class="d-flex justify-content-center">
        <b-btn :disabled="excluindo" variant="vermelho" @click="excluirTurma()">Confirmar</b-btn>
        <button type="button" class="btn btn-link" @click="modalExcluirTurma = false">Cancelar</button>
      </div>
    </b-modal>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import FormularioFuncionario from '../funcionario/Formulario.vue'
import FormularioHorario from '../horario/Formulario.vue'
import DatePicker from '../../components/fields/DatePicker'
import {idade} from '../../utils/date'
import EventBus from '../../utils/event-bus'
import moment from 'moment'

export default {
  name: 'TurmaFormulario',
  components: {
    FormularioFuncionario,
    FormularioHorario,
    DatePicker
  },

  props: {
    isModal: {
      type: Boolean,
      required: false,
      default: false
    },
    filtrarModalidade: {
      type: Number,
      required: false,
      default: null
    }
  },

  data () {
    return {
      wasValidated: false,
      wasValidatedProgramacao: false,
      estaEditando: false,
      estaSalvando: false,
      modalFuncionarioPessoa: 'funcionario',
      modalExcluirTurma: false,
      excluindo: false,
      visibilidadeProgramacao: false,
      visibilidadeTabelaProgramacao: true,
      alfabeto: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
      listaAlunos: [],
      salaFranqueadaObj: {id: null, descricao: 'Selecione'},
      tipoContrato: {
        M: 'Matrícula',
        R: 'Rematrícula',
        T: 'Transferência'
      },
      totalItens: 0
    }
  },

  computed: {
    ...mapState('funcionario', {listaFuncionarios: 'lista', bFuncionarioDisponivel: 'bFuncionarioDisponivel'}),
    ...mapState('curso', {listaCursos: 'lista'}),
    ...mapState('horario', {listaHorarios: 'lista'}),
    ...mapState('modalidadeTurma', {listaModalidadesTurmaRequisicao: 'lista'}),
    ...mapState('turma', ['itemSelecionado', 'estaCarregando', 'situacoes']),
    ...mapState('turmaAula', {listaTurmaAula: 'lista'}),
    ...mapState('livro', {listaLivros: 'lista'}),
    ...mapState('valorHoraLinhas', {listaValorHoraLinhas: 'lista'}),
    ...mapState('semestre', {listaSemestres: 'lista'}),

    intensidadeTurma: {
      get () {
        if (!this.itemSelecionado.curso || !this.itemSelecionado.curso.intensidadeSelecionada) {
          return ''
        }
        if (this.itemSelecionado.curso.intensidadeSelecionada === 'R') {
          return 'Regular'
        }
        if (this.itemSelecionado.curso.intensidadeSelecionada === 'S') {
          return 'Semi-intensivo'
        }
        if (this.itemSelecionado.curso.intensidadeSelecionada === 'I') {
          return 'Intensivo'
        }
        return ''
      }
    },

    ehHybrid: {
      get () {
        return this.itemSelecionado.modalidade_turma && this.itemSelecionado.modalidade_turma.tipo === 'HYB'
      }
    },

    textoBotao: {
      get () {
        if (this.estaSalvando) {
          return 'Salvando...'
        }
        if (this.isModal) {
          return 'Salvar e selecionar'
        }
        return 'Salvar'
      }
    },

    listaInstrutores: {
      get () {
        return this.listaFuncionarios.filter(funcionario => funcionario.instrutor === true || funcionario.instrutor_personal === true)
      }
    },

    aplicadoAlgumaTurmaAula: {
      get () {
        return this.itemSelecionado.possuiTurmaAulaFinalizada
      }
    },

    listaSalasFranqueada: {
      get () {
        const empty = [{id: null, descricao: 'Selecione'}]
        const filtered = this.$store.state.salaFranqueada.lista.filter(item => item.id !== undefined && item.personal === false)
        return empty.concat(filtered)
      }
    },

    opcoesLivros: {
      get () {
        return this.listaLivros.filter(livro => {
          if (livro.situacao === 'I' && this.itemSelecionado.livroId !== livro.id) {
            return false
          }
          return livro.curso.filter(curso => {
            return this.itemSelecionado.curso ? this.itemSelecionado.curso.id === curso.id : false
          }).length > 0
        })
      }
    },
    opcoesCursos: {
      get () {
        return this.listaCursos.filter(curso => {
          if (this.itemSelecionado.modalidade_turma) {
            return (curso.situacao === 'A' &&  curso.modalidade_turma.id === this.itemSelecionado.modalidade_turma.id)
          } else {
            return curso.situacao === 'A'
          }            
        })
      }
    },

    listaModalidadesTurma: {
      get () {
        return this.listaModalidadesTurmaRequisicao.filter(modalidade => modalidade.tipo !== 'PER')
      }
    }
  },

  mounted () {
    this.$store.commit('turma/SET_ESTA_CARREGANDO', true)
    this.$store.commit('curso/SET_PAGINA_ATUAL', 1)

    this.$store.commit('salaFranqueada/SET_PAGINA_ATUAL', 1)
    this.$store.commit('salaFranqueada/SET_FILTRO_APENAS_SALA_ATIVA', true)

    this.$store.commit('modalidadeTurma/SET_PAGINA_ATUAL', 1)

    this.$store.commit('livro/SET_PAGINA_ATUAL', 1)

    this.$store.commit('valorHoraLinhas/SET_PAGINA_ATUAL', 1)

    this.$store.commit('semestre/LIMPAR_ITEM_SELECIONADO')
    this.$store.commit('semestre/SET_PAGINA_ATUAL', 1)
    this.$store.commit('semestre/SET_ANTERIOR_ATUAL_PROXIMO', false)

    this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
    this.$store.commit('funcionario/SET_FILTROS', { apenas_funcionarios_ativos: true, consultor_ou_gestor_comercial: false })
    this.$store.dispatch('funcionario/listar')

    this.$store.dispatch('curso/listar').then(() => {
      this.$store.dispatch('horario/buscarTodos').then(() => {
        this.$store.dispatch('salaFranqueada/listar').then(() => {
          this.$store.dispatch('modalidadeTurma/listar').then(() => {
            this.$store.dispatch('livro/listar').then(() => {
              this.$store.dispatch('valorHoraLinhas/listar').then(() => {
                this.$store.dispatch('semestre/listar').then(() => {
                  this.LIMPAR_ITEM_SELECIONADO()

                  const id = this.$route.params.id
                  if (id && this.$route.name === 'turma-atualizar') {
                    this.estaEditando = true
                    this.SET_ITEM_SELECIONADO_ID(id)
                    this.buscar().then(() => {
                      this.carregarItensSelecionados()
                      this.salaFranqueadaObj = this.listaSalasFranqueada.find(item => (item.id === this.itemSelecionado.salaId))
                      const filtrosBuscarAlunos = {
                        buscar_encerrados: true
                      }
                      this.buscarAlunos(filtrosBuscarAlunos).then((itens) => {
                        const listaAlunos = []
                        if (itens.length) {
                          for (let i = 0; i < itens.length; i++) {
                            if (typeof itens[i] === 'object' && itens[i].id) {
                              const contratoValido = itens[i].situacao_contrato === 'V' || (itens[i].situacao_contrato === 'E' && this.itemSelecionado.situacao.valor === 'ENC')
                              if (contratoValido) {
                                listaAlunos.push(itens[i])
                              }
                            }
                          }
                        }
                        this.listaAlunos = listaAlunos
                        this.totalItens = listaAlunos.length
                      },
                      (err) => {
                        this.onErroCarregarDados('buscarAlunos', err)
                      })
                      if (this.ehHybrid) {
                        const params = {
                          turmaId: id,
                          modalidade: 'V'
                        }
                        this.buscarAulasTurma(params).then(() => {
                          this.visibilidadeProgramacao = true
                        },
                        (err) => {
                          this.onErroCarregarDados('buscarAulasTurma', err)
                        })
                      }
                    },
                    (err) => {
                      this.onErroCarregarDados('buscar(turma-atualizar)', err)
                    })
                  } else if (id && this.$route.name === 'turma-reabertura') {
                    this.SET_ITEM_SELECIONADO_ID(id)
                    this.buscar().then(() => {
                      this.carregarItensSelecionados()
                      this.itemSelecionado.livro = this.itemSelecionado.livro.proximo_livro || this.itemSelecionado.livro
                      this.itemSelecionado.semestre = null
                      this.itemSelecionado.data_inicio = null
                      this.itemSelecionado.data_fim = null
                      this.itemSelecionado.descricao = this.itemSelecionado.horario.descricao
                      this.itemSelecionado.situacao = { 'valor': 'FOR', 'descricao': 'Em formação', 'cor': 'info' }
                    },
                    (err) => {
                      this.onErroCarregarDados('buscar(turma-reabertura)', err)
                    })
                  } else {
                    this.itemSelecionado.situacao = { 'valor': 'FOR', 'descricao': 'Em formação', 'cor': 'info' }
                    this.$store.commit('turma/SET_ESTA_CARREGANDO', false)
                    if (this.filtrarModalidade) {
                      this.itemSelecionado.modalidade_turma = this.listaModalidadesTurmaRequisicao.find(mod => mod.id === this.filtrarModalidade)
                    }
                  }
                }, (err) => {
                  this.onErroCarregarDados('semestre', err)
                })
              }, (err) => {
                this.onErroCarregarDados('valorHoraLinhas', err)
              })
            }, (err) => {
              this.onErroCarregarDados('livro', err)
            })
          }, (err) => {
            this.onErroCarregarDados('modalidadeTurma', err)
          })
        }, (err) => {
          this.onErroCarregarDados('salaFranqueada', err)
        })
      }, (err) => {
        this.onErroCarregarDados('horario', err)
      })
    }, (err) => {
      this.onErroCarregarDados('curso', err)
    })
  },

  validations: {
    itemSelecionado: {
      descricao: {required},
      semestre: {required},
      maximo_alunos: {required},
      data_inicio: {required},
      modalidade_turma: {required},
      curso: {required},
      livro: {required},
      horario: {required},
      situacao: {valor: {required}}
    }
  },

  methods: {
    ...mapActions('funcionario', {checarDisponibilidade: 'verificaDisponibilidade'}),
    ...mapMutations('turma', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('turma', ['buscar', 'salvar', 'atualizar', 'salvarHybrid', 'atualizarHybrid', 'buscarAlunos', 'excluir', 'checarPodeExcluir', 'gerarTurmaAulasTurma']),
    ...mapMutations('turmaAula', ['SET_ITEM_SELECIONADO', 'SET_TURMA_ID']),
    ...mapMutations('turmaAula', {SET_LISTA_AULAS: 'SET_LISTA'}),
    ...mapActions('turmaAula', ['buscarAulasTurma']),

    validaSituacaoLivro () {
      if ((this.itemSelecionado.situacao) && (this.itemSelecionado.situacao.valor !== 'ENC') && (this.itemSelecionado.livro) && (this.itemSelecionado.livro.situacao === 'I')) {
        let msg = 'O livro que se encontra cadastrado à esta turma, se encontra inativo.\nFavor selecionar outro livro.'
        EventBus.$emit('chamarModal', {}, `${msg}`)
        this.itemSelecionado.livro = null
      }
    },

    carregarItensSelecionados () {
      this.itemSelecionado.curso = this.listaCursos.find(curso => curso.id == this.itemSelecionado.cursoId)
      this.itemSelecionado.funcionario = this.listaFuncionarios.find(func => func.id == this.itemSelecionado.funcionarioId)
      this.itemSelecionado.semestre = this.listaSemestres.find(sem => sem.id == this.itemSelecionado.semestreId)
      this.itemSelecionado.situacao = this.situacoes.find(sit => sit.valor == this.itemSelecionado.turmaSituacao)
      this.itemSelecionado.modalidade_turma = this.listaModalidadesTurmaRequisicao.find(mod => mod.id == this.itemSelecionado.modalidadeId)
      this.itemSelecionado.livro = this.listaLivros.find(livro => livro.id == this.itemSelecionado.livroId)
        this.itemSelecionado.horario = this.listaHorarios.find(hor => hor.id == this.itemSelecionado.horarioId)
    },

    onErroCarregarDados (localErro, erro) {
      if (erro) {
        console.log(erro)
      }
      let msg = `Ocorreu um erro ao carregar os dados de ${localErro}.\nFavor entrar em contato com o suporte.`
      EventBus.$emit('chamarModal', {}, `${msg}`)
    },

    idade (dataNascimento) {
      return idade(dataNascimento)
    },

    verificaDisponibilidade () {
      if (this.itemSelecionado.horario && this.itemSelecionado.funcionario) {
        let parametros = {
          horario: this.itemSelecionado.horario.id,
          funcionario: this.itemSelecionado.funcionario.id
        }
        this.checarDisponibilidade(parametros)
          .then((response) => {
            if (this.bFuncionarioDisponivel === false) {
              let msg = 'Este funcionário está indisponível para o horário selecionado.\nDeseja prosseguir mesmo assim?'
              EventBus.$emit('chamarModal', {
                reject: () => {
                  this.itemSelecionado.funcionario = null
                }
              }, `${msg}`)
            }
          })
          .catch((err) => {
            console.error(Object.assign({}, err))
          })
      }
    },

    setCurso () {
      this.ajustarIntensidade()
      this.ajustarLivroSelecionado()
    },

    ajustarIntensidade () {
      if (this.itemSelecionado.curso.intensidade_regular) {
        this.itemSelecionado.intensidade = 'R'
      } else if (this.itemSelecionado.curso.intensidade_semi_intensivo) {
        this.itemSelecionado.intensidade = 'S'
      } else if (this.itemSelecionado.curso.intensidade_intensivo) {
        this.itemSelecionado.intensidade = 'I'
      }
    },

    ajustarLivroSelecionado () {
      if (this.itemSelecionado.livro && this.itemSelecionado.livro.id) {
        let livroValido = false
        this.opcoesLivros.forEach(livro => {
          if (livro.id === this.itemSelecionado.livro.id) {
            livroValido = true
          }
        })
        if (!livroValido) {
          this.itemSelecionado.livro = null
        }
      }
    },

    setLivro () {
      this.calcularDataTermino()
    },

    setSalaFranqueada (value) {
      if (value.lotacao_maxima) {
        this.itemSelecionado.maximo_alunos = value.lotacao_maxima
      }
      this.itemSelecionado.sala_franqueada = this.salaFranqueadaObj
    },

    setHorario (horario) {
      this.calcularDataTermino()
      if (horario) {
        this.itemSelecionado.horario = horario
      }

      let valor_letra = this.itemSelecionado.horario.turmas.length;
      while (valor_letra>26) {
        valor_letra = valor_letra - 25
      }

      if (this.itemSelecionado.horario && this.itemSelecionado.horario.descricao) {
        let semestre = this.itemSelecionado.semestre ? this.itemSelecionado.semestre.descricao : ''
        this.itemSelecionado.descricao = [this.itemSelecionado.horario.descricao, this.alfabeto[valor_letra], semestre].join(' ')
      }
    },

    setSemestre () {
      if (this.itemSelecionado.semestre !== null) {
        this.setDataInicial(new Date(this.itemSelecionado.semestre.data_inicio))
        this.setHorario()
      }
    },

    setDataInicial (value) {
      this.itemSelecionado.data_inicio = value
      this.itemSelecionado.data_fim = null
        if (this.itemSelecionado.data_inicio !== null){
        const dataFormatada = value.toLocaleDateString('pt-BR')
        if (moment(dataFormatada, 'DD/MM/YYYY', true).isValid()) {
          this.calcularDataTermino()
        }
      }
    },

    calcularDataTermino () {
     // if (!this.ehHybrid) {
        this.$store.dispatch('turma/calcularDataTermino')
      //}
    },

    selecionarFuncionario (id) {
      this.$refs.collapseFuncionario.toggle()

      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_FILTROS', { apenas_funcionarios_ativos: true, consultor_ou_gestor_comercial: false })
      this.$store.dispatch('funcionario/listar')
        .then(() => {
          for (let i = 0, l = this.listaInstrutores.length; i < l; i++) {
            if ((this.listaInstrutores[i].id * 1) === (id * 1)) {
              this.itemSelecionado.funcionario = this.listaInstrutores[i]
              return
            }
          }
        })
    },

    cancelarFuncionario () {
      this.$refs.collapseFuncionario.toggle()
    },

    selecionarHorario (id) {
      this.$refs.collapseHorario.toggle()

      this.$store.commit('horario')
      this.$store.dispatch('horario/buscarTodos')
        .then(() => {
          for (let i = 0, l = this.listaHorarios.length; i < l; i++) {
            if ((this.listaHorarios[i].id * 1) === (id * 1)) {
              this.setHorario(this.listaHorarios[i])
              return
            }
          }
        }, (err) => {
          console.log(err)
          EventBus.$emit('criarAlerta', {
            tipo: 'E',
            mensagem: 'Erro ao buscar horarios.'
          })
        })
    },

    cancelarHorario () {
      this.$refs.collapseHorario.toggle()
    },

    validaNumeroMaximoAlunos () {
      if (isNaN(parseFloat(this.itemSelecionado.maximo_alunos)) === true) {
        this.itemSelecionado.maximo_alunos = this.itemSelecionado.maximo_alunos.replace(/[^\d.]/g, '')
      }
    },

    validate () {
      this.wasValidated = true
      return !!this.itemSelecionado.modalidade_turma &&
        !!this.itemSelecionado.curso &&
        !!this.itemSelecionado.horario &&
        !!this.itemSelecionado.data_inicio &&
        !!this.itemSelecionado.descricao &&
        !!this.itemSelecionado.semestre &&
        (this.ehHybrid || this.validaMaximoAlunos())
    },

    validateProgramacao () {
      this.wasValidatedProgramacao = true
      this.listaTurmaAula.forEach(aula => {
        if (!aula.data_aula || !moment(aula.data_aula).isValid) {
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: 'Todas as aulas devem ser preenchidas com datas válidas.'
          })
          return false
        }
      })
      return true
    },

    validaMaximoAlunos () {
      return this.itemSelecionado.maximo_alunos !== '' &&
              this.itemSelecionado.maximo_alunos !== null &&
              this.itemSelecionado.maximo_alunos !== undefined
    },

    voltar (idTurma) {
      this.LIMPAR_ITEM_SELECIONADO()

      if (this.isModal) {
        if (idTurma) {
          this.$emit('resolve', idTurma)
        } else {
          this.$emit('reject')
        }
      } else {
        this.$router.push(this.$route.matched[0].path)
      }
    },

    reaberturaTurma () {
      this.$router.push(`${this.$route.matched[0].path}/reabertura/${this.itemSelecionado.id}`)
    },

    submit () {
      this.estaSalvando = true

      if (this.validate() === false) {
        this.estaSalvando = false
        return
      }

      if (this.ehHybrid) {
        if (this.validateProgramacao() === false) {
          this.estaSalvando = false
          return
        }
        const params = {
          listaTurmaAula: this.listaTurmaAula
        }

        if (this.estaEditando) {
          this.atualizarHybrid(params)
            .then((id) => {
              this.voltar(id)
            })
            .finally(() => {
              this.estaSalvando = false
            })
        } else {
          this.salvarHybrid(params)
            .then((id) => {
              this.voltar(id)
            })
            .finally(() => {
              this.estaSalvando = false
            })
        }
      } else {
        if (this.estaEditando) {
          this.atualizar()
            .then((id) => {
              this.voltar(id)
            })
            .finally(() => {
              this.estaSalvando = false
            })
        } else {
          this.salvar()
            .then((id) => {
              this.voltar(id)
            })
            .finally(() => {
              this.estaSalvando = false
            })
        }
      }
    },

    onExcluirTurma () {
      this.checarPodeExcluir(this.$route.params.id).then((res) => {
        if (res.body.erro || !res.body.corpo) {
          return
        }
        this.modalExcluirTurma = true
      }, (err) => {
        console.log(err)
      })
    },

    excluirTurma () {
      this.excluindo = true
      this.excluir(this.$route.params.id)
        .then((res) => {
          this.excluindo = false
          if (res.body.erro) {
            EventBus.$emit('criarAlerta', {
              tipo: 'E',
              mensagem: res.body.mensagem
            })
            return
          }
          this.voltar()
        }, (err) => {
          console.log(err)
          EventBus.$emit('criarAlerta', {
            tipo: 'E',
            mensagem: 'Erro ao excluir turma.'
          })
        })
    },

    verificaCarregamento (numeroDeRequisicoesFeitas, requisicoes) {
      if (numeroDeRequisicoesFeitas !== requisicoes) {
        return true
      } else {
        return false
      }
    },

    editar (id) {
      this.$router.push(`/academico/aluno/atualizar/${id}`)
    },

    onGerarProgramacao () {
      if (!this.validate()) {
        return false
      }
      this.SET_LISTA_AULAS([])
      this.visibilidadeProgramacao = false
      this.itemSelecionado.maximo_alunos = 0
      this.$store.commit('turmaAula/SET_ITEM_SELECIONADO', this.itemSelecionado)
      this.gerarTurmaAulasTurma().then(response => {
        const lista = response.map(turmaAula => {
          if (!(turmaAula.data_aula instanceof Date)) {
            turmaAula.data_aula = new Date(turmaAula.data_aula)
          }
          return turmaAula
        })
        this.SET_LISTA_AULAS(lista)
        this.visibilidadeProgramacao = true
        this.ajustarDatasInicioFimHybrid()
        this.estaEditando = true
        this.SET_ITEM_SELECIONADO_ID(lista[0].turma_id)
      }, err => {
        let tipo = 'E'
        let mensagem = ''
        if (err.body.mensagem) {
          tipo = 'A'
          mensagem = err.body.mensagem
        }
        EventBus.$emit('criarAlerta', {
          tipo: tipo,
          mensagem: mensagem
        })
        console.log(err)
      })
    },

    toggleTabelaProgramacao () {
      this.visibilidadeTabelaProgramacao = !this.visibilidadeTabelaProgramacao
    },

    onSelectDataAula () {
      this.ajustarDatasInicioFimHybrid()
    },

    ajustarDatasInicioFimHybrid () {
      this.setarDataInicioHybrid()
      this.setarDataFimHybrid()
    },

    setarDataInicioHybrid () {
      let menorDia = this.listaTurmaAula[0].data_aula
      this.listaTurmaAula.forEach(aula => {
        if (moment(aula.data_aula).isBefore(moment(menorDia))) {
          menorDia = aula.data_aula
        }
      })
      this.itemSelecionado.data_inicio = menorDia
    },

    setarDataFimHybrid () {
      let maiorDia = this.listaTurmaAula[0].data_aula
      this.listaTurmaAula.forEach(aula => {
        if (moment(aula.data_aula).isAfter(moment(maiorDia))) {
          maiorDia = aula.data_aula
        }
      })
      this.itemSelecionado.data_fim = moment(maiorDia).format('DD/MM/YYYY')
    }
  }
}
</script>
<style scoped>
.content-sector-extra {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}
</style>
