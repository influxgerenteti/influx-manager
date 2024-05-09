<?php

namespace App\Reports\Repository;

use Doctrine\Persistence\ManagerRegistry;

class ItensEstoqueRepository
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function get($where)
    {
        $sql = <<<SQL
            SELECT 
                    tipo_item.descricao as tipo_item,
                    item.descricao as item,
                    item.saldo_estoque,
                    item.estoque_minimo,
                    item.valor_compra,
                    item.valor_venda
            
            FROM item
            inner join tipo_item
                on item.tipo_item_id = tipo_item.id
            
            where $where
        SQL;

        return $this->managerRegistry->getConnection()->fetchAllAssociative($sql);
    }


}
