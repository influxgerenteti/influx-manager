<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ValorHoraBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\ValorHoraRepository
     */
    private $valorHoraRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->valorHoraRepository = $entityManager->getRepository(\App\Entity\Principal\ValorHora::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository"      => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "valorHoraLinhasRepository" => $entityManager->getRepository(\App\Entity\Principal\ValorHoraLinhas::class),
                "nivelInstrutorRepository"  => $entityManager->getRepository(\App\Entity\Principal\NivelInstrutor::class),
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
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaValorHoraLinhasExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS]) === true) {
                if (self::verificaNivelInstrutorExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica e configura os campos que necessitam de relacionamento que sao opcionais na edicao
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ValorHora $objetoORM
     *
     * @return boolean
     */
    protected function configuraCamposRelacionaisOpcionais(&$parametros, &$mensagemErro, &$objetoORM)
    {
        $bRetornoFranqueada      = true;
        $bRetornoValorHoraLinhas = true;
        $bRetornoNivelInstrutor  = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false)) {
            $bRetornoFranqueada = self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            if ($bRetornoFranqueada === true) {
                $objetoORM->setFranqueada($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS]) === false)) {
            $bRetornoValorHoraLinhas = self::verificaValorHoraLinhasExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS]);
            if ($bRetornoValorHoraLinhas === true) {
                $objetoORM->setValorHoraLinhas($parametros[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS]);
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === false)) {
            $bRetornoNivelInstrutor = self::verificaNivelInstrutorExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]);
            if ($bRetornoNivelInstrutor === true) {
                $objetoORM->setNivelInstrutor($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]);
            }
        }

        return ($bRetornoFranqueada && $bRetornoValorHoraLinhas && $bRetornoNivelInstrutor);
    }

    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\ValorHoraRepository $valorHoraRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ValorHora $valorHoraORM
     *
     * @return boolean
     */
    public static function verificaValorHoraExiste(\App\Repository\Principal\ValorHoraRepository $valorHoraRepository, $id, &$mensagemErro, &$valorHoraORM)
    {
        $valorHoraORM = $valorHoraRepository->find($id);
        if (is_null($valorHoraORM) === true) {
            $mensagemErro = "Valor Hora não encontrado.";
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
            return true;
        }

        return false;
    }

    /**
     * Realiza as verificacoes nos campos relacionaveis e configura os indices com os objetos
     * Se algum valor nao existir ele retornara false
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Livro $objetoORM
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro, &$objetoORM)
    {
        return $this->configuraCamposRelacionaisOpcionais($parametros, $mensagemErro, $objetoORM);
    }

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\ValorHora $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR]) === true) && (empty($parametros[ConstanteParametros::CHAVE_VALOR]) === false)) {
            $objetoORM->setValor($parametros[ConstanteParametros::CHAVE_VALOR]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_VALOR_EXTRA]) === true) {
            $objetoORM->setValorExtra($parametros[ConstanteParametros::CHAVE_VALOR_EXTRA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }


}
