<?php

/**
 * Displays the content of the dialog box when the user clicks on the "Deactivate" link on the plugin settings page
 */

function wpgmp_add_feedback_form()
{
    $contact_support_template = __('Need help? We are ready to answer your questions. <a href="https://weplugins.com/support/" target="_blank">Contact Support</a>');

    $reasons = array(
        array(
            'id'                => 'NOT_WORKING',
            'text'              => __('The plugin is not working'),
            'input_type'        => 'textarea',
            'input_placeholder' => esc_attr__("Kindly share what didn't work so we can fix it in future updates."),
        ),
        array(
            'id'                => 'SUDDENLY_STOPPED_WORKING',
            'text'              => __('The plugin suddenly stopped working'),
            'input_type'        => '',
            'input_placeholder' => '',
            'internal_message'  => $contact_support_template,
        ),
        array(
            'id'                => 'BROKE_MY_SITE',
            'text'              => __('The plugin broke my site'),
            'input_type'        => '',
            'input_placeholder' => '',
            'internal_message'  => $contact_support_template,

        ),
        array(
            'id'                => 'COULDNT_MAKE_IT_WORK',
            'text'              => __("I couldn't understand how to get it work"),
            'input_type'        => '',
            'input_placeholder' => '',
            'internal_message'  => $contact_support_template,
        ),
        array(
            'id'                => 'FOUND_A_BETTER_PLUGIN',
            'text'              => __('I found a better plugin'),
            'input_type'        => 'textarea',
            'input_placeholder' => esc_attr__('Can you please name the plugin and why you liked that it more?'),
        ),
        array(
            'id'                => 'GREAT_BUT_NEED_SPECIFIC_FEATURE',
            'text'              => __('The plugin is great, but I need a specific feature'),
            'input_type'        => 'textarea',
            'input_placeholder' =>  esc_attr__('Can you share more details on the missing feature?'),
        ),
        array(
            'id'                => 'TEMPORARY_DEACTIVATION',
            'text'              => __("It's a temporary deactivation, I'm just debugging an issue"),
            'input_type'        => '',
            'input_placeholder' => '',
        ),
        array(
            'id'                => 'OTHER',
            'text'              => __('Other'),
            'input_type'        => 'textarea',
            'input_placeholder' => '',
        ),

    );

    $modal_html = '<div class="wpgmp-modal wpgmp-modal-deactivation-feedback">
    <div class="wpgmp-modal-dialog">
        <div class="wpgmp-modal-body">
            <h2>Quick Feedback</h2>
            <div class="wpgmp-modal-panel active">
                <p>If you have a moment, please let us know why you are deactivating</p><ul>';

    foreach ($reasons as $reason) {
        $list_item_classes = 'wpgmp-modal-reason' . (!empty($reason['input_type']) ? ' has-input' : '');

        if (!empty($reason['internal_message'])) {
            $list_item_classes      .= ' has-internal-message';
            $reason_internal_message = $reason['internal_message'];
        } else {
            $reason_internal_message = '';
        }

        $modal_html .= '<li class="' . esc_attr($list_item_classes) . '" data-input-type="' . esc_attr($reason['input_type']) . '" data-input-placeholder="' . esc_attr($reason['input_placeholder']) . '">
        <label>
            <span>
                <input type="radio" name="selected-reason" value="' . esc_attr($reason['id']) . '"/>
            </span>
            <span>' . esc_html($reason['text']) . '</span>
        </label>
        <div class="wpgmp-modal-internal-message">' . $reason_internal_message . '</div>
    </li>';
    }
    $modal_html .= '</ul>
    <div class="fc-backend-loader" style="display:none;"><img id="wpdf_loader_image" src="'.WPGMP_IMAGES.'svg-loader.svg"></div>
                    <label class="wpgmp-modal-anonymous-label">
                        <input type="checkbox" checked/>
                        Send website data and allow to contact me back 
                    </label>
                </div>
            </div>
            <div class="wpgmp-modal-footer">
                <a href="#" class="button button-primary wpgmp-modal-button-deactivate"></a>
                <div class="clear"></div>
            </div>
        </div>
    </div>';

    $script = '';

    $plugin_name = 'wp-google-map-plugin';
    $basename = 'wp-google-map-plugin';

    $script .= '(function($) {
            var modalHtml = ' . json_encode($modal_html) . ",
                \$modal                = $( modalHtml ),
                \$deactivateLink       = $( '#the-list .active[data-slug=\"" . $basename . "\"] .deactivate a' ),
                \$anonymousFeedback    = \$modal.find( '.wpgmp-modal-anonymous-label' ),
                selectedReasonID      = false;
            /* WP added data-plugin attr after 4.5 version/ In prev version was id attr */
            if ( 0 == \$deactivateLink.length ){
            	console.log('length available')
                \$deactivateLink = $( '#the-list .active #" . $plugin_name . " .deactivate a' );
            }

            \$modal.appendTo( $( 'body' ) );

            wpgmpModalRegisterEventHandlers();
            
            function wpgmpModalRegisterEventHandlers() {

                \$deactivateLink.click( function( evt ) {
                    evt.preventDefault();
                    /* Display the dialog box.*/
                    wpgmpModalReset();
                    \$modal.addClass( 'active' );
                    $( 'body' ).addClass( 'has-wpgmp-modal' );
                });

                \$modal.on( 'input propertychange', '.wpgmp-modal-reason-input input', function() {
                    if ( ! wpgmpModalIsReasonSelected( 'OTHER' ) ) {
                        return;
                    }

                    var reason = $( this ).val().trim();

                    /* If reason is not empty, remove the error-message class of the message container to change the message color back to default. */
                    if ( reason.length > 0 ) {
                        \$modal.find( '.message' ).removeClass( 'error-message' );
                        wpgmpModalEnableDeactivateButton();
                    }
                });

                \$modal.on( 'blur', '.wpgmp-modal-reason-input input', function() {
                    var \$userReason = $( this );

                    setTimeout( function() {
                        if ( ! wpgmpModalIsReasonSelected( 'OTHER' ) ) {
                            return;
                        }
                    }, 150 );
                });

                \$modal.on( 'click', '.wpgmp-modal-footer .button', function( evt ) {
                    evt.preventDefault();

                    if ( $( this ).hasClass( 'disabled' ) ) {
                        return;
                    }

                    var _parent = $( this ).parents( '.wpgmp-modal:first' ),
                        _this =  $( this );

                    if ( _this.hasClass( 'allow-deactivate' ) ) {
                        var \$radio = \$modal.find( 'input[type=\"radio\"]:checked' );

                        if ( 0 === \$radio.length ) {
                            /* If no selected reason, just deactivate the plugin. */
                            window.location.href = \$deactivateLink.attr( 'href' );
                            return;
                        }

                        var \$selected_reason = \$radio.parents( 'li:first' ),
                            \$input = \$selected_reason.find( 'textarea, input[type=\"text\"]' ),
                            userReason = ( 0 !== \$input.length ) ? \$input.val().trim() : '';

                        var is_anonymous = ( \$anonymousFeedback.find( 'input' ).is( ':checked' ) ) ? 0 : 1;

                        $.ajax({
                            url       : '".admin_url( 'admin-ajax.php' )."',
                            method    : 'POST',
                            data      : {
                                'action'			: 'wpgmp_submit_uninstall_reason_action',
                                'plugin'			: '" . $basename . "',
                                'reason_id'			: \$radio.val(),
                                'reason_info'		: userReason,
                                'is_anonymous'		: is_anonymous,
                                'wpgmp_ajax_nonce'	: '" . wp_create_nonce('wpgmp_ajax_nonce') . "'
                            },
                            beforeSend: function() {
                            	$('.fc-backend-loader').show(); // Show the loader
                                _parent.find( '.wpgmp-modal-footer .button' ).addClass( 'disabled' );
                                _parent.find( '.wpgmp-modal-footer .button-secondary' ).text( '" . __('Processing') . "' + '...' );
                            },
                            complete  : function( message ) {
                                /* Do not show the dialog box, deactivate the plugin. */
                                window.location.href = \$deactivateLink.attr( 'href' ); 
                            }
                        });
                    } else if ( _this.hasClass( 'wpgmp-modal-button-deactivate' ) ) {
                        /* Change the Deactivate button's text and show the reasons panel. */
                        _parent.find( '.wpgmp-modal-button-deactivate' ).addClass( 'allow-deactivate' );
                        wpgmpModalShowPanel();
                    }
                });

                \$modal.on( 'click', 'input[type=\"radio\"]', function() {
                    var \$selectedReasonOption = $( this );

                    /* If the selection has not changed, do not proceed. */
                    if ( selectedReasonID === \$selectedReasonOption.val() )
                        return;

                    selectedReasonID = \$selectedReasonOption.val();

                    \$anonymousFeedback.show();

                    var _parent = $( this ).parents( 'li:first' );

                    \$modal.find( '.wpgmp-modal-reason-input' ).remove();
                    \$modal.find( '.wpgmp-modal-internal-message' ).hide();
                    \$modal.find( '.wpgmp-modal-button-deactivate' ).text( '" . __('Submit and Deactivate') . "' );

                    wpgmpModalEnableDeactivateButton();

                    if ( _parent.hasClass( 'has-internal-message' ) ) {
                        _parent.find( '.wpgmp-modal-internal-message' ).show();
                    }

                    if (_parent.hasClass('has-input')) {
                        var reasonInputHtml = '<div class=\"wpgmp-modal-reason-input\"><span class=\"message\"></span>' + ( ( 'textfield' === _parent.data( 'input-type' ) ) ? '<input type=\"text\" />' : '<textarea rows=\"5\" maxlength=\"200\"></textarea>' ) + '</div>';

                        _parent.append( $( reasonInputHtml ) );
                        _parent.find( 'input, textarea' ).attr( 'placeholder', _parent.data( 'input-placeholder' ) ).focus();

                        if ( wpgmpModalIsReasonSelected( 'OTHER' ) ) {
                            \$modal.find( '.message' ).text( '" . __('Please tell us the reason so we can improve it.') . "' ).show();
                        }
                    }
                });

                /* If the user has clicked outside the window, cancel it. */
                \$modal.on( 'click', function( evt ) {
                    var \$target = $( evt.target );

                    /* If the user has clicked anywhere in the modal dialog, just return. */
                    if ( \$target.hasClass( 'wpgmp-modal-body' ) || \$target.hasClass( 'wpgmp-modal-footer' ) ) {
                        return;
                    }

                    /* If the user has not clicked the close button and the clicked element is inside the modal dialog, just return. */
                    if ( ! \$target.hasClass( 'wpgmp-modal-button-close' ) && ( \$target.parents( '.wpgmp-modal-body' ).length > 0 || \$target.parents( '.wpgmp-modal-footer' ).length > 0 ) ) {
                        return;
                    }

                    /* Close the modal dialog */
                    \$modal.removeClass( 'active' );
                    $( 'body' ).removeClass( 'has-wpgmp-modal' );

                    return false;
                });
            }

            function wpgmpModalIsReasonSelected( reasonID ) {
                /* Get the selected radio input element.*/
                return ( reasonID == \$modal.find('input[type=\"radio\"]:checked').val() );
            }

            function wpgmpModalReset() {
                selectedReasonID = false;

                wpgmpModalEnableDeactivateButton();

                /* Uncheck all radio buttons.*/
                \$modal.find( 'input[type=\"radio\"]' ).prop( 'checked', false );

                /* Remove all input fields ( textfield, textarea ).*/
                \$modal.find( '.wpgmp-modal-reason-input' ).remove();

                \$modal.find( '.message' ).hide();

                /* Hide, since by default there is no selected reason.*/
                \$anonymousFeedback.hide();

                var \$deactivateButton = \$modal.find( '.wpgmp-modal-button-deactivate' );

                \$deactivateButton.addClass( 'allow-deactivate' );
                wpgmpModalShowPanel();
            }

            function wpgmpModalEnableDeactivateButton() {
                \$modal.find( '.wpgmp-modal-button-deactivate' ).removeClass( 'disabled' );
            }

            function wpgmpModalDisableDeactivateButton() {
                \$modal.find( '.wpgmp-modal-button-deactivate' ).addClass( 'disabled' );
            }

            function wpgmpModalShowPanel() {
                \$modal.find( '.wpgmp-modal-panel' ).addClass( 'active' );
                /* Update the deactivate button's text */
                \$modal.find( '.wpgmp-modal-button-deactivate' ).text( '" . __('Skip and Deactivate') . "' );
            }
        })(jQuery);";
    wp_register_script('wpgmp-deactivation-form', '', array('jquery'), false, true);
    wp_enqueue_script('wpgmp-deactivation-form');
    wp_add_inline_script('wpgmp-deactivation-form', sprintf($script));
}
