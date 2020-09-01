<?php
/**
 * Klevu Product API interface for preserve layout
 */
namespace Klevu\Categorynavigation\Model\Api\Magento\Request;


interface CategoryInterface
{
    /**
     * This method executes the the Klevu API request if it has not already been called, and takes the result
     * with the result we get all the item IDs, pass into our helper which returns the child and parent id's.
     * We then add all these values to our class variable $_klevu_product_ids.
     *
     * @param $query
     * @return array
     */
    public function _getKlevuProductIds();
    
    
    /**
     * This method will returns excluded products ids
     *
     * @return array
     */
    public function getKlevuProductExcludedIds();    

    public function getSearchFilters();
    /**
     * This method resets the saved $_klevu_product_ids.
     * @return boolean
     */
    public function reset();

    /**
     * This method will return the parent child ids
     * @return array
     */
    public function getKlevuVariantParentChildIds();
}

