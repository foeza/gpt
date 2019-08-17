<?php
class EmailHelper extends AppHelper {
	var $helpers = array(
        'Rumahku', 'Html', 'Form',
    );

    function TableRow($label, $value, $options = array()){
        $labelView = $ValueView = false;
    	$labelWidth = Common::hashEmptyField($options, 'labelWidth', '50%');
    	$valueWidth = Common::hashEmptyField($options, 'valueWidth', '50%');
    	$labelStyle = Common::hashEmptyField($options, 'labelStyle', 'padding: 10px 30px; margin: 0; color: #9d9db4; font-weight: bold;');
    	$valueStyle = Common::hashEmptyField($options, 'valueStyle', 'padding: 10px 30px; text-align: right;');

        if($label){
        	$labelView = $this->Html->tag('td', $label, array(
        		'width' => $labelWidth,
        		'style' => $labelStyle,
        	));
        }

        if($value){
        	$ValueView = $this->Html->tag('td', $this->Html->tag('p', $value, array(
        		'style' => 'margin: 0;'
        	)), array(
        		'width' => $valueWidth,
        		'style' => $valueStyle,
        	));
        }

    	return $this->Html->tag('tr', $labelView.$ValueView);
    }

    function customListDoku($slug, $params =array()){
        $lists = Common::hashEmptyField($params, 'list_use_doku');
        $params = Common::hashEmptyField($params, 'params');

        switch ($slug) {
            case 'membership':
                $new = Common::hashEmptyField($params, 'MembershipOrder.new');
                $text = !empty($new) ? __('publish') : __('renewal');

                $addLists = array(
                    __('Masuk ke menu project'),
                    __('Pilih project lakukan %s pada tab action', $text),
                    __('Klik checkout'),
                );
                break;
        }

        if(!empty($addLists) && is_array($addLists)){
            $lists = array_merge($lists, $addLists);
        }
        return $lists;
    }
}
?>