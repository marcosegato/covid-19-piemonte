<?php

require_once "config.php";
require_once "koolreport/core/autoload.php";

use \koolreport\KoolReport;
use \koolreport\processes\Filter;
use \koolreport\processes\Group;
use \koolreport\processes\Sort;
use \koolreport\processes\Limit;


class Covid19Piemonte extends KoolReport
{

    public function settings()
    {
        if(_ENV == "dev") {
            return array(
                "dataSources"=>array(
                    "covid_19_piemonte"=>array(
                        "class"=>'\koolreport\datasources\CSVDataSource',
                        "filePath"=>dirname(__FILE__)."/datasources/dati_per_tutto_il_periodo_ultimo.csv",
                        "fieldSeparator"=>";",
                    ),
                    "covid_19_piemonte_daily"=>array(
                        "class"=>'\koolreport\datasources\CSVDataSource',
                        "filePath"=>dirname(__FILE__)."/datasources/covid-piemonte.csv",
                        "fieldSeparator"=>",",
                    )
                )
            );
        }

        if(_ENV == "prod") {
            return array(
                "dataSources"=>array(
                    "covid_19_piemonte"=>array(
                        "connectionString"=>"mysql:host="._MYSQL_HOST.";dbname="._MYSQL_DB,
                        'username' => _MYSQL_USER,
                        'password' => _MYSQL_PSW,
                        'charset' => 'utf8'
                    ),
                    "covid_19_piemonte_daily"=>array(
                        "connectionString"=>"mysql:host="._MYSQL_HOST.";dbname="._MYSQL_DB,
                        'username' => _MYSQL_USER,
                        'password' => _MYSQL_PSW,
                        'charset' => 'utf8'
                    )
                )
            );
        }
    }

    protected function setup()
    {
        // transcodifica della Provincia con il suo nome
        switch (_ENTE) {
            case 'AL':
                define("_ENTE_PRINT", "Alessandria");
                break;
            case 'AT':
                define("_ENTE_PRINT", "Asti");
                break;
            case 'BI':
                define("_ENTE_PRINT", "Biella");
                break;
            case 'CN':
                define("_ENTE_PRINT", "Cuneo");
                break;
            case 'NO':
                define("_ENTE_PRINT", "Novara");
                break;
            case 'TO':
                define("_ENTE_PRINT", "Torino");
                break;
            case 'VCO':
                define("_ENTE_PRINT", "Verbano-Cusio-Ossola");
                break;
            case 'VC':
                define("_ENTE_PRINT", "Vercelli");
                break;

            default:
                define("_ENTE_PRINT", _ENTE);
                break;
        }

        if(_ENV == "dev") {

            //echo "_TIPO = "._TIPO.", _ENTE = "._ENTE; // DEBUG

            // contagiati_al_giorno
            $this->src('covid_19_piemonte')
            ->pipe(new Filter(array(
                array("Ente","=",_ENTE)
            )))
            ->pipe(new Filter(array(
                array("Tipo","<>","ASL")
            )))
            ->pipe($this->dataStore('contagiati_al_giorno'));

            if( (_ENTE == "Piemonte") || (_TIPO == "REG" || _TIPO == "PROV") ) {

                // top10_province_positivi
                $this->src('covid_19_piemonte_daily')
                ->pipe(new Group(array(
                    "by"=>"provincia",
                    "sum"=>"positivi"
                )))
                ->pipe(new Sort(array(
                    "positivi"=>"desc",
                )))
                ->pipe($this->dataStore('top10_province_positivi'));

                if(_ENTE == "Piemonte") {
                    // top10_comuni_positivi1000
                    $this->src('covid_19_piemonte_daily')
                    ->pipe(new Sort(array(
                        "positivi_per_1000_abitanti"=>"desc",
                    )))
                    ->pipe(new Limit(array(10)))
                    ->pipe($this->dataStore('top10_comuni_positivi1000'));

                    // top10_comuni_positivi
                    $this->src('covid_19_piemonte_daily')
                    ->pipe(new Sort(array(
                        "positivi"=>"desc",
                    )))
                    ->pipe(new Limit(array(10)))
                    ->pipe($this->dataStore('top10_comuni_positivi'));
                }

                if(_TIPO == "PROV" && _ENTE != "Piemonte") {
                    // top10_comuni_positivi1000
                    $this->src('covid_19_piemonte_daily')
                    ->pipe(new Filter(array(
                        array("provincia","=",_ENTE_PRINT)
                    )))
                    ->pipe(new Sort(array(
                        "positivi_per_1000_abitanti"=>"desc",
                    )))
                    ->pipe(new Limit(array(10)))
                    ->pipe($this->dataStore('top10_comuni_positivi1000'));

                    // top10_comuni_positivi
                    $this->src('covid_19_piemonte_daily')
                    ->pipe(new Filter(array(
                        array("provincia","=",_ENTE_PRINT)
                    )))
                    ->pipe(new Sort(array(
                        "positivi"=>"desc",
                    )))
                    ->pipe(new Limit(array(10)))
                    ->pipe($this->dataStore('top10_comuni_positivi'));
                }
            }
        }

        if(_ENV == "prod") {

            //echo "_TIPO = "._TIPO.", _ENTE = "._ENTE; // DEBUG

            // contagiati_al_giorno
            $this->src('covid_19_piemonte')
            ->query("SELECT * FROM "._MYSQL_TABLE_H." where Ente=:ente")
            ->params(array(":ente"=>_ENTE))
            ->pipe($this->dataStore('contagiati_al_giorno'));

            if( (_ENTE == "Piemonte") || (_TIPO == "REG" || _TIPO == "PROV") ) {

                // top10_province_positivi
                $this->src('covid_19_piemonte_daily')
                ->query("SELECT * FROM "._MYSQL_TABLE_D)
                ->pipe(new Group(array(
                    "by"=>"provincia",
                    "sum"=>"positivi"
                )))
                ->pipe(new Sort(array(
                    "positivi"=>"desc",
                )))
                ->pipe($this->dataStore('top10_province_positivi'));

                if(_ENTE == "Piemonte") {
                    // top10_comuni_positivi1000
                    $this->src('covid_19_piemonte_daily')
                    ->query("SELECT * FROM "._MYSQL_TABLE_D)
                    ->pipe(new Sort(array(
                        "positivi_per_1000_abitanti"=>"desc",
                    )))
                    ->pipe(new Limit(array(10)))
                    ->pipe($this->dataStore('top10_comuni_positivi1000'));

                    // top10_comuni_positivi
                    $this->src('covid_19_piemonte_daily')
                    ->query("SELECT * FROM "._MYSQL_TABLE_D)
                    ->pipe(new Sort(array(
                        "positivi"=>"desc",
                    )))
                    ->pipe(new Limit(array(10)))
                    ->pipe($this->dataStore('top10_comuni_positivi'));
                }

                if(_TIPO == "PROV" && _ENTE != "Piemonte") {
                    // top10_comuni_positivi1000
                    $this->src('covid_19_piemonte_daily')
                    ->query("SELECT * FROM "._MYSQL_TABLE_D." where provincia=:provincia")
                    ->params(array(":provincia"=>_ENTE_PRINT))
                    ->pipe(new Sort(array(
                        "positivi_per_1000_abitanti"=>"desc",
                    )))
                    ->pipe(new Limit(array(10)))
                    ->pipe($this->dataStore('top10_comuni_positivi1000'));

                    // top10_comuni_positivi
                    $this->src('covid_19_piemonte_daily')
                    ->query("SELECT * FROM "._MYSQL_TABLE_D." where provincia=:provincia")
                    ->params(array(":provincia"=>_ENTE_PRINT))
                    ->pipe(new Sort(array(
                        "positivi"=>"desc",
                    )))
                    ->pipe(new Limit(array(10)))
                    ->pipe($this->dataStore('top10_comuni_positivi'));
                }
            }
        }
    }
}

?>