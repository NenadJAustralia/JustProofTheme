<?php defined('ALTUMCODE') || die() ?>

<input type="hidden" id="base_controller_url" name="base_controller_url" value="<?= url('campaign/' . $data->campaign->campaign_id) ?>" />

<header class="header">
    <div class="container">
        <h1 class="h2 mr-3"><?= $this->language->notification_create->header->header ?></h1>

        <div class="d-flex align-items-center text-muted mr-3">
            <img src="https://external-content.duckduckgo.com/ip3/<?= $data->campaign->domain ?>.ico" class="img-fluid icon-favicon mr-1" />

            <a href="<?= url('campaign/' . $data->campaign->campaign_id) ?>">
                <?= $data->campaign->domain ?>
            </a>
        </div>
    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <div class="my-5 mb-lg-0 d-flex flex-column flex-md-row justify-content-center align-items-center">
        <div id="notification_preview"></div>
    </div>

    <form name="create_notification" method="post" role="form">
        <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
        <input type="hidden" name="request_type" value="create" />
        <input type="hidden" name="campaign_id" value="<?= $data->campaign->campaign_id ?>" />

        <div class="form-group">
            <label><i class="fa fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= $this->language->notification_create->input->name ?></label>
            <input type="text" class="form-control" name="name" required="required" />
        </div>

        <div class="mt-5 row d-flex align-items-stretch">
            <?php foreach($data->notifications as $notification_type => $notification_config): ?>

                <?php

                /* Check for permission of usage of the notification */
                if(!$this->user->plan_settings->enabled_notifications->{$notification_type}) {
                    continue;
                }

                ?>

                <?php $notification = \Altum\Notification::get($notification_type) ?>

                <label class="col-12 col-md-6 col-lg-4 mb-3 mb-md-4 custom-radio-box mb-3">

                    <input type="radio" name="type" value="<?= $notification_type ?>" class="custom-control-input" required="required">

                    <div class="card shadow-lg zoomer h-100">
                        <div class="card-body">

                            <div class="mb-3 text-center">
                                <span class="custom-radio-box-main-icon"><i class="<?= $this->language->notification->{strtolower($notification_type)}->icon ?>"></i></span>
                            </div>

                            <div class="card-title font-weight-bold text-center"><?= $this->language->notification->{strtolower($notification_type)}->name ?></div>

                            <p class="text-muted text-center"><?= $this->language->notification->{strtolower($notification_type)}->description ?></p>

                        </div>
                    </div>

                    <div class="preview" style="display: none">
                        <?= preg_replace(['/<form/', '/<\/form>/', '/required=\"required\"/'], ['<div', '</div>', ''], $notification->html) ?>
                    </div>

                </label>

            <?php endforeach ?>
        </div>

        <div class="mt-4">
            <button type="submit" name="submit" class="btn btn-block btn-lg btn-primary"><?= $this->language->global->create ?></button>
        </div>
    </form>
</section>

<?php ob_start() ?>
<script>
    /* Preview handler */
    $('input[name="type"]').on('change', event => {

        let preview_html = $(event.currentTarget).closest('label').find('.preview').html();
        let type = $(event.currentTarget).val();

        console.log(type);

        $('#notification_preview').hide().html(preview_html).fadeIn();

        if(type.includes('_BAR')) {
            $('#notification_preview').removeClass().addClass('notification-create-preview-bar');
        } else {
            $('#notification_preview').removeClass().addClass('notification-create-preview-normal');
        }
    });

    /* Select a default option */
    $('input[name="type"]:first').attr('checked', true).trigger('change');

    /* Form submission */
    $('form[name="create_notification"]').on('submit', event => {

        $.ajax({
            type: 'POST',
            url: 'notifications-ajax',
            data: $(event.currentTarget).serialize(),
            success: (data) => {
                if (data.status == 'error') {
                    notification_container.html('');

                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Fade out refresh */
                    redirect(`notification/${data.details.notification_id}`);

                }
            },
            dataType: 'json'
        });

        event.preventDefault();
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
