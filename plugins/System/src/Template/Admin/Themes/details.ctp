<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since    2.0.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://www.quickappscms.org
 * @license  http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
?>

<div class="row">
    <div class="col-md-5">
        <div class="thumbnail">
            <?=
                $this->Html->image([
                    'plugin' => 'System',
                    'controller' => 'themes',
                    'action' => 'screenshot',
                    $theme->name,
                ], [
                    'style' => 'width:100%;'
                ]);
            ?>
        </div>
    </div>

    <div class="col-md-7">
        <h1>
            <?= $theme->humanName; ?>
            <small>(<?= $theme->version(); ?>)</small>
        </h1>
        <em><?= $theme->composer['description'] ? $theme->composer['description'] : ''; ?></em>

        <div class="extended-info">
            <?= $this->element('System.composer_details', ['composer' => $theme->composer]); ?>
        </div>
    </div>
</div>