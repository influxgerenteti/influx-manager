<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\TransferenciaBancariaBO;

/**
 *
 * @author Marcelo A Naegeler
 */
class TransferenciaBancariaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TransferenciaBancariaRepository
     */
    private $transferenciaBancariaRepository;

    /**
     *
     * @var \App\BO\Principal\TransferenciaBancariaBO
     */
    private $transferenciaBancariaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->transferenciaBancariaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TransferenciaBancaria::class);
        $this->transferenciaBancariaBO         = new TransferenciaBancariaBO(self::getEntityManager());
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
        $retornoRepositorio = $this->transferenciaBancariaRepository->getAll($parametros);
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
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->transferenciaBancariaRepository->buscarRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Registro não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param boolean $persistFlush Flag para realizar o persist e flush
     *
     * @return mixed|null|\App\Entity\Principal\TransferenciaBancaria
     */
    public function criar(&$mensagemErro, $parametros=[], $persistFlush=true)
    {
        $objetoORM = null;
        $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_PENDENTE;

        if ($this->transferenciaBancariaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TransferenciaBancaria::class, true, $parametros);
            if ($persistFlush === true) {
                self::criarRegistro($objetoORM, $mensagemErro);
            } else {
                self::persistSeguro($objetoORM, $mensagemErro);
            }
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
        $objetoORM = $this->transferenciaBancariaRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Registro não encontrado na base de dados.";
        } else {
            $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


    /**
     * Cancela a transferencia passada
     *
     * @param string $mensagemErro
     * @param integer $id
     *
     * @return boolean
     */
    public function cancelar (&$mensagemErro, $id)
    {
        $transferenciaBancaria = $this->transferenciaBancariaRepository->find($id);

        if (is_null($transferenciaBancaria) === true) {
            $mensagemErro = "Cheque não encontrado.";
            return false;
        }

        if ($transferenciaBancaria->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE) {
            $transferenciaBancaria->setSituacao(SituacoesSistema::SITUACAO_CANCELADO);
        }

        return empty($mensagemErro);
    }


}
