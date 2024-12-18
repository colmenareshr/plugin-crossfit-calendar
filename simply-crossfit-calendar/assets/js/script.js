// assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
  // Aquí puedes agregar la lógica para interactuar con la API REST
  console.log('Simply Crossfit Calendar script loaded.');

  // Ejemplo de cómo hacer una solicitud GET a la API REST
  fetch(sccData.apiUrl)
      .then(response => response.json())
      .then(data => {
          console.log('Entrenamientos:', data);
          // Aquí puedes manipular el DOM para mostrar los entrenamientos
      })
      .catch(error => {
          console.error('Error al obtener los entrenamientos:', error);
      });
});