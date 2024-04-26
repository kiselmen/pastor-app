function getImgPath(path) {
  const imgPath = import.meta.env.VITE_APP_IMAGES + path;
  return imgPath;
};

export default getImgPath;