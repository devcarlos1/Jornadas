<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speakers</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Speakers List</h1>
    
    <!-- Formulario para agregar un nuevo speaker -->
    <h2>Add New Speaker</h2>
    <form action="POST" id="speakerForm">
        <input type="text" id="name" placeholder="Name" required>
        <input type="text" id="expertise" placeholder="Expertise" required>
        <input type="text" id="social_links" placeholder="Social">
        <button type="submit">Add Speaker</button>
    </form>

    <h2>Speakers</h2>
    <ul id="speakersList"></ul>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadSpeakers();

            document.getElementById('speakerForm').addEventListener('submit', function (event) {
                event.preventDefault();
                addSpeaker();
            });
        });

        function loadSpeakers() {
            axios.get('/api/speakers/spakersList')
                .then(response => {
                    let speakers = response.data.data;
                    let list = document.getElementById('speakersList');
                    list.innerHTML = '';
                    speakers.forEach(speaker => {
                        let li = document.createElement('li');
                        li.innerHTML = `${speaker.name} - ${speaker.expertise} - ${speaker.social_links}
                            <button onclick="deleteSpeaker(${speaker.id})">Delete</button>`;
                        list.appendChild(li);
                    });
                })
                .catch(error => console.error(error));
        }

        function addSpeaker() {
            let name = document.getElementById('name').value;
            let expertise = document.getElementById('expertise').value;
            let social_links = document.getElementById('social_links').value;
            axios.post('/api/speakers/store', { name, expertise, social_links })
                .then(() => {
                    alert('Speaker added successfully');
                    document.getElementById('speakerForm').reset();
                    loadSpeakers();
                })
                .catch(error => console.error(error));
        }

        function deleteSpeaker(id) {
            axios.post(`/api/speakers/destroy/${id}`)
                .then(() => {
                    alert('Speaker deleted successfully');
                    loadSpeakers();
                })
                .catch(error => console.error(error));
        }
    </script>
</body>
</html>
