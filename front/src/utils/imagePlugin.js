function getImgPath(path) {
  if (path) {
    const imgPath = import.meta.env.VITE_APP_IMAGES + path;
    return imgPath;
  } else {
    const imgPath = import.meta.env.VITE_APP_IMAGES + 'img/nofoto.png';
    return imgPath;
  }
};

export default getImgPath;