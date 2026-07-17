<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class PecaSeek extends TWindow
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase("desenvolvimento");
        $this->setActiveRecord("Peca");
        $this->setDefaultOrder("id_peca", "asc");

        // FILTERS
        $this->addFilterField("nm_peca", "ILIKE");

        $this->form = new BootstrapFormBuilder();

        $this->form->setFormTitle(_t("Tools List"));

        $nm_peca = new TEntry("nm_peca");

        $this->form->addFields([new TLabel("Nome Peça")], [$nm_peca]);

        $this->form->setData(TSession::getValue("seek_peca"));

        $this->form->addAction("Buscar", new TAction([$this, "onSearch"]), "fa:search blue");
        $this->form->addExpandButton();

        // COLUNAS com campo, label (nome do campo), posição e porcentagem (tem que ser total 100% todas somadas)
        $col_id_peca = new TDataGridColumn("id_peca", "", "center", "5%");
        $col_cd_peca = new TDataGridColumn("cd_peca", "Codigo Peça", "center", "15%");
        $col_nm_peca = new TDataGridColumn("nm_peca", "Nome da Peça", "center", "65%");
        $col_vl_unitario = new TDataGridColumn("vl_unitario", "Valor Unitario", "right", "30%");

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        // Adicionado a coluna da peça
        $this->datagrid->addColumn($col_cd_peca);
        $this->datagrid->addColumn($col_nm_peca);
        $this->datagrid->addColumn($col_vl_unitario);

        $action1 = new TDataGridAction([$this, "onSelect"], ["id_peca" => "{id_peca}"]);
        $this->datagrid->addAction($action1, "Select", "far:hand-pointer blue");

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, "onReload"]));

        $container = new TVBox;
        $container->style = "width: 100%";
        $container->add($this->form);
        $container->add($panel = TPanelGroup::pack("", $this->datagrid, $this->pageNavigation));

        $panel->getBody()->style = "overflow-x:auto";
        parent::add($container);
    }

    public function onSelect($param)
    {
        try
        {

            $key = $param["key"];
            TTransaction::open("desenvolvimento");

            $peca = new Peca($key);

            TTransaction::close();

            $format_value = function ($value)
            {
                if (is_numeric($value))
                {
                    return number_format($value, 2, ",", ".");
                }
            };

            $object = new stdClass;
            $object->id_peca = $peca->id_peca;
            $object->nm_peca= $peca->nm_peca;
            $object->cd_peca = $peca->cd_peca;
            $object->vl_unitario = $format_value($peca->vl_unitario);

            TForm::sendData("form_os", $object);
            parent::closeWindow();
        }
        catch (Exception $ex)
        {
        }
    }
}
