<?php

declare(strict_types=1);

namespace PoP\Categories\FieldResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Categories\TypeResolvers\CategoryTypeResolver;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\QueriedObject\FieldInterfaces\QueryableFieldInterfaceResolver;

class CategoryFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(CategoryTypeResolver::class);
    }

    public static function getImplementedInterfaceClasses(): array
    {
        return [
            QueryableFieldInterfaceResolver::class,
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
            'url' => $translationAPI->__('Category URL', 'pop-categories'),
            'name' => $translationAPI->__('Category', 'pop-categories'),
            'slug' => $translationAPI->__('Category slug', 'pop-categories'),
            'description' => $translationAPI->__('Category description', 'pop-categories'),
            'parent' => $translationAPI->__('Parent category (if this category is a child of another one)', 'pop-categories'),
            'count' => $translationAPI->__('Number of custom posts containing this category', 'pop-categories'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $cmscategoriesresolver = \PoP\Categories\ObjectPropertyResolverFactory::getInstance();
        $categoryapi = \PoP\Categories\FunctionAPIFactory::getInstance();
        $category = $resultItem;
        switch ($fieldName) {
            case 'url':
                return $categoryapi->getCategoryLink($typeResolver->getID($category));

            case 'name':
                return $cmscategoriesresolver->getCategoryName($category);

            case 'slug':
                return $cmscategoriesresolver->getCategorySlug($category);

            case 'description':
                return $cmscategoriesresolver->getCategoryDescription($category);

            case 'parent':
                return $cmscategoriesresolver->getCategoryParent($category);

            case 'count':
                return $cmscategoriesresolver->getCategoryCount($category);
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'parent':
                return CategoryTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
