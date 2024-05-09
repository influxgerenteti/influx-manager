<?php

namespace App\Facade\Principal;


use App\Entity\Principal\Categoria;
use App\BO\Principal\CategoriaBO;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class CategoriaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\CategoriaRepository
     */
    private $categoriaRepository;

    /**
     *
     * @var \App\BO\Principal\CategoriaBO
     */
    private $categoriaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->categoriaRepository = self::getEntityManager()->getRepository(Categoria::class);
        $this->categoriaBO         = new CategoriaBO();
    }

    /**
     * Atualiza o registro no banco de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser atualizado
     * @param array $parametros
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $categoriaORM = null;
        if ($this->categoriaBO->verificaCategoriaExiste($this->categoriaRepository, $id, $mensagemErro, $categoriaORM) === true) {
            $categoriaORM->setNome($parametros[ConstanteParametros::CHAVE_NOME]);
            $categoriaORM->setExcluido($parametros[ConstanteParametros::CHAVE_EXCLUSAO]);
            self::flushSeguro($mensagemErro);
            return empty($mensagemErro);
        }

        return false;
    }

    /**
     * Busca um registro atraves da ID
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser buscado no banco de dados
     *
     * @return NULL|\App\Entity\Principal\Categoria
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $categoriaORM = null;
        $this->categoriaBO->verificaCategoriaExiste($this->categoriaRepository, $id, $mensagemErro, $categoriaORM);
        return $categoriaORM;
    }

    /**
     * Cria um registro na base de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param array $parametros Parametros para realizar a criacao do registro
     *
     * @return boolean null|\App\Entity\Principal\Categoria
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $categoriaORM = \App\Factory\GeneralORMFactory::criar(Categoria::class, true, $parametros);
        self::criarRegistro($categoriaORM, $mensagemErro);
        return $categoriaORM;
    }

    /**
     * Lista todas as categorias do banco de dados
     *
     * @param array $parametros Array de parametros de paginacao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->categoriaRepository->filtrarCategoriasPorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Altera o campo "excluido" para true
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser 'removido'
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $categoriaORM = null;
        if ($this->categoriaBO->verificaCategoriaExiste($this->categoriaRepository, $id, $mensagemErro, $categoriaORM) === true) {
            $categoriaORM->setExcluido(true);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
