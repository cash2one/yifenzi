<label>文章列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Article.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Article.Admin" id="ArticleAdmin">
<label for="ArticleAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Article.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Article.Create" id="ArticleCreate">
<label for="ArticleCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Article.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Article.Update" id="ArticleUpdate">
<label for="ArticleUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Article.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Article.Delete" id="ArticleDelete">
<label for="ArticleDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>文章分类列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('ArticleCategory.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="ArticleCategory.Admin" id="ArticleCategoryAdmin">
<label for="ArticleCategoryAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('ArticleCategory.Create', $rights)): ?>checked="checked"<?php endif; ?> value="ArticleCategory.Create" id="ArticleCategoryCreate">
<label for="ArticleCategoryCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('ArticleCategory.Update', $rights)): ?>checked="checked"<?php endif; ?> value="ArticleCategory.Update" id="ArticleCategoryUpdate">
<label for="ArticleCategoryUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('ArticleCategory.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="ArticleCategory.Delete" id="ArticleCategoryDelete">
<label for="ArticleCategoryDelete">删除</label>
)