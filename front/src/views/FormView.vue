<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="submit($event)">
      <div class="body-sector p-2">
        <b-row>
          <template v-for="(field, index) in fields">
            <template v-if="field.showOnUpdate === true">
              <template v-if="['oneToMany', 'manyToMany'].includes(field.type) === false">
                <b-col :key="`${field.name}_${index}`" :class="field.formViewClass" class="form-group">

                  <!-- LABEL: presente em todos os campos, EXCETO checkbox (boolean) -->
                  <label v-if="field.type !== 'boolean'" :for="`${field.name}_${index}`" class="d-block col-form-label">{{ field.label }}{{ field.required ? ' *' : '' }}</label>
                  <label v-else class="d-block col-form-label">&nbsp;</label><!-- apenas para deixar o espaçamento correto -->

                  <!-- campos date -->
                  <template v-if="field.type === 'datetime'">
                    <date-picker :id="`${field.name}_${index}`" :disabled="(edit === true && field.canUpdate === false)" v-model="instance[field.name]" :required="field.required" />
                  </template>

                  <template v-if="field.type === 'string'">
                    <!-- campos de texto com máscaras -->
                    <template v-if="field.mask !== null">
                      <input v-mask="field.mask" :id="`${field.name}_${index}`" :disabled="(edit === true && field.canUpdate === false)" v-model="instance[field.name]" :required="field.required" type="text" class="form-control">
                    </template>

                    <!-- combobox com opções pré-definidas -->
                    <template v-else-if="field.selectOptions !== null">
                      <sync-select v-model="instance[field.name]" :field="field" :id="`${field.name}_${index}`" :required="field.required" />
                    </template>

                    <!-- campos texto comum -->
                    <template v-else>
                      <input :id="`${field.name}_${index}`" :disabled="(edit === true && field.canUpdate === false)" v-model="instance[field.name]" :maxlength="field.length" :required="field.required" type="text" class="form-control">
                    </template>
                  </template>

                  <!-- textarea -->
                  <template v-if="field.type === 'text'">
                    <textarea :id="`${field.name}_${index}`" v-model="instance[field.name]" :required="field.required" name="" cols="30" rows="10" class="form-control"></textarea>
                  </template>

                  <!-- checkbox, campos boolean -->
                  <template v-if="field.type === 'boolean'">
                    <b-form-checkbox :id="`${field.name}_${index}`" v-model="instance[field.name]" :value="true">{{ field.label }}{{ field.required ? ' *' : '' }}</b-form-checkbox>
                  </template>

                  <!-- relacionamentos manyToOne e oneToOne -->
                  <template v-if="field.type === 'manyToOne' || field.type === 'oneToOne'">
                    <!-- consulta por select, carrega o endpoint de uma só vez -->
                    <template v-if="field.findType === 'select'">
                      <sync-select v-model="instance[field.name]" :field="field" :id="`${field.name}_${index}`" :required="field.required" />
                    </template>

                    <!-- consulta por typeahead, carrega o endpoint ao digitar -->
                    <template v-else-if="field.findType === 'typeahead'">
                      <async-select v-model="instance[field.name]" :field="field" :id="`${field.name}_${index}`" :required="field.required" />
                    </template>
                  </template>
                </b-col>
              </template>

              <!-- separador -->
              <b-col v-if="field.formViewBreakAfter === true" :key="`${field.name}_${index}_separator`" xs="12" />

              <!-- oneToMany relationships, form view -->
              <template v-if="field.type === 'oneToMany'">
                <b-col :key="`${field.name}_${index}`" :class="field.formViewClass" class="form-group col-12 mt-2">
                  <h5>
                    <b-btn variant="roxo" class="pull-right" @click="addOneToManyRelation(field)"><font-awesome-icon icon="plus" /> Adicionar {{ field.label }}</b-btn>
                    {{ field.label }}
                  </h5>

                  <!-- para calcular o tamanho da lista conforme itens - não pronto - talvez não necessário, mas deixa mais bonito -->
                  <!-- <div :style="{ 'min-height': '84px', 'max-height': '400px', 'height': `${84 + innerTableHeight(field)}px` }" class="table-responsive-sm"> -->
                  <div class="table-responsive-sm">
                    <g-table>
                      <thead class="text-dark">
                        <tr>
                          <template v-for="childEntityField in auxiliarEntities[field.targetEntity]">
                            <th v-if="shouldShowColumn(childEntityField, field)" :key="childEntityField.name" :title="childEntityField.label" class="d-block text-truncate">{{ childEntityField.label || childEntityField.name }}</th>
                          </template>
                          <th class="coluna-icones">Ações</th>
                        </tr>
                      </thead>

                      <tbody>
                        <perfect-scrollbar>
                          <template v-for="(item, childEntityIndex) in instance[field.name]">
                            <tr v-if="!item._removed" :key="item.id">
                              <template v-for="childEntityField in auxiliarEntities[field.targetEntity]">
                                <td v-if="shouldShowColumn(childEntityField, field)" :data-label="childEntityField.label" :key="childEntityField.name" :class="childEntityField.listViewClass" class="d-block text-truncate">
                                  <template v-if="childEntityField.type === 'datetime'">
                                    <date-picker :id="`${field.name}_${index}`" :disabled="(edit === true && field.canUpdate === false)" v-model="instance[field.name]" :required="childEntityField.required" />
                                  </template>

                                  <template v-if="childEntityField.type === 'string'">
                                    <template v-if="childEntityField.mask !== null">
                                      <input v-mask="childEntityField.mask" :id="`${childEntityField.name}_${childEntityIndex}`" :disabled="(edit === true && childEntityField.canUpdate === false)" :required="childEntityField.required" v-model="instance[field.name][childEntityIndex][childEntityField.name]" type="text" class="form-control">
                                    </template>

                                    <template v-else-if="childEntityField.selectOptions !== null">
                                      <sync-select v-model="instance[field.name][childEntityIndex][childEntityField.name]" :field="field" :id="`${field.name}_${childEntityIndex}`" :required="childEntityField.required" />
                                    </template>

                                    <template v-else>
                                      <input :id="`${childEntityField.name}_${childEntityIndex}`" :disabled="(edit === true && childEntityField.canUpdate === false)" :required="childEntityField.required" v-model="instance[field.name][childEntityIndex][childEntityField.name]" :maxlength="childEntityField.length" type="text" class="form-control">
                                    </template>
                                  </template>

                                  <template v-if="childEntityField.type === 'text'">
                                    <textarea :id="`${childEntityField.name}_${childEntityIndex}`" :required="childEntityField.required" v-model="instance[field.name][childEntityIndex][childEntityField.name]" name="" cols="30" rows="10" class="form-control"></textarea>
                                  </template>

                                  <template v-if="childEntityField.type === 'manyToOne' || childEntityField.type === 'oneToOne'">
                                    <template v-if="childEntityField.findType === 'select'">
                                      <sync-select v-model="instance[field.name][childEntityIndex][childEntityField.name]" :field="childEntityField" :id="`${field.name}_${childEntityIndex}`" :required="childEntityField.required" />
                                    </template>

                                    <template v-else-if="childEntityField.findType === 'typeahead'">
                                      <async-select v-model="instance[field.name][childEntityIndex][childEntityField.name]" :field="childEntityField" :id="`${field.name}_${childEntityIndex}`" :required="childEntityField.required" />
                                    </template>
                                  </template>
                                </td>
                              </template>

                              <td class="d-flex coluna-icones">
                                <b-btn variant="link" title="Remover" class="icone-link" @click="removeOneToManyRelation(field.name, childEntityIndex)">
                                  <font-awesome-icon icon="trash" />
                                </b-btn>
                              </td>
                            </tr>
                          </template>

                          <div v-if="getInstanceFieldLength(field) === 0" class="text-muted text-center p-1">Ainda não há nada aqui.</div>
                        </perfect-scrollbar>
                      </tbody>
                    </g-table>
                  </div>
                </b-col>
              </template>

              <!-- manyToMany relationships, form view -->
              <template v-if="field.type === 'manyToMany'">
                <b-col :key="`${field.name}_${index}`" :class="field.formViewClass" class="form-group col-12 mt-2">
                  <h5>{{ field.label }}</h5>

                  <b-row>
                    <b-col :key="`${field.name}_${index}`" :class="field.formViewClass" class="form-group">
                      <label :for="`${field.name}_${index}`" class="d-block col-form-label">Adicionar {{ field.label }}</label>
                      <async-select :for="`${field.name}_${index}`" :field="field" @input="addManyToManyRelation" />
                    </b-col>
                  </b-row>

                  <div class="table-responsive-sm">
                    <g-table>
                      <thead class="text-dark">
                        <tr>
                          <template v-for="childEntityField in auxiliarEntities[field.targetEntity]">
                            <th v-if="shouldShowColumn(childEntityField, field)" :key="childEntityField.name" :title="childEntityField.label" class="d-block text-truncate">{{ childEntityField.label || childEntityField.name }}</th>
                          </template>
                          <th class="coluna-icones">Ações</th>
                        </tr>
                      </thead>

                      <tbody>
                        <perfect-scrollbar>
                          <template v-for="(item, itemIndex) in instance[field.name]">
                            <tr v-if="!item._removed" :key="`${item.id}_${itemIndex}`">
                              <template v-for="childEntityField in auxiliarEntities[field.targetEntity]">
                                <td v-if="shouldShowColumn(childEntityField, field)" :data-label="childEntityField.label" :key="childEntityField.name" :class="childEntityField.listViewClass" class="d-block text-truncate">
                                  <template v-if="(childEntityField.type === 'manyToOne' || childEntityField.type === 'oneToOne') && item[childEntityField.name]">
                                    <span :title="makeDescription(item, childEntityField.descriptionColumn, childEntityField.name)">{{ makeDescription(item, childEntityField.descriptionColumn, childEntityField.name) }}</span>
                                  </template>

                                  <template v-else-if="childEntityField.type === 'manyToMany' || childEntityField.type === 'oneToMany'">
                                    <span :title="item[childEntityField.name].map(subitem => makeDescription(subitem, childEntityField.descriptionColumn)).join(', ')">
                                      {{ item[childEntityField.name].map(subitem => makeDescription(subitem, childEntityField.descriptionColumn)).join(', ') }}
                                    </span>
                                  </template>

                                  <template v-else>
                                    <span :title="makeDescription(item, childEntityField.name)">
                                      {{ makeDescription(item, childEntityField.name) }}
                                    </span>
                                  </template>
                                </td>
                              </template>

                              <td class="d-flex coluna-icones">
                                <b-btn variant="link" title="Remover" class="icone-link" @click="removeManyToManyRelation(field.name, itemIndex)">
                                  <font-awesome-icon icon="trash" />
                                </b-btn>
                              </td>
                            </tr>
                          </template>

                          <div v-if="getInstanceFieldLength(field) === 0" class="text-muted text-center p-1">Ainda não há nada aqui.</div>
                        </perfect-scrollbar>
                      </tbody>
                    </g-table>
                  </div>
                </b-col>
              </template>

            </template>
          </template>
        </b-row>
      </div>

      <div class="form-group">
        <b-btn :disabled="submiting" type="submit" variant="verde" @click="navigateOrReload = 'reload'">Salvar</b-btn>
        <b-btn :disabled="submiting" type="submit" variant="verde" @click="navigateOrReload = 'navigate'">Salvar e sair</b-btn>
        <b-btn :disabled="submiting" type="button" variant="link" @click="navigateToList()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import Request from '../utils/request'
import AsyncSelect from '../components/fields/AsyncSelect'
import SyncSelect from '../components/fields/SyncSelect'
import DatePicker from '../components/fields/DatePicker'
import EventBus from '../utils/event-bus'

export default {
  components: {
    AsyncSelect,
    SyncSelect,
    DatePicker
  },

  props: {
    entity: {
      type: String,
      required: false,
      default: null
    }
  },

  data () {
    return {
      edit: false,
      submiting: false,
      isValid: true,
      navigateOrReload: 'navigate',

      entityName: null,
      fields: [],
      instance: {},
      updateJoins: [],
      auxiliarEntities: {},
      permissions: []
    }
  },

  computed: {
    entityPath () {
      return `App\\Entity\\Principal\\${this.entityName}`
    }
  },

  mounted () {
    const routeParams = this.$route.params
    this.entityName = routeParams.entity || this.entity

    Request.get('/metadata', { entity: this.entityPath, view: 'form' })
      .then(response => {
        this.fields = response.body.fields
        this.auxiliarEntities = response.body.auxiliarEntities
        this.updateJoins = response.body.updateJoins
        this.permissions = response.body.permissions
        const instance = {}
        this.fields.forEach(field => {
          let value = null
          if (field.type === 'oneToMany' || field.type === 'manyToMany') {
            value = []
          }

          instance[field.name] = value
        })

        if (routeParams.id) {
          if (!this.hasPermission('EDITAR')) {
            this.navigateToList()
            return
          }

          this.edit = true
          Request.get(`/magic/${routeParams.id}`, { entity: this.entityPath, with: this.updateJoins })
            .then(response => {
              for (let i in response.body.data) {
                const fieldRelated = this.fields.find(f => f.name === i)
                if (fieldRelated.type === 'datetime') {
                  if (response.body.data[i]) {
                    response.body.data[i] = new Date(response.body.data[i])
                  }
                }
              }

              this.instance = { ...instance, ...response.body.data }
            })
            .catch(console.error)
        } else {
          this.edit = false
          this.instance = instance
        }
      })
      .catch(console.error)
  },

  methods: {
    navigateToList () {
      this.$router.push(`/m/${this.entityName}`)
    },

    submit ($event) {
      $event.preventDefault()

      this.isValid = false
      this.submiting = true
      const data = { ...this.instance }
      const invalidFields = []
      let invalidRelations = false

      this.fields.forEach(field => {
        if (field.type === 'datetime' && data[field.name]) {
          data[field.name] = data[field.name].toISOString()
        } else if (field.type === 'boolean') {
          data[field.name] = data[field.name] ? 1 : 0
        } else if ((field.type === 'string' || field.type === 'number') && data[field.name] && typeof data[field.name] === 'object' && data[field.name][field.valueColumn]) {
          data[field.name] = data[field.name][field.valueColumn]
        } else if (['manyToOne', 'oneToOne'].includes(field.type) && data[field.name]) {
          data[`${field.name}_id`] = data[field.name].id
        } else if (['oneToMany', 'manyToMany'].includes(field.type)) {
          this.auxiliarEntities[field.targetEntity].forEach(childEntityField => {
            if (childEntityField.required && data[field.name].some(fieldData => !fieldData[childEntityField.name])) {
              invalidRelations = true
            }
          })
        }

        if (field.required && (!data[field.name] || ((field.type === 'number' || field.type === 'string') && typeof data[field.name] === 'object' && !data[field.name][field.valueColumn]) || (['oneToMany', 'manyToMany'].includes(field.type) && !data[field.name].length))) {
          invalidFields.push(field)
        }
      })

      if (invalidRelations === true || invalidFields.length > 0) {
        this.submiting = false
        return
      }

      const method = this.edit === true ? 'put' : 'post'
      Request[method](`/magic`, { entity: this.entityPath, data })
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Salvo com sucesso'
          })

          if (this.navigateOrReload === 'navigate') {
            this.navigateToList()
          } else {
            this.$router.go(this.$router.currentRoute)
          }
        })
        .catch(response => {
          let errorMessage = 'Houve um erro ao salvar'
          let additionalInfo = null
          if (response.body.errors && response.body.errors[0]) {
            errorMessage = response.body.errors[0]
            additionalInfo = response.body.errors[1]
          } else if (response.body.mensagem) {
            errorMessage = response.body.mensagem
          }

          EventBus.$emit('criarAlerta', {
            tipo: response.status > 500 ? 'E' : 'A',
            mensagem: errorMessage,
            informacaoAdicional: additionalInfo
          })
        })
        .finally(() => {
          this.submiting = false
        })
    },

    getEntityFields (entityName) {
      const fields = {}
      const entity = this.auxiliarEntities[entityName]
      entity.forEach(field => {
        fields[field.name] = null
      })

      return fields
    },

    getInstanceFieldLength (field) {
      if (!this.instance[field.name]) {
        return 0
      }

      return this.instance[field.name].filter(i => i._removed !== true).length
    },

    /*
    innerTableHeight (field) {
      return this.getInstanceFieldLength(field) * 38
    },
    */

    shouldShowColumn (childEntityField, field) {
      const showManyToMany = field.manyToManyTableColumns ? !!field.manyToManyTableColumns.find(column => column.match(childEntityField.name)) : false
      const showOneToMany = field.oneToManyTableColumns ? !!field.oneToManyTableColumns.find(column => column.match(childEntityField.name)) : false
      return childEntityField.showOnBrowse && (showManyToMany || showOneToMany)
    },

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

    removeOneToManyRelation (fieldName, indexToRemove) {
      this.instance[fieldName] = this.instance[fieldName].map((item, itemIndex) => {
        if (indexToRemove === itemIndex) {
          item._removed = true
        }

        return item
      })
    },

    addOneToManyRelation (field) {
      this.instance[field.name].push({
        ...this.getEntityFields(field.targetEntity),
        _added: true
      })
    },

    removeManyToManyRelation (fieldName, indexToRemove) {
      this.instance[fieldName] = this.instance[fieldName].map((item, itemIndex) => {
        if (indexToRemove === itemIndex) {
          item._removed = true
        }

        return item
      })
    },

    addManyToManyRelation (value, field) {
      this.instance[field.name].push({ _added: true, ...value })
    },

    hasPermission (key) {
      return this.permissions.some(permission => permission.descricao === key && permission.has_permission)
    }
  }
}
</script>
