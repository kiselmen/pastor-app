<template>
  <div class="people-container">
    <!-- :disabled="!selectedIdsAccept.length" -->
    <div class="form-group">
        <button 
          @click="openActionModal('accept')" 
          type="button" 
          class="btn btn-blue" 
        >
            Создать
        </button>
    </div>
    <!-- <Button
      :theme="'light-green'"
      class="btn-accepted__submit"
      @click="openActionModal('accept')"
    >
      Добавить нового
    </Button> -->

    <div ref="scrollContainer" class="virtual-scroll-table">
      <UiTable
        :head="tableHead"
        :column-visibility="columnVisibility"
        :all-selected="isAllSelected"
        :size="allApplications.length"
        :loading="loading"
        @update-head-order="updateHeadOrder"
        @update-sort="updateSortable"
        @toggle-all="checkAllSelected"
      >
        <tr
          v-for="(item, index) in allApplications"
          :key="`profile-container-table-item-${item.id}`"
          class="virtual-item"
        >
          <template v-for="header in tableHead">
            <td
              v-if="header.id === 'Id'"
              :key="header.id"
              class="table-item-id"
            >
              <div class="checkbox-container__wrapper">
                <CheckBox v-model="item.selected" />
                {{ item[header.id] }}
              </div>
            </td>

            <td v-else-if="header.id === 'email'" class="email">
              <div class="table-item-id--email">
                <span :title="item[header.id]">
                  {{ item[header.id] }}
                </span>
                <div class="email-action-container">
                  <div class="email-action">
                    <IconMenuKebab />
                  </div>

                  <div class="email-menu--wrapper">
                    <div class="email-menu">
                      <span v-if="!emailLoading" class="item" @click.stop="handleUpdateSubscribeEmail(item.Id)">
                        Отправить повторно
                      </span>
                      <IconLoader v-if="emailLoading" :color="'#292c33'" />
                    </div>
                  </div>
                </div>
              </div>
            </td>
            <td v-else-if="header.id === 'Category'">
              <span>
                {{ formatCategory(item[header.id], language) }}
              </span>
            </td>
            <td v-else-if="header.id === 'ApplicationStatus'">
              <span>
                {{ formatStatus(item[header.id], 'ru') }}
              </span>
            </td>
            <td v-else-if="header.id === 'birthday_date' || header.id === 'baptism_date' || header.id === 'death_date'">
              <span>
                {{ formatDate(item[header.id], 'ru') }}
              </span>
            </td>
            <!-- <td v-else-if="header.id === 'Printed'">
              <span>
                {{ formatPrinted(item[header.id], 'ru') }}
              </span>
            </td>
            <td v-else-if="header.id === 'Briefcase'">
              <span>
                {{ formatBriefcase(item[header.id], 'ru') }}
              </span>
            </td> -->
            <td v-else-if="header.id === 'Actions'" class="action-column">
              <Button
                :theme="'light-green'"
                class="icon icon--green"
                :disabled="item.status == 1 || item.status == -1"
                @click="onCompleteSubmit([item.Id], 'accept')"
              >
                <IconLoader v-if="false" :color="'#292c33'" />
                <IconCheck v-else />
              </Button>
              <Button
                :theme="'light-red'"
                class="icon icon--red"
                :disabled="item.status == -2 || item.status == -1"
                @click="onCompleteSubmit([item.Id], 'decline')"
              >
                <IconLoader v-if="false" :color="'#292c33'" />
                <IconX v-else />
              </Button>
            </td>
            <td v-else>
              <span>
                {{ item[header.id] ? item[header.id] : '-' }}
              </span>
            </td>
          </template>
        </tr>
      </UiTable>
    </div>
    <!-- <div class="profile-container__footer">
      <UiPagination
        :total-pages="pageState.totalPages"
        :per-page="params.size"
        :active-page="params.page"
        @change-page="(e) => (params.page = e)"
      />
    </div> -->
    <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <AddPersone
          @toggle-modal="isModalAction = false"
          :action="action"
      />
    </ModalWrapper>
</div>
</template>

<script setup>
  import { ref, computed, watch, onMounted } from 'vue';
  import { usePeopleStore } from "@/stores/peopleStore";
  import UiTable from '@/components/table/Table.vue';
  import CheckBox from '@/components/ui/CheckBox.vue';
  import IconMenuKebab from '@/components/icons/IconMenuKebab.vue';
  import IconLoader from '@/components/icons/IconLoader.vue';
  import IconX from '@/components/icons/IconX.vue';
  import IconCheck from '@/components/icons/IconCheck.vue';
  import Button from '@/components/ui/Button.vue';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  // import QuestionModal from '@/components/people/QuestionModal.vue'
  import AddPersone from '@/components/people/AddPersone.vue';

  const peopleStore = usePeopleStore();
  const allApplications = ref([]);
  const params = ref({
    size: 10,
    page: 0,
    sort_value: 0,
    sorting: 'DESC',
    searchValue: ''
  });
  const tableHead = ref([
    { id: 'id', label: 'ID', type: 'checkbox', filterable: true, sort: 'DESC', sort_value: 0 },
    { id: 'first_name', label: 'Имя', filterable: true, sort: 'DESC', sort_value: 1 },
    { id: 'name', label: 'Фамилия', filterable: true, sort: 'DESC', sort_value: 2 },
    { id: 'patronymic', label: 'Отчество', filterable: true, sort: 'DESC', sort_value: 3 },
    { id: 'email', label: 'Почта', filterable: true, sort: 'DESC', sort_value: 4 },
    { id: 'birthday_date', label: 'Дата рождения', filterable: true, sort: 'DESC', sort_value: 5 },
    { id: 'baptism_date', label: 'Дата крещения', filterable: true, sort: 'DESC', sort_value: 6 },
    { id: 'death_date', label: 'Дата смерти', filterable: true, sort: 'DESC', sort_value: 7 },
    { id: 'live_addres', label: 'Адрес', filterable: true, sort: 'DESC', sort_value: 8 },
    { id: 'home_phone', label: 'Дом.тел.', filterable: true, sort: 'DESC', sort_value: 9 },
    { id: 'mobile_phone', label: 'Моб.тел.', filterable: true, sort: 'DESC', sort_value: 9 },
  ]);
  const columnVisibility = ref([
    { canViewid: true },
    { canViewfirst_name: true },
    { canViewOrganization: true },
    { canViewJob: true },
    { canVieweMail: true },
    { canViewCategory: true },
    { canViewApplicationStatus: true },
    { canViewDate: true },
    { canViewEmployee: true },
    { canViewReferral: true },
    { canViewLink: true },
    { canViewPrinted: true },
    { canViewNameBagde: true },
    { canViewLastNameBadge: true },
    { canViewBriefcase: true },
    { canViewActions: true }
  ]);

  const loading = ref(false);

  onMounted(async () => {
    await peopleStore.getAllPeople();
    allApplications.value = peopleStore.peoples;
    console.log('peopleStore.peoples ' , peopleStore.peoples);
  });

  const search = ref('');

  // Вычисляемое свойство для проверки выбраны ли все элементы
  const isAllSelected = computed(() =>
    allApplications.value.every(app => app.selected)
  );

  const formatDate = (dateString, language) => {
    const locale = language === 'ru' ? 'ru-RU' : 'en-US' // Specify the locale based on the language
    const options = {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      // hour: '2-digit',
      // minute: '2-digit'
    }
    return new Date(dateString).toLocaleString(locale, options).replace(',', '/');
  };

  // methods
  const updateHeadOrder = (newOrder) => {
    tableHead.value = newOrder;
    localStorage.setItem('tableHeaders', JSON.stringify(newOrder));
  };

  const clearInput = () => {
    search.value = '';
    doSearch();
  };

  const debounceTimer = ref(null);

  const doSearch = () => {
    params.value.searchValue = search.value;
    params.value.page = 0;
  };

  const handleSearchInput = () => {
    if (debounceTimer.value !== null) {
      clearTimeout(debounceTimer.value);
    }
    debounceTimer.value = setTimeout(() => {
      params.value.page = 0;
      doSearch()
    }, 500);
  };

  const selectedItem = ref(0);
  const selectedValue = computed(() => {
    return countPerPage[selectedItem.value].name.ru;
  })

  const handleCountPerPage = (item) => {
    console.log('Выбрано кол-во строк:', item.val);
    params.value.size = Number(item.val);
    params.value.page = 0;
    selectedItem.value = countPerPage.findIndex(count => count.val === item.val);
  }
  const updateSortable = ({ direction, value }) => {
    params.value.sorting = direction;
    params.value.sort_value = value;
  }

  const checkAllSelected = (newValue) => {
    allApplications.value = allApplications.value.map(app => ({
      ...app,
      selected: newValue
    }));
  }

  const emailLoading = ref(false)
  const handleUpdateSubscribeEmail = async (id) => {
    try {
      emailLoading.value = true;
      await updateSubscribeEmail(id);
      const notification = {
        id: Date.now(),
        message: 'Письмо отправлено',
        type: 'warning'
      };
      appStore.addNotification(notification);
    } catch (error) {
      console.log(error);
    } finally {
      emailLoading.value = false;
    }
  };

  const isModalAction = ref(false);
  const modalLoading = ref(false);
  const action = ref('');

  const openActionModal = (actionValue) => {
    action.value = actionValue;
    isModalAction.value = true;
  };
  const multiplyAction = async (items, action) => {
    const data = items.map(item => item.Id);
    const res = await onCompleteSubmit(data, action);
    console.log(res);
    isModalAction.value = false;
  }

  watch(params, (newParams) => {
    peopleStore.getAllPeople();
    // getRequest(newParams)
  }, { deep: true });

  // onMounted(async () => {
  //   await getRequest(params.value)
  //   const savedHeaders = localStorage.getItem('tableHeaders')
  //   if (savedHeaders) {
  //     tableHead.value = JSON.parse(savedHeaders)
  //   }
  // })

</script>

<style lang="scss" scoped>
  @import '@/assets/styles/btn.scss';

  .form {
    &-group{
      margin-bottom: 25px;
      position: relative;
    }
  }

  .profile-wrapper{
      background: var(--bs-white);
      padding: toRem(20px);
      border-radius: toRem(16px);
  }
  .people-container {
      position: relative;
      margin: 20px;
  }
  .profile-container__header{
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      margin-bottom: 24px;
  }
  .profile-container__additional {
      align-items: center;
      display: flex;
      flex-direction: column-reverse;
      flex-grow: 1;
      gap: 20px;
      justify-content: space-between;
      margin-top: 24px;
  }
  .profile-container__additional__left {
      align-items: center;
      background-color: #fff;
      display: flex;
      gap: 8px;
      justify-content: flex-start;
      padding: 20px 0;
      position: sticky;
      top: 0;
      width: 100%;
      z-index: 9;
  }
  .profile-container__additional__input{
      width: 100%;
      display: flex;
      align-items: center;
      border-radius: 16px;
      background: #f5f5f5;
      padding: 0 12px;
  }
  .subtitle {
      align-items: center;
      display: flex;
      font-size: 24px;
      font-weight: 600;
      line-height: 1.4;
      span{
          color: #9ba3b2;
          font-size: 14px;
          font-weight: 600;
          margin-left: 15px;
      }
  }
  .table-cont{
      position: relative;
  }
  .input--cross{
      cursor: pointer;
  }
  .btn-accepted__submit{
      padding: 10px 20px;
      font-size: 14px;
      font-weight: 600;
      height: toRem(40px);
  }

  .profile-container__additional__left {
      align-items: center;
      background-color: #fff;
      display: flex;
      gap: 8px;
      justify-content: flex-start;
      padding: 20px 0;
      position: sticky;
      top: 0;
      width: 100%;
      z-index: 9;
  }
  .profile-container__additional__right--wrapper {
      align-items: center;
      align-self: flex-end;
      display: flex;
      gap: 8px;
      margin-left: auto;
  }
  .profile-container__additional__left--left, .profile-container__additional__left--right {
      align-items: center;
      align-self: center;
      display: flex;
      gap: 8px;
  }
  .dropdown__item {
      min-width: 188px;
      align-items: center;
      color: #292c33;
      cursor: pointer;
      display: flex;
      font-size: 14px;
      font-weight: 500;
      gap: 12px;
      line-height: 140%;
      padding: 6px 8px;
      -webkit-text-decoration: none;
      text-decoration: none;
      transition: all .2s ease;
      white-space: nowrap;
  }
  .dropdown__item:hover {
      background-color: #eceef1;
  }
  .btn__add-person {
      flex-shrink: 0;
      height: 40px;
  }

  .btn__more-container {
      position: relative;
  }
  .btn__more{
      align-items: center;
      border-radius: 12px;
      display: flex;
      height: 40px;
      justify-content: center;
      width: 40px;
      background-color: #f5f5f5;
      color: #1B1C2E;
      // transition: $transition;
      &:hover{
          background: #f4f7f9;
          filter: brightness(.9);
      }
  }
  .btn__more-dropdown {
      bottom: 0;
      opacity: 0;
      padding-top: 12px;
      position: absolute;
      right: 0;
      transform: translateY(100%);
      transition: all .2s ease-in-out;
      visibility: hidden;
  }
  .btn__more-dropdown-inner {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 5px 20px 0 rgba(111,117,135,.15);
      display: flex;
      flex-direction: column;
      padding: 4px 0;
  }
  .dropdown__item {
      align-items: center;
      color: #292c33;
      cursor: pointer;
      display: flex;
      font-size: 14px;
      font-weight: 500;
      gap: 12px;
      line-height: 140%;
      padding: 6px 8px;
      -webkit-text-decoration: none;
      text-decoration: none;
      transition: all .2s ease;
      white-space: nowrap;
      svg{
          height: auto;
          width: 20px;
      }
      &:hover{
          background-color: #eceef1;
      }
  }
  .btn__more-container:hover .btn__more-dropdown{
      opacity: 1;
      visibility: visible;
  }

  .dropdown-selection__item {
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      line-height: 140%;
      padding: 6px 8px;
      transition: all .2s ease;
      white-space: nowrap;
      &:hover{
          background-color: #eceef1;
      }
  }
  .table-item-id--email{
      align-items: center;
      display: flex;
      gap: 10px;
      justify-content: flex-start;
      position: relative;
      width: auto;
      & > span{
          max-width: 70%;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
      }
  }
  .email-action-container {
      align-items: center;
      display: flex;
      justify-content: center;
      padding: 0 5px;
  }
  .email-menu {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 5px 20px 0 rgba(111,117,135,.15);
      cursor: pointer;
      display: flex;
      flex-direction: column;
      min-width: 140px;
      padding: 10px 8px;
      white-space: nowrap;
      svg {
          width: 17px;
          height: 17px;
      }
      &:hover{
          background-color: #f4f7f9;
      }
  }
  .email-menu--wrapper {
      bottom: -8px;
      left: calc(100% - 50px);
      opacity: 0;
      padding-bottom: 8px;
      position: absolute;
      transform: translate(0%, 100%);
      transition: all 0.2s ease-in-out;
      visibility: hidden;
      z-index: 3;
  }
  .email-action-container:hover .email-menu--wrapper {
      opacity: 1;
      visibility: visible;
  }
  .action-column{
      align-items: center;
      display: flex;
      gap: 10px;
      .btn{
          height: auto;
      }
  }

</style>