<?php

declare(strict_types=1);

namespace PoP\Categories\TypeDataLoaders;

use PoP\LooseContracts\Facades\NameResolverFacade;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeQueryableDataLoader;

class CategoryTypeDataLoader extends AbstractTypeQueryableDataLoader
{
    public function getFilterDataloadingModule(): ?array
    {
        return [\PoP_Categories_Module_Processor_FieldDataloads::class, \PoP_Categories_Module_Processor_FieldDataloads::MODULE_DATALOAD_RELATIONALFIELDS_CATEGORYLIST];
    }

    public function getObjects(array $ids): array
    {
        $query = array(
            'include' => $ids
        );
        $categoryapi = \PoP\Categories\FunctionAPIFactory::getInstance();
        return $categoryapi->getCategories($query);
    }

    public function getDataFromIdsQuery(array $ids): array
    {
        $query = array(
            'include' => $ids
        );
        return $query;
    }

    protected function getOrderbyDefault()
    {
        return NameResolverFacade::getInstance()->getName('popcms:dbcolumn:orderby:categories:count');
    }

    protected function getOrderDefault()
    {
        return 'DESC';
    }

    public function executeQuery($query, array $options = [])
    {
        $categoryapi = \PoP\Categories\FunctionAPIFactory::getInstance();
        return $categoryapi->getCategories($query, $options);
    }

    public function executeQueryIds($query): array
    {
        // $query['fields'] = 'ids';
        $options = [
            'return-type' => POP_RETURNTYPE_IDS,
        ];
        return (array)$this->executeQuery($query, $options);
    }
}
