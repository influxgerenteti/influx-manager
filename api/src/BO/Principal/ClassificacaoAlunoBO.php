<?php
namespace App\BO\Principal;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class ClassificacaoAlunoBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\ClassificacaoAlunoRepository
     */
    private $classificacaoAlunoRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->classificacaoAlunoRepository = $entityManager->getRepository(\App\Entity\Principal\ClassificacaoAluno::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
            ]
        );

    }

    /**
     * Verifica se existe a classificação do aluno no banco de dados, se existir, ele retorna no último parâmetro na função, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ClassificacaoAlunoRepository $classificacaoAlunoRepository Repositório da Classificação do Aluno
     * @param integer $id Chave primaria da Classificação do Aluno
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\ClassificacaoAluno|null $classificacaoAlunoORM Retorno no caso de sucesso
     * @param boolean $bRetornaObjeto True para retornar false para nao retornar
     *
     * @return boolean true|false
     */
    public static function verificaClassificacaoAlunoExiste(\App\Repository\Principal\ClassificacaoAlunoRepository $classificacaoAlunoRepository, $id, &$mensagemErro, &$classificacaoAlunoORM, $bRetornaObjeto=false)
    {
        if ($bRetornaObjeto === true) {
            $classificacaoAlunoORM = $classificacaoAlunoRepository->find($id);
        } else {
            $classificacaoAlunoORM = $classificacaoAlunoRepository->buscarRegistroPorId($id);
        }

        if ($classificacaoAlunoORM === null) {
            $mensagemErro = "Classificação não encontrada na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe a classificação do aluno no banco de dados, se existir, ele retorna no último parâmetro na função, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ClassificacaoAlunoRepository $classificacaoAlunoRepository Repositório da Classificação do Aluno
     * @param integer $franqueada ID da Franqueada
     * @param string $nome Nome da Classificação do Aluno
     * @param integer $id Chave primaria da ClassificacaoAluno
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     *
     * @return boolean true|false
     */
    public static function verificaNomeExiste(\App\Repository\Principal\ClassificacaoAlunoRepository $classificacaoAlunoRepository, $franqueada, $nome, $id, &$mensagemErro)
    {
        $classificacaoAlunoORM = $classificacaoAlunoRepository->buscarPorNome($franqueada, $nome, $id);
        if (is_null($classificacaoAlunoORM) === false) {
            $mensagemErro = "Classificação já cadastrada na base de dados.";
            return true;
        }

        return false;
    }


}
