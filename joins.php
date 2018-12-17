<?php
/**
 * Created by PhpStorm.
 * User: Jarrin
 * Date: 06/12/2018
 * Time: 09:20
 */

require './connection.php';

$leerling = $klas = $school = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leerling = filter_input(INPUT_POST, 'leerling', FILTER_SANITIZE_STRING);
    $klas =     filter_input(INPUT_POST, 'klas', FILTER_SANITIZE_STRING);
    $school =   filter_input(INPUT_POST, 'school', FILTER_SANITIZE_STRING);


    $stmt = $conn->prepare('INSERT INTO scholen (naam) VALUES(:naam)');
    $stmt->execute([
        ':naam' => $school
    ]);
    $school_id = $conn->lastInsertId();

    $stmt = $conn->prepare('INSERT INTO klassen (school_id, naam) VALUES(:school_id, :naam)');
    $stmt->execute([
        ':naam' => $klas,
        ':school_id' => $school_id,
    ]);
    $klas_id = $conn->lastInsertId();


    $stmt = $conn->prepare('INSERT INTO leerlingen (klas_id, naam) VALUES(:klas_id, :naam)');
    $stmt->execute([
        ':naam' => $leerling,
        ':klas_id' => $klas_id,
    ]);
}

$sql = 'SELECT 
              leerlingen.id, 
              leerlingen.naam as leerling_naam, 
              klassen.naam  as klas_naam,
              scholen.naam as school_naam
            FROM `leerlingen` 
            LEFT JOIN 
              klassen ON leerlingen.klas_id = klassen.id
            LEFT JOIN 
              scholen ON klassen.school_id = scholen.id  
              ';


$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


//var_dump($data); die;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoeren</title>
    <style>
        label {
            display: block;
            width: 350px;
            clear: both;
            margin-bottom: 15px;
        }
        label > input {
            float: right;
            width: 200px;
        }
        table td {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<form method="post">
    <?php
    if(!empty($errors)) {
        foreach ($errors as $error) {
            echo '- ' . $error . '<br>';
        }
    }

    ?>
    <h2>Nieuwe leerling, klas en school</h2>
    <label>
        Leerling:
        <input type="text" name="leerling" value="<?php echo $leerling ?>" >
    </label>
    <label>
        Klas:
        <input type="text" name="klas" value="<?php echo $klas ?>"  >
    </label>
    <label>
        School:
        <input type="text" name="school" value="<?php echo $school ?>" >
    </label>
    <button>Invoeren</button>
</form>
    <table>
        <?php foreach ($data as $row) { ?>
            <tr>
                <?php foreach ($row as $col) { ?>
                    <td><?php echo $col ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
