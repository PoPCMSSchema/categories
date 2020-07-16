<?php

declare(strict_types=1);

namespace PoP\Tags\TypeResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\Tags\TypeDataLoaders\TagTypeDataLoader;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

class TagTypeResolver extends AbstractTypeResolver
{
    public const NAME = 'Tag';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Representation of a tag, added to a post', 'tags');
    }

    public function getID($resultItem)
    {
        $cmstagsresolver = \PoP\Tags\ObjectPropertyResolverFactory::getInstance();
        $tag = $resultItem;
        return $cmstagsresolver->getTagID($tag);
    }

    public function getTypeDataLoaderClass(): string
    {
        return TagTypeDataLoader::class;
    }
}
