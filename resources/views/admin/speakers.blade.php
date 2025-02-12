<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speakers</title>
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
    </ul>
</nav>

<h1 class="text-4xl font-bold text-center my-8 text-blue-600">Speakers List</h1>

<!-- Formulario para agregar un nuevo speaker -->
 <div class="w-[50%] my-0 mx-auto">
 <h2 class="text-2xl font-semibold mb-4 text-gray-700">Add New Speaker</h2>
<form action="POST" id="speakerForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <div class="mb-4">
        <input type="text" id="name" placeholder="Name" required 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
        <input type="text" id="expertise" placeholder="Expertise" required 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
        <input type="text" id="social_links" placeholder="Social" 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        Add Speaker
    </button>
</form>
 </div>


<h2 class="text-2xl font-semibold mt-8 mb-4 text-gray-700">Speakers</h2>
<ul id="speakersList" class=" pl-5 text-gray-600 space-y-4"></ul>


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
                        li.innerHTML = `
    <div class="flex items-center justify-between bg-white shadow-md rounded-lg p-4 mb-4">
        <div>
            <h3 class="text-xl font-bold text-gray-800">${speaker.name}</h3>
            <p class="text-gray-600">Expertise: ${speaker.expertise}</p>
            <p class="text-blue-500">Social: ${speaker.social_links}</p>
        </div>
        <button 
            onclick="deleteSpeaker(${speaker.id})" 
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline"
        >
            Delete
        </button>
    </div>
`;
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
