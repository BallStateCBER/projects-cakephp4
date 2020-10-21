<?php
/**
 * @var array $filesAlphabetic
 * @var array $filesNewest
 */
?>

<div class="list-reports-buttons">
    <span class="d-inline-block align-middle">Sort:</span>
    <div class="btn-group btn-group-toggle sorting-options" data-toggle="buttons">
        <button class="newest active btn btn-sm btn-outline-secondary">Newest</button>
        <button class="alphabetic btn btn-sm btn-outline-secondary">Alphabetic</button>
    </div>
    <button class="refresh btn btn-sm btn-secondary">
        <i class="fas fa-spinner fa-spin loading" style="display: none;"></i>
        Refresh
    </button>
    <button class="reports-cancel btn btn-sm btn-secondary">
        Cancel
    </button>
</div>

<?php if ($filesNewest): ?>
    <p class="select-report-instructions">
        Select a report to link this graphic to:
    </p>
    <ul class="unstyled newest select-report">
        <?php foreach ($filesNewest as $timestamp => $info): ?>
            <li>
                <button class="btn btn-link report">
                    <?= $info['filename'] ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>
    <ul class="unstyled alphabetic select-report" style="display: none;">
        <?php foreach ($filesAlphabetic as $filename => $info): ?>
            <li>
                <button class="btn btn-link report">
                    <?= $info['filename'] ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    No reports have been uploaded.
<?php endif; ?>
