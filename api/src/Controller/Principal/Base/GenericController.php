<?php
namespace App\Controller\Principal\Base;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Facade\Log\LogFacade;

// Utilizado para Lock de processamento
use App\Helper\JasperHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
// use App\Helper\EmailHelper;

/**
 *
 * @author        Luiz Antonio Costa
 * @Route("/api")
 */
class GenericController extends Controller
{

    /**
     *
     * @var \App\Facade\Log\LogFacade
     */
    private $logFacade;

    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     *
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $managerRegistry;

    /**
     *
     * @var \App\Helper\JasperHelper
     */
    private $jasperHelper;

    // /**
    //  *
    //  * @var \App\Helper\EmailHelper
    //  */
    // private $emailHelper;

    /**
     * Construtor do controller, por padrao ele recebera o EntityManager, para poder ser trabalhado com os facades
     *
     * @param \Doctrine\ORM\EntityManagerInterface $em Dependencia injetada automaticamente para ser inserida no Facade
     */
    function __construct(\Doctrine\ORM\EntityManagerInterface $em, ManagerRegistry $managerRegistry, ContainerInterface $container)
    {
        $this->entityManager   = $em;
        $this->managerRegistry = $managerRegistry;
        $this->jasperHelper    = new JasperHelper($container->get('kernel')->getProjectDir());
        // $this->emailHelper     = new EmailHelper($managerRegistry);
        $this->constroiFacades();
    }

    /**
     * A funcao "constroiFacades()" eh chamada no constructor da classe extendida
     */
    protected function constroiFacades()
    {
        self::getEntityManager()->clear();
        $this->logFacade = new LogFacade($this->managerRegistry);
    }

    /**
     * Retorna o facade de logs
     *
     * @return \App\Facade\Log\LogFacade
     */
    public function getLogFacade()
    {
        return $this->logFacade;
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
     * Retorna o entityManager instanciada
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     *
     * @return \Doctrine\Common\Persistence\ManagerRegistry
     */
    protected function getManagerRegistry()
    {
        return $this->managerRegistry;
    }

    /**
     *
     * @return \App\Helper\JasperHelper
     */
    protected function getJasperHelper()
    {
        return $this->jasperHelper;
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
