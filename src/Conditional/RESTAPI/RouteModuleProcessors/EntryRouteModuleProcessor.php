<?php

declare(strict_types=1);

namespace PoP\Categories\Conditional\RESTAPI\RouteModuleProcessors;

use PoP\ModuleRouting\AbstractEntryRouteModuleProcessor;
use PoP\ComponentModel\State\ApplicationState;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\API\Facades\FieldQueryConvertorFacade;
use PoP\Routing\RouteNatures;
use PoP\Categories\Routing\RouteNatures as CategoryRouteNatures;
use PoP\RESTAPI\DataStructureFormatters\RESTDataStructureFormatter;

class EntryRouteModuleProcessor extends AbstractEntryRouteModuleProcessor
{
    public const HOOK_REST_FIELDS = __CLASS__ . ':RESTFields';

    private static $restFieldsQuery;
    private static $restFields;
    public static function getRESTFields(): array
    {
        if (is_null(self::$restFields)) {
            self::$restFields = self::getRESTFieldsQuery();
            if (is_string(self::$restFields)) {
                self::$restFields = FieldQueryConvertorFacade::getInstance()->convertAPIQuery(self::$restFields);
            }
        }
        return self::$restFields;
    }
    public static function getRESTFieldsQuery(): string
    {
        if (is_null(self::$restFieldsQuery)) {
            $restFieldsQuery = 'id|name|count|url';
            self::$restFieldsQuery = (string) HooksAPIFacade::getInstance()->applyFilters(
                self::HOOK_REST_FIELDS,
                $restFieldsQuery
            );
        }
        return self::$restFieldsQuery;
    }

    public function getModulesVarsPropertiesByNature()
    {
        $ret = array();
        $vars = ApplicationState::getVars();
        $ret[CategoryRouteNatures::CATEGORY][] = [
            'module' => [
                \PoP_Categories_Module_Processor_FieldDataloads::class,
                \PoP_Categories_Module_Processor_FieldDataloads::MODULE_DATALOAD_RELATIONALFIELDS_CATEGORY,
                [
                    'fields' => isset($vars['query']) ?
                        $vars['query'] :
                        self::getRESTFields()
                ]
            ],
            'conditions' => [
                'scheme' => POP_SCHEME_API,
                'datastructure' => RESTDataStructureFormatter::getName(),
            ],
        ];

        return $ret;
    }

    public function getModulesVarsPropertiesByNatureAndRoute()
    {
        $ret = array();
        $vars = ApplicationState::getVars();
        $routemodules = array(
            POP_CATEGORIES_ROUTE_CATEGORIES => [
                \PoP_Categories_Module_Processor_FieldDataloads::class,
                \PoP_Categories_Module_Processor_FieldDataloads::MODULE_DATALOAD_RELATIONALFIELDS_CATEGORYLIST,
                [
                    'fields' => isset($vars['query']) ?
                        $vars['query'] :
                        self::getRESTFields()
                ]
            ],
        );
        foreach ($routemodules as $route => $module) {
            $ret[RouteNatures::STANDARD][$route][] = [
                'module' => $module,
                'conditions' => [
                    'scheme' => POP_SCHEME_API,
                    'datastructure' => RESTDataStructureFormatter::getName(),
                ],
            ];
        }
        // Commented until creating route POP_CUSTOMPOSTS_ROUTE_CUSTOMPOSTS
        // $routemodules = array(
        //     POP_CUSTOMPOSTS_ROUTE_CUSTOMPOSTS => [
        //         \PoP_Categories_Module_Processor_FieldDataloads::class,
        //         \PoP_Categories_Posts_Module_Processor_FieldDataloads::MODULE_DATALOAD_RELATIONALFIELDS_CATEGORYPOSTLIST,
        //         [
        //             'fields' => isset($vars['query']) ?
        //                 $vars['query'] :
        //                 EntryRouteModuleProcessorHelpers::getRESTFields()
        //             ]
        //         ],
        // );
        // foreach ($routemodules as $route => $module) {
        //     $ret[CategoryRouteNatures::CATEGORY][$route][] = [
        //         'module' => $module,
        //         'conditions' => [
        //             'scheme' => POP_SCHEME_API,
        //             'datastructure' => RESTDataStructureFormatter::getName(),
        //         ],
        //     ];
        // }
        return $ret;
    }
}
