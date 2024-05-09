<?php

namespace App\Facade\Importacao;


use App\Facade\Principal\GenericFacade;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\BO\Importacao\BibliotecaObraAutorBO;
use App\Entity\Importacao\BibliotecaAutor;
use App\Entity\Importacao\BibliotecaObraAutor;
use App\Entity\Importacao\BibliotecaObra;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class BibliotecaObraAutorFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\BibliotecaAutorRepository
     */
    private $bibliotecaAutorRepository;

    /**
     *
     * @var \App\Repository\Importacao\BibliotecaObraRepository
     */
    private $bibliotecaObraRepository;

    /**
     *
     * @var \App\Repository\Importacao\BibliotecaObraAutorRepository
     */
    private $bibliotecaObraAutorRepository;
    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $foundationEntityManager;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry, "base_importacao");
        $this->foundationEntityManager       = $managerRegistry->getManager($connection);
        $this->bibliotecaAutorRepository     = self::getEntityManager()->getRepository(BibliotecaAutor::class);
        $this->bibliotecaObraAutorRepository = self::getEntityManager()->getRepository(BibliotecaObraAutor::class);
        $this->bibliotecaObraRepository      = self::getEntityManager()->getRepository(BibliotecaObra::class);
    }


    /**
     * Cria vários registros na base de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $franqueada_id ID da franqueada
     * @param \App\Helper\XlsxHelper $xlsxHelper Helper do XLSX
     */
    public function criar(&$mensagemErro, $franqueada_id, &$xlsxHelper)
    {
        if (FranqueadaBO::franqueadaExisteBanco($this->foundationEntityManager->getRepository(Franqueada::class), ["id" => $franqueada_id]) === true) {
            BibliotecaObraAutorBO::excluirRegistrosPorFranqueada($this->bibliotecaObraAutorRepository, self::getEntityManager(), $franqueada_id);
            BibliotecaObraAutorBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->bibliotecaAutorRepository, $this->bibliotecaObraRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
