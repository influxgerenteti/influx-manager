<?php

namespace App\Facade\Principal;

use App\BO\Principal\TipoOcorrenciaBO;
use App\Entity\Principal\TipoOcorrencia;
use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Dayan Freitas
 */
class TipoOcorrenciaFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\TipoOcorrenciaBO
     */
    private $tipoOcorrenciaBO;

    /**
     *
     * @var \App\Repository\Principal\TipoOcorrenciaRepository $tipoOcorrenciaRepository
     */
    private $tipoOcorrenciaRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tipoOcorrenciaBO         = new TipoOcorrenciaBO(self::getEntityManager());
        $this->tipoOcorrenciaRepository = self::getEntityManager()->getRepository(TipoOcorrencia::class);
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
        $retornoRepositorio = $this->tipoOcorrenciaRepository->listar($parametros);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems()
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
        $objetoORM = $this->tipoOcorrenciaRepository->buscaRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Tipo de ocorrência não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $parametros)
    {
        $objetoORM = null;
        if ($this->tipoOcorrenciaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TipoOcorrencia::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
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
    public function atualizar(&$mensagemErro, $id, $parametros)
    {

        $objetoORM    = $this->tipoOcorrenciaRepository->find($id);

        \App\Helper\FunctionHelper::setParams($parametros, $objetoORM);
        $this->getEntityManager()->persist($objetoORM);
        $this->getEntityManager()->flush();

        return true;
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
