<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Error</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.error-container {
    text-align: center;
    background-color: #ffffff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.error-container h1 {
    font-size: 72px;
    color: #ff6b6b;
    margin: 0;
}

.error-container p {
    font-size: 18px;
    color: #666666;
    margin: 20px 0;
}

.error-container .button {
    text-decoration: none;
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s;
}

.error-container .button:hover {
    background-color: #45a049;
}
    </style>
</head>
<body>
    <div class="error-container">
        <h1><?php echo 'Plan '.$grupo_orden; ?></h1>
        <p>Lo sentimos, el plan ya fue reservado por otro asesor</p>
        <a href="/planes_avanzados" class="button">Volver a la lista de Planes</a>
    </div>
</body>
</html>