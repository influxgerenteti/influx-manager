<?php
namespace App\Facade\Principal;


use App\Entity\Principal\Favorito;
use App\Factory\GeneralORMFactory;
use App\Entity\Principal\Usuario;
use App\Entity\Principal\Modulo;
use App\Entity\Principal\Franqueada;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Marcelo Andre Naegeler
 */
class FavoritoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FavoritoRepository
     */
    private $favoritoRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\Repository\Principal\ModuloRepository
     */
    private $moduloRepository;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->favoritoRepository   = self::getEntityManager()->getRepository(Favorito::class);
        $this->usuarioRepository    = self::getEntityManager()->getRepository(Usuario::class);
        $this->moduloRepository     = self::getEntityManager()->getRepository(Modulo::class);
        $this->franqueadaRepository = self::getEntityManager()->getRepository(Franqueada::class);
    }

    /**
     * Retorna os favoritos cadastrados para o usuário e franqueada fornecidos
     *
     * @param string $mensagemErro Mensagem de erro para o front-end.
     * @param integer $usuario_id Usuário a quem pertencem os favoritos buscados.
     * @param integer $franqueada_id Franqueada a qual pertencem os favoritos buscados.
     *
     * @return array
     */
    public function listar (&$mensagemErro="", $usuario_id=null, $franqueada_id=null)
    {
        $itens = $this->favoritoRepository->findBy(['usuario_id' => $usuario_id, 'franqueada_id' => $franqueada_id]);
        if (empty($itens) === true) {
            $mensagemErro = "Nenhum item encontrado.";
        }

        return $itens;
    }

    /**
     * Cria um novo favorito
     *
     * @param string $mensagemErro
     * @param array $parametros IDs do usuário, modulo e franquia
     *
     * @return \App\Entity\Principal\Favorito
     */
    public function criar (&$mensagemErro="", $parametros=[])
    {
        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = $this->franqueadaRepository->find($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        $parametros[ConstanteParametros::CHAVE_USUARIO]    = $this->usuarioRepository->find($parametros[ConstanteParametros::CHAVE_USUARIO]);
        $parametros[ConstanteParametros::CHAVE_MODULO]     = $this->moduloRepository->find($parametros[ConstanteParametros::CHAVE_MODULO]);

        $itemCriado = GeneralORMFactory::criar(Favorito::class, true, $parametros);
        self::criarRegistro($itemCriado, $mensagemErro);
        return $itemCriado;
    }

    /**
     * Remove um item do favorito(ou seja, exclui permanentemente do banco)
     *
     * @param string $mensagemErro
     * @param integer $id
     *
     * @return boolean
     */
    public function remover (&$mensagemErro="", $id=null)
    {
        $item = $this->favoritoRepository->find($id);
        if ($item === null) {
            $mensagemErro = "Item não encontrado.";
            return false;
        }

        self::removerRegistro($item, $mensagemErro);
        return empty($mensagemErro);
    }


}
