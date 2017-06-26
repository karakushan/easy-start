<div class="wrap">
    <h2>Группы опций</h2>
    <?php 
    $es_options=get_option('_es_options_group');
    // echo "<pre>";
    // print_r($es_options);
    // echo "</pre>";
    ?>
    <form action="<?php echo wp_nonce_url(add_query_arg(array('action'=>'add-group')));  ?>" method="post">
        <div class="es_add_group">
            <p>Вы можете групировать опции и выводить их блоками</p>
            <button type="button" id="es_add_group_butt">Добавить группу</button>
            <div class="es_add_group_cont" style="display: none">
                <input type="text" name="es_group_name" placeholder="Название группы" required="required"> 
                <input type="text" name="es_group_slug" placeholder="Название translit" required="required"> 
                <select name="es_group_type" id="" required="required">
                  <option value="">Тип опции</option>
                  <option value="theme_mod">Настройки темы</option>
                  <option value="site_options">Общие настройки</option>
              </select> 
              <input type="submit" name="es_save_group" value="Сохранить">
          </div>
      </div>
  </form>
  <form action="<?php echo wp_nonce_url(add_query_arg(array('action'=>'add-option')));  ?>">
      <?php if ($es_options): ?>
        <div id="es_tabs">
          <ul>
              <?php foreach ($es_options as $key => $es_option): ?>
                  <li><a href="#tabs-<?php echo $key ?>"><?php echo  $es_option['name'] ?></a></li>
              <?php endforeach ?>
          </ul>
          <?php foreach ($es_options as $key => $es_option): ?>
            <div id="tabs-<?php echo $key ?>">
                <h3>Группа опций "<?php echo  $es_option['name'] ?>" (<?php echo $es_option['slug'] ?>)</h3>
                <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
            </div>
        <?php endforeach ?>
    </div>
</ul>
<?php endif ?>
</form>
</div>