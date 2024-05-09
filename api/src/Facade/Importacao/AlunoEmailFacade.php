<?php

namespace App\Facade\Importacao;


use App\Facade\Principal\GenericFacade;
use App\BO\Importacao\AlunoEmailBO;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\Entity\Importacao\AlunoEmail;
use App\Entity\Importacao\Aluno;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class AlunoEmailFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\Repository\Importacao\AlunoEmailRepository
     */
    private $alunoEmailRepository;

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
        $this->alunoRepository         = self::getEntityManager()->getRepository(Aluno::class);
        $this->alunoEmailRepository    = self::getEntityManager()->getRepository(AlunoEmail::class);
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
            AlunoEmailBO::excluirRegistrosPorFranqueada($this->alunoEmailRepository, self::getEntityManager(), $franqueada_id);
            AlunoEmailBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->alunoRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
