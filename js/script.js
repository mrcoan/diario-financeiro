// AOS JS
AOS.init({
  once: true
});

// Swiper JS
const swiper = new Swiper('.swiper', {
  loop: true,
  slidesPerView: 1.1,        // no celular, o próximo banner aparece na ponta
  spaceBetween: 20,
  autoplay: { delay: 5000, pauseOnMouseEnter: true },
  pagination: { el: '.swiper-pagination', clickable: true },

  // Navigation arrows
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
});

// Toast
document.addEventListener("DOMContentLoaded", () => {
  const toast = document.getElementById("mensagem");

  // Verifica se o PHP realmente renderizou o elemento na tela
  if (toast) {
    // Mostra o toast removendo a classe 'hidden'
    toast.classList.remove("hidden");

    // Esconde o toast após 3 segundos
    setTimeout(() => {
      toast.classList.add("hidden");
    }, 5000);
  }
});

// Mostrar Senha

function toggleSenha(idInput, botao) {
  const inputField = document.getElementById(idInput);

  if (inputField.type === 'password') {
    inputField.type = 'text';
  } else {
    inputField.type = 'password';
  }
}