<?php
defined( 'ABSPATH' ) || exit;

require_once LXPG_DIR . 'includes/libs/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p7\PucFactory;

/**
 * Checks the public GitHub repo for new tags and feeds them to WordPress'
 * native "update available" UI on the Plugins screen.
 */
class LXPG_Updater {

    private const REPO_URL = 'https://github.com/lifexmarketing/lifex-project-gallery';

    public function init(): void {
        // The repo has no tagged releases yet, so track `main` directly —
        // WordPress compares against the Version header in this file on
        // that branch. Switch to tagged releases later without code
        // changes; PUC prefers tags automatically once they exist.
        $update_checker = PucFactory::buildUpdateChecker(
            self::REPO_URL,
            LXPG_FILE,
            'lifex-project-gallery'
        );

        $update_checker->setBranch( 'main' );
    }
}
