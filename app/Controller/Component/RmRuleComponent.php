<?php
class RmRuleComponent extends Component {
	public $components = array(
		'RmCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function callDataCategories(){
        $company_data = Configure::read('Config.Company.data');
        $company_id   = Common::hashEmptyField($company_data, 'UserCompany.id', 0);

        // list tree category
        $categories   = $this->controller->Rule->RuleCategory->getTreeDatas($company_id);

        $this->controller->set(array(
            'categories' => $categories,
        ));
    }

    function callRootCategories(){
        $list_root    = $this->controller->Rule->RuleCategory->getData('list', array(
            'conditions' => array(
                'RuleCategory.parent_id IS NULL',
            ),
            'order' => array(
                'RuleCategory.name' => 'ASC'
            ),
        ));

        $this->controller->set(array(
            'list_root' => $list_root,
        ));
    }

    // content rules by category
    function callDataTableRules($values, $space = '', $dashChild = '___'){
        $data = array();
        if (!empty($values)) {

            foreach($values AS $key => $result){
                $id_cat = Common::hashEmptyField($result, 'RuleCategory.id');
                
                // add data rule to rule category, sort by order
                $data['DataRulesAsChild'] = $this->controller->Rule->getData('all', array(
                    'conditions' => array(
                        'Rule.rule_category_id' => $id_cat,
                        'Rule.active' => 1,
                        'Rule.status' => 1,
                    ),
                    'fields'    => array('id', 'name'),
                    'order'     => array(
                        'Rule.order' => 'ASC'
                    ),
                ));

                $result = array_merge($result, $data);

                // debug($result);die();

                if(!empty($result['RuleCategory'])){
                    $data[] = array(
                        'id'   => $result['RuleCategory']['id'],
                        'type' => 'RuleCategory',
                        'name' => $space.$result['RuleCategory']['name']);
                } elseif ($result['Rule']) {
                    $data[] = array(
                        'id'   => $result['Rule']['id'],
                        'type' => 'Rule',
                        'name' => $space.$result['Rule']['name']);
                }

                if (!empty($result['DataRulesAsChild'])) {
                    $space_add = $dashChild.$space;
                    $data_child = $this->callDataTableRules($result['DataRulesAsChild'], $space_add, $dashChild);
                    $data = array_merge($data,$data_child);
                }
                if(!empty($result['children'])){
                    $space_add = $dashChild.$space;
                    $data_child = $this->callDataTableRules($result['children'], $space_add, $dashChild);
                    $data = array_merge($data,$data_child);
                }

            }

        }
        unset($data['DataRulesAsChild']);
        return $data;
    }

    // set the data before save data category parent
    function checkDataCategoryBeforeSave($value){
        $root_id = Common::hashEmptyField($value, 'Rule.root_category_id');
        if (!empty($root_id)) {
            return $this->controller->backprocess_ajax_list_subcategories($root_id, array(
                'call_back_data' => true,
            ));
        }
    }
}
?>