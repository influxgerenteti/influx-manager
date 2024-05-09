<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class LivroBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\LivroRepository
     */
    private $livroRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->livroRepository = $entityManager->getRepository(\App\Entity\Principal\Livro::class);
        parent::configuraGenericBO(
            [
                "cursoRepository"             => $entityManager->getRepository(\App\Entity\Principal\Curso::class),
                "itemRepository"              => $entityManager->getRepository(\App\Entity\Principal\Item::class),
                "planejamentoLicaoRepository" => $entityManager->getRepository(\App\Entity\Principal\PlanejamentoLicao::class),
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
        if (self::verificaItemExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
            if (self::verificaPlanejamentoLicao($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO]) === true) {
                if ((isset($parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO]) === false)) {
                    if ($this->verificaLivroExiste($this->livroRepository, $parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO], $mensagemErro, $parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO]) === true) {
                        return true;
                    }
                } else {
                    $parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO] = null;
                }

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
     * @param \App\Entity\Principal\Livro $objetoORM
     *
     * @return boolean
     */
    protected function configuraCamposRelacionaisOpcionais(&$parametros, &$mensagemErro, &$objetoORM)
    {
        $bRetornoCurso = true;
        $bRetornoItem  = true;
        $bRetornoPlanejamentoLicao = true;
        $bRetornoProximoLivro      = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true)&&(count($parametros[ConstanteParametros::CHAVE_CURSO]) > 0)) {
            $objetoORM->getCurso()->clear();
            $tempArray = [];
            foreach ($parametros[ConstanteParametros::CHAVE_CURSO] as $cursoIdOrm) {
                $tempArray[ConstanteParametros::CHAVE_CURSO] = $cursoIdOrm;
                $bRetornoCurso = self::verificaCursoExisteBO($tempArray, $mensagemErro, $cursoIdOrm);
                if ($bRetornoCurso === true) {
                    $objetoORM->addCurso($cursoIdOrm);
                }
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ITEM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ITEM]) === false)) {
            $bRetornoItem = self::verificaItemExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ITEM]);
            if ($bRetornoItem === true) {
                $objetoORM->setItem($parametros[ConstanteParametros::CHAVE_ITEM]);
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO]) === false)) {
            $bRetornoPlanejamentoLicao = self::verificaPlanejamentoLicao($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO]);
            if ($bRetornoPlanejamentoLicao === true) {
                $objetoORM->setPlanejamentoLicao($parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO]);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO]) === false) {
                $bRetornoProximoLivro = $this->verificaLivroExiste($this->livroRepository, $parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO], $mensagemErro, $parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO]);
            } else {
                $parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO] = null;
            }

            if ($bRetornoProximoLivro === true) {
                $objetoORM->setProximoLivro($parametros[ConstanteParametros::CHAVE_PROXIMO_LIVRO]);
            }
        }

        return ($bRetornoCurso && $bRetornoItem && $bRetornoPlanejamentoLicao && $bRetornoProximoLivro);
    }

    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\LivroRepository $livroRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Livro $livroORM
     *
     * @return boolean
     */
    public static function verificaLivroExiste(\App\Repository\Principal\LivroRepository $livroRepository, $id, &$mensagemErro, &$livroORM)
    {
        $livroORM = $livroRepository->find($id);
        if (is_null($livroORM) === true) {
            $mensagemErro = "Livro não encontrado.";
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
            if ((isset($parametros[ConstanteParametros::CHAVE_CURSO]) === true) && (count($parametros[ConstanteParametros::CHAVE_CURSO]) > 0)) {
                return true;
            }

            $mensagemErro = "Array de cursos não encontrado";
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
     * @param \App\Entity\Principal\Livro $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }
    }

    /**
     * Verifica se o livro existe e está ativo
     *
     * @param \App\Repository\Principal\LivroRepository $repository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Livro $livro
     *
     * @return boolean
     */
    public static function livroExisteEAtivo(\App\Repository\Principal\LivroRepository $repository, $id, &$mensagemErro, &$livro)
    {
        if (self::verificaLivroExiste($repository, $id, $mensagemErro, $livro) === false) {
            return false;
        }

        if ($livro->getSituacao() !== 'A') {
            $mensagemErro = 'O livro selecionado está inativo.';
            return false;
        }

        return true;
    }


}
