<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<nav class="bg-gray-800 p-4">
    <ul class="flex space-x-4">
        <li>
            <a href="{{ url('/admin/speakers') }}" class="text-white hover:text-gray-400">Speakers</a>
        </li>
        <li>
            <a href="{{ url('/admin/event') }}" class="text-white hover:text-gray-400">Events</a>
        </li>
        <li>
            <form action="{{ 'logout' }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-white hover:text-gray-400">Logout</button>
            </form>
        </li>
    </ul>
</nav>
<h1 class="text-4xl font-bold text-center my-8 text-blue-600">Events Management</h1>

<div class="w-[50%] my-0 mx-auto">
    <!-- Formulario para agregar un nuevo evento -->
<h2 class="text-2xl font-semibold text-gray-700 mb-4">Add New Event</h2>
<form id="eventForm" class="bg-white shadow-md rounded-lg p-6 mb-8">
    <div class="mb-4">
        <input 
            type="text" 
            id="title" 
            placeholder="Title" 
            required 
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>
    <div class="mb-4">
        <select 
            id="type" 
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="taller">Taller</option>
            <option value="conferencia">Conferencia</option>
        </select>
    </div>
    <div class="mb-4">
        <select 
            id="speakersList" 
            required 
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        ></select>
    </div>
    <div class="mb-4">
        <input 
            type="number" 
            id="max" 
            placeholder="Max Attendees" 
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>
    <div class="mb-4">
        <input 
            type="number" 
            id="amount" 
            placeholder="Amount" 
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>
    <div class="mb-4">
        <input 
            type="datetime-local" 
            id="start_time" 
            required 
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>
    <div class="mb-4">
        <input 
            type="datetime-local" 
            id="end_time" 
            required 
            readonly 
            class="w-full px-3 py-2 border rounded-lg bg-gray-100"
        >
    </div>
    <button 
        type="submit" 
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline"
    >
        Add Event
    </button>
</form>
</div>

<h2 class="text-2xl font-semibold mt-8 mb-4 text-gray-700">Events List</h2>
<ul id="eventsList" class="pl-5 text-gray-600 space-y-4"></ul>


    <script>
        let currentPage = 1; // Para manejar la paginación
        document.addEventListener('DOMContentLoaded', function () {
            loadSpeakers(); // Cargar speakers en el dropdown
            loadEvents(); // Cargar lista de eventos

            
            document.getElementById('eventForm').addEventListener('submit', function (event) {
                event.preventDefault();
                addEvent();
            });
        });


        document.getElementById("start_time").addEventListener("change", function() {
        let startTime = new Date(this.value); // Obtener la fecha y hora del input
        if (!isNaN(startTime.getTime())) {  // Verificar que la fecha es válida
            startTime.setMinutes(startTime.getMinutes() + 55); // Sumar 55 minutos

   
            // Ajustar manualmente la fecha sin convertir a UTC
            let year = startTime.getFullYear();
            let month = String(startTime.getMonth() + 1).padStart(2, '0'); // Mes en 2 dígitos
            let day = String(startTime.getDate()).padStart(2, '0');
            let hours = String(startTime.getHours()).padStart(2, '0');
            let minutes = String(startTime.getMinutes()).padStart(2, '0');

            let endTimeFormatted = `${year}-${month}-${day}T${hours}:${minutes}`; // Formato correcto
            
            document.getElementById("end_time").value = endTimeFormatted; // Asignar valor al input
        }
    });

    function getFormattedDate(inputDate) {        

        let date = new Date(inputDate);
        let year = date.getFullYear();
        let month = String(date.getMonth() + 1).padStart(2, '0'); // Asegurar 2 dígitos
        let day = String(date.getDate()).padStart(2, '0');
        let hours = String(date.getHours()).padStart(2, '0');
        let minutes = String(date.getMinutes()).padStart(2, '0');
        let seconds = "00"; // `datetime-local` no permite segundos, pero lo agregamos manualmente

        let formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        return formattedDate;
    }
        function loadEvents() {
            axios.get(`/api/events/eventsList`)
                .then(response => {
                    let events = response.data.data;
                    let list = document.getElementById('eventsList');
                    events.forEach(event => {
                        let li = document.createElement('li');
                        li.innerHTML = `
    <div class="bg-white shadow-md rounded-lg p-4 flex justify-between items-center">
        <div>
            <h3 class="text-xl font-semibold text-gray-800">${event.title}</h3>
            <p class="text-gray-600">Start Time: ${event.start_time}</p>
            <p class="text-gray-600">Speaker: ${event.speaker.name}</p>
            <p class="text-gray-600">Amount: $${event.amount}</p>
            <p class="text-gray-600">Total Attendees: ${event.total_attendees}</p>
            <p class="text-gray-600">Total Revenue: $${event.total_revenue}</p>
            <p class="text-gray-600">Max Attendees: ${event.max_attendees}</p>
        </div>
        <button 
            onclick="deleteEvent(${event.id})" 
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline"
        >
            Delete
        </button>
    </div>
`;

                        list.appendChild(li);
                    });
                    currentPage++; // Incrementar la página para la siguiente carga
                })
                .catch(error => console.error(error));
        }
        function loadSpeakers() {
            axios.get('/api/speakers/spakersList')
                .then(response => {
                    let speakers = response.data.data;
                    let list = document.getElementById('speakersList');
                    list.innerHTML = '';
                    speakers.forEach(speaker => {
                        let op = document.createElement('option');
                        op.id= speaker.id;
                        op.innerHTML = speaker.name;
                        list.appendChild(op);
                    });
                })
                .catch(error => console.error(error));
        }
        function addEvent() {
            let title = document.getElementById('title').value;
            let type = document.getElementById('type').value;
            let start_time = getFormattedDate(document.getElementById('start_time').value);
            let end_time = getFormattedDate(document.getElementById('end_time').value);
            let speaker_id = document.getElementById('speakersList').options[document.getElementById("speakersList").selectedIndex].id;
            let amount = document.getElementById('amount').value;
            let max_attendees = document.getElementById('max').value;
            axios.post('/api/events/store', {
                title, type, speaker_id, start_time, end_time,amount, max_attendees
            })
            .then(() => {
                alert('Event added successfully');
                document.getElementById('eventForm').reset();
                document.getElementById('eventsList').innerHTML = ''; // Limpiar lista
                currentPage = 1; // Reiniciar paginación
                loadEvents(); // Recargar la lista
            })
            .catch(error => console.error(error));
        }
        function deleteEvent(id) {
            console.log(id)
            axios.post(`/api/events/destroy/${id}`)
                .then(() => {
                    alert('Event deleted successfully');
                    document.getElementById('eventsList').innerHTML = ''; // Limpiar lista
                    currentPage = 1; // Reiniciar paginación
                    loadEvents(); // Recargar la lista
                })
                .catch(error => console.error(error));
        }

        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');

// Función para verificar si la fecha es jueves o viernes
function isThursdayOrFriday(date) {
    const day = date.getDay();
    // 4 = Jueves, 5 = Viernes
    return day === 4 || day === 5;
}

// Evento al seleccionar una fecha
startTime.addEventListener('change', (event) => {
    const selectedDate = new Date(event.target.value);

    // Si no es jueves o viernes, se limpia el campo
    if (!isThursdayOrFriday(selectedDate)) {
        alert('Solo se permiten fechas en jueves y viernes.');
        startTime.value = '';
        endTime.value = '';
    }
});

// Deshabilitar días no permitidos al abrir el calendario
startTime.addEventListener('click', () => {
    const today = new Date();
    let nextAvailableDate = new Date(today);

    // Encuentra el siguiente jueves o viernes
    while (!isThursdayOrFriday(nextAvailableDate)) {
        nextAvailableDate.setDate(nextAvailableDate.getDate() + 1);
    }

    // Define el atributo min para limitar el inicio
    startTime.min = nextAvailableDate.toISOString().split('T')[0];
});
    </script>
</body>
</html>
