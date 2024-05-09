<template>
  <div class="animated fadeIn">
    <div>
      <form @submit.prevent="filtrar()">
        <div class="form-group row">
          <div class="col-md-2">
            <label for="data-funil-vendas" class="col-form-label">Data</label>

            <div class="input-group">
              <div class="input-group-prepend">
                <b-btn variant="roxo" @click="alterarDataAgendamento(-1)"><font-awesome-icon icon="angle-left" /></b-btn>
              </div>

              <calendar id="data-funil-vendas" ref="dataFunilVendas" v-model="data_agendamento" :class-name="'text-center'" @input="setFiltroData" />
              <div class="input-group-append">
                <b-btn variant="roxo" @click="alterarDataAgendamento(1)"><font-awesome-icon icon="angle-right" /></b-btn>
              </div>
            </div>
          </div>

          <div class="col-md-2">
            <label for="consultaRapida" class="col-form-label">Consulta rápida</label>
            <input id="consultaRapida" v-model="consultaRapida" placeholder="Nome ou telefone" type="text" class="form-control" maxlength="150" @input="filtrar()">
          </div>

          <div v-if="usuarioLogadoEhGestorComercial" class="col-md-2">
            <label v-help-hint="'filtroRapido-followup-consultor-responsavel'" for="consultor-responsavel-avancado" class="col-form-label">Consultor Responsavel</label>
            <g-select :id="'consultor-responsavel-avancado'"
                      :select="setConsultorComercial"
                      :value="filtros.consultor_comercial"
                      :options="listaFuncionarioConsultor"
                      class="multiselect-truncate"
                      label="apelido"
                      track-by="id"
            />
          </div>

          <div class="col-md-auto">
            <label for="hotlist" class="col-form-label">&nbsp;</label>
            <div class="d-block">
              <b-form-checkbox id="hotlist" v-model="hotlist" class="m-0" name="hotlist">Hotlist</b-form-checkbox>
            </div>
          </div>

          <div class="col-md">
            <label for="atrasados" class="col-form-label">&nbsp;</label>
            <div class="d-block">
              <b-form-checkbox id="atrasados" v-model="atrasados" class="m-0" name="atrasados" @click="atrasados = !atrasados">Follow-ups em atraso</b-form-checkbox>
            </div>
          </div>

          <!-- <div class="col"></div> -->

          <div class="d-flex justify-content-end align-items-end col-md-auto">
            <b-btn id="showPopOverColumns" variant="link" @click="showPopOverColumns = !showPopOverColumns">
              <font-awesome-icon icon="cog" /> Colunas
            </b-btn>

            <b-btn id="popOverSubtitle" variant="link" @click="showPopOverSubtitle = !showPopOverSubtitle">Legenda</b-btn>
            <!-- <button v-b-modal.checklist class="btn btn-roxo mt-3" type="button"><font-awesome-icon icon="tasks" /> Lista de atividades</button> -->

            <b-popover :show.sync="showPopOverColumns" target="showPopOverColumns" placement="bottom">
              <div class="d-block">
                <b-form-checkbox-group
                  id="grau_interesse"
                  v-model="colunasFunilVendas"
                  :options="opcoesColunasFunilVendas"
                  name="grau-interesse"
                  stacked
                  @change="SET_COLUNAS_FUNIL_VENDAS"
                />
              </div>

              <div class="pt-1 text-center">
                <b-btn variant="link" size="sm" @click="showPopOverColumns = !showPopOverColumns">Fechar</b-btn>
              </div>
            </b-popover>

            <b-popover :show.sync="showPopOverSubtitle" target="popOverSubtitle" placement="bottom">
              <div id="ct-legenda">
                <div class="py-1">
                  <span>Contato ativo</span>
                  <div data-ct="A">
                    <div v-b-tooltip title="Lead" data-interesse="L"></div>
                    <div v-b-tooltip title="Interessado" data-interesse="I"></div>
                    <div v-b-tooltip title="Hotlist" data-interesse="H"></div>
                  </div>
                </div>

                <div class="py-1">
                  <span>Contato receptivo</span>
                  <div data-ct="R">
                    <div v-b-tooltip title="Lead" data-interesse="L"></div>
                    <div v-b-tooltip title="Interessado" data-interesse="I"></div>
                    <div v-b-tooltip title="Hotlist" data-interesse="H"></div>
                  </div>
                </div>

                <hr class="my-2">

                <div class="py-1">
                  <span>Ocorrências</span>
                  <div data-ct="OA">
                    <div v-b-tooltip title="Avaliações" data-interesse="A">Avaliações</div>
                    <div v-b-tooltip title="Atividade Extra" data-interesse="AE">Atividade Extra</div>
                    <div v-b-tooltip title="Acompanhamento Pedagógico" data-interesse="AP">Acompanhamento Pedagógico</div>
                    <div v-b-tooltip title="Bônus Classes" data-interesse="BC">Bônus Classes</div>
                    <div v-b-tooltip title="Cobranças" data-interesse="C">Cobranças</div>
                    <div v-b-tooltip title="Falta" data-interesse="F">Falta</div>
                    <div v-b-tooltip title="Insatisfações" data-interesse="IN">Insatisfações</div>
                    <div v-b-tooltip title="Outros" data-interesse="O">Outros</div>
                    <!-- <div v-b-tooltip title="Pendências" data-interesse="P">Pendências</div> -->
                    <!-- <div v-b-tooltip title="Pesquisa de Satisfação" data-interesse="PS">Pesquisa de Satisfação</div> -->
                    <div v-b-tooltip title="Reposições" data-interesse="R">Reposições</div>
                    <div v-b-tooltip title="Renovações" data-interesse="RN">Renovações</div>
                    <div v-b-tooltip title="Sugestões" data-interesse="S">Sugestões</div>
                    <div v-b-tooltip title="Transferência de Turmas" data-interesse="TR">Transferência de Turmas</div>
                    <div v-b-tooltip title="Reagendamento Personal" data-interesse="RP">Reagendamento Personal</div>
                    <div v-b-tooltip title="Entrega de Atividades" data-interesse="EA">Entrega de Atividades</div>
                  </div>
                </div>

                <div class="pt-1 justify-content-center">
                  <b-btn variant="link" size="sm" @click="showPopOverSubtitle = !showPopOverSubtitle">Fechar</b-btn>
                </div>
              </div>
            </b-popover>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive-sm mb-3">
      <table id="funil-vendas-tabela" class="table-schedule table-scroll table b-table table-borderless" >
        <thead class="text-dark">
          <tr>
            <th data-column="">&nbsp;</th>
            <th v-if="colunasFunilVendas.indexOf('NP') !== -1" data-column="">Negociação de Parcerias</th>
            <th v-if="colunasFunilVendas.indexOf('WCI') !== -1" data-column="">Contato Inicial</th>
            <th v-if="colunasFunilVendas.indexOf('WRTFL') !== -1" data-column="">Retorno Telefônico</th>
            <th v-if="colunasFunilVendas.indexOf('WRAP') !== -1" data-column="">Apresentação Pessoal</th>
            <!-- COM HORÁRIO -->
            <th v-if="colunasFunilVendas.indexOf('OA') !== -1" data-column="" class="">Ocorrências</th>
            <!-- SEM HORÁRIO -->
            <th v-if="colunasFunilVendas.indexOf('OA') !== -1" data-column="" class="ocorrencias"></th>
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <perfect-scrollbar id="funil-vendas-scroll" class="d-flex flex-column">
            <div v-if="cardLoad" class="form-loading">
              <load-placeholder :loading="cardLoad" />
            </div>

            <div v-if="!listaComercial.length && !cardLoad" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <template v-else>
              <!-- rowspan="2" -->
              <tr v-for="item in listaComercial" :key="item.id">

                <td :data-time="item" data-label="">{{ item.toString().includes(':') ? item : '' }}</td>

                <td v-if="colunasFunilVendas.indexOf('NP') !== -1" data-label="">
                  <div class="card-wrapper">
                    <div
                      v-for="element in workflow['NP'][item]"
                      :key="element.id"
                      :id="`popOverNP_${item}_${element.id}`"
                      class="list-group-item ct-card ct-convenio justify-content-between"
                      @click="redirect(element.id)"
                    >

                      <div class="truncate">
                        <span>{{ element.pessoa.nome_fantasia }}</span>
                      </div>
                      <div class="tel truncate">
                        <span>{{ element.nome_contato }} </span>
                      </div>

                      <b-popover :target="`popOverNP_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Nome:</label>
                          <div>{{ element.pessoa.nome_fantasia }}</div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Contato:</label>
                          <div>{{ element.nome_contato }}</div>
                        </div>

                        <div v-if="verificaFuncionario(element.consultor_funcionario)" class="form-group">
                          <label class="form-label">Consultor:</label>
                          <div>{{ element.consultor_funcionario ? element.consultor_funcionario.apelido : '' }}</div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>

                <td v-if="colunasFunilVendas.indexOf('WCI') !== -1" data-label="">
                  <div class="card-wrapper">
                    <div
                      v-b-modal.modalFollowUp
                      v-for="element in workflow['WCI'][item]"
                      :class="element.tipo_lead === 'A' ? 'ct-ativo' : 'ct-receptivo'"
                      :data-interesse="element.grau_interesse"
                      :key="element.id"
                      :id="`popOverWCI_${item}_${element.id}`"
                      class="list-group-item ct-card justify-content-between"
                      @click="interessadoId = element.id"
                    >
                      <div class="truncate">
                        <span>{{ element.nome }}</span>
                      </div>
                      <div :class="{'tel' : element.telefone_contato || element.telefone_secundario}" class="truncate">
                        <span>{{ element.telefone_contato ? element.telefone_contato.substr(-4) : (element.telefone_secundario ? '('+ element.telefone_secundario.substr(-4) +')' : '') }} </span>
                      </div>

                      <font-awesome-icon v-if="!element.telefone_contato && !element.telefone_secundario" icon="phone-slash" />

                      <b-popover :target="`popOverWCI_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Nome:</label>
                          <div>{{ element.nome }}</div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Telefone:</label>
                          <div>{{ (element.telefone_contato || element.telefone_secundario) | formatarTelefone }}</div>
                        </div>

                        <div v-if="verificaFuncionario(element.consultor_responsavel_funcionario)" class="form-group">
                          <label class="form-label">Consultor:</label>
                          <div>{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido : '' }}</div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>

                <td v-if="colunasFunilVendas.indexOf('WRTFL') !== -1" data-label="">
                  <div class="card-wrapper">
                    <div
                      v-b-modal.modalFollowUp
                      v-for="element in workflow['WRTFL'][item]"
                      :class="element.tipo_lead === 'A' ? 'ct-ativo' : 'ct-receptivo'"
                      :data-interesse="element.grau_interesse"
                      :key="element.id"
                      :id="`popOverWRTFL_${item}_${element.id}`"
                      class="list-group-item ct-card justify-content-between"
                      @click="interessadoId = element.id"
                    >
                      <div class="truncate">
                        <span>{{ element.nome }}</span>
                      </div>
                      <div :class="{'tel' : element.telefone_contato || element.telefone_secundario}" class="truncate">
                        <span>{{ element.telefone_contato ? element.telefone_contato.substr(-4) : (element.telefone_secundario ? '('+ element.telefone_secundario.substr(-4) +')' : '') }} </span>
                      </div>

                      <font-awesome-icon v-if="!element.telefone_contato && !element.telefone_secundario" icon="phone-slash" />

                      <b-popover :target="`popOverWRTFL_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Nome:</label>
                          <div>{{ element.nome }}</div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Telefone:</label>
                          <div>{{ (element.telefone_contato || element.telefone_secundario) | formatarTelefone }}</div>
                        </div>

                        <div v-if="verificaFuncionario(element.consultor_responsavel_funcionario)" class="form-group">
                          <label class="form-label">Consultor:</label>
                          <div>{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido : '' }}</div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>

                <td v-if="colunasFunilVendas.indexOf('WRAP') !== -1" data-label="">
                  <div class="card-wrapper">
                    <div
                      v-b-modal.modalFollowUp
                      v-for="element in workflow['WRAP'][item]"
                      :class="element.tipo_lead === 'A' ? 'ct-ativo' : 'ct-receptivo'"
                      :data-interesse="element.grau_interesse"
                      :key="element.id"
                      :id="`popOverWRAP_${item}_${element.id}`"
                      class="list-group-item ct-card justify-content-between"
                      @click="interessadoId = element.id"
                    >
                      <div class="truncate">
                        <span>{{ element.nome }}</span>
                      </div>
                      <div :class="{'tel' : element.telefone_contato || element.telefone_secundario}" class="truncate">
                        <span>{{ element.telefone_contato ? element.telefone_contato.substr(-4) : (element.telefone_secundario ? '('+ element.telefone_secundario.substr(-4) +')' : '') }} </span>
                      </div>

                      <font-awesome-icon v-if="!element.telefone_contato && !element.telefone_secundario" icon="phone-slash" />

                      <b-popover :target="`popOverWRAP_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Nome:</label>
                          <div>{{ element.nome }}</div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Telefone:</label>
                          <div>{{ (element.telefone_contato || element.telefone_secundario) | formatarTelefone }}</div>
                        </div>

                        <div v-if="verificaFuncionario(element.consultor_responsavel_funcionario)" class="form-group">
                          <label class="form-label">Consultor:</label>
                          <div>{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido : '' }}</div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>

                <!-- <td v-if="colunasFunilVendas.indexOf('OA') !== -1 && item === '07:30'" :rowspan="listaComercial.length" data-label="" class="ocorrencias"> -->
                <!-- COM HORÁRIO -->
                <td v-if="colunasFunilVendas.indexOf('OA') !== -1" data-label="" class="">
                  <div class="card-wrapper">
                    <!-- v-for="element in workflow['OA'][item]" -->
                    <div
                      v-for="element in workflow['OAT'][item]"
                      :key="element.id"
                      :id="`popOverNP_${item}_${element.id}`"
                      :data-tipo="element.tipo_ocorrencia.tipo"
                      class="list-group-item ct-card ct-ocorrencias"
                      @click="alterar(element)"
                    >
                      <!-- <div class="ct-consultor">{{ element.funcionario ? element.funcionario.apelido.charAt(0) : '' }}</div> -->
                      <div class="truncate mr-auto">
                        <span>{{ element.aluno.pessoa.nome_contato }}</span>
                      </div>
                      <!-- <div class="flex-shrink-1 truncate">
                        <span>{{ element.funcionario ? element.funcionario.apelido : '' }} </span>
                      </div> -->

                      <b-popover :target="`popOverNP_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Aluno:</label>
                          <div>{{ element.aluno.pessoa.nome_contato }}</div>
                          <div></div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Funcionário:</label>
                          <div>{{ element.funcionario ? element.funcionario.apelido : '' }}</div>
                          <div></div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Conclusão em:</label>
                          <div>{{ element.data_conclusao ? element.data_conclusao : '' }}</div>
                          <div></div>
                        </div>
                      </b-popover>
                    </div>
                  </div>

                </td>
                <!-- SEM HORÁRIO -->
                <td v-if="colunasFunilVendas.indexOf('OA') !== -1" data-label="" class="ocorrencias">
                  <div class="card-wrapper">
                    <div
                      v-for="element in workflow['OA'][item]"
                      :key="element.id"
                      :id="`popOverNP_${item}_${element.id}`"
                      :data-tipo="element.tipo_ocorrencia.tipo"
                      class="list-group-item ct-card ct-ocorrencias"
                      @click="alterar(element)"
                    >
                      <div class="truncate mr-auto">
                        <span>{{ element.aluno.pessoa.nome_contato }}</span>
                      </div>

                      <b-popover :target="`popOverNP_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Aluno:</label>
                          <div>{{ element.aluno.pessoa.nome_contato }}</div>
                          <div></div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Funcionário:</label>
                          <div>{{ element.funcionario ? element.funcionario.apelido : '' }}</div>
                          <div></div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Conclusão em:</label>
                          <div>{{ element.data_conclusao ? element.data_conclusao : '' }}</div>
                          <div></div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>
              </tr>
            </template>

          </perfect-scrollbar>
        </tbody>
      </table>
    </div>

    <template v-if="atrasados">
      <h5 class="title-module mb-2">Follow-ups em atraso</h5>
      <div class="table-responsive-sm followup-em-atraso">

        <table id="funil-vendas-tabela-atrasados" class="table-schedule table-scroll table b-table table-borderless cards-atrasados" >
          <thead class="text-dark">
            <tr>
              <th data-column="">&nbsp;</th>
              <th v-if="colunasFunilVendas.indexOf('NP') !== -1" data-column="">Negociação de Parcerias</th>
              <th v-if="colunasFunilVendas.indexOf('WCI') !== -1" data-column="">Contato Inicial</th>
              <th v-if="colunasFunilVendas.indexOf('WRTFL') !== -1" data-column="">Retorno Telefônico</th>
              <th v-if="colunasFunilVendas.indexOf('WRAP') !== -1" data-column="">Apresentação Pessoal</th>
              <!-- TEMPORARIAMENTE COMENTADO -->
              <th v-if="colunasFunilVendas.indexOf('OA') !== -1" data-column="" class="ocorrencias">Ocorrências</th>
              <!-- TEMPORARIAMENTE COMENTADO -->
            </tr>
          </thead>

          <tbody ref="scroll-wrap">
            <perfect-scrollbar id="funil-vendas-scroll" class="d-flex flex-column">
              <div v-if="atrasadosLoad" class="form-loading">
                <load-placeholder :loading="atrasadosLoad" />
              </div>

              <div v-if="!listaAtrasados.length && !atrasadosLoad" class="busca-vazia">
                <p>Nenhum resultado encontrado.</p>
              </div>

              <!-- <template v-else> -->
              <!-- <template>
              </template> -->
              <!-- rowspan="2" -->
              <tr v-for="item in listaAtrasados" :key="item.id">
                <td :data-time="item" data-label="">{{ item }}</td>

                <td v-if="colunasFunilVendas.indexOf('NP') !== -1" data-label="">
                  <div class="card-wrapper">
                    <div
                      v-b-modal.modalFollowUp
                      v-for="element in workflowAtrasados['NP'][item]"
                      :key="element.id"
                      :id="`popOverNP_${item}_${element.id}`"
                      class="list-group-item ct-card justify-content-between ct-convenio"
                      @click="redirect(element.id)"
                    >
                      <!-- <div class="ct-consultor">{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido.charAt(0) : '' }}</div> -->
                      <div class="truncate">
                        <span>{{ element.pessoa.nome_fantasia }}</span>
                      </div>
                      <div :class="{'tel' : element.telefone_contato || element.telefone_secundario}" class="truncate">
                        <span>{{ element.nome_contato }} </span>
                      </div>

                      <b-popover :target="`popOverNP_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Nome:</label>
                          <div>{{ element.pessoa.nome_fantasia }}</div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Contato:</label>
                          <div>{{ element.nome_contato }}</div>
                        </div>

                        <div v-if="verificaFuncionario(element.consultor_funcionario)" class="form-group">
                          <label class="form-label">Consultor:</label>
                          <div>{{ element.consultor_funcionario ? element.consultor_funcionario.apelido : '' }}</div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>

                <td v-if="colunasFunilVendas.indexOf('WCI') !== -1" data-label="">
                  <div class="card-wrapper">
                    <div
                      v-b-modal.modalFollowUp
                      v-for="element in workflowAtrasados['WCI'][item]"
                      :class="element.tipo_lead === 'A' ? 'ct-ativo' : 'ct-receptivo'"
                      :data-interesse="element.grau_interesse"
                      :key="element.id"
                      :id="`popOverWCI_${item}_${element.id}`"
                      class="list-group-item ct-card justify-content-between"
                      @click="interessadoId = element.id"
                    >
                      <!-- <div class="ct-consultor">{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido.charAt(0) : '' }}</div> -->
                      <div class="truncate">
                        <span>{{ element.nome }}</span>
                      </div>
                      <div :class="{'tel' : element.telefone_contato || element.telefone_secundario}" class="truncate">
                        <span>{{ element.telefone_contato ? element.telefone_contato.substr(-4) : (element.telefone_secundario ? '('+ element.telefone_secundario.substr(-4) +')' : '') }} </span>
                      </div>
                      <!-- <div v-if="element.tipo_workflow === 'ST'" class="st-card"> -->
                      <font-awesome-icon v-if="!element.telefone_contato && !element.telefone_secundario" icon="phone-slash" />
                      <!-- </div> -->

                      <b-popover :target="`popOverWCI_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Nome:</label>
                          <div>{{ element.nome }}</div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Telefone:</label>
                          <div>{{ (element.telefone_contato || element.telefone_secundario) | formatarTelefone }}</div>
                        </div>

                        <div v-if="verificaFuncionario(element.consultor_responsavel_funcionario)" class="form-group">
                          <label class="form-label">Consultor:</label>
                          <div>{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido : '' }}</div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>

                <td v-if="colunasFunilVendas.indexOf('WRTFL') !== -1" data-label="">
                  <div class="card-wrapper">
                    <div
                      v-b-modal.modalFollowUp
                      v-for="element in workflowAtrasados['WRTFL'][item]"
                      :class="element.tipo_lead === 'A' ? 'ct-ativo' : 'ct-receptivo'"
                      :data-interesse="element.grau_interesse"
                      :key="element.id"
                      :id="`popOverWRTFL_${item}_${element.id}`"
                      class="list-group-item ct-card justify-content-between"
                      @click="interessadoId = element.id"
                    >
                      <!-- <div class="ct-consultor">{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido.charAt(0) : '' }}</div> -->
                      <div class="truncate">
                        <span>{{ element.nome }}</span>
                      </div>
                      <div :class="{'tel' : element.telefone_contato || element.telefone_secundario}" class="truncate">
                        <span>{{ element.telefone_contato ? element.telefone_contato.substr(-4) : (element.telefone_secundario ? '('+ element.telefone_secundario.substr(-4) +')' : '') }} </span>
                      </div>

                      <font-awesome-icon v-if="!element.telefone_contato && !element.telefone_secundario" icon="phone-slash" />

                      <b-popover :target="`popOverWRTFL_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Nome:</label>
                          <div>{{ element.nome }}</div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Telefone:</label>
                          <div>{{ (element.telefone_contato || element.telefone_secundario) | formatarTelefone }}</div>
                        </div>

                        <div v-if="verificaFuncionario(element.consultor_responsavel_funcionario)" class="form-group">
                          <label class="form-label">Consultor:</label>
                          <div>{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido : '' }}</div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>

                <td v-if="colunasFunilVendas.indexOf('WRAP') !== -1" data-label="">
                  <div class="card-wrapper">
                    <div
                      v-b-modal.modalFollowUp
                      v-for="element in workflowAtrasados['WRAP'][item]"
                      :class="element.tipo_lead === 'A' ? 'ct-ativo' : 'ct-receptivo'"
                      :data-interesse="element.grau_interesse"
                      :key="element.id"
                      :id="`popOverWRAP_${item}_${element.id}`"
                      class="list-group-item ct-card justify-content-between"
                      @click="interessadoId = element.id"
                    >
                      <!-- <div class="ct-consultor">{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido.charAt(0) : '' }}</div> -->
                      <div class="truncate">
                        <span>{{ element.nome }}</span>
                      </div>
                      <div :class="{'tel' : element.telefone_contato || element.telefone_secundario}" class="truncate">
                        <span>{{ element.telefone_contato ? element.telefone_contato.substr(-4) : (element.telefone_secundario ? '('+ element.telefone_secundario.substr(-4) +')' : '') }} </span>
                      </div>

                      <font-awesome-icon v-if="!element.telefone_contato && !element.telefone_secundario" icon="phone-slash" />

                      <b-popover :target="`popOverWRAP_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Nome:</label>
                          <div>{{ element.nome }}</div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Telefone:</label>
                          <div>{{ (element.telefone_contato || element.telefone_secundario) | formatarTelefone }}</div>
                        </div>

                        <div v-if="verificaFuncionario(element.consultor_responsavel_funcionario)" class="form-group">
                          <label class="form-label">Consultor:</label>
                          <div>{{ element.consultor_responsavel_funcionario ? element.consultor_responsavel_funcionario.apelido : '' }}</div>
                        </div>
                      </b-popover>
                    </div>
                  </div>
                </td>

                <td v-if="colunasFunilVendas.indexOf('OA') !== -1" data-label="" class="ocorrencias">
                  <div class="card-wrapper">
                    <div
                      v-for="element in workflowAtrasados['OA'][item]"
                      :key="element.id"
                      :id="`popOverNP_${item}_${element.id}`"
                      :data-tipo="element.tipo_ocorrencia.tipo"
                      class="list-group-item ct-card ct-ocorrencias"
                      @click="alterar(element)"
                    >
                      <div class="truncate">
                        <span>{{ element.aluno.pessoa.nome_contato }}</span>
                      </div>

                      <b-popover :target="`popOverNP_${item}_${element.id}`" :delay="tempoDelayPopoverCard" placement="bottom" triggers="hover" class="popover-card">
                        <div class="form-group">
                          <label class="form-label">Aluno:</label>
                          <div>{{ element.aluno.pessoa.nome_contato }}</div>
                          <div></div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Funcionário:</label>
                          <div>{{ element.funcionario ? element.funcionario.apelido : '' }}</div>
                          <div></div>
                        </div>

                        <div class="form-group">
                          <label class="form-label">Conclusão em:</label>
                          <div>{{ element.data_conclusao ? element.data_conclusao : '' }}</div>
                          <div></div>
                        </div>
                      </b-popover>
                    </div>
                  </div>

                </td>

              </tr>

            </perfect-scrollbar>
          </tbody>
        </table>
      </div>
    </template>

    <!-- Form followup -->
    <b-modal id="modalFollowUp" ref="modalFollowUp" size="lg" centered no-close-on-backdrop hide-header hide-footer  @hide="modalFollowUp = false" @hidden="buildFunil()">
      <Followup ref="formFollowUp" :loaded="modalFollowUpLoaded"  :back="modalFollowUpClosed" is-modal  />
    </b-modal>

    <!-- Form ocorrência acadêmica -->
    <modal-formulario ref="modalFormularioOcorrenciaAcademica" :usuario-logado="usuarioLogado" :read-only="readOnly" :lista-de-tipo-ocorrencia="listaTipoOcorrencia" :lista-de-funcionarios="listaFuncionario" :cancelar-dados="cancelarModal" @fechar="cancelarModal" @filtrar="filtrar()" @hide="visibleFormularioOcorrenciaAcademica = false"/>

    <Checklist />
  </div>
</template>

<script>
import moment from 'moment'
import {mapState, mapMutations, mapActions} from 'vuex'
import {Checklist} from '../../components/'
import Followup from '../interessados/Followup.vue'
import ModalFormulario from '../ocorrencia-academica/ModalFormulario.vue'
import {dateToString} from '../../utils/date'

export default {
  name: 'ListaFunilVendas',

  components: {
    Checklist,
    Followup,
    ModalFormulario
  },

  data () {
    return {
      data_agendamento: moment(new Date()).format('DD/MM/YYYY'),
      showPopOverColumns: false,
      showPopOverSubtitle: false,
      tempoDelayPopoverCard: {show: 800, hide: 50},
      interessadoId: null,
      cardLoad: false,
      atrasadosLoad: false,

      consultaRapida: null,
      hotlist: false,
      atrasados: false,

      opcoesColunasFunilVendas: [
        {value: 'NP', text: 'Negociação de Parcerias'},
        {value: 'WCI', text: 'Contato Inicial'},
        {value: 'WRTFL', text: 'Retorno Telefônico'},
        {value: 'WRAP', text: 'Apresentação Pessoal'},
        {value: 'OA', text: 'Ocorrências'}
      ],

      workflow: {
        NP: {},
        WCI: {},
        WRTFL: {},
        WRAP: {},
        OAT: {},
        OA: {}
      },

      workflowAtrasados: {
        NP: {},
        WCI: {},
        WRTFL: {},
        WRAP: {},
        OA: {}
      },

      listaComercial: [],
      listaAtrasados: [],

      listaFunilConvenios: [],
      listaFunilInteressados: [],
      listaFunilOcorrencias: [],

      listaFunilConveniosAtrasados: [],
      listaFunilInteressadosAtrasados: [],
      listaFunilOcorrenciasAtrasados: [],

      modalOcorrenciaAcademica: false,
      readOnly: false,

      modalFollowUp: false
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('interessados', {listaInteressados: 'lista', funilVendas: 'funilVendas', filtros: 'filtros', totalInteressados: 'totalItens', colunasFunilVendas: 'colunasFunilVendas', colunasPadraoFunil: 'colunasPadraoFunil'}),
    ...mapState('root', {usuarioLogado: 'usuarioLogado'}),
    ...mapState('funcionario', {listaFuncionarioRequisicao: 'lista'}),
    ...mapState('tipoOcorrencia', {listaTipoOcorrenciaRequisicao: 'lista'}),

    listaFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionarioRequisicao]
      }
    },

    listaFuncionarioConsultor: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionarioRequisicao.filter(funcionario => funcionario.consultor === true || funcionario.cargo.tipo === 'GER')]
      }
    },

    listaTipoOcorrencia: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaTipoOcorrenciaRequisicao]
      }
    },

    usuarioLogadoEhGestorComercial: {
      get () {
        return this.usuarioLogado.funcionarios && this.usuarioLogado.funcionarios.some(func => !!func.gestor_comercial)
      }
    }
  },

  watch: {
    hotlist () {
      this.filtrar()
    }
  },

  mounted () {
    if (this.colunasFunilVendas.length === 0) {
      this.SET_COLUNAS_FUNIL_VENDAS(this.colunasPadraoFunil)
    }
    this.$store.commit('root/SET_SHOW_BREADCRUMBS', false)
    this.buildEstruturaBasicaTabela()
    this.buildFunil()
    this.listarTipoOcorrencia()
    this.listarFuncionario()
  },

  destroyed () {
    this.$store.commit('root/SET_SHOW_BREADCRUMBS', true)
  },

  methods: {
    ...mapActions('interessados', {listarInteressados: 'listar', listarFunilVendas: 'listarFunilVendas'}),
    ...mapActions('funcionario', {listarFuncionario: 'listar'}),
    ...mapActions('tipoOcorrencia', {listarTipoOcorrencia: 'listar'}),
    ...mapMutations('interessados', ['SET_COLUNAS_FUNIL_VENDAS']),

    verificaFuncionario (consultor) {
      // Função checa se o registro do funil é pertencente ao usuário logado. Se não for, retorna true para mostrar
      // na tela de qual consultor que é o registro.
      const naoPossuiRegistroFuncionario = this.usuarioLogado.funcionarios === undefined || !this.usuarioLogado.funcionarios.length
      if (naoPossuiRegistroFuncionario) {
        return true
      }
      return !!consultor && !!this.usuarioLogado.funcionarios[0] ? consultor.id !== this.usuarioLogado.funcionarios[0].id : false
    },

    setConsultorComercial (value) {
      this.filtros.consultor_comercial = value
      this.buildFunil()
    },

    buildFunil () {
      this.cardLoad = true
      this.atrasadosLoad = true
      this.listarFunilVendas().then(() => {
        this.listaFunilConvenios = this.funilVendas.convenios
        this.listaFunilInteressados = this.funilVendas.interessados
        this.listaFunilOcorrencias = this.funilVendas.ocorrenciasAcademicas.sort((a, b) => {
          if (a.tipo_ocorrencia.tipo < b.tipo_ocorrencia.tipo) {
            return -1
          }
          if (a.tipo_ocorrencia.tipo > b.tipo_ocorrencia.tipo) {
            return 1
          }
          return 0
        })

        this.listaFunilConveniosAtrasados = this.funilVendas.conveniosAtrasados
        this.listaFunilInteressadosAtrasados = this.funilVendas.interessadosAtrasados
        this.listaFunilOcorrenciasAtrasados = this.funilVendas.ocorrenciasAcademicasAtrasado

        this.setData()
        this.timeFocus()
        this.buildAtrasados()
      })
    },

    buildEstruturaBasicaTabela () {
      for (let i = 7; i < 22; i++) {
        let hora = i >= 10 ? '' : '0'
        hora += i

        if (i > 7) {
          this.listaComercial.push(hora + ':00')
        }

        this.listaComercial.push(hora + ':30')
      }
    },

    setFiltroData (value) {
      this.filtros.data_agendamento = value
      this.buildFunil()
    },

    setData () {
      this.workflow = {
        NP: {},
        WCI: {},
        WRTFL: {},
        WRAP: {},
        OAT: {},
        OA: {}
      }

      this.listaFunilConvenios
        .filter(item => {
          if (this.consultaRapida) {
            if (!item.pessoa.nome_fantasia.toLowerCase().includes(this.consultaRapida.toLowerCase()) &&
              !(item.nome_contato && item.nome_contato.includes(this.consultaRapida.toLowerCase()))
            ) {
              return false
            }
          }

          return true
        })
        .map(item => {
          if (item.situacao === 'EN') {
            const time = this.getTime(new Date(item.horario_proximo_contato.split('+')[0]))

            if (this.workflow['NP'][time] === undefined) {
              this.workflow['NP'][time] = []
            }

            this.workflow['NP'][time].push(item)
            this.workflow['NP'] = {...this.workflow['NP']}
          }
        })

      this.listaFunilInteressados
        .filter(item => {
          if (this.hotlist === true && item.grau_interesse !== 'H') {
            return false
          }

          if (this.consultaRapida) {
            if (!item.nome.toLowerCase().includes(this.consultaRapida.toLowerCase()) &&
              !(item.telefone_contato && item.telefone_contato.includes(this.consultaRapida.toLowerCase())) &&
              !(item.telefone_secundario && item.telefone_secundario.includes(this.consultaRapida.toLowerCase()))
            ) {
              return false
            }
          }

          return true
        })
        .map(item => {
          const time = this.getTime(new Date(item.agendaComerciais[0].data_agendamento.split('+')[0]))

          if (item.workflow.tipo_workflow === 'WRMP' || item.workflow.tipo_workflow === 'WRMC') {
            return
          }

          if (this.workflow[item.workflow.tipo_workflow][time] === undefined) {
            this.workflow[item.workflow.tipo_workflow][time] = []
          }

          this.workflow[item.workflow.tipo_workflow][time].push(item)
          this.workflow[item.workflow.tipo_workflow] = {...this.workflow[item.workflow.tipo_workflow]}
        })

      /* OCORRÊNCIAS ================================== */
      let tipoAnterior = null

      let i = 0
      let linha = 1

      this.listaFunilOcorrencias
        .filter(item => {
          if (this.consultaRapida) {
            if (!item.aluno.pessoa.nome_contato.toLowerCase().includes(this.consultaRapida.toLowerCase()) &&
            !item.aluno.pessoa.nome_fantasia.toLowerCase().includes(this.consultaRapida.toLowerCase())
            ) {
              return false
            }
          }

          return true
        }).map((item, index) => {
          let time = null
          let tipo = null

          if (item.data_proximo_contato) {
          // COM HORA
            time = this.getTime(new Date(item.data_proximo_contato.split(/(Z|[+-]\d{2}:?\d{2})/)[0]))
            tipo = 'OAT'
          } else {
          // SEM HORA
            tipo = 'OA'

            if (index > 0 && tipoAnterior !== item.tipo_ocorrencia.tipo) {
              tipoAnterior = item.tipo_ocorrencia.tipo
              linha = 1
              i++
            } else if (linha > 3) {
              linha = 1
              i++
            }

            time = this.listaComercial[i]

            if (!time) {
              this.listaComercial.push(index)
              time = index
            }

            linha++
          }

          if (this.workflow[tipo][time] === undefined) {
            this.workflow[tipo][time] = []
          }

          this.workflow[tipo][time].push(item)
          this.workflow[tipo] = {...this.workflow[tipo]}
        })
      /* OCORRÊNCIAS ================================== */

      this.cardLoad = false
    },

    timeFocus (time) {
      setTimeout(() => {
        if (this.listaComercial.length < 48) {
          this.timeFocus(time)
          return
        }

        time = time || this.getTime(new Date())
        const focus = document.querySelector(`[data-time="${time}"]`)
        const scroll = document.querySelector('#funil-vendas-scroll')

        scroll.scrollTo(0, focus.offsetTop)
      }, 100)
    },

    getTime (datetime) {
      let hora = datetime.getHours()
      hora = (hora < 10 ? '0' : '') + hora

      let minuto = datetime.getMinutes()
      minuto = minuto >= 30 ? '30' : '00'

      return hora + ':' + minuto
    },

    buildAtrasados () {
      this.workflowAtrasados = {
        NP: {},
        WCI: {},
        WRTFL: {},
        WRAP: {},
        OA: {}
      }

      this.listaAtrasados = []

      this.listaFunilInteressadosAtrasados
        .filter(item => {
          if (this.hotlist === true && item.grau_interesse !== 'H') {
            return false
          }

          if (this.consultaRapida) {
            if (!item.nome.toLowerCase().includes(this.consultaRapida) &&
              !(item.telefone_contato && item.telefone_contato.includes(this.consultaRapida)) &&
              !(item.telefone_secundario && item.telefone_secundario.includes(this.consultaRapida))
            ) {
              return false
            }
          }

          return true
        })
        .map(item => {
          const date = dateToString(new Date(item.agendaComerciais[0].data_agendamento))

          if (this.listaAtrasados.indexOf(date) === -1) {
            this.listaAtrasados.push(date)
          }

          if (item.workflow.tipo_workflow === 'WRMP' || item.workflow.tipo_workflow === 'WRMC') {
            return
          }

          // if (item.workflow.tipo_workflow === 'ST' || item.workflow.tipo_workflow === 'CT') {
          // item.tipo_workflow = item.workflow.tipo_workflow
          // item.workflow.tipo_workflow = 'CI'
          // }

          if (this.workflowAtrasados[item.workflow.tipo_workflow][date] === undefined) {
            this.workflowAtrasados[item.workflow.tipo_workflow][date] = []
          }

          this.workflowAtrasados[item.workflow.tipo_workflow][date].push(item)
          this.workflowAtrasados[item.workflow.tipo_workflow] = {...this.workflowAtrasados[item.workflow.tipo_workflow]}
        })

      this.listaFunilOcorrenciasAtrasados
        .filter(item => {
          if (this.consultaRapida) {
            if (!item.aluno.pessoa.nome_contato.toLowerCase().includes(this.consultaRapida) &&
                !(item.funcionario.apelido && item.funcionario.apelido.toLowerCase().includes(this.consultaRapida))
            ) {
              return false
            }
          }

          return true
        })
        .map(item => {
          const date = dateToString(new Date(item.data_proximo_contato))

          if (this.listaAtrasados.indexOf(date) === -1) {
            this.listaAtrasados.push(date)
          }

          if (this.workflowAtrasados['OA'][date] === undefined) {
            this.workflowAtrasados['OA'][date] = []
          }

          this.workflowAtrasados['OA'][date].push(item)
          this.workflowAtrasados['OA'] = {...this.workflowAtrasados['OA']}
        })

      this.listaAtrasados.sort()
      this.atrasadosLoad = false
    },

    executaFiltroRapido () {
      const workflow = {
        'NP': 1,
        'WCI': 2,
        'WRTFL': 3,
        'WRAP': 4,
        'OA': 5
      }

      let colunas = []
      this.colunasFunilVendas.map(coluna => {
        colunas.push(workflow[coluna])
      })
    },

    filtrar () {
      this.buildFunil()
    },

    redirect (id) {
      this.$router.push(`/cadastros/convenio/atualizar/${id}`)
    },

    alterar (item) {
      this.listarTipoOcorrencia()
      this.listarFuncionario()
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          this.readOnly = true
        } else {
          this.readOnly = false
        }

        this.$refs.modalFormularioOcorrenciaAcademica.SET_ITEM_SELECIONADO_ID(item.id)
        this.modalOcorrenciaAcademica = true
        this.$refs.modalFormularioOcorrenciaAcademica.visibleFormularioOcorrenciaAcademica = true
        this.$refs.modalFormularioOcorrenciaAcademica.buscarDadosFormulario()
      }
    },

    cancelarModal () {
      this.$store.commit('ocorrenciaAcademica/LIMPAR_ITEM_SELECIONADO')
      this.modalOcorrenciaAcademica = false
      this.$refs.modalFormularioOcorrenciaAcademica.visibleFormularioOcorrenciaAcademica = false
      this.$refs.modalFormularioOcorrenciaAcademica.isEdit = false
    },
    modalFollowUpLoaded(){
      this.$refs.formFollowUp.interessadoId = this.interessadoId
      this.$refs.formFollowUp.criarTela()
    },
    modalFollowUpClosed () {
      this.$refs.formFollowUp.interessadoId = null
      this.$refs.formFollowUp.isEdit = false
      this.$refs.modalFollowUp.hide()
    },

    alterarDataAgendamento (dias) {
      if(this.data_agendamento == null){
        this.data_agendamento = new Date();
      }
      // console.log('data:',this.data_agendamento)
      const data = moment(this.data_agendamento, 'DD/MM/YYYY').add(dias, 'days')
      this.$refs.dataFunilVendas.setSelectedDate({ date: data })
    }
  }
}
</script>
<style scoped>
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
.list-group {
  width: 100%;
  min-height: 20px;
}
.list-group-item {
  cursor: move;
}
.ct-card {
  display: flex;
  position: relative;
  padding-left: 1rem;
  cursor: default;
  min-width: 100%;
  max-width: 100%;
  padding: 1px;
  align-items: center;
  font-size: 0.7rem;
}

.cards-atrasados .ct-card {
  max-height: 25px;
}

.ct-card .ct-consultor {
  color: #414245;
  background-color: #E9EBF7;
  padding: 2px 1px;
  margin: 0 2px;
  border-radius: 50%;
  font-weight: 900;
  font-size: 8px;
  width: 15px;
  height: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.ct-consultor~div {
  display: flex;
  align-items: center;
  /*
  -ms-flex: 1 1 auto!important;
  flex: 1 1 auto!important;
  */
  max-width: 100%;
}

#grau_interesse.btn-group {
  display: inline;
}

/* card full color */
.ct-card {
  cursor: pointer;
  border-width: 0;
  border-style: none;
  border-color: none;

  /* min-height: 25px;
  margin-bottom: 1px; */
  /* box-shadow: inset 0 0 0 1px #000; */
}
.ct-card:before {
  display: none;
}
.ct-ativo.ct-card {
  background-color: #ea7124;
}
.ct-card.ct-convenio {
  background-color: #20c997;
}

/* .ct-card > div {
  width: 100%;
} */

/* .card-wrapper > div:not(:nth-last-of-type(1)):not(:nth-last-of-type(2)):not(:nth-last-of-type(3)) {
  margin-bottom: 1px;
} */

/* TIPO CARD OCORRÊNCIA */
/* .ct-card.ct-ocorrencias {
  background-color: #dddddd;
} */
/* BC - Bônus Classes */
.ct-card.ct-ocorrencias[data-tipo="BC"],
#ct-legenda div[data-ct='OA'] div[data-interesse='BC'] {
  background-color: #7d96e0;
}
/* IN - Insatisfações */
.ct-card.ct-ocorrencias[data-tipo="IN"],
#ct-legenda div[data-ct='OA'] div[data-interesse='IN'] {
  background-color: #fa94e0;
}
/* S - Sugestões */
.ct-card.ct-ocorrencias[data-tipo="S"],
#ct-legenda div[data-ct='OA'] div[data-interesse='S'] {
  background-color: #85d017;
}
/* O - Outros */
.ct-card.ct-ocorrencias[data-tipo="O"],
#ct-legenda div[data-ct='OA'] div[data-interesse='O'] {
  background-color: #a7a7a7;
}
/* F - Falta */
.ct-card.ct-ocorrencias[data-tipo="F"],
#ct-legenda div[data-ct='OA'] div[data-interesse='F'] {
  background-color: #c4778f;
}
/* AE - Atividade Extra */
.ct-card.ct-ocorrencias[data-tipo="AE"],
#ct-legenda div[data-ct='OA'] div[data-interesse='AE'] {
  background-color: #b38356;
}
/* R - Reposições */
.ct-card.ct-ocorrencias[data-tipo="R"],
#ct-legenda div[data-ct='OA'] div[data-interesse='R'] {
  background-color: #79ccdb;
}
/* A - Avaliações */
.ct-card.ct-ocorrencias[data-tipo="A"],
#ct-legenda div[data-ct='OA'] div[data-interesse='A'] {
  background-color: #ec8644;
}
/* C - Cobranças */
.ct-card.ct-ocorrencias[data-tipo="C"],
#ct-legenda div[data-ct='OA'] div[data-interesse='C'] {
  background-color: #ff575f;
}
/* TR - Transferência de Turmas */
.ct-card.ct-ocorrencias[data-tipo="TR"],
#ct-legenda div[data-ct='OA'] div[data-interesse='TR'] {
  background-color: #00D1B2;
}
/* RP - Reagendamento Personal */
.ct-card.ct-ocorrencias[data-tipo="RP"],
#ct-legenda div[data-ct='OA'] div[data-interesse='RP'] {
  background-color: #dddddd;
}
/* AP - Acompanhamento Pedagógico */
.ct-card.ct-ocorrencias[data-tipo="AP"],
#ct-legenda div[data-ct='OA'] div[data-interesse='AP'] {
  background-color: #48da48;
}
/* EA - Entrega de Atividades */
.ct-card.ct-ocorrencias[data-tipo="EA"],
#ct-legenda div[data-ct='OA'] div[data-interesse='EA'] {
  background-color: #9371f3;
}
/* RN - Renovações */
.ct-card.ct-ocorrencias[data-tipo="RN"],
#ct-legenda div[data-ct='OA'] div[data-interesse='RN'] {
  background-color: #FFDD57;
}
/* -------------- */

#ct-legenda > div:not(:nth-last-child(2)) {
  display: flex;
  justify-content: space-between;
}
#ct-legenda > div:nth-last-child(2) {
  display: flex;
  flex-direction: column;
}
#ct-legenda > div:not(:nth-last-child(2)) > div {
  display: flex;
  justify-content: flex-end;
}
#ct-legenda > div:nth-last-child(2) > div {
  display: flex;
  /* justify-content: start; */
  flex-wrap: wrap;
  flex-direction: column;
}
#ct-legenda > div > div div {
  width: 20px;
  height: 20px;
  margin-left: 5px;
  border-radius: 20px;
}
#ct-legenda > div:nth-last-child(2) > div div {
  margin: 3px 0;
  width: 100%;
  text-align: center;
  padding: 0 6px;
}
#ct-legenda div[data-ct='A'] div {
  background-color: #ea7124;
}
#ct-legenda div[data-ct='R'] div {
  background-color: #2d4899;
  color: #fff;
}

.ct-ativo.ct-card[data-interesse='I'],
#ct-legenda div[data-ct='A'] div[data-interesse='I'] {
  background-color: #F6A819;
}
.ct-ativo.ct-card[data-interesse='L'],
#ct-legenda div[data-ct='A'] div[data-interesse='L'] {
  background-color: #FAD70D;
}
.ct-receptivo.ct-card {
  background-color: #2d4899;
  color: #fff;
}
.ct-receptivo.ct-card[data-interesse='I'],
#ct-legenda div[data-ct='R'] div[data-interesse='I'] {
  background-color: #5881ff;
}
.ct-receptivo.ct-card[data-interesse='L'],
#ct-legenda div[data-ct='R'] div[data-interesse='L'] {
  background-color: #b9d9f1;
  color: #151b1e;
}

.badge {
  display: inline-block;
  min-width: 10px;
  padding: 3px 7px;
  font-size: 12px;
  font-weight: bold;
  line-height: 1;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  background-color: #777;
  border-radius: 10px;
}

.table-scroll tbody {
  height: calc(100% - 27.4px);
  height: -webkit-calc(100% - 27.4px);
  height: -moz-calc(100% - 27.4px);
  position: relative;
}

.table-responsive-sm {
  min-height: 300px;
}

.table-schedule tbody td span {
  font-size: x-small;
}

.table-schedule th, .table-schedule td {
  padding: 0;
  /* padding: 1px 1px 0 1px; */
  /* padding-bottom: 1px; */
}

.table-schedule th {
  display: block;
  text-align: center;
}

.table-schedule th:first-child,
.table-schedule td:first-child {
  max-width: 60px;
  font-size: 0.65rem;
  padding: 1px;
}

.table-schedule td:first-child {
  background-color: #dbdeea;
  font-weight: 900;
}
.table-schedule.table-scroll tbody tr,
.table-schedule.table-scroll tbody td {
  min-height: unset;
}

.table-schedule.table-scroll tbody tr:nth-child(2n + 2) {
  background-color: #EBECF0;
}

.table-schedule.table-scroll tbody td:not(:first-child) {
  flex-direction: row;
  align-items: stretch;
}

.table-schedule.table-scroll tbody td {
  flex-direction: column;
  justify-content: center;
}

.table-schedule.table-scroll tbody td > div:not(:last-child) {
  margin-bottom: .25rem;
}

.table-schedule.table-scroll thead tr,
.table-schedule.table-scroll tbody tr {
  flex-grow: 1;
  align-items: stretch;
  justify-items: stretch;
}

.table-schedule.table-scroll.cards-atrasados thead tr,
.table-schedule.table-scroll.cards-atrasados tbody tr {
  flex-grow: unset;

}

.card-wrapper {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  grid-column-gap: 2px;
  width: 100%;
}

.table-schedule.table tbody tr td:not(:first-child):not(:last-child) {
  border-right: 1px dashed #c2cfd6;
}

.table-schedule.table tbody tr:not(:first-child) td.ocorrencias {
  border-color: #ffffff;
  background-color: #fff;
}

.st-card {
  color: #777;
  /* color: #fff; */
  background-color: #ffffff;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  box-shadow: 0 0 0 0.2rem #ffffff;
}

.tel {
  min-width: 26px;
  justify-content: flex-end;
}

.main .container-fluid .animated .table-responsive-sm.followup-em-atraso {
  min-height: 100px;
}
</style>
