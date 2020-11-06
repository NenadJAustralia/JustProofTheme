<?php defined('ALTUMCODE') || die() ?>

<header class="header">
    <div class="container">

        <div class="d-flex justify-content-between">
            <h1 class="h2"><span class="underline"><?= $this->language->dashboard->header ?></span></h1>
        </div>

        <p>
            <span class="badge badge-success"><?= sprintf($this->language->account->plan->header, $this->user->plan->name) ?></span>

            <?php if($this->user->plan_id != 'free'): ?>
                <span><?= sprintf($this->language->account->plan->subheader, '<strong>' . \Altum\Date::get($this->user->plan_expiration_date, 2) . '</strong>') ?></span>
            <?php endif ?>

            <?php if($this->settings->payment->is_enabled): ?>
                <span>(<a href="<?= url('plan/upgrade') ?>"><?= $this->language->account->plan->renew ?></a>)</span>
            <?php endif ?>
        </p>

        <?php if($this->user->plan_settings->notifications_impressions_limit != -1): ?>
            <?php
            $progress_percentage = $this->user->plan_settings->notifications_impressions_limit == '0' ? 100 : ($this->user->current_month_notifications_impressions / $this->user->plan_settings->notifications_impressions_limit) * 100;
            $progress_class = $progress_percentage > 60 ? ($progress_percentage > 85 ? 'badge-danger' : 'badge-warning') : 'badge-success';
            ?>
            <p class="text-muted">
                <?=
                    sprintf($this->language->account->plan->notifications_impressions_limit,
                        '<span class="badge ' . $progress_class . '">' . nr($progress_percentage) . '%</span>',
                        nr($this->user->plan_settings->notifications_impressions_limit)
                    );
                ?>
            </p>
        <?php endif ?>

    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <div class="mt-5 d-flex justify-content-between">
        <h2 class="h3"><?= $this->language->dashboard->campaigns->header ?></h2>

        <div class="col-auto p-0">
            <?php if($this->user->plan_settings->campaigns_limit != -1 && $data->campaigns_total >= $this->user->plan_settings->campaigns_limit): ?>
                <button type="button" data-confirm="<?= $this->language->campaign->error_message->campaigns_limit ?>"  class="btn btn-primary rounded-pill"><i class="fa fa-plus-circle"></i> <?= $this->language->dashboard->campaigns->create ?></button>
            <?php else: ?>
                <button type="button" data-toggle="modal" data-target="#create_campaign" class="btn btn-primary rounded-pill"><i class="fa fa-plus-circle"></i> <?= $this->language->dashboard->campaigns->create ?></button>
            <?php endif ?>
        </div>
    </div>

    <?php if(count($data->campaigns)): ?>
        <div class="table-responsive table-custom-container mt-3">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= $this->language->dashboard->campaigns->name ?></th>
                    <th class="d-none d-md-table-cell"><?= $this->language->dashboard->campaigns->date ?></th>
                    <th><?= $this->language->dashboard->campaigns->is_enabled ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="tbody_campaign">

                <?php foreach($data->campaigns as $row): ?>
                    <?php
                    $row->branding = json_decode($row->branding);

                    $icon = new \Jdenticon\Identicon([
                        'value' => $row->domain,
                        'size' => 50,
                        'style' => [
                            'hues' => [235],
                            'backgroundColor' => '#86444400',
                            'colorLightness' => [0.41, 0.80],
                            'grayscaleLightness' => [0.30, 0.70],
                            'colorSaturation' => 0.85,
                            'grayscaleSaturation' => 0.40,
                        ]
                    ]);
                    $row->icon = $icon->getImageDataUri();

                    ?>
                    <tr>
                        <td class="clickable" data-href="<?= url('campaign/' . $row->campaign_id) ?>">
                            <div class="d-flex">
                                <img src="<?= $row->icon ?>" class="campaign-avatar rounded-circle mr-3" alt="" />

                                <div class="d-flex flex-column">
                                    <?= $row->name ?>

                                    <span class="text-muted">
                                        <?= $row->domain ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="clickable d-none d-md-table-cell text-muted" data-href="<?= url('campaign/' . $row->campaign_id) ?>"><span><?= \Altum\Date::get($row->date, 2) ?></span></td>
                        <td>
                            <div class="d-flex">
                                <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= $this->language->dashboard->campaigns->is_enabled_tooltip ?>">
                                    <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="campaign_is_enabled_<?= $row->campaign_id ?>"
                                            data-row-id="<?= $row->campaign_id ?>"
                                            onchange="ajax_call_helper(event, 'campaigns-ajax', 'is_enabled_toggle')"
                                        <?= $row->is_enabled ? 'checked="checked"' : null ?>
                                    >
                                    <label class="custom-control-label clickable" for="campaign_is_enabled_<?= $row->campaign_id ?>"></label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                                    <i class="fa fa-ellipsis-v"></i>

                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" data-toggle="modal" data-target="#update_campaign" data-campaign-id="<?= $row->campaign_id ?>" data-name="<?= $row->name ?>" data-domain="<?= $row->domain ?>" data-include-subdomains="<?= (bool) $row->include_subdomains ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-1"></i> <?= $this->language->global->edit ?></a>

                                        <a
                                            href="#"
                                            data-toggle="modal"
                                            data-target="#campaign_pixel_key"
                                            data-pixel-key="<?= $row->pixel_key ?>"
                                            data-campaign-id="<?= $row->campaign_id ?>"
                                            class="dropdown-item"
                                        ><i class="fa fa-fw fa-sm fa-code mr-1"></i> <?= $this->language->campaign->header->pixel_key ?></a>

                                        <?php if($this->user->plan_settings->custom_branding): ?>
                                            <a href="#" data-toggle="modal" data-target="#custom_branding_campaign" data-campaign-id="<?= $row->campaign_id ?>" data-branding-name="<?= $row->branding->name ?? '' ?>" data-branding-url="<?= $row->branding->url ?? '' ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-random mr-1"></i> <?= $this->language->campaign->header->custom_branding ?></a>
                                        <?php endif ?>

                                        <a href="#" class="dropdown-item" data-delete="<?= $this->language->global->info_message->confirm_delete ?>" data-row-id="<?= $row->campaign_id ?>"><i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= $this->language->global->delete ?></a>
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
            <p><?= $this->language->dashboard->campaigns->no_data ?></a></p>
        </div>

    <?php endif ?>

</section>

<?php ob_start() ?>
<script>
    $('[data-delete]').on('click', event => {
        let message = $(event.currentTarget).attr('data-delete');

        if(!confirm(message)) return false;

        /* Continue with the deletion */
        ajax_call_helper(event, 'campaigns-ajax', 'delete', () => {

            /* On success delete the actual row from the DOM */
            $(event.currentTarget).closest('tr').remove();

        });

        event.preventDefault();
    });

    <?php if(isset($_GET['pixel_key_modal'])): ?>

    /* Open the pixel key modal */
    $('[data-campaign-id="<?= (int) $_GET['pixel_key_modal'] ?>"][data-pixel-key]').trigger('click');

    <?php endif ?>

</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
