<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Marcelo A Naegeler
 */
class MetaFranqueadaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\MetaFranqueadaRepository
     */
    private $metaFranqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\BO\Principal\MetaFranqueadaBO
     */
    private $metaFranqueadaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->franqueadaRepository     = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
        $this->metaFranqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\MetaFranqueada::class);
        $this->usuarioRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
        $this->metaFranqueadaBO         = new \App\BO\Principal\MetaFranqueadaBO(self::getEntityManager());
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
        $retornoRepositorio = $this->franqueadaRepository->buscarFranquiasComMetas($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);

        $retorno = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];

        return $retorno;
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
        $objetoORM = $this->metaFranqueadaRepository->buscarMetasFranquia($id, $parametros);
        if (is_null($objetoORM) === true) {
            $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = $id;
            if ($this->metaFranqueadaBO->podeCriar($parametros, $mensagemErro) === true) {
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MetaFranqueada::class, true, $parametros);
                self::persistSeguro($objetoORM, $mensagemErro);
            }
        } else {
            self::getFctHelper()->setParams($parametros, $objetoORM);
        }

        if (is_null($objetoORM) === false) {
            $historico = [
                ConstanteParametros::CHAVE_FRANQUEADA          => $objetoORM->getFranqueada(),
                ConstanteParametros::CHAVE_META_FRANQUEADA     => $objetoORM,
                ConstanteParametros::CHAVE_USUARIO             => $this->usuarioRepository->find(\App\Helper\VariaveisCompartilhadas::$usuarioID),
                ConstanteParametros::CHAVE_META_1              => $objetoORM->getMeta1(),
                ConstanteParametros::CHAVE_META_2              => $objetoORM->getMeta2(),
                ConstanteParametros::CHAVE_META_3              => $objetoORM->getMeta3(),
                ConstanteParametros::CHAVE_META_FRANQUEADORA_1 => $objetoORM->getMetaFranqueadora1(),
                ConstanteParametros::CHAVE_META_FRANQUEADORA_2 => $objetoORM->getMetaFranqueadora2(),
                ConstanteParametros::CHAVE_META_FRANQUEADORA_3 => $objetoORM->getMetaFranqueadora3(),
                ConstanteParametros::CHAVE_DATA_CRIACAO        => new \DateTime(),
            ];

            $historicoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MetaFranqueadaHistorico::class, true, $historico);
            self::persistSeguro($historicoORM, $mensagemErro);
        }

        self::flushSeguro($mensagemErro);

        return empty($mensagemErro);
    }


}
