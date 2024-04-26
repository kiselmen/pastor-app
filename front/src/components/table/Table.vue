<template>
  <div ref="tableWrapper" class="table__wrapper">
    <table class="table">
      <thead class="table__head">
        <draggable v-model="headCopy" tag="tr" :item-key="key => key" v-bind="dragOptions" @end="handleDragEnd">
          <template #item="{ element: th }">
            <th
              :key="th.id"
              class="table__th"
              :class="[th.id]"
              scope="col"
            >
              <div v-if="typeof th === 'object' && th.type === 'checkbox'" class="checkbox-container__wrapper">
                <CheckBox v-model="props.allSelected" @update:model-value="handleSelectedAll" />
                <span>{{ th.label }}</span>
              </div>
              <div v-else class="checkbox-container__wrapper">
                <span>{{ th.label }}</span>
                <div v-if="th.filterable" class="filter-icon" @click="toggleSort(th.label)">
                  <IconChevronDown class="chevron" :class="{ 'is-asc': th.sort === 'ASC' }" />
                </div>
              </div>
            </th>
          </template>
        </draggable>
      </thead>
      <tbody v-if="!props.loading" class="table__body">
        <slot />
      </tbody>
      <tbody v-if="props.loading" class="table__body">
        <tr v-for="i in props.size" class="table__skeleton-wrapper">
          <td class="table__skeleton-wrapper__row">
            <Skeleton height="1rem" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
  import draggable from 'vuedraggable';
  import { ref } from 'vue';
  import CheckBox from '@/components/ui/CheckBox.vue';
  import IconChevronDown from '@/components/icons/IconChevronDown.vue'
  import Skeleton from '@/components/ui/Skeleton.vue';

  const emit = defineEmits(['updateHeadOrder', 'updateSort', 'toggle-all'])

  const props = defineProps({
    head: [],
    columnVisibility: Array(),
    size: 0,
    loading: false,
    allSelected: false
  })

  const headCopy = ref(props.head)

  const handleDragEnd = () => {
    emit('updateHeadOrder', headCopy.value) // Emit the updated head order to the parent component
  }

  const dragOptions = {
    animation: 170, // Animation duration in seconds
    group: 'description',
    disabled: false,
    ghostClass: 'sortable-ghost'
    // Add other draggable options here
  }

  const handleSelectedAll = (value) => {
    emit('toggle-all', value)
  }

  const toggleSort = (label) => {
    headCopy.value.forEach((th) => {
      if (th.label === label) {
        th.sort = th.sort === 'ASC' ? 'DESC' : 'ASC'
      } else if (th.filterable) {
        th.sort = 'DESC'
      }
    })

    const sortedColumn = headCopy.value.find(th => th.label === label)
    if (sortedColumn && sortedColumn.filterable) {
      emit('updateSort', { direction: sortedColumn.sort, value: sortedColumn.sort_value })
    }
  }

</script>

<style lang="scss">

$scrollbar-default: #f2f3f5;
$scrollbar-hover: #d1d6dc;
$scrollbar-active: #b9bfc8;

@mixin scrollbar {
  ::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }
  ::-webkit-scrollbar-thumb {
    background-color: $scrollbar-default;
    background-clip: padding-box;
    border-radius: 10px;
    border: 2px solid transparent;
    box-shadow: inset 0 0 0 10px rgba(0, 0, 0, 0.025);
  }

  ::-webkit-scrollbar-thumb:hover {
    background-color: $scrollbar-hover;
  }

  ::-webkit-scrollbar-thumb:active {
    background-color: $scrollbar-active;
  }

  ::-webkit-scrollbar-track {
    background-color: transparent; // Прозрачный scrollbar track
  }

  scrollbar-color: $scrollbar-default white;
  scrollbar-width: thin;

}

.is-asc {
  transform: rotate(180deg);
}

.chevron {
  transition: 0.2s ease-in;
}

.filter-icon {
  display: flex;
  align-items: center;
  justify-content: center;

}

.filter-id {
  top: -2px;
  display: flex;
  align-items: center;
}

.filter-status {
  position: relative;

  &__active {
    width: 8px;
    height: 8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    // background-color: $dark-blue;
    position: absolute;
    top: 0;
    right: -2px;
  }
}

.checkbox-container__wrapper {
  width: fit-content;
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
  //height: 18px;

  & > span {
    line-height: 1;
    white-space: nowrap;
    font-weight: 500;
  }

}

.table {
  border-collapse: separate;
  border-spacing: 0 10px;
  border: none;
  box-shadow: none;
  padding: 0 20px;
  min-height: 400px;
  border-radius: 8px;

  &__wrapper {
    width: 100%;
    background: #f8f9fb;
    border-radius: 20px;
    overflow-x: auto;
    scrollbar-color: #f2f3f5 #fff;
    scrollbar-width: thin;
  }

  thead, tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
  }

  &__body {
    display: block;
    // @include scrollbar;
  }

  &__head {
    width: calc(100% - 10px);
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    line-height: 24px;
    background: none;

    .table__th {
      border-right: 12px solid transparent;

    }

    position: sticky;
    top: 0;
    left: 20px;
    z-index: 8;
    background: #f8f9fb;
  }

  td {
    padding: 6px 8px;
    border-right: 12px solid transparent;

    background-color: transparent;
    border-color: transparent;
    box-shadow: none;
    font-size: 12px;
    font-weight: 500;
    height: 50px;
    line-height: 140%;
    min-height: 50px;
    min-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: all .2s ease-in-out;
    vertical-align: middle;
    width: auto;

    &.email {
      overflow: visible;
      cursor: default;
    }

    &.referall,
    &.link {
      white-space: nowrap;
    }
  }

  td:last-child {
    white-space: nowrap;

  }

  &__th {
    padding: 6px 8px;
    color: #a7a8b9;
    vertical-align: middle;
/*
    &:last-child {
      .checkbox-container__wrapper {
      }
    }
*/
    &.Id {
      background: #f8f9fb;
      z-index: 2;
      position: sticky;
      top: 0;
      left: 0;
      width: 100px;
    }
  }

  &__body {
    tr {
      cursor: pointer;

      display: table;
      table-layout: fixed;
      width: 100%;

      transition: all .2s ease;

      &.active {
        td:first-child {
          background: #ECEEF1;
        }
      }

      &:hover {
        td:first-child {
          background: var(--bs-white);
        }
      }

      td.table-item-id {
        background: #f8f9fb;
        z-index: 2;
        position: sticky;
        top: 0;
        left: 0;
        width: 100px;
      }
    }
  }

  tr {
    transition: all 0.2s ease;

    &.active {
      background: #ECEEF1;
      transition: all 0.2s ease;
    }

    p {
      // transition: all $transition;
      font-weight: 500;
      font-size: 12px;
      line-height: 120%;
      padding: 8px;
      border-radius: 8px;
      background-color: #ffffff;
      display: block;
      width: fit-content;
    }

    .new {
      color: #7749d9;
    }

    .approved {
      color: #007afc;
    }

    .waiting {
      color: #fe6b01;
    }

    .accepted {
      color: #22c56d;
    }

    .edit {
      color: #fe6b01;
    }

    .rejected {
      color: #e22446;
    }

    &:hover {
      td {
        //background-color: #ECEEF1;
        background-color: var(--bs-white);
      }

      .new {
        background-color: rgba(#7749d9, 0.05);
      }

      .approved {
        background-color: rgba(#007afc, 0.05);
      }

      .waiting {
        background-color: rgba(#fe6b01, 0.05);
      }

      .accepted {
        background-color: rgba(#22c56d, 0.05);
      }

      .edit {
        background-color: rgba(#fe6b01, 0.05);
      }

      .rejected {
        background-color: rgba(#e22446, 0.05);
      }
    }

    > :last-child {
      text-align: right;
    }
  }

  td {
    // transition: all $transition;
    //padding: 12px 10px;

    font-size: 12px;
    font-weight: 500;
    line-height: 140%;
    //min-height: 64px;
    min-height: 50px;
    //height: 64px;
    height: 50px;
    vertical-align: middle;
    //max-width: 20vw;
    //text-wrap: balance;
    overflow: hidden;
    text-overflow: ellipsis;
    border-color: transparent;
    background-color: transparent;
    box-shadow: none;
    width: auto;
    min-width: 100px;

    &:first-child {
      border-radius: 12px 0 0 12px;
    }

    &:last-child {
      text-align: left;
      border-radius: 0 12px 12px 0;
    }
  }

  &__skeleton-wrapper {
    &__row {
      padding: 16px 10px;
    }
  }
}

.kebab-menu {
  opacity: 0;
  pointer-events: none;
}

.table-menu {
  z-index: 8;
  position: absolute;
  top: calc(100% + 33px);
  right: 10px;
  width: 204px;

  background: #ffffff;
  display: flex;
  flex-direction: column;
  border-radius: 8px;
  box-shadow: 0px 5px 20px 0px rgba(111, 117, 135, 0.15);

  scrollbar-color: #f2f3f5 white;
  scrollbar-width: thin;
  max-height: 330px;
  overflow-y: scroll;

  p {
    font-size: 12px;
    font-weight: 600;
    text-align: start;
    width: 100%;
    margin-bottom: 0 !important;
    padding: 8px 8px 0 8px !important;
  }

  ul {
    padding: 8px;
    display: flex;
    flex-direction: column;

    label {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      padding: 6px 8px;
      cursor: pointer;
      border-radius: 6px;
      transition: 0.15s linear;
      font-size: 12px;
      font-weight: 600;

      &:hover {
        background-color: #f4f7f9;
      }

      span {
        white-space: nowrap;
      }
    }
  }

  &--container {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    position: relative;

    svg {
      cursor: pointer;
    }
  }
}

.settings {
  position: absolute;
  top: 15px;
  right: -5px;
  width: 10px;
  z-index: 8;
}

.table__th {
  cursor: move;
  width: 150px;
}

.table__head .sortable-chosen.sortable-ghost {
  transition: transform 0.5s, opacity 0.5s !important;
  background-color: #ECEEF1;
  border-radius: 10px;
}

table.table td{
  background-color: transparent;
  border-color: transparent;
  box-shadow: none;
  font-size: 12px;
  font-weight: 500;
  height: 50px;
  line-height: 140%;
  min-height: 50px;
  min-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: all .2s ease-in-out;
  vertical-align: middle;
  width: auto;
  padding: 6px 8px;
  border-right: 12px solid transparent;
}

</style>
