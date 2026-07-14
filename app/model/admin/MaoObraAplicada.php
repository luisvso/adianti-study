<?php

use Adianti\Database\TRecord;

class MaoObraAplicada extends TRecord
{
    const TABLENAME = 'mao_obra_aplicada';
    const PRIMARYKEY = 'id_mao_obra_aplicada';
    const IDPOLICY = 'serial';

    public function __construct($id_mao_obra_aplicada = NULL, $objectTrue = TRUE)
    {

        parent::__construct($id_mao_obra_aplicada, true);
        parent::addAttribute("nm_peca");
        parent::addAttribute("id_ordemservico");
        parent::addAttribute("qt_utilizada");
        parent::addAttribute("vl_totalitem");

    }

}