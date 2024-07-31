<header class="container-masthead">
                    <div class="row">
                        <div style="display:flex">
                            <div class="">
                            <img src="<?php echo get_template_directory_uri() ?>/images/header.svg" alt="header">
                            </div>

                        <?php if(is_active_sidebar('ear-content-1')): ?>
                        <div class="<?php echo $ear_content_class ?> columns">
                            <?php do_action( 'before_sidebar' ); ?>
                            <?php dynamic_sidebar( 'ear-content-1' ) ?>
                        </div>
                        <?php endif; ?>

                        <?php if(is_active_sidebar('ear-content-2')): ?>
                        <div class="<?php echo $ear_content_2_class ?> columns">
                            <?php do_action( 'before_sidebar' ); ?>
                            <?php dynamic_sidebar( 'ear-content-2' ) ?>
                        </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </header>
                <div id="pst-container">
						<div>Philippine Standard Time:</div><div id="pst-time"></div>
					   </div>
                       <embed src="http://localhost/wordpress/wp-content/uploads/2024/07/IPRA-LAW.pdf" type="application/pdf" style="height:700px; width:640px"></embed>