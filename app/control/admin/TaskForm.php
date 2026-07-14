<?php

// namespace app\model\admin;

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreLoader;
use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class TaskForm extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTargetContainer("adianti_right_panel");

        $this->form = new BootstrapFormBuilder("form_task");
        $this->form->setFormTitle("Task");

        $nm_titulo = new TEntry("nm_titulo");
        $nm_titulo->setSize("100%");
        $nm_titulo->setMaxLength(10);

        $ds_task = new TEntry("ds_task");
        $ds_task->setSize("100%");
        $nm_titulo->setMaxLength(100);

        $tp_prioridade = new TCombo("tp_prioridade");
        $tp_prioridade->addItems(TaskConstant::TIPO_PRIORIDADES);
        $tp_prioridade->setSize("100%");
        $nm_titulo->setMaxLength(100);


        $row1 = $this->form->addFields(
            [ new TLabel("Titulo: (*)", "#ff0000", "14px", null, "100%"), $nm_titulo], 
            [ new TLabel("Prioridade: (*)", "#ff0000", "14px", null, "100%"), $tp_prioridade]
        );
        $row1->layout = ["col-sm-6", "col-sm-6"];

        $row2 = $this->form->addFields([ new TLabel("Descrição: (*)", "#ff0000", "14px", null, "200%"), $ds_task]);
        $row2->layout = ["col-sm-12"];

        $salvar = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $fechar = $this->form->addAction('Fechar', new TAction([$this, 'onClose']), 'fa:close red');

        $btn = new TButton('btn');
        $btn->setAction(new TAction(array($this, 'onSave')), 'Salvar');
        $btn->setImage('far:check-circle green');

        $this->form->addField($btn);

        parent::add($this->form);
    }

    public function onSave($param = null)
    {

        $data = $this->form->getData();

        $object = new Task();
        $object->fromArray((array) $data);

        W5iSessao::obterObjetoEdicaoSessao($object, "id_task", null, __CLASS__);


        TaskRepository::onSave($object);
        $this->onClose();
        W5iSessao::removerObjetoEdicaoSessao(__CLASS__);

        AdiantiCoreApplication::loadPage("TaskList", "onReload");

    }

    public function onClose()
    {

        TScript::create("Template.closeRightPanel()");
        W5iSessao::removerObjetoEdicaoSessao(__CLASS__);

        AdiantiCoreApplication::loadPage("TaskList", "onReload");

    }

    public function onShow()
    {

    }

    public function onEdit($param)
    {

        $key = $param["key"];

        TTransaction::open("desenvolvimento");

        $object = new Task($key);

        TTransaction::close();

        W5iSessao::incluirObjetoEdicaoSessao($object, $key, "id_task", __CLASS__);

        $this->form->setData($object);


    }

}