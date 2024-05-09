<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Luiz Antonio Costa
 */
class PermissaoBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "usuarioRepository"           => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "papelRepository"             => $entityManager->getRepository(\App\Entity\Principal\Papel::class),
                "moduloPapelAcaoRepository"   => $entityManager->getRepository(\App\Entity\Principal\ModuloPapelAcao::class),
                "moduloUsuarioAcaoRepository" => $entityManager->getRepository(\App\Entity\Principal\ModuloUsuarioAcao::class),
                "acaoSistemaRepository"       => $entityManager->getRepository(\App\Entity\Principal\AcaoSistema::class),
            ]
        );
    }

    /**
     * Busca o usuario
     *
     * @param int $id
     *
     * @return \App\Entity\Principal\Usuario|NULL
     */
    protected function buscaUsuario($id)
    {
        return self::getUsuarioRepository()->find($id);
    }

    /**
     * Busca o modulo
     *
     * @param int $id
     *
     * @return \App\Entity\Principal\Modulo|NULL
     */
    protected function buscaModulo($id)
    {
        return self::getModuloRepository()->find($id);
    }

    /**
     * Busca os papeis da rota atraves dos modulos que utilizam a rota
     *
     * @param string $rota
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return \App\Entity\Principal\Papel[]
     */
    protected function buscaPapeisPorModuloRota($rota, $moduloId, $acaoId)
    {
        $papeisExistentes = new \Doctrine\Common\Collections\ArrayCollection();

        // se forem rotas *magic* consulta direto por módulo, senão, por rota
        if (strpos($rota, '/api/magic') !== false) {
            $moduloPapelAcaoArray = self::getModuloPapelAcaoRepository()->buscaPorModuloAcao($moduloId, $acaoId);
        } else {
            $moduloPapelAcaoArray = self::getModuloPapelAcaoRepository()->buscaPorRotaModuloAcao($rota, $moduloId, $acaoId);
        }

        foreach ($moduloPapelAcaoArray as $moduloPapelAcaoORM) {
            $papel = $moduloPapelAcaoORM->getPapel();
            if ($papeisExistentes->contains($papel) === false) {
                $papeisExistentes->add($papel);
            }

            continue;
        }

        return $papeisExistentes;
    }


    public function acaoSistemaPeloMetodo($metodo)
    {

        $acaoSistema = "listar";

        switch ($metodo) {
            case 'GET':
            $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("listar")]);
                break;
            case 'POST':
            $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("criar")]);
                break;
            case 'PATCH':
            $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("editar")]);
                break;
            case 'PUT':
            $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("editar")]);
                break;
            case 'DELETE':
            $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("excluir")]);
                break;
        }
        return $acaoSistema;
    }
/**
     * Busca Usuarios que tem acesso a este modulo
     *
     * @param string $rota
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return boolean
     */
    public function possuiPermissao($metodo, $rota, $moduloId)
    {


        $acaoSistemaORM    = $this->retornaAcaoSistemaORM($metodo);
        $acaoId = $acaoSistemaORM->getId();
        // $franqueadaId = VariaveisCompartilhadas::$franqueadaID;
        $usuarioId = VariaveisCompartilhadas::$usuarioID;
        
        // $usuarioORM        = $this->buscaUsuario(VariaveisCompartilhadas::$usuarioID);
        
        // $usuariosLiberados = $this->buscaUsuariosPossuiAcesso($rota, $moduloId, $acaoSistemaORM->getId());
        // return $this->existeUsuarioEmUsuariosLiberados($usuarioORM, $usuariosLiberados);
 
        //$usuariosExistentes = new \Doctrine\Common\Collections\ArrayCollection();
        //
        // se forem rotas *magic* consulta direto por módulo, senão, por rota
       
        $moduloUsuarioAcaoArray = [];

        $moduloUsuarioAcaoArray = self::getModuloUsuarioAcaoRepository()->buscaPermissaoPorModuloAcao( $usuarioId,$moduloId, $acaoId);
    //     if (strpos($rota, '/api/magic') !== false) {    
    //         $moduloUsuarioAcaoArray = self::getModuloUsuarioAcaoRepository()->buscaPermissaoPorModuloAcao( $usuarioId,$moduloId, $acaoId);            
    //     } else {
    //  //       $moduloUsuarioAcaoArray = self::getModuloUsuarioAcaoRepository()->buscaPermissaoPorRotaModuloAcao($usuarioId, $rota, $moduloId, $acaoId);
    //     }

        // foreach ($moduloUsuarioAcaoArray as $moduloUsuarioAcaoORM) {
        //     $usuario = $moduloUsuarioAcaoORM->getUsuario();
        //     if ($usuariosExistentes->contains($usuario) === false) {
        //         $usuariosExistentes->add($usuario);
        //     }

        //     continue;
        // }

        if($moduloUsuarioAcaoArray != null && count($moduloUsuarioAcaoArray) > 0){
            return true;
        }
        return false;
    }


    /**
     * Busca Usuarios que tem acesso a este modulo
     *
     * @param string $rota
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return \App\Entity\Principal\Usuario[]
     */
    protected function buscaUsuariosPossuiAcesso($rota, $moduloId, $acaoId)
    {
        $usuariosExistentes = new \Doctrine\Common\Collections\ArrayCollection();
        //
        // se forem rotas *magic* consulta direto por módulo, senão, por rota
        if (strpos($rota, '/api/magic') !== false) {
            $moduloUsuarioAcaoArray = self::getModuloUsuarioAcaoRepository()->buscaPorModuloAcao($moduloId, $acaoId);
        } else {
            $moduloUsuarioAcaoArray = self::getModuloUsuarioAcaoRepository()->buscaPorRotaModuloAcao($rota, $moduloId, $acaoId);
        }

        foreach ($moduloUsuarioAcaoArray as $moduloUsuarioAcaoORM) {
            $usuario = $moduloUsuarioAcaoORM->getUsuario();
            if ($usuariosExistentes->contains($usuario) === false) {
                $usuariosExistentes->add($usuario);
            }

            continue;
        }

        return $usuariosExistentes;
    }

    /**
     * Verifica se existe os papeis no array informado
     *
     * @param \App\Entity\Principal\Papel[] $papeisUsuario
     * @param \App\Entity\Principal\Papel[] $papeisModuloRota
     *
     * @return boolean
     */
    protected function existePapelEmPapeisModuloRota($papeisUsuario, $papeisModuloRota)
    {
        foreach ($papeisModuloRota as $papelModuloRotaORM) {
            foreach ($papeisUsuario as $papelUsuarioORM) {
                if ($papelUsuarioORM->getId() === $papelModuloRotaORM->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se o usuario possui acesso no modulo
     *
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param \App\Entity\Principal\Usuario[] $usuariosLiberados
     *
     * @return boolean
     */
    protected function existeUsuarioEmUsuariosLiberados($usuarioORM, $usuariosLiberados)
    {
        foreach ($usuariosLiberados as $usuarioLiberadoORM) {
            if ($usuarioLiberadoORM->getId() === $usuarioORM->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se a acao existe no papel atribuido ao usuario
     *
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param int $moduloId
     * @param int $acaoSistemaId
     *
     * @return boolean
     */
    protected function existeAcaoNoPapelModulo($usuarioORM, $moduloId, $acaoSistemaId)
    {
        $bRetorno      = false;
        $usuarioPapeis = $usuarioORM->getPapels();
        foreach ($usuarioPapeis as $papelORM) {
            $bEncontrou = false;
            $modulosAtribuidosAoPapel = $papelORM->getModuloPapelAcao();
            foreach ($modulosAtribuidosAoPapel as $moduloPapelAcaoORM) {
                if ($moduloPapelAcaoORM->getModulo()->getId() === ((int) $moduloId)) {
                    $bEncontrou = ($moduloPapelAcaoORM->getAcaoSistema()->getId() === ((int) $acaoSistemaId));
                }

                if ($bEncontrou === true) {
                    break;
                }
            }

            if ($bEncontrou === true) {
                $bRetorno = true;
                break;
            }
        }//end foreach

        return $bRetorno;
    }

    /**
     * Verifica se a acao existe como excessao no ModuloUsuarioAcao
     *
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param int $moduloId
     * @param int $acaoSistemaId
     *
     * @return boolean
     */
    protected function existeAcaoNoUsuarioModulo($usuarioORM, $moduloId, $acaoSistemaId)
    {
        $bRetorno = false;
        $modulosAcoesAtribuidosAoUsuario = $usuarioORM->getModuloUsuarioAcaos();
        foreach ($modulosAcoesAtribuidosAoUsuario as $moduloUsuarioAcaoORM) {
            $bEncontrou = false;
            if ($moduloUsuarioAcaoORM->getModulo()->getId() === ((int) $moduloId)) {
                $bEncontrou = ($moduloUsuarioAcaoORM->getAcaoSistema()->getId() === ((int) $acaoSistemaId));
            }

            if ($bEncontrou === true) {
                $bRetorno = true;
                break;
            }
        }

        return $bRetorno;
    }

    /**
     * Retorna a acao do metodo
     *
     * @param string $metodo Metodo da requisicao
     *
     * @return NULL|\App\Entity\Principal\AcaoSistema
     */
    protected function retornaAcaoSistemaORM($metodo, $acaoSistemaId=null)
    {
        $acaoSistema = null;
        if (is_null($acaoSistemaId) === false) {
            $acaoSistema = self::getAcaoSistemaRepository()->find($acaoSistemaId);
        } else {
            switch ($metodo) {
                case 'GET':
                $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("listar")]);
                    break;
                case 'POST':
                $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("criar")]);
                    break;
                case 'PATCH':
                $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("editar")]);
                    break;
                case 'PUT':
                $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("editar")]);
                    break;
                case 'DELETE':
                $acaoSistema = self::getAcaoSistemaRepository()->findOneBy(["descricao" => strtoupper("excluir")]);
                    break;
            }
        }//end if

        return $acaoSistema;
    }

    /**
     * Verifica se algum dos papeis que o usuario possui, possui acesso ao modulo informado
     *
     * @param string $metodo
     * @param int $usuarioId
     * @param string $rota
     * @param int $moduloId
     *
     * @return boolean
     */
    public function isPermitidoPorPapel($metodo, $usuarioId, $rota, $moduloId)
    {
        $usuarioORM     = $this->buscaUsuario($usuarioId);
        $acaoSistemaORM = $this->retornaAcaoSistemaORM($metodo);
        $papeisUsuario  = $usuarioORM->getPapels();
        $papeisModulo   = $this->buscaPapeisPorModuloRota($rota, $moduloId, $acaoSistemaORM->getId());

        return $this->existePapelEmPapeisModuloRota($papeisUsuario, $papeisModulo);
    }

    

    /**
     * Verifica se a acao é permitida pro usuario
     *
     * @param int $acaoSistemaId
     * @param int $moduloId
     * @param \App\Entity\Principal\Modulo|NULL $moduloORM
     * @param int $usuarioId
     * @param \App\Entity\Principal\Usuario|NULL $usuarioORM
     *
     * @return boolean
     */
    public function isPermitidoAcaoNoModuloProUsuario($acaoSistemaId, $moduloId=null, $moduloORM=null, $usuarioId=null, $usuarioORM=null)
    {
        if ((is_null($usuarioORM) === true) && (is_null($usuarioId) === false)) {
            $usuarioORM = $this->buscaUsuario($usuarioId);
        }

        if ((is_null($moduloORM) === true) && (is_null($moduloId) === false)) {
            $moduloORM = $this->buscaModulo($moduloId);
        } else {
            $moduloId = $moduloORM->getId();
        }

        $acaoExisteNoPapelModuloAcao   = $this->existeAcaoNoPapelModulo($usuarioORM, $moduloId, $acaoSistemaId);
        $acaoExisteNoUsuarioModuloAcao = $this->existeAcaoNoUsuarioModulo($usuarioORM, $moduloId, $acaoSistemaId);
        return ($acaoExisteNoPapelModuloAcao === true)||($acaoExisteNoUsuarioModuloAcao === true);
    }

    /**
     * Verifica se o usuario possui a franqueada atribuida em sua tabela(usuario_franqueada)
     *
     * @param int $usuarioId
     * @param int $franqueadaId
     *
     * @return boolean
     */
    public function isUsuarioPossuiFranqueadaAtribuida($usuarioId, $franqueadaId)
    {
        $usuarioORM = $this->buscaUsuario($usuarioId);
        $franqueadasEncontradas = $usuarioORM->getFranqueadas()->filter(
            function (\App\Entity\Principal\Franqueada $franqueada) use ($franqueadaId) {
                return $franqueada->getId() === (int) $franqueadaId;
            }
        );

        return $franqueadasEncontradas->count() > 0;
    }

    /**
     * Retorna os papeis do usuario
     *
     * @param int $usuarioId
     *
     * @return int[]
     */
    public function retornaPapeisIdUsuario($usuarioId)
    {
        $usuarioORM    = $this->buscaUsuario($usuarioId);
        $papeisUsuario = $usuarioORM->getPapels();
        $retorno       = [];
        foreach ($papeisUsuario as $papelORM) {
            $retorno[] = $papelORM->getId();
        }

        return $retorno;
    }


}
