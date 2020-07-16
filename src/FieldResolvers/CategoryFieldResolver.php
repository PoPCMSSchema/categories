<?php

declare(strict_types=1);

namespace PoP\Tags\FieldResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Tags\TypeResolvers\TagTypeResolver;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\QueriedObject\FieldInterfaces\QueryableObjectFieldInterfaceResolver;

class TagFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(TagTypeResolver::class);
    }

    public static function getImplementedInterfaceClasses(): array
    {
        return [
            QueryableObjectFieldInterfaceResolver::class,
        ];
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'url',
            'name',
            'slug',
            'description',
            'parent',
            'count',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'url' => SchemaDefinition::TYPE_URL,
            'name' => SchemaDefinition::TYPE_STRING,
            'slug' => SchemaDefinition::TYPE_STRING,
            'description' => SchemaDefinition::TYPE_STRING,
            'parent' => SchemaDefinition::TYPE_ID,
            'count' => SchemaDefinition::TYPE_INT,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'url' => $translationAPI->__('Tag URL', 'pop-tags'),
            'name' => $translationAPI->__('Tag', 'pop-tags'),
            'slug' => $translationAPI->__('Tag slug', 'pop-tags'),
            'description' => $translationAPI->__('Tag description', 'pop-tags'),
            'parent' => $translationAPI->__('Parent category (if this category is a child of another one)', 'pop-tags'),
            'count' => $translationAPI->__('Number of custom posts containing this tag', 'pop-tags'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $cmstagsresolver = \PoP\Tags\ObjectPropertyResolverFactory::getInstance();
        $tagapi = \PoP\Tags\FunctionAPIFactory::getInstance();
        $tag = $resultItem;
        switch ($fieldName) {
            case 'url':
                return $tagapi->getTagLink($typeResolver->getID($tag));

            case 'name':
                return $cmstagsresolver->getTagName($tag);

            case 'slug':
                return $cmstagsresolver->getTagSlug($tag);

            case 'description':
                return $cmstagsresolver->getTagDescription($tag);

            case 'parent':
                return $cmstagsresolver->getTagParent($tag);

            case 'count':
                return $cmstagsresolver->getTagCount($tag);
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'parent':
                return TagTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
