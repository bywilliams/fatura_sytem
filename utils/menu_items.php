<ul class="list-unstyled components">

    <!-- Menu de Opçoes Admin -->
    <?php if ($userData->levels_access_id == 1) : ?>

        <li>
            <a href="<?= $BASE_URL ?>admin_central.php" target="myFrame"><i class="fa-solid fa-lock"></i> Admin</a>
        </li>

    <?php endif ?>
    <!-- Menu de Opçoes e ferramentas -->

    <?php foreach ($menus as $menu) : ?>
        <?php if ($menu->sub_menu == "") : ?>

            <li>
                <a href="<?= $BASE_URL . $menu->url; ?>" target="myFrame" class="<?= $menu->id_menu == 1 ? 'active_item' : '' ?>">
                    <i class="<?= $menu->class_icon ?>"></i>
                    <?= $menu->menu_name ?>
                </a>
            </li>

        <?php else : ?>

            <li>
                <a href="<?= $menu->url; ?>" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="<?= $menu->class_icon ?>"></i>
                    <?= $menu->menu_name ?>
                </a>
                <ul class="collapse list-unstyled" id="<?= substr($menu->url, 1); ?>">
                    <?php $subMenus = $menu_Dao->findSubMenu($menu->id_menu); ?>
                    <?php foreach ($subMenus as $subMenu) : ?>


                        <li>
                            <a href="<?= $BASE_URL ?><?= $subMenu->url_submenu; ?>" target="myFrame">
                                <i class="<?= $subMenu->class_icon_submenu ?>"></i>
                                <?= $subMenu->submenu_name; ?>
                            </a>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>

</ul>