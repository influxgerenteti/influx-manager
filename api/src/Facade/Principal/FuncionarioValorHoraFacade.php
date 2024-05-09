<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\FuncionarioValorHoraBO;

/**
 *
 * @author Luiz A Costa
 */
class FuncionarioValorHoraFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FuncionarioValorHoraRepository
     */
    private $funcionarioValorHoraRepository;

    /**
     *
     * @var \App\BO\Principal\FuncionarioValorHoraBO
     */
    private $funcionarioValorHoraBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->funcionarioValorHoraRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FuncionarioValorHora::class);
        $this->funcionarioValorHoraBO         = new FuncionarioValorHoraBO(self::getEntityManager());
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
        $retornoRepositorio = $this->funcionarioValorHoraRepository->filtrarFuncionarioValorHoraPorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
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
        $objetoORM = $this->funcionarioValorHoraRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Funcionario Valor Hora não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\FuncionarioValorHora
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->funcionarioValorHoraBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\FuncionarioValorHora::class, true, $parametros);
            self::criarRegistro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
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
        $objetoORM = $this->funcionarioValorHoraRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Funcionario Valor Hora não encontrado na base de dados.";
        } else {
            $this->funcionarioValorHoraBO->configuraParametros($parametros, $objetoORM);
            self::flushSeguro($mensagemErro);
        }
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
        $objetoORM = $this->funcionarioValorHoraRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Funcionario Valor Hora não encontrado na base de dados.";
        } else {
            self::removerRegistro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }


}
