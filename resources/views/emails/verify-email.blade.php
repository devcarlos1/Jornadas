<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verifica tu cuenta</title>
</head>
<body>
    <h2>¡Gracias por registrarte en Jornadas de Videojuegos!</h2>
    <p>Para activar tu cuenta, haz clic en el siguiente enlace:</p>
    <a href="{{ url('/api/verify-email/' . $token) }}">Verificar Cuenta</a>
</body>
</html>
