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

    <script>
        let currentPage = 1; // Para manejar la paginación
        document.addEventListener('DOMContentLoaded', function () {
            loadEvents(); // Cargar lista de eventos

            
            document.getElementById('eventForm').addEventListener('submit', function (event) {
                event.preventDefault();
                addRegister();
            });
        });
        function loadEvents() {
            axios.get(`/api/events/eventsList`)
                .then(response => {
                    let events = response.data.data;
                    let list = document.getElementById('eventsList');
                    events.forEach(event => {
                        let li = document.createElement('li');
                        li.innerHTML = `${event.title} - ${event.start_time} 
                            (Speaker: ${event.speaker.name}) - (Total attendees: ${event.total_attendees}) - (Total Revenue: ${event.total_revenue})
                            <form action="{{ route('paypal.pay') }}" method="POST">
    @csrf
    <input type="hidden" name="amount" value=${event.amount}> 
    <button type="submit">Pagar</button>
</form>
`;
                        list.appendChild(li);
                    });
                    currentPage++; // Incrementar la página para la siguiente carga
                })
                .catch(error => console.error(error));
        }
           
    </script>
</body>
</html>
