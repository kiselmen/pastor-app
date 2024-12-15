const createURL = (key, value, viewStore) => {
  let url = '';
  viewStore.allowFilterData.forEach(item => {
    if (item.name === key) {
      if (value) url = url + '&_' + key + '=' + value;
    } else {
      const storeKey = item.name + 'FilterMask';
      const storeValue = viewStore[storeKey];
      if (storeValue) url = url + '&_' + item.name + '=' + storeValue;
    }
  });
  if (url) url = '?' + url.substring(1);
  return url;
};

export {
  createURL,
}

