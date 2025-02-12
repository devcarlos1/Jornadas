<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

    <h2>Events List</h2>
    <ul id="eventsList"></ul>
    <h2>Events List Registration</h2>
    <ul id="eventsListRegistration"></ul>

    <script>
        let currentPage = 1; // Para manejar la paginación
        document.addEventListener('DOMContentLoaded', function () {
            loadEvents(); // Cargar lista de eventos
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
        function loadEvents() {
            axios.get(`/api/events/eventsList`)
                .then(response => {
                    let events = response.data.data;
                    let list = document.getElementById('eventsList');
                    events.forEach(event => {
                        if(event.total_attendees === event.max_attendees){
                            let li = document.createElement('li');
                        li.innerHTML = `${event.title} - ${event.start_time} 
                            (Speaker: ${event.speaker.name}) - <span title= "${event.amount}">${event.amount}</span> - No Disponible`;    
                            list.appendChild(li);
  
                        }else{
                            let li = document.createElement('li');
                        const type = `<select name="type" id="type" onchange="changeType(this)">
    <option value="Presencial" id="presencial">Presencial</option>
    <option value="Virtual" id="virtual">Virtual</option>
    <option value="Gratuito" id="gratuito">Gratuito</option>
</select>`;
                        li.innerHTML = `${event.title} - ${event.start_time} 
                            (Speaker: ${event.speaker.name}) - <span title= "${event.amount}">${event.amount}</span> - ${type} - Disponible 
                            <form action="{{ route('paypal.pay') }}" method="POST"  onsubmit="setAmount(${event.amount})">
    @csrf
        <input type="hidden" name="eventid" value=${event.id}> 
        <input type="hidden" name="type" id='typeValue'  > 
    <input type="hidden" name="amount" id="amount" value=${event.amount} > 
    <button type="submit">Pagar</button>
</form>  <form action="http://127.0.0.1:8000/api/free/registration" method="POST" style="display:none">
    @csrf
        <input type="hidden" name="eventid" value=${event.id}> 
       <button type="submit">Inscribete</button>
</form>
`;      
list.appendChild(li);

                        }                 
                    });
                    currentPage++; // Incrementar la página para la siguiente carga
                })
                .catch(error => console.error(error));
        }


        function GetEventsRegistration() {
            axios.get(`/api/user/registration`)
                .then(response => {
                    let events = response.data.events.data;
                    let list = document.getElementById('eventsListRegistration');
                    events.forEach(event => {
                        let li = document.createElement('li');
                        li.innerHTML = `${event.title} - ${event.start_time} 
                            (Speaker: ${event.speaker.name}) - <span title= "${event.amount}">${event.amount}</span> 
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
