<?php

use Adianti\Database\TRecord;

class OrdemServico extends TRecord
{
    const TABLENAME = 'ordem_servico';
    const PRIMARYKEY = 'id_ordemservico';
    const IDPOLICY = 'serial';

    public function __construct($id_ordemservico = NULL, $objectTrue = true)
    {

        parent::__construct($id_ordemservico, true);
        parent::addAttribute("nu_ordemservico");
        parent::addAttribute("nm_patrimonio");
        parent::addAttribute("dt_abertura");
        parent::addAttribute("dt_conclusao");
        parent::addAttribute("ds_defeito");
        parent::addAttribute("tp_situacao");
        parent::addAttribute("vl_custototal");

    }
}