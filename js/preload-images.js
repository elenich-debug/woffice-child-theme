// preload-images.js
function preloadImage(src) {
  const link = document.createElement('link');
  link.rel = 'preload';
  link.href = src;
  link.as = 'image';
  document.head.appendChild(link);
  console.log(`Предзагружено изображение: ${src}`);
}

function observeImages() {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach(mutation => {
      mutation.addedNodes.forEach(node => {
        if (node.tagName === 'IMG' && node.src) {
          preloadImage(node.src);
        }
      });
    });
  });

  observer.observe(document.body, { childList: true, subtree: true });
}

document.addEventListener('DOMContentLoaded', function () {
  // Предзагрузка изображений, уже присутствующих в DOM
  const images = document.querySelectorAll('img[src]');
  images.forEach(img => preloadImage(img.src));

  // Наблюдение за динамически добавляемыми изображениями
  observeImages();
});