<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\SalaAgendamentoPersonalBO;
use Symfony\Component\Validator\Constraints\IsNull;
use App\Helper\VariaveisCompartilhadas;
use App\Entity\Principal\SalaAgendamentoPersonal;

/**
 *
 * @author Gilberto M Martins
 */
class SalaAgendamentoPersonalFacade extends GenericFacade
{
      /**
     *
     * @var \App\BO\Principal\SalaAgendamentoPersonalBO $salaAgendamentoPersonalBO
     */
    private $salaAgendamentoPersonalBO;

     /**
     *
     * @var \App\Repository\Principal\SalaAgendamentoRepository
     */
    private $salaAgendamentoRepository;

     /**
     *
     * @var \App\Repository\Principal\SalaFranqueadaRepository
     */
    private $salaFranqueadaRepository;

         /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->salaAgendamentoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\SalaAgendamentoPersonal::class);
        $this->salaAgendamentoPersonalBO         = new SalaAgendamentoPersonalBO(self::getEntityManager());
        $this->salaFranqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\SalaFranqueada::class);
        $this->franqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
        
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar ($parametros)
    {
       // if ((isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) && ((int) $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA] === 1)) {
            $retornoRepositorio = $this->salaAgendamentoRepository->listar($parametros);

      //  } 

        return $retornoRepositorio;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id, $parametros = [])
    {
     
        $objetoORM = $this->salaAgendamentoRepository->buscarPersonalPorId($id, $parametros);

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\SalaAgendamentoPersonal
     */
    public function criar($parametros)
    {
        if(empty($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]) ||
            empty($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) ||
            empty($parametros[ConstanteParametros::CHAVE_DIA_SEMANA])) {
            return;
        }
        
        $objetoORM = null;
        $franqueadaId = VariaveisCompartilhadas::$franqueadaID;
        $salaFranqueadaORM = $this->salaFranqueadaRepository->buscarFranqueadaPorId($parametros['sala_franqueada'], $franqueadaId);
        $FranqueadaORM = $this->franqueadaRepository->find($franqueadaId);
        
        if ((isset($salaFranqueadaORM) === false) || (empty($salaFranqueadaORM) === true) || (Is_Null($salaFranqueadaORM) === true)) {
            $mensagemErro = "Sala Franqueada não encontrada.";
        }else{
            $dataInicio = new \DateTime($parametros[ConstanteParametros::CHAVE_HORA_INICIAL]);
            $dataFim = new \DateTime($parametros[ConstanteParametros::CHAVE_HORA_FINAL]);

            if($dataInicio > $dataFim) {
                return;
            }
            
            $params = [
                'sala_franqueada'   => $salaFranqueadaORM,
                'franqueada'        => $FranqueadaORM,
                'hora_inicial'      => $dataInicio,
                'hora_final'        => $dataFim,
                'dia_semana'        => $parametros['dia_semana']
            ];
            
            $objetoORM = \App\Factory\GeneralORMFactory::criar(SalaAgendamentoPersonal::class, true, $params);
            self::criarRegistro($objetoORM, $mensagemErro);
        }
        return $objetoORM;
    }

    public function deletar(SalaAgendamentoPersonal $disponibilidade) {
        $this->salaAgendamentoRepository->deletar($disponibilidade);
    }

    public function obterDisponibilidades($salaFranqueadaId) {
        return $this->salaAgendamentoRepository->getBySalaFranqueada($salaFranqueadaId);
    }
}
