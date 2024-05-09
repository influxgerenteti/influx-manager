<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
/**
 *
 * @author Marcelo A Naegeler
 */
class RenegociacaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\RenegociacaoRepository
     */
    private $renegociacaoRepository;

    /**
     *
     * @var \App\Repository\Principal\PessoaRepository
     */
    private $pessoaRepository;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\TituloReceberRepository
     */
    private $tituloReceberRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->renegociacaoRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\Renegociacao::class);
        $this->pessoaRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Pessoa::class);
        $this->franqueadaRepository    = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
        $this->tituloReceberRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TituloReceber::class);
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
        return $this->renegociacaoRepository->listar($parametros);
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
     * @param boolean $deveGravar Se deve executar o flush
     *
     * @return \App\Entity\Principal\Renegociacao
     */
    public function criar(&$mensagemErro, $parametros=[], $contaReceberORM, $deveGravar=true)
    {
        
        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = $this->franqueadaRepository->find($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA] = $this->pessoaRepository->find($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA]);


        // echo json_ecode($parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]);
        // if ($parametros['titulos_receber'][0][ConstanteParametros::CHAVE_SACADO_PESSOA] == "")
        // {
        //     $parametros['titulos_receber'][0][ConstanteParametros::CHAVE_SACADO_PESSOA] = $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA];

        // }
        
        // $numeroTituloRef = $parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER_RENEGOCIADOS][0];
        // $tituloReferenciaORM = $this->tituloReceberRepository->find($numeroTituloRef);
        // $parametros['conta_receber'] = $tituloReferenciaORM->getContaReceber();
       
        $parametros['conta_receber'] = $contaReceberORM;
        
        $renegociacaoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Renegociacao::class, true, $parametros);

        $renegociacaoORM->setContaReceber($contaReceberORM);


        //SOMA TITULOS
        $total = 0;
        $titulos = $this->tituloReceberRepository->findBy(["conta_receber" =>  $contaReceberORM->getId()]);
        foreach ($titulos as $titulo) {
            $total += $titulo->getValorOriginal();
            $titulo->setVAlorItem( $titulo->getValorOriginal());
        }

        $contaReceberORM->setValorTotal($total);
        
        self::persistSeguro($renegociacaoORM, $mensagemErro);

        if (isset($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER_RENEGOCIADOS]) === true) {
            foreach ($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER_RENEGOCIADOS] as $titulo) {
                $tituloORM = $this->tituloReceberRepository->find($titulo);
                if (is_null($tituloORM) === false) {
                    $renegociacaoORM->addTitulosReceber($tituloORM);
                    $tituloORM->setSituacao(\App\Helper\SituacoesSistema::SITUACAO_SUBSTITUIDO);
                }
            }
        }

        if ($deveGravar === true) {
            self::flushSeguro($mensagemErro);
        }

        return $renegociacaoORM;
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


}
