<?php echo $header; ?><?php echo $column_left; ?>
    <div id="content">
        <div class="page-header">
            <div class="container-fluid">
                <div class="pull-right">
                    <a href="<?php echo $clear; ?>" data-toggle="tooltip" title="<?php echo $cache_del; ?>" class="btn btn-warning"><i class="fa fa-eraser"></i></a>
                    <button type="submit" form="form-featured" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
                </div>
                <h1><?php echo $heading_title; ?></h1>
                <ul class="breadcrumb">
                    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="container-fluid">
            <?php if ($success) { ?>
                <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
                </div>
                <div class="panel-body">
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-featured" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
                            <div class="col-sm-10">
                                <?php if ($minify_status) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_status" value="1" checked="checked"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_status" value="0"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } else { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_status" value="1"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_status" value="0" checked="checked"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-time"><?php echo $text_time; ?></label>
                            <div class="col-sm-2">
                                <input type="number" style="display:inline-block;width:80%;" name="minify_time" value="<?php echo $minify_time; ?>" class="form-control" id="input-time"> <?php echo $text_time_amount; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $text_css; ?></label>
                            <div class="col-sm-10">
                                <?php if ($minify_css) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_css" value="1" checked="checked"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_css" value="0"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } else { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_css" value="1"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_css" value="0" checked="checked"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $text_js; ?></label>
                            <div class="col-sm-10">
                                <?php if ($minify_js) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_js" value="1" checked="checked"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_js" value="0"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } else { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_js" value="1"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_js" value="0" checked="checked"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $text_gzip; ?></label>
                            <div class="col-sm-10">
                                <?php if ($minify_gzip) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_gzip" value="1" checked="checked"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_gzip" value="0"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } else { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_gzip" value="1"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_gzip" value="0" checked="checked"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $text_html; ?></label>
                            <div class="col-sm-10">
                                <?php if ($minify_html) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_html" value="1" checked="checked"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_html" value="0"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } else { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_html" value="1"> <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="minify_html" value="0" checked="checked"> <?php echo $text_disabled; ?>
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $text_async; ?></label>
                            <div class="col-sm-10">
                                <?php if ($minify_async) { ?>
                                    <label class="radio-inline">
                                        <input type="radio" name="minify_async" value="1" checked="checked"> <?php echo $text_enabled; ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="minify_async" value="0"> <?php echo $text_disabled; ?>
                                    </label>
                                <?php } else { ?>
                                    <label class="radio-inline">
                                        <input type="radio" name="minify_async" value="1"> <?php echo $text_enabled; ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="minify_async" value="0" checked="checked"> <?php echo $text_disabled; ?>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- Minify by https://github.com/milkamil93 -->
<?php echo $footer; ?>