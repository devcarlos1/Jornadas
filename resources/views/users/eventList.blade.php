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
            <form action="{{ 'logout' }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-white hover:text-gray-400">Logout</button>
            </form>
        </li>
    </ul>
</nav>

    <h2 class="text-4xl font-bold text-center my-8 text-blue-600">Conference List</h2>
    <ul id="eventsConference" class="pl-5 text-gray-600 space-y-4 w-[50%] my-0 mx-auto"></ul>
    <h2 class="text-4xl font-bold text-center my-8 text-blue-600">Workshop List</h2>
    <ul id="eventsWorkshop" class="pl-5 text-gray-600 space-y-4 w-[50%] my-0 mx-auto"></ul>

    <script>
        let currentPage = 1; // Para manejar la paginación
        document.addEventListener('DOMContentLoaded', function () {
            loadEvents();
        });

function changeType(selectElement) {
    // Encuentra el formulario más cercano al select
    let formPay = selectElement.parentNode.nextElementSibling;
    // Encuentra el input dentro de ese mismo formulario
    let input = formPay.querySelector("input[id='typeValue']");
    let inputAmount = formPay.querySelector("input[id='amount']");

    let formFree = selectElement.parentNode.nextElementSibling.nextElementSibling;
    // Obtiene el valor directamente desde el select pasado como referencia
    let valorSeleccionado = selectElement.value;
    // Actualiza el input con el valor seleccionado
    console.log(input);
    console.log(inputAmount);
    console.log(formFree);
    console.log(valorSeleccionado);
    const amount= selectElement.previousSibling.previousSibling.title;
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
            inputAmount.value= Number(amount) /2;
            selectElement.previousSibling.previousSibling.textContent = Number(amount)/2;
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

function loadEvents() {
            axios.get(`/api/events/eventsList`)
                .then(response => {
                    let events = response.data.data;
                    let listCon = document.getElementById('eventsConference');
                    let listWork = document.getElementById('eventsWorkshop');

                    events.forEach(event => {
                        if(event.total_attendees === event.max_attendees){
                            let li = document.createElement('li');
                        li.innerHTML = li.innerHTML = `
    <div class="p-4 bg-white rounded-lg shadow-md flex flex-col gap-2">
        <h3 class="text-lg font-bold text-gray-800">${event.title}</h3>
        <p class="text-sm text-gray-600">Inicio: ${event.start_time}</p>
        <p class="text-sm text-gray-600">Inicio: ${event.type}</p>
        <p class="text-sm text-gray-600">Ponente: <span class="font-semibold">${event.speaker.name}</span></p>
        <p class="text-sm text-gray-600">
            <span class="font-semibold" title="${event.amount}">Cupo: ${event.amount}</span> - 
            <span class="text-red-500 font-bold">No Disponible</span>
        </p>
    </div>
`;
                            list.appendChild(li);
  
                        }else{
                            let li = document.createElement('li');
                            const type =`
    <select name="type" id="type" onchange="changeType(this)" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="Presencial" id="presencial" class="text-gray-700">Presencial</option>
        <option value="Virtual" id="virtual" class="text-gray-700">Virtual</option>
        <option value="Gratuito" id="gratuito" class="text-gray-700">Gratuito</option>
    </select>
`;
                        li.innerHTML =  `
    <div class="p-4 mb-4 bg-white rounded-lg shadow-md border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">${event.title}</h3>
        <p class="text-sm text-gray-600">${event.start_time}</p>
        <p class="text-sm text-gray-600">Speaker: <span class="font-medium">${event.speaker.name}</span></p>
        <p class="text-sm text-gray-600">
            <span title="${event.amount}" class="font-semibold">${event.amount}$</span> ${type} 
            <span class="text-green-600 font-bold">Disponible</span>
        </p>

        <form action="{{ route('paypal.pay') }}" method="POST" class="mt-2" onsubmit="setAmount(${event.amount})">
            @csrf
            <input type="hidden" name="eventid" value="${event.id}"> 
            <input type="hidden" name="type" id='typeValue' value="Presencial"> 
            <input type="hidden" name="amount" id="amount" value="${event.amount}"> 
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-300">
                Pagar
            </button>
        </form>  

        <form action="http://127.0.0.1:8000/api/free/registration" method="POST" style="display:none" class="mt-2">
            @csrf
            <input type="hidden" name="eventid" value="${event.id}"> 
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700 transition duration-300">
                Inscribete
            </button>
        </form>
    </div>
`;    

                        
                        if(event.type === "conferencia"){
                            listCon.appendChild(li);
         }else if(event.type === "taller"){
            listWork.appendChild(li);
         }
                        }                 
                    });
                    currentPage++; // Incrementar la página para la siguiente carga
                })
                .catch(error => console.error(error));
        }
      


        </script>
</body>
</html>
