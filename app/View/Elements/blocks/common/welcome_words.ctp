<?php
        $_site_name  = !empty($_site_name)?$_site_name:false;
        $action_type = !empty($action_type)?$action_type:false;
        $full_name   = $this->Rumahku->filterEmptyField($User, 'full_name');
?>

<div class="greeting">
    <?php 
            echo $this->Html->tag('h4', sprintf(__('Hai, %s'), $full_name));
            
            switch ($action_type) {
                case 'sell':
                    echo $this->Html->tag('p', sprintf(__('Lengkapi data mengenai produk Anda pada kolom di bawah ini, dan ikuti tahapannya.')));
                    break;
                
                default:
                    echo $this->Html->tag('p', sprintf(__('Terima kasih telah mendaftar sebagai anggota %s.'), $_site_name));
                    echo $this->Html->tag('p', sprintf(__('Bantu %s menklasifikasikan diri Anda, agar sistem kami dapat memberikan saran pilihan properti terbaik, yang sesuai dengan impian Anda.'), $_site_name));
                    break;
            }
    ?>
</div>