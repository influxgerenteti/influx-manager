<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ChequeBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\ChequeRepository
     */
    private $chequeRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->chequeRepository = $entityManager->getRepository(\App\Entity\Principal\Cheque::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository"            => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "bancoRepository"                 => $entityManager->getRepository(\App\Entity\Principal\Banco::class),
                "pessoaRepository"                => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "usuarioRepository"               => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "motivoDevolucaoChequeRepository" => $entityManager->getRepository(\App\Entity\Principal\MotivoDevolucaoCheque::class),
            ]
        );
    }

    /**
     * Verifica campos relacionados a entidade
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        $bRetorno = false;
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === true) && (is_object($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)) {
                $bRetorno = true;
            } else if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ATENDENTE_USUARIO]) === true) {
                $bRetorno = true;
            } else {
                $bRetorno = false;
            }
        }

        return $bRetorno;
    }

    protected function configuraCamposRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoMotivoDevolucaoCheque = true;
        $bRetornoPessoa = true;

        if (isset($parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE]) === false) {
                $bRetornoMotivoDevolucaoCheque = self::verificaMotivoDevolucaoChequeExiste($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE]);
            } else {
                $parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE] = null;
                $bRetornoMotivoDevolucaoCheque = true;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PESSOA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PESSOA]) === false)) {
            $bRetornoPessoa = self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PESSOA], ConstanteParametros::CHAVE_PESSOA, true);
        }

        return ($bRetornoMotivoDevolucaoCheque && $bRetornoPessoa);
    }

    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\ChequeRepository $chequeRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Cheque $chequeORM
     *
     * @return boolean
     */
    public static function verificaChequeExiste(\App\Repository\Principal\ChequeRepository $chequeRepository, $id, &$mensagemErro, &$chequeORM)
    {
        $chequeORM = $chequeRepository->find($id);
        if (is_null($chequeORM) === true) {
            $mensagemErro = "Cheque não encontrado.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se os campos relacionais estao validos e indica se pode salvar ou não
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->configuraCamposRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\Cheque $objetoORM
     * @param string $mensagemErro mensagem de erro
     */
    public function configuraParametros($parametros, &$objetoORM, &$mensagemErro)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_NUMERO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NUMERO]) === false)) {
            $objetoORM->setNumero($parametros[ConstanteParametros::CHAVE_NUMERO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TITULAR]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TITULAR]) === false)) {
            $objetoORM->setTitular($parametros[ConstanteParametros::CHAVE_TITULAR]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_COMPLEMENTO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_COMPLEMENTO]) === false)) {
            $objetoORM->setComplemento($parametros[ConstanteParametros::CHAVE_COMPLEMENTO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_AGENCIA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_AGENCIA]) === false)) {
            $objetoORM->setAgencia($parametros[ConstanteParametros::CHAVE_AGENCIA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONTA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_CONTA]) === false)) {
            $objetoORM->setConta($parametros[ConstanteParametros::CHAVE_CONTA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA];
            if (($data instanceof \DateTime) === false) {
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            }
            if ($data == false) {
                $data = $parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA];
                \App\Helper\FunctionHelper::formataCampoDateTime($data, $data);
            }

            $objetoORM->setDataBomPara($data);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_BAIXA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_BAIXA]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_BAIXA];
            if (($data instanceof \DateTime) === false) {
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            }

            $objetoORM->setDataBaixa($data);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO];
            if (($data instanceof \DateTime) === false) {
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            }

            $objetoORM->setDataDevolucao($data);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_BANCO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_BANCO]) === false)) {
            $objetoORM->setBanco($parametros[ConstanteParametros::CHAVE_BANCO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE]) === false)) {
            $objetoORM->setMotivoDevolucaoCheque($parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_VALOR]) === true) {
            $objetoORM->setValor($parametros[ConstanteParametros::CHAVE_VALOR]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO]) === true) {
            $objetoORM->setValorDesconto($parametros[ConstanteParametros::CHAVE_VALOR_DESCONTO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO]) === true) {
            $objetoORM->setObservacao($parametros[ConstanteParametros::CHAVE_OBSERVACAO]);
        }

    }


}
