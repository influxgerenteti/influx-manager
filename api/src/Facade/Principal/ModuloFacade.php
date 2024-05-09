<?php
namespace App\Facade\Principal;

use App\BO\Principal\ModuloBO;
use App\Factory\GeneralORMFactory;
use App\Helper\ConstanteParametros;
use App\Helper\Logger;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Repository\Principal\UsuarioRepository;
use App\Helper\RedisHelper;
/**
 *
 * @author Marcelo Andre Naegeler
 */
class ModuloFacade extends GenericFacade
{

    
    /**
     *
     * @var \App\Repository\Principal\ModuloRepository
     */
    private $moduloRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\BO\Principal\ModuloBO
     */
    private $moduloBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->moduloRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\Modulo::class);
        $this->usuarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
        $this->moduloBO          = new ModuloBO(self::getEntityManager());
    }

    /**
     * Retorna os módulos cadastrados, sendo possível estruturar o modulo.
     *
     * @param array $parametros
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function listar ($parametros)
    {
        $retornoRepositorio = $this->moduloRepository->buscarTodosSemRelacaoComPai($parametros);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Lista os menus através do usuarioId
     *
     * @param int $usuarioId
     *
     * @return array|NULL
     */
    public function listarMenu ($usuarioId=null)
    {
        $modulosOrdenados = [];
        // $menuPorPapeis    = $this->usuarioRepository->buscarMenuPorPapel($usuarioId);
        $menuPorPapeis  = [];
        // Logger::logCodeLine();
        $cacheKey = "menu".":".$usuarioId;
        $cachedReturn = null; // removido o cache temporariamente
        $cachedReturn = RedisHelper::getRedisCache($cacheKey);        
        if($cachedReturn != null ){
            $data = \json_decode($cachedReturn,true);

            $menuPorUsuario = $data;
        }
        else
        {
            $menuPorUsuario = $this->usuarioRepository->buscarMenuPorUsuario($usuarioId);
            $dataPack = json_encode($menuPorUsuario,0);
            RedisHelper::setRedisCache($cacheKey,$dataPack,3600);
        }
        
        // Logger::logCodeLine();

        // die;


        if ($menuPorPapeis === null) {
            $menuPorPapeis = [];
        }

        if ($menuPorUsuario === null) {
            $menuPorUsuario = [];
        }

        $itens = array_merge($menuPorPapeis, $menuPorUsuario);
        foreach ($itens as $modulo) {
            $modulosOrdenados[$modulo[ConstanteParametros::CHAVE_ID]] = $modulo;
        }

        $itens = array_values($modulosOrdenados);

        return ModuloBO::organizarItens($itens);
    }

    /**
     * Lista os menus através do usuarioId
     *
     * @param int $usuarioId
     *
     * @return array|NULL
     */
    public function listarMenuRelatorios ($usuarioId=null)
    {
        $modulosOrdenados = [];
        // $menuPorPapeis    = $this->usuarioRepository->buscarMenuPorPapel($usuarioId);
        $menuPorPapeis  = [];
        $menuPorUsuario = $this->usuarioRepository->buscarMenuRelatoriosPorUsuario($usuarioId);

        
        if ($menuPorPapeis === null) {
            $menuPorPapeis = [];
        }
        
        if ($menuPorUsuario === null) {
            $menuPorUsuario = [];
        }
        
        
        $itens = array_merge($menuPorPapeis, $menuPorUsuario);
        foreach ($itens as $modulo) {
            $modulosOrdenados[$modulo[ConstanteParametros::CHAVE_ID]] = $modulo;
        }
        
        $itens = array_values($modulosOrdenados);


        return ModuloBO::organizarItens($itens);
    }

    /**
     * Cria novo módulo
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return NULL|\App\Entity\Principal\Modulo
     */
    public function criar (&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->moduloBO->podeSalvar($parametros, $mensagemErro) === true) {
            $acaoSistemasORM = [];
            if (is_null($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS]) === false) {
                $acaoSistemasORM = $parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS];
            }

            unset($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS]);
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Modulo::class, true, $parametros);
            if (count($acaoSistemasORM) > 0) {
                foreach ($acaoSistemasORM as $acaoSistemaORM) {
                    $objetoORM->addAcaoSistema($acaoSistemaORM);
                }
            }

            self::criarRegistro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Edita o modulo sob a ID $id
     *
     * @param string $mensagemErro
     * @param integer $id
     * @param array $parametros
     *
     * @return bool
     */
    public function editar (&$mensagemErro=null, $id=null, $parametros=[])
    {
        $objetoORM = $this->moduloRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Modulo não encontrado na base de dados.";
        } else {
            if ($this->moduloBO->podeSalvar($parametros, $mensagemErro) === true) {
                $acaosSistemaORM = [];
                if (is_null($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS]) === false) {
                    $acaosSistemaORM = $parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS];
                }

                unset($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS]);
                $acaoSistemas = $objetoORM->getAcaoSistemas();
                $contador     = $acaoSistemas->count();
                for ($i = 0;$i < $contador;$i++) {
                    $objetoORM->removeAcaoSistema($acaoSistemas->get($i));
                }

                if (count($acaosSistemaORM) > 0) {
                    foreach ($acaosSistemaORM as $acaoSistemaORM) {
                        $objetoORM->addAcaoSistema($acaoSistemaORM);
                    }
                }

                self::getFctHelper()->setParams($parametros, $objetoORM);
                self::flushSeguro($mensagemErro);
            }//end if
        }//end if

        return empty($mensagemErro);
    }

    /**
     * Altera a situação de um módulo para "Removido"
     *
     * @param string $mensagemErro
     * @param integer $id
     *
     * @return boolean
     */
    public function remover (&$mensagemErro, $id=null)
    {
        $item = $this->moduloRepository->find($id);
        $item->setSituacao(SituacoesSistema::SITUACAO_REMOVIDO);
        self::flushSeguro($mensagemErro);
        return empty($mensagemErro);
    }

    /**
     * Retorna os módulos cadastrados
     *
     * @param string $mensagem Mensagem de erro para o front-end.
     * @param integer $id ID do módulo a ser buscado.
     *
     * @return \App\Entity\Principal\Modulo
     */
    public function buscarModulo (&$mensagem="", $id=null)
    {
        $modulo = $this->moduloRepository->buscarModulo($id);
        if ($modulo === null) {
            $mensagem = "Item não encontrado.";
        }

        return $modulo;
    }

    /**
     * Retorna os módulos que podem ser pais
     *
     * @param string $mensagem Mensagem de erro para o front-end.
     *
     * @return array
     */
    public function buscarModulosPais (&$mensagem="")
    {
        $itens = $this->moduloRepository->buscarModulosPai();
        if ($itens === null) {
            $mensagem = "Nenhum item encontrado.";
        }

        return $itens;
    }

    /**
     * Busca todos os modulos do sistema
     *
     * @return array|NULL
     */
    public function buscarTodosOsModulosSemPai()
    {
        return $this->moduloRepository->buscarModulosSemPai();
    }


}
