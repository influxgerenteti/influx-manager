<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class SalaFranqueadaBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\SalaFranqueadaRepository
     */
    private $salaFranqueadaRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->salaFranqueadaRepository = $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "salaRepository"       => $entityManager->getRepository(\App\Entity\Principal\Sala::class),
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
            if (self::verificaSalaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SALA]) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica e configura os campos que necessitam de relacionamento que sao opcionais na edicao
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\SalaFranqueada $objetoORM
     *
     * @return boolean
     */
    protected function configuraCamposRelacionaisOpcionais(&$parametros, &$mensagemErro, &$objetoORM)
    {
        $bRetornoFranqueada = true;
        $bRetornoSala       = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false)) {
            $bRetornoFranqueada = self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            if ($bRetornoFranqueada === true) {
                $objetoORM->setFranqueada($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SALA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_SALA]) === false)) {
            $bRetornoSala = self::verificaSalaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SALA]);
            if ($bRetornoSala === true) {
                $objetoORM->setSala($parametros[ConstanteParametros::CHAVE_SALA]);
            }
        }

        return ($bRetornoFranqueada && $bRetornoSala);
    }

    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\SalaFranqueadaRepository $salaFranqueadaRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\SalaFranqueada $salaFranqueadaORM
     *
     * @return boolean
     */
    public static function verificaSalaFranqueadaExiste(\App\Repository\Principal\SalaFranqueadaRepository $salaFranqueadaRepository, $id, &$mensagemErro, &$salaFranqueadaORM)
    {
        $salaFranqueadaORM = $salaFranqueadaRepository->find($id);
        if (is_null($salaFranqueadaORM) === true) {
            $mensagemErro = "Sala da franqueada não encontrada.";
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
     * @param \App\Entity\Principal\SalaFranqueada $objetoORM
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
     * @param \App\Entity\Principal\SalaFranqueada $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_LOTACAO_MAXIMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_LOTACAO_MAXIMA]) === false)) {
            $objetoORM->setLotacaoMaxima($parametros[ConstanteParametros::CHAVE_LOTACAO_MAXIMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PERSONAL]) === true) && ($parametros[ConstanteParametros::CHAVE_PERSONAL] !== "")) {
            $objetoORM->setPersonal($parametros[ConstanteParametros::CHAVE_PERSONAL]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }

    /**
     * Verifica se a sala na franqueada existe e está ativa
     *
     * @param \App\Repository\Principal\SalaFranqueadaRepository $repository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\SalaFranqueada $salaFranqueada
     *
     * @return boolean
     */
    public static function salaFranqueadaExisteEAtiva(\App\Repository\Principal\SalaFranqueadaRepository $repository, $id, &$mensagemErro, &$salaFranqueada)
    {
        if (self::verificaSalaFranqueadaExiste($repository, $id, $mensagemErro, $salaFranqueada) === false) {
            return false;
        }

        if ($salaFranqueada->getSituacao() !== 'A') {
            $mensagemErro = 'A sala selecionada está inativa.';
            return false;
        }

        return true;
    }


}
