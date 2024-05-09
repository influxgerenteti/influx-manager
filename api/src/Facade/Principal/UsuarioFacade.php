<?php

namespace App\Facade\Principal;

use App\BO\Principal\UsuarioBO;
use App\Factory\GeneralORMFactory;
use App\Entity\Principal\Usuario;
use App\Helper\ConstanteParametros;
use App\Entity\Principal\Franqueada;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Principal\Modulo;
use App\Helper\EmailAwsSender;
use Dompdf\Exception;
use App\Entity\Principal\ModuloUsuarioAcao;
use App\Entity\Principal\Papel;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\IsNull;

/**
 *
 * @author Luiz Antonio Costa
 */
class UsuarioFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository $usuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\BO\Principal\UsuarioBO
     */
    private $usuarioBO;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository $franqueadaRepository
     */
    private $franqueadaRepository;


    /**
     *
     * @var \App\Repository\Principal\PapelRepository $papelRepository
     */
    private $papelRepository;

    /**
     *
     * @var \App\Repository\Principal\ModuloUsuarioAcaoRepository $usuarioAcaoRepository
     */
    private $usuarioAcaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ModuloRepository $moduloRepository
     */
    private $moduloRepository;

    /**
     *
     * @var EmailAwsSender $emailAwsSender
     */
    private $emailAwsSender;
    
    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct(ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->usuarioRepository    = self::getEntityManager()->getRepository(Usuario::class);
        $this->franqueadaRepository = self::getEntityManager()->getRepository(Franqueada::class);
        $this->moduloRepository     = self::getEntityManager()->getRepository(Modulo::class);
        $this->usuarioBO            = new UsuarioBO(self::getEntityManager());
        $this->emailAwsSender       = new EmailAwsSender();
        $this->papelRepository = self::getEntityManager()->getRepository(Papel::class);
        $this->usuarioAcaoRepository = self::getEntityManager()->getRepository(ModuloUsuarioAcao::class);
    }

   

    /**
     * Lista os usuarios com base nos filtros informados
     *
     * @param array $params Arrray()
     *
     * @return array Resultado da consulta com os registros filtrados
     */
    public function listaUsuarios($params=[])
    {
        $retornoRepositorio = $this->usuarioRepository->filtraUsuariosPorPagina($params, $params[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;

    }

    /**
     * Busca o usuario através da id
     *
     * @param int $id
     * @param boolean $retornaObjeto
     *
     * @return \App\Entity\Principal\Usuario
     */
    public function buscarUsuario(int $id, $retornaObjeto=false)
    {
        return $this->usuarioRepository->buscarUsuarioEFranqueadas($id, $retornaObjeto);
    }

    /**
     * Busca o usuário através do e-mail
     *
     * @param string $mensagem
     * @param string $email
     *
     * @return \App\Entity\Principal\Usuario
     */
    public function buscarUsuarioPorEmail(&$mensagem=null, $email=null)
    {
        $usuario = $this->usuarioRepository->findOneByEmail($email);
        if ($usuario === null) {
            $mensagem = "Usuário não encontrado.";
            return null;
        }

        return $usuario;
    }

    /**
     * Busca o usuário através do cpf
     *
     * @param string $mensagem
     * @param string $cpf
     *
     * @return \App\Entity\Principal\Usuario
     */
    public function buscarUsuarioPorCpf(&$mensagem=null, $cpf=null)
    {
        $cpf     = preg_replace('/[.-]/', '', $cpf);
        $usuario = $this->usuarioRepository->findOneByCpf($cpf);
        if ($usuario === null) {
            $mensagem = "Usuário não encontrado.";
            return null;
        }

        return $usuario;
    }

    public function callBackPersist($objetoORM, &$mensagemErro)
    {
        self::persistSeguro($objetoORM, $mensagemErro);
    }

    public function callBackFlush(&$mensagemErro)
    {
        self::flushSeguro($mensagemErro);
    }


    /**
     * Cria o usuario com base nos dados enviados
     *
     * @param string $msg ponteiro de retorno para resposta
     * @param array $params = array()
     *
     * @return \App\Entity\Principal\Usuario retorna o usuario ou NULL
     */
    public function criarUsuario(&$msg, $params=[])
    {
        $usuarioORM    = null;
        $arrFranqueada = $params[ConstanteParametros::CHAVE_FRANQUEADAS];
        unset($params[ConstanteParametros::CHAVE_FRANQUEADAS]);

        if (empty($arrFranqueada) === true) {
            $msg = "Obrigatório o envio de franqueadas";
        } else {
            if (UsuarioBO::podeCriar($this->usuarioRepository, $this->franqueadaRepository, $params, $msg) === true) {
                if (array_search($params[ConstanteParametros::CHAVE_FRANQUEADA_PADRAO]->getId(), $arrFranqueada) === false) {
                    $msg = "Franqueada padrão deve existir na atribuição de franqueadas";
                    return empty($msg);
                }

                $params[ConstanteParametros::CHAVE_SENHA] = password_hash(substr($params[ConstanteParametros::CHAVE_CPF], 8), PASSWORD_DEFAULT);
                $usuarioORM = GeneralORMFactory::criar(\App\Entity\Principal\Usuario::class, true, $params);
                $usuarioORM->setForcaTrocaSenha(true);
                self::persistSeguro($usuarioORM, $msg);

                if ($this->usuarioBO->configuraFranqueadaXUsuario($arrFranqueada, $usuarioORM, $usuarioORM) === false) {
                    $msg        = "Ocorreu um erro na atribuição de franqueadas:\n" . $msg;
                    $usuarioORM = null;
                } else {
                    if ((isset($params[ConstanteParametros::CHAVE_PAPELS]) === true)&&(count($params[ConstanteParametros::CHAVE_PAPELS]) > 0)) {
                        $this->usuarioBO->configuraPapeis($usuarioORM, $params, $msg);
                    }

                    self::flushSeguro($msg);
                }//end if
            }//end if
        }//end if

        return $usuarioORM;
    }

    /**
     * Cria token pro usuario recem criado e envia e-mail para o mesmo
     *
     * @param \App\Entity\Principal\Usuario $usuario
     * @param \Swift_Mailer $mailer
     * @param string $msg
     *
     * @return boolean
     */
    public function criarTokenUsuario(Usuario $usuario, \Swift_Mailer $mailer, &$msg)
    {
        $retorno  = false;
        $params   = [
            "usuario" => $usuario,
            "token"   => UsuarioBO::gerarToken(),
        ];
        $tokenORM = GeneralORMFactory::criar(\App\Entity\Principal\Token::class, true, $params);
        self::criarRegistro($tokenORM, $msg);
        if (empty($msg) === true) {
            $token    = $tokenORM->getToken();
            $urlApp   = getenv('APP_URL');
            $mensagem = "Seja bem-vindo!<br/>Para criar sua senha, <a href='$urlApp/criar-senha?token=$token'>clique aqui</a>.";

            // self::getEmailHelper()->setAssunto('Criar senha');
            // self::getEmailHelper()->setBody($mensagem);
            // self::getEmailHelper()->setPara([ $usuario->getEmail() ]);
            // $retorno = self::getEmailHelper()->enviarMensagem($usuario->getId());

            // mudar para aws já esta implementado no projeto
            $retorno = true;
        }

        return $retorno;
    }

    /**
     * Seta a flag do usuario ativo para false
     *
     * @param string $msg retorno pro front-end
     * @param integer $id chave do registro que foi removido
     *
     * @return boolean TRUE | FALSE
     */
    public function removerUsuario(&$msg, $id)
    {
        $retorno    = false;
        $usuarioOBJ = $this->usuarioRepository->find($id);
        if (empty($usuarioOBJ) === false) {
            $usuarioOBJ->setExcluido(true);
            self::flushSeguro($msg);
            $retorno = empty($msg);
        } else {
            $msg = "Não foi possivel buscar o usuário no banco de dados";
        }

        return $retorno;
    }

    /**
     * Atualiza o usuario com as informações passadas por parametros
     *
     * @param string $msg retorno pro front-end
     * @param integer $id chave do registro que foi removido
     * @param array $params parametros que contem as informações de atualizacoes
     *
     * @return boolean TRUE | FALSE
     */
    public function atualizarUsuario(&$msg,$id,  $params=[])
    {
        

        try {
            
            $usuarioORM = $this->usuarioRepository->find($id);
            
        
            if (isset($params[ConstanteParametros::CHAVE_SENHA])) {
                unset($params[ConstanteParametros::CHAVE_SENHA]);
            }
            // if ((isset($params[ConstanteParametros::CHAVE_FORCA_TROCA_SENHA]) === true) || (empty($params[ConstanteParametros::CHAVE_SENHA]) === true)) {
            //     unset($params[ConstanteParametros::CHAVE_FORCA_TROCA_SENHA]);
            // }

            //define franqueada padão
            $franqueadaPadraoId = '';
            if (isset($params[ConstanteParametros::CHAVE_FRANQUEADA_PADRAO]) ) {
                $franqueadaPadraoId = $params[ConstanteParametros::CHAVE_FRANQUEADA_PADRAO];
                $franqueadaPadrao = $this->franqueadaRepository->find($franqueadaPadraoId);
                $usuarioORM->setFranqueadaPadrao($franqueadaPadrao);           
                unset($params[ConstanteParametros::CHAVE_FRANQUEADA_PADRAO]);
            }
            

            // VALIDA FRANQUEADAS
            $franqueadasLiberadas = $params[ConstanteParametros::CHAVE_FRANQUEADAS];
            $this->validaFranqueadas($franqueadaPadraoId,$franqueadasLiberadas);
            
            
            if (isset($params[ConstanteParametros::CHAVE_PAPELS]) == true) {
                //LIMPAR FRANQUEADAS
                $usuarioORM->limparFranqueadas();
                //ADD FRANQUEADAS
                $this->addAcessoFranqueadas( $usuarioORM, $franqueadaPadraoId, $franqueadasLiberadas);
                unset($params[ConstanteParametros::CHAVE_FRANQUEADAS]);
                
                //LIMPAR PAPEIS
                $usuarioORM->limparPapels();
                //ADD PAPEIS
                $this->addPapeis($usuarioORM,$params);
                unset($params[ConstanteParametros::CHAVE_PAPELS]);
            }



            //copias as permissoes pra não atrabalhar no parse
            $permissoes = [];
            if (isset($params[ConstanteParametros::CHAVE_DADOS_PERMISSAO]) ) {
                $permissoes = $params[ConstanteParametros::CHAVE_DADOS_PERMISSAO];               
            }
            unset($params[ConstanteParametros::CHAVE_DADOS_PERMISSAO]);
            
            // $msg = "";
            //parse dos atributos da tabela de usuário
            $this->getFctHelper()->setParams($params, $usuarioORM);
           
            //seta as permissoes no objeto de usuario
           
            
            //grava tudo no banco.
            $msg = "";
            self::flushSeguro($msg);
            if($msg != ""){
                throw new Exception("Não foi possível gravar dados do usuário.");
            }
 
            Try {
                
                $this->addPermissoes($usuarioORM->getId(),$permissoes);

            }catch (\Throwable $th) {

            }

  
             return true;

        } catch (\Throwable $th) {
            throw $th;
            //echo $th->getMessage();
            //die;
        }
    
        
        
       
       
          
            // if ((isset($parametros[ConstanteParametros::CHAVE_DADOS_PERMISSAO]) === true)&&(count($parametros[ConstanteParametros::CHAVE_DADOS_PERMISSAO]) === 0)) {
            // }
            // $franqueadas = $usuarioOBJ->getFranqueadas();
            // $tamanho     = $franqueadas->count();
            // if (array_search($usuarioOBJ->getFranqueadaPadrao()->getId(), $arrFranqueada) === false) {
            //     $msg = "Franqueada padrão deve existir na atribuição de franqueadas";
            //     return empty($msg);
            // }

            // for ($p = 0; $p < $tamanho; $p++) {
            //     $franqueadaRemovida = $franqueadas->get($p);
            //     $usuarioOBJ->removeFranqueada($franqueadaRemovida);
            //     $franqueadaRemovida->removeUsuario($usuarioOBJ);
            // }

            // for ($i = 0;$i < count($arrFranqueada);$i++) {
            //     $franqueadaORM = $this->franqueadaRepository->find($arrFranqueada[$i]);
            //     if (is_null($franqueadaORM) === true) {
            //         $msg .= "Franqueada: " . $arrFranqueada[$i] . " não encontrada";
            //         break;
            //     }

            //     $usuarioOBJ->addFranqueada($franqueadaORM);
            // }

            // if ((isset($params[ConstanteParametros::CHAVE_PAPELS]) === true)&&(count($params[ConstanteParametros::CHAVE_PAPELS]) > 0)) {
            //     $this->usuarioBO->configuraPapeis($usuarioOBJ, $params, $msg);
            // }

            // if (empty($msg) === false) {
            //     return empty($msg);
            // }

            // $this->getFctHelper()->setParams($params, $usuarioOBJ);
            // self::flushSeguro($msg);
        

        
    }


    
    public function validaFranqueadas($franqueadaId, $franqueadasLiberadas){
        
        if (empty($franqueadasLiberadas)){
            throw new Exception("nenhuma franqueada selecionada");
        }

        $franqueada = $this->franqueadaRepository->find($franqueadaId);
        if(!$franqueada){
            throw new Exception("franquada padrão inválida");
        }
        if (!in_array($franqueadaId, $franqueadasLiberadas)) { 
            throw new Exception("franquada padrão não foi selecionada");
        }   
        
    }
    public function addAcessoFranqueadas(&$usuarioOrm,$franqueadaPadrao,$franqueadas){
        foreach ($franqueadas as $franqueadaId) {
            $franqueadaORM = $this->franqueadaRepository->find($franqueadaId);
            if (is_null($franqueadaORM)) {
                throw new Exception('Franqueada não pode ser adicionada ao usuário, ID:'.$franqueadaId);
            }
            $usuarioOrm->addFranqueada($franqueadaORM);
            
        }
    }

    public function addPapeis(&$usuarioOrm,$params){

        
        if (!isset($params[ConstanteParametros::CHAVE_PAPELS]) ){
            throw new Exception('Nenhum papel enviado na requisição');
        }
        $papeis = $params[ConstanteParametros::CHAVE_PAPELS];

        if (count($papeis) <= 0){
            throw new Exception('Nenhum papel selecionado');
        }
        
        foreach ($papeis as $papel ) {
            $papelORM = $this->papelRepository->find($papel);
            if($papelORM == null){
                throw new Exception('Papel inválido');
            }
            $usuarioOrm->addPapel($papelORM);
        }

    }

    public function addPermissoes($usuarioId, $permissoes){

        

        if (count($permissoes) <= 0){
            throw new Exception('Nenhuma permissão enviada ao usuário');
        }
        
        
        
        $sql = "DELETE FROM modulo_usuario_acao WHERE usuario_id = {$usuarioId} ;".PHP_EOL;

        
       
        foreach ($permissoes as $permissao ) {
            $moduloId = $permissao['modulo'];
            $acaoId = $permissao['acao_sistema'];
            $sql = "{$sql} INSERT INTO modulo_usuario_acao (modulo_id,usuario_id,acao_sistema_id) values({$moduloId},{$usuarioId},{$acaoId}) ; ".PHP_EOL;           
            
        }

        $result = $this->usuarioAcaoRepository->executePermissionInsertList($sql);
        echo json_encode($result);
        
        // $usuarioOrm->addModuloUsuarioAcaoList($modulos);
    }
   


    /**
     * Atualiza a senha no primeiro acesso
     *
     * @param string $mensagemErro
     * @param int $id
     * @param array $parametros
     *
     * @return boolean
     */
    public function atualizarSenhaPrimeiroAcesso(&$mensagemErro, $id, $parametros=[])
    {
        $usuarioOBJ = $this->usuarioRepository->find($id);
        if ($parametros[ConstanteParametros::CHAVE_SENHA] === $parametros[ConstanteParametros::CHAVE_CONFIRMAR_SENHA]) {
            $usuarioOBJ->setSenha(password_hash($parametros[ConstanteParametros::CHAVE_SENHA], PASSWORD_DEFAULT));
            $usuarioOBJ->setForcaTrocaSenha(false);
            self::flushSeguro($mensagemErro);
        } else {
            $mensagemErro .= "As senhas não são iguais.";
        }

        return empty($mensagemErro);
    }

     /**
     * Atualiza Acesso a Todos Relatórios aos usuarios franqueados
     *
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function atualizaUsuariosFranqueadosTodosRelatorios(&$mensagem)
    {
        $usuarioOBJ = $this->usuarioRepository->findAll();
        $ModulosOBJ = $this->moduloRepository->findAll();
        $i = 0;
        
        $permissoes = [];
        foreach ($usuarioOBJ as $usuario ) {
            $papeis = $usuario->getPapels();
            foreach ($papeis as $papel) {
                if ($papel->getDescricao() === 'Franqueado' && $usuario->getSituacao() == 'A') {
                     foreach ($ModulosOBJ as $modulo ) {
                        $moduloRelatorios = $modulo->getModuloPai();
                        if ($moduloRelatorios !== null) {
                            if ($moduloRelatorios->getNome() == 'Relatórios' || $moduloRelatorios->getId() == '5') {
                                $acaoSistemaModuloORM = $this->moduloRepository->buscarModulo($modulo->getID());
                                $acaoSistemaModulo =  $acaoSistemaModuloORM['acaoSistemas'];
                                foreach ($acaoSistemaModulo as $acaoSistema ) {
                                    $result = $this->usuarioAcaoRepository->buscaPermissaoPorRotaModuloAcao($usuario->getID(), 
                                    null,  
                                    $modulo->getID(), 
                                    $acaoSistema['id']);
                                    
                                    if ( empty($result) == true) {
                                        $temp = [
                                            'modulo' => $modulo->getID(),
                                            'usuario' => $usuario->getID(),
                                            'acao_sistema' => $acaoSistema['id']
                                        ];
                                        
                                        $permissoes[] = $temp;
                                    }                                                                  
                                }
                                
                            }
                        }   
                        
                    }    
                    
                }
            }
        }

        if (empty($permissoes) == false) {
            foreach ($permissoes as $permissao ) {
                $moduloId = $permissao['modulo'];
                $acaoId = $permissao['acao_sistema'];
                $usuarioId = $permissao['usuario'];
                $sql = "INSERT INTO modulo_usuario_acao (modulo_id,usuario_id,acao_sistema_id) values({$moduloId},{$usuarioId},{$acaoId}) ; ".PHP_EOL;           
                try {
                    $result = $this->usuarioAcaoRepository->executePermissionInsertList($sql);
                } catch (\Throwable $th) {

                } 
            }
            $mensagem = 'Usuários atualizados com sucesso!'; 
        } else {
            $mensagem = 'Sem usuários para atualizar';
        }
        return;
    }
                    
    /**
     * Cria token pro usuario recem criado e envia e-mail para o mesmo
     *
     * @param \App\Entity\Principal\Usuario $usuario
     * @param \Swift_Mailer $mailer
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function criarTokenRedefinirSenha(Usuario $usuario, &$mensagemErro)
    {
        $retorno  = false;
        $params   = [
            "usuario" => $usuario,
            "token"   => UsuarioBO::gerarToken(),
        ];
        $tokenORM = GeneralORMFactory::criar(\App\Entity\Principal\Token::class, true, $params);
        self::criarRegistro($tokenORM, $mensagemErro);

        if (empty($mensagemErro) === true) {
            $token    = $tokenORM->getToken();
            $urlApp   = getenv('APP_URL');
            $nome =  $usuario->getNome();
            $mensagem = "
                <p>Olá {$nome}.</p>
                <p>Você esta recebendo este email porque foi solicitada uma recuperação de senha com seu login.
                Caso não tenha realizado a solicitação pode desconsiderar esta menssagem.</p>

                <p>Para prosseguir e gerar uma nova senha favor acessar o link abaixo.</p>

                <a href='https://manager.influx.com.br/redefinir-senha?token=$token'> Gerar nova senha<a>
            ";
            if( $usuario->getEmail() != ""){
                $this->emailAwsSender->setEmails([ $usuario->getEmail()]);
                $this->emailAwsSender->setSubject('Redefinição de Senha - Influx Manager');
                $this->emailAwsSender->setMessage($mensagem);
                $retorno = $this->emailAwsSender->send();    
            }
            else{
                throw new Exception("usuário não tem email cadastrado");
                return false;
            }
            
        }

        return $retorno;
    }

    /**
     * Retorna se a senha está correta com a senha do usuário.
     *
     * @param string $mensagem
     * @param \App\Entity\Principal\Usuario $usuario
     * @param string $senha
     *
     * @return boolean
     */
    public function validarSenha (&$mensagem="", Usuario $usuario=null, $senha="", $byPass = false)
    {
        if($byPass){
            return true;
        }

        $senhaValida = password_verify($senha, $usuario->getSenha());
        if ($senhaValida === false) {
            $mensagem = "Senha inválida";
        }

        return $senhaValida;
    }

    /**
     * Adiciona franqueada ao usuário
     *
     * @param string $msg retorno pro front-end
     * @param integer $id chave do registro
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     *
     * @return boolean TRUE | FALSE
     */
    public function adicionarFranqueada (&$msg, $id, $franqueadaORM)
    {
        $usuarioORM = $this->usuarioRepository->find($id);
        $usuarioORM->addFranqueada($franqueadaORM);

        self::flushSeguro($msg);
        return empty($msg);
    }

    /**
     * Busca registros de usuários com base no nome informado
     *
     * @param string $nome Nome do usuário a ser buscado
     * @param integer $franqueada ID da Franqueada
     *
     * @return \App\Entity\Principal\Usuario[]
     */
    public function buscarPorNome ($nome, $franqueada)
    {
        return $this->usuarioRepository->buscarPorNome($nome, $franqueada);
    }


}
