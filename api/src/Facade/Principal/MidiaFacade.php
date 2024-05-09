<?php

namespace App\Facade\Principal;

use App\BO\Principal\MidiaBO;
use App\Entity\Principal\Franqueada;
use App\Entity\Principal\Midia;
use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Dayan Freitas
 */
class MidiaFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Principal\MidiaRepository
     */
    private $midiaRepository;

     /**
      *
      * @var \App\BO\Principal\MidiaBO
      */
    private $midiaBO;


    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->midiaRepository = self::getEntityManager()->getRepository(Midia::class);
        $this->midiaBO         = new MidiaBO(self::getEntityManager());
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

        $retornoRepositorio = $this->midiaRepository->filtrarPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|Object
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = null;
        $objetoORM = $this->midiaRepository->buscarPorId($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Midia não existe na base de dados";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, &$parametros=[])
    {
        $objetoORM = null;
        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;
        if ($this->midiaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Midia::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

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
        $objetoORM = null;
        if ($this->midiaBO->verificaMidiaExiste($this->midiaRepository, $id, $mensagemErro, $objetoORM) === true) {
            if ($this->midiaBO->podeAlterar($mensagemErro, $parametros) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
                // self::flushSeguro($mensagemErro);
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
        return empty($mensagemErro);
    }


}
