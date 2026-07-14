<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class TaskList extends TPage
{

    protected $datagrid;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder();
        $this->form->addFields([]);

        $this->form->addHeaderAction(_t('New'), new TAction(['TaskForm', 'onShow'], ['register_state' => 'false']), 'fa:plus green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        $this->datagrid->style = "width: 100%";

        $this->datagrid->setHeight(320);

        $nm_titulo = new TDataGridColumn("nm_titulo", "Titulo", "center", 50);
        $ds_task = new TDataGridColumn("ds_task", "Descrição", "center", 50);
        $tp_prioridade = new TDataGridColumn("tp_prioridade", "Prioridade", "center", 50);
        $tp_prioridade->setTransformer( function ($value){

            return TaskConstant::TIPO_PRIORIDADES[$value];
        });

        // $nm_tituloFilter = new TEntry('nm_titulo');
        // $ds_taskFilter = new TEntry('ds_task');

        // $rowFilters = $this->form->addFields(
        //     [new TLabel('Titulo'), $nm_tituloFilter],
        //     [new TLabel('Descrição'), $ds_taskFilter]
        // );

        // $rowFilters->layout = ['col-sm-8', 'col-sm-4'];

        // $this->form->setData(TSession::getValue(__CLASS__.'_filter_data'));

        // Action para ordenar os campos da tabela
        $nm_titulo->setAction(new TAction([$this, 'onReload']), ['order' => 'nm_titulo']);
        $ds_task->setAction(new TAction([$this, 'onReload']), ['order' => 'ds_task']);
        $tp_prioridade->setAction(new TAction([$this, 'onReload']), ['order' => 'tp_prioridade']);

        $this->datagrid->addColumn($nm_titulo);
        $this->datagrid->addColumn($ds_task);
        $this->datagrid->addColumn($tp_prioridade);

        $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fa:search');
        $this->form->addAction("Limpar", new TAction([$this, 'onClean']), 'fa:eraser red');

        //Editar Action
        $editar = new TDataGridAction(['TaskForm', 'onEdit'], ['key' => '{id_task}']);
        $editar->setButtonClass("btn btn-default");
        $editar->setLabel("Editar");
        $editar->setImage("far:edit blue");

        //Deletar Action
        $deletar = new TDataGridAction([$this, 'onDelete'], ['key' => '{id_task}', 'static' => 1]);
        $deletar->setButtonClass("btn btn-default");
        $deletar->setLabel("Deletar");
        $deletar->setImage("far:trash-alt red");

        $this->datagrid->addAction($editar);
        $this->datagrid->addAction($deletar);

        $this->datagrid->createModel();

        $container = new TVBox;
        $container->style = "width: 100%";
        $container->add($this->form);
        $container->add(TPanelGroup::pack("", $this->datagrid ));


        parent::add($container);
    }

    public function onSearch($param = null)
    {

        $data = $this->form->getData();
        TSession::delValue('filtros');

        $filtros = [];

        TSession::setValue('filtros', null);

        $filtros[] = new TFilter('unaccent()', 'LIKE', "%{$data->nm_titulo}");

        TSession::setValue('filtros', $filtros);

        $this->onReload($param);

    }

    public function onClean()
    {

        $this->form->clear();
        $this->onReload();

    }



    public function onReload()
    {

        TTransaction::open('desenvolvimento');

        $repositorio = new TRepository(Task::class);
        $dados = $repositorio->load(new TCriteria);

        foreach ($dados as $rep)
        {
            $this->datagrid->addItem($rep);
        }


        TTransaction::close();
        $this->loaded = true;

    }
    
    public function onShow()
    {

    }

    public function onDelete($param)
    {

        TaskRepository::onDelete($param);

        AdiantiCoreApplication::loadPage(__CLASS__, "onReload");

    }

}