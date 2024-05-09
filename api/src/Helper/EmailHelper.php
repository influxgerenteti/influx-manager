<?php
namespace App\Helper;

use App\Entity\Log\EmailLog;

/**
 * @desc Helper para envio de emails utilizando o SwiftMailer, o helper foi criado no intuito de tornar mais facil a tratativa de erros e de envio de e-mails. <br>
 * Caso o helper deixa a desejar de alguma funcionalidade, exemplor: anexo de arquivos ao e-mail. <br>
 * Tera de ser implementado manualmente seguindo a documentação
 *
 * @see https://swiftmailer.symfony.com/docs/introduction.html
 *
 * @author Luiz Antonio Costa
 */
class EmailHelper
{
    // Lista de constantes
    private const CHAVE_MODO_ENCRIPTACAO = "encription";
    private const CHAVE_IS_SMTP          = "is_smtp";
    private const CHAVE_HOST       = "host";
    private const CHAVE_PORT       = "port";
    private const CHAVE_USERNAME   = "username";
    private const CHAVE_PASSWORD   = "password";
    private const CHAVE_FROM_NOME  = "from_nome";
    private const CHAVE_FROM_EMAIL = "from_email";

    /**
     * Modo de encriptação de email para utilizar quando o isSmtp estiver true
     *
     * @var     string
     * @example Valores validos: "tls", "ssl" ou ""
     */
    protected $encryptionMode = "";

    /**
     * Nome de exibição em quem for enviar o email
     *
     * @var     string
     * @example <b>"Suporte GatiLabs"</b><<'suporte@gatilabs.com.br'>>
     */
    protected $fromNome = "";

    /**
     * Email de quem vai estar enviando email
     *
     * @var     string
     * @example "Suporte GatiLabs"<b><<'suporte@gatilabs.com.br'>></b>
     */
    protected $fromEmail = "";

    /**
     * Lista de e-mails para envio
     *
     * @var     array
     * @example ['email_A@dominio.com', 'email_B@dominio.com' => 'Joseclildo Consuelo']
     */
    protected $listaEmailsEnvio = [];

    /**
     * Nome Autoexplicativo para identificar se o e-mail é em SMTP
     *
     * @var boolean
     */
    protected $isSmtp = false;

    /**
     * Endereco do servidor
     *
     * @var string
     */
    protected $host = "";

    /**
     * Porta em que deve ser utilizada
     *
     * @var integer
     */
    protected $port = 587;

    /**
     * Usuario de autenticação
     *
     * @var string
     */
    protected $username = "";

    /**
     * Senha de autenticação
     *
     * @var string
     */
    protected $password = "";

    /**
     * Assunto do Email
     *
     * @var string
     */
    protected $assunto = "";

    /**
     * Corpo da mensagem
     *
     * @var string
     */
    protected $mensagemTexto = "";

    /**
     * Objeto que está conectado ao servidor para poder realizar o envio de mensagems
     *
     * @var \Swift_Mailer
     */
    protected $mailer = null;

    /**
     * Objeto do logger
     *
     * @var \Swift_Plugins_Loggers_ArrayLogger
     */
    protected $logger = null;

    /**
     * Objeto que estará armazenado a mensagem para poder enviar
     *
     * @var \Swift_Message
     */
    protected $mensagem = null;

    /**
     * Objeto de conexão com o banco de Logs
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManagerBaseLog = null;

    /**
     *
     * @param \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry
     */
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $managerRegistry)
    {
        $this->entityManagerBaseLog = $managerRegistry->getManager("base_log");
        $entityManager = $managerRegistry->getManager("base_principal");
        $emailConfiguracaoRepository = $entityManager->getRepository(\App\Entity\Principal\EmailConfiguracao::class);
        $emailConfiguracaoORM        = $emailConfiguracaoRepository->findAll();
        $emailConfiguracaoORM        = $emailConfiguracaoORM[0];
        $configuracao = self::carregaParametrosBaseDados($emailConfiguracaoORM);
        self::reconfiguraParametros($configuracao);
        self::mudarServidorEmail($configuracao);
    }

    /**
     * Configura os parametros baseado nas informações vindo da base de dados
     *
     * @param \App\Entity\Principal\EmailConfiguracao $emailConfiguracaoORM
     *
     * @return string[]|boolean[]|number[]
     */
    protected function carregaParametrosBaseDados(\App\Entity\Principal\EmailConfiguracao $emailConfiguracaoORM)
    {
        return [
            self::CHAVE_MODO_ENCRIPTACAO => $emailConfiguracaoORM->getEncryption(),
            self::CHAVE_IS_SMTP          => $emailConfiguracaoORM->getIsSmtp(),
            self::CHAVE_HOST             => $emailConfiguracaoORM->getHost(),
            self::CHAVE_PORT             => $emailConfiguracaoORM->getPort(),
            self::CHAVE_USERNAME         => $emailConfiguracaoORM->getUsername(),
            self::CHAVE_PASSWORD         => $emailConfiguracaoORM->getPassword(),
            self::CHAVE_FROM_NOME        => $emailConfiguracaoORM->getFromNome(),
            self::CHAVE_FROM_EMAIL       => $emailConfiguracaoORM->getFromEmail(),
        ];
    }

    /**
     * Limpa os parametros de mensagem
     */
    protected function limpaMensagem()
    {
        $this->assunto       = "";
        $this->mensagemTexto = "";
        $this->mensagem      = null;
    }

    /**
     * Configura os parametros da classe
     *
     * @param array $configuracao
     */
    protected function reconfiguraParametros(array $configuracao)
    {
        $this->encryptionMode = $configuracao[self::CHAVE_MODO_ENCRIPTACAO];
        $this->isSmtp         = $configuracao[self::CHAVE_IS_SMTP];
        $this->host           = $configuracao[self::CHAVE_HOST];
        $this->port           = $configuracao[self::CHAVE_PORT];
        $this->username       = $configuracao[self::CHAVE_USERNAME];
        $this->password       = $configuracao[self::CHAVE_PASSWORD];
        $this->fromNome       = $configuracao[self::CHAVE_FROM_NOME];
        $this->fromEmail      = $configuracao[self::CHAVE_FROM_EMAIL];
    }

    /**
     * Muda o objeto do servidor para um novo com as configurações passadas
     *
     * @param array $configuracao
     *
     * @throws \Exception Caso ocorra algum erro na criacao do Transport ou do mailer
     */
    protected function mudarServidorEmail(array $configuracao)
    {
        try {
            if ($configuracao[self::CHAVE_IS_SMTP] === false) {
                // TODO: Adicionar configuracao para servidores não SMTP verificar no Symfony lista de Transportes Disponiveis
            } else {
                $this->logger = new \Swift_Plugins_Loggers_ArrayLogger();
                $transport    = new \Swift_SmtpTransport($configuracao[self::CHAVE_HOST], $configuracao[self::CHAVE_PORT], $configuracao[self::CHAVE_MODO_ENCRIPTACAO]);
                $transport->setUsername($configuracao[self::CHAVE_USERNAME]);
                $transport->setPassword($configuracao[self::CHAVE_PASSWORD]);
                $this->mailer = new \Swift_Mailer($transport);
                $this->mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($this->logger));
                self::limpaMensagem();
            }
        } catch (\Exception $e) {
            $errorMsg  = "Erro ao tentar criar o objeto do Mailer no EmailHelper: ";
            $errorMsg .= $e->getMessage();
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Registra o log de envio do e-mail
     *
     * @param int $usuarioId
     * @param boolean $bOcorreuErro
     * @param string $logErro
     *
     * @throws \ErrorException
     */
    protected function registraLogEmail($usuarioId, $bOcorreuErro=false, $logErro="")
    {
        $emailLog = new EmailLog();
        $emailLog->setUsuario($usuarioId);
        $emailLog->setDataEnvio(new \DateTime());
        $emailLog->setAssunto($this->assunto);
        $emailLog->setEmailCorpo($this->mensagem);
        $emailLog->setOcorreuErro($bOcorreuErro);
        $emailLog->setErroPhp($logErro);
        $emailLog->setListaEmailEnvio($this->listaEmailsEnvio);
        try {
            $this->entityManagerBaseLog->persist($emailLog);
            $this->entityManagerBaseLog->flush();
            $this->entityManagerBaseLog->detach($emailLog);
        } catch (\Exception $e) {
            $msg     .= "\nNão foi possivel inserir o registro no banco de dados devido a algum erro no persist<br>Erro:" . $e->getMessage();
            $emailLog = null;
            throw new \ErrorException($msg);
        }
    }

    /**
     * Cria um novo e-mail com o assunto informado(caso já tenha sido configurado um previamente ele ira aplicar
     *
     * @param string $assunto
     */
    public function setAssunto($assunto="Sem Assunto")
    {
        self::limpaMensagem();
        $this->mensagem = new \Swift_Message($assunto);
    }

    /**
     * Adiciona conteudo ao e-mail
     *
     * @param string $body
     */
    public function setBody($body="Mensagem corpo")
    {
        if ($this->mensagem === null) {
            self::setAssunto();
        }

        $this->mensagem->setBody($body, 'text/html');
    }

    /**
     * Lista de e-mails para enviar a mensagem
     *
     * @param array $emails
     *
     * @example ['email_A@dominio.com', 'email_B@dominio.com' => 'Joseclildo Consuelo']
     */
    public function setPara(array $emails)
    {
        $this->mensagem->setTo($emails);
        $this->listaEmailsEnvio = implode(', ', $emails);
    }

    /**
     * Envia a mensagem armazenada na propriedade "mensagem" e depois limpa todas as informações da mensagem.
     *
     * @return number Quantidade de pessoas enviadas
     */
    public function enviarMensagem($usuarioId)
    {
        $bOcorreuErro       = false;
        $todosOsLogsDeEnvio = "";
        if ($this->mensagem === null) {
            $resultado = 0;
        } else {
            $this->mensagem->setFrom([$this->fromEmail => $this->fromNome]);
            $resultado = $this->mailer->send($this->mensagem);
            if ($resultado === 0) {
                $bOcorreuErro       = false;
                $todosOsLogsDeEnvio = $this->logger->dump();
            }

            self::registraLogEmail($usuarioId, $bOcorreuErro, $todosOsLogsDeEnvio);
        }

        self::limpaMensagem();
        return $resultado;
    }


}
