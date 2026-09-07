<?php
if (isset($_SESSION['user_id'])) {
    $menu = $_POST['menu'] ?? null;
    if ($menu == "category") {
        if ($_SESSION['user_role'] == "1") {
            _selectNoParam(
                $stCa,
                $coCA,
                "SELECT id, name, tai, parent_id, tuluv, slug FROM category WHERE parent_id = '0'",
                $cid,
                $cname,
                $ctai,
                $cparent_id,
                $ctuluv,
                $slug
            ); ?>
            <div class="p-3 border" style="height: 350px;overflow: overlay;" id="metahtml">
                <?php
                while (_fetch($stCa)) { ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="<?= $cid ?>" name="cate_id[]" id="cate<?= $cid ?>">
                        <label class="form-check-label" for="cate<?= $cid ?>">
                            <?= $cname ?>
                        </label>
                    </div>
                <?php echo select_subcatecheck(0, $cid);
                } ?>
            </div>
        <?php } else {
            _selectNoParam(
                $stCa,
                $coCA,
                "SELECT category.id, category.name, category.tai, category.parent_id, category.tuluv, category.slug FROM category INNER JOIN admintax ON category.id = admintax.category_id WHERE admintax.userid = " . $_SESSION['user_id'],
                $cid,
                $cname,
                $ctai,
                $cparent_id,
                $ctuluv,
                $slug
            ); ?>
            <div class="p-3 border" style="height: 350px;overflow: overlay;" id="metahtml">
                <?php
                while (_fetch($stCa)) { ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="<?= $cid ?>" name="cate_id[]" id="cate<?= $cid ?>">
                        <label class="form-check-label" for="cate<?= $cid ?>">
                            <?= $cname ?>
                        </label>
                    </div>
                <?php
                } ?>
            </div>
        <?php }
    } else if ($menu == "link") { ?>
        <input type="text" class="form-control" id="metahtml" name="meta" placeholder="Цаг үеийн мэдээ" required>
        <label for="meta">Холбоос /URL/</label>
    <?php } else if ($menu == "post") {
        _selectNoParam(
            $stCa,
            $coCA,
            "SELECT id, name, ognoo FROM posts WHERE turul = 'post' ORDER BY ognoo DESC",
            $cid,
            $cname,
            $ognoo
        ); ?>
        <div>
            <input type="text" class="form-control" id="searcher" onkeyup="myFunction()" placeholder="Нийтлэлийн нэрээр хайх">
        </div>
        <div class="p-3 border" style="height: 350px;overflow: overlay;" id="metahtml">
            <?php
            while (_fetch($stCa)) { ?>
                <div class="form-check postlist">
                    <input class="form-check-input" type="radio" value="<?= $cid ?>" name="meta" id="meta<?= $cid ?>">
                    <label class="form-check-label" for="meta<?= $cid ?>">
                        <?= $cname ?>
                    </label>
                    <small>(<?= _format_date($ognoo) ?>)</small>
                </div>
            <?php
            } ?>
        </div>
    <?php } else if ($menu == "page") {
        _selectNoParam(
            $stCa,
            $coCA,
            "SELECT id, name, ognoo FROM posts WHERE turul = 'page' ORDER BY ognoo DESC",
            $cid,
            $cname,
            $ognoo
        ); ?>
        <div class="p-3 border" style="height: 350px;overflow: overlay;" id="metahtml">
            <?php
            while (_fetch($stCa)) { ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="<?= $cid ?>" name="meta" id="meta<?= $cid ?>">
                    <label class="form-check-label" for="meta<?= $cid ?>">
                        <?= $cname ?> (<?= $ognoo ?>)
                    </label>
                </div>
            <?php
            } ?>
        </div>
<?php }
}
