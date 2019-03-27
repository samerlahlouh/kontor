<?php function createTable($cols, $rows, $btns = [], $controlBtnNames = [], $extra_columns = [], $extra_columns_values = [], $extra_control_btns = []){
    $controlBtns = [];
    if(count($controlBtnNames)){
        $btn='';
        foreach ($controlBtnNames as $btnName) {
            if($btnName=='add')
                $btn = create_button('btn_add', '', 'btn-primary', '', 'fa fa-plus-circle');
            elseif($btnName=='edit')
                $btn = create_button('btn_edit', '', 'btn-success', '', 'fa fa-pencil-square-o');
            elseif($btnName=='del')
                $btn = create_button('btn_del', '', 'btn-danger', '', 'fa fa-trash-o');
            array_push($controlBtns, $btn);
        }
    }
    if(count($extra_control_btns))
        foreach ($extra_control_btns as $extra_control_btn) 
            array_push($controlBtns, $extra_control_btn);
?>

    <div id="showHideCols" class="btn-group dropleft hide">
        <button id="showHideCols_btn" type="button" class="btn btn-danger dropdown-toggle showHideCols_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
        <ul class="dropdown-menu">
            <?php for ($i=0; $i < sizeof($cols) ; $i++) {
                 if ($cols[$i] != 'id' && strcmp(substr($cols[$i],-3) ,'_id') && strcmp(substr($cols[$i],-7) ,'_hidden')) {?>
                    <li class="custom-control custom-checkbox">
                        <a class="small toggle-vis" data-value="<?=$i + 1?>">
                        <input class="custom-control-input" type="checkbox"/>
                        <label class="custom-control-label"><?=$cols[$i]?></label>
                        </a>
                    </li>
            <?php }
            }?>
        </ul>
        <?php if(count($controlBtns)){
             foreach ($controlBtns as $controlBtn) {
                 echo $controlBtn;
             }
         }?>
    </div>
        
    <div>
        <table id="example" class="table table-bordered row-border hover" style="width:100%;" data-btns-num="<?=sizeof($btns)?>" data-extra-columns-num="<?=sizeof($extra_columns)?>" data-cols-num="<?=sizeof($cols)+1+sizeof($extra_columns)?>">
            <thead>
                <tr>
                    <th>#</th>
                    
                    <?php foreach ($extra_columns as $extra_column) {?>
                        <th style="text-align:center;"><?=$extra_column['title']?></th>
                    <?php }?>
                    
                    <?php for ($i=0; $i < sizeof($cols) ; $i++) {
                        if($cols[$i] == 'id' || !strcmp(substr($cols[$i],-3) ,'_id') || !strcmp(substr($cols[$i],-7) ,'_hidden')){?>
                            <th style="display: none;"></th>
                        <?php }else{?>
                                    <th style="text-align:center;"><?=$cols[$i]?></th>
                        <?php }
                    }?>
                    

                    <?php foreach ($btns as $btn) {?>
                        <th></th>
                    <?php }?>
                    
                </tr>
            </thead>
            <tbody>

                <?php foreach ($rows as $rowKey=>$row) {?>
                    <tr>
                        <td></td>

                        <?php foreach ($extra_columns as $columnKey => $extra_column) {?>
                            <td  style="text-align:center; padding-right: 4px ; padding-left: 4px;">
                                <?php 
                                if($extra_column['type'] == 'checkbox')
                                    echo create_checkbox("checkbox_$rowKey",
                                                            $extra_column['text'], 
                                                            $extra_column['class'],
                                                            isset($extra_columns_values[$columnKey][$rowKey]) ? $extra_columns_values[$columnKey][$rowKey] : '');

                                elseif($extra_column['type'] == 'custom')
                                    echo $extra_column['html'][$rowKey];
                                ?>
                            </td>
                        <?php }?>

                        <?php foreach ($row as $key => $col) {
                            if($key=='id' || !strcmp(substr($key,-3) ,'_id') || !strcmp(substr($key,-7) ,'_hidden')){?>
                                <td style="display: none;"><?=$col?></td>
                            <?php }else{ ?>
                                        <td style="text-align:center;"><?=$col?></td>
                            <?php }
                        }?>

                        <?php foreach ($btns as $btn) {?>
                            <td  style="text-align:center; padding-right: 4px ; padding-left: 4px;">
                                <?php echo $btn; ?>
                            </td>
                        <?php }?>

                    </tr>
                <?php }?>
                
            </tbody>
            <tfoot>
                <tr>

                    <th></th>

                    <?php foreach ($extra_columns as $extra_column) {?>
                        <th></th>
                    <?php }?>

                    <?php for ($i=0; $i < sizeof($cols) ; $i++) {?>
                        <?php if ($cols[$i] != 'id' && strcmp(substr($cols[$i],-3) ,'_id') && strcmp(substr($cols[$i],-7) ,'_hidden')) {?>
                                <th></th>
                        <?php }else{?>
                                <th style="display: none;"></th>
                        <?php }
                    }?>

                    <?php foreach ($btns as $btn) {?>
                        <th></th>
                    <?php }?>

                </tr>
            </tfoot>
        </table>
    </div>
<?php
}
?>