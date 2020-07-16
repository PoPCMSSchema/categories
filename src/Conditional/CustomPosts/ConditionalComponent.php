<?php

declare(strict_types=1);

namespace PoP\Categories\Conditional\CustomPosts;

use PoP\Categories\Component;
use PoP\Root\Component\YAMLServicesTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;

/**
 * Initialize component
 */
class ConditionalComponent
{
    use YAMLServicesTrait;

    public static function initialize(
        array $configuration = [],
        bool $skipSchema = false,
        array $skipSchemaComponentClasses = []
    ): void {
        self::maybeInitYAMLSchemaServices(Component::$COMPONENT_DIR, $skipSchema, '/Conditional/CustomPosts');

        if (class_exists('\PoP\RESTAPI\Component')
            && !in_array(\PoP\RESTAPI\Component::class, $skipSchemaComponentClasses)
        ) {
            \PoP\Categories\Conditional\CustomPosts\Conditional\RESTAPI\ConditionalComponent::initialize(
                $configuration,
                $skipSchema
            );
        }
    }

    /**
     * Boot component
     *
     * @return void
     */
    public static function beforeBoot(): void
    {
        ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__ . '\\FieldResolvers');

        // Initialize all conditional components
        if (class_exists('\PoP\RESTAPI\Component')) {
            \PoP\Categories\Conditional\CustomPosts\Conditional\RESTAPI\ConditionalComponent::beforeBoot();
        }
    }
}
