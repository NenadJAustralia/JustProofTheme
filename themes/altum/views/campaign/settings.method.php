<?php defined('ALTUMCODE') || die() ?>

<div class="mt-5 d-flex justify-content-between">
        <h2 class="h3"><?= $this->language->campaign->notifications->header ?></h2>

        <div class="col-auto p-0">
            <?php if($this->user->plan_settings->notifications_limit != -1 && $data->notifications_total >= $this->user->plan_settings->notifications_limit): ?>
                <button type="button" data-confirm="<?= $this->language->notification->error_message->notifications_limit ?>" class="btn btn-primary rounded-pill"><i class="fa fa-plus-circle"></i> <?= $this->language->campaign->notifications->create ?></button>
            <?php else: ?>
                <a href="<?= url('notification-create/' . $data->campaign->campaign_id) ?>" class="btn btn-primary rounded-pill"><i class="fa fa-plus-circle"></i> <?= $this->language->campaign->notifications->create ?></a>
            <?php endif ?>
        </div>
    </div>

<?php if(count($data->notifications)): ?>
    <div class="table-responsive table-custom-container mt-3">
        <table class="table table-custom">
            <thead>
            <tr>
                <th><?= $this->language->campaign->notifications->name ?></th>
                <th class="d-none d-md-table-cell"><?= $this->language->campaign->notifications->display_trigger ?></th>
                <th class="d-none d-md-table-cell"><?= $this->language->campaign->notifications->display_duration ?></th>
                <th><?= $this->language->campaign->notifications->is_enabled ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody id="tbody_campaign">

            <?php foreach($data->notifications as $row): ?>
                <?php $row->settings = json_decode($row->settings) ?>

                <tr>
                    <td class="clickable" data-href="<?= url('notification/' . $row->notification_id) ?>">
                        <div class="d-flex flex-column">
                            <?= $row->name ?>

                            <div class="text-muted">
                                <i class="<?= $this->language->notification->{strtolower($row->type)}->icon ?> fa-sm mr-1"></i> <?= $this->language->notification->{strtolower($row->type)}->name ?>
                            </div>
                        </div>
                    </td>
                    <td class="clickable d-none d-md-table-cell" data-href="<?= url('notification/' . $row->notification_id) ?>">
                        <div class="text-muted d-flex flex-column">

                            <?php
                            switch($row->settings->display_trigger) {
                                case 'delay':

                                    echo '<span>' . $row->settings->display_trigger_value . ' <small>' . $this->language->global->date->seconds . '</small></span>';
                                    echo '<small>' . $this->language->notification->settings->{'display_trigger_' . $row->settings->display_trigger} . '</small>';

                                    break;

                                case 'scroll':

                                    echo $row->settings->display_trigger_value . '%';
                                    echo '<small>' . $this->language->notification->settings->{'display_trigger_' . $row->settings->display_trigger}  . '</small>';

                                    break;

                                case 'exit_intent':

                                    echo $this->language->notification->settings->{'display_trigger_' . $row->settings->display_trigger};

                                    break;
                            }
                            ?>

                        </div>
                    </td>
                    <td class="clickable d-none d-md-table-cell" data-href="<?= url('notification/' . $row->notification_id) ?>">
                        <span><?= $row->settings->display_duration == -1 ? $this->language->campaign->notifications->display_duration_unlimited : $row->settings->display_duration . ' <small>' . $this->language->global->date->seconds . '</small>' ?></span>
                    </td>
                    <td>
                        <div class="d-flex">
                            <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= $this->language->campaign->notifications->is_enabled_tooltip ?>">
                                <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="notification_is_enabled_<?= $row->notification_id ?>"
                                    data-row-id="<?= $row->notification_id ?>"
                                    onchange="ajax_call_helper(event, 'notifications-ajax', 'is_enabled_toggle')"
                                    <?= $row->is_enabled ? 'checked="checked"' : null ?>
                                >
                                <label class="custom-control-label clickable" for="notification_is_enabled_<?= $row->notification_id ?>"></label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="dropdown">
                            <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                                <i class="fa fa-ellipsis-v"></i>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="<?= url('notification/' . $row->notification_id) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-1"></i> <?= $this->language->global->edit ?></a>
                                    <a href="<?= url('notification/' . $row->notification_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i> <?= $this->language->notification->statistics->link ?></a>
                                    <a href="#" class="dropdown-item" data-delete="<?= $this->language->global->info_message->confirm_delete ?>" data-row-id="<?= $row->notification_id ?>"><i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= $this->language->global->delete ?></a>
                                </div>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>

            </tbody>
        </table>
    </div>

    <div class="mt-3"><?= $data->pagination ?></div>

<?php else: ?>

    <div class="d-flex flex-column align-items-center justify-content-center">
        <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/no_data.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= $this->language->global->no_data ?>" />
        <h2 class="h4 text-muted"><?= $this->language->global->no_data ?></h2>
        <p><?= $this->language->campaign->notifications->no_data ?></a></p>
    </div>

<?php endif ?>

<?php ob_start() ?>
<script>
    $(document).ready(() => {
        $('[data-delete]').on('click', event => {
            let message = $(event.currentTarget).attr('data-delete');

            if(!confirm(message)) return false;

            /* Continue with the deletion */
            ajax_call_helper(event, 'notifications-ajax', 'delete', () => {

                /* On success delete the actual row from the DOM */
                $(event.currentTarget).closest('tr').remove();

            });

        });
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
