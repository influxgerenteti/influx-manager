<?php

namespace App\Facade\Log;

use App\Entity\Principal\Usuario;
use App\Entity\Principal\Franqueada;
use App\Entity\Log\Log;
use App\Factory\GeneralORMFactory;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz Antonio Costa
 */
class LogFacade extends GenericFacade
{
    public static $LOG_LOGIN  = "L";
    public static $LOG_ACCESS = "A";
    public static $LOG_CREATE = "C";
    public static $LOG_READ   = "R";
    public static $LOG_UPDATE = "U";
    public static $LOG_DELETE = "D";
    public static $LOG_PERMISSAO = "P";

    /**
     *
     * @var \App\Repository\Log\LogRepository $logRepository
     */
    private $logRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, "base_log");
        $this->logRepository = self::getEntityManager()->getRepository(Log::class);
    }

    /**
     * Retornara a lista de logs do banco
     *
     * @param array $params parametros para filtros
     *
     * @return array
     */
    public function listaLogs($params=[])
    {
        $retornoRepositorio = $this->logRepository->filtraLogsPorPagina($params, $params[ConstanteParametros::CHAVE_FRANQUEADA], $params[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Cria o log com os parametros informados
     *
     * @param string $errorMsg se existir algum erro enviara para o controller
     * @param array $params Parametros do post.<br>Estrutura: array("info_evento"=>"valor","id_franqueada"=>"valor","ip"=>"valor")
     *
     * @return \App\Entity\Log\Log O Objeto ou Nulo
     */
    public function criarLog(&$errorMsg, $params=[])
    {
        $basePrincipal = self::getManagerRegistry()->getManager("base_principal");
        $usuarioORM    = null;
        
        if (is_null($params[ConstanteParametros::CHAVE_USUARIO]) === false) {

            $usuarioORM = $basePrincipal->getRepository(Usuario::class)->find($params[ConstanteParametros::CHAVE_USUARIO]); 
            $params[ConstanteParametros::CHAVE_USUARIO_NOME] = $usuarioORM->getNome();
        
        }

        $franqueadaORM = $basePrincipal->getRepository(Franqueada::class)->find($params[ConstanteParametros::CHAVE_FRANQUEADA]);
        if (is_null($usuarioORM) === true) {
            $params[ConstanteParametros::CHAVE_USUARIO] = null;
        }

        if (is_null($franqueadaORM) === true) {
            $params[ConstanteParametros::CHAVE_FRANQUEADA] = null;
        }

        $objeto = GeneralORMFactory::criar(\App\Entity\Log\Log::class, true, $params);
        self::criarRegistro($objeto, $errorMsg);

        return $objeto;
    }

    /**
     * Busca um Log pela ID
     *
     * @param string $mensagem Mensagem de erro
     * @param integer $id ID do Log a ser buscado
     *
     * @return \App\Entity\Log\Log
     */
    public function buscarLog(&$mensagem="", $id=null)
    {
        $log = $this->logRepository->findOneById($id);
        if ($log === null) {
            $mensagem = "Item não encontrado.";
        }

        return $log;
    }


}
