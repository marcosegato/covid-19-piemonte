<?php

require_once "config.php";
require_once "koolreport/core/autoload.php";

use \koolreport\KoolReport;
use \koolreport\processes\Filter;
use \koolreport\processes\Group;


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
                    )
                )
            );
        }
    }

    protected function setup()
    {
        if(_ENV == "dev") {
            $this->src('covid_19_piemonte')
            ->pipe(new Filter(array(
                array("Ente","=",_ENTE)
            )))
            ->pipe($this->dataStore('contagiati_al_giorno'));
        }

        if(_ENV == "prod") {
            $this->src('covid_19_piemonte')
            ->query("SELECT * FROM "._MYSQL_TABLE_HIST." where Ente=:ente")
            ->params(array(":ente"=>_ENTE))
            ->pipe($this->dataStore('contagiati_al_giorno'));
        }
    }
}

?>