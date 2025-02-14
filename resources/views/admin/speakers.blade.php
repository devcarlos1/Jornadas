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
        <li>
            <form action="{{ 'logout' }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-white hover:text-gray-400">Logout</button>
            </form>
        </li>
    </ul>
</nav>

<!-- Formulario para agregar un nuevo speaker -->
 <div class="w-[50%] my-0 mx-auto mt-6">
 <h2 class="text-2xl font-semibold mb-4 text-gray-700">Add New Speaker</h2>
<form action="POST" id="speakerForm" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 ">
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
    <div class="flex items-center space-x-4">
        <img id="preview" class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-md" src="https://via.placeholder.com/100" alt="Vista previa">
        <label class="cursor-pointer">
            <span class="bg-gradient-to-r from-teal-400 to-blue-500 text-white px-6 py-2 rounded-full shadow-md hover:from-teal-500 hover:to-blue-600 transition duration-300 inline-block">
                <i class="fas fa-upload mr-2"></i>Seleccionar Imagen
            </span>
            <input type="file" name="photo" id="photo" accept="image/png, image/jpeg, image/jpg"  class="hidden" onchange="previewImage(event)" required>
        </label>
    </div>
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-3">
        Add Speaker
    </button>
</form>
 </div>

 <h1 class="text-4xl font-bold text-center my-8 text-blue-600">Speakers List</h1>
<ul id="speakersList" class=" pl-5 text-gray-600 space-y-4 w-fit my-0 mx-auto"></ul>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadSpeakers();

            document.getElementById('speakerForm').addEventListener('submit', function (event) {
                event.preventDefault();
                addSpeaker();
            });
        });
        function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

        
        function loadSpeakers() {
            axios.get(`/api/speakers/spakersList`)
                .then(response => {
                    let speakers = response.data.data;
                    let list = document.getElementById('speakersList');
                    speakers.forEach(speaker => {
                        let li = document.createElement('li');
                        li.innerHTML = `
    <div class="bg-white shadow-md rounded-lg p-4 flex justify-between items-center flex-col">
        <div class="grid grid-cols-3 gap-6  justify-center items-end">
<label class="block text-gray-700 font-semibold">
Photo
<div class=" space-x-4 w-fit flex items-center">
    <!-- Vista previa de la imagen seleccionada -->
    <img id="preview" class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-md" src="${speaker.photo}" alt="Vista previa">
    
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
Speaker Name
<input 
        type="text" 
        value="${speaker.name}" 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="event title"
        disabled
        id="nameUpdate"
    />
</label>

<label class="block text-gray-700 font-semibold mt-4">
    Expertise
    <input 
        type="text" 
        value="${speaker.expertise}" 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="Expertise"
        disabled
        id="expertiseUpdate"
    />
</label>
<label class="block text-gray-700 font-semibold mt-4">
    Social Links
    <input 
        type="text" 
        value="${speaker.social_links}" 
        class="block w-full mt-2 px-4 py-2 w-fit border rounded-lg text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="Amount"
        disabled
        id="socialUpdate"
    />
</label>


        </div>
        <div id="btnsUpdate" class="mt-4 hidden">
        <button 
        onclick="updateEvent(event, ${speaker.id})" 
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
            onclick="deleteEvent(${speaker.id})" 
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




        function addSpeaker() {
            let name = document.getElementById('name').value;
            let expertise = document.getElementById('expertise').value;
            let social_links = document.getElementById('social_links').value;
            let photo = document.getElementById('photo').value;
            const formData = new FormData();
            formData.append('name', name);
            formData.append('expertise', expertise);
            formData.append('social_links', social_links);
            const fileInput = document.getElementById('photo');
            if (fileInput.files.length > 0) {
             formData.append('photo', fileInput.files[0]);
             } 
            axios.post('/api/speakers/store', formData)
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



        function updateEvent(e,id) {
           let name =  e.target.parentNode.parentNode.querySelector('#nameUpdate').value;
            let expertise =  e.target.parentNode.parentNode.querySelector('#expertiseUpdate').value;
            let social_links = e.target.parentNode.parentNode.querySelector('#socialUpdate').value;
            const formData = new FormData();
            formData.append('name', name);
            formData.append('expertise', expertise);
            formData.append('social_links', social_links);
            formData.append('_method', 'PUT');
            const fileInput = e.target.parentNode.parentNode.querySelector('#photoUpdate');
            if (fileInput.files.length > 0) {
                console.log(fileInput.files[0]);
             formData.append('photo', fileInput.files[0]);
             }else{
                console.log(base64ToFile(e.target.parentNode.parentNode.querySelector('#preview').src));
               formData.append('photo',base64ToFile(e.target.parentNode.parentNode.querySelector('#preview').src));
             }
            axios.post(`/api/speakers/update/${id}`,formData)
                .then(() => {
                    alert('speakers update successfully');
                })
                .catch(error => console.error(error));
        }

        function activeUpdate(event){
            const inputs = [
        'nameUpdate',
        'expertiseUpdate',
        'socialUpdate'
    ];
    
    inputs.forEach(id => {
        const element = event.target.parentNode.parentNode.querySelector(`#${id}`);
        if (element) {
            element.removeAttribute('disabled');
        }
    });

    // Mostrar el label del selector de imagen
    const imageLabel = event.target.parentNode.parentNode.querySelector('#photoUpdate').closest('label');
    console.log(imageLabel)
    if (imageLabel) {
        imageLabel.classList.remove('hidden');
    }
     event.target.parentNode.parentNode.querySelector(`#activeUp`).style.display="none";
     event.target.parentNode.parentNode.querySelector(`#btnsUpdate`).style.display="block";

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

    </script>
</body>
</html>
