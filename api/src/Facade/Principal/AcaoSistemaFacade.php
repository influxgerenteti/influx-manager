<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\AcaoSistemaBO;

/**
 *
 * @author Luiz A Costa
 */
class AcaoSistemaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AcaoSistemaRepository
     */
    private $acaoSistemaRepository;

    /**
     *
     * @var \App\BO\Principal\AcaoSistemaBO
     */
    private $acaoSistemaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->acaoSistemaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AcaoSistema::class);
        $this->acaoSistemaBO         = new AcaoSistemaBO(self::getEntityManager());
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
        $retornoRepositorio = $this->acaoSistemaRepository->filtraAcaoSistemaPorPagina($parametros);
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
     * @return array|\App\Entity\Principal\AcaoSistema
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->acaoSistemaRepository->buscaAcaoSistemaPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "AcaoSistema não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\AcaoSistema
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->acaoSistemaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $modulosORM = $parametros[ConstanteParametros::CHAVE_MODULOS];
            unset($parametros[ConstanteParametros::CHAVE_MODULOS]);
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AcaoSistema::class, true, $parametros);
            if (count($modulosORM) > 0) {
                foreach ($modulosORM as $moduloORM) {
                    $objetoORM->addModulo($moduloORM);
                }
            }

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
        $objetoORM = $this->acaoSistemaRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "AcaoSistema não encontrado na base de dados.";
        } else {
            if ($this->acaoSistemaBO->podeSalvar($parametros, $mensagemErro) === true) {
                $modulosORM = $parametros[ConstanteParametros::CHAVE_MODULOS];
                unset($parametros[ConstanteParametros::CHAVE_MODULOS]);
                $modulos  = $objetoORM->getModulo();
                $contador = $modulos->count();
                for ($i = 0;$i < $contador;$i++) {
                    $objetoORM->removeModulo($modulos->get($i));
                }

                if (count($modulosORM) > 0) {
                    foreach ($modulosORM as $moduloORM) {
                        $objetoORM->addModulo($moduloORM);
                    }
                }

                self::getFctHelper()->setParams($parametros, $objetoORM);
                self::flushSeguro($mensagemErro);
            }
        }//end if

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
