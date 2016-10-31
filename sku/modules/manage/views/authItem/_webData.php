<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.WebData', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.WebData" id="MainHome"><label for="MainWebData">网站数据管理</label>
    </td>
    <td>
        <label>多语言管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Home.LanguageBackend', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Home.LanguageBackend" id="HomeLanguageBackend">
        <label for="HomeLanguageBackend">多语言-后台</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Home.LanguagePartner', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Home.LanguagePartner" id="HomeLanguagePartner">
        <label for="HomeLanguageFrontend"> 多语言-商户</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Home.LanguageApi', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Home.LanguageApi" id="HomeLanguageApi">
        <label for="HomeLanguageApi">多语言-API</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Home.LanguageSku', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Home.LanguageSku" id="HomeLanguageSku">
        <label for="HomeLanguageApi">多语言-API</label>

        )


    </td>
</tr>
