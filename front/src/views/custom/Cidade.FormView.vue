<template>
  <div class="animated fadeIn">

    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="submit($event)">
      <div class="body-sector p-3">
        <template v-for="(field, index) in fields" class="row">
          <div v-if="field.showOnUpdate === true" :key="`${field.name}_${index}`" class="form-group col-md-3">
            <template v-if="field.type === 'datetime'">
              <label :for="`${field.name}_${index}`" class="col-form-label">{{ field.label }}</label>
              <g-datepicker :element-id="`${field.name}_${index}`" :disabled="(edit === true && field.canUpdate === false)" v-model="instance[field.name]" />
            </template>

            <template v-if="field.type === 'string'">
              <label :for="`${field.name}_${index}`" class="col-form-label">{{ field.label }}</label>
              <template v-if="field.mask !== null">
                <input v-mask="field.mask" :id="`${field.name}_${index}`" :disabled="(edit === true && field.canUpdate === false)" v-model="instance[field.name]" type="text" class="form-control">
              </template>
              <template v-else>
                <input :id="`${field.name}_${index}`" :disabled="(edit === true && field.canUpdate === false)" v-model="instance[field.name]" :maxlength="field.length" type="text" class="form-control">
              </template>
            </template>
          </div>
        </template>
      </div>

      <div class="form-group">
        <b-btn :disabled="submiting" type="submit" variant="verde">Salvar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import FormView from '../FormView'
export default {
  extends: FormView,

  data () {
    return {
      name: 'Marcelo'
    }
  }
}
</script>
