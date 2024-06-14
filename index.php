<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda de medina</title>
    <style>
        

        body {
    font-family:  sans-serif;
    background-color: #ffe6f2;
    margin: 0;
    padding: 0;
    display: flex;
    
  }
        .form{
  position:relative;
  width:500px;
  height:1000px;
  padding-left:50px;
  padding-top:25px;
  background-color:#f1d7ff;
  border-radius:20px;
  margin:auto;
  
  size :15px;
  
}
input[type=text],[type=email],[type=date]{
  width:470px;
  height:35px;
  font:15px normal normal uppercasenhelvetica, arial, serif;
  background-color:#f1d7ff ;
}
textarea{
  widht:470px;
  height:110px;
  font:20px normal normal uppercase helvetica, arial serif;

}


input[type=submit]{
  position:relative;
  width:150px;
  height:40px;
  border-radius:30px;
  margin-left:150px;
  border:0px;
  background-color:#734a91;
  font:15px normal normal uppercase helvetica, arial serif;
}
P{
  text-shadow:0 1px 0 #fff;
  font-size:24px;
}

label{
  margin:11px 20px 0 0;
  font-size:100 px;
  color:purple;
  text-transform: uppercase;
  text-shadow:0px 1px 0px #fff;
  align:center;
font:20px normal normal uppercase helvetica, arial serif;

}
 table {
            width: 100%;
            
            margin-top: 20px;
        }
        th, td {
            border: 2px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:nth-child(odd) {
            background-color: #ffffff;
        }
        h1 {
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="form">
    <h1 align="center">Agenda de Sherman</h1>

    <!-- Formulario para ingresar datos -->
    <form method="POST" action="">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required><br><br>
        <label for="apodo">Apodo:</label>
        <input type="text" id="apodo" name="apodo" required><br><br>
        <label for="TelCasa">Teléfono Casa:</label>
        <input type="text" id="TelCasa" name="TelCasa" required><br><br>
        <label for="Cel">Celular:</label>
        <input type="text" id="Cel" name="Cel" required><br><br>
        <label for="NumTrabajo">Número Trabajo:</label>
        <input type="text" id="NumTrabajo" name="NumTrabajo" required><br><br>
        <label for="FechaNaci">Fecha Nacimiento:</label>
        <input type="date" id="FechaNaci" name="FechaNaci" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" value="Guardar">
    </form>

    <!-- Formulario para buscar datos -->
    <form method="GET" action="">
        <label for="buscar">Buscar:</label>
        <input type="text" id="buscar" name="buscar" required><br><br>
        <input type="submit" value="Buscar">
    </form>

    <!-- Botón para mostrar todos los datos -->
    <form method="POST" action="">
        <input type="hidden" name="mostrar_todos" value="1"><br><br>
        <input type="submit" value="Mostrar Todos">
    </form>
    

    <?php
    // Datos de conexión a la base de datos
    $servername = "localhost";
    $username = "root"; 
    $password = ""; 
    $dbname = "agenda"; 

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Función para escapar salidas y prevenir XSS
    function escapeOutput($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    // Procesar datos del formulario de guardar
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['mostrar_todos'])) {
        $nombre = htmlspecialchars($_POST['nombre']);
        $apellido = htmlspecialchars($_POST['apellido']);
        $apodo = htmlspecialchars($_POST['apodo']);
        $TelCasa = htmlspecialchars($_POST['TelCasa']);
        $Cel = htmlspecialchars($_POST['Cel']);
        $NumTrabajo = htmlspecialchars($_POST['NumTrabajo']);
        $FechaNaci = htmlspecialchars($_POST['FechaNaci']);
        $email = htmlspecialchars($_POST['email']);

        // Validación básica
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            die("Correo electrónico inválido.");
        }

        $stmt = $conn->prepare("INSERT INTO dat (nombre, apellido, apodo, TelCasa, Cel, NumTrabajo, FechaNaci, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nombre, $apellido, $apodo, $TelCasa, $Cel, $NumTrabajo, $FechaNaci, $email);

        if ($stmt->execute()) {
            echo "Datos guardados correctamente<br><a href='index.php'>RESTAURAR PAGINA</a><br><br>";
        } else {
            echo "Error al insertar datos: " . escapeOutput($stmt->error) . "<br><br>";
        }

        $stmt->close();
    }

    // Procesar datos del formulario de búsqueda
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['buscar'])) {
        $buscar = htmlspecialchars($_GET['buscar']);
        $stmt = $conn->prepare("SELECT nombre, apellido, apodo, TelCasa, Cel, NumTrabajo, FechaNaci, email FROM dat WHERE nombre LIKE ? OR apellido LIKE ?");
        $buscar_param = "%" . $buscar . "%";
        $stmt->bind_param("ss", $buscar_param, $buscar_param);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<table><tr><th>Nombre</th><th>Apellido</th><th>Apodo</th><th>TelCasa</th><th>Cel</th><th>NumTrabajo</th><th>FechaNaci</th><th>Email</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . escapeOutput($row["nombre"]). "</td><td>" . escapeOutput($row["apellido"]). "</td><td>" . escapeOutput($row["apodo"]). "</td><td>" . escapeOutput($row["TelCasa"]). "</td><td>" . escapeOutput($row["Cel"]). "</td><td>" . escapeOutput($row["NumTrabajo"]). "</td><td>" . escapeOutput($row["FechaNaci"]). "</td><td>" . escapeOutput($row["email"]). "</td></tr>";
                }
                echo "</table><br><a href='index.php'>RESTAURAR PAGINA</a><br><br>";
            } else {
                echo "No se encontraron resultados.";
            }
        } else {
            echo "Error en la búsqueda: " . escapeOutput($stmt->error);
        }

        $stmt->close();
    }

    // Mostrar toda la tabla si se presionó el botón "Mostrar Todos"
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mostrar_todos'])) {
        echo "<h1>Todos los contactos</h1>";
        $result = $conn->query("SELECT nombre, apellido, apodo, TelCasa, Cel, NumTrabajo, FechaNaci, email FROM dat");
        if ($result->num_rows > 0) {
            echo "<table><tr><th>Nombre</th><th>Apellido</th><th>Apodo</th><th>TelCasa</th><th>Cel</th><th>NumTrabajo</th><th>FechaNaci</th><th>Email</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . escapeOutput($row["nombre"]). "</td><td>" . escapeOutput($row["apellido"]). "</td><td>" . escapeOutput($row["apodo"]). "</td><td>" . escapeOutput($row["TelCasa"]). "</td><td>" . escapeOutput($row["Cel"]). "</td><td>" . escapeOutput($row["NumTrabajo"]). "</td><td>" . escapeOutput($row["FechaNaci"]). "</td><td>" . escapeOutput($row["email"]). "</td></tr>";
            }
            echo "</table><br><a href='index.php'>RESTAURAR PAGINA</a><br><br>";
        } else {
            echo "No hay contactos guardados.";
        }
    }

    // Cerrar conexión
    $conn->close();
    ?>
</div>
</body>
</html>