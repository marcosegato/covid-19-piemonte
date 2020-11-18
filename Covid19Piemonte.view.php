<?php
    use \koolreport\widgets\google\LineChart;
    use \koolreport\widgets\koolphp\Table;

    if($this->dataStore('contagiati_al_giorno')->isEmpty()) {
        $isEmpty        = true;
        $lastDate       = "-";
        $numeroAbitanti = 0;
        $positiviToday  = 0;
        $positiviYest   = 0;
        $positiviMille  = 0;
        $positiviMilYe  = 0;
        $rapportoPoNeg  = 0;
        $rapportoMille  = 0;
        $rapportoCss    = "bg-warning";
    } else {
        $isEmpty = false;
        $lastRegDate    = $this->dataStore('contagiati_al_giorno')->bottom(1);
        $lastDate       = DateTime::createFromFormat('Y/m/d', $lastRegDate[0]["Data"])->format('d/m/Y');
        $numeroAbitanti = number_format($lastRegDate[0]["Abitanti"], 0, ',', '.');
        $positiviToday  = number_format($lastRegDate[0]["Positivi"], 0, ',', '.');
        $positiviYest   = number_format($lastRegDate[0]["Positivi"] - $lastRegDate[0]["Delta positivi"], 0, ',', '.');
        $rapportoPoNeg  = ($positiviYest == 0) ? ($positiviToday * 100) : ((round($positiviToday/$positiviYest,4)-1)*100);
        $positiviMille  = $lastRegDate[0]["Positivi 1000 abitanti"];
        $positiviMilYe  = $lastRegDate[0]["Positivi 1000 abitanti"] - $lastRegDate[0]["Delta positivi 1000 abitanti"];
        $rapportoMille  = ($positiviMilYe == 0) ? ($positiviMille * 100) : ((round($positiviMille/$positiviMilYe,4)-1)*100);
        $positiviMille  = number_format($positiviMille, 2, ',', '.');
        $positiviMilYe  = number_format($positiviMilYe, 2, ',', '.');
        switch (true) {
            case ($positiviToday == 0):
                $rapportoCss = "bg-success";
                break;
            case ($rapportoPoNeg > 0):
                $rapportoCss = "bg-danger";
                break;
            default:
                $rapportoCss = "bg-warning";
                break;
        }
    }
?>
                        <h1 class="mt-4">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo (($isEmpty) ? ("Nessuno") : _ENTE); ?>
                        </h1>
                        <div class="input-group input-group-lg mb-4">
                            <div class="input-group-prepend">
                                <label class="input-group-text bg-dark text-white" for="elencoenti">Ente</label>
                            </div>
                            <select class="selectpicker" data-style="btn btn-outline-secondary btn-lg" id="elencoenti" data-live-search="true" onchange="javascript:location.href = this.value;">
                                <option selected>Seleziona...</option>
                                <option value='index.php'>Piemonte</option>
                                <option data-divider="true"></option>
                                <?php
                                require_once "lista_comuni.php";
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body"><h1><?php echo $lastDate; ?></h1></div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <div class="small text-white stretched-link"><i class="fas fa-calendar-alt"></i> Data di osservazione</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body"><h1><?php echo $numeroAbitanti; ?></h1></div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <div class="small text-white stretched-link"><i class="fas fa-users"></i> Numero totale di abitanti</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card <?php echo $rapportoCss; ?> text-white mb-4 koolphp-card">
                                    <div class="card-indicator">
                                        <span title="">
                                            <?php echo number_format($rapportoPoNeg, 2, ',', '.'); ?>% <i class="fas fa-caret-<?php if($rapportoPoNeg>=0) echo "up"; else echo "down";?>"></i>
                                        </span>
                                    </div>
                                    <div class="card-body"><h1><?php echo $positiviToday; ?> <small class="text-white"><?php echo "(".$positiviYest.")";?></small></h1></div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <div class="small text-white stretched-link"><i class="fas fa-calculator"></i> Numero di positivi</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card <?php echo $rapportoCss; ?> text-white mb-4 koolphp-card">
                                    <div class="card-indicator">
                                        <span title="">
                                            <?php echo number_format($rapportoMille, 2, ',', '.'); ?>% <i class="fas fa-caret-<?php if($rapportoMille>=0) echo "up"; else echo "down";?>"></i>
                                        </span>
                                    </div>
                                    <div class="card-body"><h1><?php echo $positiviMille; ?> <small class="text-white"><?php echo "(".$positiviMilYe.")";?></small></h1></div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <div class="small text-white stretched-link"><i class="fas fa-chart-pie"></i> Positivi ogni 1000 abitanti</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-area mr-1"></i>
                                        Andamento storico
                                    </div>
                                    <div class="card-body">
                                        <?php
                                            LineChart::create(array(
                                                "dataStore"=>$this->dataStore('contagiati_al_giorno'),
                                                "columns"=>array(
                                                    "Data"=>array(
                                                        "label"=>"Data",
                                                        "type"=>"datetime",
                                                        "format"=>"Y/m/d",
                                                        "displayFormat"=>"d/m/Y",
                                                    ),
                                                    "Positivi"=>array(
                                                        "label"=>"Positivi",
                                                        "type"=>"number",
                                                        "thousandSeparator"=>".",
                                                        "prefix"=>"",
                                                    ),
                                                    "Positivi 1000 abitanti"=>array(
                                                        "label"=>"Positivi ogni 1000 abitanti",
                                                        "type"=>"number",
                                                        "decimals"=>2,
                                                        "thousandSeparator"=>".",
                                                        "decimalPoint"=>",",
                                                        "prefix"=>"",
                                                    )
                                                ),
                                                "options"=>array(
                                                    "responsive"=>true,
                                                    "curveType"=>"function",
                                                    "series"=> array(
                                                        0=> array("targetAxisIndex"=> 0),
                                                        1=> array("targetAxisIndex"=> 1, "visibleInLegend"=>false, "enableInteractivity"=>false),
                                                    ),
                                                    "vAxes"=>array(
                                                        0=> array("title"=> 'Numero di Positivi'),
                                                        1=> array("title"=> 'Positivi ogni 1000 abitanti')
                                                    ),
                                                ),
                                                "colorScheme"=>array("FF0000",""),
                                            ));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-table mr-1"></i>
                                        Dettaglio dello storico giornaliero
                                    </div>
                                    <div class="card-body"> -->
                                        <?php
                                            // Table::create(array(
                                            //     "dataSource"=>$this->dataStore('contagiati_al_giorno'),
                                            //     "columns"=>array(
                                            //         "Ente",
                                            //         "Provincia",
                                            //         "Abitanti",
                                            //         "Positivi",
                                            //         "Positivi 1000 abitanti",
                                            //         "Data"=>array(
                                            //             "type"=>"datetime",
                                            //             "format"=>"Y/m/d",
                                            //             "displayFormat"=>"d/m/Y",
                                            //         ),
                                            //     ),
                                            //     "paging"=>array(
                                            //         "pageSize"=>10,
                                            //         "pageIndex"=>0,
                                            //     ),
                                            // ));
                                        ?>
                                    <!-- </div>
                                </div>
                            </div>
                        </div> -->