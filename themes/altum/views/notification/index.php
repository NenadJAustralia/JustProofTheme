<?php defined('ALTUMCODE') || die() ?>

<input type="hidden" id="base_controller_url" name="base_controller_url" value="<?= url('notification/' . $data->notification->notification_id) ?>" />

<header class="header pb-0">
    <div class="container">

        <div class="d-flex align-items-center">
            <h1 class="h2 mr-3"><?= $data->notification->name ?></h1>

            <div class="custom-control custom-switch mr-3" data-toggle="tooltip" title="<?= $this->language->campaign->notifications->is_enabled_tooltip ?>">
                <input
                        type="checkbox"
                        class="custom-control-input"
                        id="campaign_is_enabled_<?= $data->notification->notification_id ?>"
                        data-row-id="<?= $data->notification->notification_id ?>"
                        onchange="ajax_call_helper(event, 'notifications-ajax', 'is_enabled_toggle')"
                    <?= $data->notification->is_enabled ? 'checked="checked"' : null ?>
                >
                <label class="custom-control-label clickable" for="campaign_is_enabled_<?= $data->notification->notification_id ?>"></label>
            </div>

            <div class="dropdown">
                <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                    <i class="fa fa-ellipsis-v"></i>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="<?= url('notification/' . $data->notification->notification_id) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-1"></i> <?= $this->language->global->edit ?></a>
                        <a href="<?= url('notification/' . $data->notification->notification_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i> <?= $this->language->notification->statistics->link ?></a>
                        <a href="#" class="dropdown-item" data-delete="<?= $this->language->global->info_message->confirm_delete ?>" data-row-id="<?= $data->notification->notification_id ?>"><i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= $this->language->global->delete ?></a>
                    </div>
                </a>
            </div>
        </div>

        <div class="d-flex">
            <div class="d-flex align-items-center text-muted mr-3">
                <img src="https://external-content.duckduckgo.com/ip3/<?= $data->notification->domain ?>.ico" class="img-fluid icon-favicon mr-1" />

                <a href="<?= url('campaign/' . $data->notification->campaign_id) ?>">
                    <?= $data->notification->domain ?>
                </a>
            </div>

            <span class="text-muted">
                <i class="<?= $this->language->notification->{strtolower($data->notification->type)}->icon ?> fa-sm mr-1"></i> <?= $this->language->notification->{strtolower($data->notification->type)}->name ?>
            </span>
        </div>

        <?= $this->views['method_menu'] ?>
    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <?= $this->views['method'] ?>

</section>

<?php ob_start() ?>
<link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/pickr.min.css' ?>" rel="stylesheet" media="screen">
<link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script>
    /* Delete handler for the notification */
    $('[data-delete]').on('click', event => {
        let message = $(event.currentTarget).attr('data-delete');

        if(!confirm(message)) return false;

        /* Continue with the deletion */
        ajax_call_helper(event, 'notifications-ajax', 'delete', (data) => {
            redirect(`campaign/${data.details.campaign_id}`);
        });

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
