<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="COVID-19 Regione Piemonte: storico giornalieri dei positivi" />
        <meta name="author" content="Marco Segato" />
        <title>COVID-19 Piemonte</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/card.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-86422-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-86422-1');
        </script>

    </head>
    <body class="sb-nav-fixed">
        <?php
            if(isset($_GET["ente"])) {
                define("_ENTE", htmlspecialchars($_GET["ente"]));
            } else {
                define("_ENTE", "Piemonte");
            }
            if(isset($_GET["tipo"])) {
                define("_TIPO", htmlspecialchars($_GET["tipo"]));
            } else {
                define("_TIPO", "");
            }
        ?>
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">COVID-19 Piemonte</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Analisi</div>
                            <a class="nav-link" href="index.php?tipo=REG">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Regione Piemonte
                            </a>
                            <a class="nav-link" href="index.php?tipo=PROV">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Province
                            </a>
                            <a class="nav-link" href="index.php?tipo=COM">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Comuni
                            </a>
                            <div class="sb-sidenav-menu-heading">Info</div>
                            <a class="nav-link" href="https://marcosegato.altervista.org/" target="_blank">
                                <div class="sb-nav-link-icon"><i class="fas fa-at"></i></div>
                                Author
                            </a>
                            <a class="nav-link" href="#credits">
                                <div class="sb-nav-link-icon"><i class="fas fa-hands-helping"></i></div>
                                Credits
                            </a>
                            <a class="nav-link" href="https://github.com/marcosegato/covid-19-piemonte" target="_blank">
                                <div class="sb-nav-link-icon"><i class="fas fa-code"></i></div>
                                Source code
                            </a>
                            <a class="nav-link" href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank">
                                <div class="sb-nav-link-icon"><i class="fas fa-copyright"></i></div>
                                License
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                            <p><strong>INFO</strong>: L'aggiornamento della mappa dei contagi è sospeso.</p>
                            <hr/>
                            <p class="small mb-0">
                                Dal 31.12.2022, con l'applicazione delle indicazioni contenute nella circolare ministeriale "Aggiornamento delle modalità di gestione dei casi e dei contatti stretti di caso COVID-19 del 31.12.2022 Prot. 0051961-31/12/2022-DGPRE-DGPRE-P", non è più richiesto l'esito negativo del test per sospendere l'isolamento, se non in casi specifici. Pertanto, il dato quotidiano relativo al numero di persone attualmente positive non risulta più quantificabile.
                            </p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Chiudi">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <?php
                            require_once "Covid19Piemonte.php";
                            $report = new Covid19Piemonte;
                            $report->run()->render();
                        ?>

                        <div class="card mb-4" id="credits">
                            <div class="card-header">
                                <i class="fas fa-info-circle mr-1"></i>
                                Disclaimer and credits
                            </div>
                            <div class="card-body">
                                <p>Soggetti risultati positivi al test covid-19 che non risultano deceduti o guariti. L'indicatore "attualmente positivi" è stato adeguato alla circolare del 12/10/2020 "COVID-19: indicazioni per la durata ed il termine dell'isolamento e della quarantena".</p>
                                <p>
                                Open Data repository (storico): <a href="https://github.com/marcosegato/covid-19-piemonte-data-scraper" target="_blank">https://github.com/marcosegato/covid-19-piemonte-data-scraper</a><br/>
                                Open Data repository (corrente): <a href="https://github.com/floatingpurr/covid-piemonte" target="_blank">https://github.com/floatingpurr/covid-piemonte</a><br/>
                                Original Data source: <a href="https://www.regione.piemonte.it/web/covid-19-mappa-dei-contagi-piemonte" target="_blank">https://www.regione.piemonte.it/web/covid-19-mappa-dei-contagi-piemonte</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">
                                <i class="fas fa-at"></i> <a href="https://marcosegato.altervista.org/" target="_blank">Author</a>
                                &middot;
                                <i class="fas fa-code"></i> <a href="https://github.com/marcosegato/covid-19-piemonte" target="_blank">Code</a>
                                &middot;
                                <i class="fab fa-creative-commons"></i> <a href="https://creativecommons.org/publicdomain/zero/1.0/" target="_blank">License</a>
                            </div>
                            <div>
                                <i class="fas fa-file-alt"></i> <a href="https://marcosegato.altervista.org/cookie-policy" target="_blank">Cookies</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
        <script src="js/scripts.js"></script>
        <script src="js/cookie_policy.js"></script>
        <script src="js/cookieconsent.latest.min.js"></script>
    </body>
</html>
