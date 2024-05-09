<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div
        class="d-flex justify-content-between filtro-header head-content-sector"
      >
        <div>
          <div
            :class="{ 'filtro-selecionado': filtroSelecionado === 1 }"
            class="btn"
            aria-controls="filtros-rapidos"
            aria-expanded="false"
            @click="
              (filtroRapido = !filtroRapido),
                (filtroAvancado = false),
                (className = filtroRapido ? 'rapido-open' : null),
                (filtroSelecionado = 1),
                limparFiltros()
            "
          >
            Filtro Rápido
          </div>
          <div
            :class="{ 'filtro-selecionado': filtroSelecionado === 2 }"
            class="btn"
            aria-controls="filtros-avancados"
            aria-expanded="true"
            @click="
              (filtroAvancado = !filtroAvancado),
                (filtroRapido = false),
                (className = filtroAvancado ? 'filtro-open' : null),
                (filtroSelecionado = 2)
            "
          >
            Avançado
          </div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="(buscaRapida = true), filtrar()">
          <div class="form-group row mb-0">
            <!-- essa div aqui, se o layout quebrar, remover que não é daqui -->
            <div class="col-md-3">
              <label
                v-help-hint="'filtroRapido-contas-pagar_pessoa'"
                for="favorecido_pessoa_rapido"
                class="col-form-label"
                >Destino</label
              >
              <typeahead
                id="favorecido_pessoa_rapido"
                :value="favorecido_pessoa_rapido_temporario"
                :item-hit="setFavorecidoRapidoTemporario"
                source-path="/api/pessoa/buscar_nome_contato"
                key-name="nome_contato"
              />
            </div>

            <div class="col-md-2">
              <label
                v-help-hint="'filtroRapido-contas-pagar_mes'"
                for="mes_rapido"
                class="col-form-label"
                >Mês</label
              >
              <select
                id="mes_rapido"
                v-model="mes_rapido"
                class="custom-select form-control"
                @change="(buscaRapida = true), filtrar()"
              >
                <option value="0">Janeiro</option>
                <option value="1">Fevereiro</option>
                <option value="2">Março</option>
                <option value="3">Abril</option>
                <option value="4">Maio</option>
                <option value="5">Junho</option>
                <option value="6">Julho</option>
                <option value="7">Agosto</option>
                <option value="8">Setembro</option>
                <option value="9">Outubro</option>
                <option value="10">Novembro</option>
                <option value="11">Dezembro</option>
              </select>
            </div>
            <div class="col-auto">
              <label
                v-help-hint="'filtroRapido-contas-pagar_ano'"
                for="ano_rapido"
                class="col-form-label d-block"
                >Ano</label
              >
              <b-form-radio-group
                id="ano_rapido"
                v-model="ano_rapido"
                :options="anos"
                buttons
                button-variant="cinza"
                name="ano_rapido"
                class="checkbtn-line"
                @change="setAno"
              />
            </div>
            <div class="col-auto">
              <label
                v-help-hint="'filtroRapido-contas-pagar_situacao'"
                for="situacao_rapido"
                class="col-form-label"
                >Situação</label
              >
              <b-form-checkbox-group
                id="situacao_rapido"
                v-model="selectedRapidos"
                :options="situacao"
                buttons
                button-variant="cinza"
                name="situacao_rapido"
                class="checkbtn-line"
                @input="(buscaRapida = true), filtrar()"
              />
            </div>
          </div>
        </form>
      </b-collapse>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="(buscaAvancada = true), filtrar()">
          <div class="form-group row">
            <div class="col-md-3">
              <label
                v-help-hint="'filtroAvancado-contas-pagar_pessoa'"
                for="favorecido_pessoa"
                class="col-form-label"
                >Destino</label
              >
              <typeahead
                id="favorecido_pessoa"
                :value="favorecido_pessoa_temporario"
                :item-hit="setFavorecidoTemporario"
                source-path="/api/pessoa/buscar_nome_contato"
                key-name="nome_contato"
              />
            </div>

            <div class="col-md-3">
              <label
                v-help-hint="'filtroAvancado-contas-pagar_forma_cobranca'"
                for="forma_cobranca_avancada"
                class="col-form-label"
                >Forma de Cobrança</label
              >
              <g-select
                v-model="forma_cobranca_avancada"
                :options="listaFormaPagamento"
                label="descricao"
                track-by="id"
              />
            </div>

            <div class="col-auto">
              <label
                v-help-hint="'filtroAvancado-contas-pagar_forma_situacao'"
                for="situacao_avancado"
                class="col-form-label"
                >Situação</label
              >
              <b-form-checkbox-group
                id="situacao_avancado"
                v-model="selectedAvancadosTemporario"
                :options="situacao"
                buttons
                button-variant="cinza"
                name="situacao_avancado"
                class="checkbtn-line"
              />
            </div>
          </div>

          <div class="form-group row">
            <b-col md="3">
              <label
                v-help-hint="'filtroAvancado-contas-pagar_plano_contas'"
                for="plano_conta[categoria]"
                class="col-form-label"
                >Plano de Contas</label
              >
              <g-treeselect
                id="plano_conta[categoria]"
                :value="plano_conta_temporario"
                :select="setPlanoConta"
                :options="planoConta"
                label="descricao"
              />
            </b-col>

            <div class="col-md-3">
              <label
                v-help-hint="
                  'filtroAvancado-contas-pagar_data_inicial_vencimento'
                "
                for="data_inicial_vencimento"
                class="col-form-label"
                >Vencimento</label
              >
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_inicial_vencimento'"
                      :value="data_inicial_vencimento_temporario"
                      :selected="setDataInicialVencimento"
                    />
                  </div>
                </div>

                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_final_vencimento'"
                      :value="data_final_vencimento_temporario"
                      :selected="setDataFinalVencimento"
                    />
                  </div>
                </div>
              </div>
              <div
                v-if="
                  dateToCompare(data_inicial_vencimento_temporario) >
                    dateToCompare(data_final_vencimento_temporario) &&
                  data_final_vencimento_temporario !== ''
                "
                class="floating-message bg-danger"
              >
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-3">
              <label
                v-help-hint="'filtroAvancado-contas-pagar_pagamento'"
                class="col-form-label"
                >Pagamento</label
              >
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_inicial_pagamento'"
                      :value="data_inicial_pagamento_temporario"
                      :selected="setDataInicialPagamento"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_final_pagamento'"
                      :value="data_final_pagamento_temporario"
                      :selected="setDataFinalPagamento"
                    />
                  </div>
                </div>
              </div>
              <div
                v-if="
                  dateToCompare(data_inicial_pagamento_temporario) >
                    dateToCompare(data_final_pagamento_temporario) &&
                  data_final_pagamento_temporario !== ''
                "
                class="floating-message bg-danger"
              >
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-3">
              <label
                v-help-hint="'filtroAvancado-contas-pagar_valor'"
                class="col-form-label"
                >Valor</label
              >
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Min</div>
                    </div>
                    <input
                      v-money="moeda"
                      id="valor_inicial"
                      v-model="valor_inicial_temporario"
                      type="text"
                      class="form-control"
                      maxlength="17"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Max</div>
                    </div>
                    <input
                      v-money="moeda"
                      id="valor_final"
                      v-model="valor_final_temporario"
                      type="text"
                      class="form-control"
                      maxlength="17"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button
            type="submit"
            class="btn btn-cinza btn-block text-uppercase"
            @click="(filtroAvancado = false), (className = null)"
          >
            Buscar
          </button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable" class="selectAll">
        <thead class="text-dark">
          <tr>
            <th data-label="Selecionar todos" class="coluna-checkbox">
              <b-form-checkbox
                :disabled="!lista.length"
                v-model="checkAll"
                :indeterminate="indeterminate"
                aria-describedby="selected"
                aria-controls="selected"
                class="p-0"
                @change="toggleAll"
              />
            </th>
            <th class="coluna-numero-parcela d-block">#</th>
            <th data-column="favorecido.nome_contato">Destino</th>
            <th v-b-tooltip.viewport.down data-column="tituloPagar.data_prorrogacao" title="Vencimento 1" class="d-block text-truncate coluna-data">Vencimento 1</th>
            <th class="coluna-data">Pago em</th>
            <th
              v-b-tooltip.viewport.down
              data-column="planoConta.descricao"
              title="Categorias"
              class="size-150"
            >
              Categorias
            </th>
            <th
              v-b-tooltip.viewport.down
              data-column="formaCobranca.descricao"
              title="Forma de Cobrança"
              class="d-block text-truncate size-115"
            >
              F. Cobrança
            </th>
            <th
              v-b-tooltip.viewport.down
              data-column="formaPagamento.descricao"
              title="Forma de Pagamento"
              class="d-block text-truncate size-115"
            >
              F. Pagamento
            </th>
            <th data-column="" class="coluna-valor">Valor</th>
            <th data-column="" class="coluna-valor">Valor Pago</th>
            <th data-column="" class="coluna-situacao-icone">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <!--
            <tr><td data-label="Selecionar Todos"><b-form-checkbox :disabled="!lista.length" v-model="checkAll" :indeterminate="indeterminate" class="m-0 p-0" aria-describedby="selected" aria-controls="selected" @change="toggleAll"/></td></tr>
          -->
          <perfect-scrollbar>
            <div v-if="estaCarregando" class="form-loading">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div
              v-else-if="!lista.length && !estaCarregando"
              class="busca-vazia"
            >
              <p>Nenhum resultado encontrado.</p>
            </div>
            <div v-else>
              <tr
              v-b-tooltip.bottom
              v-for="(item, i) in lista"
                :key="i"
                :lista="(list = lista)"
                :title="tooltipText(item.observacao)"
                @click="checkRow($event, item)"
                @dblclick="abrirEdicao(item.id)"
              >
                <td data-label="Selecionar" class="coluna-checkbox">
                  <b-form-checkbox
                    v-if="item.titulo_pagar.podeSerPago"
                    :id="`titulo-pagar-${item.titulo_pagar.id}`"
                    v-model="selected"
                    :value="item"
                    class="m-0"
                  />
                </td>
  
                <td data-label="#" class="d-block coluna-numero-parcela">
                  {{ item.titulo_pagar.numero_parcela_documento }}
                </td>
  
                <td data-label="Destino">
                  <span
                    v-b-tooltip.viewport.down
                    v-if="item.titulo_pagar.favorecido_pessoa"
                    :title="`${
                      item.titulo_pagar.favorecido_pessoa.razao_social ||
                      item.titulo_pagar.favorecido_pessoa.nome_contato
                    } (${item.titulo_pagar.favorecido_pessoa.cnpj_cpf})`"
                    class="d-block text-truncate"
                    >{{
                      item.titulo_pagar.favorecido_pessoa.razao_social ||
                      item.titulo_pagar.favorecido_pessoa.nome_contato
                    }}</span
                  >
                </td>
  
                <td data-label="Vencimento" class="coluna-data">
                  <template
                    v-if="
                      hoje < getDateFromISO(item.titulo_pagar.data_prorrogacao)
                    "
                  >
                    <span class="badge date-success align-middle rounded">{{
                      item.titulo_pagar.data_prorrogacao | formatarData
                    }}</span>
                  </template>
                  <template
                    v-else-if="
                      hoje === getDateFromISO(item.titulo_pagar.data_prorrogacao)
                    "
                  >
                    <span class="badge date-warning align-middle rounded">{{
                      item.titulo_pagar.data_prorrogacao | formatarData
                    }}</span>
                  </template>
                  <template
                    v-else-if="
                      hoje > getDateFromISO(item.titulo_pagar.data_prorrogacao)
                    "
                  >
                    <span class="badge date-danger align-middle rounded">{{
                      item.titulo_pagar.data_prorrogacao | formatarData
                    }}</span>
                  </template>
                </td>
  
                <td data-label="Pago em" class="coluna-data">
                  <span class="badge date-payment align-middle rounded">{{
                    item.titulo_pagar.movimento_conta.length > 0
                      ? item.titulo_pagar.movimento_conta[
                          item.titulo_pagar.movimento_conta.length - 1
                        ].data_contabil
                      : "" | formatarDataString
                  }}</span>
                </td>
  
                <td
                  :class="
                    item.plano_contas_conta_pagar ? null : 'invisible-options'
                  "
                  data-label="Categorias"
                  class="d-block text-truncate size-150"
                >
                  <template v-if="item.plano_contas_conta_pagar">
                    <template v-for="planoConta in item.plano_contas_conta_pagar">
                      {{ planoConta.plano_conta.descricao }}
                    </template>
                  </template>
                </td>
  
                <td
                  v-b-tooltip.viewport.down
                  :title="
                    item.titulo_pagar.forma_cobranca
                      ? item.titulo_pagar.forma_cobranca.descricao
                      : ''
                  "
                  data-label="Forma de Cobrança"
                  class="d-block text-truncate size-115"
                >
                  {{
                    item.titulo_pagar.forma_cobranca
                      ? item.titulo_pagar.forma_cobranca.descricao
                      : ""
                  }}
                </td>
  
                <td
                  v-b-tooltip.viewport.down
                  :title="
                    item.titulo_pagar.movimento_conta.length > 0 &&
                    item.titulo_pagar.movimento_conta[
                      item.titulo_pagar.movimento_conta.length - 1
                    ].forma_pagamento
                      ? item.titulo_pagar.movimento_conta[
                          item.titulo_pagar.movimento_conta.length - 1
                        ].forma_pagamento.descricao
                      : ''
                  "
                  data-label="Forma de Pagamento"
                  class="d-block text-truncate size-115"
                >
                  {{
                    item.titulo_pagar.movimento_conta.length > 0 &&
                    item.titulo_pagar.movimento_conta[
                      item.titulo_pagar.movimento_conta.length - 1
                    ].forma_pagamento
                      ? item.titulo_pagar.movimento_conta[
                          item.titulo_pagar.movimento_conta.length - 1
                        ].forma_pagamento.descricao
                      : ""
                  }}
                </td>
  
                <td data-label="Valor" class="coluna-valor">
                  {{ item.titulo_pagar.valor_documento | formatarMoeda }}
                </td>
  
                <td data-label="Valor Pago" class="coluna-valor">
                  {{
                    item.titulo_pagar.movimento_conta.length > 0
                      ? item.titulo_pagar.movimento_conta[
                          item.titulo_pagar.movimento_conta.length - 1
                        ].valor_lancamento
                      : 0 | formatarMoeda
                  }}
                </td>
  
                <td data-label="Situação" class="coluna-situacao-icone">
                  <PillSituation 
                    :situation="situacoes[item.titulo_pagar.situacao]" 
                    :situationClass="item.titulo_pagar.situacao.toLowerCase()" 
                    :textTooltip="situacoes[item.titulo_pagar.situacao]"
                  >
                  </PillSituation>
                </td>
  
                <td class="d-flex coluna-icones">
                  <a
                    href="javascript:void(0)"
                    title="Atualizar"
                    class="icone-link"
                    @click.prevent="
                      (itemSelecionado = item.id),
                        (tituloHighlight = item.titulo_pagar.id),
                        $refs.lancarConta.show()
                    "
                  >
                    <font-awesome-icon icon="pen" />
                  </a>
  
                  <a
                    :class="
                      item.titulo_pagar.podeSerPago ||
                      item.titulo_pagar.valor_saldo > 0
                        ? null
                        : 'disable-icon'
                    "
                    :disabled="!item.titulo_pagar.podeSerPago"
                    href="javascript:void(0)"
                    title="Pagar"
                    class="icone-link"
                    @click.prevent="
                      item.titulo_pagar.podeSerPago && selecionarApenasUm(item)
                    "
                  >
                    <font-awesome-icon icon="dollar-sign" />
                  </a>
  
                  <!--
                    <a :class="item.titulo_pagar.movimento_conta.length > 0 ? null : 'disable-icon'" href="javascript:void(0)" title="Recibo" class="icone-link" @click.prevent="emitirRecibo(item)">
                    <font-awesome-icon icon="receipt" />
                    </a>
                  -->
  
                  <a
                    :class="{
                      'disable-icon': !item.titulo_pagar.podeSerExcluido,
                    }"
                    href="javascript:void(0)"
                    title="Remover"
                    class="icone-link"
                    @click.prevent="
                      item.titulo_pagar.podeSerExcluido && removerItem(item)
                    "
                  >
                    <font-awesome-icon icon="trash-alt" />
                  </a>
                </td>
              </tr>
            </div>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div
      id="total-container"
      class="d-flex justify-content-between align-items-center"
    >
      <div class="d-flex">
        <div class="info-btn">
          <button v-b-modal.lancarConta type="button" class="btn btn-roxo">
            Lançar Conta
          </button>
          <button
            v-b-modal.pagar-selecionados
            :disabled="!selected.length"
            type="button"
            class="btn btn-azul"
            @click="createSelected()"
          >
            Pagar Selecionados
          </button>
        </div>

        <div class="info-btn">
          <button
            v-b-modal.pagamentoInstrutor
            type="button"
            class="btn btn-primary"
          >
            Pagamento de Instrutor
          </button>
        </div>
      </div>

      <div class="info-label">
        <div>
          <div>Pago:</div>
          <div>A Pagar:</div>
          <div>Total selecionado:</div>
        </div>

        <div class="text-right">
          <div>{{ valoresContas.pagas | formatarMoeda }}</div>
          <div>{{ valoresContas.pagar | formatarMoeda }}</div>
          <div>{{ totalSelecionado | formatarMoeda }}</div>
        </div>
      </div>
    </div>

    <b-modal
      id="verConta"
      ref="verConta"
      size="lg"
      centered
      no-close-on-backdrop
      hide-header
      hide-footer
    >
      <info-conta-pagar
        :id="itemSelecionado"
        :is-modal="true"
        @resolve="editarConta"
        @cancel="cancelarModalVer()"
      />
    </b-modal>

    <b-modal
      id="lancarConta"
      ref="lancarConta"
      size="lg"
      centered
      no-close-on-backdrop
      hide-header
      hide-footer
    >
      <formulario-conta-pagar
        v-show="modalFormularioAtivo === 'conta-pagar'"
        ref="formContaPagar"
        :id="itemSelecionado"
        :highlight-id="tituloHighlight"
        :is-modal="true"
        @resolve="contaSalva()"
        @cancel="cancelarModal()"
        @mostrarFormularioPessoa="mostrarFormularioPessoa"
      />
      <formulario-pessoa
        v-show="modalFormularioAtivo === 'pessoa'"
        ref="modalFormularioPessoa"
        :load-categories="false"
        :is-modal="true"
        @resolve="pessoaSalva"
        @reject="cancelarPessoa()"
      />
    </b-modal>

    <!-- PAGAMENTO DE INSTRUTOR -->
    <b-modal
      id="pagamentoInstrutor"
      ref="pagamentoInstrutor"
      v-model="pagamentoInstrutor"
      size="xl"
      centered
      no-close-on-backdrop
      hide-header
      hide-footer
    >
      <h3>Pagamento de Instrutor</h3>

      <form
        :class="{ 'was-validated': !isValid }"
        class="needs-validation"
        novalidate
        @submit.prevent="buscarPagamentosInstrutor()"
      >
        <div class="form-group row">
          <div class="col-md-6">
            <label for="instrutor" class="form-label">Instrutor</label>
            <sync-select
              v-model="instrutor"
              :was-validated="isValid"
              :field="{
                targetEntity: 'App\\Entity\\Principal\\Funcionario',
                name: 'instrutor_pagamento',
                descriptionColumn: 'apelido',
                valueColumn: 'id',
                where: [{ field: 'instrutor', criteria: '=', value: 1 }],
              }"
              :required="!instrutor || instrutor.id === null"
            />
          </div>

          <div class="col-md-6">
            <label class="col-form-label">Período de trabalho</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">De</div>
                    <div class="datepicker-input">
                      <v-date-picker v-model="dataInicio">
                        <template v-slot="{ inputValue, inputEvents }">
                          <input
                            class="form-control"
                            :input-props="{
                              id: 'dataInicio',
                              class: 'form-control',
                              placeholder: 'Data',
                              autocomplete: 'off',
                            }"
                         
                            :attributes="[
                              {
                                highlight: { class: 'today-mark' },
                                dates: new Date(),
                              },
                            ]"
                            :value="inputValue"
                            v-on="inputEvents"
                          />
                        </template>
                      </v-date-picker>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">Até</div>
                    <div class="datepicker-input">
                      <v-date-picker v-model="dataFim">
                        <template v-slot="{ inputValue, inputEvents }">
                          <input
                            class="form-control"
                            :input-props="{
                              id: 'dataFim',
                              class: 'form-control',
                           
                              autocomplete: 'off',
                            }"
                        
                            :attributes="[
                              {
                                highlight: { class: 'today-mark' },
                                dates: new Date(),
                              },
                            ]"
                            :value="inputValue"
                            v-on="inputEvents"
                          />
                        </template>
                      </v-date-picker>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              v-if="compararData(dataInicio, dataFim)"
              class="floating-message bg-danger"
            >
              Data inicial deve ser menor que a data final!
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col">
            <label class="col-form-label">Modalidade</label>
            <div>
              <b-form-checkbox-group
                id="modalidade"
                v-model="modalidade"
                :options="listaModalidade"
                :checked="modalidade"
                name="modalidade"
              />
            </div>
          </div>
        </div>

        <button
          :disabled="!instrutor || instrutor.id === null"
          type="submit"
          class="btn btn-cinza btn-block text-uppercase"
        >
          Buscar
        </button>
      </form>

      <div class="table-responsive-sm mt-3">
        <div v-if="carregandoPagamentoInstrutor" class="d-flex h-100">
          <load-placeholder :loading="carregandoPagamentoInstrutor" />
        </div>

        <g-table
          v-if="
            instrutor &&
            listaPagamentoInstrutor.length &&
            !carregandoPagamentoInstrutor
          "
        >
          <thead>
            <tr>
              <th data-column="">Turma</th>
              <th data-column="" class="size-150">Aulas/Atividades</th>
              <th data-column="" class="size-150">Valor hora</th>
              <th data-column="" class="size-150">Extra</th>
              <th data-column="" class="size-150">Total</th>
            </tr>
          </thead>
          <tbody>
            <perfect-scrollbar>
              <div
                v-if="
                  !listaPagamentoInstrutor.length &&
                  !carregandoPagamentoInstrutor
                "
                class="busca-vazia"
              >
                <p>Nenhum resultado encontrado.</p>
              </div>

              <tr v-for="(item, index) in listaPagamentoInstrutor" :key="index">
                <td data-label="Turma">
                  <span>{{ item.descricao }}</span>
                </td>
                <td data-label="Aulas/Atividades" class="size-150">
                  <span>{{ item.quantidade_registros }}</span>
                </td>
                <td data-label="Valor hora" class="size-150">
                  <span>{{ item.valor_hora | formatarMoeda }}</span>
                </td>
                <td data-label="Extra" class="size-150">
                  <span>{{ item.valor_extra | formatarMoeda }}</span>
                </td>
                <td data-label="Total" class="size-150">
                  <span>{{ item.valor_total | formatarMoeda }}</span>
                </td>
              </tr>
            </perfect-scrollbar>
          </tbody>
        </g-table>

        <!--  -->
        <div v-else class="form-group list-group-accent">
          <div
            class="
              list-group-item list-group-item-accent-info list-group-item-info
              border-0
            "
          >
            <font-awesome-icon icon="info-circle" /> Busque pelo instrutor e os
            filtros desejados para iniciar.
          </div>
        </div>
      </div>

      <div
        v-if="
          instrutor &&
          listaPagamentoInstrutor.length &&
          !carregandoPagamentoInstrutor
        "
        class="cfooter"
      >
        <div><span>Total a pagar</span></div>
        <div></div>
        <div><span>R$</span></div>
        <div>
          {{ total_pagar | formatarMoeda(true) }}
        </div>
      </div>
      <b-btn
        :disabled="!listaPagamentoInstrutor.length || gerandoPagamentoInstrutor"
        type="button"
        class="btn btn-roxo"
        variant="verde"
        @click="gerarPagamentoInstrutor()"
      >
        {{
          gerandoPagamentoInstrutor
            ? "Gerando conta a pagar..."
            : "Gerar conta a pagar"
        }}
      </b-btn>

      <b-btn variant="link" @click="cancelarPagamentoInstrutor()"
        >Cancelar</b-btn
      >
    </b-modal>
    <!--  -->

    <b-modal
      id="pagar-selecionados"
      ref="pagarSelecionados"
      v-model="visible"
      size="lg"
      centered
      no-close-on-backdrop
      hide-header
      hide-footer
      modal-class="pagar-selecionados"
      @show="montaLista()"
    >
      <form>
        <div class="table-card">
          <div class="cheader"></div>

          <div class="cbody">
            <ul v-for="item in createdSelected" :key="item.titulo_pagar.id">
              <li
                v-if="hoje < getDateFromISO(item.titulo_pagar.data_prorrogacao)"
                class="date-success"
              >
                <div>
                  {{
                    item.fornecedor_pessoa.nome_fantasia ||
                    item.fornecedor_pessoa.razao_social ||
                    item.fornecedor_pessoa.nome_contato
                  }}
                </div>
                <div>{{ item.titulo_pagar.valor_saldo | formatarMoeda }}</div>
                <div>
                  {{ item.titulo_pagar.data_prorrogacao | formatarData }}
                </div>
              </li>

              <li
                v-else-if="
                  hoje === getDateFromISO(item.titulo_pagar.data_prorrogacao)
                "
                class="date-warning"
              >
                <div>
                  {{
                    item.fornecedor_pessoa.nome_fantasia ||
                    item.fornecedor_pessoa.razao_social ||
                    item.fornecedor_pessoa.nome_contato
                  }}
                </div>
                <div>{{ item.titulo_pagar.valor_saldo | formatarMoeda }}</div>
                <div>
                  {{ item.titulo_pagar.data_prorrogacao | formatarData }}
                </div>
              </li>

              <li
                v-else-if="
                  hoje > getDateFromISO(item.titulo_pagar.data_prorrogacao)
                "
                class="date-danger"
              >
                <div>
                  {{
                    item.fornecedor_pessoa.nome_fantasia ||
                    item.fornecedor_pessoa.razao_social ||
                    item.fornecedor_pessoa.nome_contato
                  }}
                </div>
                <div>{{ item.titulo_pagar.valor_saldo | formatarMoeda }}</div>
                <div>
                  {{ item.titulo_pagar.data_prorrogacao | formatarData }}
                </div>
              </li>

              <li>
                <div>
                  <label
                    :for="'valor_montante_' + item.titulo_pagar.id"
                    class="col-form-label"
                    >Valor à pagar</label
                  >
                  <vue-numeric
                    v-model="item.valor_montante"
                    :precision="2"
                    :id="'valor_montante_' + item.titulo_pagar.id"
                    separator="."
                    autocomplete="off"
                    class="form-control"
                    @input="somarTotalItem(item)"
                  />
                </div>

                <div>
                  <label
                    :for="'valor_desconto_' + item.titulo_pagar.id"
                    class="col-form-label"
                    >Desconto</label
                  >
                  <vue-numeric
                    v-model="item.valor_desconto"
                    :precision="2"
                    :id="'valor_desconto_' + item.titulo_pagar.id"
                    separator="."
                    autocomplete="off"
                    class="form-control"
                    @input="somarTotalItem(item)"
                  />
                </div>
              </li>

              <li>
                <div>
                  <label
                    :for="'valor_baixado_' + item.titulo_pagar.id"
                    class="col-form-label"
                    >Diferença/Baixa</label
                  >
                  <vue-numeric
                    v-model="item.valor_diferenca_baixa"
                    :disabled="true"
                    :precision="2"
                    :id="'valor_baixado_' + item.titulo_pagar.id"
                    separator="."
                    class="form-control"
                  />
                </div>

                <div>
                  <label
                    :for="'valor_juros_' + item.titulo_pagar.id"
                    class="col-form-label"
                    >Juros/Multa</label
                  >
                  <vue-numeric
                    v-model="item.valor_juros"
                    :precision="2"
                    :id="'valor_juros_' + item.titulo_pagar.id"
                    separator="."
                    autocomplete="off"
                    class="form-control"
                    @input="somarTotalItem(item)"
                  />
                </div>
              </li>

              <li>
                <span>
                  <small
                    v-if="item.valor_total < 0"
                    class="d-block alert alert-danger"
                    style="font-size: 11px"
                    >O valor a pagar não pode ser negativo.</small
                  >
                </span>
              </li>
              <li>{{ item.valor_total | formatarMoeda(true) }}</li>
            </ul>
          </div>
        </div>

        <div class="cfooter">
          <div><span>Total a pagar</span></div>
          <div></div>
          <div><span>R$</span></div>
          <div>
            {{ totalPagar | formatarMoeda(true) }}
          </div>
        </div>

        <div class="row form-group font-large">
          <div class="col-md-3 text-left">Data de pagamento</div>
          <div class="col-md-2">
            <g-datepicker
              :element-id="'data_pagamento_movimento_conta_temporario'"
              :value="data_pagamento_movimento_conta_temporario"
              :selected="setDataPagamentoMovimentoConta"
            />
            <div
              v-if="
                dateToCompare(data_pagamento_movimento_conta_temporario) >
                  dateToCompare(hoje) &&
                data_pagamento_movimento_conta_temporario !== ''
              "
              class="floating-message bg-danger"
            >
              Data de pagamento deve ser menor que a data de hoje!
            </div>
          </div>
          <div class="col-md-3 text-left"><span>Forma de pagamento</span></div>
          <div class="col-md-4">
            <g-select
              id="forma_pagamento"
              :value="forma_pagamento"
              :select="setFormaPagamento"
              :options="listaFormaPagamentoSemCheque"
              class="multiselect-truncate"
              label="descricao"
              track-by="id"
            />
          </div>
        </div>

        <div class="row form-group font-large">
          <div class="col-md-3 text-left">Conta</div>
          <div class="col-md-2">
            <g-select
              id="conta"
              :value="conta"
              :select="setConta"
              :options="listaContas"
              class="multiselect-truncate full-width"
              label="descricao"
              track-by="id"
            />
          </div>
          <div class="col-md-3 text-left"><span>Saldo R$</span></div>
          <div class="col-md-4 text-left">
            <div v-if="conta">
              {{ conta.valor_saldo | formatarMoeda(true) }}
            </div>
          </div>
        </div>

        <button
          :disabled="pagando || podeSalvar || totalPagar < 0"
          type="button"
          class="btn btn-verde"
          @click="pagarTitulo(createdSelected)"
        >
          {{ pagando ? "Pagando..." : "Pagar" }}
        </button>
        <button
          type="button"
          class="btn btn-link"
          @click="
            (visible = false),
              (conta = createdSelected[0].titulo_pagar.conta.id),
              cancelar()
          "
        >
          Cancelar
        </button>
      </form>
    </b-modal>
  </div>
</template>

<script>
import EventBus from "../../utils/event-bus";
import PillSituation from '../../components/PillSituation.vue';
import { mapState, mapActions, mapMutations } from "vuex";
import {
  beginOfDay,
  endOfDay,
  getDateFromISO,
  dateToCompare,
  dateToString,
  stringToISODate,
} from "../../utils/date";
import { currencyToNumber, numberToCurrency } from "../../utils/number";
import FormularioContaPagar from "./Formulario.vue";
import FormularioPessoa from "../pessoas/Formulario.vue";
import Info from "./Info.vue";
import { required } from "vuelidate/lib/validators";
import SyncSelect from "../../components/fields/SyncSelect";
import moment from "moment";

export default {
  components: {
    "formulario-conta-pagar": FormularioContaPagar,
    "formulario-pessoa": FormularioPessoa,
    "info-conta-pagar": Info,
    SyncSelect,
    PillSituation
  },

  data() {
    return {
      checkAll: false,
      indeterminate: false,
      selected: [],
      createdSelected: [],
      list: [],
      valoresContas: {
        pagas: 0,
        pagar: 0,
      },
      totalSelecionado: 0,
      className: "rapido-open",
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      mes_rapido: "",
      ano_rapido: "",
      anos: [],
      buscaAvancada: false,
      buscaRapida: false,
      fornecedorPessoaTemporariaContaPagar: null,
      fornecedor_pessoa: null,
      favorecido_pessoa: null,
      forma_pagamento_temporario: null,
      data_inicial_vencimento: "",
      data_final_vencimento: "",
      data_inicial_pagamento: "",
      data_final_pagamento: "",
      valor_inicial: null,
      valor_final: null,
      forma_cobranca_avancada: "",
      plano_conta: null,
      favorecido_pessoa_rapido_temporario: null,

      favorecido_pessoa_temporario: null,
      data_inicial_vencimento_temporario: "",
      data_final_vencimento_temporario: "",
      data_inicial_pagamento_temporario: "",
      data_final_pagamento_temporario: "",
      data_pagamento_movimento_conta_temporario: "",
      valor_inicial_temporario: null,
      valor_final_temporario: null,
      selectedAvancadosTemporario: [],
      plano_conta_temporario: null,

      selectedRapidos: [],
      selectedAvancados: [],
      situacao: [
        { text: "Pendente", value: "PEN" },
        { text: "Liquidado", value: "LIQ" },
        { text: "Vencido", value: "VEN" },
      ],
      moeda: {
        decimal: ",",
        thousands: ".",
        precision: 2,
        masked: true,
      },
      hoje: new Date().toISOString().split("T")[0],
      conta: "",
      forma_pagamento: "",
      visible: false,
      pagando: false,
      valorVerificado: true,
      podeSalvar: false,
      totalPagar: 0,
      totalTitulo: 0,
      contadorTeste: 0,
      itemSelecionado: null,
      situacoes: {
        PEN: "Pendente",
        LIQ: "Liquidado",
        "LIQ-PEN": "Liquidação Pendente",
        CAN: "Cancelado",
        BAI: "Baixado",
        SUB: "Substituído",
        VEN: "Vencido",
      },
      modalFormularioAtivo: "conta-pagar",
      tituloHighlight: null,
      listaFormaPagamentoSemCheque: [],

      isValid: true,
      pagamentoInstrutor: false,
      dataInicio: null,
      dataFim: null,
      instrutor: "",

      listaPagamentoInstrutor: [],
      listaModalidade: [
        { value: "TUR", text: "Turmas" },
        { value: "PER", text: "Aulas personal" },
        { value: "ATI", text: "Atividades Extras" },
        { value: "REP", text: "Reposição de aula" },
        { value: "BON", text: "Bonus class" },
      ],

      modalidade: ["TUR", "PER", "ATI", "REP", "BON"],

      carregandoPagamentoInstrutor: false,
      total_pagar: 0,
      registros_considerados: [],

      gerandoPagamentoInstrutor: false,
    };
  },

  validations: {
    instrutor: { required },
  },

  computed: {
    ...mapState("conta", { listaContas: "lista" }),
    ...mapState("contaPagar", [
      "lista",
      "estaCarregando",
      "todosItensCarregados",
    ]),
    ...mapState("root", ["franqueadaSelecionada"]),
    ...mapState("planoConta", { planoConta: "selectDespesas" }),

    listaFormaPagamento: {
      get() {
        return [{ descricao: "Selecione", id: null }].concat(
          this.$store.state.formaPagamento.lista
        );
      },
    },
  },

  watch: {
    selected(value, oldVal) {
      // Handle changes in individual checkboxes
      if (value.length === 0) {
        this.indeterminate = false;
        this.checkAll = false;
      } else if (value.length === this.list.length) {
        this.indeterminate = false;
        this.checkAll = true;
      } else {
        this.indeterminate = true;
        this.checkAll = false;
      }
      
      if (value.length > 0 || oldVal.length > 0) {
        let total = 0;
        for (let i = value.length; i--; ) {
          total += parseFloat(value[i].titulo_pagar.valor_saldo);
        }
        this.totalSelecionado = total;

        this.conta = total > 0 || this.conta === "" ? value[0].titulo_pagar.conta : "";
      }
    },

    filtroSelecionado(value) {
      this.filtrar();
    },

    list(lista) {
      this.valoresContas = {
        pagas: 0,
        pagar: 0,
      };
      for (let i = lista.length; i--; ) {
        const pagar = Number(lista[i].titulo_pagar.valor_saldo);

        this.valoresContas.pagas +=
          Number(lista[i].titulo_pagar.valor_documento) - pagar;
        this.valoresContas.pagar += pagar;
      }
      this.$store.commit("contaPagar/SET_ESTA_CARREGANDO", false);
    },

    createdSelected(value) {
      value.map((item) => {
        item.valor_montante = currencyToNumber(item.valor_montante);
        item.valor_desconto = currencyToNumber(item.valor_desconto);
        item.valor_juros = currencyToNumber(item.valor_juros);
        item.valor_diferenca_baixa = currencyToNumber(
          item.valor_diferenca_baixa
        );
        item.total_titulo =
          item.valor_montante - item.valor_desconto + item.valor_juros;

        this.somarTotal(item);
      });
    },
  },

  mounted() {
    this.$store.commit("conta/SET_PAGINA_ATUAL", 1);
    this.$store.commit("formaPagamento/SET_PAGINA_ATUAL", 1);

    this.getListaConta();
    this.filtrar();
    this.getListaFormaPagamento();

    this.data_pagamento_movimento_conta_temporario = dateToString(new Date());
    const thisYear = new Date().getFullYear();
    for (
      let year = thisYear - 2, endYear = thisYear + 2;
      year <= endYear;
      year++
    ) {
      this.anos.push(year);
    }
  },

  methods: {
    ...mapActions("contaPagar", [
      "listar",
      "atualizar",
      "remover",
      "listarPagamentoInstrutor",
      "criarPagamentoInstrutor",
    ]),
    ...mapActions("pessoas", ["getPessoa"]),
    ...mapActions("conta", { getListaConta: "getLista" }),
    ...mapActions("tituloPagar", ["pagar"]),
    ...mapActions("formaPagamento", { getListaFormaPagamento: "getLista" }),

    ...mapMutations("contaPagar", [
      "SET_PAGINA_ATUAL",
      "SET_ESTA_CARREGANDO",
      "SET_ITEM_SELECIONADO",
      "SET_ITEM_SELECIONADO_ID",
      "SET_ORDER_BY",
    ]),

    getDateFromISO: getDateFromISO,

    dateToCompare: dateToCompare,

    stringToISODate: stringToISODate,

    dateToString: dateToString,

    compararData(dataInicio, dataFim) {
      return dataFim && dataInicio > dataFim;
    },

    test(vm) {
      if (vm) {
        if (vm.id !== null) {
          this.instrutor = vm;
        } else {
          this.instrutor = null;
        }
      }
    },

    montaLista() {
      let temCheques = true;
      for (let i = 0, l = this.selected.length; i < l; i++) {
        if (
          this.selected[i].titulo_pagar.forma_cobranca.forma_cheque === false
        ) {
          temCheques = false;
          break;
        }
      }

      this.listaFormaPagamentoSemCheque =
        this.$store.state.formaPagamento.lista.filter(
          (item) => temCheques === true || item.forma_cheque === false
        );
    },

    checkRow(event, item) {
      const tag = event.target.tagName.toLocaleLowerCase();
      const tags = ["label", "input", "path", "svg", "a"];
      if (tags.includes(tag)) {
        return;
      }
      const index = this.selected.indexOf(item);
      if (index !== -1) {
        this.selected.splice(index, 1);
        return;
      }
      this.selected.push(item);
    },

    setDataPagamentoMovimentoConta(value) {
      this.data_pagamento_movimento_conta_temporario = value;
    },

    sortTable(response) {
      this.SET_ORDER_BY(response.detail);
      this.SET_PAGINA_ATUAL(1);
      this.listar();
    },

    setDataInicialVencimento(value) {
      this.data_inicial_vencimento_temporario = value;
    },

    abrirEdicao(id) {
      this.itemSelecionado = id;
      this.$refs.lancarConta.show();
    },

    setDataFinalVencimento(value) {
      this.data_final_vencimento_temporario = value;
    },

    setDataInicialPagamento(value) {
      this.data_inicial_pagamento_temporario = value;
    },

    setDataFinalPagamento(value) {
      this.data_final_pagamento_temporario = value;
    },

    setFormaPagamento(value) {
      this.forma_pagamento = value;
    },

    setConta(value) {
      this.conta = value;
    },

    limparFiltros() {
      this.favorecido_pessoa_temporario = this.favorecido_pessoa;
      this.data_inicial_vencimento_temporario = this.data_inicial_vencimento;
      this.data_final_vencimento_temporario = this.data_final_vencimento;
      this.data_inicial_pagamento_temporario = this.data_inicial_pagamento;
      this.data_final_pagamento_temporario = this.data_final_pagamento;
      this.valor_inicial_temporario = this.valor_inicial;
      this.valor_final_temporario = this.valor_final;
      this.selectedAvancadosTemporario = this.selectedAvancados;
      this.plano_conta_temporario = this.plano_conta;
    },

    toggleAll(checked) {
      if (checked) {
        const list = this.list
          .slice()
          .filter((item) => item.titulo_pagar.podeSerPago);
        this.selected = list;
        return;
      }

      this.selected = [];
    },

    setAno(value) {
      this.ano_rapido = value;
      this.filtrar();
    },

    selecionarApenasUm(item) {
      this.selected = [item];
      this.createSelected();
      this.$refs.pagarSelecionados.show();
    },

    filtrar() {
      this.toggleAll(false);

      if (this.filtroSelecionado === 1) {
        this.$store.commit("contaPagar/SET_FILTROS_FAVORECIDO", null);
        this.$store.commit("contaPagar/SET_FILTROS_FORMA_COBRANCA", null);
        this.$store.commit("contaPagar/SET_FILTROS_PLANO_CONTA", null);
        this.$store.commit(
          "contaPagar/SET_FILTROS_DATA_INICIAL_VENCIMENTO",
          ""
        );
        this.$store.commit("contaPagar/SET_FILTROS_DATA_FINAL_VENCIMENTO", "");
        this.$store.commit("contaPagar/SET_FILTROS_DATA_INICIAL_PAGAMENTO", "");
        this.$store.commit("contaPagar/SET_FILTROS_DATA_FINAL_PAGAMENTO", "");
        this.$store.commit("contaPagar/SET_FILTROS_VALOR_INICIAL", "");
        this.$store.commit("contaPagar/SET_FILTROS_VALOR_FINAL", "");

        if (!this.buscaRapida) {
          this.favorecido_pessoa_rapido_temporario = this.favorecido_pessoa;
          this.mes_rapido = new Date().getMonth();
          this.ano_rapido = new Date().getFullYear();
          this.selectedRapidos = ["PEN"];
        }

        this.favorecido_pessoa = this.favorecido_pessoa_rapido_temporario;

        this.$store.commit(
          "contaPagar/SET_FILTROS_FAVORECIDO",
          this.favorecido_pessoa
        );
        this.$store.commit("contaPagar/SET_FILTROS_MES", this.mes_rapido);
        this.$store.commit("contaPagar/SET_FILTROS_ANO", this.ano_rapido);
        this.$store.commit(
          "contaPagar/SET_FILTROS_SITUACAO",
          this.selectedRapidos
        );
      } else {
        this.$store.commit("contaPagar/SET_FILTROS_MES", null);
        this.$store.commit("contaPagar/SET_FILTROS_ANO", null);
        this.$store.commit("contaPagar/SET_FILTROS_SITUACAO", []);

        if (!this.buscaAvancada) {
          this.limparFiltros();
          return;
        }

        this.forma_pagamento_temporario = this.forma_cobranca_avancada.id;
        this.plano_conta = this.plano_conta_temporario;
        this.favorecido_pessoa = this.favorecido_pessoa_temporario;
        this.data_inicial_vencimento = this.data_inicial_vencimento_temporario;
        this.data_final_vencimento = this.data_final_vencimento_temporario;
        this.data_inicial_pagamento = this.data_inicial_pagamento_temporario;
        this.data_final_pagamento = this.data_final_pagamento_temporario;
        this.valor_inicial = this.valor_inicial_temporario;
        this.valor_final = this.valor_final_temporario;
        this.selectedAvancados = this.selectedAvancadosTemporario;

        this.$store.commit(
          "contaPagar/SET_FILTROS_FAVORECIDO",
          this.favorecido_pessoa
        );
        this.$store.commit(
          "contaPagar/SET_FILTROS_SITUACAO",
          this.selectedAvancados
        );
        this.$store.commit(
          "contaPagar/SET_FILTROS_FORMA_COBRANCA",
          this.forma_pagamento_temporario
        );

        this.$store.commit(
          "contaPagar/SET_FILTROS_DATA_INICIAL_VENCIMENTO",
          this.data_inicial_vencimento
            ? beginOfDay(this.data_inicial_vencimento)
            : null
        );
        this.$store.commit(
          "contaPagar/SET_FILTROS_DATA_FINAL_VENCIMENTO",
          this.data_final_vencimento
            ? endOfDay(this.data_final_vencimento)
            : null
        );

        this.$store.commit(
          "contaPagar/SET_FILTROS_DATA_INICIAL_PAGAMENTO",
          this.data_inicial_pagamento
            ? beginOfDay(this.data_inicial_pagamento)
            : null
        );
        this.$store.commit(
          "contaPagar/SET_FILTROS_DATA_FINAL_PAGAMENTO",
          this.data_final_pagamento ? endOfDay(this.data_final_pagamento) : null
        );

        this.$store.commit(
          "contaPagar/SET_FILTROS_PLANO_CONTA",
          this.plano_conta ? this.plano_conta.id : null
        );

        const valorInicial = currencyToNumber(this.valor_inicial);
        const valorFinal = currencyToNumber(this.valor_final);
        this.$store.commit(
          "contaPagar/SET_FILTROS_VALOR_INICIAL",
          valorInicial || null
        );
        this.$store.commit(
          "contaPagar/SET_FILTROS_VALOR_FINAL",
          valorFinal || null
        );

        this.$store.commit(
          "contaPagar/SET_FILTROS_FORNECEDOR",
          this.fornecedor_pessoa
        );
      }

      this.SET_PAGINA_ATUAL(1);
      this.listar();
    },

    somarTotalItem(item, campoBaixado = false) {
      this.somarTotal(item, campoBaixado);
      this.valorTotal();
    },

    somarTotal(item, campoBaixado) {
      const montante = currencyToNumber(item.valor_montante);
      const juros = currencyToNumber(item.valor_juros);
      const desconto = currencyToNumber(item.valor_desconto);

      item.valor_total = montante + juros - desconto;
      item.total_titulo = item.valor_total;

      if (campoBaixado === false) {
        item.valor_diferenca_baixa = montante - item.titulo_pagar.valor_saldo;
      }
    },

    createSelected() {
      const newList = [];
      this.selected.map((item, index) => {
        this.forma_pagamento = null;
        newList.push(Object.assign({}, item));
        if (index === 0 && item.titulo_pagar.forma_cobranca) {
          this.forma_pagamento = item.titulo_pagar.forma_cobranca;
        }
      });
      this.createdSelected = newList;
    },

    validar(item) {
      const saldo = currencyToNumber(item.titulo_pagar.valor_saldo);
      const montante =
        currencyToNumber(item.valor_montante) -
        currencyToNumber(item.valor_diferenca_baixa);

      item.erro_valor = montante > saldo;
      this.podeSalvar = item.erro_valor;

      this.somarTotal(item);
    },

    valorTotal() {
      const lista = this.createdSelected;
      if(lista.length > 0){
        let valor = 0;
        for (let i = lista.length; i--; ) {
          valor += parseFloat(lista[i].total_titulo);
        }
      }
      conosle.log(valor)
      this.totalPagar = valor;
    },

    selectValue: (e) => {
      const target = e.target;
      target.setSelectionRange(0, target.value.length);
    },

    pagarTitulo(lista) {
      if (document.getElementsByClassName("floating-message").length > 0) {
        return;
      }

      this.pagando = true;
      const titulos = [];

      for (let i = lista.length; i--; ) {
        const titulo = {};

        titulo.titulo_pagar = lista[i].titulo_pagar.id;
        titulo.conta = this.conta.id;
        titulo.forma_pagamento = this.forma_pagamento
          ? this.forma_pagamento.id
          : null;
        titulo.franqueada = this.franqueadaSelecionada;
        titulo.tipo_movimento_conta = 1;

        if (!titulo.forma_pagamento && !lista[i].forma_pagamento) {
          titulo.forma_pagamento = lista[i].titulo_pagar.forma_cobranca.id;
        }

        titulo.valor_montante = currencyToNumber(lista[i].valor_montante);
        titulo.valor_juros = currencyToNumber(lista[i].valor_juros);
        titulo.valor_desconto = currencyToNumber(lista[i].valor_desconto);
        titulo.valor_diferenca_baixa = currencyToNumber(
          lista[i].valor_diferenca_baixa
        );
        titulo.valor_total = currencyToNumber(lista[i].valor_total);

        titulo.valor_lancamento = currencyToNumber(
          numberToCurrency(titulo.valor_total)
        );

        titulo.data_contabil = stringToISODate(
          this.data_pagamento_movimento_conta_temporario,
          true
        );
        titulo.data_deposito = stringToISODate(
          this.data_pagamento_movimento_conta_temporario,
          true
        );
        titulo.data_movimento = stringToISODate(dateToString(new Date()), true);

        titulos.push(titulo);
      }

      this.pagar(titulos)
        .then(() => {
          this.pagando = false;
          this.visible = false;

          this.filtrar();
        })
        .catch(() => {
          this.pagando = false;
        });
    },

    removerItem(item) {
      EventBus.$emit(
        "chamarModal",
        {
          resolve: (success) => {
            this.SET_ITEM_SELECIONADO_ID(item.titulo_pagar.id);
            this.remover().then(() => {
              this.filtrar();
            });
          },
        },
        `Deseja excluir esta parcela?`
      );
    },

    emitirRecibo(item) {
      console.log("RECIBO...", item);
    },

    contaSalva() {
      this.$refs.lancarConta.hide();
      this.itemSelecionado = null;
      this.filtrar();
    },

    cancelar() {
      this.selected = [];
      this.createdSelected = [];
    },

    cancelarModal() {
      this.$refs.lancarConta.hide();
      this.itemSelecionado = null;
    },

    editarConta(id) {
      this.$refs.verConta.hide();
      this.itemSelecionado = null;
      this.itemSelecionado = id;
      this.$refs.lancarConta.show();
    },

    cancelarModalVer() {
      this.$refs.verConta.hide();
      setTimeout(() => {
        this.itemSelecionado = null;
      }, 300);
    },

    mostrarFormularioPessoa() {
      this.fornecedorPessoaTemporariaContaPagar =
        this.$refs.formContaPagar.retornaDestinoFornecedorPessoa();
      this.$refs.modalFormularioPessoa.limparTipoPessoa("F");
      this.modalFormularioAtivo = "pessoa";
    },

    pessoaSalva(pessoaID) {
      this.modalFormularioAtivo = "conta-pagar";

      if (!pessoaID) {
        return;
      }

      this.$refs.formContaPagar.marcarTodasCategoriasPessoas();

      this.$store.commit("pessoas/setPessoaSelecionada", pessoaID);
      this.getPessoa().then((data) => {
        this.$store.commit("contaPagar/SET_FORNECEDOR_PESSOA", data);
      });
    },

    cancelarPessoa() {
      if (this.fornecedorPessoaTemporariaContaPagar !== null) {
        this.$store.commit(
          "contaPagar/SET_FORNECEDOR_PESSOA",
          Object.assign({}, this.fornecedorPessoaTemporariaContaPagar)
        );
      }
      this.modalFormularioAtivo = "conta-pagar";
    },

    setFavorecidoTemporario(value) {
      this.favorecido_pessoa_temporario =
        typeof value === "object" && value !== null ? value.id : value;
    },

    setFavorecidoRapidoTemporario(value) {
      this.favorecido_pessoa_rapido_temporario =
        typeof value === "object" && value !== null ? value.id : value;
      this.buscaRapida = true;
      this.filtrar();
    },

    tooltipText(observacao) {
      if (observacao) {
        return observacao.length > 500
          ? observacao.substring(0, 500) + "..."
          : observacao;
      }
    },

    resetBuscaInstrutor() {
      this.listaPagamentoInstrutor = [];
      this.total_pagar = 0;
      this.registros_considerados = [];
    },

    buscarPagamentosInstrutor() {
      this.isValid = true;

      if (!this.isValid || this.$v.instrutor.$invalid) {
        this.isValid = false;
        return;
      }

      this.resetBuscaInstrutor();

      const data = {
        funcionario: this.instrutor.id,
        data_inicio: moment(this.dataInicio, "DD/MM/YYYY").toISOString(),
        data_fim: moment(this.dataFim, "DD/MM/YYYY").toISOString(),
        modalidade_turma: this.modalidade,
      };

      this.carregandoPagamentoInstrutor = true;
      this.listarPagamentoInstrutor(data)
        .then((response) => {
          this.listaPagamentoInstrutor = response.registros;
          this.total_pagar = response.valor_total;
          this.registros_considerados = response.registros_considerados;
        })
        .catch((response) => {
          EventBus.$emit("criarAlerta", {
            tipo: response.status > 500 ? "E" : "A",
            mensagem: response.body.mensagem,
          });
        })
        .finally(() => {
          this.carregandoPagamentoInstrutor = false;
        });
    },

    cancelarPagamentoInstrutor() {
      this.pagamentoInstrutor = false;
      this.resetBuscaInstrutor();
    },

    setInstrutor(value) {
      this.instrutor = value;
      if (!value) {
        this.resetBuscaInstrutor();
      }
    },

    setPlanoConta(value) {
      this.plano_conta_temporario = value;
    },

    gerarPagamentoInstrutor() {
      this.gerandoPagamentoInstrutor = true;

      const data = {
        funcionario: this.instrutor.id,
        data_inicio: moment(this.dataInicio, "DD/MM/YYYY").toISOString(),
        data_fim: moment(this.dataFim, "DD/MM/YYYY").toISOString(),
        modalidade_turma: this.modalidade,
      };

      this.criarPagamentoInstrutor(data)
        .then(() => {
          this.instrutor = null;
          this.dataInicio = null;
          this.dataFim = null;

          this.resetBuscaInstrutor();
          this.$refs.pagamentoInstrutor.hide();
          this.filtrar();
        })
        .catch(console.error)
        .finally(() => {
          this.gerandoPagamentoInstrutor = false;
        });
    },
  },
};
</script>

<style scoped>
span.badge {
  font-size: 95%;
}

#filtros-rapidos,
#filtros-avancados {
  transition: all 0.1s;
}

.filtro-avancado .form-group {
  margin-bottom: 1rem;
}

.filtro-header {
  color: #4a4a4a;
}

.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151b1e;
  background-color: #fff;
}
.filtro-avancado .input-group-text {
  border: 0;
  background-color: #e5e5e5;
}

.btn-filter {
  color: #fff;
  background-color: #4a69c5;
  transition: all 0.2s;
}
.btn-filter.active {
  color: #4a69c5;
  background-color: transparent;
}
.btn-filter:not(.active):hover {
  background-color: #415eb5;
}

.table-filter div {
  display: flex;
  align-items: center;
}
.table-filter div label {
  margin: 0;
}
.table-filter div input {
  margin-left: 0.3rem;
}

.table-scroll {
  height: auto !important;
}

.text-left {
  font-size: 14px;
  text-align: left !important;
}

.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

.checkbtn-line {
  display: block;
}

.info-btn .btn {
  display: block;
  width: 100%;
}
.info-label > div {
  display: inline-block;
}

/* Table Card */
.table-card,
.cheader,
.cbody ul,
.cfooter,
.cbase {
  display: flex;
}

.cheader {
  color: #4a4a4a;
  background-color: #fff;
}

.cheader div,
.cbody li,
.cfooter div,
.cbase div {
  flex: 1 1 0;
  padding: 0.75rem;
}

.cbody {
  overflow-y: overlay;
  height: calc(100vh - 250px);
  height: -webkit-calc(100vh - 250px);
  height: -moz-calc(100vh - 250px);
  color: #4a4a4a;
}
.cbody ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  background-color: #f8f9fa;
  box-shadow: inset 0px 3px 0px 0px rgba(74, 74, 74, 0.05);
}
.cbody li {
  display: grid;
  position: relative;
}
.cbody ul li:first-child {
  text-align: center;
  box-shadow: inset 0px 3px 0px 0px rgba(74, 74, 74, 0.05);
}
.cbody ul li:first-child div {
  font-size: medium;
  display: flex;
  align-self: end;
  margin: 0 auto;
  padding-bottom: 0.4rem;
}

.table-card {
  width: 100%;
  max-width: 100%;
  background-color: #ebecf0;
  flex-direction: column;
  /* height: calc(100vh - 310px);
  height: -webkit-calc(100vh - 310px);
  height: -moz-calc(100vh - 310px); */
  height: auto;
}
.table-card thead {
  background-color: #f8f9fa;
}
.table-card tbody td:last-child {
  background-color: #f3f3f3;
  border-color: #f3f3f3;
}

.cfooter {
  background-color: #e0e0e0;
  font-size: large;
  margin-bottom: 1rem;
}
.cbody input {
  display: block;
  color: #3e515b;
  background-clip: padding-box;
  border: 0;
  border-radius: 0;
  width: 100%;
  padding: 0;
  line-height: 1;
  /* background-color: transparent; */
  transition: all 0.2s;
  font-size: large;
}
/* .cbody label:after {
  font-family: 'FontAwesome', 'Comfortaa' ;
  content: '\f14b';
  display: inline-block;
  padding-right: 3px;
  vertical-align: middle;
  font-size: x-large;
} */
.cbody input:focus {
  padding-left: 0.5rem;
  background-color: #e9e9e9;
}
.cbody ul li:last-child {
  color: #151b1e;
  font-size: large;
  align-items: flex-end;
  padding-bottom: 0.4rem;
}
.cfooter div,
.cbase div {
  display: grid;
  text-align: right;
  align-items: center;
  padding: 0.2rem 0.75rem;
}
.cfooter div:last-child {
  border-top: 1px dashed #c4c4c4;
  background-color: #e9e9e9;
  color: #3e515b;
  text-align: left;
}
.cbody ul li:last-child {
  background-color: #ebecf0;
}

.cheader div:last-child,
.cbody ul li:last-child,
.cfooter div:last-child,
.cbase div:last-child {
  padding-right: 1rem;
}

.cbase {
  font-size: large;
  margin-bottom: 1rem;
}
.cbase div:nth-child(4) {
  text-align: left;
}

.datepicker {
  padding: 0;
}

.floating-message {
  position: absolute;
  z-index: 1;
  margin-top: 4px;
  padding: 3px 5px;
  font-size: 0.7rem;
  width: 145px;
}

.floating-message::before {
  content: "";
  position: absolute;
  top: -16px;
  border: 8px solid #ff3860;
  border-top-color: transparent;
  border-left-color: transparent;
  border-right-color: transparent;
}

.cbody ul li:last-child .floating-message {
  margin-top: 74px;
  left: 12px;
}
@media (max-width: 991.98px) {
  .table-scroll {
    height: auto !important;
  }
}

@media (min-width: 769px) {
  .table-sm thead th.coluna-icones,
  .table-sm tbody td.coluna-icones {
    max-width: 70px;
  }
}

/*  min-width: 400px; */
</style>
