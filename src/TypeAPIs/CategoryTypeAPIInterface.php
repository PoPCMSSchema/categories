<?php

declare(strict_types=1);

namespace PoP\Categories\TypeAPIs;

use PoP\Taxonomies\TypeAPIs\TaxonomyTypeAPIInterface;

/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
interface CategoryTypeAPIInterface extends TaxonomyTypeAPIInterface
{
    /**
     * Indicates if the passed object is of type Category
     *
     * @param [type] $object
     * @return boolean
     */
    public function isInstanceOfCategoryType($object): bool;
}
