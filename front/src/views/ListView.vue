<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div v-if="filters.quick.length" :class="{'open-filter': filterCollapse.quick}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="toggleFilters('quick')">Filtro Rápido</div>
          <div v-if="filters.advanced.length" :class="{'open-filter': filterCollapse.advanced}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="toggleFilters('advanced')">Avançado</div>
        </div>

        <router-link v-if="hasPermission('CRIAR')" :to="`/m/${entity}/adicionar`" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="quickFilterCollapse" v-model="filterCollapse.quick">
        <form class="p-2" @submit.prevent>
          <b-row>
            <template v-for="(field, index) in filters.quick">
              <b-col :key="`${field.name}_${index}`" :class="field.formViewClass" class="form-group">
                <label :for="`quick_${field.name}_${index}`" class="d-block col-form-label">{{ field.label }}</label>

                <template v-if="field.type === 'datetime'">
                  <date-picker :id="`${field.name}_${index}`" v-model="filtersData.quick[field.name]" :required="field.required" @input="debouncedFilter()" />
                </template>

                <template v-if="field.type === 'string' || field.filterInfo.criteria === 'LIKE'">
                  <template v-if="field.mask !== null">
                    <input v-mask="field.mask" :id="`quick_${field.name}_${index}`" v-model="filtersData.quick[field.name]" type="text" class="form-control" @input="debouncedFilter()">
                  </template>

                  <template v-else-if="field.selectOptions !== null">
                    <sync-select :id="`quick_${field.name}_${index}`" v-model="filtersData.quick[field.name]" :field="field" @input="debouncedFilter()" />
                  </template>

                  <template v-else>
                    <input :id="`quick_${field.name}_${index}`" v-model="filtersData.quick[field.name]" :maxlength="field.length" type="text" class="form-control" @input="debouncedFilter()">
                  </template>
                </template>

                <template v-else-if="field.type === 'boolean'">
                  <template v-if="field.selectOptions !== null">
                    <b-form-checkbox-group :id="`quick_${field.name}_${index}`" v-model="filtersData.quick[field.name]" :options="field.selectOptions" :name="`quick_${field.name}_${index}`" @input="filter() && $forceUpdate()" />
                  </template>
                  <!-- <template v-else> -->
                  <!--   <b-form-checkbox v-model="filtersData.quick[field.name]" /> -->
                  <!-- </template> -->
                </template>

                <template v-if="field.type === 'text'">
                  <textarea :id="`quick_${field.name}_${index}`" v-model="filtersData.quick[field.name]" name="" cols="30" rows="10" class="form-control" @input="debouncedFilter()"></textarea>
                </template>

                <template v-if="field.type === 'manyToOne' || field.type === 'oneToOne'">
                  <template v-if="field.findType === 'select'">
                    <sync-select :id="`quick_${field.name}_${index}`" v-model="filtersData.quick[field.name]" :field="field" @input="debouncedFilter()" />
                  </template>

                  <template v-else-if="field.findType === 'typeahead'">
                    <async-select :id="`quick_${field.name}_${index}`" v-model="filtersData.quick[field.name]" :field="field" @input="debouncedFilter()" />
                  </template>
                </template>
              </b-col>
            </template>
          </b-row>
        </form>
      </b-collapse>

      <b-collapse id="advancedFilterCollapse" v-model="filterCollapse.advanced">
        <form ref="advancedFilterForm" class="p-2" @submit.prevent="filter('advanced')">
          <b-row>
            <template v-for="(field, index) in filters.advanced">
              <b-col :key="`${field.name}_${index}`" :class="field.formViewClass" class="form-group">
                <label :for="`advanced_${field.name}_${index}`" class="d-block col-form-label">{{ field.label }}</label>

                <template v-if="field.type === 'datetime'">
                  <date-picker :id="`advanced_${field.name}_${index}`" v-model="filtersData.advanced[field.name]" />
                </template>

                <template v-if="field.type === 'string'">
                  <template v-if="field.mask !== null">
                    <input v-mask="field.mask" :id="`advanced_${field.name}_${index}`" v-model="filtersData.advanced[field.name]" type="text" class="form-control">
                  </template>

                  <template v-else-if="field.selectOptions !== null">
                    <sync-select :id="`advanced_${field.name}_${index}`" v-model="filtersData.advanced[field.name]" :field="field" />
                  </template>

                  <template v-else>
                    <input :id="`advanced_${field.name}_${index}`" v-model="filtersData.advanced[field.name]" :maxlength="field.length" type="text" class="form-control">
                  </template>
                </template>

                <template v-if="field.type === 'text'">
                  <textarea :id="`advanced_${field.name}_${index}`" v-model="filtersData.advanced[field.name]" name="" cols="30" rows="10" class="form-control"></textarea>
                </template>

                <template v-if="field.type === 'manyToOne' || field.type === 'oneToOne'">
                  <template v-if="field.findType === 'select'">
                    <sync-select :id="`advanced_${field.name}_${index}`" v-model="filtersData.advanced[field.name]" :field="field" @input="toggleFilters('advanced', false)" /> <!-- toggleFilters para resolver reatividade -->
                  </template>

                  <template v-else-if="field.findType === 'typeahead'">
                    <async-select v-model="filtersData.advanced[field.name]" :id="`advanced_${field.name}_${index}`" :field="field" />
                  </template>
                </template>
              </b-col>
            </template>
          </b-row>

          <button type="submit" class="btn btn-cinza btn-block text-uppercase mt-3" @click="toggleFilters('advanced')">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="onSortTable">
        <thead class="text-dark">
          <tr>
            <template v-for="field in fields">
              <th v-if="field.showOnBrowse" :key="field.name" :data-column="field.canOrderBy ? (field.orderColumn || field.name) : null" :title="field.label" class="d-block text-truncate">{{ field.label || field.name }}</th>
            </template>
            <th class="coluna-icones">Ações</th>
          </tr>
        </thead>

        <tbody>
          <perfect-scrollbar @ps-y-reach-end="loadMore()">
            <tr v-for="item in itemsList" :key="item.id" @dblclick="mainAction(item)">
              <template v-for="field in fields">
                <td v-if="field.showOnBrowse" :data-label="field.label" :key="field.name" :class="field.listViewClass" class="d-block text-truncate">
                  <template v-if="(field.type === 'manyToOne' || field.type === 'oneToOne') && item[field.name]">
                    <span :title="makeDescription(item, field.descriptionColumn, field.name)">{{ makeDescription(item, field.descriptionColumn, field.name) }}</span>
                  </template>

                  <template v-else-if="field.type === 'manyToMany' || field.type === 'oneToMany'">
                    <span :title="item[field.name].map(subitem => makeDescription(subitem, field.descriptionColumn)).join(', ')">
                      {{ item[field.name].map(subitem => makeDescription(subitem, field.descriptionColumn)).join(', ') }}
                    </span>
                  </template>

                  <template v-else-if="field.type === 'boolean'">
                    <b-form-checkbox v-model="item[field.name]" :disabled="true" />
                  </template>

                  <template v-else>
                    <template v-if="field.type === 'datetime'">
                      <template v-if="field.format && field.format === 'date'">
                        <span :title="formatarData(item[field.name])">
                          {{ item[field.name] | formatarData }}
                        </span>
                      </template>

                      <template v-else>
                        <span :title="formatarDataHora(item[field.name])">
                          {{ item[field.name] | formatarDataHora }}
                        </span>
                      </template>
                    </template>

                    <template v-else>
                      <template v-if="field.type === 'string' && field.selectOptions !== null">
                        <span :title="getFieldOption(field, item)">
                          {{ getFieldOption(field, item) }}
                        </span>
                      </template>

                      <template v-else>
                        <span :title="item[field.name]">
                          {{ item[field.name] }}
                        </span>
                      </template>
                    </template>
                  </template>
                </td>
              </template>

              <td class="d-flex coluna-icones">
                <b-btn v-if="hasPermission('EDITAR')" type="button" variant="link" title="Atualizar" class="icone-link" @click="mainAction(item)">
                  <font-awesome-icon icon="pen" />
                </b-btn>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div></div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <template v-if="isLoading === false">
            <small v-if="itemsCount >= 1">{{ itemsCount }} ite{{ itemsCount > 1 ? 'ns' : 'm' }} encontrado{{ itemsCount > 1 ? 's' : '' }}</small>
            <small v-else>Nada encontrado</small>
          </template>

          <template v-else>
            <small>Carregando...</small>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Request from '../utils/request'
import SyncSelect from '../components/fields/SyncSelect'
import AsyncSelect from '../components/fields/AsyncSelect'
import DatePicker from '../components/fields/DatePicker'
import {debounce} from '../utils/functions'
import formatarData from '../filters/formatar-data'
import formatarDataHora from '../filters/formatar-data-hora'

export default {
  components: { SyncSelect, AsyncSelect, DatePicker },

  data () {
    return {
      filters: {
        quick: [],
        advanced: []
      },
      filterCollapse: { quick: false, advanced: false },
      filtersData: {
        quick: {},
        advanced: {}
      },
      debouncedFilter: debounce(() => this.filter('quick'), 300),
      sort: { column: null, direction: null },
      where: [],
      fields: [],
      itemsList: [],
      itemsCount: 0,
      isLoading: true,
      currentPage: 0,
      browseJoins: [],
      auxiliarEntities: {},
      permissions: []
    }
  },

  computed: {
    entity () {
      return this.$route.params.entity
    },

    entityPath () {
      return `App\\Entity\\Principal\\${this.entity}`
    }
  },

  mounted () {
    this.currentPage = 0
    this.isLoading = true

    Request.get('/metadata', { entity: this.entityPath, view: 'list' })
      .then(response => {
        this.where = []

        this.fields = response.body.fields.map(field => {
          if (field.selectOptions !== null && field.selectOptions !== 'object') {
            field.selectOptions = JSON.parse(field.selectOptions.replace(/'/g, '"'))
          }

          return field
        })

        this.browseJoins = response.body.browseJoins
        this.auxiliarEntities = response.body.auxiliarEntities
        this.permissions = response.body.permissions

        if (response.body.filters) {
          if (response.body.filters.quick) {
            this.filters.quick = this.prepareFilters('quick', response.body.filters)
          }

          if (response.body.filters.advanced) {
            this.filters.advanced = this.prepareFilters('advanced', response.body.filters)
          }
        }

        this.isLoading = false
        this.loadMore()
      })
      .catch((error) => {
        this.isLoading = false
        console.error(error)
      })
  },

  methods: {
    formatarData,
    formatarDataHora,

    makeDescription (item, fieldColumn, fieldName) {
      let valueGroup = []
      fieldColumn.split(',').forEach(column => {
        let value = fieldName ? item[fieldName] : item
        column.split('.').forEach(path => {
          value = value[path] || value
        })

        valueGroup.push(value)
      })

      return valueGroup.join(' - ')
    },

    mainAction (item) {
      if (this.hasPermission('EDITAR')) {
        this.$router.push(`/m/${this.entity}/atualizar/${item.id}`)
      }
    },

    onSortTable (data) {
      this.sort.column = data.detail.order
      this.sort.direction = data.detail.direcao

      this.currentPage = 0
      this.loadMore()
    },

    loadMore () {
      if (this.isLoading === false && (this.currentPage === 0 || this.itemsCount > this.itemsList.length)) {
        this.currentPage++
        this.isLoading = true

        Request.get('/magic', { entity: this.entityPath, page: this.currentPage, with: this.browseJoins, where: this.where, sort_column: this.sort.column, sort_direction: this.sort.direction })
          .then((response) => {
            // empty list before showing results
            if (this.currentPage === 1) {
              this.itemsList = []
            }

            this.itemsList = [...this.itemsList, ...response.body.data]
            this.itemsCount = response.body.total
            this.isLoading = false
          })
          .catch(console.error)
      }
    },

    prepareFilters (form, filters) {
      return filters[form].map(filter => {
        const field = this.getField(filter)
        if (field) {
          this.filtersData[form][filter.field] = null
        }

        const formViewClass = filter.formViewClass || field.formViewClass

        if (!field.where) {
          field.where = []
        }

        if (!field.with) {
          field.with = []
        }

        if (!filter.where) {
          filter.where = []
        }

        if (!filter.with) {
          filter.with = []
        }

        const where = [...field.where, ...filter.where]
        const withEntities = [...field.with, ...filter.with]
        return { ...field, name: filter.field, formViewClass, with: withEntities, where, filterInfo: filter }
      })
    },

    getField (filter) {
      const splitFieldName = filter.field.split('.')
      let field = this.fields.find(f => f.name === splitFieldName[0])

      if (splitFieldName.length > 1) {
        field = this.auxiliarEntities[field.targetEntity].find(f => f.name === splitFieldName[1])
      }

      return field
    },

    filter (form = 'quick') {
      let where = this.filters[form].filter(field => !!this.filtersData[form][field.name])
      where = where.map(field => {
        let fieldName = field.name

        let value = null
        if (field.filterInfo.criteria === 'LIKE') {
          value = field.filterInfo.pattern.replace(/&value/, this.filtersData[form][field.name])
        }

        if (field.type === 'datetime') {
          if (this.filtersData[form][field.name]) {
            value = this.filtersData[form][field.name].toISOString()
          }
        } else if (field.type === 'boolean') {
          if (field.filterInfo.criteria === '=' && this.filtersData[form][field.name] !== null) {
            value = this.filtersData[form][field.name] ? 1 : 0
          } else if (field.filterInfo.criteria === 'IN') {
            value = this.filtersData[form][field.name].map(val => val === true ? 1 : 0)
            if (value.length === 0) {
              value = null
            }
          }
        } else if ((field.type === 'string' || field.type === 'number') && this.filtersData[form][field.name] && typeof this.filtersData[form][field.name] === 'object' && this.filtersData[form][field.name][field.valueColumn]) {
          value = this.filtersData[form][field.name][field.valueColumn]
        } else if (['manyToOne', 'oneToOne'].includes(field.type) && this.filtersData[form][field.name] && field.filterInfo.criteria !== 'LIKE') {
          value = this.filtersData[form][field.name].id
        }

        return {
          field: fieldName,
          criteria: field.filterInfo.criteria,
          value
        }
      })

      where = where.filter(criteria => !!criteria.value)
      this.where = where

      this.currentPage = 0
      this.loadMore()
    },

    getFieldOption (field, item) {
      let options = field.selectOptions
      if (typeof field.selectOptions !== 'object') {
        options = JSON.parse(field.selectOptions.replace(/'/g, '"'))
      }

      const option = options.find(option => option[field.valueColumn] === item[field.name])
      return option ? option[field.descriptionColumn] : undefined
    },

    toggleFilters (filter, closeOnRepeat = true) {
      this.filterCollapse[filter] = this.filterCollapse[filter] === false

      // algum problema com o sync-select não atualizando na tela
      if (closeOnRepeat === false) {
        this.filterCollapse[filter] = true
      }

      if (filter === 'quick') {
        this.filterCollapse.advanced = false
      }

      if (filter === 'advanced') {
        this.filterCollapse.quick = false
      }
    },

    hasPermission (key) {
      return this.permissions.some(permission => permission.descricao === key && permission.has_permission)
    }
  }
}
</script>

<style scoped>
.table-responsive-sm {
  min-height: 110px !important;
}

.filtro-avancado .btn.open-filter {
  color: #151B1E;
  background-color: #fff;
}
</style>
