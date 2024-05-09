<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class SistemaAvaliacaoBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\SistemaAvaliacaoRepository
     */
    private $sistemaAvaliacaoRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->sistemaAvaliacaoRepository = $entityManager->getRepository(\App\Entity\Principal\SistemaAvaliacao::class);
        parent::configuraGenericBO(
            [
                "conceitoAvaliacaoRepository" => $entityManager->getRepository(\App\Entity\Principal\ConceitoAvaliacao::class),
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
    protected function verificaCamposRelacionais(&$parametros, &$mensagemErro)
    {
        if (self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_CONCEITO_APROVACAO, $parametros[ConstanteParametros::CHAVE_CONCEITO_APROVACAO]) === true) {
            if (self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_CONCEITO_CORTE_COMPROMISSO_QUALIDADE, $parametros[ConstanteParametros::CHAVE_CONCEITO_CORTE_COMPROMISSO_QUALIDADE]) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\SistemaAvaliacaoRepository $sistemaAvaliacaoRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\SistemaAvaliacao $sistemaAvaliacaoORM
     *
     * @return boolean
     */
    public static function verificaSistemaAvaliacaoExiste(\App\Repository\Principal\SistemaAvaliacaoRepository $sistemaAvaliacaoRepository, $id, &$mensagemErro, &$sistemaAvaliacaoORM)
    {
        $sistemaAvaliacaoORM = $sistemaAvaliacaoRepository->find($id);
        if (is_null($sistemaAvaliacaoORM) === true) {
            $mensagemErro = "Sistema avaliação não encontrado.";
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
        if ($this->verificaCamposRelacionais($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\SistemaAvaliacao $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FREQUENCIA_MINIMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FREQUENCIA_MINIMA]) === false)) {
            $objetoORM->setFrequenciaMinima($parametros[ConstanteParametros::CHAVE_FREQUENCIA_MINIMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FREQUENCIA_CORTE_COMPROMISSO_QUALIDADE]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FREQUENCIA_CORTE_COMPROMISSO_QUALIDADE]) === false)) {
            $objetoORM->setFrequenciaCorteCompromissoQualidade($parametros[ConstanteParametros::CHAVE_FREQUENCIA_CORTE_COMPROMISSO_QUALIDADE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_APROVACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_APROVACAO]) === false)) {
            $objetoORM->setNotaAprovacao($parametros[ConstanteParametros::CHAVE_NOTA_APROVACAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_CORTE_COMPROMISSO_QUALIDADE]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_CORTE_COMPROMISSO_QUALIDADE]) === false)) {
            $objetoORM->setNotaCorteCompromissoQualidade($parametros[ConstanteParametros::CHAVE_NOTA_CORTE_COMPROMISSO_QUALIDADE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_EXCLUIDO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_EXCLUIDO]) === false)) {
            $objetoORM->setExcluido($parametros[ConstanteParametros::CHAVE_EXCLUIDO]);
        }
    }


}
