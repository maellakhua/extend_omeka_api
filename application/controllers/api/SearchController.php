<?php

class SearchController extends Zend_Controller_Action {

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->db->setDefaultModelName('SearchText');
    }

    public function getAction() {
     // Respect only GET parameters when browsing.
        $this->getRequest()->setParamSources(array('_GET'));
        
        // Inflect the record type from the model name.
        $pluralName = $this->view->pluralize($this->_helper->db->getDefaultModelName());

        // Apply contrller-provided default sort parameters
        if (!$this->_getParam('sort_field')) {
            $defaultSort = apply_filters("{$pluralName}_browse_default_sort",
                null ,
                array('params' => $this->getAllParams())
            );
            if (is_array($defaultSort) && isset($defaultSort[0])) {
                $this->setParam('sort_field', $defaultSort[0]);

                if (isset($defaultSort[1])) {
                    $this->setParam('sort_dir', $defaultSort[1]);
                }
            }
        }
        
        $params = $this->getAllParams();

        //$recordsPerPage = null;
        //$currentPage = $this->getParam('page', 1);
        
        // Get the records filtered to Omeka_Db_Table::applySearchFilters().
        $records = $this->_helper->db->findBy($params, null, null, TRUE);
        $totalRecords = $this->_helper->db->count($params);
        
        // Add pagination data to the registry. Used by pagination_links().
        // if ($recordsPerPage) {
        //     Zend_Registry::set('pagination', array(
        //         'page' => $currentPage, 
        //         'per_page' => $recordsPerPage, 
        //         'total_results' => $totalRecords, 
        //     ));
        // }
        
        $this->view->assign(array($pluralName => $records, 'total_results' => $totalRecords));
        $this->_helper->jsonApi($records);
        // print_r($records);
    }

}