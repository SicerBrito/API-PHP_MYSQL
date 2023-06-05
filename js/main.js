document.getElementById('form').addEventListener('submit', function(e) {
    e.preventDefault();
    let name = document.getElementById('name').value;
    let email = document.getElementById('email').value;
    // Enviar solicitud POST para crear el usuario
    fetch('scripts/API.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ name: name, email: email })
    })
    .then(function(response) {
        // Manejar la respuesta del servidor
        if (response.ok) {
            console.log(response)

            return response.json();
        } else {
            throw new Error('Error en la solicitud');
        }
    })
    .then(function(data) {
        console.log(data);
    })
    .catch(function(error) {
        // Manejar errores de la solicitud
        console.log(error);
    });
});