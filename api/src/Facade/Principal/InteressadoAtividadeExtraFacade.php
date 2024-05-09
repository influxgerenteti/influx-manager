<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\InteressadoAtividadeExtraBO;

/**
 *
 * @author Luiz A Costa
 */
class InteressadoAtividadeExtraFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AtividadeExtraRepository
     */
    private $atividadeExtraRepository;

    /**
     *
     * @var \App\Repository\Principal\InteressadoAtividadeExtraRepository
     */
    private $interessadoAtividadeExtraRepository;

    /**
     *
     * @var \App\BO\Principal\InteressadoAtividadeExtraBO
     */
    private $interessadoAtividadeExtraBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->atividadeExtraRepository            = self::getEntityManager()->getRepository(\App\Entity\Principal\AtividadeExtra::class);
        $this->interessadoAtividadeExtraRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\InteressadoAtividadeExtra::class);
        $this->interessadoAtividadeExtraBO         = new InteressadoAtividadeExtraBO(self::getEntityManager());
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
     * @param \App\Entity\Principal\AtividadeExtra $atividadeExtraORM entidade atividade extra
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\InteressadoAtividadeExtra
     */
    public function criar(&$mensagemErro, $atividadeExtraORM, &$parametros=[])
    {
        $objetoORM = null;
        $parametrosInteressadoAtividadeExtra = [
            ConstanteParametros::CHAVE_ATIVIDADE_EXTRA => $atividadeExtraORM,
            ConstanteParametros::CHAVE_INTERESSADO     => $parametros[ConstanteParametros::CHAVE_INTERESSADO],
            ConstanteParametros::CHAVE_LIVRO           => $parametros[ConstanteParametros::CHAVE_LIVRO],
        ];
        if ($this->interessadoAtividadeExtraBO->podeCriar($parametrosInteressadoAtividadeExtra, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\InteressadoAtividadeExtra::class, true, $parametrosInteressadoAtividadeExtra);
            self::persistSeguro($objetoORM, $mensagemErro);
            $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM] = $objetoORM->getInteressado();
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria da atividade extra
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, &$parametros=[])
    {
        $objetoORM = $this->atividadeExtraRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "AtividadeExtra não encontrada na base de dados.";
        } else {
            $interessadoAtividadeExtraORM        = $objetoORM->getInteressadoAtividadeExtra();
            $parametrosInteressadoAtividadeExtra = [
                ConstanteParametros::CHAVE_INTERESSADO => $parametros[ConstanteParametros::CHAVE_INTERESSADO],
                ConstanteParametros::CHAVE_LIVRO       => $parametros[ConstanteParametros::CHAVE_LIVRO],
            ];
            if ($this->interessadoAtividadeExtraBO->podeCriar($parametrosInteressadoAtividadeExtra, $mensagemErro) === true) {
                $interessadoAtividadeExtraORM->setInteressado($parametrosInteressadoAtividadeExtra[ConstanteParametros::CHAVE_INTERESSADO]);
                $interessadoAtividadeExtraORM->setLivro($parametrosInteressadoAtividadeExtra[ConstanteParametros::CHAVE_LIVRO]);
                $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM] = $interessadoAtividadeExtraORM->getInteressado();
            }
        }

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
