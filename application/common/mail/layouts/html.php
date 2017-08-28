<?php
use Sil\PhpEnv\Env;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php
    $brandColor = Env::get('EMAIL_BRAND_COLOR', '');
    $logo = Env::get('EMAIL_BRAND_LOGO', '');
    ?>
    <header>
        <table style="background-color: <?= $brandColor ?>; width: 100%">
            <tr>
                <td>
                    <img src="<?= $logo ?>" style="max-height: 4em; vertical-align: middle">
                </td>
            </tr>
        </table>
    </header>

    <main>
        <?php
        /* @var $content string email contents */
        ?>
        <?= $content ?>
    </main>
</body>
</html>
