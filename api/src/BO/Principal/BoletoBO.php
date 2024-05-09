<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class BoletoBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\BoletoRepository
     */
    private $boletoRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->boletoRepository = $entityManager->getRepository(\App\Entity\Principal\Boleto::class);
        parent::configuraGenericBO(
            [
                "contaRepository"         => $entityManager->getRepository(\App\Entity\Principal\Conta::class),
                "franqueadaRepository"    => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "tituloReceberRepository" => $entityManager->getRepository(\App\Entity\Principal\TituloReceber::class),
            ]
        );
    }

    /**
     * Verifica se os campos de relacionamentos estao certos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
                self::buscaProximoNossoNumero($parametros[ConstanteParametros::CHAVE_CONTA], $parametros[ConstanteParametros::CHAVE_NOSSO_NUMERO]);
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica os campos não relacionais e converte conforme necessário
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaDemaisCampos (&$parametros, &$mensagemErro)
    {
        if (empty($parametros[ConstanteParametros::CHAVE_VALOR]) === true) {
            $mensagemErro = "O valor do boleto é obrigatório";
            return false;
        }

        if (empty($parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO]) === true) {
            $parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO] = 0;
        }

        if (($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO] instanceof \DateTime) === false) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO], $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO]);
        }

        return true;
    }

    /**
     * Verifica se pode prosseguir com a criacao de registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaDemaisCampos($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o Boleto existe atraves do campo ID
     *
     * @param \App\Repository\Principal\BoletoRepository $boletoRepository Repositorio da Conta
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Boleto $resultadoORM Ponteiro para retornar o objeto
     *
     * @return boolean
     */
    public static function verificaBoletoExiste(\App\Repository\Principal\BoletoRepository $boletoRepository, $id, &$mensagemErro, &$resultadoORM=null)
    {
        $resultadoORM = $boletoRepository->find($id);
        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "Boleto não foi encontrado na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Busca o próximo número do boleto
     *
     * @param null|\App\Entity\Principal\Conta $contaORM
     * @param integer $proximoNossoNumero
     *
     * @return boolean
     */
    public static function buscaProximoNossoNumero ($contaORM, &$proximoNossoNumero)
    {
        $proximoNossoNumero = $contaORM->getUltimoNossoNumero() + 1;
        $contaORM->setUltimoNossoNumero($proximoNossoNumero);
    }


}
