<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Principal\ModuloPapelAcao;
use App\Helper\ConstanteParametros;
use App\Entity\Principal\ModuloUsuarioAcao;
use App\BO\Principal\PermissaoBO;

/**
 *
 * @author Luiz A Costa
 */
class PermissaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\PapelRepository
     */
    private $papelRepository;

    /**
     *
     * @var \App\Repository\Principal\ModuloRepository
     */
    private $moduloRepository;

    /**
     *
     * @var \App\Repository\Principal\AcaoSistemaRepository
     */
    private $acaoSistemaRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\BO\Principal\PermissaoBO
     */
    private $permissaoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct(ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->permissaoBO           = new PermissaoBO(self::getEntityManager());
        $this->papelRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Papel::class);
        $this->moduloRepository      = self::getEntityManager()->getRepository(\App\Entity\Principal\Modulo::class);
        $this->acaoSistemaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AcaoSistema::class);
        $this->usuarioRepository     = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
    }

    /**
     * Cria ModuloPapelAcao
     *
     * @param array $dadosPermissao
     * @param \App\Entity\Principal\Papel $papelORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function criaModuloPapelAcao($dadosPermissao=null, &$papelORM=null, &$mensagemErro="")
    {
        $bRetorno = true;

        if (is_null($dadosPermissao) === false) {
            $dadosPermissao = $this->buscaModulosPais($dadosPermissao);
            $totalDados     = count($dadosPermissao);

            for ($i = 0; $i < $totalDados; $i++) {
                $moduloORM = $this->moduloRepository->find($dadosPermissao[$i][ConstanteParametros::CHAVE_MODULO]);
                if (is_null($moduloORM) === true) {
                    $mensagemErro .= "Módulo com a ID " . $dadosPermissao[$i][ConstanteParametros::CHAVE_MODULO] . " não foi encontrado na base de dados.\n";
                    $bRetorno      = false;
                    break;
                }

                $acaoSistemaORM = $this->acaoSistemaRepository->find($dadosPermissao[$i][ConstanteParametros::CHAVE_ACAO_SISTEMA]);
                if (is_null($acaoSistemaORM) === true) {
                    $mensagemErro .= "Ação com a ID " . $dadosPermissao[$i][ConstanteParametros::CHAVE_ACAO_SISTEMA] . " não foi encontrada na base de dados.\n";
                    $bRetorno      = false;
                    break;
                }

                $moduloPapelAcaoORM = new ModuloPapelAcao();
                $papelORM->addModuloPapelAcao($moduloPapelAcaoORM);
                $moduloORM->addModuloPapelAcao($moduloPapelAcaoORM);
                $acaoSistemaORM->addModuloPapelAcao($moduloPapelAcaoORM);
                self::persistSeguro($moduloPapelAcaoORM, $mensagemErro);
            }//end for
        }//end if

        return $bRetorno;
    }

    /**
     * Remove todas as associacoes com cada entidade
     *
     * @param \App\Entity\Principal\Papel $papelORM
     * @param int $modulo
     */
    protected function removeModuloPapelAcao(&$papelORM, int $modulo=null)
    {
        $permissoes = $papelORM->getModuloPapelAcao();
        $contador   = $permissoes->count();

        // Para o caso do usuário ter filtrado o módulo e não perder as permissões de outros módulos.
        $deletarTodasPermissoes = is_null($modulo);

        for ($i = 0; $i < $contador; $i++) {
            $permissaoORM = $permissoes->get($i);
            if ($deletarTodasPermissoes === true || $permissaoORM->getModulo()->getId() === $modulo) {
                self::removerRegistro($permissaoORM);
            }
        }
    }

    /**
     * Cria ModuloUsuarioAcao
     *
     * @param array $dadosPermissao
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function criaModuloUsuarioAcao($dadosPermissao=null, &$usuarioORM=null, &$mensagemErro="")
    {
        $bRetorno = true;

        if (is_null($dadosPermissao) === false) {
            $dadosPermissao       = $this->buscaModulosPais($dadosPermissao);
            $totalDados           = count($dadosPermissao);
            $modulosJaCadastrados = [];
            $acoesParaModulosJaCadastrados = [];
            for ($i = 0; $i < $totalDados; $i++) {
                $moduloORM = $this->moduloRepository->find($dadosPermissao[$i][ConstanteParametros::CHAVE_MODULO]);
                if (is_null($moduloORM) === true) {
                    $mensagemErro .= "Módulo com a ID " . $dadosPermissao[$i][ConstanteParametros::CHAVE_MODULO] . " não foi encontrado na base de dados.\n";
                    $bRetorno      = false;
                    break;
                }

                $acaoSistemaORM = $this->acaoSistemaRepository->find($dadosPermissao[$i][ConstanteParametros::CHAVE_ACAO_SISTEMA]);
                if (is_null($acaoSistemaORM) === true) {
                    $mensagemErro .= "Ação com a ID " . $dadosPermissao[$i][ConstanteParametros::CHAVE_ACAO_SISTEMA] . " não foi encontrada na base de dados.\n";
                    $bRetorno      = false;
                    break;
                }

                if ((isset($modulosJaCadastrados[$moduloORM->getId()]) === false) || ((isset($modulosJaCadastrados[$moduloORM->getId()]) === true) && (isset($acoesParaModulosJaCadastrados[$moduloORM->getId()][$acaoSistemaORM->getId()]) === false))) {
                    $moduloUsuarioAcaoORM = new ModuloUsuarioAcao();
                    $usuarioORM->addModuloUsuarioAcao($moduloUsuarioAcaoORM);
                    $moduloORM->addModuloUsuarioAcao($moduloUsuarioAcaoORM);
                    $acaoSistemaORM->addModuloUsuarioAcao($moduloUsuarioAcaoORM);
                    self::persistSeguro($moduloUsuarioAcaoORM, $mensagemErro);
                    $modulosJaCadastrados[$moduloORM->getId()] = $moduloORM->getId();
                    $acoesParaModulosJaCadastrados[$moduloORM->getId()][$acaoSistemaORM->getId()] = $acaoSistemaORM->getId();
                }
            }//end for
        }//end if

        return $bRetorno;
    }

    /**
     * Busca módulos pais para atribuir as permissões, caso não estejam no array de permissões
     *
     * @param array $permissoes
     *
     * @return array $permissoes
     */
    protected function buscaModulosPais ($permissoes)
    {
        $totalDados = count($permissoes);
        $modulosIDs = array_unique(
            array_map(
                function ($permissao) {
                    return $permissao[ConstanteParametros::CHAVE_MODULO];
                },
                $permissoes
            )
        );

        $moduloDashboardORM = $this->moduloRepository->findOneBy([ConstanteParametros::CHAVE_URL => '/dashboard']);
        $acaoSistemaORM     = $this->acaoSistemaRepository->findOneBy([ConstanteParametros::CHAVE_DESCRICAO => 'LISTAR']);
        $dashID = $moduloDashboardORM->getId();
        $acaoID = $acaoSistemaORM->getId();

        $precisaForcarDashboard = true;
        for ($i = 0; $i < $totalDados; $i++) {
            if (((int) $permissoes[$i][ConstanteParametros::CHAVE_MODULO] === $dashID) && ((int) $permissoes[$i][ConstanteParametros::CHAVE_ACAO_SISTEMA] === $acaoID)) {
                $precisaForcarDashboard = false;
                break;
            }
        }

        if ($precisaForcarDashboard === true) {
            $modulosIDs[] = $dashID;
            $permissoes[] = [
                ConstanteParametros::CHAVE_USUARIO      => null,
                ConstanteParametros::CHAVE_MODULO       => $dashID,
                ConstanteParametros::CHAVE_ACAO_SISTEMA => $acaoID,
            ];
        }

        for ($i = 0; $i < $totalDados; $i++) {
            $moduloORM = $this->moduloRepository->find($permissoes[$i][ConstanteParametros::CHAVE_MODULO]);
            if (is_null($moduloORM) === false) {
                $moduloPaiORM = $moduloORM->getModuloPai();
                if (is_null($moduloPaiORM) === false) {
                    if (array_search($moduloPaiORM->getId(), $modulosIDs) === false) {
                        $modulosIDs[] = $moduloPaiORM->getId();
                        $permissoes[] = [
                            ConstanteParametros::CHAVE_MODULO       => $moduloPaiORM->getId(),
                            ConstanteParametros::CHAVE_ACAO_SISTEMA => $permissoes[$i][ConstanteParametros::CHAVE_ACAO_SISTEMA],
                            ConstanteParametros::CHAVE_USUARIO      => null,
                        ];
                    }

                    $moduloAvoORM = $moduloPaiORM->getModuloPai();
                    if (is_null($moduloAvoORM) === false) {
                        if (array_search($moduloAvoORM->getId(), $modulosIDs) === false) {
                            $modulosIDs[] = $moduloAvoORM->getId();
                            $permissoes[] = [
                                ConstanteParametros::CHAVE_MODULO       => $moduloAvoORM->getId(),
                                ConstanteParametros::CHAVE_ACAO_SISTEMA => $permissoes[$i][ConstanteParametros::CHAVE_ACAO_SISTEMA],
                                ConstanteParametros::CHAVE_USUARIO      => null,
                            ];
                        }
                    }
                }//end if
            }//end if
        }//end for

        return $permissoes;
    }

    /**
     * Remove todas as associações com cada entidade
     *
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param int $modulo
     */
    protected function removeModuloUsuarioAcao(&$usuarioORM, int $modulo=null)
    {
        $permissoes = $usuarioORM->getModuloUsuarioAcaos();
        $contador   = $permissoes->count();

        // Para o caso do usuário ter filtrado o módulo e não perder as permissões de outros módulos.
        $deletarTodasPermissoes = is_null($modulo);
        $listaDeIdsParaDeltar   = [];
        for ($i = 0; $i < $contador; $i++) {
            $permissaoORM = $permissoes->get($i);
            if ($deletarTodasPermissoes === true || $permissaoORM->getModulo()->getId() === $modulo) {
                array_push($listaDeIdsParaDeltar, $permissaoORM->getModulo()->getId());
            }
        }

        if (count($listaDeIdsParaDeltar) > 0) {
            $queryForDelete = "DELETE FROM App\Entity\Principal\ModuloUsuarioAcao mua WHERE mua.modulo IN (" . implode(",", $listaDeIdsParaDeltar) . ") AND mua.usuario = '" . $usuarioORM->getId() . "'";
            $query          = self::getEntityManager()->createQuery($queryForDelete);
            $query->execute();
        }
    }

    /**
     * Atualiza as permissões do papel
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizarPapelModulos(&$mensagemErro, $parametros=[])
    {
        $papel    = $parametros[ConstanteParametros::CHAVE_PAPEL];
        $dados    = $parametros[ConstanteParametros::CHAVE_DADOS];
        $modulo   = $parametros[ConstanteParametros::CHAVE_MODULO];
        $papelORM = $this->papelRepository->find($papel);

        if (is_null($papelORM) === false) {
            $this->removeModuloPapelAcao($papelORM, $modulo);
            $this->criaModuloPapelAcao($dados, $papelORM, $mensagemErro);
        } else {
            $mensagemErro = "Papel não encontrado na base.";
        }

        if (empty($mensagemErro) === true) {
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Atualiza as permissões do usuário
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizarUsuarioModulos(&$mensagemErro, $parametros=[])
    {
        $usuario    = $parametros[ConstanteParametros::CHAVE_USUARIO];
        $dados      = $parametros[ConstanteParametros::CHAVE_DADOS];
        $modulo     = $parametros[ConstanteParametros::CHAVE_MODULO];
        $usuarioORM = $this->usuarioRepository->find($usuario);

        if (is_null($usuarioORM) === false) {
            $this->removeModuloUsuarioAcao($usuarioORM, $modulo);
            $this->criaModuloUsuarioAcao($dados, $usuarioORM, $mensagemErro);
        } else {
            $mensagemErro = "Usuário não encontrado na base.";
        }

        if (empty($mensagemErro) === true) {
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Realiza a busca de papeis
     *
     * @param array $parametros
     *
     * @return array
     */
    public function listar($parametros)
    {
        if ((is_null($parametros[ConstanteParametros::CHAVE_PAPEL]) === false) && (is_null($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)) {
            return $this->moduloRepository->buscarModuloPorPapel($parametros);
        } else {
            return $this->moduloRepository->buscarModuloPorUsuario($parametros);
        }
    }

    /**
     * Busca o registro pelo papel
     *
     * @param string $mensagemErro
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorPapel(&$mensagemErro, $id)
    {
        $retorno = $this->papelRepository->buscarPorPapel($id);
        if (is_null($retorno) === true) {
            $mensagemErro = "Papel não encontrado.";
        }

        return $retorno;
    }

    /**
     * Busca o registro pelo usuario
     *
     * @param string $mensagemErro
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorUsuario(&$mensagemErro, $id)
    {
        $retorno = $this->usuarioRepository->buscarPermissaoPorUsuario($id);
        if (is_null($retorno) === true) {
            $mensagemErro = "Usuario não encontrado.";
        }

        return $retorno;
    }

    /**
     * Busca o módulo com suas ações possíveis (acao_sistema) e as permissões do papel e do usuário para o módulo e ação (modulo_papel_acao, modulo_usuario_acao)
     *
     * @param string $mensagemErro
     * @param string $URLModulo
     * @param int    $usuarioID
     *
     * @return array|NULL
     */
    public function buscarPorModulo(&$mensagemErro, $URLModulo, $usuarioID)
    {
        $usuarioORM = $this->usuarioRepository->find($usuarioID);
        $papeis     = $usuarioORM->getPapels()->map(
            function ($papel) {
                return $papel->getId();
            }
        );

        $moduloORM = $this->moduloRepository->findOneBy([ConstanteParametros::CHAVE_URL => $URLModulo]);
        if (is_null($moduloORM) === true) {
            $mensagemErro = "Módulo não encontrado.";
            return false;
        }

        $parametros = [
            ConstanteParametros::CHAVE_MODULO  => $moduloORM->getId(),
            ConstanteParametros::CHAVE_USUARIO => $usuarioORM->getId(),
            ConstanteParametros::CHAVE_PAPEL   => $papeis,
        ];

        $modulo = $this->moduloRepository->buscarPermissaoPorModulo($parametros);

        $numeroAcoes = count($modulo['acaoSistemas']);
        for ($i = 0; $i < $numeroAcoes; $i++) {
            $acao = $modulo['acaoSistemas'][$i];

            // $papelPossuiPermissao = array_search($acao['id'], array_column($modulo['modulo_papel_acao'], 'acao_sistema_id')) !== false;
            // if ($papelPossuiPermissao === true) {
            // $modulo['acaoSistemas'][$i]['possui_permissao'] = true;
            // continue;
            // }
            $usuarioPossuiPermissao = array_search($acao['id'], array_column($modulo['moduloUsuarioAcaos'], 'acao_sistema_id')) !== false;
            if ($usuarioPossuiPermissao === true) {
                $modulo['acaoSistemas'][$i]['possui_permissao'] = true;
                continue;
            }

            $modulo['acaoSistemas'][$i]['possui_permissao'] = false;
        }

        $modulo['permissoes'] = $modulo['acaoSistemas'];

        unset($modulo['acaoSistemas']);
        // unset($modulo['modulo_papel_acao']);
        unset($modulo['moduloUsuarioAcaos']);

        return $modulo;
    }

    /**
     * Verifica se o usuario possui a ação no papel atribuido, ou se possui como uma permissão de exceção
     *
     * @param string $mensagemErro
     * @param int $acaoSistemaId
     * @param int $moduloId
     * @param string $moduloURL
     * @param int $usuarioId
     * @param \App\Entity\Principal\Usuario $usuarioORM
     *
     * @return boolean
     */
    public function verificaUsuarioPossuiAcaoSistema(&$mensagemErro, $acaoSistemaId, $moduloId=null, $moduloURL=null, $usuarioId=null, $usuarioORM=null)
    {
        $moduloORM = null;

        if ((is_null($usuarioId) === true)&&(is_null($usuarioORM) === true)) {
            $mensagemErro = "Será necessario passar o usuarioID ou o Objeto para prosseguir com a validação de permissao em PermissaoBO";
        }

        if ((is_null($moduloId) === true)&&(is_null($moduloURL) === true)) {
            $mensagemErro .= "Será necessario passar o moduloID ou a URL do Modulo para prosseguir com a validação de permissao em PermissaoBO";
        }

        if (is_null($moduloURL) === false) {
            $moduloORM = $this->moduloRepository->findOneBy(["url" => $moduloURL]);
        }

        if (empty($mensagemErro) === false) {
            return false;
        }

        if ($this->permissaoBO->isPermitidoAcaoNoModuloProUsuario($acaoSistemaId, $moduloId, $moduloORM, $usuarioId, $usuarioORM) === true) {
            return true;
        }

        $mensagemErro = "Usuario não possui acesso a esta ação neste modulo.";
        return false;
    }


}
