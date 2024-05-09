<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Marcelo A Naegeler
 */
class TransferenciaBancariaBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\TransferenciaBancariaRepository
     */
    private $transferenciaBancariaRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->transferenciaBancariaRepository = $entityManager->getRepository(\App\Entity\Principal\TransferenciaBancaria::class);
        parent::configuraGenericBO(
            [
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
            return true;
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
            $mensagemErro = "O valor da transferência é obrigatório";
            return false;
        }

        if (empty($parametros[ConstanteParametros::CHAVE_DATA_ESTORNO]) === true) {
            $parametros[ConstanteParametros::CHAVE_DATA_ESTORNO] = null;
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


}
