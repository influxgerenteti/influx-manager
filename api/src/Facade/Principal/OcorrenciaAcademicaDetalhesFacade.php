<?php

namespace App\Facade\Principal;

use App\BO\Principal\OcorrenciaAcademicaDetalhesBO;
use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Dayan Freitas
 */
class OcorrenciaAcademicaDetalhesFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\OcorrenciaAcademicaDetalhesBO
     */
    private $ocorrenciaAcademicaDetalhesBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->ocorrenciaAcademicaDetalhesBO = new  OcorrenciaAcademicaDetalhesBO(self::getEntityManager());
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
     * @param object $ocorrenciaORM objeto da ocorrencia academica
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param boolean $bPersistFlush Para identificar se deve aplicar o persist e o flush, ou só um ou outro
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $ocorrenciaORM=null, $parametros=[], $bPersistFlush=true)
    {
        $objetoORM = null;
        if ($ocorrenciaORM !== null) {
            $parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA] = $ocorrenciaORM;
        }

        if ($this->ocorrenciaAcademicaDetalhesBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\OcorrenciaAcademicaDetalhes::class, true, $parametros);
            if ($bPersistFlush === true) {
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


    /**
     * Verifica se já há ocorrencia academica detalhes cadastrado com os parametros passados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return boolean
     */
    public function possuiOcorrencia(&$mensagemErro, $parametros=[])
    {
        $tipo         = $parametros[ConstanteParametros::CHAVE_TIPO_OCORRENCIA];
        $contratoORM  = $parametros[ConstanteParametros::CHAVE_CONTRATO];
        $turmaAulaORM = $parametros[ConstanteParametros::CHAVE_TURMA_AULA];
        $ocorrencias  = $contratoORM->getOcorrenciaAcademicas();

        foreach ($ocorrencias as $ocorrenciaORM) {
            $tipoOcorrencia = $ocorrenciaORM->getTipoOcorrencia()->getTipo();
            if ($tipoOcorrencia !== $tipo) {
                continue;
            }

            foreach ($ocorrenciaORM->getOcorrenciaAcademicaDetalhes() as $detalhesORM) {
                if ($detalhesORM->getTurmaAula() === $turmaAulaORM) {
                    return true;
                }
            }
        }

        return false;
    }


}
