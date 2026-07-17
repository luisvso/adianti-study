<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class OrdemServicoHeaderList extends TPage
{
    protected $pageNavigation;
    protected $datagrid;
    protected $limit;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder("ordemServico_view");
        $this->form->setFormTitle("Lista de Ordem Serviço");
        $this->form->addFields([]);

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight(320);

        // INICIO HEADER
        $nm_ordemservicoFilter = new TEntry("nm_ordemservico");

        $this->form->addFields([new TLabel("Nome Ordem Serviço")], [$nm_ordemservicoFilter]);
        $this->form->setData(TSession::getValue("filtrosOS"));
        $this->form->addAction("Buscar", new TAction([$this, "onSearch"]), "fa:search blue");

        $this->form->addActionLink("New", new TAction(["OrdemServicoForm", "onShow"]), "fa:plus green");

        $this->form->addExpandButton();

        // FINAL HEADER

        // COLUNAS -> adicionado label
        $col_nu_ordemservico = new TDataGridColumn('nu_ordemservico', "N Ordem Serviço", "center", 50);
        $col_nm_patrimonio = new TDataGridColumn('nm_patrimonio', "Nome Patrimônio", "center", 50);
        $col_dt_abertura = new TDataGridColumn("dt_abertura", "Data de Abertura", "center", 50);
        $col_dt_conclusao = new TDataGridColumn("dt_conclusao", "Data de Conclusão", "center", 50);
        $col_ds_defeito = new TDataGridColumn("ds_defeito", "Defeito", "center", 50);
        $col_tp_situacao = new TDataGridColumn("tp_situacao", "Situação", "center", 50);
        $col_vl_custototal = new TDataGridColumn("vl_custototal", "Custo Total", "right", 50);

        // Colunas Adicionadas
        $this->datagrid->addColumn($col_nu_ordemservico);
        $this->datagrid->addColumn($col_nm_patrimonio);
        $this->datagrid->addColumn($col_dt_abertura);
        $this->datagrid->addColumn($col_dt_conclusao);
        $this->datagrid->addColumn($col_ds_defeito);
        $this->datagrid->addColumn($col_tp_situacao);
        $this->datagrid->addColumn($col_vl_custototal);

        // $this->form->addHeaderAction("New", new TAction([OrdemServicoForm, "onShow"], ["register_state" => "false"]), "fa:plus green");

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, "onReload"]));
        $this->pageNavigation->setLimit(2);

        // Adicionado 
        $container = new TVBox;
        $container->style = "width: 100%";
        $container->add($this->form);
        $container->add(TPanelGroup::pack("", $this->datagrid, $this->pageNavigation));

        parent::add($container);
    }

    public function onReload($param = null)
    {

        TTransaction::open("desenvolvimento");
        $repositorio = new TRepository(OrdemServico::class);
        $limit = 2;

        $criterio = new TCriteria;
        $filtrosOS = TSession::getValue("filtrosOS") ?? [];

        $criterio->setProperties($param);
        $criterio->setProperty("limit", $limit);

        foreach ($filtrosOS as $item)
        {
            $criterio->add($item);
        }

        $dados = $repositorio->load($criterio);
        $this->datagrid->clear();

        foreach ($dados as $rep)
        {
            $this->datagrid->addItem($rep);
        }

        $criterio->resetProperties();
        TSession::delValue("filtrosOS");
        $count = $repositorio->count($criterio);

        $this->pageNavigation->setProperties($param);
        $this->pageNavigation->setCount($count);
        $this->pageNavigation->setLimit($limit);

        TTransaction::close();
        $this->loaded = true;
    }

    public function onSearch($param = null)
    {
        $dadosFormOS = $this->form->getData();
        TSession::delValue("filtrosOS");

        $filtros = [];

        TSession::setValue("filtrosOS", null);

        if (isset($dadosFormOS->nm_ordemservicoFilter) && !empty($dadosFormOS->nm_ordemservicoFilter))
        {
            $filtros[] = new TFilter("nm_ordemservico", "ILIKE", '%{$data->nm_ordemservicoFilter}');
        }

        TSession::setValue("filtrosOS", $filtros);
        $this->onReload($param);
    }
}
