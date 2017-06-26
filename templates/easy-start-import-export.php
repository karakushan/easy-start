<div class="wrap">
<h2><?php _e( 'Export', 'easy-start' ) ?></h2>
    <?php
    $config=new \ES_LIB\ES_config();
    $config_file=json_encode($config->data,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $config_base=get_option('es-config-json');
    $config_value=!empty($config_base) ? $config_base:$config_file;
    ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'es-options-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Данные для экспорта</th>
                <td>
                    <p>Вы можете перенести конфигурационые данные от одного проекта к другому. Скопируйте этот код и вставьте в файл es-config.json, который нужно предварительно создать в корне вашей темы.</p>
                    <textarea  cols="100" rows="10" readonly><?php echo $config_value ?></textarea>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>

