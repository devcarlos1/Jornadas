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
    <div class="flex items-center space-x-4">
        <img id="preview" class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-md" src="https://via.placeholder.com/100" alt="Vista previa">
        <label class="cursor-pointer">
            <span class="bg-gradient-to-r from-teal-400 to-blue-500 text-white px-6 py-2 rounded-full shadow-md hover:from-teal-500 hover:to-blue-600 transition duration-300 inline-block">
                <i class="fas fa-upload mr-2"></i>Seleccionar Imagen
            </span>
            <input type="file" name="photo" id="photo" accept="image/png, image/jpeg, image/jpg"  class="hidden" onchange="previewImage(event)" required>
        </label>
    </div>
    <button 
        type="submit" 
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline mt-4"
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
    <div class="bg-white shadow-md rounded-lg p-4 flex justify-between items-center flex-col">
        <div class="grid grid-cols-4 gap-6  justify-center items-end">
<label class="block text-gray-700 font-semibold">
Photo
<div class=" space-x-4 w-fit flex items-center">
    <!-- Vista previa de la imagen seleccionada -->
    <img id="preview" class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-md" src="${event.photo}" alt="Vista previa">
    
    <!-- Botón personalizado para seleccionar imagen -->
    <label class="cursor-pointer hidden">
        <span class="bg-gradient-to-r from-teal-400 to-blue-500 text-white px-6 py-2 rounded-full shadow-md hover:from-teal-500 hover:to-blue-600 transition duration-300 inline-block">
            <i class="fas fa-upload mr-2"></i>Seleccionar Imagen
        </span>
        <!-- Input oculto para el archivo de imagen -->
        <input type="file" name="photo" id="photoUpdate" accept="image/png, image/jpeg, image/jpg" class="hidden" onchange="previewImage(event)" files="${event.photo}" required>
    </label>
</div>
</label>

<label class="block text-gray-700 font-semibold">
Event Title    
<input 
        type="text" 
        value="${event.title}" 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="event title"
        disabled
        id="titleUpdate"
    />
</label>

<label class="block text-gray-700 font-semibold mt-4">
    Type of Event
    <select 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        name="type"     
        disabled
        id="typeUpdate"
    >
        <option value="Taller" ${event.type === 'taller' ? 'selected' : ''}>Taller</option>
        <option value="Conferencia" ${event.type === 'conferencia' ? 'selected' : ''}>Conferencia</option>
    </select>
</label>

<label class="block text-gray-700 font-semibold mt-4">
Start Time 
   <input 
        type="datetime-local" 
        value="${event.start_time}" 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="Start Time"
        disabled
        id="start_timeUpdate"
    />
</label>

<label class="block text-gray-700 font-semibold mt-4">
End Time 
   <input 
        type="datetime-local" 
        value="${event.end_time}" 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="End Time"
        disabled
        id="end_timeUpdate"
    />
</label>

<label class="block text-gray-700 font-semibold mt-4" id="LabelSpeaker_NameUpdate">
    Speaker Name
    <input 
        type="text" 
        value="${event.speaker.name}" 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="Speaker Name"
        disabled
        id="Speaker_NameUpdate"
    />
</label>
<select id="speakersListUpdate"   class="hidden w-full h-fit px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></select>

<label class="block text-gray-700 font-semibold mt-4">
    Amount
    <input 
        type="number" 
        value="${event.amount}" 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="Amount"
        disabled
        id="amountUpdate"
    />
</label>

<label class="block text-gray-700 font-semibold mt-4">
    Total Attendees
    <input 
        type="number" 
        value="${event.total_attendees}" 
        class="block w-full mt-2 px-4 py-2 border w-fit rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder=" Total Attendees"
        disabled
    />
</label>

<label class="block text-gray-700 font-semibold mt-4">
    Total Revenue
    <input 
        type="number" 
        value="${event.total_revenue}" 
        class="block w-full mt-2 px-4 py-2 border w-fit rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="Total Revenue"
        disabled
        id=""
    />
</label>

<label class="block text-gray-700 font-semibold mt-4">
    Max Attendees
    <input 
        type="number" 
        value="${event.max_attendees}" 
        class="block w-full mt-2 px-4 py-2 border w-fit rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="Max Attendees"
        disabled
        id="maxUpdate"
    />
</label>

        </div>
        <div id="btnsUpdate" class="mt-4 hidden">
        <button 
        onclick="updateEvent(event, ${event.id})" 
        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
        Confirmar
    </button>

    <!-- Botón para cancelar cambios -->
    <button 
        onclick="cancelUpdate(event)" 
        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
        Cancelar
    </button>
        </div>
<div class="mt-4">
        <button 
    onclick="activeUpdate(event)" 
    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300"
    id="activeUp">
    Actualizar Eventos
         </button>
        <button 
            onclick="deleteEvent(${event.id})" 
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline"
        >
            Delete
        </button>
</div>
    </div>
`;

                        list.appendChild(li);
                    });
                    currentPage++; // Incrementar la página para la siguiente carga
                })
                .catch(error => console.error(error));
        }

         function loadSpeakers() {
            axios.get(`/api/speakers/spakersList`)
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
        function loadSpeakersUpdate(e) {
            axios.get(`/api/speakers/spakersList`)
                .then(response => {
                    let speakers = response.data.data;
                    let list = e.querySelector('#speakersListUpdate');
                    console.log(list);
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
            const formData = new FormData();
            formData.append('title', title);
            formData.append('type', type);
            formData.append('start_time', start_time);
            formData.append('end_time', end_time);
            formData.append('speaker_id', speaker_id);
            formData.append('amount', amount);
            formData.append('max_attendees', max_attendees);
            const fileInput = document.getElementById('photo');
            if (fileInput.files.length > 0) {
             formData.append('photo', fileInput.files[0]);
             } 
            axios.post('/api/events/store', formData)
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
            axios.post(`/api/events/destroy/${id}`)
                .then(() => {
                    alert('Event deleted successfully');
                    document.getElementById('eventsList').innerHTML = ''; // Limpiar lista
                    currentPage = 1; // Reiniciar paginación
                    loadEvents(); // Recargar la lista
                })
                .catch(error => console.error(error));
        }

        function updateEvent(e,id) {
           let title =  e.target.parentNode.parentNode.querySelector('#titleUpdate').value;
            let type =  e.target.parentNode.parentNode.querySelector('#typeUpdate').value;
            let start_time = getFormattedDate( e.target.parentNode.parentNode.querySelector('#start_timeUpdate').value);
            let end_time = getFormattedDate( e.target.parentNode.parentNode.querySelector('#end_timeUpdate').value);
            let speaker_id =  e.target.parentNode.parentNode.querySelector('#speakersListUpdate').options[ e.target.parentNode.parentNode.querySelector("#speakersListUpdate").selectedIndex].id;
            let amount =  e.target.parentNode.parentNode.querySelector('#amountUpdate').value;
            let max_attendees =  e.target.parentNode.parentNode.querySelector('#maxUpdate').value;
            const formData = new FormData();
            formData.append('title', title);
            formData.append('type', type);
            formData.append('start_time', start_time);
            formData.append('end_time', end_time);
            formData.append('speaker_id', speaker_id);
            formData.append('amount', amount);
            formData.append('max_attendees', max_attendees);
            formData.append('_method', 'PUT');
            const fileInput = e.target.parentNode.parentNode.querySelector('#photoUpdate');
            if (fileInput.files.length > 0) {
                console.log(fileInput.files[0]);
             formData.append('photo', fileInput.files[0]);
             }else{
                console.log(base64ToFile(e.target.parentNode.parentNode.querySelector('#preview').src));
               formData.append('photo',base64ToFile(e.target.parentNode.parentNode.querySelector('#preview').src));
             }
            axios.post(`/api/events/update/${id}`,formData)
                .then(() => {
                    alert('Event update successfully');
                })
                .catch(error => console.error(error));
        }

        function activeUpdate(event){
            const inputs = [
        'photoUpdate',
        'titleUpdate',
        'typeUpdate',
        'start_timeUpdate',
        'amountUpdate',
        'maxUpdate'
    ];
    
    inputs.forEach(id => {
        const element = event.target.parentNode.parentNode.querySelector(`#${id}`);
        if (element) {
            element.removeAttribute('disabled');
        }
    });

    // Mostrar el label del selector de imagen
    const imageLabel = event.target.parentNode.parentNode.querySelector('#photoUpdate').closest('label');
    if (imageLabel) {
        imageLabel.classList.remove('hidden');
    }
     event.target.parentNode.parentNode.querySelector('#LabelSpeaker_NameUpdate').style.display="none";
     event.target.parentNode.parentNode.querySelector(`#activeUp`).style.display="none";
     event.target.parentNode.parentNode.querySelector(`#btnsUpdate`).style.display="block";
     event.target.parentNode.parentNode.querySelector(`#speakersListUpdate`).style.display="block";

     loadSpeakersUpdate(event.target.parentNode.parentNode);
            }
        function cancelUpdate(event) {
    const row = event.target.parentNode.parentNode;

    // Deshabilitar todos los inputs y selects en esa fila
    const formElements = row.querySelectorAll('input, select');
    formElements.forEach(element => {
        element.setAttribute('disabled', true);
    });

    // Ocultar labels para inputs de tipo file
    const fileLabels = row.querySelectorAll('input[type="file"]');
    fileLabels.forEach(fileInput => {
        const imageLabel = fileInput.closest('label');
        if (imageLabel) {
            imageLabel.classList.add('hidden');
        }
    });
}


function base64ToFile(base64, filename) {
    // Separar el encabezado de los datos base64
    const arr = base64.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    
    // Convertir base64 a binario
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }

    // Crear un archivo Blob y luego un File
    return new File([u8arr], filename, { type: mime });
}
        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');

// Función para verificar si la fecha es jueves o viernes
function isThursdayOrFriday(date) {
        const d = new Date(date);
        const day = d.getUTCDate();
        const weekDay = d.getUTCDay();
        // Verifica que sea el 27 (jueves) o el 28 (viernes)
        return (day === 27 && weekDay === 4) || (day === 28 && weekDay === 5);
}

// Evento al seleccionar una fecha
startTime.addEventListener('change', (event) => {
    const selectedDate = new Date(event.target.value);

    // Si no es jueves o viernes, se limpia el campo
    if (!isThursdayOrFriday(selectedDate)) {
        alert('Solo se permiten fechas en jueves (27) y viernes (28).');
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
function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output =event.target.parentNode.parentNode.querySelector('#preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
}
    </script>
</body>
</html>
