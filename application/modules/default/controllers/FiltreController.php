<?php

class FiltreController extends Genius_AbstractController
{
    public function init()
    {
        Zend_Layout::getMvcInstance()->setLayout('gv');
    }
    /**
     * Lorsque l'on clique sur une section du configurateur : ex : imprimante Etiquette : thermique
     * cela va chercher des element en session et chercher les resultat pré en registrer.
     */
    public function indexAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('gv');

        $this->view->slider = "statics/geo/slider.phtml";
        $this->view->infotel = "statics/geo/infotel.phtml";
        $this->view->active = 'index';
        $this->view->filter = "statics/geo/filter.phtml";
        $this->view->explication = "statics/geo/explication_configurator.phtml";
        $this->view->autocomplete = "statics/geo/search_autocomplete.phtml";
        $this->view->subheader = "statics/subheader.phtml";
        $this->view->panier = "statics/geo/icone_panier.phtml";

        $session = new Zend_Session_Namespace('filtre');
        //var_dump($session->search);


        if($session->search == null ){
            $baseUrl = new Zend_View_Helper_BaseUrl();
            $this->sessionEmpty();
            return $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur/aide');
        }

        $dispatcher = new Genius_Class_dispatchFilter($session);

        $dispatcher->result();

        $error = new Zend_Session_Namespace('errormessage');

        $this->view->fake = $this->fakeDescription();
        $this->view->bulle = $this->infoBulle();
        $this->view->result = $dispatcher->getResult();
        $this->view->input = $dispatcher->getInput();
        $this->view->message = $session->message;
        $this->view->error = $error->msg;
        $this->view->search = $session->search;
        $this->view->choice = count($session->choice);
    }

    /**
     * route : filtre/apply
     * Applique le filtre en fonction de la section choisit et des inputs
     */
    public function makefiltreAction()
    {
        // Instance de la session
        $session = new Zend_Session_Namespace('filtre');
        $delaisSession = $session->setExpirationSeconds( 600);

        //var_dump(session_cache_expire ($delaisSession));
        //die();

        // Instance de la classe qui vas gerer a tout filtrer et faire
        // La recherche dans la base de donnée
        $filtering = new Genius_Class_FilteringPrinterThermique($_POST);

        if($session->search == 'search_etiquette_couleur')              $filtering = new Genius_Class_FilteringPrinterCouleur($_POST);
        if($session->search == 'search_etiquette_portable')             $filtering = new Genius_Class_FilteringPrinterPortable($_POST);
        if($session->search == 'search_etiquette_badgeuse')             $filtering = new Genius_Class_FilteringPrinterBadgeuse($_POST);
        if($session->search == 'search_printer_laser')                  $filtering = new Genius_Class_FilteringPrinterLaser($_POST);
        if($session->search == 'search_printer_matricielle')            $filtering = new Genius_Class_FilteringPrinterMatricielle($_POST);

        if($session->search == 'search_douchette')                      $filtering = new Genius_Class_FilteringDouchette($_POST);
        if($session->search == 'search_douchette_ring')                 $filtering = new Genius_Class_FilteringDouchetteRing($_POST);

        if($session->search == 'search_terminal')                       $filtering = new Genius_Class_FilteringTerminal($_POST);
        if($session->search == 'search_terminal_pda')                   $filtering = new Genius_Class_FilteringTerminalPda($_POST);
        if($session->search == 'search_terminal_embarque')              $filtering = new Genius_Class_FilteringTerminalEmbarque($_POST);
        if($session->search == 'search_terminal_poignet')               $filtering = new Genius_Class_FilteringTerminalPoignet($_POST);

        if($session->search == 'search_poste_client')                   $filtering = new Genius_Class_FilteringPosteClient($_POST);
        if($session->search == 'search_poste_pc')                       $filtering = new Genius_Class_FilteringPostePc($_POST);
        if($session->search == 'search_poste_portable')                 $filtering = new Genius_Class_FilteringPostePortable($_POST);

        //Gestion du filtre ----
        $filtering
            ->setSession($session)
            ->handle()
            ->search()
            ->setResult()
        ;

        $baseUrl = new Zend_View_Helper_BaseUrl();
        $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur');
    }

    /**
     * route : /filtre/form?f=xxxx
     * Change de section Imprimante / Douchette / Terminal
     */
    public function makefiltreformAction()
    {
        $session = new Zend_Session_Namespace('filtre');

        $this->resetEmptySession();

        $session->search = $_GET['f'];

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur');
    }

    /**
     * Reset des resultat
     */
    public function deletefiltreAction()
    {
        $session = new Zend_Session_Namespace('filtre');

        unset($session->resultTerminal);
        unset($session->resultTerminalPda);
        unset($session->resultTerminalEmbarque);
        unset($session->resultTerminalPoignet);

        unset($session->resultDouchette);

        unset($session->resultThermique);
        unset($session->resultEtiquetteCouleur);
        unset($session->resultEtiquettePortable);
        unset($session->resultEtiquetteBadgeuse);
        unset($session->resultPrinterLaser);
        unset($session->resultPrinterMatricielle);

        unset($session->resultPosteClient);
        unset($session->resultPostePc);
        unset($session->resultPostePortable);

        if ($session->search == 'search_thermique' )                unset($session->inputThermique) ;
        elseif($session->search == 'search_etiquette_couleur' )     unset($session->inputEtiquetteCouleur) ;
        elseif($session->search == 'search_etiquette_portable' )    unset($session->inputEtiquettePortable) ;
        elseif($session->search == 'search_etiquette_badgeuse' )    unset($session->inputEtiquetteBadgeuse) ;
        elseif($session->search == 'search_printer_laser' )         unset($session->inputPrinterLaser) ;
        elseif($session->search == 'search_printer_matricielle' )   unset($session->inputPrinterMatricielle) ;
        elseif($session->search == 'search_douchette' )             unset($session->inputDouchette) ;
        elseif($session->search == 'search_douchette_ring' )        unset($session->inputDouchetteRing) ;
        elseif($session->search == 'search_terminal' )              unset($session->inputTerminal) ;
        elseif($session->search == 'search_terminal_pda' )          unset($session->inputTerminalPda) ;
        elseif($session->search == 'search_terminal_embarque' )     unset($session->inputTerminalEmbarque) ;
        elseif($session->search == 'search_terminal_poignet' )      unset($session->inputTerminalPoignet) ;
        elseif($session->search == 'search_poste_pc' )              unset($session->inputPostePc) ;
        elseif($session->search == 'search_poste_client' )          unset($session->inputPosteClient) ;
        elseif($session->search == 'search_poste_portable' )        unset($session->inputPostePortable) ;
        else{
            unset($session->inputThermique) ;
            unset($session->inputDouchette);
            unset($session->inputDouchetteRing);
            unset($session->inputEtiquetteCouleur);
            unset($session->resultEtiquettePortable);
            unset($session->resultEtiquetteBadgeuse);
            unset($session->resultPrinterLaser);
            unset($session->resultPrinterMatricielle);

            unset($session->inputTerminal);
            unset($session->inputTerminalPda) ;
            unset($session->inputTerminalEmbarque) ;
            unset($session->inputTerminalPoignet) ;

            unset($session->inputPosteClient);
            unset($session->inputPostePc);
            unset($session->inputPostePotable);
        }

        $baseUrl = new Zend_View_Helper_BaseUrl();
        $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur');
    }

    /**
     * Choisit un materiel est le met dans le panier.
     */
    public function choiceAction()
    {
        global $db;

        $exception=[
            'search_terminal_poignet',
            'search_terminal_poignet_',
            'search_terminal_embarque',
        ];

        $session = new Zend_Session_Namespace('filtre');

        $currentCounter = count($session->choice);

        $isInException =
            array_search($session->search, $exception) === false ? false : true;

        $input = $this->getInput($session);

        $this->modelFiltre = $this->getModel($session);

        $this->words = $this->setWordTranslation();

        $inputFormat = $this->formatInput($input);

        $id = $_GET['product'];
        $choice = [];

        if(
            ($session->search == 'search_poste_portable' )
            or
            ($session->search == 'search_poste_pc' )
            or
            ($session->search == 'search_etiquette_couleur' )
        )
        {
            $product =  is_numeric($id) ? $this->modelFiltre->selectGenerique($id) : null ;
        }
        else{
            $product =  is_numeric($id) ? $this->modelFiltre->select($id) : null ;
        }

        $baseUrl = new Zend_View_Helper_BaseUrl();

        // test si le produit exist
        if($product == null ) {
            $this->getResponse()->setRedirect($baseUrl->baseUrl().'/');
        }
        else{

            $result = $db->query($product)->fetch();


            $image = UPLOAD_URL.'images/'.$result['path_folder'].'/'.$result['filename'].'-small-'.$result['id_img'].'.'.$result['format'];

            $productName = strtoupper (substr($result['search'],0,strpos($result['search'], ' '))).' - '.strtoupper($result['nom']);

                if($session->search == 'search_poste_pc'){
                    $image = UPLOAD_URL.'images/geo/fiche_pc_'.$result['nom'].'.jpg';
                    $productName = 'PC - '.$result['nom'];
                }
                elseif( $session->search == 'search_poste_portable'){
                    $image = UPLOAD_URL.'images/geo/fiche_portable_'.$result['nom'].'.jpg';
                    $productName = 'Portable - '.$result['nom'];
                }
                elseif( $session->search == 'search_etiquette_couleur'){
                    $image = UPLOAD_URL.'images/geo/fiche_couleur_'.strtolower($result['nom']).'.jpg';
                    $productName = 'Imprimante etiquette couleur - '.strtolower($result['nom']);
                }

                $choice=
                    [
                        'input' => $inputFormat,
                        'choice' => $productName,
                        'section' => $this->setWordTranslation()[$session->search] == null ? $session->search : $this->setWordTranslation()[$session->search],
                        'qte' => 1,
                        'image' => $image,
                    ];

                $session->choice[$id] =$choice;

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
            {
                $newCounter = count($session->choice);

                if( $newCounter == $currentCounter ){
                    return $this->_helper->json(
                        [
                            'count' =>  count($session->choice),
                            'msg' => 'Article est deja ajouté ! ',
                            'state' => 0,
                        ]
                    );
                }

                if($session->search == 'search_poste_portable' or $session->search == 'search_poste_pc'or $session->search == 'search_etiquette_couleur'){

                    $caracteristique  = 'Résumer détaillé de la configuration afficher a l\'étape 3';

                    return $this->_helper->json(
                        [
                            'article' => $productName,
                            'description' => '',
                            'carac' => $caracteristique,
                            'image' => $image,
                            'count' =>  count($session->choice),
                            'msg' => 'L\'article a bien  été ajouté !',
                            'state' => 1,
                        ]
                    );
                }
                else{
                    $caracteristique  = 'Caractéristiques : ' .$result['carac_1'] . ' - '.$result['carac_2'] . ' - '.$result['carac_3'] . ' - '.$result['carac_6'] . '...';
                    return $this->_helper->json(
                        [
                            'article' => $productName,
                            'description' => strip_tags(substr($result['text'],0,500),''),
                            'carac' => $caracteristique,
                            'image' => $image,
                            'count' =>  count($session->choice),
                            'msg' => 'L\'article a bien  été ajouté !',
                            'state' => 1,
                        ]
                    );
                }

            }
            else{
                // Gestion redirection multi selection exemple un terminal poignet avec un scanner ring associé.
                if($isInException){

                    if( ($session->search == 'search_terminal_poignet') AND ($session->inputTerminalPoignet['option'] !== null))
                    {
                        $session->message = '<b>ETAPE 2</b> : Valider le ring scanner avec le bouton caddy afin de faire votre demande de devis';
                        $session->search = 'search_douchette_ring';
                        return $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur');
                    }

                    if( ($session->search == 'search_terminal_embarque') AND ($session->inputTerminalEmbarque['option']['douchette'] !== null))
                    {
                        $session->message = '<b>ETAPE 2</b> : Valider la douchette avec le bouton caddy afin de faire votre demande de devis';
                        $session->search = 'search_douchette';
                        return $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur');
                    }
                }
                $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur/panier');
            }


        }
    }

    /**
     * nous donne la vue , des elements selectionné ,
     * si panier vide redirection sur l'index
     */
    public function panierAction()
    {
        $this->view->headTitle()->append('Pannier - ');

        $session = new Zend_Session_Namespace('filtre');
        $session->setExpirationSeconds( 600);

        $baseUrl = new Zend_View_Helper_BaseUrl();

        if($session->choice == null)
            $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur') ;

        $this->view->panier =  $session->choice;
    }

    /**
     * Nous donne les input renseigner par l'utilisateur
     * @return mixed
     */
    public function getInput($session )
    {
        //Imprimante
        if ($session->search == 'search_thermique') return $session->inputThermique;
        if ($session->search == 'search_badgeuse') return $session->inputPrinter;
        if ($session->search == 'search_etiquette_couleur') return $session->inputEtiquetteCouleur;
        if ($session->search == 'search_etiquette_portable') return $session->inputEtiquettePortable;
        if ($session->search == 'search_etiquette_badgeuse') return $session->inputEtiquetteBadgeuse;
        if ($session->search == 'search_printer_laser') return $session->inputPrinterLaser;
        if ($session->search == 'search_printer_matricielle') return $session->inputPrinterMatricielle;

        //Terminal
        if ($session->search == 'search_terminal') return $session->inputTerminal;
        if ($session->search == 'search_terminal_pda') return $session->inputTerminalPda;
        if ($session->search == 'search_terminal_embarque') return $session->inputTerminalEmbarque;
        if ($session->search == 'search_terminal_poignet') return $session->inputTerminalPoignet;

        //Douchette
        if ($session->search == 'search_douchette') return $session->inputDouchette;
        if ($session->search == 'search_douchette_ring') return $session->inputDouchetteRing;
        if ($session->search == 'search_scanner_fixe') return $session->inputScannerFixe;

        //poste de travail
        if ($session->search == 'search_poste_pc') return $session->inputPostePc;
        if ($session->search == 'search_poste_portable') return $session->inputPostePortable;
        if ($session->search == 'search_poste_client') return $session->inputPosteClient;

    }

    /**
     * Nous donne les input renseigner par l'utilisateur
     * @return mixed
     */
    public function getModel($session)
    {
        //Imprimante
        if ($session->search == 'search_thermique') return new Genius_Model_Filtre();
        if ($session->search == 'search_etiquette_badgeuse') return new Genius_Model_FiltreEtiquetteBadgeuse();
        if ($session->search == 'search_etiquette_couleur') return new Genius_Model_FiltreEtiquetteCouleur();
        if ($session->search == 'search_etiquette_portable') return new Genius_Model_FiltreEtiquettePortable();
        if ($session->search == 'search_printer_laser') return new Genius_Model_FiltrePrinterLaser();
        if ($session->search == 'search_printer_matricielle') return new Genius_Model_FiltrePrinterMatricielle();

        //Terminal
        if ($session->search == 'search_terminal') return new Genius_Model_FiltreTerminal();
        if ($session->search == 'search_terminal_pda') return new Genius_Model_FiltreTerminalPda();
        if ($session->search == 'search_terminal_embarque') return new Genius_Model_FiltreTerminalEmbarque();
        if ($session->search == 'search_terminal_poignet') return new Genius_Model_FiltreTerminalPoignet();

        //Douchette
        if ($session->search == 'search_douchette') return new Genius_Model_FiltreDouchette();
        if ($session->search == 'search_douchette_ring') return new Genius_Model_FiltreDouchetteRing();
        //if ($session->search == 'search_scanner_fixe') return $session->inputScannerFixe;

        //poste de travail
        if ($session->search == 'search_poste_pc')  return new Genius_Model_FiltrePostePc();
        if ($session->search == 'search_poste_portable') return new Genius_Model_FiltrePostePortable();
        if ($session->search == 'search_poste_client') return new Genius_Model_FiltrePosteClient();

        return new Genius_Model_Filtre();
    }

    /**
     * Tableau de translations des mots
     * @param $input
     */
    public function setWordTranslation()
    {
        return
            [
                'search_thermique' => 'imprimante etiquette thermique',
                'search_etiquette_couleur' => 'imprimante etiquette couleur',
                'search_etiquette_portable' => 'imprimante etiquette portative',
                'search_etiquette_badgeuse' => 'imprimante badgeuse',
                'search_printer_laser' => 'imprimante laser',
                'search_printer_matricielle' => 'imprimante matricielle',
                'search_douchette' => 'lecteur code barre',
                'search_douchette_ring' => 'ring scanner',
                'search_terminal' => 'terminal mobile',
                'search_terminal_pda' => 'terminal pda',
                'search_terminal_embarque' => 'terminal embarqué',
                'search_terminal_poignet' => 'terminal poignet',
                'search_poste_pc' => 'poste de travail',
                'search_poste_client' => 'client leger',
                'search_poste_portable' => 'pc portable',
                'conso' => 'Consommable & soft',
                'dimension' => 'Dimension étiquette',
                'dpi' => 'Résolution',
                'gamme' => 'Gamme du matériel',
                'width' => 'Largeur d\'impression',
                'opt' => 'Options',
                'optiond' => 'Options de la douchette',
                'use' => 'Utilisation',
                'marque' => 'Marque',
                'dd' => 'Disque dur',
                'interface' => 'Interface de connection',
                'interfaced' => 'Interface de connection ',
                'interfaceb' => 'Interface de connection de la base',
                'printhead' => 'avec une tête supplémentaire',
                'software' => 'logiciel',
                'ruban' => 'ruban carbone',
                'na' => 'non-définie',
                'all' => 'pas de préferences',

                'bureau' => 'bureau',
                'si' => 'semi-industriel',
                'i' => 'industriel',
                'indu' => 'industriel',

                'peel' => 'pré-décollage',
                'cut' => 'massicot',
                'rfid' => 'rfid',
                'rewind' => 'pré-imprimer',

                'feuille' => 'impression feuille a feuille',

                'ligne' => 'impression ligne',
                'aiguille' => 'impression aiguille',
                'tt' => 'transfert-thermique',
                'dt' => 'direct-thermique',
                'both' => 'les deux',

                'grand_froid' => 'grand froid',
                'gf' => 'grand froid',
                'clavier_dur' => 'clavier durcit',
                'picture' => 'appareil photo',
                'vocal' => 'à commande vocal',
                'dual_display' => 'double affichage',

                '2gb_ram' => '2GB ram',
                '4gb_ram' => '4GB ram',
                '8gb_ram' => '8GB ram',
                '16gb_ram' => '16GB ram',

                '2gb_flash' => '2GB flash',
                '4gb_flash' => '4GB flash',
                '8gb_flash' => '8GB flash',
                '16gb_flash' => '16GB flash',
                '32gb_flash' => '32GB flash',
                '64gb_flash' => '64GB flash',

                'ram' => 'Mémoire',
                'core' => 'Processeur',
                'ecran' => 'Ecran',

                '160gb' => '160 GB',
                '250gb' => '250 GB',
                '320gb' => ' 320GB',
                '500gb' => '500 GB',
                '1000gb' => '1 TO',

                'ssd' => 'ssd',

                'gun' => 'avec poignet',
                'droit' => 'sans poignet',

                'tour' => 'tour',
                'sff' => 'sff',
                'usff' => 'usff',

                'pao' => 'pao',
                'cao' => 'cao',

                'core_i3' => 'core i3',
                'core_i5' => 'core i5',
                'core_i7' => 'core i7',

                'nume' => 'numérique',
                'alpha_numeric' => 'alpha-numérique',
                'alpha' => 'alpha-numérique',
                'pave_nume' => 'pavé numérique',

                'com' => 'communication et option',

                '12p' => '12 "',
                '13p' => '13 "',
                '14p' => '14 "',
                '15p' => '15 "',
                '15_6p' => '15,6 "',
                '17p' => '17 "',
                '22p' => '22 "',
                '24p' => '24 "',
                '19p_4' => '19" 4/3',
                '19p_16' => '19" 16/9',

                'os' => 'Système d\'exploitation',
                'wince' => 'windows CE',
                'win' => 'windows',
                'linux' => 'linux',
                'propri' => 'propriétaire',
                'ce' => 'windows CE',
                'mo' => 'windows mobile',
                'android' => 'android',
                'win_embeded_std' => 'windows embeded standard',
                'win_embeded_compact' => 'windows embeded compact',
                'winmobile' => 'windows mobile',
                'win_7' => 'windows 7',
                'win_10' => 'windows 10',
                'win_xp_pro' => 'windows xp pro',
                'win_xp_embeded' => 'windows xp embeded',

                'win_7_pro' => 'windows 7 pro',
                'win_10_pro' => 'windows 10 pro',
                'win_cp_pro' => 'windows xp pro',


                'serie' => 'série',
                'usb' => 'usb',
                'eth' => 'réseau filaire',
                'wifi' => 'wifi',
                'parra' => 'parralléle',

                'm_zebra' => 'ZEBRA',
                'm_datamax' => 'DATAMAX',
                'm_intermec' => 'INTERMEC',
                'm_toshiba' => 'TEC / TOSHIBA',
                'm_honeywell' => 'HONEYWELL',
                'm_hp' => 'HEWLETT PACKARD',
                'm_lexmark' => 'LEXMARK',
                'm_ibm' => 'IBM',
                'm_epson' => 'EPSON',
                'm_oki' => 'OKI',
                'm_motorola' => 'MOTOROLA',
                'm_symbol' => 'SYMBOL',
                'm_datalogic' => 'DATALOGIC',
                'm_opticon' => 'OPTICON',
                'm_lxe' => 'LXE',
                'm_psion' => 'PSION',

                'narrow' => 'narrow band',

                '2' => '2"',
                '3' => '3"',
                '4' => '4"',
                '5' => '5"',
                '6' => '6"',
                '8' => '8"',

                'filaire' => 'filaire',
                'nofilaire' => 'sans-fil',
                'radio' => 'radio',


                'c_132' => '132 colonnes',
                'c_80' => '80 colonnes',

                '200' => '200 dpi',
                '300' => '300 dpi',
                '600' => '600 dpi',
                '1200' => '1200 dpi',

                'a3' => 'format A3',
                'a4' => 'format A4',

                'groupe_petit' => 'petit groupe de travail',
                'groupe_dep' => 'groupe de travail départemental',

                'mono' => 'impression noir & blanc',
                'couleur' => 'impression couleur',

                'rectov' => 'recto / verso',

                'one_face' => 'mode impression une face',
                'dual_face' => 'mode impression recto / verso',
                'magnetique' => 'lecteur carte magnétique',
                'puce' => 'lecteur carte puce',
                'nfc' => 'système NFC',
                'serrure' => 'système vérouillage par clé',

                '1std' => '1d standard',
                '1d' => '1d standard',
                'scanner_1d' => 'avec scanner 1d ',
                'scanner_2d' => 'avec scanner 2d ',
                '1lg' => '1d longue portée',
                '1xlg' => '1d trés longue portée',

                '2std' => '2d standard',
                '2d' => '2d standard',
                '2lg' => '2d longue portée',

            ];
    }

    /**
     * Format les input selectionné par le client pour que se soit compréhensible a la lecture
     * @param $input
     * @return array|string
     */
    private function formatInput($input)
    {
        if($input == null )
            return 'Aucunes précisions sur les caractèristiques'
        ;

        $items=[];
        $selected=[];

        foreach ($input as $index => $item)
        {
            if( ($item !== '') AND ($item !== null) AND ($item !== 'na') )
            {
                $item_ = $item;
                $cle = $this->words[$index] == null ? $index : $this->words[$index] ;
                $valeur = $this->words[$item] == null ? $item : $this->words[$item] ;

                $selected_item=[];
                $listing=[];

                if( is_array($item_) )
                {
                    foreach ($item as $sub_key => $sub_item) {
                        $sub_cle = $this->words[$sub_key] == null ? $sub_key : $this->words[$sub_key] ;
                        $selected_item[] = $sub_cle;
                        $listing[$valeur] = $selected_item;
                    }
                    $items[$cle] = $selected_item;
                }

                if( ! is_array($item_) )  $selected[$cle] = $valeur;
            }
        }

        $array_merged = $selected;

        if( ! empty($items) )
            $array_merged = array_merge($selected,$items) ;


        return $array_merged;
    }

    /**
     * Delete un materiel selectionné par le client.
     */
    public function deletechoiceAction()
    {
        $id = $_GET['product'];

        $session = new Zend_Session_Namespace('filtre');

        unset($session->choice[$id]);
        unset($session->message);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
        {
            return $this->_helper->json($id);
        }
        else{
            $baseUrl = new Zend_View_Helper_BaseUrl();
            $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur/panier');
        }
    }

    /**
     * rajoute une unité pour la demande de devis
     * @return \Zend_Controller_Response_Abstract
     */
    public function addqteAction()
    {
        $id =  $_GET['product'];

        $session = new Zend_Session_Namespace('filtre');
        $baseUrl = new Zend_View_Helper_BaseUrl();

        if($session->choice == null)
        {
            $this->sessionEmpty();

            return $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur/aide');
        }

        $session->choice[$id]['qte'] += 1;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
        {
            return $this->_helper->json($id);
        }
        else{
            $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur/panier');
        }

    }

    /**
     * soustrait une unité a la demande de devis
     * @return \Zend_Controller_Response_Abstract
     */
    public function subqteAction()
    {
        $id =  $_GET['product'];

        $session = new Zend_Session_Namespace('filtre');

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $isSubable = $session->choice[$id]['qte'] == 1 ? false: true;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
        {
            if($isSubable)
                $session->choice[$id]['qte'] -= 1 ;

            return $this->_helper->json($id);
        }

        if( $session->choice[$id]['qte'] > 1 )
            $session->choice[$id]['qte'] -= 1;


            return $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur/panier');
    }

    /**
     * Crée une nouvelle session sur l'état de la session en cours. si session vide : on ecrit un msg d'alerte
     */
    public function sessionEmpty()
    {
        $session = new Zend_Session_Namespace('session');
        $session->setExpirationSeconds( 5);
        $session->msg = 'votre panier est <b>vide</b> ou la session en cours a été ré-initialiser par mesure de sécurité. <br> N\'hesiter pas a utiliser notre <u>configurateur</u> , afin de dimensionné votre materiel. ';
    }

    /**
     * Supprime la session de l'état de session en cours.
     */
    private function resetEmptySession()
    {
        $msgEmptySession = new Zend_Session_Namespace('session');

        unset($msgEmptySession->msg);

        unset($msgEmptySession->empty);
    }

    /**
     * page de la logic concernant la reception de la demande
     * recherche de la session en cours
     * enregistrement en bdd de la demande
     * envoi du mail de la demande
     * redirection sur index plus message de validation succes envoie
     * @return \Zend_Controller_Response_Abstract
     */
    public function demandeAction()
    {
        $session = new Zend_Session_Namespace('filtre');

        if($session->choice == null)
        {
            $baseUrl = new Zend_View_Helper_BaseUrl();
            $this->sessionEmpty();
            return $this->getResponse()->setRedirect($baseUrl->baseUrl().'/configurateur/aide');
        }

        $this->recordInDb($session->choice);

        $this->sendMail($session->choice);

        $session = new Zend_Session_Namespace('session');
        $filtre = new Zend_Session_Namespace('filtre');

        $this->resetSessionResult($filtre);

        $filtre->setExpirationSeconds(1);
        $session->setExpirationSeconds( 5);

        $session->success = true;
        $session->sucessMsg = 'La demande de devis informatif a bien été envoyé. <br> N\'hesiter pas a utiliser notre configurateur , afin de rechercher du materiel. ';

        $baseUrl = new Zend_View_Helper_BaseUrl();
        return $this->getResponse()->setRedirect($baseUrl->baseUrl().'/');
    }

    /**
     * Reset le namespace de la session recherche / choix / resultat /pannier
     * @param $filtre
     */
    private function resetSessionSearch($filtre)
    {
        unset($filtre->choice);
        unset($filtre->search);
        unset($filtre);
    }

    /**
     * Reset la session des article dans le pannier
     * @param $filtre
     */
    private function resetSessionResult($filtre)
    {
        unset($filtre->choice);
    }

    /**
     * Enregistrement de la demande en base de donnée
     * @param $choice
     */
    private function recordInDb($choice)
    {
        $date = new DateTime();
        $post = $_POST;
        $rowTable = [];

        foreach ($choice as $index => $item)
        {
            $rowTable=
                [
                    'id_product' => $index,
                    'compagny' => $post['compagny'],
                    'prenom' => $post['prenom'],
                    'nom' => $post['nom'],
                    'tel' => $post['tel'],
                    'email' => $post['email'],
                    'model' => $item['choice'],
                    'qte' => $item['qte'],
                    'inputs' => serialize($item['input']),
                    'info' => $post['info'],
                    'created_at' => $date->format('Y-m-d H:i:s')
                ];

            Genius_Model_Global::insert('ec_filtres_demandes', $rowTable);
        }
    }

    /**
     * Envoi du mail concernant la demande de devis
     * @param $choice
     */
    private function sendMail($choice)
    {
        $assignvalues = array(
            "phtml"=>"info-materiel.phtml",
            "sender"=>'geoffrey.valero@eurocomputer.Fr',
            "receiver"=>"geoffrey.valero@eurocomputer.Fr",
            "addcc"=>"geoffrey.valero@eurocomputer.Fr",
            "subject"=>"Demande de devis - ".$_POST['compagny'],
            "post"=>$_POST,
            "host"=>'Administrateur',
            "input" => $choice,
        );

        $state = Genius_Class_Email::send($assignvalues);
    }

    /**
     * formatage du message a envoyé format STRING
     * @param $message
     * @param $item
     * @return string
     */
    private function saveMESSAGE($message, $item)
    {
        $message .= "<hr>";
        $message .= '<b>'.$item['choice']."</b>\r\n";
        $message .= " - ".$item['section']."\r\n";
        $message .= " - quantité: ".$item['qte']."\r\n";
        $message .= "--------------------------------------------- \r\n";

        foreach ($item['input'] as $i => $input) {
            if (is_array($input))
            {
                $message .= " ".$i."\r\n";
                foreach ($input as $subindex => $subvalue) {
                    $message .= " - ".$subvalue."\r\n";
                }
            }
            else
            {
                $message .= " - ".$i.' : '.$input."\r\n";
            }
        }
        $message .= "\r\n";

        return $message;
    }

    /**
     * Tableau de description des fausses fiches portable / PC et imprimante couleur
     * @return array
     */
    public function fakeDescription()
    {
        return
            [
                'pc_Dell' => 'Le PC Dell est connu pour ses performances en matière de sécurité, il est également doté d\'un châssis très robuste pouvant résister à de nombreux type d\'environnement de travail. 
De plus il est conçu pour prendre en charge jusqu\'à trois écrans. 
Il répondra donc à tous vos besoins de productivité, notamment grâce à ses différents formats disponibles : le format Tour - SFF – USFF.
Le PC Dell est doté d’une capacité allant du 160Go au 320 Go, ainsi que d’un processeur Intel allant du Corei3 au Corei7. 
',
                'pc_HP' => 'Le PC HP est doté : d\'un processeur Intel Core i3 - i5 - i7, d\'un système d\'exploitation Windows 7 ou 10 pro, d\'un disque dur HDD de 250 Go en moyenne et d\'une carte graphique ainsi qu\'une mémoire RAM de 4 Gb de base pouvant être augmentée. 

Le modèle HP est extrêmement performant et s\'adaptera parfaitement à vos besoins notamment grâce à son format SFF idéal pour les espaces restreints, de format USFF ou tour sont également disponibles.  
',
                'pc_Lenovo' => 'Le PC Lenovo est un des ordinateurs de bureau les plus écologiques et les plus économes en énergie, avec des caractéristiques incluant un module d\'alimentation conforme ENERGY STAR et la dernière version de l\'outil Lenovo Power Manager. Ces ordinateurs de bureau utilisent l\'énergie plus efficacement et vous permettent de réaliser des économies importantes sur un cycle de vie de trois ans.
Conçu pour les professionnels, ce modèle offre une administration simplifiée, une sécurité renforcée et une disponibilité maximale aux PME et aux grandes entreprises.
',
                'portable_Lenovo' => 'Le portable Lenovo allie performance et ergonomie, il est robuste est disponible en Windows 7 ou Windows 10. Son écran mesure 15’6 TFT doté d’un traitement anti-reflets. Avec un disque dur allant de 250 à 500 Go il vous offrira d’excellentes performances. 
Il pourra également résister à des projections liquide idéal dans des environnements professionnels. 
',
                'portable_HP' => 'Cet ordinateur portable est tout à fait adapter à des environnements difficiles ainsi qu\'à des utilisations très intenses, grâce à son châssis semi-durci : poussières - températures - vibrations - altitudes - chutes, il répond à toutes ces problématiques. Son processeur Intel Core i5 augmentera vos performances. Il est également doté d\'un écran LED HD traité anti-reflets, extrêmement confortable pour vos utilisateurs. Ce matériel est disponible chez Eurocomputer en Windows 7 et Windows 10 Pro.',
                'etiquette_Epson' => 'Les imprimantes étiquettes jet d’encre couleur Epson répondent aisément à la problématique traditionnelle de la surimpression thermique. 
Elles permettent d’imprimer facilement des étiquettes, des fiches couleur et des tickets personnalisés d’une qualité exceptionnelle, en interne et lorsque vous en avez besoin. 
Disponible en résolution 300 – 600 ou 1200 dpi en fonction de votre besoin.
Vous pourrez imprimer des étiquettes jusqu’à 203 mm de large. 
'
            ];
    }

    public function infoBulle()
    {
        return
            [
                'prt_th_dpi' =>
                    'Niveau de précision des impressions, exprimé en DOT (8-12-24) 
                    ou en DPI (points par pouce - 203-300-600) ;<br> plus la résolution est élevée plus l\'impression est lisible'
                ,
                'prt_th_largeur' => 'Indiquez ici la largeur maximum des étiquettes que vous souhaitez imprimer',
                'prt_th_marque' => 'Nous vous proposons les marques les plus fiables et les plus répandues sur le marché',

                'prt_th_gamme' => 'La gamme dépend des conditions d\'utilisation de l\'imprimante et de son 
                    environnement : <br> - BUREAU = petit débit & environnement bureautique <br>- SEMI-INDUSTRIELLE = débit important dans un environnement non préservé 
                    <br>- INDUSTRIELLE = robuste pouvant être intégrée dans la chaine de production  pour un fonctionnement intensif, 
                    dans un environnement peu protégé',

                'prt_th_use' => 'Deux modes d\'utilisations possibles : <br>- mode DIRECT = sans ruban encreur 
                    (moins coûteux mais tenue moins longue)  <br>-mode TRANSFERT = avec ruban encreur (un consommable supplémentaire) 
                    mais impressions de meilleure qualité, plus durables et tête d\'impression préservée (la tête d\'impression est une 
                    pièce d\'usure onéreuse à remplacer).',

                'prt_th_opt' => 'CUTTER / MASSICOT :  déchirer nettement et facilement vos étiquettes - PRE-DECOLLAGE/PEEL-OFF mettre automatiquement 
                    à disposition de vos utilisateurs des étiquettes pré-décollées - RE-ENROULEUR d\'étiquettes pré-imprimées : mettre à disposition de 
                    vos utilisateur des rouleaux complets d\'étiquettes en vue d\'une utilisation ultérieur -  RFID (Radio Frequency Identification) est une 
                    méthode permettant de mémoriser et récupérer des données à distance',

                'prt_th_com' => 'Il détermine la connexion physique de votre imprimante, selon qu\'il s\'agisse d\'une utilisation personnelle, partagée, filaire ou wifi',
                'prt_th_soft' => 'Nous vous proposons également les cartes adaptées à  l\'imprimante ainsi que les rubans encreurs et le logiciel d\'édition',
            ];
    }

}