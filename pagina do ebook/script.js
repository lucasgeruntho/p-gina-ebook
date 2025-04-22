// Verifica se o usuário já preencheu o formulário
if (localStorage.getItem("leadCadastrado") === "sim") {
  document.getElementById("popup-overlay").style.display = "none";
  document.getElementById("conteudo-site").style.display = "block";
  document.body.style.overflow = "auto";
} else {
  // Mostra o pop-up depois de 15 segundos
  setTimeout(() => {
    document.getElementById("popup-overlay").style.display = "flex";
  }, 15000);
}

// Máscara automática para WhatsApp
const inputWhats = document.getElementById("whatsapp");
inputWhats.addEventListener("input", function () {
  let valor = inputWhats.value.replace(/\D/g, '');

  if (valor.length > 11) valor = valor.slice(0, 11);

  let formatado = "";

  if (valor.length > 0) formatado += "(" + valor.substring(0, 2) + ")";
  if (valor.length > 2 && valor.length <= 7)
    formatado += " " + valor.substring(2);
  else if (valor.length > 7)
    formatado += " " + valor.substring(2, 7) + "-" + valor.substring(7);

  inputWhats.value = formatado;
});

// Enviar dados via fetch sem recarregar a página
document.getElementById("lead-form").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("salvar.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.text())
    .then(data => {
      if (data === "sucesso" || data === "ja_cadastrado") {
        localStorage.setItem("leadCadastrado", "sim");

        document.getElementById("success-message").style.display = "block";

        setTimeout(() => {
          document.getElementById("popup-overlay").style.display = "none";
          document.getElementById("conteudo-site").style.display = "block";
          document.body.style.overflow = "auto";
        }, 2000);
      } else {
        alert("Ocorreu um erro: " + data);
      }
    })
    .catch(err => {
      alert("Erro ao enviar dados.");
      console.error(err);
    });
});
