<?php

namespace App\Facade\Principal;


use App\BO\Principal\CepBO;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class CepFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\CepRepository
     */
    private $cepRepository;

    /**
     *
     * @var \App\BO\Principal\CepBO
     */
    private $cepBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->cepRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Cep::class);
        $this->cepBO         = new CepBO(self::getEntityManager());
    }

    /**
     * Consulta o CEP no banco de dados
     *
     * @param string $cep Cep do endereco
     *
     * @return \App\Entity\Principal\Cep|NULL
     */
    protected function consultaCepBancoDados($cep)
    {
        return $this->cepRepository->findOneBy(["cep" => $cep]);
    }

    /**
     * Consulta o CEP na API externa
     *
     * @param string $mensagemErro Ponteiro de retorno de mensagem de erro para o front-end
     * @param string $cep CEP a ser consultado na API
     *
     * @return \App\Entity\Principal\Cep|NULL
     */
    protected function consultaCepApi(&$mensagemErro, $cep)
    {
        $objetoORM = null;
        if (self::getCurlHelper()->executarUrl("http://viacep.com.br/ws/" . $cep . "/json/") === true) {
            $retornoApiJsonDecode = json_decode(self::getCurlHelper()->getRawBody());
            if ((is_null($retornoApiJsonDecode) === true) || ((isset($retornoApiJsonDecode->erro) === true) && ($retornoApiJsonDecode->erro === true))) {
                $mensagemErro = "CEP não encontrado, favor entrar em contato com o suporte técnico.";
            } else {
                $objetoORM = $this->cepBO->importarCepDB(self::getEntityManager(), $retornoApiJsonDecode);
            }
        } else {
            $mensagemErro = "Ocorreu um erro ao executar o WS de CEP(ViaCep), segue o erro do cURL:" . self::getCurlHelper()->getErrorCurl();
            self::getCurlHelper()->limparRequisicaoAnterior();
        }

        return $objetoORM;
    }

    /**
     * Busca o registro pelo CEP
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param string $cep Cep a ser consultado na base de dados
     *
     * @return array|\App\Entity\Principal\Cep
     */
    public function buscarCep(&$mensagemErro, $cep)
    {
        $cepORM = $this->consultaCepBancoDados($cep);
        if ($cepORM === null) {
            $cepORM = $this->consultaCepApi($mensagemErro, $cep);
        }

        return $cepORM;
    }


}
