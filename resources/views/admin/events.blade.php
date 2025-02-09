<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Events Management</h1>

    <!-- Formulario para agregar un nuevo evento -->
    <h2>Add New Event</h2>
    <form id="eventForm">
        <input type="text" id="title" placeholder="Title" required>
        <select id="type">
            <option value="taller">Taller</option>
            <option value="conferencia">Conferencia</option>
        </select>
        <select id="speakersList" required></select>
        <input type="number" id="max">
        <input type="datetime-local" id="start_time" required>
        <input type="datetime-local" id="end_time" required readonly>
        <button type="submit">Add Event</button>
    </form>

    <h2>Events List</h2>
    <ul id="eventsList"></ul>

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
                        li.innerHTML = `${event.title} - ${event.start_time} 
                            (Speaker: ${event.speaker.name}) 
                            <button onclick="deleteEvent(${event.id})">Delete</button>`;
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
            let max_attendees = document.getElementById('max').value;
            console.log(speaker_id)
            axios.post('/api/events/store', {
                title, type, speaker_id, start_time, end_time, max_attendees
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
    </script>
</body>
</html>
