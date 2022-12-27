<?php

declare(strict_types=1);

namespace PoPCMSSchema\Categories\SchemaHooks;

use PoP\ComponentModel\FilterInputs\FilterInputInterface;
use PoP\ComponentModel\Schema\SchemaTypeModifiers;
use PoP\ComponentModel\TypeResolvers\InputObjectType\HookNames;
use PoP\ComponentModel\TypeResolvers\InputObjectType\InputObjectTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\InputTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\ScalarType\IDScalarTypeResolver;
use PoP\Root\App;
use PoP\Root\Hooks\AbstractHookSet;
use PoPCMSSchema\Categories\FilterInputs\CategoryIDsFilterInput;

abstract class AbstractAddCategoryFilterInputObjectTypeHookSet extends AbstractHookSet
{
    private ?IDScalarTypeResolver $idScalarTypeResolver = null;
    private ?CategoryIDsFilterInput $categoryIDsFilterInput = null;

    final public function setIDScalarTypeResolver(IDScalarTypeResolver $idScalarTypeResolver): void
    {
        $this->idScalarTypeResolver = $idScalarTypeResolver;
    }
    final protected function getIDScalarTypeResolver(): IDScalarTypeResolver
    {
        /** @var IDScalarTypeResolver */
        return $this->idScalarTypeResolver ??= $this->instanceManager->getInstance(IDScalarTypeResolver::class);
    }
    final public function setCategoryIDsFilterInput(CategoryIDsFilterInput $categoryIDsFilterInput): void
    {
        $this->categoryIDsFilterInput = $categoryIDsFilterInput;
    }
    final protected function getCategoryIDsFilterInput(): CategoryIDsFilterInput
    {
        /** @var CategoryIDsFilterInput */
        return $this->categoryIDsFilterInput ??= $this->instanceManager->getInstance(CategoryIDsFilterInput::class);
    }

    protected function init(): void
    {
        App::addFilter(
            HookNames::INPUT_FIELD_NAME_TYPE_RESOLVERS,
            $this->getInputFieldNameTypeResolvers(...),
            10,
            2
        );
        App::addFilter(
            HookNames::INPUT_FIELD_DESCRIPTION,
            $this->getInputFieldDescription(...),
            10,
            3
        );
        App::addFilter(
            HookNames::INPUT_FIELD_TYPE_MODIFIERS,
            $this->getInputFieldTypeModifiers(...),
            10,
            3
        );
        App::addFilter(
            HookNames::INPUT_FIELD_FILTER_INPUT,
            $this->getInputFieldFilterInput(...),
            10,
            3
        );
    }

    /**
     * @param array<string,InputTypeResolverInterface> $inputFieldNameTypeResolvers
     * @return array<string,InputTypeResolverInterface>
     */
    public function getInputFieldNameTypeResolvers(
        array $inputFieldNameTypeResolvers,
        InputObjectTypeResolverInterface $inputObjectTypeResolver,
    ): array {
        if (!(is_a($inputObjectTypeResolver, $this->getInputObjectTypeResolverClass(), true))) {
            return $inputFieldNameTypeResolvers;
        }
        return array_merge(
            $inputFieldNameTypeResolvers,
            [
                'categoryIDs' => $this->getIDScalarTypeResolver(),
            ]
        );
    }

    abstract protected function getInputObjectTypeResolverClass(): string;

    public function getInputFieldDescription(
        ?string $inputFieldDescription,
        InputObjectTypeResolverInterface $inputObjectTypeResolver,
        string $inputFieldName
    ): ?string {
        if (!(is_a($inputObjectTypeResolver, $this->getInputObjectTypeResolverClass(), true))) {
            return $inputFieldDescription;
        }
        return match ($inputFieldName) {
            'categoryIDs' => $this->__('Get results from the categories with given IDs', 'pop-users'),
            default => $inputFieldDescription,
        };
    }

    public function getInputFieldTypeModifiers(
        int $inputFieldTypeModifiers,
        InputObjectTypeResolverInterface $inputObjectTypeResolver,
        string $inputFieldName
    ): int {
        if (!(is_a($inputObjectTypeResolver, $this->getInputObjectTypeResolverClass(), true))) {
            return $inputFieldTypeModifiers;
        }
        return match ($inputFieldName) {
            'categoryIDs'
                => SchemaTypeModifiers::IS_ARRAY | SchemaTypeModifiers::IS_NON_NULLABLE_ITEMS_IN_ARRAY,
            default
                => $inputFieldTypeModifiers,
        };
    }

    public function getInputFieldFilterInput(
        ?FilterInputInterface $inputFieldFilterInput,
        InputObjectTypeResolverInterface $inputObjectTypeResolver,
        string $inputFieldName,
    ): ?FilterInputInterface {
        if (!(is_a($inputObjectTypeResolver, $this->getInputObjectTypeResolverClass(), true))) {
            return $inputFieldFilterInput;
        }
        return match ($inputFieldName) {
            'categoryIDs' => $this->getCategoryIDsFilterInput(),
            default => $inputFieldFilterInput,
        };
    }
}
