<?php

declare(strict_types=1);

namespace PoP\Tags\Facades;

use PoP\Tags\TypeAPIs\TagTypeAPIInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class TagTypeAPIFacade
{
    public static function getInstance(): TagTypeAPIInterface
    {
        return ContainerBuilderFactory::getInstance()->get('tag_type_api');
    }
}
