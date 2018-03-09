<?php

/**
 * Created by PhpStorm.
 * User: gvalero
 * Date: 11/10/2017
 * Time: 10:33
 */
class Genius_Class_FilteringPrinterMatricielle
{
    public $session;
    public $result;
    private $post;

    /**
     * FilteringArticle constructor.
     */
    public function __construct($post)
    {
        $this->post = $post;
        $this->result = [];
    }

    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }

    public function handle()
    {
        $int = array_flip($this->post['interfacep']);
        var_dump($int);
        $this->session->inputPrinterMatricielle['interface'] = $int;

        $this->session->inputPrinterMatricielle['marque'] = $this->post['marque'];
        $this->session->inputPrinterMatricielle['format'] = $this->post['format'];
        $this->session->inputPrinterMatricielle['gamme'] = $this->post['gamme'];

        var_dump( $this->session->inputPrinterMatricielle);
        return $this;
    }

    public function search()
    {
        $model = new Genius_Model_FiltrePrinterMatricielle();
        global $db;

        $model = $model ->select();
        if($this->post['marque'] == 'm_lexmark') $model = $model->where('m_lexmark = 1');
        if($this->post['marque'] == 'm_ibm') $model = $model->where('m_ibm = 1');
        if($this->post['marque'] == 'm_epson') $model = $model->where('m_epson = 1');
        if($this->post['marque'] == 'm_oki') $model = $model->where('m_oki = 1');

        if($this->post['format'] == 'c_132') $model = $model->where('c_132 = 1');
        if($this->post['format'] == 'c_80') $model = $model->where('c_80 = 1');

        if($this->post['gamme'] == 'bureau') $model = $model->where('bureau = 1');
        if($this->post['gamme'] == 'indu') $model = $model->where('indu = 1');



        if(isset($this->session->inputPrinterMatricielle['interface']['eth']))  $model = $model->where('eth = 1') ;
        if(isset($this->session->inputPrinterMatricielle['interface']['serie']))  $model = $model->where('serie = 1') ;
        if(isset($this->session->inputPrinterMatricielle['interface']['parra']))  $model = $model->where('parra = 1') ;
        if(isset($this->session->inputPrinterMatricielle['interface']['usb']))  $model = $model->where('usb = 1') ;
        if(isset($this->session->inputPrinterMatricielle['interface']['twinax']))  $model = $model->where('twinax = 1') ;
        $model = $model->limit(10);
        //var_dump($db->query($model));

        $this->result = $db->query($model)->fetchAll();

        //var_dump($db->query($model)->fetchAll());
        //die();
        return  $this;
    }

    public function setResult()
    {
        $this->session->resultPrinterMatricielle = $this->result;
    }
}