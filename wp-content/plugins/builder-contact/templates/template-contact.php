<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Contact
 *
 * Access original fields: $args['mod_settings']
*/
$fields_default = array(
	'mod_title_contact' => '',
	'layout_contact' => 'style1',
	'field_name_label' => empty($args['mod_settings']['field_name_label']) && !empty($args['mod_settings']['field_name_placeholder']) ? '' : __('Name', 'builder-contact'),
	'field_name_placeholder' => '',
	'field_email_label' => empty($args['mod_settings']['field_email_label']) && !empty($args['mod_settings']['field_email_placeholder']) ? '' : __('Email', 'builder-contact'),
	'field_email_placeholder' => '',
	'field_subject_label' => empty($args['mod_settings']['field_subject_label']) && !empty($args['mod_settings']['field_subject_placeholder']) ? '' : __('Subject', 'builder-contact'),
	'field_subject_placeholder' => '',
	'gdpr' => '',
	'gdpr_label' => __('I consent to my submitted data being collected and stored', 'builder-contact'),
	'field_captcha_label' => __('Captcha', 'builder-contact'),
	'field_extra' => '{ "fields": [] }',
	'field_order' => '{}',
	'field_message_label' => empty($args['mod_settings']['field_message_label']) && !empty($args['mod_settings']['field_message_placeholder']) ? '' : __('Message', 'builder-contact'),
	'field_message_placeholder' => '',
	'field_sendcopy_label' => __('Send Copy', 'builder-contact'),
	'field_send_label' => __('Send', 'builder-contact'),
	'field_send_align' => 'left',
	'animation_effect' => '',
	'css_class_contact' => '',
	'field_message_active' => 'yes',
	'field_message_require' => 'yes',
	'field_subject_active' => '',
	'field_subject_require' => '',
	'field_name_require' => '',
	'field_email_require' => '',
	'field_email_active' => 'yes',
	'field_name_active' => 'yes',
	'field_sendcopy_active' => '',
	'field_captcha_active' => '',
	'field_optin_active' => '',
	'field_optin_label' => __( 'Subscribe to my newsletter.', 'builder-contact' ),
	'provider' => '', // Optin service provider
);

$fields_args = wp_parse_args($args['mod_settings'], $fields_default);
unset($args['mod_settings']);
$fields_default=null;

$field_extra = json_decode( $fields_args['field_extra'], true );
$field_order = json_decode( $fields_args['field_order'], true );

    $container_class = apply_filters('themify_builder_module_classes', array(
    'module','module-'.$args['mod_name'], $args['module_ID'], 'animated-label', 'contact-' . $fields_args['layout_contact'], $fields_args['css_class_contact']
		), $args['mod_name'], $args['module_ID'], $fields_args);
if(!empty($fields_args['global_styles']) && Themify_Builder::$frontedit_active===false){
    $container_class[] = $fields_args['global_styles'];
}

$container_props = apply_filters('themify_builder_module_container_props', self::parse_animation_effect($fields_args, array(
	'id' => $args['module_ID'],
'class' => implode(' ',$container_class),
)), $fields_args, $args['mod_name'], $args['module_ID']);

$orders = array();
if ( 'yes' === $fields_args['field_name_active'] ) {
    $orders['name']=0;
}
if ( 'yes' === $fields_args['field_email_active'] ) {
    $orders['email']=1;
}
if ( 'yes' === $fields_args['field_subject_active'] ) {
    $orders['subject']=2;
}
if ( 'yes' === $fields_args['field_message_active'] ) {
    $orders['message']=3;
}
foreach($orders as $k=>$v){
    $orders[$k]=isset($field_order['field_'.$k.'_label'])?(int)$field_order['field_'.$k.'_label']:0;
}
if(!empty($field_extra['fields'])){
    foreach( $field_extra['fields'] as $i => $field ){
	$orders['extra_'.$i] = (int)(isset($field_order[$field['label']])?$field_order[$field['label']]:(isset($field['order'])?$field['order']:0));
    }
}
$field_order=null;
$isAnimated=$fields_args['layout_contact']==='animated-label';
asort($orders,SORT_NUMERIC);
if(Themify_Builder::$frontedit_active===false){
    $container_props['data-lazy']=1;
}

?>
<!-- module contact -->
<div <?php echo self::get_element_attributes(self::sticky_element_props($container_props,$fields_args)); ?>>
    <?php $container_props=$container_class=null;?>
    <?php if ($fields_args['mod_title_contact'] !== ''): ?>
	<?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_contact'], $fields_args) . $fields_args['after_title']; ?>
    <?php endif; ?>

    <?php do_action('themify_builder_before_template_content_render'); ?>

	<form
		action="<?php echo admin_url('admin-ajax.php'); ?>"
		class="builder-contact"
		id="<?php echo $args['module_ID']; ?>-form"
		method="post"
		data-post-id="<?php echo esc_attr( $args['builder_id'] ); ?>"
		data-element-id="<?php echo esc_attr( str_replace( 'tb_', '', $args['module_ID'] ) ); ?>"
	>
    <div class="contact-message"></div>
	<div class="builder-contact-fields tf_rel">
	<?php foreach($orders as $k=>$i):?>
	    <?php if ( $k==='name' || $k==='email' || $k==='subject' || $k==='message' ) :
			$required = 'yes' === $fields_args["field_{$k}_active"] && 'yes' === $fields_args["field_{$k}_require"];
		?>
		    <div class="builder-contact-field builder-contact-field-<?php echo $k,($k==='message'?' builder-contact-textarea-field':' builder-contact-text-field')?>">
			    <label class="control-label" for="<?php echo $args['module_ID']; ?>-contact-<?php echo $k?>"><span class="tb-label-span"><?php if ($fields_args['field_'.$k.'_label'] !== ''): ?><?php echo $fields_args['field_'.$k.'_label']; ?> </span><?php if ( $required ) : ?><span class="required">*</span><?php endif; endif; ?></label>
			    <div class="control-input">
				    <?php if($k==='message'):?>
					    <textarea name="contact-message"<?php if($isAnimated===false):?> placeholder="<?php echo $fields_args['field_message_placeholder']; ?>"<?php endif;?> id="<?php echo $args['module_ID']; ?>-contact-message" rows="8" cols="45" class="form-control" required></textarea>
				    <?php else:?>
					    <input type="text" name="contact-<?php echo $k?>"<?php if($isAnimated===false):?> placeholder="<?php echo $fields_args['field_'.$k.'_placeholder']; ?>"<?php endif;?> id="<?php echo $args['module_ID']; ?>-contact-<?php echo $k?>" value="" class="form-control" <?php echo ( $required ) ? 'required' : '' ?>/>
				    <?php endif;?>
				    <?php if($isAnimated===true):?>
					    <span class="tb_contact_label">
						    <span class="tb-label-span"><?php if ($fields_args['field_'.$k.'_label'] !== ''): ?><?php echo $fields_args['field_'.$k.'_label']; ?> </span><?php if ( $required ) : ?><span class="required">*</span><?php endif; endif; ?>
					    </span>
				    <?php endif;?>
			    </div>
		</div>
	    <?php else:?>
		    <?php 
		    $index = str_replace('extra_','',$k);
		    if(!isset($field_extra['fields'][$index])){
			continue;
		    }
		    $field = $field_extra['fields'][$index];
		    $field['value'] = isset( $field['value'] ) ? $field['value'] : '';
	$field['label'] = isset( $field['label'] ) ? $field['label'] : '';
		    $required = isset( $field['required'] ) && true === $field['required'];
		    ?>
		    <div class="builder-contact-field builder-contact-field-extra<?php if($field['type']==='tel'):?> builder-contact-text-field<?php endif;?> builder-contact-<?php echo $field['type']; ?>-field">
			<label class="control-label" for="field_extra_<?php echo $index; ?>">
			    <?php echo $field['label']; ?>
			    <?php if( 'static' !== $field['type'] ):?>
				<input type="hidden" name="field_extra_name_<?php echo $index; ?>" value="<?php echo $field['label']; ?>"/>
			    <?php endif;
			    if( $required===true): ?>
				<span class="required">*</span>
			    <?php endif; ?>
			</label>
			<div class="control-input">
			    <?php if( 'textarea' === $field['type'] ): ?>
				<textarea name="field_extra_<?php echo $index; ?>" id="field_extra_<?php echo $index; ?>"<?php if($isAnimated===false):?> placeholder="<?php echo esc_html($field['value']); ?>"<?php endif;?> rows="8" cols="45" class="form-control" <?php echo $required===true?  'required' : '' ?> ></textarea>
			    <?php elseif( 'text' === $field['type'] ||  'tel' === $field['type'] || 'upload' === $field['type']): ?>
				<input type="<?php echo $field['type']==='upload'?'file':$field['type']?>" name="field_extra_<?php echo $index; ?>" id="field_extra_<?php echo $index; ?>"<?php if($isAnimated===false &&  'upload' !== $field['type']):?> placeholder="<?php echo esc_html($field['value']); ?>"<?php endif;?> class="form-control" <?php echo $required===true?  'required' : '' ?> />
			    <?php elseif( 'static' === $field['type'] ): ?>
				<?php echo do_shortcode( $field['value'] ); ?>
			    <?php elseif(!empty($field['value'])):?>
				<?php if( 'radio' === $field['type'] ): ?>
				    <?php foreach( $field['value'] as $value ): ?>
					<label>
					    <input type="radio" name="field_extra_<?php echo $index; ?>" value="<?php esc_attr_e($value); ?>" class="form-control" <?php echo $required===true?  'required' : '' ?> /> <?php echo $value; ?>
					</label>
				    <?php endforeach; ?>
				<?php elseif( 'select' === $field['type'] ): ?>
				    <select id="field_extra_<?php echo $index; ?>" name="field_extra_<?php echo $index; ?>" class="form-control" <?php echo $required===true?  'required' : '' ?>>
					    <?php if($required===false):?><option value=""><?php _e('Please select one' , 'themify')?></option><?php endif;?>
					<?php foreach( $field['value'] as $value ): ?>
					    <option value="<?php esc_attr_e($value); ?>"> <?php echo strip_tags($value); ?> </option>
					<?php endforeach; ?>
				    </select>
				<?php elseif( 'checkbox' === $field['type'] ): ?>
				    <?php foreach( $field['value'] as $value ): ?>
					<label>
					    <input type="checkbox" name="field_extra_<?php echo $index; ?>[]" value="<?php echo esc_html($value); ?>" class="form-control" <?php echo count($field['value'])==1 && $required===true?  'required' : '' ?> /> <?php echo $value; ?>
					</label>
				    <?php endforeach; ?>
				<?php endif; ?>
			    <?php endif; ?>

							    <?php if($isAnimated===true && ('text' === $field['type'] || 'tel' === $field['type'] || 'textarea' === $field['type'])):?>
								    <span class="tb_contact_label">
									    <?php echo $field['label']; 
									    if( $required===true): ?>
										    <span class="required">*</span>
									    <?php endif; ?>
								    </span>
							    <?php endif;?>
			</div>
		    </div>
	    <?php endif;?>

	<?php endforeach;?>
	    <?php if ( 'yes' === $fields_args['field_sendcopy_active'] ) : ?>
		<div class="builder-contact-field builder-contact-field-sendcopy">
		    <div class="control-label">
			<div class="control-input checkbox">
			    <label class="send-copy">
				<input type="checkbox" name="contact-sendcopy" id="<?php echo $args['module_ID']; ?>-sendcopy" value="1" /> <?php echo $fields_args['field_sendcopy_label']; ?>
			    </label>
			</div>
		    </div>
		</div>
	    <?php endif; ?>

			    <?php if ( $fields_args['field_optin_active'] ) : ?>
				    <?php
				    if ( ! class_exists( 'Builder_Optin_Services_Container' ) )
					    include_once( THEMIFY_BUILDER_INCLUDES_DIR. '/optin-services/base.php' );
				    $optin_instance = Builder_Optin_Services_Container::get_instance()->get_provider( $fields_args['provider'] );
					$optin_inputs='';
				    if($optin_instance){
				        foreach ( $optin_instance->get_options() as $provider_field ) :
							if ( isset( $provider_field['id'], $fields_args[ $provider_field['id'] ] ) ){
                                $optin_inputs .= '<input type="hidden" name="contact-optin-'.$provider_field['id'].'" value="'.esc_attr( $fields_args[ $provider_field['id'] ] ).'" />';
                            }
						endforeach;
                    }
				    if ( ''!==$optin_inputs ) : ?>
					    <div class="builder-contact-field builder-contact-field-optin">
						    <div class="control-label">
							    <div class="control-input checkbox">
								    <input type="hidden" name="contact-optin-provider" value="<?php echo esc_attr( $fields_args['provider'] ); ?>" />
								    <?php echo $optin_inputs; ?>
								    <label class="optin">
									    <input type="checkbox" name="contact-optin" id="<?php echo $args['module_ID']; ?>-optin" value="1" /> <?php echo $fields_args['field_optin_label']; ?>
								    </label>
							    </div>
						    </div>
					    </div>
				    <?php endif; ?>
			    <?php endif; ?>

			    <?php if ( 'accept' === $fields_args['gdpr'] ) : ?>
				    <div class="builder-contact-field builder-contact-field-gdpr">
					    <div class="control-label">
						    <div class="control-input checkbox">
							    <label class="field-gdpr">
								    <input type="checkbox" name="gdpr" value="1" required/> <?php echo $fields_args['gdpr_label']; ?> <span class="required">*</span>
							    </label>
						    </div>
					    </div>
				    </div>
			    <?php endif; ?>

	    <?php if ( 'yes' === $fields_args['field_captcha_active'] && Builder_Contact::get_instance()->get_option('recapthca_public_key') != '' && Builder_Contact::get_instance()->get_option('recapthca_private_key') != '') : ?>
		<?php $recaptcha_version = Builder_Contact::get_instance()->get_option('recapthca_version','v2'); ?>
		<div class="builder-contact-field builder-contact-field-captcha">
					    <?php if('v3' !== $recaptcha_version) : ?>
		    <label class="control-label" for="<?php echo $args['module_ID']; ?>-contact-captcha"><?php echo $fields_args['field_captcha_label']; ?> <span class="required">*</span></label>
					    <?php endif; ?>
		    <div class="control-input">
			<div class="themify_captcha_field <?php if ( 'v2' === $recaptcha_version ) :?>g-recaptcha<?php endif; ?>" data-sitekey="<?php echo esc_attr(Builder_Contact::get_instance()->get_option('recapthca_public_key')); ?>" data-ver="<?php esc_attr_e($recaptcha_version); ?>"></div>
		    </div>
		</div>
	    <?php endif; ?>
	    <div class="builder-contact-field builder-contact-field-send control-input tf_text<?php echo $fields_args['field_send_align'][0];?> tf_clear">
			<button type="submit" class="btn btn-primary"><span class="tf_loader"></span> <?php echo $fields_args['field_send_label']; ?></button>
	    </div>
	</div>
    </form>

    <?php do_action('themify_builder_after_template_content_render'); ?>
</div>
<!-- /module contact -->
