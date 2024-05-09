<?php

namespace App\Facade\Principal;

use App\BO\Principal\ServicoBO;
use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Dayan Freitas
 */
class ServicoFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\ServicoBO
     */
    private $servicoBO;


    /**
     *
     * @var \App\Repository\Principal\ServicoRepository
     */
    private $servicoRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->servicoBO         = new ServicoBO(self::getEntityManager());
        $this->servicoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Servico::class);
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

        $retornoRepositorio = $this->servicoRepository->listar($parametros);
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
        $objetoORM = null;
        $this->servicoBO->verificaServicoExiteId($this->servicoRepository, $id, $mensagemErro, $objetoORM);

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
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->servicoBO->podeSalvar($parametros, $mensagemErro) === true) {
            if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
                $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_CONCLUIDA;
            }

            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Servico::class, true, $parametros);
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
    public function atualizar(&$mensagemErro, $id, $parametros=[], &$servicoORM=null)
    {
        $objetoORM = $this->servicoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Serviço não encontrado na base de dados.";
        } else {
            $parametros[ConstanteParametros::CHAVE_ALUNO] = $objetoORM->getAluno()->getId();
            if ($this->servicoBO->podeSalvar($parametros, $mensagemErro) === true) {
                if ((bool) $parametros[ConstanteParametros::CHAVE_CANCELAMENTO] === true) {
                    $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_CANCELADO;
                }

                if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
                    $parametros[ConstanteParametros::CHAVE_SITUACAO] = SituacoesSistema::SITUACAO_CONCLUIDA;
                }

                self::getFctHelper()->setParams($parametros, $objetoORM);
                $servicoORM = $objetoORM;
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

    /**
     * Busca registro atraves do numero de protocolo
     *
     * @param string $query numero do protocolo para ser buscado
     *
     * @return NULL|\App\Entity\Principal\Servico
     */
    public function buscarServicosPorProtocolo($query)
    {

        return $this->servicoRepository->buscarServicoPorNumeroDeProtocolo($query);
    }


}
