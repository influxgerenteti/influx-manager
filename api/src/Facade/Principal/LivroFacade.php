<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\LivroBO;

/**
 *
 * @author Luiz A Costa
 */
class LivroFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\LivroRepository
     */
    private $livroRepository;

    /**
     *
     * @var \App\Repository\Principal\CursoRepository
     */
    private $cursoRepository;

    /**
     *
     * @var \App\BO\Principal\LivroBO
     */
    private $livroBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->livroRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Livro::class);
        $this->cursoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Curso::class);
        $this->livroBO         = new LivroBO(self::getEntityManager());
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
        $retornoRepositorio = $this->livroRepository->filtrarLivroPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     *
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->livroRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Livro não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Livro
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        $bErro     = false;
        if ($this->livroBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Livro::class, true, $parametros);
            foreach ($parametros[ConstanteParametros::CHAVE_CURSO] as $cursoId) {
                $cursoORM = $this->cursoRepository->find($cursoId);
                if (is_null($cursoORM) === true) {
                    $bErro        = true;
                    $mensagemErro = "Não foi possivel encontrar o curso com a ID:" . $cursoId;
                    break;
                } else {
                    $objetoORM->addCurso($cursoORM);
                }
            }

            // Se o item do livro não possui plano de contas, seta ele para o 42 (plano de contas dos livros)
            $itemORM = $parametros[ConstanteParametros::CHAVE_ITEM];

            if ($itemORM->getPlanoConta() === null) {
                $planoContaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\PlanoConta::class);
                $planoContaLivros     = $planoContaRepository->find(42);
                $itemORM->setPlanoConta($planoContaLivros);
            }

            if ($bErro === false) {
                self::criarRegistro($objetoORM, $mensagemErro);
            }
        }//end if

        return $objetoORM;
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
        $objetoORM = $this->livroRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Livro não encontrado na base de dados.";
        } else {
            if ($this->livroBO->podeAlterar($parametros, $mensagemErro, $objetoORM) === true) {
                $this->livroBO->configuraParametros($parametros, $objetoORM);

                // Se o item do livro não possui plano de contas, seta ele para o 42 (plano de contas dos livros)
                $itemORM = $objetoORM->getItem();

                if ($itemORM->getPlanoConta() === null) {
                    $planoContaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\PlanoConta::class);
                    $planoContaLivros     = $planoContaRepository->find(42);
                    $itemORM->setPlanoConta($planoContaLivros);
                }

                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $objetoORM = $this->livroRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Livro não encontrado na base de dados.";
        } else {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
