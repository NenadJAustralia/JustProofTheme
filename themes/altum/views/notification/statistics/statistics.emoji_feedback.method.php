<?php
defined('ALTUMCODE') || die();

/* Create the content for each tab */
$html = [];

/* Extra Javascript needed */
$javascript = '';
?>

<?php /* Feedback Chart */ ?>
<?php ob_start() ?>
<div class="chart-container mb-5">
    <canvas id="clicks_chart"></canvas>
</div>
<?php $html['charts'] = ob_get_clean() ?>


<?php

ob_start();

/* Logs for the charts */
$result = $this->database->query("
    SELECT
         `type`,
         COUNT(`id`) AS `total`
    FROM
         `track_notifications`
    WHERE
        `notification_id` = {$data->notification->notification_id}
        AND (`date` BETWEEN '{$data->date->start_date_query}' AND '{$data->date->end_date_query}')
        AND `type` LIKE 'feedback_emoji_%'
    GROUP BY
        `type`
    ORDER BY
        `total` DESC
");

?>

<h2 class="h3 mt-5"><?= $this->language->notification->statistics->header_feedback ?></h2>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= $this->language->notification->statistics->feedback ?></th>
            <th><?= $this->language->notification->statistics->feedback_total ?></th>
        </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_object()): ?>
            <tr>
                <td>
                    <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/emojis/' . str_replace('feedback_emoji_', '', $row->type) . '.svg' ?>" class="altumcode-emoji-feedback-emoji" data-type="angry" /> <?= $this->language->notification->emoji_feedback->{$row->type} ?>
                </td>
                <td><?= nr($row->total) ?></td>
            </tr>
        <?php endwhile ?>
        </tbody>
    </table>
</div>

<?php $html['feedback'] = ob_get_clean() ?>


<?php ob_start() ?>
<script>
    let clicks_chart = document.getElementById('clicks_chart').getContext('2d');

    new Chart(clicks_chart, {
        type: 'line',
        data: {
            labels: <?= $data->logs_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode($this->language->notification->emoji_feedback->feedback_emoji_angry) ?>,
                    data: <?= $data->logs_chart['feedback_emoji_angry'] ?? '[]' ?>,
                    borderColor: '#ED4956',
                    fill: false
                },
                {
                    label: <?= json_encode($this->language->notification->emoji_feedback->feedback_emoji_sad) ?>,
                    data: <?= $data->logs_chart['feedback_emoji_sad'] ?? '[]' ?>,
                    borderColor: '#ed804c',
                    fill: false
                },
                {
                    label: <?= json_encode($this->language->notification->emoji_feedback->feedback_emoji_neutral) ?>,
                    data: <?= $data->logs_chart['feedback_emoji_neutral'] ?? '[]' ?>,
                    borderColor: '#8f8f8f',
                    fill: false
                },
                {
                    label: <?= json_encode($this->language->notification->emoji_feedback->feedback_emoji_happy) ?>,
                    data: <?= $data->logs_chart['feedback_emoji_happy'] ?? '[]' ?>,
                    borderColor: '#6c94ed',
                    fill: false
                },
                {
                    label: <?= json_encode($this->language->notification->emoji_feedback->feedback_emoji_excited) ?>,
                    data: <?= $data->logs_chart['feedback_emoji_excited'] ?? '[]' ?>,
                    borderColor: '#4aed92',
                    fill: false
                }
            ]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                        return `${nr(value)} ${data.datasets[tooltipItem.datasetIndex].label}`;
                    }
                }
            },
            title: {
                display: true,
                text: <?= json_encode($this->language->notification->statistics->feedback_chart) ?>
            },
            legend: {
                display: true
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        userCallback: (value, index, values) => {
                            if (Math.floor(value) === value) {
                                return nr(value);
                            }
                        }
                    },
                    min: 0
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        }
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
