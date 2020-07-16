<?php

declare(strict_types=1);

namespace PoP\Categories\Facades;

use PoP\Categories\TypeAPIs\CategoryTypeAPIInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class CategoryTypeAPIFacade
{
    public static function getInstance(): CategoryTypeAPIInterface
    {
        return ContainerBuilderFactory::getInstance()->get('category_type_api');
    }
}
