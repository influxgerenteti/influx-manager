<template>
  <div class="animated fadeIn">
    <h5 class="title-module mb-3">Avaliações</h5>
    <div v-if="carregandoAvaliacao" class="d-flex form-loading">
      <load-placeholder :loading="carregandoAvaliacao" />
    </div>

    <!-- 1ª Avaliação Parcial -->
    <div class="content-sector sector-primary p-3">
      <h5 v-b-toggle.notas-7th class="title-module d-flex collapse-toggle" >1ª Avaliação Parcial<font-awesome-icon icon="caret-up" class="ml-auto my-auto collapse-toggle-state" /></h5>
      <b-collapse id="notas-7th">
        <div class="table-responsive-sm">
          <g-table id="listagem-alunos" class="table-notas">
            <thead>
              <tr>
                <th>Aluno</th>
                <th class="size-100">Listening</th>
                <th class="size-100">Speaking</th>
                <th class="size-115">Writing</th>
              </tr>
            </thead>
            <tbody>
              <perfect-scrollbar>
                <div v-if="!listaAlunos.length && !carregandoAvaliacao" class="busca-vazia">
                  <p>Nenhum resultado encontrado.</p>
                </div>                
                  <tr v-for="(item, index) in listaAlunos" :key="index">
                    <td><span>{{ item.pessoa.nome_contato }}</span></td>
                    <td class="size-100">
                      <g-notas-avaliacao :id="`nota_listening_1-${index}`" v-model="item.alunoAvaliacaoConceituals.nota_listening_1" :options="selectNotas" label="descricao" table-view />
                    </td>
                    <td class="size-100">
                      <g-notas-avaliacao :id="`nota_speaking_1-${index}`" v-model="item.alunoAvaliacaoConceituals.nota_speaking_1" :options="selectNotas" label="descricao" table-view />
                    </td>
                    <td class="size-115">
                      <g-notas-avaliacao :id="`nota_writing_1-${index}`" v-model="item.alunoAvaliacaoConceituals.nota_writing_1" :options="selectNotas" label="descricao" table-view />
                    </td>
                  </tr>
              </perfect-scrollbar>
            </tbody>
          </g-table>
        </div>
      </b-collapse>
    </div>

    <!-- MID-TERM -->
    <div class="content-sector sector-secondary p-3">
      <h5 v-b-toggle.notas-mid-term class="title-module d-flex collapse-toggle">Mid-Term<font-awesome-icon icon="caret-up" class="ml-auto my-auto collapse-toggle-state" /></h5>
      <b-collapse id="notas-mid-term">
        <div class="table-responsive-sm">
          <g-table id="listagem-alunos" class="table-notas">
            <thead>
              <tr>
                <th>Aluno</th>
                <th class="notas-column">Notas</th>
                <th class="size-205">Retake</th>
              </tr>
            </thead>
            <tbody>
              <perfect-scrollbar>
                <div v-if="!listaAlunos.length && !carregandoAvaliacao" class="busca-vazia">
                  <p>Nenhum resultado encontrado.</p>
                </div>
                <tr v-for="(item, index) in listaAlunos" :key="index">
                  <td><span>{{ item.pessoa.nome_contato }}</span></td>
                  <td class="notas-column">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">OG</div>
                      </div>

                      <g-notas-avaliacao :id="`nota_mid_term_oral-${index}`" v-model="item.alunoAvaliacaos.nota_mid_term_oral" :options="selectNotas" label="descricao" table-view />
                    </div>

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">T</div>
                      </div>

                      <the-mask :mask="['#,##', '##,##']" :id="`nota_mid_term_test-${index}`" v-model="item.alunoAvaliacaos.nota_mid_term_test" type="text" masked class="form-control" />
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">C</div>
                      </div>

                      <the-mask :mask="['#,##', '##,##']" :id="`nota_mid_term_composition-${index}`" v-model="item.alunoAvaliacaos.nota_mid_term_composition" type="text" masked class="form-control" />
                    </div>

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">WG</div>
                      </div>

                      <input :id="`nota_mid_term_escrita-${index}`" v-model="item.alunoAvaliacaos.nota_mid_term_escrita" :soma="item.alunoAvaliacaos.nota_mid_term_escrita = somaWG(item, 'nota_mid_term_test', 'nota_mid_term_composition')" type="text" class="form-control" disabled>
                    </div>
                  </td>
                  <td class="size-205">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">OG</div>
                      </div>

                      <g-notas-avaliacao :id="`nota_retake_mid_term_oral-${index}`" v-model="item.alunoAvaliacaos.nota_retake_mid_term_oral" :options="selectNotas" label="descricao" table-view />
                    </div>

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">WG</div>
                      </div>

                      <the-mask :mask="['#,##', '##,##']" :id="`nota_retake_mid_term_escrita-${index}`" v-model="item.alunoAvaliacaos.nota_retake_mid_term_escrita" type="text" masked class="form-control" />
                    </div>
                  </td>
                </tr>
              </perfect-scrollbar>
            </tbody>
          </g-table>
        </div>
      </b-collapse>
    </div>

    <!-- 2ª Avaliação Parcial -->
    <div class="content-sector sector-primary p-3">
      <h5 v-b-toggle.notas-23rd class="title-module d-flex collapse-toggle">2ª Avaliação Parcial<font-awesome-icon icon="caret-up" class="ml-auto my-auto collapse-toggle-state" /></h5>
      <b-collapse id="notas-23rd">
        <div class="table-responsive-sm">
          <g-table id="listagem-alunos" class="table-notas">
            <thead>
              <tr>
                <th>Aluno</th>
                <th class="size-100">Listening</th>
                <th class="size-100">Speaking</th>
                <th class="size-115">Writing</th>
              </tr>
            </thead>
            <tbody>
              <perfect-scrollbar>
                <div v-if="!listaAlunos.length && !carregandoAvaliacao" class="busca-vazia">
                  <p>Nenhum resultado encontrado.</p>
                </div>
                <tr v-for="(item, index) in listaAlunos" :key="index">
                  <td><span>{{ item.pessoa.nome_contato }}</span></td>
                  <td class="size-100">
                    <g-notas-avaliacao :id="`nota_listening_2-${index}`" v-model="item.alunoAvaliacaoConceituals.nota_listening_2" :options="selectNotas" label="descricao" table-view />
                  </td>
                  <td class="size-100">
                    <g-notas-avaliacao :id="`nota_speaking_2-${index}`" v-model="item.alunoAvaliacaoConceituals.nota_speaking_2" :options="selectNotas" label="descricao" table-view />
                  </td>
                  <td class="size-115">
                    <g-notas-avaliacao :id="`nota_writing_2-${index}`" v-model="item.alunoAvaliacaoConceituals.nota_writing_2" :options="selectNotas" label="descricao" table-view />
                  </td>
                </tr>
              </perfect-scrollbar>
            </tbody>
          </g-table>
        </div>
      </b-collapse>
    </div>

    <!-- FINAL TEST -->
    <div class="content-sector sector-secondary p-3">
      <h5 v-b-toggle.final-test class="title-module d-flex collapse-toggle">Final Test<font-awesome-icon icon="caret-up" class="ml-auto my-auto collapse-toggle-state" /></h5>
      <b-collapse id="final-test">
        <div class="table-responsive-sm">
          <g-table id="listagem-alunos" class="table-notas">
            <thead>
              <tr>
                <th>Aluno</th>
                <th class="notas-column">Notas</th>
                <th class="size-205">Retake</th>
              </tr>
            </thead>
            <tbody>
              <perfect-scrollbar>
                <div v-if="!listaAlunos.length && !carregandoAvaliacao" class="busca-vazia">
                  <p>Nenhum resultado encontrado.</p>
                </div>
                <tr v-for="(item, index) in listaAlunos" :key="index">
                   <td><span>{{ item.pessoa.nome_contato }}</span></td>
                    <td class="notas-column">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <div class="input-group-text">OG</div>
                        </div>

                        <g-notas-avaliacao :id="`nota_final_oral-${index}`" v-model="item.alunoAvaliacaos.nota_final_oral" :options="selectNotas" label="descricao" table-view />
                      </div>

                      <div class="input-group">
                        <div class="input-group-prepend">
                          <div class="input-group-text">T</div>
                        </div>

                        <the-mask :mask="['#,##', '##,##']" :id="`nota_final_test-${index}`" v-model="item.alunoAvaliacaos.nota_final_test" type="text" masked class="form-control" />
                      </div>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <div class="input-group-text">C</div>
                        </div>

                        <the-mask :mask="['#,##', '##,##']" :id="`nota_final_composition-${index}`" v-model="item.alunoAvaliacaos.nota_final_composition" type="text" masked class="form-control" />
                      </div>

                      <div class="input-group">
                        <div class="input-group-prepend">
                          <div class="input-group-text">WG</div>
                        </div>

                        <input :id="`nota_final_escrita-${index}`" v-model="item.alunoAvaliacaos.nota_final_escrita" :soma="item.alunoAvaliacaos.nota_final_escrita = somaWG(item, 'nota_final_test', 'nota_final_composition')" type="text" class="form-control" disabled>
                      </div>
                    </td>
                    <td class="size-205">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <div class="input-group-text">OG</div>
                        </div>

                        <g-notas-avaliacao :id="`nota_retake_final_oral-${index}`" v-model="item.alunoAvaliacaos.nota_retake_final_oral" :options="selectNotas" label="descricao" table-view />
                      </div>

                      <div class="input-group">
                        <div class="input-group-prepend">
                          <div class="input-group-text">WG</div>
                        </div>

                        <the-mask :mask="['#,##', '##,##']" :id="`nota_retake_final_escrita-${index}`" v-model="item.alunoAvaliacaos.nota_retake_final_escrita" type="text" masked class="form-control" />

                      </div>
                    </td>
                </tr>
              </perfect-scrollbar>
            </tbody>
          </g-table>
        </div>
      </b-collapse>
    </div>
    <div class="form-group pt-2 mb-0">
      <b-btn :disabled="aplicandoNotas" variant="verde" @click="salvarNotasGeral(false)">{{ aplicandoNotas ? 'Salvando...': 'Salvar' }}</b-btn>
      <b-btn :disabled="aplicandoNotas" variant="verde" @click="salvarNotasGeral(true)">{{ aplicandoNotas ? 'Salvando...': 'Salvar e sair' }}</b-btn>

      <b-btn variant="link" @click="$emit('fecharModal')">Cancelar</b-btn>
    </div>
  </div>
</template>
<script>
import {mapState, mapMutations, mapActions} from 'vuex'

export default{
  name: 'AvaliacoesAlunos',

  props: {
    isPersonal: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      listaAlunos: [],
      aplicandoNotas: false,
      carregandoAvaliacao: false

    }
  },

  computed: {
    ...mapState('turma', {turmaId: 'itemSelecionadoID', turmaSelecionadaObj: 'itemSelecionado'}),
    ...mapState('alunoDiario', ['listaAvaliacaoTurma']),
    ...mapState('diarioPersonal', ['listaAvaliacoesAluno', 'contratoSelecionadoID']),
    ...mapState('conceitoAvaliacao', {listaConceitoAvaliacaoRequisicao: 'lista'}),
    ...mapState('licao', {livroId: 'livroSelecionadoID'}),

    selectNotas: {
      get () {
        const empty = [{id: null, descricao: ' '}]
        return empty.concat(this.listaConceitoAvaliacaoRequisicao)
      }
    }
  },

  methods: {
    ...mapActions('alunoDiario', {lancarAtualizarNotasTurma: 'lancarAtualizarNotas', buscarAvaliacoesTurma: 'buscarAvaliacaoAlunosPorTurma'}),
    ...mapActions('diarioPersonal', {lancarAtualizarNotasPersonal: 'lancarAtualizarNotas', buscarAvaliacaoAluno: 'buscarAvaliacoesPorContrato'}),
    ...mapActions('conceitoAvaliacao', {listarConceitoAvaliacao: 'listar'}),
    ...mapMutations('conceitoAvaliacao', {SET_PAGINA_ATUAL_CONCEICO_AVALIACAO: 'SET_PAGINA_ATUAL', SET_LISTA_CONCEITO_AVALIACAO: 'SET_LISTA'}),
    ...mapMutations('alunoDiario', {SET_LISTA_AVALIACOES_TURMA: 'SET_LISTA_AVALIACOES_TURMA'}),
    ...mapMutations('diarioPersonal', {SET_LISTA_AVALIACAO_ALUNO_PERSONAL: 'SET_LISTA_AVALIACAO_ALUNO_PERSONAL'}),

    somaWG (item, n1, n2) {
      let retorno = ''
      let T = item.alunoAvaliacaos[n1] + ''
      let C = item.alunoAvaliacaos[n2] + ''

      if (T && T !== '') {
        T = T.replace(',', '.')
      }

      if (C && C !== '') {
        C = C.replace(',', '.')
      }

      if (T !== '' && T >= 0 && C !== '' && C >= 0) {
        retorno = ((T * 1) + (C * 1)).toFixed(2).replace('.', ',')
      }

      return retorno
    },

    adicionaCamposConceitoAvaliacao (alunoAvaliacaoConceitualsReferencia, alunoAvaliacaosReferencia, alunoAvaliacaoConceitualMetaData, alunoAvaliacaoMetaData) {
      if (alunoAvaliacaoConceitualMetaData.nota_listening_1.id) {
        alunoAvaliacaoConceitualsReferencia.nota_listening_1 = alunoAvaliacaoConceitualMetaData.nota_listening_1.id
      }
      if (alunoAvaliacaoConceitualMetaData.nota_listening_2.id) {
        alunoAvaliacaoConceitualsReferencia.nota_listening_2 = alunoAvaliacaoConceitualMetaData.nota_listening_2.id
      }
      if (alunoAvaliacaoConceitualMetaData.nota_speaking_1.id) {
        alunoAvaliacaoConceitualsReferencia.nota_speaking_1 = alunoAvaliacaoConceitualMetaData.nota_speaking_1.id
      }
      if (alunoAvaliacaoConceitualMetaData.nota_speaking_2.id) {
        alunoAvaliacaoConceitualsReferencia.nota_speaking_2 = alunoAvaliacaoConceitualMetaData.nota_speaking_2.id
      }
      if (alunoAvaliacaoConceitualMetaData.nota_writing_1.id) {
        alunoAvaliacaoConceitualsReferencia.nota_writing_1 = alunoAvaliacaoConceitualMetaData.nota_writing_1.id
      }
      if (alunoAvaliacaoConceitualMetaData.nota_writing_2.id) {
        alunoAvaliacaoConceitualsReferencia.nota_writing_2 = alunoAvaliacaoConceitualMetaData.nota_writing_2.id
      }
      if (alunoAvaliacaoMetaData.nota_final_oral.id) {
        alunoAvaliacaosReferencia.nota_final_oral = alunoAvaliacaoMetaData.nota_final_oral.id
      }
      if (alunoAvaliacaoMetaData.nota_mid_term_oral.id) {
        alunoAvaliacaosReferencia.nota_mid_term_oral = alunoAvaliacaoMetaData.nota_mid_term_oral.id
      }
      if (alunoAvaliacaoMetaData.nota_retake_final_oral.id) {
        alunoAvaliacaosReferencia.nota_retake_final_oral = alunoAvaliacaoMetaData.nota_retake_final_oral.id
      }
      if (alunoAvaliacaoMetaData.nota_retake_mid_term_oral.id) {
        alunoAvaliacaosReferencia.nota_retake_mid_term_oral = alunoAvaliacaoMetaData.nota_retake_mid_term_oral.id
      }
    },

    adicionaCamposNotas (alunoAvaliacaosReferencia, alunoAvaliacaoMetaData) {
      if (alunoAvaliacaoMetaData.nota_final_composition) {
        alunoAvaliacaoMetaData.nota_final_composition += ''
        alunoAvaliacaosReferencia.nota_final_composition = alunoAvaliacaoMetaData.nota_final_composition.replace(',', '.') * 1
      }
      if (alunoAvaliacaoMetaData.nota_final_escrita) {
        alunoAvaliacaoMetaData.nota_final_escrita += ''
        alunoAvaliacaosReferencia.nota_final_escrita = alunoAvaliacaoMetaData.nota_final_escrita.replace(',', '.') * 1
      }
      if (alunoAvaliacaoMetaData.nota_final_test) {
        alunoAvaliacaoMetaData.nota_final_test += ''
        alunoAvaliacaosReferencia.nota_final_test = alunoAvaliacaoMetaData.nota_final_test.replace(',', '.') * 1
      }
      if (alunoAvaliacaoMetaData.nota_mid_term_composition) {
        alunoAvaliacaoMetaData.nota_mid_term_composition += ''
        alunoAvaliacaosReferencia.nota_mid_term_composition = alunoAvaliacaoMetaData.nota_mid_term_composition.replace(',', '.') * 1
      }
      if (alunoAvaliacaoMetaData.nota_mid_term_escrita) {
        alunoAvaliacaoMetaData.nota_mid_term_escrita += ''
        alunoAvaliacaosReferencia.nota_mid_term_escrita = alunoAvaliacaoMetaData.nota_mid_term_escrita.replace(',', '.') * 1
      }
      if (alunoAvaliacaoMetaData.nota_mid_term_test) {
        alunoAvaliacaoMetaData.nota_mid_term_test += ''
        alunoAvaliacaosReferencia.nota_mid_term_test = alunoAvaliacaoMetaData.nota_mid_term_test.replace(',', '.') * 1
      }
      if (alunoAvaliacaoMetaData.nota_retake_final_escrita) {
        alunoAvaliacaoMetaData.nota_retake_final_escrita += ''
        alunoAvaliacaosReferencia.nota_retake_final_escrita = alunoAvaliacaoMetaData.nota_retake_final_escrita.replace(',', '.') * 1
      }
      if (alunoAvaliacaoMetaData.nota_retake_mid_term_escrita) {
        alunoAvaliacaoMetaData.nota_retake_mid_term_escrita += ''
        alunoAvaliacaosReferencia.nota_retake_mid_term_escrita = alunoAvaliacaoMetaData.nota_retake_mid_term_escrita.replace(',', '.') * 1
      }
    },

    montaParametrosDiarioTurma (alunoAvaliacaoConceitualsReferencia, alunoAvaliacaosReferencia) {
      this.listaAlunos.map(alunoMetaData => {
        let alunoAvaliacaoConceitualMetaData = alunoMetaData.alunoAvaliacaoConceituals
        let alunoAvaliacaoMetaData = alunoMetaData.alunoAvaliacaos
        let objetoAlunoAvaliacaoConceitual = {
          aluno: alunoMetaData.alunoId,
          livro: this.turmaSelecionadaObj.livro.id,
          contrato: alunoMetaData.contrato.id
        }
        let objetoAlunoAvaliacao = {
          aluno: alunoMetaData.alunoId,
          livro: this.turmaSelecionadaObj.livro.id,
          contrato: alunoMetaData.contrato.id
        }
        this.adicionaCamposConceitoAvaliacao(objetoAlunoAvaliacaoConceitual, objetoAlunoAvaliacao, alunoAvaliacaoConceitualMetaData, alunoAvaliacaoMetaData)
        this.adicionaCamposNotas(objetoAlunoAvaliacao, alunoAvaliacaoMetaData)
        if (alunoAvaliacaoConceitualMetaData.id) {
          objetoAlunoAvaliacaoConceitual.id = alunoAvaliacaoConceitualMetaData.id
        }
        if (alunoAvaliacaoMetaData.id) {
          objetoAlunoAvaliacao.id = alunoAvaliacaoMetaData.id
        }
        alunoAvaliacaoConceitualsReferencia.push(objetoAlunoAvaliacaoConceitual)
        alunoAvaliacaosReferencia.push(objetoAlunoAvaliacao)
      })
    },

    montaParametrosDiarioPersonal (alunoAvaliacaoConceitualsReferencia, alunoAvaliacaosReferencia) {
      this.listaAlunos.map(alunoMetaData => {
        let alunoAvaliacaoConceitualMetaData = alunoMetaData.alunoAvaliacaoConceituals
        let alunoAvaliacaoMetaData = alunoMetaData.alunoAvaliacaos
        let objetoAlunoAvaliacaoConceitual = {
          aluno: alunoMetaData.alunoId,
          livro: this.livroId,
          contrato: this.contratoSelecionadoID
        }
        let objetoAlunoAvaliacao = {
          aluno: alunoMetaData.alunoId,
          livro: this.livroId,
          contrato: this.contratoSelecionadoID
        }
        this.adicionaCamposConceitoAvaliacao(objetoAlunoAvaliacaoConceitual, objetoAlunoAvaliacao, alunoAvaliacaoConceitualMetaData, alunoAvaliacaoMetaData)
        this.adicionaCamposNotas(objetoAlunoAvaliacao, alunoAvaliacaoMetaData)
        if (alunoAvaliacaoConceitualMetaData.id) {
          objetoAlunoAvaliacaoConceitual.id = alunoAvaliacaoConceitualMetaData.id
        }
        if (alunoAvaliacaoMetaData.id) {
          objetoAlunoAvaliacao.id = alunoAvaliacaoMetaData.id
        }
        alunoAvaliacaoConceitualsReferencia.push(objetoAlunoAvaliacaoConceitual)
        alunoAvaliacaosReferencia.push(objetoAlunoAvaliacao)
      })
    },

    montaParametrosGeral () {
      let alunoAvaliacaoConceituals = []
      let alunoAvaliacaos = []
      let parametrosNotas = {}

      if (this.isPersonal) {
        this.montaParametrosDiarioPersonal(alunoAvaliacaoConceituals, alunoAvaliacaos)
        parametrosNotas = {
          alunos_avaliacao_conceitual: alunoAvaliacaoConceituals,
          alunos_avaliacao: alunoAvaliacaos
        }
      } else {
        this.montaParametrosDiarioTurma(alunoAvaliacaoConceituals, alunoAvaliacaos)
        parametrosNotas = {
          turma: this.turmaId,
          alunos_avaliacao_conceitual: alunoAvaliacaoConceituals,
          alunos_avaliacao: alunoAvaliacaos
        }
      }

      return parametrosNotas
    },

    salvarNotasTurma (bSalvarESair, parametrosBackEnd) {
      this.lancarAtualizarNotasTurma(parametrosBackEnd)
        .then(() => {
          this.carregarAvaliacoes(bSalvarESair)
          if (bSalvarESair) {
            this.$emit('fecharModal')
          }
        })
        .catch((error) => {
          console.info(error)
          this.aplicandoNotas = false
        })
    },

    salvarNotasPersonal (bSalvarESair, parametrosBackEnd) {
      this.lancarAtualizarNotasPersonal(parametrosBackEnd)
        .then(() => {
          this.carregarAvaliacoes(bSalvarESair)
          if (bSalvarESair) {
            this.$emit('fecharModal')
          }
        })
        .catch((error) => {
          console.info(error)
          this.aplicandoNotas = false
        })
    },

    salvarNotasGeral (bSalvarESair) {
      this.aplicandoNotas = true
      const parametrosNotas = this.montaParametrosGeral()
      if (this.isPersonal) {
        this.salvarNotasPersonal(bSalvarESair, parametrosNotas)
      } else {
        this.salvarNotasTurma(bSalvarESair, parametrosNotas)
      }
    },

    carregarConceitoAvaliacao () {
      this.SET_PAGINA_ATUAL_CONCEICO_AVALIACAO(1)
      this.SET_LISTA_CONCEITO_AVALIACAO([])
      this.listarConceitoAvaliacao()
        .then(item => {
          if (this.isPersonal) {
            this.carregarDadosPersonal()
          } else {
            this.carregarDadosTurma()
          }
        })
    },

    carregarDadosPersonal () {
      const data = {livro: this.livroId}
      this.buscarAvaliacaoAluno(data)
        .then(lista => {
          this.adicionarNaListagem([this.listaAvaliacoesAluno])
          this.carregandoAvaliacao = false
        })
    },

    carregarDadosTurma () {
      const data = {
        turma: this.turmaId,
        livro: this.livroId
      }
      this.buscarAvaliacoesTurma(data)
        .then(lista => {
          this.adicionarNaListagem(this.listaAvaliacaoTurma)
          this.carregandoAvaliacao = false
        })
    },

    buscarConceitoSelecionado (conceitoDb) {
      if (conceitoDb && conceitoDb !== '') {
        return this.selectNotas.find(item => (conceitoDb.id === item.id))
      }
      return ''
    },

    criarObjetoLista (item) {
      return {
        alunoId: item.id,
        contrato: item.contratos[0],
        situacao: item.contratos[0].situacao,
        pessoa: {
          nome_contato: item.pessoa.nome_contato
        },
        alunoAvaliacaos: {
          id: null,
          // Mid-Term
          nota_mid_term_oral: {id: null, descricao: ' '},
          nota_retake_mid_term_oral: {id: null, descricao: ' '},
          // Final Teste
          nota_final_oral: {id: null, descricao: ' '},
          nota_retake_final_oral: {id: null, descricao: ' '}
        },
        alunoAvaliacaoConceituals: {
          id: null,
          nota_listening_1: '',
          nota_speaking_1: '',
          nota_writing_1: '',
          nota_listening_2: '',
          nota_speaking_2: '',
          nota_writing_2: ''
        }
      }
    },

    adicionarNaListagem (arrayDadosAluno) {
      arrayDadosAluno.map(item => {
        let objetoAluno = this.criarObjetoLista(item)
        if (item.alunoAvaliacaoConceituals.length) {
          let alunoAvaliacaoConceitualSelecionado = item.alunoAvaliacaoConceituals[0]
          objetoAluno.alunoAvaliacaoConceituals.id = alunoAvaliacaoConceitualSelecionado.id
          objetoAluno.alunoAvaliacaoConceituals.nota_listening_1 = this.buscarConceitoSelecionado(alunoAvaliacaoConceitualSelecionado.nota_listening_1)
          objetoAluno.alunoAvaliacaoConceituals.nota_speaking_1 = this.buscarConceitoSelecionado(alunoAvaliacaoConceitualSelecionado.nota_speaking_1)
          objetoAluno.alunoAvaliacaoConceituals.nota_writing_1 = this.buscarConceitoSelecionado(alunoAvaliacaoConceitualSelecionado.nota_writing_1)
          objetoAluno.alunoAvaliacaoConceituals.nota_listening_2 = this.buscarConceitoSelecionado(alunoAvaliacaoConceitualSelecionado.nota_listening_2)
          objetoAluno.alunoAvaliacaoConceituals.nota_speaking_2 = this.buscarConceitoSelecionado(alunoAvaliacaoConceitualSelecionado.nota_speaking_2)
          objetoAluno.alunoAvaliacaoConceituals.nota_writing_2 = this.buscarConceitoSelecionado(alunoAvaliacaoConceitualSelecionado.nota_writing_2)
        }
        if (item.alunoAvaliacaos.length) {
          let alunoAvaliacaoSelecionado = item.alunoAvaliacaos[0]
          objetoAluno.alunoAvaliacaos.id = alunoAvaliacaoSelecionado.id
          objetoAluno.alunoAvaliacaos.nota_mid_term_oral = this.buscarConceitoSelecionado(alunoAvaliacaoSelecionado.nota_mid_term_oral)
          objetoAluno.alunoAvaliacaos.nota_final_oral = this.buscarConceitoSelecionado(alunoAvaliacaoSelecionado.nota_final_oral)
          objetoAluno.alunoAvaliacaos.nota_retake_mid_term_oral = this.buscarConceitoSelecionado(alunoAvaliacaoSelecionado.nota_retake_mid_term_oral)
          objetoAluno.alunoAvaliacaos.nota_retake_final_oral = this.buscarConceitoSelecionado(alunoAvaliacaoSelecionado.nota_retake_final_oral)
          objetoAluno.alunoAvaliacaos.nota_final_composition = alunoAvaliacaoSelecionado.nota_final_composition ? alunoAvaliacaoSelecionado.nota_final_composition.replace('.', ',') : null
          objetoAluno.alunoAvaliacaos.nota_final_escrita = alunoAvaliacaoSelecionado.nota_final_escrita ? alunoAvaliacaoSelecionado.nota_final_escrita.replace('.', ',') : null
          objetoAluno.alunoAvaliacaos.nota_final_test = alunoAvaliacaoSelecionado.nota_final_test ? alunoAvaliacaoSelecionado.nota_final_test.replace('.', ',') : null
          objetoAluno.alunoAvaliacaos.nota_mid_term_composition = alunoAvaliacaoSelecionado.nota_mid_term_composition ? alunoAvaliacaoSelecionado.nota_mid_term_composition.replace('.', ',') : null
          objetoAluno.alunoAvaliacaos.nota_mid_term_escrita = alunoAvaliacaoSelecionado.nota_mid_term_escrita ? alunoAvaliacaoSelecionado.nota_mid_term_escrita.replace('.', ',') : null
          objetoAluno.alunoAvaliacaos.nota_mid_term_test = alunoAvaliacaoSelecionado.nota_mid_term_test ? alunoAvaliacaoSelecionado.nota_mid_term_test.replace('.', ',') : null
          objetoAluno.alunoAvaliacaos.nota_retake_final_escrita = alunoAvaliacaoSelecionado.nota_retake_final_escrita ? alunoAvaliacaoSelecionado.nota_retake_final_escrita.replace('.', ',') : null
          objetoAluno.alunoAvaliacaos.nota_retake_mid_term_escrita = alunoAvaliacaoSelecionado.nota_retake_mid_term_escrita ? alunoAvaliacaoSelecionado.nota_retake_mid_term_escrita.replace('.', ',') : null
        }
        this.listaAlunos.push(objetoAluno)
      })
    },

    carregarAvaliacoes (bSalvarESair) {
      if (!bSalvarESair) {
        this.aplicandoNotas = false
        this.carregandoAvaliacao = true
        this.listaAlunos = []
        this.SET_LISTA_AVALIACOES_TURMA([])
        this.SET_LISTA_AVALIACAO_ALUNO_PERSONAL([])
        this.carregarConceitoAvaliacao()
      }
    }
  }
}
</script>
<style scoped>

.table-hover tbody tr:hover .form-control:not(input[disabled='disabled']) {
  background-color: #fff;
}
.input-group-text {
  background-color: transparent;
}
tr input[type="text"] {
  text-transform: uppercase;
}

.notas-column {
  padding-right: 3.5rem;
}
td.size-75 input {
  max-width: 30px;
}

.table-notas td {
  overflow: visible;
}

#listagem-alunos {
  position: relative;
  min-height: 200px;
}

</style>
