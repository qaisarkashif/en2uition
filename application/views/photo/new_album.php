<div class="main">
    <div class="col-xs-12 col-sm-12">
        <form role="form" class="form-horizontal" id="frm-new-album" method="post" action="/photo/save_album" autocomplete="off" onsubmit="return false;">
            <fieldset>
                <legend>Create a new album:</legend>
                <div class="form-group">
                    <label for="album-title" class="col-sm-2 control-label">Title: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="album-title" name="album-title" placeholder="Album title..." maxlength="150" value="<?=$album_title?>"/>
                        <input type="hidden" class="form-control" id="album-id" name="album-id" value="<?=$album_id?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Photo(s): </label>
                    <div class="col-sm-10">
                        <span class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Add files...</span>
                            <input id="fileupload" type="file" name="files[]" multiple/>
                        </span>
                        <br>
                        <br>
                        <div id="progress" class="progress">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>
                        <table class="files" border="0">
                            <tbody id="files"></tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label sr-only">Save: </label>
                    <div class="col-sm-10">
                        <input type="button" class="btn btn-default" value="Save" id="btn-save-album"/>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="clear"></div>
</div>