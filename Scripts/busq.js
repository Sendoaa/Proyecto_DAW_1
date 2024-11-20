// Función para restablecer los filtros de radio y checkbox
function resetFilters() {
  const radios = document.querySelectorAll('input[name="ordenar"]');
  radios.forEach((radio) => {
    radio.checked = false;
  });

  const checkboxes = document.querySelectorAll('input[name="discapacidad[]"]');
  checkboxes.forEach((checkbox) => {
    checkbox.checked = false;
  });
}

// Configura el botón para limpiar los filtros
document.addEventListener("DOMContentLoaded", function () {
  const clearButton = document.getElementById("borrar-todo");
  clearButton.addEventListener("click", function (event) {
    event.preventDefault();
    resetFilters();
  });
});

// Al cargar la página, si la fecha no está en localStorage, la establece
window.onload = function () {
  const isDateSet = localStorage.getItem("dateSet");
  if (!isDateSet) {
    setTodayDate();
    localStorage.setItem("dateSet", "true");
  }
};

// Guarda el ID del viaje para utilizarlo al abrir el modal
let viajeId;

function openModal(id) {
    viajeId = id; // Guarda el ID del viaje a comprar
    document.getElementById("confirmModal").style.display = "block"; // Muestra el modal
}

function closeModal() {
    document.getElementById("confirmModal").style.display = "none"; // Oculta el modal
}

// Al confirmar la compra, muestra un overlay y envía los datos del viaje y usuario
document.addEventListener("DOMContentLoaded", function () {
  const confirmButton = document.getElementById("confirmButton");
  const loadingOverlay = document.getElementById("loadingOverlay");
  const spinner = document.querySelector(".spinner");
  const checkmark = document.querySelector(".checkmark");
  const xmark = document.querySelector(".xmark"); // Referencia a la X
  const loadingText = document.getElementById("loadingText");

  confirmButton.addEventListener("click", function () {
      if (viajeId && userId) {
          loadingOverlay.style.display = "flex";
          spinner.style.display = "block";
          checkmark.style.display = "none";
          xmark.style.display = "none";
          loadingText.textContent = "Comprando...";

          // Simula un tiempo de espera antes de enviar los datos
          setTimeout(() => {
              fetch("ManejoDatos/cambiar_estado.php", {
                  method: "POST",
                  headers: {
                      "Content-Type": "application/x-www-form-urlencoded",
                  },
                  body: `id=${viajeId}&userId=${userId}`, // Envía el ID del viaje y el userId
              })
              .then((response) => response.text())
              .then((data) => {
                  console.log(data); 
                  if (data.includes("El conductor no puede comprar su propio viaje.")) {
                      // Si el conductor intenta comprar su propio viaje, muestra la "X" y un mensaje de error
                      spinner.style.display = "none";
                      loadingText.textContent = "No puedes comprar tu propio viaje";
                      xmark.style.display = "block";

                      // Oculta el overlay después de un momento
                      setTimeout(() => {
                          loadingOverlay.style.display = "none";
                      }, 1500); 
                  } else {
                      // Compra exitosa: muestra el checkmark y recarga la página
                      spinner.style.display = "none";
                      loadingText.textContent = "Compra completada";
                      checkmark.style.display = "block";

                      setTimeout(() => {
                          loadingOverlay.style.display = "none";
                          location.reload();
                      }, 500);
                  }
              })
              .catch((error) => {
                  console.error("Error:", error);
                  spinner.style.display = "none";
                  loadingText.textContent = "Error al completar la compra";
                  xmark.style.display = "block";
              });
          }, 500);
      } else {
            alert("Ez zaude saioa hasita");
      }
  });
});

// Mostrar/ocultar los filtros al hacer clic en el botón de filtros
document.addEventListener("DOMContentLoaded", function () {
  const filtros = document.getElementById("filtros");
  const btnFiltros = document.querySelector(".btn_filtros");
  const sectionBusq = document.querySelector(".section_busq");

  btnFiltros.addEventListener("click", function () {
      filtros.style.display = filtros.style.display === "none" ? "block" : "none";
  });
});
