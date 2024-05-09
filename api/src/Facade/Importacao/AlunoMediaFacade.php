<?php

namespace App\Facade\Importacao;


use App\Facade\Principal\GenericFacade;
use App\Entity\Importacao\AlunoMedia;
use App\Entity\Importacao\Aluno;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\BO\Importacao\AlunoMediaBO;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class AlunoMediaFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\AlunoMediaRepository
     */
    private $alunoMediaRepository;

    /**
     *
     * @var \App\Repository\Importacao\AlunoRepository
     */
    private $alunoRepository;

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
        $this->foundationEntityManager = $managerRegistry->getManager($connection);
        $this->alunoMediaRepository    = self::getEntityManager()->getRepository(AlunoMedia::class);
        $this->alunoRepository         = self::getEntityManager()->getRepository(Aluno::class);
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
            AlunoMediaBO::excluirRegistrosPorFranqueada($this->alunoMediaRepository, self::getEntityManager(), $franqueada_id);
            AlunoMediaBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->alunoRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
