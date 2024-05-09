<template>

  <div class="animated fadeIn">
    <!-- <div v-if="this.todosItens.length <= 0">
       <h1>Atenção, ainda existem dados carrgando ...</h1>
    </div> -->
    <div v-if="!estaCarregando && naoEncontrado">
      Item não encontrado
    </div>

    <form v-else :class="{ 'was-validated': !estaValido }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="this.todosItens.length <= 0" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="this.todosItens.length <= 0" class="form-loading screen-load">
        <load-placeholder :loading="verificaCarregamento(loadCount, 8)"/>
      </div>

      <div v-if="itemSelecionado.situacao === 'C'" class="situacao can">
        <h5 v-b-toggle.outros-contratos class="title-module">Contrato cancelado</h5>
        <p>{{ itemSelecionado.motivo_cancelamento }}</p>
      </div>

      <div class="body-sector">
        <div class="content-sector sector-roxo-c p-2">
          <h5 class="title-module mb-2">Dados do Contrato</h5>

          <b-row class="form-group">
            <b-col md="4">
              <template v-if="precisaBuscarAluno">
                <label v-help-hint="'form-contrato_busca_aluno'" for="busca_aluno" class="col-form-label">Aluno *</label>
                <typeahead id="busca_aluno" :item-hit="setAluno" :key-name="['pessoa.cnpj_cpf', 'pessoa.nome_contato']" v-model="itemSelecionado.aluno" :invalid="alunoBuscado && $v.itemSelecionado.aluno.$invalid" source-path="/api/aluno/buscar-nome-cpf" selected-key="pessoa.nome_contato" required @blur="buscado()" />
                <div v-if="!estaValido || alunoBuscado && $v.itemSelecionado.aluno.$invalid" class="multiselect-invalid">É preciso selecionar um aluno cadastrado!</div>

              </template>
              <template v-else>
                <label for="aluno" class="col-form-label">Aluno</label>
                <span v-if="!itemSelecionado.aluno" class="form-control">Carregando...</span>
                <input v-else-if="itemSelecionado.aluno.pessoa" id="aluno" :value="itemSelecionado.aluno.pessoa.nome_contato" type="text" class="form-control" readonly>
              </template>
            </b-col>

            <b-col md="2">
              <label v-help-hint="'form-contrato_bolsista'" class="col-form-label d-block">Bolsista</label>
              <b-form-group>
                <b-form-radio-group
                  v-model="itemSelecionado.bolsista"
                  :options="[{text: 'Sim', value: true}, {text: 'Não', value: false}]"
                  :disabled="itemSelecionado.situacao === 'C'"
                  name="bolsista"
                />
              </b-form-group>
            </b-col>

            <b-col md="2">
              <label v-help-hint="'form-contrato_numero_contrato'" for="numero_contrato" class="col-form-label">Número do Contrato</label>
              <input id="numero_contrato" :value="numeroContrato" type="text" class="form-control" readonly>
            </b-col>

            <b-col v-if="itemSelecionado.situacao !== 'C'" md="4">
              <label v-help-hint="'form-contrato_situacao'" for="situacao" class="col-form-label">Situação do Contrato *</label>
              <template v-if="editando">
                <g-select
                  id="situacao"
                  :value="situacaoContrato"
                  :select="setSituacao"
                  :options="situacoes.filter(item => {return item.id !== 'C'})"
                  :class="!estaValido && !itemSelecionado.situacao ? 'invalid-input' : 'valid-input'"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id" />
                <div v-if="!estaValido && !itemSelecionado.situacao" class="multiselect-invalid">
                  Selecione a situação
                </div>
              </template>
              <template v-else>
                <input id="situacao" :value="situacaoContrato.descricao" type="text" class="form-control" readonly>
              </template>
            </b-col>
          </b-row>

          <b-row class="form-group">
            <b-col md="3">
              <label v-help-hint="'form-contrato_tipo_contrato'" for="tipo_contrato" class="col-form-label">Tipo de Contrato *</label>
              <g-select
                id="tipo_contrato"
                :value="tipoContrato"
                :options="tiposContrato"
                :class="!estaValido && !itemSelecionado.tipo_contrato ? 'invalid-input' : 'valid-input'"
                :disabled="itemSelecionado.situacao === 'C'"
                class="multiselect-truncate"
                label="descricao"
                track-by="id"
                @input="setTipoContrato" />
              <div v-if="!estaValido && !itemSelecionado.tipo_contrato" class="multiselect-invalid">
                Selecione o tipo de contrato
              </div>
            </b-col>

            <b-col md="3">
              <label v-help-hint="'form-contrato_modalidade_turma'" for="modalidade_turma" class="col-form-label">Modalidade *</label>
              <g-select
                id="modalidade_turma"
                v-model="itemSelecionado.modalidade_turma"
                :options="listaModalidadesTurma"
                :class="!estaValido && !itemSelecionado.modalidade_turma ? 'invalid-input' : 'valid-input'"
                :disabled="editando"
                class="multiselect-truncate"
                label="descricao"
                track-by="id"
                @input="setModalidadeCurso" />
              <div v-if="!estaValido && !itemSelecionado.modalidade_turma" class="multiselect-invalid">
                Selecione a modalidade
              </div>
            </b-col>
            <b-col v-if="(modalidadeTurmaNaoPreenchida || modalidadeTurmaNaoEhPersonal) && (editando === false)" md="4">
              <label v-help-hint="'form-contrato_turma'" for="turma" class="col-form-label">Turma *</label>
              <div class="d-flex">
                <g-select
                  id="turma"
                  :value="itemSelecionado.turma"
                  :select="setTurma"
                  :options="listaTurmasSemEncerramento"
                  :class="!estaValido && !itemSelecionado.turma ? 'invalid-input' : 'valid-input'"
                  :disabled="itemSelecionado.situacao === 'C'"
                  class="flex-grow-1"
                  label="turmaDescricao"
                  track-by="turmaId"
                  @input="calcularValorCurso" />
                <b-btn v-b-modal.modalTurma v-if="itemSelecionado.situacao !== 'C'" variant="roxo" class="btn-40">...</b-btn>
              </div>
              <div v-if="!estaValido && !itemSelecionado.turma" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </b-col>

            <b-col v-if="editando === true && modalidadeTurmaNaoEhPersonal" md="4">
              <label v-help-hint="'form-contrato_turma'" for="turma" class="col-form-label">Turma</label>
              <div class="d-flex">
                <span v-if="!itemSelecionado.turma" class="form-control">Carregando...</span>
                <input v-else-if="itemSelecionado.turma" id="descricao_turma" :value="itemSelecionado.turma.descricao" type="text" class="form-control" readonly>
              </div>
            </b-col>

            <b-col v-if="modalidadePersonal && editando === false" md="3">
              <label v-help-hint="'form-contrato_quantidade_creditos'" for="quantidade_creditos" class="col-form-label">Quant. de créditos *</label>
              <g-select
                id="quantidade_creditos"
                :value="itemSelecionado.creditos_personal"
                :select="setCreditosPersonal"
                :options="listaCreditosPersonal"
                :required="true"
                :class="!estaValido && !itemSelecionado.creditos_personal ? 'invalid-input' : 'valid-input'"
                label="descricao"
                track-by="id" />
              <div v-if="!estaValido && !itemSelecionado.creditos_personal" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </b-col>

            <b-col v-if="modalidadePersonal && editando === true" md="3">
              <label v-help-hint="'form-contrato_quantidade_creditos'" for="quantidade_creditos" class="col-form-label">Quant. de créditos *</label>
              <div class="d-flex">
                <span v-if="!itemSelecionado.creditos_personal" class="form-control">Carregando...</span>
                <input v-else id="quantidade_creditos" :value="itemSelecionado.creditos_personal.quantidade" type="text" class="form-control" readonly>
              </div>
            </b-col>

            <b-col v-if="modalidadePersonalAvulso && editando === false" md="2">
              <label v-help-hint="'form-contrato_quantidade_creditos_avulsos'" for="quantidade_creditos_avulsos" class="col-form-label">Quant. de créditos avulsos *</label>
              <vue-numeric id="quantidade_creditos_avulsos" :class="!estaValido && !itemSelecionado.creditos_personal_avulso.quantidade ? 'invalid-input' : 'valid-input'" :precision="0" :empty-value="null" :value="itemSelecionado.creditos_personal_avulso.quantidade" :max="999" :min="1" class="form-control" required maxlength="3" @input="setQuantidadeCreditosPersonalAvulso"/>
              <div v-if="!estaValido && !itens[0].numero_parcelas" class="multiselect-invalid">
                Campo obrigatório
              </div>
            </b-col>

            <b-col v-if="modalidadePersonalAvulso && editando === false" md="1">
              <label v-help-hint="'form-contrato_quantidade_creditos_avulsos'" for="quantidade_creditos_avulsos" class="col-form-label">Aulas/semana *</label>
              <vue-numeric id="quantidade_creditos_avulsos" :class="!estaValido && !itemSelecionado.creditos_personal_avulso.aula_por_semana ? 'invalid-input' : 'valid-input'" :precision="0" :empty-value="null" :value="itemSelecionado.creditos_personal_avulso.aula_por_semana" :max="itemSelecionado.creditos_personal_avulso.quantidade" :min="1" class="form-control" required maxlength="2" @input="setQuantidadeAulasPersonalAvulso"/>
              <div v-if="!estaValido && !itens[0].numero_parcelas" class="multiselect-invalid">
                Campo obrigatório
              </div>
            </b-col>

            <!-- TEMPLATE -->

          </b-row>

          <template v-if="modalidadePersonal">
            <b-row class="form-group">

              <b-col md="3">
                <label for="livro_personal" class="col-form-label">Livro *</label>
                <g-select
                  id="livro_personal"
                  :value="itemSelecionado.livro"
                  :options="listaLivros"
                  :required="true"
                  :class="!estaValido && !itemSelecionado.livro ? 'invalid-input' : 'valid-input'"
                  label="descricao"
                  track-by="id"
                  @input="setLivro" />
                <div v-if="!estaValido && !itemSelecionado.livro" class="multiselect-invalid">
                  Selecione uma opção!
                </div>
              </b-col>

              <b-col md="3">
                <label for="semestre_personal" class="col-form-label">Semestre *</label>
                <g-select
                  id="semestre_personal"
                  v-model="itemSelecionado.semestre"
                  :options="listaSemestresPersonal"
                  :required="true"
                  :class="!estaValido && !itemSelecionado.semestre ? 'invalid-input' : 'valid-input'"
                  label="descricao"
                  @input="setSemestre" />
                <div v-if="!estaValido && !itemSelecionado.semestre" class="multiselect-invalid">
                  Selecione uma opção!
                </div>
              </b-col>

              <b-col md="3">
                <label v-help-hint="'form-contrato_selecao_aulas'" v-if="!possuiAgendamentosPersonalSalvos" class="d-block col-form-label">Horário fixo do aluno *</label>
                <label v-help-hint="'form-contrato_selecao_aulas'" v-else class="d-block col-form-label" style="visibility: hidden;">Informações dos agendamentos</label>
                <b-btn v-b-modal.planilhapersonal v-if="personalProgramado" variant="success" block>{{ itemSelecionado.agendamento_personal.length }} aula(s)</b-btn>
                <b-btn v-b-modal.planilhapersonal v-else :variant="possuiAgendamentosPersonalSalvos ? 'success' : 'danger'" block>
                  {{ possuiAgendamentosPersonalSalvos ? 'Informações dos agendamentos' : 'Escolher dias de aula' }}
                </b-btn>
              </b-col>
            </b-row>
          </template>

          <!-- Form PlanilhaPersonal -->
          <b-modal id="planilhapersonal" ref="planilhapersonal" v-model="planilhapersonal"
                   :class="{'naoPossuiAgendamentosPersonal': !possuiAgendamentosPersonalSalvos}"
                   :size="possuiAgendamentosPersonalSalvos ? 'md' : 'xl'"
                   :no-close-on-backdrop="!possuiAgendamentosPersonalSalvos"
                   stacking centered hide-header hide-footer>
            <div v-if="carregandoInformacoesPersonal" class="form-loading">
              <load-placeholder :loading="carregandoInformacoesPersonal" />
            </div>

            <div v-if="!possuiAgendamentosPersonalSalvos">
              <form :class="{ 'was-validated': personalConfirmado }" class="needs-validation form-personal" novalidate @submit.prevent="buscarAgendamentos($event)">

                <b-row class="form-group mb-3">
                  <b-col sm="4" md="">
                    <label for="instrutor_personal" class="col-form-label">Instrutor *</label>
                    <g-select
                      id="instrutor_personal"
                      v-model="itemSelecionado.instrutor"
                      :options="listaInstrutoresPersonal"
                      :class="personalConfirmado && !itemSelecionado.instrutor ? 'invalid-input' : 'valid-input'"
                      :select="resetAgendamentosPersonal"
                      required
                      label="apelido"
                      track-by="id" />
                    <div v-if="personalConfirmado && !itemSelecionado.instrutor" class="multiselect-invalid">
                      Selecione uma opção!
                    </div>
                  </b-col>

                  <b-col sm="4" md="">
                    <label for="sala_franqueada_personal" class="col-form-label">Sala *</label>
                    <g-select
                      id="sala_franqueada_personal"
                      v-model="itemSelecionado.sala_franqueada"
                      :options="listaSalasPersonal"
                      :select="resetAgendamentosPersonal"
                      :class="personalConfirmado && !itemSelecionado.sala_franqueada ? 'invalid-input' : 'valid-input'"
                      required
                      label="descricao"
                      track-by="id" />
                    <div v-if="personalConfirmado && !itemSelecionado.sala_franqueada" class="multiselect-invalid">
                      Selecione uma opção!
                    </div>
                  </b-col>

                  <b-col sm="2">
                    <label for="filtro_data_personal" class="col-form-label">Data *</label>
                    <g-datepicker v-model="dataFiltroPersonal" element-id="filtro_data_personal" />
                  </b-col>

                  <b-col md="auto">
                    <label class="col-form-label d-block">&nbsp;</label>
                    <b-btn type="submit" variant="roxo" block>Buscar agendamentos</b-btn>
                  </b-col>
                </b-row>

                <div v-if="!buscaPersonal" class="list-group-accent mb-3">
                  <div class="list-group-item list-group-item-accent-info list-group-item-info border-0">
                    <font-awesome-icon icon="info-circle" /> Selecione o <b>instrutor</b> e a <b>sala</b> para prosseguir com a programação das aulas personal.
                  </div>
                </div>
              </form>

              <template v-if="buscaPersonal && !carregandoInformacoesPersonal">
                <p v-if="!filtrarNovamente" class="mb-1">Selecione abaixo os horários para este contrato <b>personal</b>:</p>

                <g-personal :dates="weekDates" @adicionar-agendamento="adicionarAgendamento" />
                
                <div class="form-group">
                  <!-- "quantidade_creditos": 32, "aula_por_semana": 2  -->
                  <label class="col-form-label">Datas e horários selecionados: 
                    <span v-if="itemSelecionado.creditos_personal">(Créditos: {{ itemSelecionado.creditos_personal.quantidade_creditos }} | Horários restantes: {{ horariosRestantes }})
                    </span>
                  </label>

                  <div v-if="!itemSelecionado.agendamento_personal.length && !filtrarNovamente">
                    Selecione os horários na planilha acima.
                  </div>

                  <div v-if="filtrarNovamente" class="list-group-item list-group-item-accent-warning list-group-item-warning border-0 mt-2">
                    Busque os agendamentos confome os novos parâmetros antes de realizar esta configuração.
                  </div>

                  <div v-if="!filtrarNovamente">
                    <b-btn v-for="(agendamento, index) in itemSelecionado.agendamento_personal" :key="index" class="mr-2" variant="roxo" @click="removerAgendamento(index)">
                      {{ agendamento.inicio | formatarDataDiaSemana(true) }} &times;
                    </b-btn>

                    <div v-if="personalConfirmado && !itemSelecionado.agendamento_personal.length" class="list-group-item list-group-item-accent-danger list-group-item-danger border-0">
                      Selecione os horários personal.
                    </div>

                    <!-- <div v-if="!personalProgramado" class="list-group-item list-group-item-accent-warning list-group-item-warning border-0 mt-2">
                      Créditos: {{ itemSelecionado.creditos_personal.quantidade_creditos }} | Horários restantes: {{ horariosRestantes() }}
                    </div> -->
                  </div>
                </div>
              </template>

              <b-row class="form-group mb-0 mt-auto">
                <b-col sm="" md="auto" class="pr-0">
                  <b-btn :disabled="filtrarNovamente" variant="verde" @click="validarProgramacaoPersonal()">Confirmar</b-btn>
                  <b-btn variant="link" @click="resetAgendamentosPersonal(!personalProgramado)">{{ personalProgramado ? 'Redefinir horários' : 'Cancelar' }}</b-btn>
                </b-col>
              </b-row>
            </div>
            <div v-else>
              <div class="form-group pt-2 pl-2" style="font-size: 0.875rem;">
                <strong>Instrutor:</strong> {{ personalSalvoNomeInstrutor }}
                <br><strong>Sala:</strong> {{ personalSalvoDescricaoSala }}
                <br><strong>Data início:</strong> {{ personalSalvoDataInicio }}
                <br><strong>Data fim:</strong> {{ personalSalvoDataFim }}
                <br><strong>Dia da semana/horarios:</strong>
                <template v-for="(item, index) in diasHoras" >
                  <br :key="index">{{ item.dia }} {{ converteHorarioBancoParaInputText(item.timestamp) }}
                </template>
              </div>
              <div class="form-group pt-2">
                <b-btn variant="link" @click="voltarModalPersonal">Sair</b-btn>
              </div>
            </div>

          </b-modal>

          <b-row class="form-group">
            <b-col md="3">
              <label v-help-hint="'form-contrato_responsavel_venda_funcionario'" for="responsavel_venda_funcionario" class="col-form-label">Responsável da Venda *</label>
              <g-select
                id="responsavel_venda_funcionario"
                :value="itemSelecionado.responsavel_venda_funcionario"
                :select="setResponsavelVendaFuncionario"
                :options="listaFuncionarios"
                :class="!estaValido && !itemSelecionado.responsavel_venda_funcionario ? 'invalid-input' : 'valid-input'"
                :disabled="itemSelecionado.situacao === 'C'"
                class="multiselect-truncate"
                label="apelido"
                track-by="id" />
              <div v-if="!estaValido && !itemSelecionado.responsavel_venda_funcionario" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </b-col>

            <b-col md="3">
              <label v-help-hint="'form-responsavel_carteira_funcionario'" for="responsavel_carteira_funcionario" class="col-form-label">Responsável da Carteira *</label>
              <g-select
                id="responsavel_carteira_funcionario"
                :value="itemSelecionado.responsavel_carteira_funcionario"
                :select="setResponsavelCarteiraFuncionario"
                :options="listaFuncionarios"
                :class="!estaValido && !itemSelecionado.responsavel_carteira_funcionario ? 'invalid-input' : 'valid-input'"
                :disabled="itemSelecionado.situacao === 'C'"
                class="multiselect-truncate"
                label="apelido"
                track-by="id" />
              <div v-if="!estaValido && !itemSelecionado.responsavel_carteira_funcionario" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </b-col>

            <b-col md="6">
              <label v-help-hint="'form-contrato_responsavel_financeiro_pessoa'" for="responsavel_financeiro_pessoa" class="col-form-label">Contratante *</label>
              <input id="responsavel_financeiro_pessoa" :value="itemSelecionado.responsavel_financeiro_pessoa ? itemSelecionado.responsavel_financeiro_pessoa.nome : 'Carregando...'" type="text" class="form-control" readonly>
            </b-col>
          </b-row>

          <div v-if="itemSelecionado.aluno && itemSelecionado.aluno.contratos.length > 1" class="content-sector-extra p-2">
            <h5 v-b-toggle.outros-contratos class="title-module d-flex collapse-toggle">Contratos do aluno ({{ itemSelecionado.aluno.contratos.length }})<font-awesome-icon icon="caret-up" class="ml-auto my-auto collapse-toggle-state" /></h5>

            <b-collapse id="outros-contratos" visible>
              <b-row class="header-card-list mt-2">
                <b-col md="1">
                  <label class="col-form-label">Contrato</label>
                </b-col>
                <b-col md="2">
                  <label class="col-form-label">Início</label>
                </b-col>
                <b-col md="2">
                  <label class="col-form-label">Término</label>
                </b-col>
                <b-col md="1">
                  <label class="col-form-label">Idioma</label>
                </b-col>
                <b-col md="2">
                  <label class="col-form-label">Curso</label>
                </b-col>
                <b-col md="2">
                  <label class="col-form-label">Livro</label>
                </b-col>
                <b-col md="1">
                  <label class="col-form-label"></label>
                </b-col>
              </b-row>

              <b-row v-for="(contrato, index) in itemSelecionado.aluno.contratos" :key="index" class="body-card-list">
                <b-col md="1" data-header="Contrato">{{ itemSelecionado.aluno.id }}/{{ contrato.sequencia_contrato }}</b-col>

                <b-col md="2" data-header="Início">{{ contrato.data_inicio_contrato | formatarData }}</b-col>
                <b-col md="2" data-header="Térimino"><span class="badge date-payment align-middle rounded">{{ contrato.data_termino_contrato | formatarData }}</span></b-col>

                <template v-if="contrato.curso">
                  <b-col md="1" data-header="Idioma">{{ contrato.curso ? contrato.curso.idioma.descricao : '' }}</b-col>
                  <b-col md="2" data-header="Curso">{{ contrato.curso ? contrato.curso.descricao : '' }}</b-col>
                  <b-col md="2" data-header="Livro">{{ contrato.livro.descricao }}</b-col>
                </template>

                <template v-else>
                  <b-col md="1" data-header="Idioma"/>
                  <b-col md="2" data-header="Curso"/>
                  <b-col md="2" data-header="Livro">{{ contrato.livro.descricao }}</b-col>
                </template>

                <b-col md="1">
                  <span v-b-tooltip.viewport.left.hover :title="situacoes.filter(sit => sit.id === contrato.situacao)[0].descricao" :class="'circle-badge-' + contrato.situacao.toLowerCase()" class="circle-badge"></span>
                </b-col>

                <b-col md="1">
                  <b-btn v-b-tooltip :href="ehContratoSelecionado(itemSelecionado.aluno.id, contrato.sequencia_contrato) ? '#' : `/cadastros/contrato/atualizar/${contrato.id}`" :title="ehContratoSelecionado(itemSelecionado.aluno.id, contrato.sequencia_contrato) ? 'Visualizando': 'Detalhes do contrato'" :class="ehContratoSelecionado(itemSelecionado.aluno.id, contrato.sequencia_contrato) ? 'icone-link clear' : 'icone-link clear color-blue'" variant="white" style="min-width: 100%; width: 100%; padding-right: 0; padding-left: 0;" target="_self" rel="noreferrer">
                    <font-awesome-icon icon="file" />
                  </b-btn>
                </b-col>
              </b-row>

            </b-collapse>
          </div>
        </div>

        <template v-if="!editando">
          <div class="content-sector sector-verde-c p-2">
            <h5 class="title-module mb-2">Taxa de Matrícula</h5>
            <b-row class="form-group">
              <b-col md="4">
                <label v-help-hint="'form-contrato_itens_forma_cobranca_taxa_matricula'" for="itens[0].forma_cobranca" class="col-form-label">Forma de cobrança {{ itens[0].valor ? '*' : '' }}</label>
                <g-select
                  id="itens[0].forma_cobranca"
                  :value="itens[0].forma_cobranca"
                  :select="setFormaCobranca"
                  :extra-param="0"
                  :options="listaFormasPagamento"
                  :class="!estaValido && !itens[0].forma_cobranca && itens[0].valor ? 'invalid-input' : 'valid-input'"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id" />
                <div v-if="!estaValido && !itens[0].forma_cobranca && itens[0].valor" class="multiselect-invalid">
                  Selecione uma opção
                </div>
              </b-col>

              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_valor_taxa_matricula'" for="itens[0].valor" class="col-form-label">Valor total *</label>
                <vue-numeric v-if="itens[0]" id="valor[0]" :class="{ 'valid-input': estaValido }" :precision="2" :empty-value="null" v-model="itens[0].valor" :max="9999999.99" separator="." class="form-control" required @input="calcularValoresEParcelasItem('valor', 0)" />
                <input 
                class="form-control" 
                disabled 
                v-if="!itens[0]" type="text" value="Carregando ..."
                >
              </b-col>

              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_numero_parcelas_taxa_matricula'" for="itens[0].numero_parcelas" class="col-form-label">Número de parcelas *</label>
                <vue-numeric id="itens[0].numero_parcelas" :class="!estaValido && !itens[0].numero_parcelas ? 'invalid-input' : 'valid-input'" :precision="0" :empty-value="null" v-model="itens[0].numero_parcelas" :max="maximoParcelas" :min="1" separator="." class="form-control" required maxlength="2" @input="calcularValoresEParcelasItem('numero_parcelas', 0)" />
                <div v-if="!estaValido && !itens[0].numero_parcelas" class="multiselect-invalid">
                  Campo obrigatório
                </div>
              </b-col>
              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_valor_parcela_taxa_matricula'" for="itens[0].valor_parcela" class="col-form-label">Valor da parcela *</label>
                <vue-numeric 
                id="itens[0].valor_parcela" 
                :class="{ 'valid-input': !estaValido }" 
                :precision="2" 
                :empty-value="null" 
                v-model="itens[0].valor_parcela" 
                :max="9999999.99" 
                separator="." 
                class="form-control" 
                required 
                @input="calcularValoresEParcelasItem('valor_parcela', 0)"
                v-if="itens[0]" 
                />
                <input 
                class="form-control" 
                disabled 
                v-if="!itens[0]" type="text" value="Carregando ..."
                >
                </b-col>
            </b-row>

            <b-row>
              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_data_vencimento_taxa_matricula'" class="col-form-label" for="itens[0].data_vencimento">Vencimento *</label>
                <g-datepicker 
                :value="itens[0].data_vencimento" 
                :selected="setDataVencimento" 
                :extra-param="0" 
                :min-date="minDate" 
                element-id="data_vencimento[0]" 
                maxlength="10" 
                required />
                <div v-if="!estaValido && !itens[0].data_vencimento" 
                class="multiselect-invalid">
                  Selecione uma data
                </div>
              </b-col>

              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_dias_subsequentes_taxa_matricula'" class="col-form-label" for="itens[0].dias_subsequentes">Dias subsequentes {{ itens[0].numero_parcelas > 1 ? '*' : '' }}</label>
                <g-select
                  id="itens[0].dias_subsequentes"
                  :value="itens[0].dias_subsequentes"
                  :select="setDiasSubsequentes"
                  :extra-param="0"
                  :options="listaDiasSubsequentes"
                  :class="!estaValido && itens[0].numero_parcelas > 1 && (!itens[0].dias_subsequentes || !itens[0].dias_subsequentes.id) ? 'invalid-input' : 'valid-input'"
                  :disabled="itens[0].numero_parcelas === 1"
                  label="descricao"
                  class="multiselect-truncate" />
                <div v-if="!estaValido && itens[0].numero_parcelas > 1 && (!itens[0].dias_subsequentes || !itens[0].dias_subsequentes.id)" class="multiselect-invalid">
                  Selecione uma opção
                </div>
              </b-col>

              <b-col md="6">
                <label v-help-hint="'form-contrato_itens_plano_conta_taxa_matricula'" for="itens[0].plano_conta" class="col-form-label">Plano de Contas *</label>
                <input 
                :value="itens[0].plano_conta ? itens[0].plano_conta.descricao : null" 
                type="text" 
                class="form-control" 
                disabled>
              </b-col>
            </b-row>
          </div>

          <div class="content-sector sector-roxo-c p-2">
            <h5 class="title-module mb-2">Valor do Curso</h5>

            <b-row class="form-group">
              <b-col md="4">
                <label v-help-hint="'form-contrato_itens_forma_cobranca'" for="itens[1].forma_cobranca" class="col-form-label">Forma de cobrança *1</label>
                <g-select
                  id="itens[1].forma_cobranca"
                  :value="itens[1].forma_cobranca"
                  :select="setFormaCobranca"
                  :extra-param="1"
                  :options="listaFormasPagamento"
                  :class="!estaValido && !itens[1].forma_cobranca ? 'invalid-input' : 'valid-input'"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id" />
                <div v-if="!estaValido && !itens[1].forma_cobranca && itens[1].valor" class="multiselect-invalid">
                  Selecione uma opção
                </div>
              </b-col>

              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_valor'" for="itens[1].valor" class="col-form-label">Valor total *</label>
                <vue-numeric 
                id="valor[1]" 
                :class="!estaValido && !itens[1].valor ? 'invalid-input' : 'valid-input'" 
                :precision="2" 
                :empty-value="null" 
                :read-only="!itemSelecionado.bolsista" 
                v-model="itens[1].valor" 
                :max="9999999.99"
                read-only-class="form-control form-control-disabled" 
                separator="." class="form-control" 
                required 
                @input="calcularValoresEParcelasItem('valor', 1)"/>
                <div v-if="!estaValido && !itens[1].valor" class="multiselect-invalid">
                  Campo obrigatório
                </div>
              </b-col>

              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_numero_parcelas'" for="itens[1].numero_parcelas" class="col-form-label">Número de parcelas *</label>
                <vue-numeric id="itens[1].numero_parcelas" :class="!estaValido && !itens[1].numero_parcelas ? 'invalid-input' : 'valid-input'" :precision="0" :empty-value="null" v-model="itens[1].numero_parcelas" :max="maximoParcelas" :min="1" separator="." class="form-control" required maxlength="2" @input="calcularValoresEParcelasItem('numero_parcelas', 1)" />
                <div v-if="!estaValido && !itens[1].numero_parcelas" class="multiselect-invalid">
                  Campo obrigatório
                </div>
              </b-col>

              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_valor_parcela'" for="itens[1].valor_parcela" class="col-form-label">Valor da parcela *</label>
                <vue-numeric 
                id="itens[1].valor_parcela" 
                :class="!estaValido && !itens[1].valor_parcela ? 'invalid-input' : 'valid-input'" 
                :precision="2" 
                :empty-value="null" 
                v-model="itens[1].valor_parcela" 
                :max="9999999.99" separator="." 
                class="form-control" 
                required 
                @input="calcularValoresEParcelasItem('valor_parcela', 1)" 
                />
                <div v-if="!estaValido && !itens[1].valor_parcela" class="multiselect-invalid">
                  Campo obrigatório
                </div>
              </b-col>

            </b-row>

            <b-row class="form-group">
              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_data_vencimento'" class="col-form-label" for="itens[1].data_vencimento">Vencimento *</label>
                <g-datepicker :value="itens[1].data_vencimento" :selected="setDataVencimento" :min-date="minDate" :extra-param="1" element-id="data_vencimento[1]" maxlength="10" required />
                <div v-if="!estaValido && !itens[1].data_vencimento" class="multiselect-invalid">
                  Selecione uma data
                </div>
              </b-col>

              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_dias_subsequentes'" class="col-form-label" for="itens[1].dias_subsequentes">Dias subsequentes {{ itens[1].numero_parcelas > 1 ? '*' : '' }}</label>
                <g-select
                  id="itens[1].dias_subsequentes"
                  :value="itens[1].dias_subsequentes"
                  :select="setDiasSubsequentes"
                  :extra-param="1"
                  :options="listaDiasSubsequentes"
                  :class="!estaValido && itens[1].numero_parcelas > 1 && (!itens[1].dias_subsequentes || !itens[1].dias_subsequentes.id) ? 'invalid-input' : 'valid-input'"
                  :disabled="itens[1].numero_parcelas === 1"
                  label="descricao"
                  class="multiselect-truncate" />
                <div v-if="!estaValido && itens[1].numero_parcelas > 1 && (!itens[1].dias_subsequentes || !itens[1].dias_subsequentes.id)" class="multiselect-invalid">
                  Selecione uma opção
                </div>
              </b-col>

              <b-col md="4">
                <label v-help-hint="'form-contrato_itens_plano_conta'" for="itens[1].plano_conta" class="col-form-label">Plano de Contas *</label>
                <g-treeselect
                  id="itens[1].plano_conta"

                  :value="itens[1].plano_conta"
                  :input="setPlanoConta"
                  :extra-param="1"

                  :options="listaPlanosConta"
                  :invalid="!estaValido && !itens[1].plano_conta"
                  required
                />

                <div v-if="!estaValido && !itens[1].plano_conta" class="multiselect-invalid">
                  Selecione uma opção
                </div>
              </b-col>

              <template v-if="itemSelecionado.aluno">
                <b-col v-if="(objFranqueada.desconto_super_amigos_ativo || objFranqueada.desconto_super_amigos_turbinado_ativo) && !editando && !itemSelecionado.aluno.contratos.length" md="2">
                  <div v-help-hint="'form-contrato_desconto_super_amigos'" class="col-form-label">&nbsp;</div>
                  <b-button v-b-modal.modalSuperAmigo v-if="!editando" :variant="descontoSuperAmigoAplicado ? 'verde' : ''" class="btn btn-160 ">
                    <font-awesome-icon icon="hand-scissors"/> Super Amigo
                  </b-button>
                </b-col>
              </template>
            </b-row>

            <b-row class="form-group">
              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_desconto'" for="itens[1].desconto" class="col-form-label">Desconto</label>
                <g-select
                  id="itens[1].desconto"
                  :value="itens[1].desconto"
                  :select="setDesconto"
                  :extra-param="1"
                  :options="descontosValorCurso"
                  :prevent-nullable="true" />
              </b-col>
              <b-col v-if="itens[1].desconto === 'Desconto Especial'" md="2">
                <!-- <pre>
                {{ permissoes['DESCONTO_SUPERVISIONADO'] }}
                </pre> -->
                <label v-help-hint="'form-valor_desconto_especial'" for="valor_desconto_especial" class="col-form-label">Valor desconto especial</label>
                <input v-money="moeda" v-input-locker="{permissao: permissoes['DESCONTO_SUPERVISIONADO']}" id="valor_desconto_supervisionado" v-model="itens[1].valor_desconto_especial" :max="valor_original_curso" type="text" class="form-control" @blur="calcularDescontos(1)">
                <!-- <label v-help-hint="'form-valor_desconto_especial'" class="col-form-label">Motivo desconto especial</label>
                <textarea id="observacao" v-model="itemSelecionado.observacao" class="form-control"></textarea> -->
          </b-col>

              <b-col v-if="objFranqueada.percentual_desconto_a_vista" md="2">
                <div v-help-hint="'form-contrato_itens_desconto_a_vista'" class="col-form-label">&nbsp;</div>
                <b-form-checkbox v-model="desconto_avista" @change="setValorDescontoAvista">Desconto à vista</b-form-checkbox>
              </b-col>

              <!-- <b-col v-if="desconto_avista === true" md="2"> -->
              <!--   <label v-help-hint="'form-valor_desconto_avista'" for="valor_desconto_avista" class="col-form-label">Valor desconto à vista</label> -->
              <!--   <input v-model="objFranqueada.percentual" type="number" readonly class="form-control" > -->
              <!-- </b-col> -->

              <b-col md="2">
                <label v-help-hint="'form-contrato_itens_valor_total_desconto'" for="itens[1].valor_total_desconto" class="form-label">Valor total com desconto</label>
                <vue-numeric id="valor_total_desconto" :precision="2" :empty-value="null" v-model="itens[1].valor_total_desconto" :max="9999999.99" separator="." class="form-control" disabled />
              </b-col>

              <b-col md="2">
                <label v-help-hint="'form-contrato_valor_parcela_desconto'" for="itens[1].valor_parcela_desconto" class="form-label">Valor da parcela com desconto</label>
                <vue-numeric id="valor_parcela_desconto" :precision="2" :empty-value="null" v-model="itens[1].valor_parcela_desconto" :max="9999999.99" separator="." class="form-control" disabled />
              </b-col>
            </b-row>
            
            <b-col v-if="itens[1].desconto === 'Desconto Especial'" md="10">
                <label v-help-hint="'form-valor_desconto_especial'" class="col-form-label">Motivo desconto especial</label>
                <textarea id="observacao" v-model="itemSelecionado.observacao" class="form-control full-textarea" rows="2" maxLength="5000"></textarea>
              </b-col>
			  
            <b-row v-if="descontoInformarFamiliar === true || descontoInformarConvenio === true">
              <b-col v-if="descontoInformarFamiliar" md="4">
                <label v-help-hint="'form-contrato_familiar_desconto'" for="familiar_desconto" class="form-label">Nome do familiar *</label>
                <input id="familiar_desconto" v-model="itemSelecionado.familiar_desconto" type="text" class="form-control" maxlength="255" required>
                <div v-if="!estaValido && !itemSelecionado.familiar_desconto" class="multiselect-invalid">
                  Campo obrigatório
                </div>
              </b-col>

              <b-col v-if="descontoInformarConvenio" md="4">
                <label v-help-hint="'form-contrato_convenio_desconto'" for="convenio_desconto" class="form-label">Busque um convênio *</label>
                <typeahead id="convenio_desconto" :item-hit="setConvenioDesconto"
                           :key-name="['pessoa.nome_fantasia', 'pessoa.razao_social', 'pessoa.nome_contato']"
                           v-model="itemSelecionado.convenio_desconto"
                           :class-validate="{'is-invalid': !estaValido && (!itemSelecionado.convenio_desconto || !itemSelecionado.convenio_desconto.id)}"
                           :selected-key="['pessoa.nome_fantasia','pessoa.razao_social']"
                           source-path="/api/convenio/buscar-ativos-nome"
                           required />
                <div v-if="!estaValido && (!itemSelecionado.convenio_desconto || !itemSelecionado.convenio_desconto.id)" class="multiselect-invalid">
                  Selecione um convênio
                </div>
              </b-col>
            </b-row>
          </div>

          <div class="content-sector sector-laranja p-3">
            <h5 class="title-module mb-2">Material Didático</h5>

            <template v-for="(itemContaReceber, index) in itens">
              <div :key="index" v-if="index > 1" class="lista-item">
                <b-row class="form-group">
                  <b-col md="4">
                    <label :for="`item[${index}].item`" class="col-form-label">Item *</label>

                    <template v-if="modalidadePersonal || index > 2">
                      <g-select
                        :id="`item[${index}].item`"
                        :value="itemContaReceber.item"
                        :select="setItem"
                        :extra-param="index"
                        :options="listaItens"
                        :class="!estaValido && !itemContaReceber.item ? 'invalid-input' : 'valid-input'"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id" />
                      <div v-if="!estaValido && !itemContaReceber.item" class="multiselect-invalid">
                        Selecione uma opção
                      </div>
                    </template>

                    <template v-else>
                      <label v-help-hint="'form-contrato_item'"></label>
                      <input :value="itemContaReceber.item ? itemContaReceber.item.descricao : null" type="text" class="form-control" readonly>
                    </template>
                  </b-col>

                  <b-col md="2">
                    <div v-help-hint="'form-contrato_entrege'" class="col-form-label">&nbsp;</div>
                    <b-form-checkbox v-model="itemContaReceber.item_entregue">Entregue</b-form-checkbox>
                  </b-col>

                </b-row>

                <b-row class="form-group">
                  <b-col md="4">
                    <label v-help-hint="'form-contrato_itemContaReceber_forma_cobranca'" for="itemContaReceber.forma_cobranca" class="col-form-label">Forma de cobrança *</label>
                    <g-select
                      id="itemContaReceber.forma_cobranca"
                      :value="itemContaReceber.forma_cobranca"
                      :select="setFormaCobranca"
                      :extra-param="index"
                      :options="listaFormasPagamento"
                      :class="!estaValido && !itemContaReceber.forma_cobranca ? 'invalid-input' : 'valid-input'"
                      class="multiselect-truncate"
                      label="descricao"
                      track-by="id" />
                    <div v-if="!estaValido && !itemContaReceber.forma_cobranca" class="multiselect-invalid">
                      Selecione uma opção
                    </div>
                  </b-col>

                  <b-col md="2">
                    <label v-help-hint="'form-contrato_valor_item'" :for="`item[${index}].valor`" class="col-form-label">Valor do Item *</label>
                    <template v-if="!editando">
                      <vue-numeric v-input-locker="verificaCampoBloqueado()" :id="`item[${index}].valor`" :precision="2" :empty-value="null" v-model="itemContaReceber.valor" :max="9999999.99" separator="." class="form-control valid-input" required @input="calcularValoresEParcelasItem('valor', index)" />
                    </template>
                    <span v-else class="d-block text-muted">{{ itemContaReceber.valor | formatarNumero }}</span>
                  </b-col>

                  <b-col md="2">
                    <label v-help-hint="'form-contrato_itemContaReceber.numero_parcelas'" :for="`numero_parcelas_${index}`" class="col-form-label">Número de parcelas *</label>

                    <template v-if="(!editando || !itemContaReceber.numero_parcelas) && (habilitouCampo === false)">
                      <g-select
                        :id="`numero_parcelas_${index}`"
                        :value="itemContaReceber.numero_parcelas"
                        :select="setNumeroParcela"
                        :extra-param="index"
                        :options="filtroParcelas(itemContaReceber)"
                        :prevent-nullable="true"
                        :class="!estaValido && !itemContaReceber.numero_parcelas ? 'invalid-input' : 'valid-input'"
                        class="multiselect-truncate" />
                    </template>
                    <template v-else-if="(!editando || !itemContaReceber.numero_parcelas) && (habilitouCampo === true)">
                      <vue-numeric :id="`numero_parcelas_${index}`" :class="!estaValido && !itemContaReceber.numero_parcelas ? 'invalid-input' : 'valid-input'" :precision="0" :empty-value="null" v-model="itemContaReceber.numero_parcelas" :max="maximoParcelas" :min="1" separator="." class="form-control" required maxlength="1" @input="calcularValoresEParcelasItem('numero_parcelas', index)" />
                    </template>
                    <span v-else class="d-block text-muted">{{ itemContaReceber.numero_parcelas | formatarNumero }}</span>
                    <div v-if="!estaValido && !itemContaReceber.numero_parcelas" class="multiselect-invalid">
                      Campo obrigatório
                    </div>
                  </b-col>

                  <b-col md="2">
                    <label v-help-hint="'form-contrato_itemContaReceber.valor_parcela'" for="itemContaReceber.valor_parcela" class="col-form-label">Valor da parcela *</label>
                    <vue-numeric v-input-locker="verificaCampoBloqueado()" id="itemContaReceber.valor_parcela" :precision="2" :empty-value="null" v-model="itemContaReceber.valor_parcela" :max="9999999.99" separator="." class="form-control" @input="calcularValoresEParcelasItem('valor_parcela', index)"/>
                  </b-col>

                </b-row>

                <b-row>
                  <b-col md="2">
                    <label v-help-hint="'form-contrato_data_vencimento'" class="col-form-label" for="itemContaReceber.data_vencimento">Vencimento *</label>
                    <g-datepicker :value="itemContaReceber.data_vencimento" :selected="setDataVencimento" :min-date="minDate" :extra-param="index" element-id="itemContaReceber.data_vencimento" maxlength="10" required />
                    <div v-if="!estaValido && !itemContaReceber.data_vencimento" class="multiselect-invalid">
                      Selecione uma data
                    </div>
                  </b-col>

                  <b-col md="2">
                    <label v-help-hint="'form-contrato_itemContaReceber.dias_subsequentes'" class="col-form-label" for="itemContaReceber.dias_subsequentes">Dias subsequentes {{ itemContaReceber.numero_parcelas > 1 ? '*' : '' }}</label>
                    <g-select
                      id="itemContaReceber.dias_subsequentes"
                      :value="itemContaReceber.dias_subsequentes"
                      :select="setDiasSubsequentes"
                      :extra-param="index"
                      :options="listaDiasSubsequentes"
                      :class="!estaValido && itemContaReceber.numero_parcelas > 1 && (!itemContaReceber.dias_subsequentes || !itemContaReceber.dias_subsequentes.id) ? 'invalid-input' : 'valid-input'"
                      :disabled="itemContaReceber.numero_parcelas === 1"
                      label="descricao"
                      class="multiselect-truncate" />
                    <div v-if="!estaValido && itemContaReceber.numero_parcelas > 1 && (!itemContaReceber.dias_subsequentes || !itemContaReceber.dias_subsequentes.id)" class="multiselect-invalid">
                      Selecione uma opção
                    </div>
                  </b-col>

                  <b-col md="6">
                    <label v-help-hint="'form-contrato_itemContaReceber.plano_conta'" :for="`itemContaReceber.plano_conta`" class="col-form-label">Plano de Contas *</label>
                    <g-treeselect
                      :id="`itemContaReceber.plano_conta`"

                      :value="itemContaReceber.plano_conta"
                      :input="setPlanoConta"
                      :extra-param="index"

                      :options="listaPlanosConta"
                      :invalid="!estaValido && !itemContaReceber.plano_conta"
                      required
                    />

                    <div v-if="!estaValido && !itemContaReceber.plano_conta" class="multiselect-invalid">
                      Selecione uma opção
                    </div>
                  </b-col>

                  <b-col md="2">
                    <div class="col-form-label">&nbsp;</div>
                    <b-btn v-if="index > 2" variant="light" class="btn-40" @click="removerItem(index)">
                      <font-awesome-icon icon="minus" />
                    </b-btn>

                    <b-btn v-if="index === (itens.length - 1)" variant="azul" class="btn-40" @click="adicionarItem()">
                      <font-awesome-icon icon="plus" />
                    </b-btn>
                  </b-col>
                </b-row>
              </div>
            </template>
          </div>
        </template>

        <template v-else-if="editando && itens && estaCarregando === false">
          <div class="content-sector sector-vinho-p p-3">
            <h5 class="title-module mb-2">Itens do Contrato</h5>

            <b-row v-for="(itemContaReceber, indexItem) in itens" :key="indexItem" class="form-group align-items-center">
              <b-col md="3">
                <label v-if="indexItem === 0" :id="'item_contrato_descricao'+indexItem" class="col-form-label">Item</label>
                <input :id="'item_contrato_descricao'+indexItem" :value="itemContaReceber.item ? itemContaReceber.item.descricao : null" type="text" class="form-control" disabled>
              </b-col>

              <b-col md="3">
                <label v-if="indexItem === 0" :id="'plano_contas_descricao_'+indexItem" class="col-form-label">Plano de Contas</label>
                <input :id="'plano_contas_descricao_'+indexItem" :value="itemContaReceber.plano_conta.descricao" type="text" class="form-control" disabled>
              </b-col>

              <b-col md="2">
                <label v-if="indexItem === 0" :id="'item_contrato_valor_original__'+indexItem" class="col-form-label">Valor bruto</label>
                <vue-numeric :id="'item_contrato_valor_original__'+indexItem" :precision="2" :value="itemValorBruto(itemContaReceber)" :max="9999999.99" :disabled="true" separator="." class="form-control" />
              </b-col>

              <b-col md="1">
                <label v-if="indexItem === 0" :id="'item_contrato_desconto_'+indexItem" class="col-form-label">Descontos</label>
                <vue-numeric :id="'item_contrato_desconto_'+indexItem" :precision="2" :value="itemValorDesconto(itemContaReceber)" :max="9999999.99" :disabled="true" separator="." class="form-control" />
              </b-col>

              <b-col md="2">
                <label v-if="indexItem === 0" :id="'item_contrato_valor_contratado_'+indexItem" class="col-form-label">Valor líquido dos itens</label>
                <vue-numeric :id="'item_contrato_valor_contratado_'+indexItem" :precision="2" :value="itemValorLiquido(itemContaReceber)" :max="9999999.99" :disabled="true" separator="." class="form-control" />
              </b-col>

              <b-col v-if="indexItem >= 2" md="1">
                <b-form-checkbox :id="'item_contrato_entregue_'+indexItem" :checked="itemContaReceber.item_entregue" disabled>Entregue</b-form-checkbox>
              </b-col>
            </b-row>

            <b-row class="form-group align-items-center">
              <b-col md="3"/>

              <b-col md="3" style="text-align: right;">
                Totais bruto/descontos/líquido
              </b-col>

              <b-col md="2">
                <vue-numeric :precision="2" :value="totalItensBruto" :max="9999999.99" :disabled="true" separator="." class="form-control" />
              </b-col>

              <b-col md="1">
                <vue-numeric :precision="2" :value="totalItensDescontos" :max="9999999.99" :disabled="true" separator="." class="form-control" />
              </b-col>

              <b-col md="2">
                <vue-numeric :precision="2" :value="totalItensLiquido" :max="9999999.99" :disabled="true" separator="." class="form-control" />
              </b-col>

            </b-row>
          </div>
        </template>

        <titulos-conta-receber :esta-valido="estaValido" :editando="editando" :btn-imprimir-boletos="false" @gerar-parcelas="prepararParametros" />
      </div>

      <div class="row mb-3">
        <div class="col-md-6 form-group">
          <b-btn v-if="itemSelecionado.situacao !== 'C'" :disabled="salvando" type="submit" variant="verde">
            <template v-if="salvando">Salvando...</template>
            <template v-else-if="editando">Salvar</template>
            <template v-else>Finalizar e imprimir contrato</template>
          </b-btn>
          <b-btn v-if="editando" variant="roxo" @click="$refs.modalImpressao.show()">Imprimir contrato</b-btn>

          <b-btn v-if="editando && titulosReceber.filter(item => { return item.forma_cobranca.forma_boleto === true }).length" variant="primary" class="m-auto" target="_blank" @click="imprimirBoletos(true)">Imprimir boletos</b-btn>

          <b-btn variant="link" @click="voltar()">Voltar</b-btn>
        </div>

        <div class="col-md-6">
          <b-btn v-b-modal.modalCancelarContrato v-if="editando && itemSelecionado.situacao !== 'C'" class="d-flex ml-auto" variant="outline-danger">Cancelar contrato</b-btn>
        </div>
      </div>
    </form>

    <b-modal id="modalTurma" ref="modalTurma" size="xl" title="Selecionar turma" hide-footer no-close-on-backdrop @hidden="resetModalTurma()">
      <lista-turma-modal ref="listaTurmaModal" :is-modal="true" :filtrar-modalidade="itemSelecionado.modalidade_turma ? itemSelecionado.modalidade_turma.id : null" @resolve="resolveTurma" />
    </b-modal>

    <b-modal id="modalCancelarContrato" ref="modalCancelarContrato" v-model="modalCancelarContrato" size="lg" title="Cancelamento de contrato" hide-footer no-close-on-backdrop>
      <div class="form-group row mb-3">
        <div class="col-md-12">
          <label for="motivo-canelamento" class="col-form-label">Motivo do cancelamento *</label>
          <textarea id="motivo-canelamento" v-model="itemSelecionado.motivo_cancelamento" :class="{'invalid-input' : !estaValido && $v.itemSelecionado.motivo_cancelamento.$invalid}" class="form-control" rows="3" required maxlength="5000"></textarea>
          <div class="d-flex justify-content-between">
            <div v-if="!estaValido && $v.itemSelecionado.motivo_cancelamento.$invalid" class="multiselect-invalid">Preencha o motivo do cancelamento!</div>
            <span class="text-secondary">Limite de caracteres: {{ 5000 - itemSelecionado.motivo_cancelamento.length }}</span>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-center">
        <b-btn :disabled="salvando" variant="vermelho" @click="salvarCancelar()">Confirmar</b-btn>
        <button type="button" class="btn btn-link" @click="modalCancelarContrato = false, estaValido = true, itemSelecionado.motivo_cancelamento = ''">Cancelar</button>
      </div>
    </b-modal>

    <b-modal id="modalImpressao" ref="modalImpressao" hide-header hide-footer no-close-on-backdrop @hidden="voltarImprimirContrato()">
      <p class="text-center font-14">
        Deseja realizar a impressão?
      </p>

      <div class="text-center">
        <div class="mb-1 d-flex">
          <g-select
            :value="modeloContratoSelecionado"
            :select="setModeloContratoSelecionado"
            :options="listaModelosContratoAtivosFranqueada"
            class="multiselect-truncate w-auto"
            label="descricao"
            track-by="id" />
          <b-btn :disabled="!modeloContratoSelecionado.id" class="ml-1" variant="verde" @click="imprimirContrato">Imprimir</b-btn>
          <b-btn :disabled="!modeloContratoSelecionado.id" class="ml-1" variant="verde" @click="editarTextoContrato">Editar</b-btn>
        </div>

        <div class="mb-1">
          <b-btn v-if="titulosReceber.find(item => item.forma_cobranca && item.forma_cobranca.forma_boleto)" variant="primary" @click="abrirImprimirBoleto">Imprimir boletos</b-btn>
        </div>

        <div>
          <b-btn variant="link" @click="$refs.modalImpressao.hide()">Fechar</b-btn>
        </div>
      </div>
    </b-modal>
    <g-modal id="modalEditarContrato" ref="modalEditarContrato" v-model="visibilidadeEditarTextoContrato" size="xl"
             no-close-on-backdrop>
      <editar-texto-contrato id="editarTextoContrato" ref="editarTextoContrato"
                             :modelo-contrato-id="modeloContratoSelecionado.id" :contrato-id="parseInt(itemSelecionadoID)"
                             @voltar="cancelarEditarTextoContrato()"/>
    </g-modal>

    <b-modal id="modalSuperAmigo" ref="modalSuperAmgio" v-model="visibilidadeModalSuperAmgio" size="lg" hide-header hide-footer no-close-on-backdrop>
      <div class="form-group">

        <div class="row">
          <div class="col-md-auto">
            <label v-help-hint="'modal-super-amigo_tipo_desconto'" for="tipo_desconto_super_amigo" class="col-form-label d-block">Tipo desconto super amigo *</label>
            <button v-if="objFranqueada.desconto_super_amigos_ativo" :class="descontoSuperAmigo ? 'btn-verde' : ''" type="button" class="btn" @click="descontoSuperAmigo = true, descontoSuperAmigoTurbinado = false">Super Amigo</button>
            <button v-if="objFranqueada.desconto_super_amigos_turbinado_ativo" :class="descontoSuperAmigoTurbinado ? 'btn-verde' : ''" type="button" class="btn" @click="descontoSuperAmigo = false, descontoSuperAmigoTurbinado = true">Super Amigo Turbinado</button>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <label v-help-hint="'modal-super-amigo_nome'" for="nome_super_amigo" class="col-form-label">Informe o Super Amigo *</label>
            <typeahead id="nome_super_amigo" ref="refNomeSuperAmigo" :item-hit="setNomeSuperAmigo" source-path="/api/aluno/buscar_nome_com_contrato_ativo" key-name="pessoa.nome_contato" />
          </div>
        </div>

        <div class="row">
          <div class="col mt-2">
            <template v-if="!alunoSuperAmigo">
              <div class="list-group-item list-group-item-accent-info list-group-item-info border-0">
                <font-awesome-icon icon="info-circle" /> Adicione o aluno que indicou.
              </div>
            </template>
            <template v-else>
              <b-row class="header-card-list mb-0">
                <b-col md="3">
                  <label class="col-form-label">Nome do super amigo</label>
                </b-col>
                <b-col md="3">
                  <label class="col-form-label">Turma</label>
                </b-col>
                <b-col md="3">
                  <label class="col-form-label">Livro</label>
                </b-col>
                <b-col md="3" data-label="Situação">
                  <!-- <label class="col-form-label">Situação do contrato</label> -->
                </b-col>
              </b-row>

              <div class="row data-scroll">
                <perfect-scrollbar class="scroller col-12">
                  <b-row class="body-card-list">
                    <b-col md="3" class="truncate" data-header="Nome do super amigo">{{ alunoSuperAmigo.pessoa.nome_contato }}</b-col>
                    <b-col md="3" data-header="Turma"> {{ alunoSuperAmigo.contratos[0].turma.descricao }}</b-col>
                    <b-col md="3">{{ alunoSuperAmigo.contratos[0].turma.livro.descricao }}</b-col>
                    <b-col md="3">
                      <span v-b-tooltip.viewport.left.hover :title="situacoes.find(situacao => situacao.id === alunoSuperAmigo.contratos[0].situacao ).descricao" :class="'circle-badge-' + alunoSuperAmigo.contratos[0].situacao.toLowerCase() " class="circle-badge"></span>
                    </b-col>
                  </b-row>
                </perfect-scrollbar>
              </div>
            </template>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mt-3">
            <b-btn :disabled="((!descontoSuperAmigo || !descontoSuperAmigoTurbinado) && !alunoSuperAmigo) || ((!descontoSuperAmigo && !descontoSuperAmigoTurbinado) && alunoSuperAmigo)" type="button" variant="verde" @click="aplicarSuperAmigo">Aplicar</b-btn>
            <b-btn variant="link" @click="$refs.modalSuperAmgio.hide(), visibilidadeModalSuperAmgio = false">Fechar</b-btn>
          </div>

          <div class="col-md-6 mt-3">
            <button type="button" class="btn d-flex ml-auto btn-outline-danger" @click="removerDescontoSuperAmigo()">Remover desconto</button>
          </div>
        </div>
      </div>
    </b-modal>

    <!--Modal de planilha fixa  -->
    <!-- <b-modal id="modalPlanilhaFixa" ref="modalPlanilhaFixa" size="lg" title="Selecionar dias de aulas" hide-footer no-close-on-backdrop @hidden="resetModalTurma()">
      <lista-turma-modal ref="listaTurmaModal" :is-modal="true" @resolve="resolveTurma" />
    </b-modal> -->

    <!--Modal de login   -->
    <!-- <b-modal id="modalDeLogin" ref="model_de_login" size="lg" title="Login" hide-footer no-close-on-backdrop>

      <b-form-row>
        <b-col class="">
          <label for=""></label>
          <input type="text">
        </b-col>
      </b-form-row>

    </b-modal> -->
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required, requiredIf} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'
import Typeahead from '../../components/Typeahead.vue'
import EditarTextoContrato from './EditarTextoContrato.vue'
import {dateToString, dateToCompare, idade, converteHorarioBancoParaInputText, formatarDataPadraoBancoDados, firstDayOfWeek, lastDayOfWeek} from '../../utils/date'
import {toNumber, numberToCurrency, round} from '../../utils/number'
import ListaTurmaModal from '../turma/ModalLista.vue'
import TitulosContaReceber from '../titulo-receber/TitulosContaReceber.vue'
import moment from 'moment'
import formatarDataDiaSemana from '../../filters/formatar-data-dia-semana'
import formatarDataHora from '../../filters/formatar-data-hora'
import open from '../../utils/open'

const isAluno = (value, vm) => {
  return value !== null
}

export default {
  components: {
    Typeahead,
    ListaTurmaModal,
    TitulosContaReceber,
    EditarTextoContrato
  },

  data () {
    return {
      input_locker_callback: null,
      loadCount: 0,
      salvando: false,
      editando: false,
      estaValido: true,
      naoEncontrado: false,
      precisaBuscarAluno: false,
      bloqueiaValorMaterialDidatico: true,
      habilitouCampo: false,
      printURL: '',
      alunoBuscado: false,

      listaBoletos: [],
      situacoes: [
        {id: 'V', descricao: 'Vigente'},
        {id: 'C', descricao: 'Cancelado'},
        {id: 'E', descricao: 'Encerrado'},
        {id: 'R', descricao: 'Rescindido'},
        {id: 'T', descricao: 'Trancado'}
      ],

      modalCancelarContrato: false,

      tiposContrato: [
        {id: 'M', descricao: 'Matrícula'},
        {id: 'R', descricao: 'Rematrícula'}
      ],

      itens: [
        {
          id: null,
          item: null,
          plano_conta: null,
          valor_parcela: 0,
          numero_parcelas: 1,
          valor: 0,
          conta: null,
          forma_cobranca: null,
          data_vencimento: '',
          dias_subsequentes: null,
          item_entregue: true
        },
        {
          id: null,
          item: null,
          plano_conta: null,
          valor_parcela: 0,
          numero_parcelas: 1,
          valor: 0,
          conta: null,
          forma_cobranca: null,
          data_vencimento: '',
          dias_subsequentes: null,
          item_entregue: true,
          desconto: '0%',
          valor_parcela_desconto: 0,
          valor_total_desconto: 0
        },
        {
          id: null,
          item: null,
          plano_conta: null,
          valor_parcela: 0,
          numero_parcelas: 1,
          valor: 0,
          conta: null,
          forma_cobranca: null,
          data_vencimento: '',
          dias_subsequentes: null,
          item_entregue: false
        }
      ],

      dadosContaReceber: {
        aluno: null,
        franqueada: null,
        sacado_pessoa: null,
        usuario: null,
        vendedor_funcionario: null,
        valor_total: null,
        observacao: null
      },

      planoContaMatricula: null,
      planoContaValorCurso: null,
      planoContaMaterialDidatico: null,

      minDate: dateToString(new Date()),

      planilhapersonal: false,

      descontoSuperAmigoAplicado: false,
      alunoSuperAmigo: null,
      visibilidadeModalSuperAmgio: false,
      visibilidadeEditarTextoContrato: false,
      descontoSuperAmigo: false,
      descontoSuperAmigoTurbinado: false,
      listaCreditosPersonal: [
        {id: 32, descricao: '32', quantidade_creditos: 32, aula_por_semana: 2},
        {id: 48, descricao: '48', quantidade_creditos: 48, aula_por_semana: 3},
        {id: 64, descricao: '64', quantidade_creditos: 64, aula_por_semana: 4},
        {id: -1, descricao: 'Avulsos'}
      ],
      possuiAgendamentosPersonalSalvos: false,
      personalSalvoNomeInstrutor: '',
      personalSalvoDataInicio: '',
      personalSalvoDataFim: '',
      personalSalvoDescricaoSala: '',
      diasHoras: [],

      personal: {
        creditos_personal: null,
        aula_por_semana: null,
        funcionario: null,
        sala_franqueada: null,
        semestre: null,
        agendamento_personal: []
      },

      dataFiltroPersonal: moment().format('DD/MM/YYYY'),
      buscaPersonal: false,
      carregandoInformacoesPersonal: false,
      // descontoAVistaValorCurso: '0%',

      valor_original_curso: '0',
      desconto_avista: 0,
      valor_desconto_avista_franquiada: '0',

      personalProgramado: false,
      personalConfirmado: false,
      filtrarNovamente: false,
      msg: '',

      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: true
      },
      converteHorarioBancoParaInputText: converteHorarioBancoParaInputText
    }
  },

  computed: {
    ...mapState('contrato', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando', 'titulosReceber', 'valorTotalItens', 'valorTotalParcelas']),
    ...mapState('turma', {listaTurmas: 'lista'}),
    ...mapState('curso', {listaCursos: 'lista'}),
    ...mapState('livro', {listaLivros: 'lista'}),
    ...mapState('formaPagamento', {listaFormasPagamento: 'lista'}),
    ...mapState('conta', {listaContas: 'lista'}),
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    // Foi colocado para listar apenas os semestres atuais, mas depois foi definido pra listar todos no personal.
    // Se for pra voltar apenas para os semestres atuais, dá pra chamar semestre/listaAtuais
    ...mapState('semestre', {listaSemestres: 'lista', listaSemestresPersonalObj: 'lista'}),
    ...mapState('planoConta', {listaPlanosConta: 'selectReceitas'}),
    ...mapState('parametrosFranqueadora', {parametrosFranqueadora: 'item'}),
    ...mapState('item', {todosItens: 'lista'}),
    ...mapState('diasSubsequentes', ['listaDiasDaFranqueada']),
    ...mapState('modeloTemplate', {listaModelosContrato: 'lista', modeloContratoSelecionado: 'itemSelecionado'}),
    ...mapState('franqueadas', ['objFranqueada']),
    ...mapState('modalidadeTurma', {listaModalidadesTurmaRequisicao: 'lista'}),
    ...mapState('root', ['usuarioLogado']),
    ...mapState('modulos', ['permissoes']),

    listaModelosContratoAtivosFranqueada: {
      get () {
        return this.listaModelosContrato.filter(modelo => {
          return modelo && modelo.situacao === 'A' 
        }) || []
      }
    },

    modalidadeTurmaNaoEhPersonal: {
      get () {
        return (this.itemSelecionado.modalidade_turma && this.itemSelecionado.modalidade_turma.tipo && this.itemSelecionado.modalidade_turma.tipo !== 'PER')
      }
    },

    listaSemestresPersonal: {
      get () {
        // return this.listaSemestresPersonalObj.lista
        return this.listaSemestresPersonalObj
      }
    },

    modalidadeTurmaNaoPreenchida: {
      get () {
        return !this.itemSelecionado.modalidade_turma
      }
    },

    horariosRestantes: {
      get () {
        const aulasPorSemana = this.modalidadePersonalAvulso ? this.itemSelecionado.creditos_personal_avulso.aula_por_semana : this.itemSelecionado.creditos_personal.aula_por_semana
        const agendamentos = this.itemSelecionado.agendamento_personal.length

        return aulasPorSemana - agendamentos
      }
    },

    maximoParcelas: {
      get () {
        return this.parametrosFranqueadora.numero_maximo_parcelas
      }
    },

    titulosReceber: {
      get () {
        return this.$store.state.contrato.titulosReceber
      },
      set (value) {
        this.$store.commit('contrato/SET_TITULOS_RECEBER', value)
      }
    },

    listaModalidadesTurma: {
      get () {
        return this.listaModalidadesTurmaRequisicao.map(modalidade => {
          return modalidade
        })
      }
    },

    listaFuncionarios: {
      get () {
        return this.listaFuncionariosRequisicao.filter(funcionario => { return funcionario.consultor === true })
      }
    },

    listaInstrutoresPersonal: {
      get () {
        return this.listaFuncionariosRequisicao.filter(funcionario => funcionario.instrutor_personal === true)
      }
    },

    listaTurmasSemEncerramento: {
      get () {
        return this.listaTurmasFiltradaPorModalidade.filter(turma => { return turma.situacaoTurma !== 'ENC' })
      }
    },

    listaTurmasFiltradaPorModalidade: {
      get () {
        let listaTurmas = this.listaTurmas
        if (this.itemSelecionado.modalidade_turma && this.itemSelecionado.modalidade_turma.id) {
          listaTurmas = listaTurmas.filter(turma => {
            return turma.modalidadeTurmaId === this.itemSelecionado.modalidade_turma.id
          })
        }
        return listaTurmas
      }
    },

    descontosValorCurso: {
      get () {
        let descontos = ['0%']

        const itemValorCurso = this.itens[1]
        const formaCobranca = itemValorCurso.forma_cobranca

        if (formaCobranca) {
          const debitoOuLiquidacaoImediata = formaCobranca.forma_cartao_debito === true || formaCobranca.liquidacao_imediata === true
          const aVista = itemValorCurso.numero_parcelas === 1 && debitoOuLiquidacaoImediata
          const cartaoCredito = formaCobranca.forma_cartao === true


          if (formaCobranca.forma_cheque === true || cartaoCredito) {
            console.log('1')
            descontos = descontos.concat(['10%', '15%', '20%'])
          } else if (formaCobranca.forma_boleto === true ) {
            console.log('2')
            
            descontos = descontos.concat(['5%', '15%', '20%'])
          } else {
            console.log('3')
            
            descontos = descontos.concat(['5%', '10%', '15%', '20%'])
          }
        }

        descontos.push('Desconto Especial')
        return descontos
      }
    },

    descontoInformarFamiliar: {
      get () {
        /* Regra de desconto familiar conforme PI-612 */
        const itemValorCurso = this.itens[1]
        const formaCobranca = itemValorCurso.forma_cobranca
        const desconto = itemValorCurso.desconto

        if (desconto === '15%') {
          const boletoOuCartaoCredito = formaCobranca.forma_boleto === true || (formaCobranca.forma_cartao === true && formaCobranca.forma_cartao_debito === false)
          const debitoOuChequeOuLiquidacaoImediata = formaCobranca.forma_cartao_debito === true || formaCobranca.liquidacao_imediata === true || formaCobranca.forma_cheque === true
          const desconto15 = desconto === '15%'

          if (desconto15 === true) {
            return true
          }
        }

        return false
      }
    },

    descontoInformarConvenio: {
      get () {
        /* Regra de desconto convênio conforme PI-612 */
        const itemValorCurso = this.itens[1]
        const formaCobranca = itemValorCurso.forma_cobranca
        const turma = this.itemSelecionado.turma
        const desconto = itemValorCurso.desconto

        if (turma) {
          const intensivoOuSemi = turma.intensidade === 'S' || turma.intensidade === 'I'

          if (desconto === '20%') {
            const boletoOuCartaoCredito = formaCobranca.forma_boleto === true || (formaCobranca.forma_cartao === true && formaCobranca.forma_cartao_debito === false)
            const debitoOuChequeOuLiquidacaoImediata = formaCobranca.forma_cartao_debito === true || formaCobranca.liquidacao_imediata === true || formaCobranca.forma_cheque === true

             const desconto20 = desconto === '20%'

            if (desconto20 === true) {
              return true
            }
          }
        }

        return false
      }
    },

    listaDescontosAVistaValorCurso: {
      get () {
        let descontos = ['0%']
        const itemValorCurso = this.itens[1]
        const formaCobranca = itemValorCurso.forma_cobranca

        if (formaCobranca) {
          const debitoOuLiquidacaoImediata = formaCobranca.forma_cartao_debito === true || formaCobranca.liquidacao_imediata === true
          const aVista = itemValorCurso.numero_parcelas === 1 && debitoOuLiquidacaoImediata
          const cartaoCredito = formaCobranca.forma_cartao === true && formaCobranca.forma_cartao_debito === false

          if (aVista || cartaoCredito || formaCobranca.forma_cheque === true || formaCobranca.forma_boleto === true) {
            descontos = descontos.concat(['1%', '2%', '3%', '4%', '5%'])
          }
        }

        return descontos
      }
    },

    imprimirContratoURL: {
      get () {
        const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
        const modeloContrato = this.modeloContratoSelecionado.id
        const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
        const rota = this.$route.matched[0].path
        const contratoID = this.itemSelecionadoID
        return `api/contrato/imprimir/${contratoID}?Authorization=${auth}&URLModulo=${rota}&franqueada=${franqueada}&modelo_contrato=${modeloContrato}`
      }
    },

    listaItens: {
      get () {
        return this.todosItens.filter(item => item.tipo_item.tipo === 'P')
      }
    },

    numeroContrato: {
      get () {
        return this.itemSelecionado.id ? `${this.itemSelecionado.aluno.id}/${this.itemSelecionado.sequencia_contrato}` : ''
      }
    },

    tipoContrato: {
      get () {
        const tipo = this.itemSelecionado.tipo_contrato
        const item = this.tiposContrato.filter(item => item.id === tipo)[0]
        return tipo ? {id: tipo, descricao: item.descricao} : {}
      }
    },

    situacaoContrato: {
      get () {
        const situacao = this.itemSelecionado.situacao
        const item = this.situacoes.filter(item => item.id === situacao)[0]
        return situacao ? {id: situacao, descricao: item.descricao} : {}
      }
    },

    listaContratante: {
      get () {
        const lista = []

        if (this.itemSelecionado.aluno && this.itemSelecionado.aluno.pessoa) {
          lista.push({
            ...this.itemSelecionado.aluno.pessoa,
            nome: this.prepararNome(this.itemSelecionado.aluno.pessoa.nome_contato, 'aluno')
          })

          if (this.itemSelecionado.aluno.responsavel_financeiro_pessoa) {
            lista.push({
              ...this.itemSelecionado.aluno.responsavel_financeiro_pessoa,
              nome: this.prepararNome(this.itemSelecionado.aluno.responsavel_financeiro_pessoa.nome_contato, 'responsável financeiro')
            })
          }
        }

        return lista
      }
    },

    listaDiasSubsequentes: {
      get () {
        return this.listaDiasDaFranqueada
      }
    },

    listaSalasPersonal: {
      get () {
        return this.$store.state.salaFranqueada.lista.filter(salaFranqueada => salaFranqueada.personal === true)
      }
    },

    weekDates: {
      get () {
        const dates = {}
        let selectedDate = moment(this.dataFiltroPersonal, 'DD/MM/YYYY').startOf('week')
        dates[0] = selectedDate.format('DD/MM/YYYY')

        for (let i = 1; i < 7; i++) {
          selectedDate = selectedDate.add(1, 'day')
          dates[i] = selectedDate.format('DD/MM/YYYY')
        }

        return dates
      }
    },

    modalidadePersonal: {
      get () {
        return this.itemSelecionado.modalidade_turma && this.itemSelecionado.modalidade_turma.tipo === 'PER'
      }
    },

    modalidadePersonalAvulso: {
      get () {
        return this.modalidadePersonal && this.itemSelecionado.creditos_personal && this.itemSelecionado.creditos_personal.id === -1
      }
    },

    totalItensBruto: {
      get () {
        let total = 0
        this.itens.forEach(item => {
          total += this.itemValorBruto(item)
        })
        return total
      }
    },

    totalItensDescontos: {
      get () {
        let total = 0
        this.itens.forEach(item => {
          total += this.itemValorDesconto(item)
        })
        return total
      }
    },

    totalItensLiquido: {
      get () {
        let total = 0
        this.itens.forEach(item => {
          total += this.itemValorLiquido(item)
        })
        return total
      }
    }

  },

  watch: {
    input_locker_callback (value) {
      if (value && value.id) {
        this.habilitouCampo = true
      }
    }
  },

  mounted () {
    const id = this.$route.params.id
    this.editando = !!id

    this.LIMPAR_ITEM_SELECIONADO()
    this.$store.commit('contrato/SET_TITULOS_RECEBER', [])

    this.$store.commit('turma/SET_PAGINA_ATUAL', 1)
    this.$store.commit('curso/SET_PAGINA_ATUAL', 1)
    this.$store.commit('livro/SET_PAGINA_ATUAL', 1)
    this.$store.commit('formaPagamento/SET_PAGINA_ATUAL', 1)
    this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
    this.$store.commit('semestre/SET_PAGINA_ATUAL', 1)
    this.$store.commit('semestre/SET_PAGINA_ATUAL_ATUAIS', 1)
    this.$store.commit('semestre/SET_LISTAR_PROXIMOS', false)
    this.$store.commit('planoConta/SET_PAGINA_ATUAL', 1)
    this.$store.commit('item/SET_PAGINA_ATUAL', 1)
    this.$store.commit('modeloTemplate/SET_PAGINA_ATUAL', 1)
    this.$store.commit('operadoraCartao/SET_PAGINA_ATUAL', 1)
    this.$store.commit('modalidadeTurma/SET_PAGINA_ATUAL', 1)
    this.$store.commit('salaFranqueada/SET_PAGINA_ATUAL', 1)

    this.$store.commit('franqueadas/SET_PAGINA_ATUAL', 1)
    this.$store.commit('franqueadas/setFranqueadaSelecionada', this.$store.state.root.usuarioLogado.franqueadaSelecionada)
    this.$store.dispatch('franqueadas/buscarParametros')

    if (this.$store.state.turma.estaCarregando === false) {
      this.$store.dispatch('turma/listar')
    }

    if (this.$store.state.livro.estaCarregando === false) {
      this.$store.dispatch('livro/listar')
    }

    this.$store.dispatch('curso/listar').then(this.countCarregamento)
    this.$store.dispatch('formaPagamento/getLista').then(this.countCarregamento)
    this.$store.dispatch('funcionario/listar').then(this.countCarregamento)
    this.$store.dispatch('semestre/listar').then(this.countCarregamento)
    this.$store.dispatch('semestre/listarAtuais').then(this.countCarregamento)
    this.$store.dispatch('planoConta/listar').then(this.countCarregamento)
    this.$store.dispatch('modeloTemplate/listar')
    this.$store.dispatch('diasSubsequentes/buscarPorFranqueadaAtual').then(this.countCarregamento)
    this.$store.dispatch('operadoraCartao/listar')
    this.$store.dispatch('modalidadeTurma/listar')
    this.$store.dispatch('salaFranqueada/listar')

    this.$store.dispatch('item/getListaProdutosServicos', { franqueada: this.$store.state.root.usuarioLogado.franqueadaSelecionada })
      .then(() => {
        const matricula = this.todosItens.find(item => item.tipo_item.tipo === 'M')
        this.setItem(matricula, 0)
        if (this.editando) {
          const valorCurso = this.todosItens.find(item => item.tipo_item.tipo === 'V')
          valorCurso.valor_original = valorCurso.valor_venda
          this.valor_original_curso = valorCurso.valor_venda

          this.calcularTotalItens()
        }
      })

    if (this.editando) {
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
        .then(() => {
          if (this.itemSelecionado.situacao !== 'C') {
            this.itemSelecionado.motivo_cancelamento = ''
          }
          if (!this.itemSelecionado.responsavel_financeiro_pessoa || !this.itemSelecionado.aluno) {
            return null
          }

          const modalidadeTurma = this.listaModalidadesTurma.find((item) => (item.tipo === this.itemSelecionado.modalidade_turma.tipo))
          this.itemSelecionado.modalidade_turma = modalidadeTurma

          const tipo = this.itemSelecionado.responsavel_financeiro_pessoa.id === this.itemSelecionado.aluno.pessoa.id ? 'aluno' : 'responsável financeiro'
          const obj = {...this.itemSelecionado.responsavel_financeiro_pessoa, nome: this.prepararNome(this.itemSelecionado.responsavel_financeiro_pessoa.nome_contato, tipo)}
          this.setResponsavelFinanceiroPessoa(obj)

          this.itens = this.itemSelecionado.contratoContaReceber[0].itemsContaReceber
          this.SET_VALOR_TOTAL_ITENS(toNumber(this.itemSelecionado.contratoContaReceber[0].valor_total))

          this.$store.commit('contrato/SET_TITULOS_RECEBER', this.itemSelecionado.contratoContaReceber[0].titulos_receber)

          if (this.itemSelecionado.contratoContaReceber[0].titulos_receber.length > 0) {
            this.itemSelecionado.contratoContaReceber[0].titulos_receber.forEach(tituloReceber => {
              if (tituloReceber.boletos.length > 0) {
                // teoricamente é para existir apenas 1 boleto por titulo_receber, pois será um boleto por parcela
                this.listaBoletos.push(tituloReceber.boletos.map(boleto => boleto.id)[0])
              }
            })
          }
          if (this.modalidadePersonal) {
            this.$store.dispatch('agendamentoPersonal/buscarInfoPorContrato', id).then(res => {
              if (res && res.dataInicio) {
                this.possuiAgendamentosPersonalSalvos = true
                this.personalSalvoNomeInstrutor = res.apelidoProfessor
                this.personalSalvoDataInicio = res.dataInicio
                this.personalSalvoDataFim = res.dataFim
                this.personalSalvoDescricaoSala = res.descricaoSala
                this.diasHoras = res.diasHoras
              }
            })
          }
          EventBus.$emit('calcularTotalParcelas')
        })
        .catch(() => {
          this.naoEncontrado = true
        })
    } else {
      const alunoPreSelecionado = this.$route.query.aluno
      if (alunoPreSelecionado) {
        this.setAlunoSelecionado(alunoPreSelecionado)
        this.buscarAluno()
          .then(aluno => this.setAluno(aluno))
          .catch(() => {
            this.precisaBuscarAluno = true
          })
      } else {
        this.precisaBuscarAluno = true
      }

      this.setDataVencimento(dateToString(new Date()), 0)
      this.setDataVencimento(dateToString(new Date()), 1)
      this.setDataVencimento(dateToString(new Date()), 2)

      this.setDataMatricula(dateToString(new Date()))
    }
  },

  validations: {
    itemSelecionado: {
      aluno: {isAluno},
      tipo_contrato: {required},
      modalidade_turma: {required},
      responsavel_venda_funcionario: {required},
      responsavel_carteira_funcionario: {required},
      responsavel_financeiro_pessoa: {required},
      motivo_cancelamento: {
        required: requiredIf(function () {
          return this.modalCancelarContrato === true
        })
      },
      sala_franqueada: {
        required: requiredIf(function () {
          return this.modalidadePersonal && !this.editando
        })
      },
      semestre: {
        required: requiredIf(function () {
          return this.modalidadePersonal
        })
      },
      instrutor: {
        required: requiredIf(function () {
          return this.modalidadePersonal && !this.editando
        })
      },
      livro: {
        required: requiredIf(function () {
          return this.modalidadePersonal
        })
      }
    }
  },

  methods: {
    ...mapActions('contrato', ['buscar', 'criar', 'atualizar']),
    ...mapActions('aluno', {buscarAluno: 'buscarAlunoComPessoa'}),
    ...mapActions('salaFranqueada', {listarDisponibilidade: 'listarDisponibilidade'}),
    ...mapMutations('contrato', ['LIMPAR_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ITEM_SELECIONADO', 'SET_VALOR_TOTAL_ITENS']),
    ...mapMutations('aluno', {setAlunoSelecionado: 'SET_ITEM_SELECIONADO'}),
    ...mapMutations('modeloTemplate', {setModeloContratoSelecionado: 'SET_ITEM_SELECIONADO'}),

    formatarDataDiaSemana: formatarDataDiaSemana,
    formatarDataHora: formatarDataHora,

    ehContratoSelecionado (value1, value2) {
      if (this.numeroContrato === this.getNumeroContrato(value1, value2)) {
        return true
      }

      return false
    },

    voltarModalPersonal () {
      this.$refs.planilhapersonal.hide()
    },

    getNumeroContrato (value1, value2) {
      return `${value1}/${value2}`
    },

    validarProgramacaoPersonal () {
      this.personalConfirmado = true
      const camposPreenchidos = !!this.itemSelecionado.instrutor && !!this.itemSelecionado.sala_franqueada
      const qtdeAulasSemana = this.modalidadePersonalAvulso ? this.itemSelecionado.creditos_personal_avulso.aula_por_semana : this.itemSelecionado.creditos_personal.aula_por_semana
      const qtdeAgendamentosPreenchhidos = this.itemSelecionado.agendamento_personal ? this.itemSelecionado.agendamento_personal.length : 0
      const todosAgendamentosForamPreenchidos = qtdeAgendamentosPreenchhidos === qtdeAulasSemana

      if (!todosAgendamentosForamPreenchidos) {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: `Falta preencher ${qtdeAulasSemana - qtdeAgendamentosPreenchhidos} agendamentos!`
        })
      }
      if (camposPreenchidos && todosAgendamentosForamPreenchidos) {
        this.personalProgramado = true
        this.$refs.planilhapersonal.hide()
      } else {
        this.personalProgramado = false
      }
    },

    filtroParcelas (item) {
      let lista = []
      if (item.item === null) {
        return lista
      }

      lista = ['valor_venda', 'valor_venda_2', 'valor_venda_3', 'valor_venda_4', 'valor_venda_5', 'valor_venda_6'].map(i => {
        return item.item.itemFranqueadas.length > 0 && item.item.itemFranqueadas[0][i] && item.item.itemFranqueadas[0][i] * 1 ? (i === 'valor_venda' ? 1 : i.replace(/.*(\d)/, '$1') * 1) : undefined
      })

      return lista.filter(i => i !== undefined)
    },

    resetModalTurma () {
      this.$refs.listaTurmaModal.resetModalTurma()
    },

    prepararNome (nome, tipo) {
      return `${nome} (${tipo})`
    },

    voltar () {
      if (this.visibilidadeEditarTextoContrato) {
        return false
      }
      window.history.back()
    },

    voltarImprimirContrato () {
      if (this.visibilidadeEditarTextoContrato) {
        return false
      }
      if (this.$route.query.aluno) {
        this.$router.push(`/academico/aluno/atualizar/${this.$route.query.aluno}`)
      } else {
        this.$refs.modalImpressao.hide()
      }
    },

    mostrarModalImpressao (dados) {
      this.SET_ITEM_SELECIONADO_ID(dados.objetoORM)
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const boletos = [`boletos[]=`, dados.boletos.join('&boletos[]=')].join('')
      this.printURL = `/api/boleto/imprimir-boletos?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${boletos}`
      this.$refs.modalImpressao.show()
    },

    ajustarCamposTitulos () {
      const listaOperadoras = this.$store.state.operadoraCartao.lista
      this.titulosReceber = this.titulosReceber.map((titulo) => {
        if (titulo.transacao_cartao && titulo.transacao_cartao.operadora_cartao) {
          let idOperadoraSelecionada = titulo.transacao_cartao.operadora_cartao
          let operadoraCartao = listaOperadoras.find((operadora) => {
            return operadora.id === idOperadoraSelecionada
          })
          if (operadoraCartao !== undefined) {
            titulo.transacao_cartao.operadora_cartao = operadoraCartao

            titulo.transacao_cartao.parcelamento_operadora_cartao = operadoraCartao.parcelamentoOperadoraCartaos.find((parcelamento) => {
              return parcelamento.id === titulo.transacao_cartao.parcelamento_operadora_cartao
            })
          }
        }

        return titulo
      })
    },

    salvarCancelar () {
      this.setSituacao({id: 'C'})
      this.salvar()
    },

    salvar () {
      const convenioInvalido = this.descontoInformarConvenio === true && (!this.itemSelecionado.convenio_desconto || !this.itemSelecionado.convenio_desconto.id)
      const familiarInvalido = this.descontoInformarFamiliar === true && !this.itemSelecionado.familiar_desconto

      let personalInvalido = false
      if (!this.editando && this.modalidadePersonal) {
        const aulasPorSemana = this.modalidadePersonalAvulso ? this.itemSelecionado.creditos_personal_avulso.aula_por_semana : this.itemSelecionado.creditos_personal.aula_por_semana
        if (!this.itemSelecionado.agendamento_personal || this.itemSelecionado.agendamento_personal.length !== aulasPorSemana) {
          personalInvalido = true
        }
      }
      if (this.$v.$invalid === true || this.valorTotalItens !== this.valorTotalParcelas || convenioInvalido === true || familiarInvalido === true || personalInvalido === true) {
        this.estaValido = false
        return
      }

      let existeDadosCartaoVazio = false
      if (this.editando === false) {
        let possuiItemFormaCobrancaCartao = false
        for (var item of this.itens) {
          if (item.forma_cobranca && item.forma_cobranca.forma_cartao) {
            possuiItemFormaCobrancaCartao = true
          }
        }
        if (possuiItemFormaCobrancaCartao) {
          for (var titulo of this.titulosReceber) {
            if (titulo.transacao_cartao !== null) {
              if (titulo.transacao_cartao.identificador === null || titulo.transacao_cartao.numero_lancamento === null || !titulo.transacao_cartao.operadora_cartao ||
                  !titulo.transacao_cartao.parcelamento_operadora_cartao || titulo.transacao_cartao.identificador === '' || titulo.transacao_cartao.numero_lancamento === '') {
                existeDadosCartaoVazio = true
              }
            }
          }
        }
      }

      EventBus.$emit('validarParcelas', () => {
        EventBus.$emit('chamarModal', {
          resolve: () => {
            const situacaoAnterior = this.itemSelecionado.situacao

            this.dadosContaReceber.franqueada = this.usuarioLogado.franqueadaSelecionada
            this.itemSelecionado.itensOriginais = this.itens
            this.itemSelecionado.dadosContaReceberOriginais = this.dadosContaReceber
            this.itemSelecionado.titulosOriginais = this.titulosReceber

            this.salvando = true

            if (this.editando === true) {
              this.atualizar()
                .then(this.voltar, () => {
                  this.itemSelecionado.situacao = situacaoAnterior
                })
                .finally(() => {
                  this.salvando = false
                  this.modalCancelarContrato = false
                })
            } else {
              this.criar()
                .then(
                  this.mostrarModalImpressao,
                  (err) => {
                    console.log(err)
                    voltar()
                  }
                )
                .finally(() => {
                  this.salvando = false
                  this.ajustarCamposTitulos()
                })
            }
          },
          reject: () => {
            this.itemSelecionado.motivo_cancelamento = ''
          }
        }, existeDadosCartaoVazio ? 'Os dados do cartão não foram preenchidos!\nDeseja continuar?' : null)
      })
    },

    setAluno (value) {
      this.itemSelecionado.aluno = value
      this.dadosContaReceber.aluno = value

      if (value && !this.itemSelecionado.responsavel_financeiro_pessoa) {
        if (value.pessoa.id === value.responsavel_financeiro_pessoa.id) {
          value.pessoa.nome = this.prepararNome(value.pessoa.nome_contato, 'aluno')
          this.setResponsavelFinanceiroPessoa(value.pessoa)
        } else {
          value.responsavel_financeiro_pessoa.nome = this.prepararNome(value.responsavel_financeiro_pessoa.nome_contato, 'responsável financeiro')
          this.setResponsavelFinanceiroPessoa(value.responsavel_financeiro_pessoa)
        }
      }
    },

    calcularValorCurso () {
      const ID_ITEM_PERSONAL_AVULSO = 225
      let curso
      if (!this.itemSelecionado.modalidade_turma) {
        return
      }
      if (this.modalidadePersonal) {
        if (this.modalidadePersonalAvulso) {
          curso = this.todosItens.find(item => item.id === ID_ITEM_PERSONAL_AVULSO)
        } else {
          curso = this.todosItens.find(item => item.tipo_item.tipo === `VP${this.itemSelecionado.creditos_personal.quantidade_creditos}`)
        }
      } else if (this.itemSelecionado.turma) {
        curso = this.todosItens.find(item => item.id === this.itemSelecionado.turma.servicoId)
      } else {
        return
      }
      this.setItem(curso, 1)
      this.calcularTotalItens()
    },

    setModalidadeCurso (value) {
      this.resetAgendamentosPersonal()
      this.itemSelecionado.turma = null
      this.itemSelecionado.creditos_personal = null
      this.limparItemValorCurso()

      this.setLivro(null)

      if (this.modalidadePersonal) {
        this.itemSelecionado.creditos_personal = this.listaCreditosPersonal[0]
        this.personal.creditos_personal = this.itemSelecionado.creditos_personal.quantidade_creditos
        this.personal.aula_por_semana = this.itemSelecionado.creditos_personal.aula_por_semana
        this.resetAgendamentosPersonal()
      } else {
        this.personal = {
          creditos_personal: null,
          aula_por_semana: null,
          funcionario: null,
          sala_franqueada: null,
          semestre: null,
          agendamento_personal: []
        }
      }

      this.calcularValorCurso()
    },

    setCreditosPersonal (value) {
      this.itemSelecionado.creditos_personal = value
      this.setValoresPersonal()
    },

    setValoresPersonal () {
      if (this.modalidadePersonalAvulso) {
        this.personal.creditos_personal = this.itemSelecionado.creditos_personal_avulso.quantidade
        this.personal.aula_por_semana = this.itemSelecionado.creditos_personal_avulso.aula_por_semana
      } else {
        this.personal.creditos_personal = this.itemSelecionado.creditos_personal.quantidade_creditos
        this.personal.aula_por_semana = this.itemSelecionado.creditos_personal.aula_por_semana
      }
      this.resetAgendamentosPersonal()
      this.calcularValorCurso()
    },

    setTurma (value) {
      if (value && (value.turmaId || value.id)) {
        this.itemSelecionado.turma = value
        let livroObj = this.listaLivros.find(liv => (liv.id === value.livroId))
        let cursoObj = this.listaCursos.find(cur => (cur.id === value.cursoId))
        let semestreObj
        if (this.modalidadePersonal) {
          semestreObj = this.listaSemestresPersonal.find(sem => (sem.id === value.semestreId))
        } else {
          semestreObj = this.listaSemestres.find(sem => (sem.id === value.semestreId))
        }
        this.setLivro(livroObj)
        this.setCurso(cursoObj)
        this.setSemestre(semestreObj, true)
        this.setDataInicioContrato(dateToString(new Date(value.dataInicioTurma)))
        this.setDataTerminoContrato(dateToString(new Date(value.dataFimTurma)))

        // let valorCurso = value.curso.servico

        // this.setItem(0, 1)

        // if (!this.validadeIdadeAluno(value)) {
        //   EventBus.$emit('chamarModal', {
        //     resolve: success => {
        //       // TODO: BLOQUEAR CAMPO, so liberar com confirmação do responsavel
        //     },
        //     reject: () => {
        //       this.itemSelecionado.turma = null
        //       this.limparItemValorCurso()
        //     }
        //   }, `${this.msg}`)
        // }
      } else {
        this.itemSelecionado.turma = null
        this.setLivro(null)
      }
      this.calcularValorCurso()
    },

    limparItemValorCurso () {
      const valorCurso = {
        id: null,
        item: null,
        plano_conta: null,
        valor_parcela: 0,
        numero_parcelas: 1,
        valor: 0,
        valor_venda: 0,
        conta: null,
        forma_cobranca: null,
        data_vencimento: '',
        dias_subsequentes: null,
        item_entregue: false,
        desconto: '0%',
        valor_parcela_desconto: 0,
        valor_total_desconto: 0,
        itemFranqueadas: []
      }
      this.setItem(valorCurso, 1)
    },

    validadeIdadeAluno (turma) {
      this.msg = ''
      const curso = turma.curso

      const idadeAluno = idade(this.itemSelecionado.aluno.pessoa.data_nascimento)
      const idadeMinimaCurso = curso.idade_minima ? curso.idade_minima : null
      const idadeMaximaCurso = curso.idade_maxima ? curso.idade_maxima : null

      if (idadeMinimaCurso && idadeMaximaCurso) {
        if ((idadeAluno >= idadeMinimaCurso) && (idadeAluno <= idadeMaximaCurso)) {
          return true
        }

        if (idadeAluno < idadeMinimaCurso) {
          this.msg = 'Idade do aluno menor que a idade mínima!'
        } else if (idadeAluno > idadeMaximaCurso) {
          this.msg = 'Idade do aluno maior que a idade máxima!'
        }

        return false
      } else if (idadeMinimaCurso || idadeMaximaCurso) {
        if (idadeMinimaCurso && !idadeMaximaCurso) {
          if (idadeAluno >= idadeMinimaCurso) {
            return true
          }

          this.msg = 'Idade do aluno menor que a idade mínima!'
          return false
        } else if (!idadeMinimaCurso && idadeMaximaCurso) {
          if (idadeAluno <= idadeMaximaCurso) {
            return true
          }

          this.msg = 'Idade do aluno maior que a idade máxima!'
          return false
        }
      }

      return true
    },

    setCurso (value) {
      this.itemSelecionado.curso = value
    },

    setNumeroParcela (value, index) {
      this.itens[index].numero_parcelas = value
      if (index === 2) {
        this.calcularValoresEParcelasItem('numero_parcelas', index, true)
      } else {
        this.calcularValoresEParcelasItem('numero_parcelas', index, false)
      }
    },

    setLivro (value) {
      this.itemSelecionado.livro = value
      this.setItem(value ? value.item : null, 2)
    },

    setSemestre (value, isTurma = false) {
      this.itemSelecionado.semestre = value

      if (isTurma !== true) {
        this.setDataInicioContrato(dateToString(new Date(value.data_inicio)))
        this.setDataTerminoContrato(dateToString(new Date(value.data_termino)))
      }
    },

    setTipoContrato (value) {
      this.itemSelecionado.tipo_contrato = value.id
    },

    setSituacao (value) {
      this.itemSelecionado.situacao = value.id
    },

    setDataInicioContrato (value) {
      this.itemSelecionado.data_inicio_contrato = value
    },

    setDataTerminoContrato (value) {
      this.itemSelecionado.data_termino_contrato = value
    },

    setDataMatricula (value) {
      this.itemSelecionado.data_matricula = value
    },

    setQuantidadeCreditosPersonalAvulso (value) {
      this.itemSelecionado.creditos_personal_avulso.quantidade = value
      this.itens[1].quantidade = value
      this.setValoresPersonal()
    },

    setQuantidadeAulasPersonalAvulso (value) {
      this.itemSelecionado.creditos_personal_avulso.aula_por_semana = value
      this.setValoresPersonal()
    },

    setResponsavelVendaFuncionario (value) {
      this.itemSelecionado.responsavel_venda_funcionario = value
      this.dadosContaReceber.vendedor_funcionario = value
    },

    setResponsavelCarteiraFuncionario (value) {
      this.itemSelecionado.responsavel_carteira_funcionario = value
    },

    setResponsavelFinanceiroPessoa (value) {
      this.itemSelecionado.responsavel_financeiro_pessoa = value
      this.dadosContaReceber.sacado_pessoa = value
    },

    setFormaCobranca (value, index) {
      this.itens[index].desconto = '0%'
      this.itens[index].forma_cobranca = value
      this.calcularDescontos(index)
    },

    setDataVencimento (value, index) {
      if (value.length >= 10 && dateToCompare(value) < dateToCompare(this.minDate)) {
        value = ''
      }

      this.itens[index].data_vencimento = value
      this.calcularDescontos(index)
    },

    setDiasSubsequentes (value, index) {
      this.itens[index].dias_subsequentes = value
    },

    setPlanoConta (value, index) {
      this.itens[index].plano_conta = value || null
    },

    calcularMaterialDidatico (event, itemIndex) {
      const item = this.itens[itemIndex]
      if (!item.numero_parcelas) {
        item.numero_parcelas = 1
      }

      if (item.numero_parcelas > this.maximoParcelas) {
        item.numero_parcelas = this.maximoParcelas
      }

      if (item.item !== null) {
        let valorMaterialParcelado = (item.item.itemFranqueadas[0].valor_venda) || 0
        item.valor_parcela = item.valor
        if (item.numero_parcelas > 1) {
          valorMaterialParcelado = (item.item.itemFranqueadas[0][`valor_venda_${item.numero_parcelas}`]) || 0
          const valorMaterialParceladoTotal = (valorMaterialParcelado * 1) * item.numero_parcelas
          const percentualParcela = item.valor / valorMaterialParceladoTotal
          item.valor_parcela = ((item.valor * percentualParcela) / item.numero_parcelas)
        }

        // this.bloqueiaValorMaterialDidatico = item.valor < valorMaterialParceladoTotal
        this.calcularDescontos(itemIndex)
      }
    },

    verificaCampoBloqueado () {
      if (this.bloqueiaValorMaterialDidatico === true) {
        return {permissao: this.permissoes['DESCONTO_SUPERVISIONADO'], callBack: this}
      }
    },

    calcularValoresEParcelasItem (campo, itemIndex, bAplicarSelecaoParcela) {
      const item = this.itens[itemIndex]

      if (itemIndex === 1 && this.modalidadePersonalAvulso) {
        const valor = (item.numero_parcelas === 1 ? item.item.itemFranqueadas[0].valor_venda : item.item.itemFranqueadas[0][`valor_venda_${item.numero_parcelas}`]) || 0
        item.valor = (valor * 1) * (this.itemSelecionado.creditos_personal_avulso.quantidade || 1)
      } else if ((itemIndex !== 2 || bAplicarSelecaoParcela === true) && itemIndex > 1 && item.item && item.item.itemFranqueadas.length > 0) {
        const valor = (item.numero_parcelas === 1 ? item.item.itemFranqueadas[0].valor_venda : item.item.itemFranqueadas[0][`valor_venda_${item.numero_parcelas}`]) || 0
        item.valor = (valor * 1) * item.numero_parcelas
        // this.bloqueiaValorMaterialDidatico = false
      }

      if (campo === 'valor_parcela') {
        if (!item.numero_parcelas) {
          item.numero_parcelas = 1
        }

        if (!item.valor_parcela) {
          item.valor_parcela = 0
        }

        item.valor = item.valor_parcela * item.numero_parcelas
      } else if (campo === 'valor' || campo === 'numero_parcelas') {
        if (!item.numero_parcelas) {
          item.numero_parcelas = 1
        }

        if (campo === 'numero_parcelas' && item.numero_parcelas > this.maximoParcelas) {
          item.numero_parcelas = this.maximoParcelas
        }

        item.valor_parcela = item.valor / item.numero_parcelas
      }

      this.calcularDescontos(itemIndex)
    },

    setItem (value, index) {
      const item = this.itens[index]
      item.item = value

      if (value) {
        if (!this.editando) {
          const valorItem = value.itemFranqueadas && value.itemFranqueadas.length ? value.itemFranqueadas[0].valor_venda : 0
          item.valor = toNumber(valorItem)
        }
        this.setPlanoConta(value.plano_conta, index)

        this.calcularValoresEParcelasItem('valor', index)
      }
    },

    removerItem (index) {
      if (index > 2) {
        this.itens.splice(index, 1)
      }

      this.calcularValoresEParcelasItem('valor', index)
    },

    adicionarItem () {
      this.itens.push({
        id: null,
        item: null,
        plano_conta: this.planoContaMaterialDidatico,
        valor_parcela: 0,
        numero_parcelas: 1,
        valor: 0,
        forma_cobranca: null,
        data_vencimento: '',
        dias_subsequentes: null,
        item_entregue: false
      })
    },

    calcularTotalItens () {
      const valorTotal = round(this.itens.reduce((acc, curr) => acc + (curr.valor_total_desconto || curr.valor), 0))
      this.dadosContaReceber.valor_total = valorTotal
      this.SET_VALOR_TOTAL_ITENS(valorTotal)
    },

    ajustarFormatoValor (numero) {
      /**
       * Troca a virgula para a direita, o valor está vindo como 100,000 ao invés de 1000,00 mas também pode vir como 1.000,00
       * Independente da forma como vier, tem que retornar 1000,00
       */
      numero = numero.replace('.', '')
      let numeroArray = numero.split(',')
      let quantidadeTrocar = numeroArray[1].length - 2
      if (quantidadeTrocar > 0) {
        let parteInteira = numeroArray[0] + numeroArray[1].substring(0, quantidadeTrocar)
        let parteDecimal = numeroArray[1].substring(quantidadeTrocar, quantidadeTrocar + 2)
        return parteInteira + '.' + parteDecimal
      } else {
        return numero
      }
    },

    calcularDescontos (index) {
      let desconto = '0%'
      let descontoNumero = 0

      // Valor default
      this.itens[index].valor_total_desconto = this.itens[index].valor
      this.itens[index].valor_parcela_desconto = this.itens[index].valor_total_desconto / this.itens[index].numero_parcelas
      this.itens[index].percentual_desconto = 0
      this.itens[index].valor_desconto = 0

      if (index === 1) {
        let valorTotalComDesconto = 0

        if (this.itens[index].desconto) {
          if (this.itens[index].desconto === 'Desconto Especial') {
            let valorDesconto = this.itens[index].valor_desconto_especial ? parseFloat(this.ajustarFormatoValor(this.itens[index].valor_desconto_especial)) : 0
            if (valorDesconto > this.itens[index].valor) {
              valorDesconto = this.itens[index].valor
            }

            this.itens[index].valor_desconto_especial = numberToCurrency(valorDesconto)
            valorTotalComDesconto = this.itens[index].valor - valorDesconto
            desconto = 0
            descontoNumero = 0
            this.itens[index].percentual_desconto = 0
          } else {
            desconto = this.itens[index].desconto
            descontoNumero = toNumber(desconto.toString().replace(/\D/g, ''))
            valorTotalComDesconto = this.itens[index].valor * ((100 - descontoNumero) / 100)
            this.itens[index].percentual_desconto = descontoNumero
          }
        }

        // Valor total do curso com desconto
        valorTotalComDesconto = this.desconto_avista ? valorTotalComDesconto * ((100 - this.objFranqueada.percentual_desconto_a_vista) / 100) : valorTotalComDesconto // Desconto em pagamentos a vista
        this.itens[index].valor_total_desconto = valorTotalComDesconto
        this.itens[index].valor_parcela_desconto = this.itens[index].valor_total_desconto / this.itens[index].numero_parcelas

        // this.itens[index].percentual_desconto = descontoNumero
        this.itens[index].valor_desconto = this.itens[index].valor - this.itens[index].valor_total_desconto
      }

      this.calcularTotalItens()
    },

    setDesconto (value, index) {
      this.itens[index].desconto = value
      this.itemSelecionado.convenio_desconto = null
      this.itemSelecionado.familiar_desconto = null
      this.calcularDescontos(index)
    },

    resolveTurma (idTurma) {
      this.$store.commit('turma/SET_PAGINA_ATUAL', 1)
      let turma = this.listaTurmasFiltradaPorModalidade.filter(turma => turma.turmaId === idTurma)[0]
      if (turma === undefined) {
        // Se não achou a turma selecionada, carrega novamente e seleciona a turma
        this.$store.dispatch('turma/listar').then(() => {
          turma = this.listaTurmasFiltradaPorModalidade.filter(turma => turma.turmaId === idTurma)[0]
          this.setTurma(turma)
          this.$refs.modalTurma.hide()
        }, (err) => {
          console.log(err)
          this.$refs.modalTurma.hide()
        })
      } else {
        this.setTurma(turma)
        this.$refs.modalTurma.hide()
      }
    },

    setValorDescontoAvista (value) {
      this.desconto_avista = value
      this.calcularDescontos(1)
    },

    setNomeSuperAmigo (value) {
      if (value) {
        this.alunoSuperAmigo = value
      }
    },

    aplicarSuperAmigo () {
      this.itemSelecionado.aluno_indicador = this.alunoSuperAmigo.id
      this.itemSelecionado.aplica_desconto_super_amigos = this.descontoSuperAmigo
      this.itemSelecionado.aplica_desconto_super_amigos_turbinado = this.descontoSuperAmigoTurbinado
      this.descontoSuperAmigoAplicado = true
      this.visibilidadeModalSuperAmgio = false
    },

    removerDescontoSuperAmigo () {
      this.alunoSuperAmigo = null
      this.$refs.refNomeSuperAmigo.resetSelection()

      this.descontoSuperAmigo = false
      this.descontoSuperAmigoTurbinado = false

      this.itemSelecionado.aluno_indicador = null
      this.itemSelecionado.aplica_desconto_super_amigos = false
      this.itemSelecionado.aplica_desconto_super_amigos_turbinado = false

      this.descontoSuperAmigoAplicado = false
    },

    prepararParametros (callback) {
      this.estaValido = false
      this.$store.commit('contrato/LIMPAR_PARAMETROS_PARCELAMENTO')

      this.itens.map((item, index) => {
        const planoConta = item.plano_conta ? item.plano_conta.descricao.replace(/_\s/g, '') : ''
        let descontoAntecipacao = item.valor_parcela - item.valor_parcela_desconto
        descontoAntecipacao = descontoAntecipacao.toFixed(2)

        const param = {
          forma_cobranca: item.forma_cobranca,
          data_vencimento: item.data_vencimento,
          dias_subsequentes: item.dias_subsequentes,
          numero_parcelas: item.numero_parcelas,
          valor_parcela: item.valor_parcela_desconto,
          valor_total: item.valor_total_desconto,
          desconto_antecipacao: descontoAntecipacao,
          observacao: planoConta
        }

        this.$store.commit('contrato/SET_PARAMETROS_PARCELAMENTO', {index, value: param})
      })

      callback()
    },

    countCarregamento () {
      this.loadCount++
    },

    verificaCarregamento (numeroDeRequisicoesFeitas, requisicoes) {
      return numeroDeRequisicoesFeitas !== requisicoes
    },

    buscado () {
      this.alunoBuscado = true
    },

    setConvenioDesconto (value) {
      this.itemSelecionado.convenio_desconto = value
    },

    imprimirBoletos (redirect) {
      EventBus.$emit('imprimirBoleto', this.listaBoletos, redirect)
    },

    abrirImprimirBoleto () {
      open(this.printURL, '_blank')
    },

    buscarAgendamentos ($event) {
      $event.preventDefault()

      this.buscaPersonal = true
      this.carregandoInformacoesPersonal = true
      this.itemSelecionado.agendamento_personal = []
      const data = moment(this.dataFiltroPersonal, 'DD/MM/YYYY').hour(0).minutes(0).second(0).toISOString()

      const funcionario = this.itemSelecionado.instrutor ? this.itemSelecionado.instrutor.id : null
      const sala = this.itemSelecionado.sala_franqueada ? this.itemSelecionado.sala_franqueada.id : null
      const salaObj = this.itemSelecionado.sala_franqueada

      this.$store.commit('agendamentoPersonal/SET_FILTROS', { funcionario: funcionario, sala_franqueada: sala, data })
      this.$store.commit('indisponibilidadePersonal/SET_FILTROS', { data })
      this.$store.commit('salaFranqueada/SET_ITEM_SELECIONADO', { ...salaObj })

      Promise.all([
        this.$store.dispatch('agendamentoPersonal/listar'),
        this.$store.dispatch('indisponibilidadePersonal/listar'),
        this.listarDisponibilidade({
          data_inicial: formatarDataPadraoBancoDados(firstDayOfWeek(this.dataFiltroPersonal)),
          data_final: formatarDataPadraoBancoDados(lastDayOfWeek(this.dataFiltroPersonal))
        }),
      ])
        .then(() => {
          this.carregandoInformacoesPersonal = false
          this.filtrarNovamente = false
        })
    },

    adicionarAgendamento (options) {
      const aulasPorDiaDaSemana = 2

      let listaPorDiaSemana = this.itemSelecionado.agendamento_personal.filter(agendamento => this.formatarDataDiaSemana(agendamento.inicio) === this.formatarDataDiaSemana(options.data, false, 'nome', 'DD/MM/YYYY'))
      let obj = this.itemSelecionado.agendamento_personal.find((agendamento) => {
        const [horas, minutos] = options.horario.split(':')
        const dataInicio = moment(options.data, 'DD/MM/YYYY').hours(horas).minutes(minutos).toISOString()
        const dataFinal = moment(options.data, 'DD/MM/YYYY').hours(horas).minutes(minutos).add(1, 'hour').toISOString()
        if ((dataInicio === agendamento.inicio) || (agendamento.inicio > dataInicio && agendamento.inicio < dataFinal) || (agendamento.final > dataInicio && agendamento.final < dataFinal)) {
          return agendamento
        }
      })

      if (listaPorDiaSemana.length >= aulasPorDiaDaSemana || obj !== undefined) {
        return false
      }

      if (this.personal.aula_por_semana <= this.itemSelecionado.agendamento_personal.length) {
        return false
      }

      const [horas, minutos] = options.horario.split(':')
      const data = moment(options.data, 'DD/MM/YYYY').hours(horas).minutes(minutos)
      const horaAnterior = moment(data).subtract(1, 'hour')
      const horaPosterior = moment(data).add(59, 'minutes').add(59, 'seconds')

      const arr = this.$store.state.agendamentoPersonal.lista.concat(this.itemSelecionado.agendamento_personal)
      const horariosSelecionados = arr.filter(dataSelecionada => {
        const inicio = moment(dataSelecionada.inicio)
        if (inicio <= data && inicio > horaAnterior) {
          return true
        }

        if (inicio >= data && inicio < horaPosterior) {
          return true
        }

        return false
      })

      if (horariosSelecionados.length <= 2) {
        this.itemSelecionado.agendamento_personal.push({
          inicio: data.toISOString(),
          final: horaPosterior.toISOString()
        })
      }
    },

    removerAgendamento (index) {
      this.itemSelecionado.agendamento_personal.splice(index, 1)
      this.personalProgramado = false
    },

    resetAgendamentosPersonal (close) {
      const lista = [...this.itemSelecionado.agendamento_personal]

      lista.map(item => this.removerAgendamento(0))
      this.personalConfirmado = false
      this.filtrarNovamente = true

      if (close === true) {
        this.filtrarNovamente = false
        this.$refs.planilhapersonal.hide()
      }
    },

    imprimirContrato () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const modeloContrato = this.modeloContratoSelecionado.id
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const contratoID = this.itemSelecionadoID
      const url = `/api/contrato/imprimir/${contratoID}?Authorization=${auth}&URLModulo=${rota}&franqueada=${franqueada}&modelo_contrato=${modeloContrato}`

      // open(url, '_blank')
      var host = process.env.VUE_APP_HOST;
      window.open(`${host}${url}`, '_blank')
      
    },

    editarTextoContrato () {
      if (this.modeloContratoSelecionado.id && this.itemSelecionadoID) {
        this.$refs.editarTextoContrato.getTextoContrato()
        this.visibilidadeEditarTextoContrato = true
        this.$refs.modalImpressao.hide()
      } else {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: 'O modelo de contrato deve ser selecionado para poder editar o contrato.'
        })
      }
    },

    cancelarEditarTextoContrato () {
      this.visibilidadeEditarTextoContrato = false
      this.$refs.modalImpressao.show()
    },

    itemValorBruto (itemContaReceber) {
      return parseFloat(itemContaReceber.valor_item)
    },

    itemValorDesconto (itemContaReceber) {
      return parseFloat(itemContaReceber.valor_desconto)
    },

    itemValorLiquido (itemContaReceber) {
      return parseFloat(this.itemValorBruto(itemContaReceber) - this.itemValorDesconto(itemContaReceber))
    }
  }
}
</script>

<style>
.font-14 {
  font-size: 14px
}
.lista-item:not(:last-child) {
  padding-bottom: 1rem;
  border-bottom: 1px solid #EBECF0;
}

#planilhapersonal .modal-content .naoPossuiAgendamentosPersonal {
  height: calc(100vh - 60px);
  height: -webkit-calc(100vh - 60px);
  height: -moz-calc(100vh - 60px);
}

/* Sobrescrevendo a estilização que em alguns casos estava quebrando a tabela personal */
.table-responsive-sm #table-personal{
  height: calc(100vh - 15%)!important;
  height: -webkit-calc(100vh - 15%)!important;
  height: -moz-calc(100vh - 15%)!important;
  position: relative!important;
}
#planilhapersonal .modal-body {
  display: flex;
  flex-direction: column;
}

.color-blue {
  color:#3e59a9 !important;
}

.app-body .main{
  overflow: scroll;
}

</style>
