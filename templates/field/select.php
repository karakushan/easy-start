<select name="easy[<?php echo $name ?>]" id="es-field-<?php echo $name ?>">
    <option value=""><?php echo $args['first_option'] ?></option>
    <? foreach ($args['values'] as $key => $val) {
        echo '<option value="'.$key.'" '.selected($key,$value,0).'>'.$val.'</option>';
    } ?>
</select>