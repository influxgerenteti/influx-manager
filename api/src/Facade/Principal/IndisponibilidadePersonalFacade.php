<?php

namespace App\Facade\Principal;

use App\Helper\ConstanteParametros;
use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
/**
 *
 * @author Marcelo A Naegeler
 */
class IndisponibilidadePersonalFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Principal\IndisponibilidadePersonalRepository
     */
    private $indisponibilidadePersonalRepository;

    /**
     *
     * @var \App\BO\Principal\InteressadoBO
     */
    private $interessadoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->indisponibilidadePersonalRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\IndisponibilidadePersonal::class);
        $this->indisponibilidadePersonalBO         = new \App\BO\Principal\IndisponibilidadePersonalBO(self::getEntityManager());
    }

    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return \App\Entity\Principal\IndisponibilidadePersonal[]
     */
    public function listar($parametros)
    {
        $resultado = $this->indisponibilidadePersonalRepository->listar($parametros);
        return [
            ConstanteParametros::CHAVE_ITENS => $resultado,
            ConstanteParametros::CHAVE_TOTAL => count($resultado),
        ];
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return \App\Entity\Principal\IndisponibilidadePersonal
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        return $this->indisponibilidadePersonalRepository->buscarPorId($id);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return \App\Entity\Principal\IndisponibilidadePersonal
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = \App\Helper\VariaveisCompartilhadas::$franqueadaID;
        $objetoORM = null;

        if ($this->indisponibilidadePersonalBO->validaHorarios($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\IndisponibilidadePersonal::class, true, $parametros);
            if (empty($mensagemErro) === true) {
                self::criarRegistro($objetoORM, $mensagemErro);
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
        $objetoORM = $this->indisponibilidadePersonalRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Indisponibilidade não encontrada.";
        } else {
            $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = \App\Helper\VariaveisCompartilhadas::$franqueadaID;
            if ($this->indisponibilidadePersonalBO->validaHorarios($parametros, $mensagemErro) === true) {
                \App\Helper\FunctionHelper::setParams($parametros, $objetoORM);
                self::flushSeguro($mensagemErro);
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
        $objetoORM = $this->indisponibilidadePersonalRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Indisponibilidade não encontrada";
        } else {
            self::removerRegistro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }


}
