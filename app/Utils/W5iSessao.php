<?php


use Exception;
use App\Utils\W5iCookies;
use App\Utils\W5iConstantes;
use Adianti\Registry\TSession;
use Adianti\Widget\Dialog\TMessage;
use Reflection;
use ReflectionClass;

class W5iSessao
{
    /**
     * Insere na sessão o objeto em edição
     * @author: Paulo
     * @created: 31/01/2024
     * @param $object: objeto em edição que será adicionado na sessão
     * @param $key: chave do registro
     * @param $primaryKey: nome da chave primária do objeto (null)
     * @param $class: classe que o objeto é chamado (null)
     */
    public static function incluirObjetoEdicaoSessao($object, $key, $primaryKey = null, $class = null)
    {
        try
        {
            if ($class == null)
            {
                $class = __CLASS__;
            }

            $nameClass = get_class($object);

            $objectCopy = clone $object;

            if($nameClass != 'stdClass')
            {
                if ($primaryKey != null)
                    $objectCopy->__set($primaryKey, $key);
            }
            
            //$objectCopy->keyValue = $key;
            TSession::setValue($class . '.objectEdit', $objectCopy);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Obtem da sessão o objeto colocado em edição
     * @author: Paulo
     * @created: 31/01/2024
     * @$object: objeto em edição que será adicionado na sessão
     * @$primaryKey: nome da chave primária do objeto (null)
     * @$readOnlyFields: array de campos que não devem ser editados na tela (null)
     * @$class: classe que o objeto é chamado (null)
     */
    public static function obterObjetoEdicaoSessao(?object $object = null, $primaryKey = null, $readOnlyFields = null, $class = null)
    {
        try
        {
            if ($class == null)
            {
                $class = __CLASS__;
            }

            $objectEdit = TSession::getValue($class . '.objectEdit');
            if ($primaryKey != null && $object != null)
            {
                if ($objectEdit != NULL)
                {
                    $object->__set($primaryKey, $objectEdit->{$primaryKey});
                    if ($readOnlyFields != null && is_array($readOnlyFields))
                    {
                        foreach ($readOnlyFields as $field)
                        {
                            $object->__set($field, $objectEdit->__get($field));
                        }
                    }
                }
                else
                {
                    $object->__set($primaryKey, NULL);
                }
            }
            else
            {
                return $objectEdit;
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Remove o objeto da sessão
     * @author: Paulo
     * @created: 31/01/2024
     * @$class: classe que o objeto é chamado (null)
     */
    public static function removerObjetoEdicaoSessao($class = null)
    {
        try
        {
            if ($class == null)
            {
                $class = __CLASS__;
            }
            TSession::delValue($class . '.objectEdit');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Insere na sessão o estado do registro
     * @author: Paulo
     * @created: 30/01/2024
     * @$estado: estado do registro. Valores possíves: leitura, edicao
     * @$class: classe que o objeto é chamado
     */
    public static function inserirEstadoRegistro($estado, $class = null)
    {
        if (!in_array($estado, [W5iConstantes::ESTADO_REGISTRO_LEITURA, W5iConstantes::ESTADO_REGISTRO_EDICAO]))
        {
            throw new Exception("Estado do registro inconsistente");
        }
        if ($class == null)
        {
            $class = __CLASS__;
        }

        TSession::setValue($class . '.estadoRegistro', $estado);
    }

    /**
     * Obtem da sessão o campo do objeto colocado em edição
     * @author: Paulo
     * @created: 08/02/2024
     * @$field: nome do campo que será retornado
     * @$class: classe que o objeto é chamado (null)
     */
    public static function obterCampoObjetoEdicaoSessao($field, $class = null)
    {
        try
        {
            if ($class == null)
            {
                $class = __CLASS__;
            }

            $objectEdit = TSession::getValue($class . '.objectEdit');

            if ($objectEdit != NULL)
            {
                return $objectEdit->__get($field);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Obtem da sessão o estado do registro
     * @author: Paulo
     * @created: 30/01/2024
     * @$class: classe que o objeto é chamado
     */
    public static function obterEstadoRegistro($class = null)
    {
        if ($class == null)
        {
            $class = __CLASS__;
        }

        return TSession::getValue($class . '.estadoRegistro');
    }

    /**
     * Remove da sessão o estado do registro
     * @author: Paulo
     * @created: 30/01/2024
     * @$class: classe que o objeto é chamado
     */
    public static function limparEstadoRegistro($class = null)
    {
        if ($class == null)
        {
            $class = __CLASS__;
        }

        TSession::delValue($class . '.estadoRegistro');
    }

    /**
     * Limpar a lista de uma sessao
     * @author: Daniel Kawasaka <dkawasaka@gmail.com>
     * @param: $nome nome da sessao
     * @param: $detail nome da propriedade a ser limpa
     */
    public static function detailClean($nome, $detail)
    {
        $object = TSession::getValue($nome);
        $lista = array();
        $object->{$detail} = $lista;

        TSession::setValue($nome, $object);
    }

    /**
     * Adiciona um novo item a uma propriedade array
     * @author: Daniel Kawasaka <dkawasaka@gmail.com>
     * @param: $nome nome da sessao
     * @param: $propriedade nome da propriedade a ser adicionado 
     * @param: $key nome da key do array
     * @param: $objectNovo objeto a ser salvo na key
     */
    public static function detailAddItem($nome, $propriedade, $key, $objectNovo)
    {
        $object = TSession::getValue($nome);
        $lista = array();
        if (property_exists($object, $propriedade))
        {
            $lista = $object->{$propriedade};
        }
        $lista[$key] = $objectNovo;
        $object->{$propriedade} = $lista;

        TSession::setValue($nome, $object);
    }

    /**
     * Remove um item do array pela key
     * @author: Daniel Kawasaka <dkawasaka@gmail.com>
     * @param: $nome nome da sessao
     * @param: $propriedade nome da propriedade a ser adicionado 
     * @param: $key nome da key do array
     */
    public static function detailExcluirItem($nome, $propriedade, $key)
    {
        $object = TSession::getValue($nome);

        $lista = $object->{$propriedade};
        unset($lista[$key]);
        $object->{$propriedade} = $lista;

        TSession::setValue($nome, $object);
    }

    /**
     * Inserir um novo item na sessao, destruindo as que foram criadas caso ja não esteja em utilizaão 
     * @author: Ramon
     * @param $nm_sessao: nome da sessao
     * @param: $valor:  valor a ser adicionado 
     */
    public static function definirValor($nm_sessao, $valor)
    {
        $cookie_tab_sipec = $_COOKIE['__adianti_current_tab_sipec'];
        $uniqid = W5iCookies::encontrarValorPorChave($_COOKIE['__adianti_tabs_sipec'], 'name', $cookie_tab_sipec);

        $cookie_tab_sipec = W5iCookies::substituirAcentos($cookie_tab_sipec);
        $cookie_tab_sipec = W5iCookies::removerCaracterEspecial($cookie_tab_sipec);
        self::destruirSessoesPorNomeJanela($cookie_tab_sipec, $uniqid, $nm_sessao);

        TSession::setValue(W5iCookies::uniqid() . $nm_sessao, $valor);
    }

    /**
     * Obtem o valor da sessao com identificador unico por janela aberta
     * @author: Ramon
     * @param $nm_sessao: nome da sessao
     */
    public static function obterValor($nm_sessao)
    {
        return TSession::getValue(W5iCookies::uniqid() . $nm_sessao);
    }

    /**
     * Exclui o valor da sessao com identificador unico por janela aberta
     * @author: Ramon
     * @param $nm_sessao: nome da sessao
     */
    public static function excluirValor($nm_sessao)
    {
        return TSession::delValue(W5iCookies::uniqid() . $nm_sessao);
    }

    /**
     * Inserir um novo item na sessao, destruindo as que foram criadas caso ja não esteja em utilizaão 
     * @author: Ramon
     * @param $nomeJanela: nome da janela atual
     * @param: $uniqidAtual:  identificador da da janela atual  
     */
    public static function destruirSessoesPorNomeJanela($nomeJanela, $uniqidAtual, $nm_sessao)
    {
        foreach ($_SESSION['sipec'] as $nomeSessao => $valor)
        {
            if (strpos($nomeSessao, '_' . $nomeJanela . '_') !== false)
            {
                $partes = explode('_', $nomeSessao);
                $uniqidSessao = $partes[0];

                if (strpos($nomeSessao, $uniqidAtual) !== false)
                {
                    if (strpos($nomeSessao, $nm_sessao) !== false)
                    {
                        unset($_SESSION['sipec'][$nomeSessao]);
                    }
                }
                else
                {
                    unset($_SESSION['sipec'][$nomeSessao]);
                }
            }
        }
    }
}
