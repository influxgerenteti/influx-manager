<?php

namespace App\Facade\Principal;


use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\VariaveisCompartilhadas;
/**
 *
 * @author Luiz A Costa
 */
class FormaPagamentoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FormaPagamentoRepository
     */
    private $formaPagamentoRepository;


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
        $this->formaPagamentoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FormaPagamento::class);
        $this->franqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
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
        $retornoRepositorio = $this->formaPagamentoRepository->filtraFormaPagamentoPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @param boolean $retornarObjeto se deverá ser retornado como instância de classe
     *
     * @return array|\App\Entity\Principal\FormaPagamento
     */
    public function buscarPorId(&$mensagemErro, $id, $retornarObjeto=false)
    {
        $objetoORM = $this->formaPagamentoRepository->buscarPorId($id, $retornarObjeto);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Forma de pagamento não encontrado.";
        }

        return $objetoORM;
    }

    /**
     * Busca as formas de pagamento pela descrição
     *
     * @param string $descricao
     *
     * @return \App\Entity\Principal\FormaPagamento[]
     */
    public function buscarPorDescricao ($descricao)
    {
        return $this->formaPagamentoRepository->buscarVariosPorDescricao($descricao);
    }

    /**
     * Busca uma forma de pagamento que conforme propriedades
     *
     * @param array $propriedades
     *
     * @return \App\Entity\Principal\FormaPagamento
     */
    public function buscarPorPropriedades ($propriedades)
    {
        return $this->formaPagamentoRepository->findOneBy($propriedades);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\FormaPagamento
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        $parametros['franqueada'] = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        // if (is_null($this->formaPagamentoRepository->buscaPorDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO])) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\FormaPagamento::class, true, $parametros);
            // $franqueadaORM = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
            // $objetoORM->setFranqueada(VariaveisCompartilhadas::$franqueadaID);
            self::criarRegistro($objetoORM, $mensagemErro);
        // } else {
        //     $mensagemErro = "Essa forma de pagamento já se encontra cadastrada no sistema.";
        // }
            
        $result = $this->formaPagamentoRepository->find($objetoORM->getId());
        return $result;
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
        $objetoORM = $this->formaPagamentoRepository->find($id);
        if (is_null($objetoORM) === false) {
            if ((is_null($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)
                && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)
            ) {
                $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
            }

            if (is_null($parametros[ConstanteParametros::CHAVE_DESCRICAO_ABREVIADA]) === false) {
                $objetoORM->setDescricaoAbreviada($parametros[ConstanteParametros::CHAVE_DESCRICAO_ABREVIADA]);
            }

            $objetoORM->setLiquidacaoImediata($parametros[ConstanteParametros::CHAVE_LIQUIDACAO_IMEDIATA]);            
            $objetoORM->setFormaBoleto($parametros[ConstanteParametros::CHAVE_FORMA_BOLETO]);
            $objetoORM->setFormaCheque($parametros[ConstanteParametros::CHAVE_FORMA_CHEQUE]);
            $objetoORM->setFormaCartao($parametros[ConstanteParametros::CHAVE_FORMA_CARTAO]);
            $objetoORM->setFormaCartaoDebito($parametros[ConstanteParametros::CHAVE_FORMA_CARTAO_DEBITO]);
            $objetoORM->setFormaTrasferencia($parametros[ConstanteParametros::CHAVE_FORMA_TRANSFERENCIA]);
            // $objetoORM->setFranqueada($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

            self::flushSeguro($mensagemErro);
        } else {
            $mensagemErro = "Essa forma de pagamento já se encontra cadastrada no sistema.";
        }//end if

        return empty($mensagemErro);
    }


}
