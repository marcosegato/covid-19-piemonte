<pre>

<?php

require_once "config.php";

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - START<br/>";

$output_filename = "datasources/dati_per_tutto_il_periodo_ultimo.csv";

$host = "https://raw.githubusercontent.com/to-mg/covid-19-piemonte/master/data/dati_per_tutto_il_periodo_ultimo.csv";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $host);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, false);
curl_setopt($ch, CURLOPT_REFERER, "https://raw.githubusercontent.com");
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_HEADER, 0);
$result = curl_exec($ch);
curl_close($ch);

//print_r($result); // prints the contents of the collected file before writing..

// the following lines write the contents to a file in the same directory (provided permissions etc)
$fp = fopen($output_filename, 'w');
fwrite($fp, $result);
fclose($fp);

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - CSV file download completed<br/>";

if (file_exists($output_filename)) {
    echo "File exists!\n";
} else {
    die("File does not exists.\n");
}

$conn = mysqli_connect(_MYSQL_HOST, _MYSQL_USER, _MYSQL_PSW, _MYSQL_DB);
// Check connection
if (!$conn) {
  die("MySQL connection failed: " . mysqli_connect_error());
} else {
    echo "MySQL connection succesfull!\n";
}

//$sql = "LOAD DATA LOCAL INFILE '" .$output_filename. "' INTO TABLE "._MYSQL_TABLE." FIELDS TERMINATED BY ';' IGNORE 1 LINES (Ente,Tipo,Provincia,ASL,Codice ISTAT,Abitanti,Positivi,Positivi 1000 abitanti,Delta positivi,Delta positivi 1000 abitanti,Data)";
$sqlTruncate = "truncate table "._MYSQL_TABLE;
$resTruncate = mysqli_query($conn, $sqlTruncate);

$file = fopen($output_filename, "r");
$flag = true;

while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
    if($flag) { $flag = false; continue; }

    $ente = "";
    if (isset($column[0])) {
        $ente = mysqli_real_escape_string($conn, $column[0]);
    }
    $tipo = "";
    if (isset($column[1])) {
        $tipo = mysqli_real_escape_string($conn, $column[1]);
    }
    $provincia = "";
    if (isset($column[2])) {
        $provincia = mysqli_real_escape_string($conn, $column[2]);
    }
    $asl = "";
    if (isset($column[3])) {
        $asl = mysqli_real_escape_string($conn, $column[3]);
    }
    $codiceIstat = "";
    if (isset($column[4])) {
        $codiceIstat = mysqli_real_escape_string($conn, $column[4]);
    }
    $abitanti = "";
    if (isset($column[5])) {
        $abitanti = mysqli_real_escape_string($conn, $column[5]);
    }
    $positivi = "";
    if (isset($column[6])) {
        $positivi = mysqli_real_escape_string($conn, $column[6]);
    }
    $positiviMilleAbitanti = "";
    if (isset($column[7])) {
        $positiviMilleAbitanti = mysqli_real_escape_string($conn, $column[7]);
    }
    $deltaPositivi = "";
    if (isset($column[8])) {
        $deltaPositivi = mysqli_real_escape_string($conn, $column[8]);
    }
    $deltaPositiviMilleAbitanti = "";
    if (isset($column[9])) {
        $deltaPositiviMilleAbitanti = mysqli_real_escape_string($conn, $column[9]);
    }
    $data = "";
    if (isset($column[10])) {
        $data = mysqli_real_escape_string($conn, $column[10]);
    }

    $sqlInsert = "INSERT into "._MYSQL_TABLE." (`Ente`,`Tipo`,`Provincia`,`ASL`,`Codice ISTAT`,`Abitanti`,`Positivi`,`Positivi 1000 abitanti`,`Delta positivi`,`Delta positivi 1000 abitanti`,`Data`) values ('".$ente."','".$tipo."','".$provincia."','".$asl."','".$codiceIstat."',$abitanti,$positivi,$positiviMilleAbitanti,$deltaPositivi,$deltaPositiviMilleAbitanti,'".$data."')";
    $insertId = mysqli_query($conn, $sqlInsert);

    /*if (! empty($insertId)) {
        $type = "success";
        $message = "CSV Data Imported into the Database";
    } else {
        $type = "error";
        $message = "Problem in Importing CSV Data: " .  mysqli_error();
    }
    echo "$message";*/
}

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - CSV Data Imported into the Database<br/>";

?>

</pre>