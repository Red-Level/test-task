<?php
/*
Plugin Name: Delete repeat fields
Version: 1.0.0
*/

// Добавляем мета-бокс (не сохраняет ничего)
add_action( 'add_meta_boxes', 'add_meta_box_remove_repeat_acf_pro' );
function add_meta_box_remove_repeat_acf_pro() {
  add_meta_box( 'custom-meta-box', 'Удаляем выборочно подкасты', 'callback__remove_repeat_acf_pro_meta_box', [
    'promo',
    'post'
  ], 'side', 'high', null );
}

// Вывод в мета-боксе
function callback__remove_repeat_acf_pro_meta_box( $post, $meta ) {
  $id_post       = $post->ID;
  $field_objects = get_field_objects( $id_post );
    $sub_field_name = 'podborka_item_type';

  // Собираем все повторители
  $listParentRepeats = [];
  foreach ( $field_objects as $item ) {
    if ( $item['type'] === 'repeater' ) {
      $listParentRepeats[] = [
        'label' => $item['label'],
        'key'   => str_replace( 'field_', 'field-', $item['key'] ),
        'name'  => $item['name'],
      ];
    }
  }

  // Собираем все субполя по типу $sub_field_name
  $listArray = [];
  foreach ( $listParentRepeats as $item ) {
    foreach ( $field_objects[ $item['name'] ]['value'] as $subitem ) {
      $listArray[] = trim( $subitem[$sub_field_name] );
    }
  }
  // оставляем только уникальные
  $listArray = array_unique( $listArray );

  ?>

  <?php if ( ! $listArray ) { ?>
        <p>На этой странице нет нужных полей, проверьте ACF поля, поиск осуществляется по ключу - podborka_item_type</p>
  <?php } ?>

  <?php if ( $listArray ) { ?>
        <div>
            <p>
                <label for='custom-remove-acf-field'><strong>Выбрать тип подкаста:</strong></label>
            </p>

            <select name='remove_acf_select_field' id='custom-remove-acf-field' style='width: 95%;'>
                <option value='0'>Не выбрано</option>
        <?php foreach ( $listParentRepeats as $item ) { ?>
                    <option value='<?php echo $item['key']; ?>'><?php echo $item['label']; ?></option>
        <?php } ?>
            </select>
        </div>
        <div>
            <p>
                <label for='custom-remove-acf-field'><strong>Выбрать какие удаляем:</strong></label>
            </p>

            <select name='remove_acf_select_subfield' id='custom-remove-acf-subfield' style='width: 95%;'>
                <option value='0'>Не выбрано</option>
        <?php foreach ( $listArray as $item ) { ?>
                    <option value='<?php echo $item; ?>'><?php echo $item; ?></option>
        <?php } ?>
            </select>
        </div>
        <p>
            <a class='button button-primary button-large custom-remove-acf-field--btn'>Удалить</a>
            <span class="spinner custom-remove-acf-field--spinner" style="display: inline-block; margin-top: 10px;"></span>
        </p>
        <script>
            jQuery(function ($) {
                $(document).on('click', '.custom-remove-acf-field--btn', function (e) {
                    if (!$(this).hasClass('disabled')) {
                      

                        let _this = $(this);
                        let spinner = $('.custom-remove-acf-field--spinner');
                        let field = $('#custom-remove-acf-field option:selected').val();
                        let subfield = $('#custom-remove-acf-subfield option:selected').val();
                        let parent_class = '.acf-' + field;

                        _this.addClass('disabled');
                        spinner.addClass('is-active');

                        $(parent_class + ' .acf-field').each(function () {
                            if ($(this).data('name') === '<?php echo $sub_field_name; ?>') {
                                if ($(this).find('select option:selected').val() === subfield && !$(this).parents('.acf-row').hasClass('acf-clone')) {
                                    $(this).parents('.acf-row').find('.acf-icon.-minus').click();
                                    $(this).parents('.acf-row').find('.acf-icon.-minus').click();
                                }
                               
                            }

                        });
                        setTimeout(function() {
                            _this.removeClass('disabled');
                            spinner.removeClass('is-active');
                        }, 1000);
                    }
                    return false;
                });
            });
        </script>
    <?php
  }
}

function admin_footer_remove_repeat_acf_pro_spinner() {
    ?>
    <div class="disable-page-spinner">
        <span class="spinner"></span>
    </div>
    <style>
        .disable-page-spinner {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background: rgba(0,0,0,.2);
            z-index: 9999;
            height: 100%;
            display: none;
        }
        .disable-page-spinner .spinner {
            float: none !important;
            display: block;
            margin: auto !important;
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }
    </style>
  <?php
}

add_action('admin_notices', 'admin_footer_remove_repeat_acf_pro_spinner');