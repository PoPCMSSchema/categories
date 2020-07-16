<?php

declare(strict_types=1);

namespace PoP\Categories\FieldResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\CustomPosts\FieldInterfaces\CustomPostFieldInterfaceResolver;

class CustomPostFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return [
            CustomPostFieldInterfaceResolver::class,
        ];
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'categories',
            'mainCategory',
            'catName',
            'catSlugs',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'categories' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'mainCategory' => SchemaDefinition::TYPE_ID,
            'catName' => SchemaDefinition::TYPE_STRING,
            'catSlugs' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_STRING),
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function isSchemaFieldResponseNonNullable(TypeResolverInterface $typeResolver, string $fieldName): bool
    {
        $nonNullableFieldNames = [
            'categories',
            'catSlugs',
        ];
        if (in_array($fieldName, $nonNullableFieldNames)) {
            return true;
        }
        return parent::isSchemaFieldResponseNonNullable($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'categories' => $translationAPI->__('Categories to which this post was added', 'pop-categories'),
            'mainCategory' => $translationAPI->__('Main category to which this post was added', 'pop-categories'),
            'catName' => $translationAPI->__('Name of the main category to which this post was added', 'pop-categories'),
            'catSlugs' => $translationAPI->__('Slugs of the categories to which this post was added', 'pop-categories'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $categoryapi = \PoP\Categories\FunctionAPIFactory::getInstance();
        $post = $resultItem;
        switch ($fieldName) {
            case 'categories':
                return $categoryapi->getPostCategories($typeResolver->getID($post), ['return-type' => POP_RETURNTYPE_IDS]);

            case 'mainCategory':
                // Simply return the first category
                if ($cats = $typeResolver->resolveValue($post, 'categories', $variables, $expressions, $options)) {
                    return $cats[0];
                }
                return null;

            case 'catName':
                if ($cat = $typeResolver->resolveValue($post, 'mainCategory', $variables, $expressions, $options)) {
                    return $categoryapi->getCategoryName($cat);
                }
                return null;

            case 'catSlugs':
                return $categoryapi->getPostCategories($typeResolver->getID($post), ['return-type' => POP_RETURNTYPE_SLUGS]);
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
