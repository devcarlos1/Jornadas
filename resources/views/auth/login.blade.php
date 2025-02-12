<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center mb-6">Iniciar Sesión</h2>

        <form id="login-form">
            <div class="mb-4">
                <label class="block text-gray-700">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="w-full p-2 border rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Contraseña</label>
                <input type="password" id="password" name="password" class="w-full p-2 border rounded" required>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Ingresar</button>
        </form>

        <p class="text-center text-gray-600 mt-4">¿No tienes una cuenta? 
            <a href="{{ route('register') }}" class="text-blue-500">Regístrate</a>
        </p>
    </div>
    <script>
        document.getElementById("login-form").addEventListener("submit", (e) => {
            e.preventDefault(); // Evita recargar la página

            axios.get("http://127.0.0.1:8000/sanctum/csrf-cookie").then(() => {
                axios.post("http://127.0.0.1:8000/api/login", {
                    email: document.getElementById("email").value,
                    password: document.getElementById("password").value
                }, { withCredentials: true }) // Usa cookies para autenticación
                .then(response => {
                   response.data.role === 'admin'? window.location.href = "admin/speakers" :window.location.href = "users/dashboard";
                })
                .catch(error => {
                    console.log(error)
                });
            }).catch(error => console.log(error));
        });
    </script>
</body>
</html>
