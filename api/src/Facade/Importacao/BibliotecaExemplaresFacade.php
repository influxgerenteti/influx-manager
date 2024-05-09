<?php

namespace App\Facade\Importacao;


use App\Facade\Principal\GenericFacade;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\BO\Importacao\BibliotecaExemplaresBO;
use App\Entity\Importacao\BibliotecaExemplares;
use App\Entity\Importacao\BibliotecaObra;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class BibliotecaExemplaresFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\BibliotecaObraRepository
     */
    private $bibliotecaObraRepository;

    /**
     *
     * @var \App\Repository\Importacao\BibliotecaExemplaresRepository
     */
    private $bibliotecaExemplaresRepository;

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
        $this->foundationEntityManager        = $managerRegistry->getManager($connection);
        $this->bibliotecaExemplaresRepository = self::getEntityManager()->getRepository(BibliotecaExemplares::class);
        $this->bibliotecaObraRepository       = self::getEntityManager()->getRepository(BibliotecaObra::class);
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
            BibliotecaExemplaresBO::excluirRegistrosPorFranqueada($this->bibliotecaExemplaresRepository, self::getEntityManager(), $franqueada_id);
            BibliotecaExemplaresBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->bibliotecaObraRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
