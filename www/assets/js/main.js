// Ерөнхий тайлбар: одоогоор нэмэлт скрипт шаардлагагүй хуудсуудад
// сайтын нийтлэг жижиг харилцан үйлдлүүдийг энд нэмнэ.
document.addEventListener('DOMContentLoaded', function () {
  var alerts = document.querySelectorAll('.alert-success');
  alerts.forEach(function (el) {
    setTimeout(function () { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; }, 4000);
  });
});
