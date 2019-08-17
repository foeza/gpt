<?php
class RmTargetComponent extends Component {
    public $components = array(
        'RmCommon'
    );

    function initialize(Controller $controller, $settings = array()) {
        $this->controller = $controller;
    }

    function _callBeforeSaveTargetSales($value = null, $id = null){
        $this->TargetProjectSale = ClassRegistry::init('TargetProjectSale');
        
        $data =& $this->controller->request->data;

        $temp_detail = array();
        $TargetProjectSaleDetail    = Common::hashEmptyField($value, 'TargetProjectSaleDetail');

        if(!empty($TargetProjectSaleDetail)){
            
            foreach ($TargetProjectSaleDetail as $key => $val) {
                $detail_id      = Common::hashEmptyField($val, 'TargetProjectSaleDetail.id');
                $month_target   = Common::hashEmptyField($val, 'TargetProjectSaleDetail.month_target');
                $target_revenue = Common::hashEmptyField($val, 'TargetProjectSaleDetail.target_revenue');
                $target_listing = Common::hashEmptyField($val, 'TargetProjectSaleDetail.target_listing');
                $target_ebrosur = Common::hashEmptyField($val, 'TargetProjectSaleDetail.target_ebrosur');

                $temp_detail[$month_target] = array(
                    'id' => $detail_id,
                    'month_target' => $month_target,
                    'target_revenue' => $target_revenue,
                    'target_listing' => $target_listing,
                    'target_ebrosur' => $target_ebrosur,
                );
            }

            $value['TargetProjectSaleDetail'] = $temp_detail;
        }

        if(!empty($data)){
            $company_id = Configure::read('Config.Company.data.UserCompany.user_id');

            $data = Common::dataConverter($data, array(
                'price' => array(
                    'TargetProjectSale' => array(
                        'target_revenue',
                        'target_listing',
                        'target_ebrosur',
                    ),
                ),
            ), false);

            $target_revenue = Common::hashEmptyField($data, 'TargetProjectSale.target_revenue');
            $currency_id    = Common::hashEmptyField($data, 'TargetProjectSale.currency_id', null);

            $data = Hash::insert($data, 'TargetProjectSale.company_id', $company_id);
            
            if(!empty($id)){
                $data = Hash::insert($data, 'TargetProjectSale.id', $id);
            }

            $target_revenue_measure = $this->priceMeasure($target_revenue, $currency_id);
            $data = Hash::insert($data, 'TargetProjectSale.target_revenue_measure', $target_revenue_measure);

            $TargetProjectSaleDetail = Common::hashEmptyField($data, 'TargetProjectSaleDetail');

            if(!empty($TargetProjectSaleDetail)){
                foreach ($TargetProjectSaleDetail as $key => $val) {
                    $data_temp =& $TargetProjectSaleDetail[$key];

                    $data_temp = Common::dataConverter($data_temp, array(
                        'price' => array(
                            'target_revenue',
                            'target_listing',
                            'target_ebrosur',
                        ),
                    ), false, '-');

                    $target_revenue_detail = Common::hashEmptyField($data_temp, 'target_revenue');

                    $data_temp['target_revenue_measure'] = $this->priceMeasure($target_revenue_detail, $currency_id);
                    $data_temp['currency_id'] = $currency_id;
                }

                $data = Hash::insert($data, 'TargetProjectSaleDetail', $TargetProjectSaleDetail);
            }

            if(!empty($id) && !empty($value)){
                $data = Hash::insert($data, 'TargetProjectSaleDetail.{n}.target_project_sales_id', $id);

                $data['TargetProjectSaleLog'][] = array(
                    'target_project_sales_id'   => $id,
                    'reason_change'             => Common::hashEmptyField($data, 'TargetProjectSale.reason_change'),
                    'new_data'                  => serialize($data),
                    'old_data'                  => serialize($value),
                    'user_id'                   => Configure::read('User.id')
                );
            }

            $result = $this->TargetProjectSale->doSave($data, $id);

            $this->RmCommon->setProcessParams($result, array(
                'controller' => 'target_revenue',
                'action' => 'index',
                'admin' => true,
            ));
        }else{
            if(!empty($value)){
                $target_revenue = Common::hashEmptyField($value, 'TargetProjectSale.target_revenue');
                $TargetProjectSaleDetail = Common::hashEmptyField($value, 'TargetProjectSaleDetail');

                $value = Hash::insert($value, 'TargetProjectSale.target_revenue', floatval($target_revenue));

                if(!empty($TargetProjectSaleDetail)){
                    foreach ($TargetProjectSaleDetail as $key => $val) {
                        $ref_detail =& $TargetProjectSaleDetail[$key];

                        $target_revenue = Common::hashEmptyField($val, 'target_revenue');

                        $ref_detail['target_revenue'] = floatval($target_revenue);
                    }

                    $value = Hash::insert($value, 'TargetProjectSaleDetail', $TargetProjectSaleDetail);
                }

                $data = $value;
            }
        }

        $currencies = $this->TargetProjectSale->Currency->getData('list', array(
            'fields' => array(
                'Currency.id', 'Currency.symbol',
            ),
            'cache' => __('Currency.symbol'),
        ));

        $this->controller->set(array(
            'currencies' => $currencies
        ));
    }

    function priceMeasure($price, $currency_id){

        if(!empty($price) && !empty($currency_id)){
            $currencies = $this->TargetProjectSale->Currency->getData('list', array(
                'fields' => array(
                    'Currency.id', 'Currency.rate',
                ),
                'cache' => __('Currency.rate'),
            ));

            if(!empty($currencies)){
                $rate = Common::hashEmptyField($currencies, $currency_id);

                $result = $price * $rate;
            }else{
                $result = $price;
            }
        }else{
            $result = $price;
        }

        return $result;
    }
}
?>