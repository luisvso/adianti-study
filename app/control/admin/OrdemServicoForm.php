<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TNumeric;
use Adianti\Widget\Form\TText;
use Adianti\Wrapper\BootstrapDatagridWrapper;
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

        // FIELDS FOR CREATING MESTER
        $nu_ordemservico = new TEntry("nu_ordemservico");
        $nu_ordemservico->setSize("50%");
        $nu_ordemservico->setEditable(FALSE);

        $nm_patrimonio = new TEntry("nm_patrimonio");
        $nm_patrimonio->setSize("100%");

        $dt_abertura = new TDate("dt_abertura");
        $dt_abertura->setEditable(FALSE);
        $dt_abertura->setSize("100%");

        // Aplicando Mascara de Data na data de abertura
        $dt_abertura->setValue(date("d-m-Y"));
        $dt_abertura->setMask("dd/mm/yyyy");
        $dt_abertura->setDatabaseMask("yyyy-mm-dd");

        $dt_conclusao = new TDate("dt_conclusao");
        $dt_conclusao->setSize("100%");

        $ds_defeito = new TText("ds_defeito");
        $ds_defeito->setSize("100%");

        $tp_situacao = new TEntry("tp_situacao");
        $tp_situacao->setEditable(FALSE);
        $tp_situacao->setSize("50%");
        $tp_situacao->setValue(PrioridadeEnum::ABERTA->label());

        $vl_custototal = new TEntry("vl_custototal");
        $vl_custototal->setEditable(FALSE);
        $vl_custototal->setSize("50%");

        // CAMPOS DOS DETALHES
        $row_id = new THidden("id");
        $uniqid = new THidden("peca_uniqid");
        $nm_peca = new TEntry("nm_peca");
        $vl_unitario = new TNumeric("vl_unitario", 2, ",", ".", true, false, false);
        $qt_utilizada = new TNumeric("qt_utilizada", 0, "", ".", true, false, false);

        $vl_totalitem = new TNumeric("vl_totalitem", 2, ",", ".", true, false, false);
        $vl_totalitem->setEditable(FALSE);


        // ADICIONANDO OS CAMPO DO MESTRE
        $row1 = $this->form->addFields(
            [new TLabel("Numero da OS: (*)", "#ff0000", "14px", null, "100%"), $nu_ordemservico]
        );
        $row1->layout = ["col-sm-12"];

        $row2 = $this->form->addFields(
            [new TLabel("Nome da OS: (*)", "#ff0000", "14px", null, "100%"), $nm_patrimonio],
        );
        $row2->layout = ["col-sm-12"];

        $row3 = $this->form->addfields(
            [new TLabel("Data de Abertura: (*)", "#ff0000", "14px", null, "100%"), $dt_abertura],
            [new TLabel("Prazo de Conclusão: (*)", "#ff0000", "14px", null, "100%"), $dt_conclusao]
        );
        $row3->layout = ["col-sm-6", "col-sm-6"];

        $row4 = $this->form->addFields(
            [new TLabel("Descrição do Defeito: (*)", "#ff0000", "14px", null, "100%"), $ds_defeito]
        );
        $row4->layout = ["col-sm-12"];

        $row5 = $this->form->addFields(
            [new TLabel("Situação: (*)", "#ff0000", "14px", null, "100%"), $tp_situacao],
        );
        $row5->layout = ["col-sm-12"];

        $row6 = $this->form->addFields(
            [new TLabel("Valor Custo Total: (*)", "#ff0000", "14px", null, "100%"), $vl_custototal]
        );
        $row6->layout = ["col-sm-12"];

        // HEADER DO DETALHE
        $this->form->addContent(["<h4>Detail</h4><hr>"]);

        // ADICIONANDO OS CAMPOS DOS DETALHES
        $this->form->addFields(
            [$uniqid],
            [$row_id]
        );

        $this->form->addFields(
            [new TLabel("Nome Peça: (*)", "#ff0000", "14px", null, "100%"), $nm_peca],
            [new TLabel("Valor Unitario: (*)", "#ff0000", "14px", null, "100%"), $vl_unitario]
        );

        $this->form->addFields(
            [new TLabel("Quantidade Utilizada: (*)", "#ff0000", "14px", null, "100%"), $qt_utilizada],
            [new TLabel("Valor Total: (*)", "#ff0000", "14px", null, "100%"), $vl_totalitem]
        );

        // BOTÃO DE ADICIONAR
        $add_peca = TButton::create("add_peca", [$this, "onPecaAdd"], "Registrar", "fa:plus-circle green");
        $add_peca->getAction()->setParameter("static", "1");
        $this->form->addFields([], [$add_peca]);

        // LISTA DE PECAS
        $this->peca_lista = new BootstrapDatagridWrapper(new TDataGrid);
        $this->peca_lista->setHeight(150);
        $this->peca_lista->makeScrollable();
        $this->peca_lista->setId("pecas_lista");
        $this->peca_lista->generateHiddenFields();
        $this->peca_lista->style = "min-width: 700px; width:100%;margin-bottom: 10px";

        //COLUNAS DA LISTAGEM
        // $col_id = new TDataGridColumn("id", "id", "center", 10);
        // $col_uniqid = new TDataGridColumn("uniqid", "Uniqid", "center", 10);
        $col_nm_peca = new TDataGridColumn("nm_peca", "Nome da Peça", "center", "40%");
        $col_vl_unitario = new TDataGridColumn("vl_unitario", "Valor Unitario", "right", "10%");
        $col_qt_utilizada = new TDataGridColumn("qt_utilizada", "Quantidade Utilizada", "center", "40%");
        $col_totalitem = new TDataGridColumn("vl_totalitem", "Valor Total: R$", "right", "10%");

        // COLUNAS ADICIONADAS
        $this->peca_lista->addColumn($col_nm_peca);
        $this->peca_lista->addColumn($col_vl_unitario);
        $this->peca_lista->addColumn($col_qt_utilizada);
        $this->peca_lista->addColumn($col_totalitem);

        // ACTION FOR DETAILS
        $actionEditDetail = new TDataGridAction([$this, "onEditPeca"]);
        $actionEditDetail->setFields(["uniqid", "*"]);

        $actionDeleteDetail = new TDataGridAction([$this, "onDeletePeca"]);
        $actionDeleteDetail->setFields(["uniqid", "*"]);

        // ACTION ADDED INSIDE THE DATAGRID
        $this->peca_lista->addAction($actionEditDetail, "Editar", "far:edit blue");
        $this->peca_lista->addAction($actionDeleteDetail, "Deletar", "far:trash-alt red");

        $this->peca_lista->createModel();

        $panel = new TPanelGroup;
        $panel->add($this->peca_lista);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent([$panel]);

        $format_value = function ($value)
        {
            if (is_numeric($value))
            {
                return number_format($value, 2, ",", ".");
            }
        };

        $col_vl_unitario->setTransformer($format_value);
        $col_totalitem->setTransformer($format_value);

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

        $A = 1;

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

    public function onClear()
    {
        $this->form->clear();
    }

    public function onPecaAdd($param)
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();

            if ((! $data->nm_peca) || (! $data->vl_unitario) || (! $data->qt_utilizada))
            {
                throw new Exception("Os Campos das peças precisam ser preenchidos");
            }

            $uniqid = !empty($data->peca_uniqid) ? $data->peca_uniqid : uniqid();

            $grid_peca = [
                "uniqid" => $uniqid,
                "id" => $data->id,
                "nm_peca" => $data->nm_peca,
                "vl_unitario" => $data->vl_unitario,
                "qt_utilizada" => $data->qt_utilizada,
                "vl_totalitem" => $data->vl_totalitem = $data->vl_unitario * $data->qt_utilizada,
            ];

            $row = $this->peca_lista->addItem((object) $grid_peca);
            $row->id = $uniqid;

            TDataGrid::replaceRowById("pecas_lista", $uniqid, $row);

            $data->peca_uniqid = "";
            $data->id = "";
            $data->nm_peca = "";
            $data->vl_unitario = "";
            $data->qt_utilizada = "";
            $data->vl_totalitem = "";

            TForm::sendData("form_os", $data, false, false);

            $this->calcularVlCustoTotal($grid_peca, strtolower($param["method"]));
        }
        catch (Exception $ex)
        {
            new TMessage("error", $ex->getMessage());
        }
    }

    public static function onEditPeca($param)
    {

        $format_value = function ($value)
        {
            if (is_numeric($value))
            {
                return number_format($value, 2, ",", ".");
            }
        };

        $data = new stdClass;
        $data->peca_uniqid = $param["uniqid"];
        $data->id = $param["id"];
        $data->nm_peca = $param["nm_peca"];
        $data->vl_unitario = $format_value($param["vl_unitario"]);
        $data->qt_utilizada = $param["qt_utilizada"];
        $data->vl_totalitem = $format_value($param["vl_totalitem"]);

        TForm::sendData("form_os", $data, false, false);
    }

    public static function onDeletePeca($param)
    {

        $data = new stdClass;
        $data->uniqid = "";
        $data->id = "";
        $data->nm_peca = "";
        $data->vl_unitario = "";
        $data->qt_utilizada = "";
        $data->vl_totalitem = "";

        TForm::sendData("form_os", $data, false, false);

        TDataGrid::removeRowById("pecas_lista", $param["uniqid"]);


        self::calcularVlCustoTotal([], $param["method"]);

        // calcularVlCustoTotal( (array) $formOs, $param["method"]);
    }

    public static function calcularVlCustoTotal(array $data, string $method)
    {
        $vlTotalItemSomar = $data["vl_totalitem"];

        $format_value = function ($value)
        {
            if (is_numeric($value))
            {
                return number_format($value, 2, ",", ".");
            }
        };

        // $formOs = $this->form->getData();
        $formOs = "21";

        $custoTotal = (int) $formOs->vl_custototal;

        if (preg_match("/^(\w+)*delete(\w+)*$/", $method))
        {
            $custoTotal -= $vlTotalItemSomar;
        }

        if (preg_match("/^(\w+)*(add)(\w+)*$/", $method))
        {
            $custoTotal += $vlTotalItemSomar;
        }

        Tform::sendData($this->formName, ["vl_custototal" => $format_value($custoTotal)], FALSE, FALSE);
    }
}
