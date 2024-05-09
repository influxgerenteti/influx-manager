<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Principal\UrlSistema;

class UrlSistemaFixtures extends Fixture
{
    /**
     *
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    public function __construct(\Symfony\Component\Routing\RouterInterface $router)
    {
        $this->router = $router;
    }

    public function load(ObjectManager $manager)
    {
        $collecaoRotas = $this->router->getRouteCollection();
        $todasAsRotas  = $collecaoRotas->all();
        foreach ($todasAsRotas as $rotaObj) {
            $rotaString = $rotaObj->getPath();
            if (preg_match("[/_error|/doc/|doc.json]", $rotaString) === 0) {
                $urlSistema = new UrlSistema();
                $urlSistema->setUrlSistema($rotaString);
                $manager->persist($urlSistema);
            }
        }

        $manager->flush();
    }


}
