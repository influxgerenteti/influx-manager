<?php

namespace App\Facade\$(namespace);

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author $(AUTOR)
 */
class $(nomeFacade)Facade extends GenericFacade
{
    
    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);        
    }
    
    
    /**
     * Lista todos os registros do banco de dados
     * 
     * @param array $parametros Parametros da requisicao
     * 
     * @return array
     */
    public function listar($parametros)
    {
        
    }
    
    /**
     * Busca o registro pela chave primaria
     * 
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * 
     * @return array|Object
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        
    }
    
    /**
     * Cria um objeto no banco de dados
     * 
     * @param string $mensagemErro Mensagem que ira retornar para front-end 
     * @param array $parametros Parametros a serem inclusos no objeto
     * 
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        
    }
    
    /**
     * Atualiza um registro no banco de dados
     * 
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     * 
     * @return boolean 
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        return empty($mensagemErro);
    }
    
    /**
     * Remove um registro do banco de dados
     * 
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     * 
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        return empty($mensagemErro);
    }
}