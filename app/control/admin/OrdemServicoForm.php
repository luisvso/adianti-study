<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TText;
use Adianti\Wrapper\BootstrapFormBuilder;

class OrdemServicoForm extends TPage
{
    protected $form;
    protected $formName = "form_os";

    public function __construct()
    {

        parent::__construct();
        $this->form = new BootstrapFormBuilder($this->formName);
        $this->form->setFormTitle("Ordem Serviço");

        // FIELDS FOR CREATING
        $nu_ordemservico = new TEntry("nu_ordemservico");
        $nu_ordemservico->setSize("50%");
        $nu_ordemservico->setEditable(FALSE);

        $nm_patrimonio = new TEntry("nm_patrimonio");
        $nm_patrimonio->setSize("100%");

        $dt_abertura = new TDate("dt_abertura");
        $dt_abertura->setEditable(FALSE);
        $dt_abertura->setSize("100%");

        $dt_conclusao = new TDate("dt_conclusao");
        $dt_conclusao->setSize("100%");

        $ds_defeito = new TText("ds_defeito");
        $ds_defeito->setSize("100%");

        $tp_situacao = new TEntry("tp_situacao");
        $tp_situacao->setEditable(FALSE);
        $tp_situacao->setSize("50%");

        $vl_custototal = new TEntry("vl_custototal");
        $vl_custototal->setEditable(FALSE);
        $vl_custototal->setSize("50%");



        $row1 = $this->form->addFields(
            [ new TLabel("Numero da OS: (*)", "#ff0000", "14px", null, "100%"), $nu_ordemservico]
        );
        $row1->layout = ["col-sm-12"];

        $row2 = $this->form->addFields(
            [ new TLabel("Nome da OS: (*)", "#ff0000", "14px", null, "100%"), $nm_patrimonio],
        );
        $row2->layout = ["col-sm-12"];

        $row3 = $this->form->addfields(
            [ new TLabel("Data de Abertura: (*)", "#ff0000", "14px", null, "100%" ), $dt_abertura],
            [ new TLabel("Prazo de Conclusão: (*)", "#ff0000", "14px", null, "100%"), $dt_conclusao]
        );
        $row3->layout = ["col-sm-6", "col-sm-6"];

        $row4 = $this->form->addFields(
            [ new TLabel("Descrição do Defeito: (*)", "#ff0000", "14px", null, "100%" ), $ds_defeito]
        );
        $row4->layout = ["col-sm-12"];

        $row5 = $this->form->addFields(
            [ new TLabel("Situação: (*)", "#ff0000", "14px", null, "100%"), $tp_situacao],
        );
        $row5->layout = ["col-sm-12"];

        $row6 = $this->form->addFields(
            [ new TLabel("Valor Custo Total: (*)", "#ff0000", "14px", null, "100%"), $vl_custototal ]
        );
        $row6->layout = ["col-sm-12"];

        // Ações do formulario
        $salvar = $this->form->addAction("Salvar", new TAction([$this, "onSave"]), "fa:save green");
        $fechar = $this->form->addAction("Cancelar", new TAction([$this, "onClose"]), "fa:close red");

        $btn = new TButton("btn");
        $btn->setAction(new TAction(array($this, "onSave")), "Salvar");
        $btn->setImage("far:check-circle green");

        $this->form->addField($btn);

        $vbox = new TVBox;
        $vbox->style = "width: 100%";
        $vbox->add($this->form);

        parent::add($vbox);

    }

    public function onShow()
    {
        $this->gerarNumeroOS();
    }

    public function gerarNumeroOS()
    {

        $A=1;

        TForm::sendData($this->formName, ['nu_ordemservico' => $A], FALSE, FALSE);
    }

    public function onSave()
    {
        $dadosOS = $this->form->getData();

        $object = new OrdemServico();

        $object->fromArray((array) $dadosOS);

        W5iSessao::obterCampoObjetoEdicaoSessao($object, "id_ordemservico", null, __CLASS__);

    }

    public function onClose()
    {
    }

}