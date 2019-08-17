<?php 
        $displayStyle = !empty($displayStyle)?$displayStyle:false;

        $this->Html->addCrumb($module_title);

        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'properties',
                'action' => 'search',
                'find',
                'admin' => false,
            ),
        ));
?>
<div class="content gray">
    <div class="container">
        <div class="row">
        
            <!-- BEGIN MAIN CONTENT -->
            <div class="main col-sm-12 col-md-8">
                <?php
                        echo $this->element('blocks/common/searchs/sorting');

                        if(!empty($properties)){
                            if( $displayShow == 'grid' ) {
                                $mod = 3;
                            } else {
                                $mod = false;
                            }
                ?>
                <div id="property-listing" class="<?php echo $displayStyle;?> clearfix">
                    <?php
                            echo $this->Html->tag('div', $this->element('blocks/properties/items', array(
                                '_class' => 'col-md-4',
                                'mod' => $mod,
                            )), array(
                                'class' => 'row'
                            ));
                            echo $this->element('custom_pagination');
                    ?>
                </div>
                <?php
                        }else{
                            echo $this->Html->tag('div', __('Data tidak ditemukan.'), array(
                                'class' => 'alert alert-danger'
                            ));
                        }
                ?>
            </div>  
            <!-- END MAIN CONTENT -->
            
            <!-- BEGIN SIDEBAR -->
            <div class="sidebar gray col-sm-12 col-md-4 property-left">
                
                <?php 
                        echo $this->element('blocks/properties/sidebars/find');
                        echo $this->Html->tag('div', $this->element('blocks/users/agents', array(
                            '_classFrame' => 'agents-list',
                            '_class' => 'col-sm-12',
                        )), array(
                            'class' => 'row',
                        ));

						$period		= 6; // month
						$actionID	= 3; // terjual
						$periodTo	= date('Y-m-d');
						$periodFrom	= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $periodTo, $period - 1)));
						$periodDate	= $this->Rumahku->getCombineDate($periodFrom, $periodTo);

						$widget = $this->element('blocks/market_trend/frontend_widget', array(
							'title'			=> __('Periode %s', $periodDate), 
							'period'		=> $period, 
							'actionID'		=> $actionID, 
							'propertyTypes'	=> empty($mt_propertyTypes) ? array() : $mt_propertyTypes, 
							'location'		=> empty($mt_location) ? array() : $mt_location, 
						));

						if($widget){
							$panelTitle = $this->Html->tag('h3', __('STATISTIK PROPERTI TERJUAL'), array(
								'class' => 'widget-title', 
							));

							$content = $this->Html->tag('div', $panelTitle.$widget, array(
								'class' => 'col-sm-12', 
							));

							echo($this->Html->tag('div', $content, array(
								'class' => 'row hidden-print', 
							)));
						}
                ?>
            </div>
            <!-- END SIDEBAR -->

        </div>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>