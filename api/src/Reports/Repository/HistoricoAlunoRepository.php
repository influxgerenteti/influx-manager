<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class HistoricoAlunoRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($alunoId)
    {
        $sql = <<<SQL
            select id ,
            case aluno.pessoa_id  
                        when aluno.responsavel_financeiro_pessoa_id
                        then
                            0
                        else
                            1
                    end as temResposavel
            from aluno
            where id = $alunoId
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
