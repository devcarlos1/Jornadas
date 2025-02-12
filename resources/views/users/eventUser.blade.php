<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<nav class="bg-gray-800 p-4">
    <ul class="flex space-x-4">
        <li>
            <a href="/users/eventList" class="text-white hover:text-gray-400">Event List</a>
        </li>
        <li>
            <a href="/users/eventUser" class="text-white hover:text-gray-400">My Events</a>
        </li>
        <li>
            <form action="{{ 'logout' }}"method="POST" class="inline">
                @csrf
                <button type="submit" class="text-white hover:text-gray-400">Logout</button>
            </form>
        </li>
    </ul>
</nav>

    <h2 class="text-4xl font-bold text-center my-8 text-blue-600">Events List Registration</h2>
    <ul id="eventsListRegistration" class="pl-5 text-gray-600 space-y-4 w-[50%] my-0 mx-auto"></ul>

    <script>
        let currentPage = 1; // Para manejar la paginación
        document.addEventListener('DOMContentLoaded', function () {
            GetEventsRegistration();
        });

function changeType(selectElement) {
    // Encuentra el formulario más cercano al select
    let formPay = selectElement.nextElementSibling;
    // Encuentra el input dentro de ese mismo formulario
    let input = formPay.querySelector("input[id='typeValue']");
    let inputAmount = formPay.querySelector("input[id='amount']");

    let formFree = selectElement.nextElementSibling.nextElementSibling;
    // Obtiene el valor directamente desde el select pasado como referencia
    let valorSeleccionado = selectElement.value;
    // Actualiza el input con el valor seleccionado

    const amount= selectElement.previousSibling.previousSibling.title
    switch (valorSeleccionado) {
        case 'Presencial': 
            input.value = valorSeleccionado;
            inputAmount.value= Number(amount);
            selectElement.previousSibling.previousSibling.textContent=Number(amount);
            formPay.style.display="block";
            formFree.style.display="none"
            break;
    
        case 'Virtual': 
            input.value = valorSeleccionado;
            inputAmount.value= Number(amount) - 10;
            selectElement.previousSibling.previousSibling.textContent = Number(amount) - 10;
            formPay.style.display="block";
            formFree.style.display="none"

            break;
        case 'Gratuito': 
            input.value = 0;
            inputAmount.value= 0;
            selectElement.previousSibling.previousSibling.textContent=0;
            formPay.style.display="none";
            formFree.style.display="block"
            break;
        default:
            break;
    }
    
    // Muestra el valor en la consola
}

 function gratisApi (e){
e.preventDefault();
    const instance = axios.create({
    baseURL: "http://127.0.0.1:8000/api",
    timeout: 1000,
    withCredentials: true
});

instance.post("/free/registration").then(response => {
    console.log("Pago realizado:", response.data);
})
.catch(error => console.error("Error en el pago:", error));
};

function DeletePay(events){
    let conferenciaCount = 0;
    let tallerCount = 0;

    events.forEach(event => {
        if (event.type === 'Presencial') {
            conferenciaCount++;
        } else if (event.type === 'Virtual') {
            tallerCount++;
        }
    });
    events.forEach(event => {
        if (conferenciaCount >= 5) {
            event.getElementById('presencial').remove();
        }

        if (tallerCount >= 4) {
            event.getElementById('virtual').remove();
        }
    });
}

        function GetEventsRegistration() {
            axios.get(`/api/user/registration`)
                .then(response => {
                    let events = response.data.events.data;
                    let list = document.getElementById('eventsListRegistration');
                    events.forEach(event => {
                        let li = document.createElement('li');
                        li.innerHTML = `
    <div class="p-4 mb-4 bg-white rounded-lg shadow-md border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">${event.title}</h3>
        <p class="text-sm text-gray-600">${event.start_time}</p>
        <p class="text-sm text-gray-600">
            Speaker: <span class="font-medium">${event.speaker.name}</span>
        </p>
        <p class="text-sm text-gray-600">
            <span title="${event.amount}" class="font-semibold">${event.amount}</span>
        </p>
    </div>
`;
               
                        list.appendChild(li);
                    });
                    DeletePay(events);
                    currentPage++; // Incrementar la página para la siguiente carga
                })
                .catch(error => console.error(error));
        }




        </script>
</body>
</html>
