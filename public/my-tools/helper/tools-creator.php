<?php
function create_button($id='', $text='',  $calss='', $style='', $icon='', $modal_dataTarget='', $attrs=''){
$modal='';
if($modal_dataTarget){
    $modal = "data-toggle='modal' data-target='$modal_dataTarget' data-whatever='@getbootstrap'";
}
return "<button id='$id' type='button' class='btn $calss btn-xs dt-edit' style='$style' $modal $attrs>
            $text
            <i class='$icon' aria-hidden='true'></i>
        </button>";
}

//---------------------------------- Create card -------------------------------------//
function begin_card($icon='', $title='', $headerBtns=[]){?>
    <div class='card mb-3 border-purple-color main-card'>
        <div class='card-header'>
            <i class='<?=$icon?>'></i> <?=$title?>
            <?php 
            foreach ($headerBtns as $headerBtn) {
                echo $headerBtn;
            }
            ?>
        </div>
            
        <div class='card-body'>
<?php
}
function end_card($footer_text='', $controlBtnNames=[], $targetformId='', $footer_btns = [], $footer_text_style=''){?>
    </div>
    <?php if($footer_text || $controlBtnNames || $footer_btns){?>
        <div class='card-footer small text-muted' style="<?=$footer_text_style?>">
            <?php
                if (strtotime($footer_text))
                    echo $footer_text . __('main_lng.footer_txt');
                else
                    echo $footer_text;
                
                foreach ($controlBtnNames as $btnName) {
                    if($btnName=='add')
                        echo Form::submit(__('main_lng.add'),['style'=>'margin-left:2px;margin-right:2px;', 'class'=>'btn btn-primary pull-right', 'form'=>$targetformId, 'id'=>'card_submit_add_btn']);
                    elseif($btnName=='update')
                        echo Form::submit(__('main_lng.update'),['style'=>'margin-left:2px;margin-right:2px;', 'class'=>'btn btn-success pull-right', 'form'=>$targetformId, 'id'=>'card_submit_edit_btn']);
                }

                foreach ($footer_btns as $footer_btn) {
                    echo $footer_btn;
                }
            ?>
        </div>
    <?php }?>
    
</div>
<?php 
}

//---------------------------------- Create child card -------------------------------------//
function begin_incubated_child_card(){?>
   <div id="accordion" role="tablist" aria-multiselectable="true">
<?php
}

function begin_child_card($id, $title='', $showing_status='', $refresh_btn=false){?>
    <div id="accordion" role="tablist" aria-multiselectable="true">
        <div class="card card-child border-black-color">
                <h6 class="card-header child-card-header-color" role="tab" id="heading_<?=$id?>">

                    <?php if($refresh_btn){?>
                    <i id="refresh_btn_<?=$id?>" class="fa fa-refresh pull-right refresh-btn"></i>
                    <?php }?>

                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?=$id?>" aria-expanded="true" aria-controls="collapse_<?=$id?>" class="d-block link-card child-card-header-text">
                        <i class="fa fa-chevron-down pull-right"></i>
                        <?= $title?>
                    </a>
                </h6>

                <div id="collapse_<?=$id?>" class="collapse <?=$showing_status?>" role="tabpanel" aria-labelledby="heading_<?=$id?>">
                    <div class="card-body">
<?php
}
function end_child_card(){?>
                </div>
            </div>
        </div>
    </div>
<?php 
}
function end_incubated_child_card(){?>
    </div>
<?php 
}

//---------------------------------- Create Modal -------------------------------------//
function begin_modal($id='', $title=''){
    $modalLabelId = $id.'Label'?>

    <div id='<?=$id?>' class="modal fade" tabindex="-1" role="dialog" aria-labelledby='<?=$modalLabelId?>' aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id='<?=$modalLabelId?>'><?=$title?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
<?php
}
function end_modal($controlBtnNames='', $targetformId=''){?>
      </div>
      <?php if($controlBtnNames){?>

      <div class="modal-footer">
            <?php 
            $controlBtns = [];
            foreach ($controlBtnNames as $btnName) {
                if($btnName=='add')
                    echo Form::submit(__('main_lng.add'),['class'=>'btn btn-primary', 'form'=>$targetformId, 'id'=>'submit_add_btn']);
                elseif($btnName=='edit')
                    echo Form::submit(__('main_lng.edit'),['class'=>'btn btn-success', 'form'=>$targetformId, 'id'=>'submit_edit_btn']);
                elseif($btnName=='del')
                    echo Form::submit(__('main_lng.del'),['class'=>'btn btn-danger', 'form'=>$targetformId, 'id'=>'submit_del_btn']);
                elseif($btnName=='close'){?>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=__('main_lng.close')?></button>  
            <?php 
                }
            }
            ?>
      </div>

      <?php }?>
    </div>
  </div>
</div>
<?php }

//---------------------------------- Create row -------------------------------------//
function begin_row($col_style="", $col_kind='col-md', $row_class=''){?>
   <div class="row <?=$row_class?>">
        <div class="<?=$col_kind?>" style="<?=$col_style?>">
<?php
}
function next_col($col_style="", $col_kind='col-md'){?>
        </div>
        <div class="<?=$col_kind?>" style="<?=$col_style?>">
<?php
}
function end_row(){?>
        </div>
    </div>
<?php 
}

//---------------------------------- Create input group -------------------------------------//
function create_input_group($id='', $text='', $icon='', $kind='text', $input_data=[], $private_attrs=[], $defult_value=''){
    $attrs = ['id'=>$id, 'class'=>'form-control input-with-icon-label', 'placeholder'=>$text];
    $attrs = array_merge($attrs, $private_attrs);

    echo Form::label($id, $text, ['class'=>'col-form-label label_above'])?>
    <div class="input-group mb-3">
        <?php 
            echo Form::label($id, $text, ['class'=>'col-form-label label_side']);
            if($kind == 'text')
                echo Form::text($id, $defult_value , $attrs);
            elseif($kind == 'password')
                echo Form::password($id, $attrs);
            elseif($kind == 'number')
                echo Form::number($id, $defult_value , $attrs);
            elseif($kind == 'date'){
                $attrs = array_merge($attrs, ['onClick'=>'$(this).removeClass("placeholderclass")']);
                echo Form::date($id, $defult_value , $attrs);
            }
            elseif($kind == 'email')
                echo Form::email($id, $defult_value , $attrs);
            elseif($kind == 'textarea')
                echo Form::textarea($id, $defult_value , $attrs);
            elseif($kind == 'select'){
                unset($attrs['placeholder']);
                $select_elements = $input_data;
                
                $optionsAttributes = [];
                if($defult_value == ''){
                    $select_elements['0'] = $text;
                    $optionsAttributes = ['0' => ['hidden', 'disabled', 'selected']];
                }
                
                echo Form::select($id, $select_elements, $defult_value, $attrs, $optionsAttributes);
            }

        ?>
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="<?=$icon?>" aria-hidden="true"><?= ($icon=='sharp'?'#':'')?></i></span>
        </div>
    </div>
 <?php }
 //---------------------------------- Create row -------------------------------------//
function line(){?>
    <hr/>
<?php }

//---------------------------------- Create checkbox -------------------------------------//
function create_checkbox($id='checkbx', $text='', $class='', $default_value=''){ 
    if($default_value == 1)
        $default_value = 'checked';
    return "<div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input $class $default_value' name='$id' id='$id' $default_value>
                <label class='custom-control-label' for='$id'>$text</label>
            </div>";
 }

//---------------------------------- Create checkbox -------------------------------------//
function create_checkbox_group($name='checkbxGroup', $texts=[], $values=[], $class='', $default_values=[], $private_attrs='', $classes=[]){ 
    foreach ($values as $key => $value) {
        $default_value = '';
        if(isset($default_values[$key]) && $default_values[$key] == 1)
            $default_value = 'checked';
        $checkbox_element =  "<div id='checkbox_div_$value' class='form-group checkbox_div'>
                                <div class='custom-control custom-checkbox'>
                                <input id='checkbox_$value' type='checkbox' class='custom-control-input $class teen_is_".$classes[$key]."' $default_value name='".$name."[]' value='$value' $private_attrs>
                                <label class='custom-control-label' for='checkbox_$value'>".(isset($texts[$key])?$texts[$key]:'')."</label>
                                </div>
                            </div>";
        echo $checkbox_element;
    }
 }?>

 