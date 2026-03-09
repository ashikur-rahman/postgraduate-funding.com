<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Postgraduate Funding</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Postgraduate Funding Guide for UK University Students — Scholarships, Grants and Charity Funds.">

    <link rel="icon" href="<?= base_url('public/site_template_asset/images/favicon.ico'); ?>">
    <link rel="stylesheet" href="<?= base_url('public/site_template_asset/css/main.min.css'); ?>">
</head>

<body>

<header>
    <div class="container">
        <a href="<?= base_url(); ?>">
            <h2>The Alternative Guide to</h2>
            <h1>Postgraduate Funding</h1>
        </a>

        <?php if (session()->get('role') === "editor"): ?>
            <?php if (session()->get('user_type') === "Guest"): ?>
                <p>Hello, Guest</p>
            <?php else: ?>
                <p>Hello, <?= session()->get('name'); ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p>Online</p>
        <?php endif; ?>

        <form action="<?= base_url('site/search_keyword'); ?>" method="post">
            <?= csrf_field(); ?>
            <input type="text" name="key_word" placeholder="Search funding database">
            <button type="submit">Search</button>
        </form>
    </div>
</header>


<nav>

<?php
function buildMenuTree(array $data, $parent = 0)
{
    $branch = [];

    foreach ($data as $row) {
        if ($row['parent'] == $parent) {
            $row['children'] = buildMenuTree($data, $row['id']);
            $branch[] = $row;
        }
    }

    return $branch;
}

$menuTreeArray = buildMenuTree($menus);


function renderMenu($menuTreeArray, $parentId = 0)
{
    $html = '';

    foreach ($menuTreeArray as $menu) {
        if ($menu['parent'] == $parentId && $menu['is_hidden'] != 1) {

            $html .= "<li>";
            $html .= "<a href='" . base_url($menu['title_alias']) . "'>";
            $html .= $menu['label'];
            $html .= "</a>";

            if (!empty($menu['children'])) {
                $html .= "<ul>";
                $html .= renderMenu($menu['children'], $menu['id']);
                $html .= "</ul>";
            }

            $html .= "</li>";
        }
    }

    return $html;
}
?>

    <ul>
        <?= renderMenu($menuTreeArray); ?>

        <?php if (session()->get('role') === "editor"): ?>
            <li>
                <a href="<?= base_url('logout'); ?>">Logout</a>
            </li>
        <?php endif; ?>
    </ul>

</nav>


<main>
    <?= $this->renderSection('content'); ?>
</main>


<footer>

    <div class="container">

        <section>
            <h3>Contact</h3>
            <p>London, United Kingdom</p>
            <p>contact@XXXX-domain.com</p>
        </section>

        <section>
            <h3>Connect With Us</h3>

            <ul>
                <li>
                    <a href="https://twitter.com/XXXX" target="_blank">Twitter</a>
                </li>

                <li>
                    <a href="https://facebook.com/XXXX" target="_blank">Facebook</a>
                </li>

                <li>
                    <a href="https://youtube.com/XXXX" target="_blank">YouTube</a>
                </li>
            </ul>

        </section>

    </div>


    <div>
        <p>
            Copyright © <?= date('Y'); ?> Postgraduate Funding Guide
        </p>
    </div>

</footer>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= base_url('public/site_template_asset/js/bootstrap.min.js'); ?>"></script>

<?= $this->renderSection('scripts'); ?>

</body>
</html>