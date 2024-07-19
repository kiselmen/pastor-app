<template>
  <div class="table-container" ref="tableElem">
    <table class="table">
      <thead class="table-head">
        <tr>
          <th v-if="hasActiveBtn">
            <div class="table-actions" v-if = multiRowActions.length>
              <CheckBox
                @toggleCheckBox="onSelectAllRows"
                :modelValue = "isAllRowSelected"
              />
              <ActionMenu
                :actions = "multiRowActions"
                @startAction="onAllRowsAction"
              >
                <template #icon>
                  <MenuKebabIcon/>
                </template>
              </ActionMenu>
            </div>
          </th>
          <th v-for="name in props.tableHeadNames">
            {{ name }}
          </th>
        </tr>
      </thead>
      <tbody class="table-body">
        <tr v-for="row in props.tableData" :key = row.id>
          <td v-if="hasActiveBtn">
            <div class="table-actions">
              <CheckBox v-if = multiRowActions.length
                :modelValue = "row.selected"
                :boxID = "row.id"
                @toggleCheckBox="onSelectRow"
              />
              <ActionMenu
                :actions = "props.actions"
                :boxID = "row.id"
                :parentElem = "tableElem"
                @startAction="onRowAction"
              >
                <template #icon>
                  <MenuKebabIcon/>
                </template>
              </ActionMenu>
            </div>  
          </td>
          <td v-for="element_id in props.tableHeadID" :style = "{color: isColorRow(element_id, row[element_id]) }">
            {{ row[element_id] }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
  import CheckBox from '@/components/ui/CheckBox.vue';
  import ActionMenu from '@/components/ui/ActionMenu.vue';
  import MenuKebabIcon from '@/components/icons/IconMenuKebab.vue';
  import { ref, computed } from 'vue';

  const props = defineProps({
    tableHeadNames: { type: Array, default: new Array() },
    tableHeadID: { type: Array, default: new Array() },
    tableData: { type: Array, default: new Array() },
    hasActiveBtn: {type: Boolean, default: false },
    isAllRowSelected: {type: Boolean, default: false },
    actions: {type: Array, default: new Array() },
  })

  const emits = defineEmits(['rowAction', 'allRowAction', 'selectRow', 'selectAllRows']);

  const tableElem = ref(null);

  const multiRowActions = computed( () => {
    return props.actions.filter(item => item.type > 0);
  });

  const onSelectRow = (value, rowID) => {
    emits('selectRow', value, rowID);
  }

  const onSelectAllRows = (value) => {
    emits('selectAllRows', value);
  }

  const onAllRowsAction = (actionID) => {
    emits('allRowAction', actionID);
  }

  const onRowAction = (actionID, rowID) => {  
    emits('rowAction', actionID, rowID);
  }

  const isColorRow = (name, color) => {
    if (name == 'color') {
      return color ? color : '';
    } else {
      return '';
    }
  }

</script>

<style lang="scss" scoped>
  *, *::before, *::after {
    box-sizing: border-box;
  }

  thead, tbody, tfoot, tr, td, th {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
  }

  thead, tbody, tfoot, tr, td, th {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
  }

  tr {
    display: table-row;
    vertical-align: inherit;
    unicode-bidi: isolate;
    border-color: inherit;
  }
  tr:hover {
      color: var(--bs-primary);
    }

  th {
    display: table-cell;
    vertical-align: inherit;
    font-weight: bold;
    text-align: -internal-center;
    unicode-bidi: isolate;
  } 

  td {
    display: table-cell;
    vertical-align: inherit;
    unicode-bidi: isolate;
  }

  .table {
    width: 100%;
    margin-bottom: 1rem;
    vertical-align: top;
    border-color: var(--bs-primary);
    caption-side: bottom;

    display: table;
    border-collapse: collapse;
    box-sizing: border-box;
    text-indent: initial;
    unicode-bidi: isolate;
    border-spacing: 2px;
    border-color: var(--bs-gray-500);
    &-container {
      padding: 1rem;      
    }
    &-actions {
      display: flex;
      flex-direction: row;
      justify-content: flex-start;
      align-items: flex-end;
      cursor: pointer;
    }    
  }

  .table > thead {
    vertical-align: bottom;
  }

  .table th {
    line-height: 2.5rem;
  }

  .table th, .table td {
    text-align: center;
    line-height: 2rem;
  }

  .table > :not(caption) > * > * {
    padding: 0.5rem 0.5rem;
    background-color: var(--bs-white);
    border-bottom-width: 1px 
  }

  .table tr th {
    border-bottom-width: 1px;
  }

  .table td, .table th {
    vertical-align: middle;
  }

  .table > tbody {
    vertical-align: inherit;
  }

  tbody {
    display: table-row-group;
    vertical-align: middle;
    unicode-bidi: isolate;
    border-color: inherit;
  }

</style>