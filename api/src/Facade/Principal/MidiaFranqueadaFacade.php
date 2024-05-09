<?php

namespace App\Facade\Principal;

use App\BO\Principal\MidiaFranqueadaBO;
use App\Entity\Principal\MidiaFranqueada;
use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Dayan Freitas
 */
class MidiaFranqueadaFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Principal\MidiaFranqueadaRepository
     */
    private $midiaFranqueadaRepository;
    /**
     *
     * @var \App\BO\Principal\MidiaFranqueadaBO
     */
    private $midiaFranqueadaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->midiaFranqueadaRepository = self::getEntityManager()->getRepository(MidiaFranqueada::class);
        $this->midiaFranqueadaBO         = new MidiaFranqueadaBO(self::getEntityManager());
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
        $retornoRepositorio = $this->midiaFranqueadaRepository->filtrarPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
        $objetoORM = null;
        if ($this->midiaFranqueadaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MidiaFranqueada::class, true, $parametros);
            $this->midiaFranqueadaBO->configuraParametros($parametros, $objetoORM);

            self::persistSeguro($objetoORM, $mensagemErro);
            if (empty($mensagem) === true) {
                self::flushSeguro($mensagemErro);
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
        $objetoORM = $this->midiaFranqueadaRepository->findOneBy(
            [
                'franqueada' => (int) $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                'midia'      => (int) $parametros[ConstanteParametros::CHAVE_MIDIA],
            ]
        );

        if (is_null($objetoORM) === false) {
            if (isset($parametros[ConstanteParametros::CHAVE_VISIBILIDADE]) === true) {
                $objetoORM->setVisibilidade($parametros[ConstanteParametros::CHAVE_VISIBILIDADE]);
            }
        } else {
            if ($this->midiaFranqueadaBO->podeSalvar($parametros, $mensagemErro) === true) {
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MidiaFranqueada::class, true, $parametros);
                self::persistSeguro($objetoORM, $mensagemErro);
            }
        }

        if (empty($mensagem) === true) {
            self::flushSeguro($mensagemErro);
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
