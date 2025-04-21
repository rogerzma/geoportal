<!-- filepath: c:\xampp\htdocs\geoportal\resources\views\welcome.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Geoportal de Parcelas</title>
    <meta charset="utf-8" />
    <style>
        iframe {
            width: 100vw;
            height: 100vh;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Incrustar el mapa de QGIS2Web -->
    <iframe src="{{ asset('qgis2web_2025_04_20-21_28_11_136464/index.html') }}"></iframe>
</body>
</html>