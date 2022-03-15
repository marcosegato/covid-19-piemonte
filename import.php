<pre>

<?php

require_once "config.php";

/**
 * Importazione dati storici
 */

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - START HIST<br/>";

$gz_file_input    = "datasources/dati_per_tutto_il_periodo_ultimo.csv.gz";
$output_filename  = "datasources/dati_per_tutto_il_periodo_ultimo.csv";

$host = "https://raw.githubusercontent.com/marcosegato/covid-19-piemonte-data-scraper/master/data/dati_per_tutto_il_periodo_ultimo.csv.gz";
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
$fp = fopen($gz_file_input, 'w');
fwrite($fp, $result);
fclose($fp);

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - ZIP file download completed<br/>";

if (file_exists($gz_file_input)) {
    echo "File $gz_file_input exists!\n";
} else {
    die("File $gz_file_input does not exists.\n");
}

// Unzip file
// Raising this value may increase performance
$buffer_size = 4096; // read 4kb at a time
// Open our files (in binary mode)
$file = gzopen($gz_file_input, 'rb');
$out_file = fopen($output_filename, 'wb');
// Keep repeating until the end of the input file
while (!gzeof($file)) {
    // Read buffer-size bytes
    // Both fwrite and gzread and binary-safe
    fwrite($out_file, gzread($file, $buffer_size));
}
// Files are done, close files
fclose($out_file);
gzclose($file);

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - CSV file extraction completed<br/>";

if (file_exists($output_filename)) {
    echo "File $output_filename exists!\n";
} else {
    die("File $output_filename does not exists.\n");
}

$conn = mysqli_connect(_MYSQL_HOST, _MYSQL_USER, _MYSQL_PSW, _MYSQL_DB);
// Check connection
if (!$conn) {
  die("MySQL connection failed: " . mysqli_connect_error());
} else {
    echo "MySQL connection succesfull!\n";
}

//$sql = "LOAD DATA LOCAL INFILE '" .$output_filename. "' INTO TABLE "._MYSQL_TABLE_H." FIELDS TERMINATED BY ';' IGNORE 1 LINES (Ente,Tipo,Provincia,ASL,Codice ISTAT,Abitanti,Positivi,Positivi 1000 abitanti,Delta positivi,Delta positivi 1000 abitanti,Data)";
$sqlTruncate = "truncate table "._MYSQL_TABLE_H;
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

    $sqlInsert = "INSERT into "._MYSQL_TABLE_H." (`Ente`,`Tipo`,`Provincia`,`ASL`,`Codice ISTAT`,`Abitanti`,`Positivi`,`Positivi 1000 abitanti`,`Delta positivi`,`Delta positivi 1000 abitanti`,`Data`) values ('".$ente."','".$tipo."','".$provincia."','".$asl."','".$codiceIstat."',$abitanti,$positivi,$positiviMilleAbitanti,$deltaPositivi,$deltaPositiviMilleAbitanti,'".$data."')";
    if($tipo<>"ASL") {
        $insertId = mysqli_query($conn, $sqlInsert);
    }

    /*if (! empty($insertId)) {
        $type = "success";
        $message = "CSV Data Imported into the Database";
    } else {
        $type = "error";
        $message = "Problem in Importing CSV Data: " .  mysqli_error($conn);
    }
    echo "$message";*/
}

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - CSV Data Imported into the Database<br/><br/>";


/**
 * Importazione dati giornalieri
 */

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - START DAILY<br/>";

$output_filename  = "datasources/covid-piemonte.csv";

$host = "https://raw.githubusercontent.com/floatingpurr/covid-piemonte/main/data/covid-piemonte.csv";
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
    echo "File $output_filename exists!\n";
} else {
    die("File $output_filename does not exists.\n");
}

$conn = mysqli_connect(_MYSQL_HOST, _MYSQL_USER, _MYSQL_PSW, _MYSQL_DB);
// Check connection
if (!$conn) {
  die("MySQL connection failed: " . mysqli_connect_error());
} else {
    echo "MySQL connection succesfull!\n";
}

//$sql = "LOAD DATA LOCAL INFILE '" .$output_filename. "' INTO TABLE "._MYSQL_TABLE_D." FIELDS TERMINATED BY ';' IGNORE 1 LINES (`comune`,`codice_istat`,`provincia`,`positivi`,`positivi_per_1000_abitanti`)";
$sqlTruncate = "truncate table "._MYSQL_TABLE_D;
$resTruncate = mysqli_query($conn, $sqlTruncate);

$file = fopen($output_filename, "r");
$flag = true;

while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
    if($flag) { $flag = false; continue; }

    $comune = "";
    if (isset($column[0])) {
        $comune = mysqli_real_escape_string($conn, $column[0]);
    }
    $codice_istat = "";
    if (isset($column[1])) {
        $codice_istat = mysqli_real_escape_string($conn, $column[1]);
    }
    $provincia = "";
    if (isset($column[2])) {
        $provincia = mysqli_real_escape_string($conn, $column[2]);
    }
    $positivi = "";
    if (isset($column[3])) {
        $positivi = mysqli_real_escape_string($conn, $column[3]);
    }
    $positivi_per_1000_abitanti = "";
    if (isset($column[4])) {
        $positivi_per_1000_abitanti = mysqli_real_escape_string($conn, $column[4]);
    }

    $sqlInsert = "INSERT into "._MYSQL_TABLE_D." (`comune`,`codice_istat`,`provincia`,`positivi`,`positivi_per_1000_abitanti`) values ('".$comune."',$codice_istat,'".$provincia."',$positivi,$positivi_per_1000_abitanti)";
    $insertId = mysqli_query($conn, $sqlInsert);

    /*if (! empty($insertId)) {
        $type = "success";
        $message = "CSV Data Imported into the Database";
    } else {
        $type = "error";
        $message = "Problem in Importing CSV Data: " .  mysqli_error($conn);
    }
    echo "$message";*/
}

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " - CSV Data Imported into the Database<br/>";

?>

</pre>