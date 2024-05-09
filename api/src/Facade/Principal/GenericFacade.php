<?php
namespace App\Facade\Principal;

use App\Helper\FunctionHelper;

use App\Helper\CurlHelper;
use Doctrine\Common\Persistence\ManagerRegistry;
// use App\Helper\EmailHelper;

/**
 * Facade generico que servira de importacao para objetos e afins
 *
 * @author Luiz Antonio Costa
 */
class GenericFacade
{
    /**
     *
     * @var $fctHelper FunctionHelper
     */
    private $fctHelper;

    /**
     *
     * @var $curlHelper CurlHelper
     */
    private $curlHelper;

    // /**
    //  *
    //  * @var \App\Helper\EmailHelper
    //  */
    // private $emailHelper;

    /**
     *
     * @var $em \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     *
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $managerRegistry;

    function __construct(ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        $this->fctHelper  = new FunctionHelper();
        $this->curlHelper = new CurlHelper();
        $this->em         = $managerRegistry->getManager($connection);
        $this->managerRegistry = $managerRegistry;
        // $this->emailHelper     = new EmailHelper($managerRegistry);
    }

    /**
     *
     * @return \App\Helper\FunctionHelper
     */
    public function getFctHelper()
    {
        return $this->fctHelper;
    }

    /**
     *
     * @return \App\Helper\CurlHelper
     */
    public function getCurlHelper()
    {
        return $this->curlHelper;
    }

    /**
     * Gera a String de metodo nao implementado
     *
     * @param string $metodo
     */
    public function geraMetodoNaoImplementado($metodo)
    {
        return "Metodo:" . $metodo . " não implementado";
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Irá criar um registro no banco de dados com os valores passados na entidade
     *
     * @param Object $objetoORM Entity que ira ser criada no banco de dados
     * @param string $msg Msg de erro do doctrine
     */
    protected function criarRegistro(&$objetoORM, &$msg="")
    {
        try {
            $this->getEntityManager()->persist($objetoORM);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->detach($objetoORM);
        } catch (\Exception $e) {
            $msg      .= "\nNão foi possivel inserir o registro no banco de dados devido a algum erro no persist<br>Erro:" . $e->getMessage();
            $objetoORM = null;
        }
    }

    /**
     * Executa o $entityManager->persist($entity) para adicionar a entity na memoria para posteriormente ser comitada no banco(gravada) via flushSeguro($msg)
     *
     * @param Object $objetoORM
     * @param string $msg
     */
    protected function persistSeguro(&$objetoORM, &$msg="")
    {
        try {
            $this->getEntityManager()->persist($objetoORM);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $msg      .= "\nNão foi possivel inserir o registro no banco de dados devido a algum erro no persist<br>Erro:" . $e->getMessage();            
            $objetoORM = null;
            Throw $e;
        }
    }

    /**
     * Executa o $entityManager->remove($entity) para adicionar a entity na memoria para posteriormente ser removida do banco via flushSeguro($msg)
     *
     * @param Object $objetoORM
     * @param string $msg
     */
    protected function removerSeguro(&$objetoORM, &$msg="")
    {
        try {
            $this->getEntityManager()->remove($objetoORM);
        } catch (\Exception $e) {
            $msg      .= "\nNão foi possivel remover o registro no banco de dados devido a algum erro no persist<br>Erro:" . $e->getMessage();
            $objetoORM = null;
        }
    }

    /**
     * Irá <b>REMOVER</b> o registro do banco(e no caso de cascata, excluir os relacionados)
     *
     * @param Object $objetoORM Entity que ira ser criada no banco de dados
     * @param string $msg Msg de erro do doctrine
     */
    protected function removerRegistro(&$objetoORM, &$msg="")
    {
        try {
            $this->getEntityManager()->remove($objetoORM);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->detach($objetoORM);
        } catch (\Exception $e) {
            $msg .= "\nNão foi possivel remover o registro no banco de dados devido a algum erro no persist<br>Erro:" . $e->getMessage();
        }
    }

    /**
     * Irá realizar o flush das alterações com Try/Catch para caso dê algum problema no try/catch já retornar a msg de erro pro front
     *
     * @param string $msg ponteiro de msg para retornar pro front end
     */
    protected function flushSeguro(&$msg="")
    {
        try {
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $msg .= "\nNão foi possivel fazer o flush das alterações devido ao seguinte erro do banco:" . $e->getMessage();
        }
    }

    /**
     * Remove os indices do array que nao possuem valor ou forem nulos.
     *
     * @param array $parametros Array com os parametros
     */
    protected function limparParametrosVazios(&$parametros)
    {
        foreach ($parametros as $chave => $valor) {
            if ($parametros[$chave] === '0') {
                continue;
            }

            if (empty($parametros[$chave]) === true) {
                unset($parametros[$chave]);
                continue;
            }

            if (is_null($parametros[$chave]) === true) {
                unset($parametros[$chave]);
                continue;
            }
        }
    }

    /**
     *
     * @return \Doctrine\Common\Persistence\ManagerRegistry
     */
    protected function getManagerRegistry()
    {
        return $this->managerRegistry;
    }

    // /**
    //  *
    //  * @return \App\Helper\EmailHelper
    //  */
    // protected function getEmailHelper()
    // {
    //     return $this->emailHelper;
    // }


}
