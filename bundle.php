<div id="Chat" class="chat">
    <div class="chat__toggler">
        <span class="chat__toggler-message">Open chat</span>
        <span class="chat__toggler-button"><i class="fa fa-times"></i></span>
    </div>
    <div class="chat__messenger">
        <ul class="chat__massages"></ul>
        <div class="chat__form">
            <form method="post">
                <input class="chat__input"
                       name='message'
                       autocomplete="off"
                       placeholder="Send message">
                <button class="chat__submit"><i class="fa fa-arrow-circle-right"></i></button>
            </form>
        </div>
    </div>
</div>

<!--Edit Main Content Area here-->
<div class="contentArea">
    <div class="divPanel notop page-content">
        <div class="row-fluid">
            <div class="span12" id="divMain">
                <!--Data Tables Start within DivMain-->

                <div class="custom-header-footer well">
                    <div class="well-left">
                        <ol class="breadcrumb" style="background-color: transparent">
                            <li><a href="<?= APP_URL ?>partner/dashboard">My Loans</a></li>
                            <li><b title="Loan Number"><?= $loanNumber ?></b>: <?= $bundledesc ?> </li>
                        </ol>
                    </div>
                    <div class="well-center">
                        <?= $user ?>
                        <small class="text-muted">logged as</small>
                        <span style="white-space: nowrap"><?= $position ?> (<?= $primary_lender ?>)</span>
                        <br/>
                    </div>
                    <div class="well-right">
                        <small> Borrower: <?= $owner ?></small>
                    </div>
                </div>
                <ul class="nav custom-header-footer nav-tabs" role="tablist" style="margin-bottom: 10px;">
                    <li class="active">
                        <a href="#tab-table3" onclick="initTable('files');" data-toggle="tab">
                            Borrower/Co-Borrower:Docs
                        </a>
                    </li>
                    <li>
                        <a href="#tab-table1" onclick="initTable('tasks');" data-toggle="tab">
                            Borrower/Co-Borrower: Tasks & Docs
                        </a>
                    </li>
                    <li>
                        <a href="#tab-table2" onclick="initTable('agent');" data-toggle="tab">
                            Lending Team: Docs
                        </a>
                    </li>
                    <li>
                        <a href="#tab-table5" onclick="initTable('share');" data-toggle="tab">
                            Loan Participants
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab-table1">
                        <table id="Tasks" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Group Task</th>
                                <th title="Required Documents">Required</th>
                                <th title="Uploaded Documents">Uploaded</th>
                                <th>Task Status</th>
                                <th>Owner</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>Group Task</th>
                                <th>Required Docs</th>
                                <th>Uploaded Docs</th>
                                <th>Task Status</th>
                                <th>Owner</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tab-pane" id="tab-table2">
                        <table id="Agentfiles" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>File</th>
                                <th>Uploaded By</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>File</th>
                                <th>Uploaded By</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tab-pane active" id="tab-table3">
                        <div id="Files"></div>
                    </div>

                    <div class="tab-pane" id="tab-table5">
                        <div id="Sharing"></div>
                    </div>
                </div><!--Tab Content -->
            </div>   <!--Data Tables End within DivMain-->
        </div>   <!--End DivClass Row Fluid-->
    </div>
</div>


<script type="text/javascript" class="init">

    var ChatTimer;
    var ChatMessages;

    function ApprovalState(val) {
        switch (val) {
            case '1': return 'Approved';
            case '2': return 'Declined';
            default: return 'State reset';
        }
    }

    function formatTasks(files) {
        var filesStr = '<div class="filesContainer">';
        if (files.length > 0) {
            var btnOpts;
            for (var i = 0; i < files.length; i++) {
                btnOpts =
                    ' type="button"' +
                    ' class="btn btn-default btn-file btn-xs ' +
                    ' data-name="' + files[i]['name'] + '"' +
                    ' data-fileid="' + files[i]['DT_RowID'] + '"' +
                    ' data-ownertype="' + files[i]['owner_type'] + '"' +
                    ' data-accountid="' + files[i]['account_id'] + '"' +
                    ' data-link="' + files[i]['link'] + '"' +
                    ' data-taskid="' + files[i]['task_id'] + '"' +
                    '';
                var status = '';
                if (files[i]['sync_status'] == 0 && files[i]['delete_status'] == 0) {
                    status = '<div class="filesContainer__link filesContainer__link--default"><i class="fa fa-chain-broken"></i>&nbsp;Not uploaded to LQB</div>';
                }else if (files[i]['sync_status'] == 1 && files[i]['delete_status'] != 1) {
                    status = '<div class="filesContainer__link filesContainer__link--success"><i class="fa fa-chain"></i>&nbsp;Uploaded to LQB</div>';
                }else if (files[i]['delete_status'] == 1) {
                    status = '<div class="filesContainer__link filesContainer__link--falied"><i class="fa fa-chain-broken"></i>&nbsp;Deleted from LQB</div>';
                }else if (files[i]['approval_state'] == '2' && files[i]['delete_status'] == 3) {
                    status = '<div class="filesContainer__link filesContainer__link--falied"><i class="fa fa-chain-broken"></i>&nbsp;Rejected</div>';
                }


                filesStr += '<div class="filesContainer__item ' +
                    ((files[i]['approval_state'] == '0') ? "filesContainer--initial " : "") +
                    ((files[i]['is_new'] == '1') ? "filesContainer--new " : "") + '">' +
                    '<span class="filesContainer__header " title="'+ files[i]['name'] + ((files[i]['is_new'] == '1') ? " new" : "")+'">' +
                    '<i class="filesContainer__status glyphicon ' +
                    ((files[i]['approval_state'] == '1') ? "glyphicon-thumbs-up" :
                        ((files[i]['approval_state'] == '2') ? "glyphicon-thumbs-down" : "")) + '"></i>&nbsp;' +
                    '' + files[i]['name'] +
                    ((files[i]['is_new'] == '1') ? " <span>new</span>" : "") + '</span>' +
                    '<div class="filesContainer__date"><i class="fa fa-calendar-o"></i>&nbsp;Uploaded at ' + moment(files[i]['date']).format('ll') + '</div>' +
                    '<div class="filesContainer__date"><i class="fa fa-calendar-o"></i>&nbsp;' + ApprovalState(files[i]['approval_state']) + (moment(files[i]['approval_status_date']).isValid() ? (' at ' + moment(files[i]['approval_status_date']).format('ll')) : '') + '</div>' +
                    '<div class="filesContainer__owner"><i class="fa"></i>&nbsp;' + files[i]['bucket_name'] + '</div>' +
                    '<div class="filesContainer__owner" title="'+ files[i]['approval_comment'] +'"><i class="fa"></i>&nbsp;' + files[i]['approval_comment'] + '</div>' +
                    status +
                    '<div class="filesContainer__controls">' +
                    '<button ' + btnOpts + ' onclick="taskFileApprove(this)">Approve</button> ' +
                    '<button ' + btnOpts + ' onclick="taskFileDecline(this)">Decline</button> ' +
                    '<button ' + btnOpts + ' onclick="taskFileOpen(this)">Open</button>' +
                    '</div>' +
                    '</div>';
            }
        } else {
            filesStr += '<div class="filesContainer__item filesContainer__item--empty text-muted">No files available</div>';
        }
        filesStr += '' +
            '</div>';
        return filesStr;
    }

    function formatFiles(files) {
        var f = files;
        var filesStr = '<div class="filesContainer"> <div class="dt-buttons-left-none "> <a onClick="initFilesTable();" class="dt-button buttons-excel buttons-html5" tabindex="0"> <span> Refresh</span></a></div>';
        if (f.length > 0) {
            var btnOpts;
            for (var i = 0; i < f.length; i++) {
                btnOpts =
                    ' type="button"' +
                    ' class="btn btn-default btn-file btn-xs" ' +
                    ' data-name="' + f[i]['name'] + '"' +
                    ' data-fileid="' + f[i]['DT_RowID'] + '"' +
                    ' data-ownertype="' + f[i]['owner_type'] + '"' +
                    ' data-accountid="' + f[i]['account_id'] + '"' +
                    ' data-link="' + f[i]['link'] + '"' +
                    ' data-taskid="' + f[i]['task_id'] + '"' +
                    '';

                var status = '';
                if (f[i]['sync_status'] == 0 && f[i]['delete_status'] == 0) {
                    status = '<div class="filesContainer__link filesContainer__link--default"><i class="fa fa-chain-broken"></i>&nbsp;Not uploaded to LQB</div>';
                }else if (f[i]['sync_status'] == 1 && f[i]['delete_status'] != 1) {
                    status = '<div class="filesContainer__link filesContainer__link--success"><i class="fa fa-chain"></i>&nbsp;Uploaded to LQB</div>';
                }else if (f[i]['delete_status'] == 1) {
                    status = '<div class="filesContainer__link filesContainer__link--falied"><i class="fa fa-chain-broken"></i>&nbsp;Deleted from LQB</div>';
                }else if (f[i]['approval_state'] == '2' && f[i]['delete_status'] == 3) {
                    status = '<div class="filesContainer__link filesContainer__link--falied"><i class="fa fa-chain-broken"></i>&nbsp;Rejected</div>';
                }

                filesStr += '<div class="filesContainer__item ' +
                    ((f[i]['approval_state'] == '0') ? "filesContainer--initial " : "") +
                    ((f[i]['is_new'] == '1') ? "filesContainer--new" : "") + '">' +
                    '<span class="filesContainer__header " title="'+ f[i]['name'] + ((f[i]['is_new'] == '1') ? " new" : "")+'">' +
                    '<i class="filesContainer__status glyphicon ' +
                    ((f[i]['approval_state'] == '1') ? "glyphicon-thumbs-up" :
                        ((f[i]['approval_state'] == '2') ? "glyphicon-thumbs-down" : "")) + '"></i>&nbsp;' +
                    f[i]['name'] +
                    ((f[i]['is_new'] == '1') ? " <span>new</span>" : "") + '</span>' +
                    '<div class="filesContainer__text" title="' + f[i]['group'] + ' / ' + f[i]['task'] + '"><b>' +
                    f[i]['group'] + '</b>' + f[i]['task'] + '</div>' +
                    '<div class="filesContainer__owner"><i class="fa fa-user"></i>&nbsp;' + f[i]['user'] + '</div>' +
                    '<div class="filesContainer__date"><i class="fa fa-calendar-o"></i>&nbsp;Uploaded at ' + moment(f[i]['date']).format('ll') + '</div>' +
                    '<div class="filesContainer__date"><i class="fa fa-calendar-o"></i>&nbsp;' + ApprovalState(f[i]['approval_state']) + (moment(f[i]['approval_status_date']).isValid() ? (' at ' + moment(f[i]['approval_status_date']).format('ll')) : '') + '</div>' +
                    '<div class="filesContainer__owner"><i class="fa"></i>&nbsp;' + f[i]['bucket_name'] + '</div>' +
                    '<div class="filesContainer__owner" title="'+ f[i]['approval_comment'] +'"><i class="fa"></i>&nbsp;' + f[i]['approval_comment'] + '</div>' +
                    status +
                    '<div class="filesContainer__controls">' +
                    '<button ' + btnOpts + ' onclick="fileApprove(this)">Approve</button> ' +
                    '<button ' + btnOpts + ' onclick="fileDecline(this)">Decline</button> ' +
                    '<button ' + btnOpts + ' onclick="fileOpen(this)">Open</button>' +
                    '</div>' +
                    '</div>';
            }
        } else {
            filesStr += '<div class="filesContainer__item filesContainer__item--empty text-muted">No files available</div>';
        }
        filesStr += '</div>';
        return filesStr;
    }

    function taskFileApprove(element) {

        var filesTable = $('#Tasks').dataTable();
        var DT = $('#Tasks').DataTable();
        var fileData = $(element).data();
        var filesTableRow = filesTable.fnGetData($('tr#' + fileData['taskid']));
        var index;
        var data = {
            access_token: '',
            userType: fileData['ownertype']
        };
        for (var i = 0; i < filesTableRow.files.length; i++) {
            if (fileData['fileid'] == filesTableRow.files[i].DT_RowID) {
                index = i;
            }
        }
        if (typeof index !== 'undefined') {
            filesTableRow.files[index].approval_state = '1';
            filesTableRow.files[index].is_new = '0';
        }

        var editor = new $.fn.dataTable.Editor({
            ajax: function (method, url, d, callback, err) {
                var params = {
                    access_token: '<?php echo Session::instance()->id();?>',
                    bundle_id: <?php echo $Id; ?>,
                    files: [{
                        account_id: fileData['accountid'],
                        task_id: fileData['taskid'],
                        id: fileData['fileid'],
                        bucket: $('#DTE_Field_bucket_id').val(),
                        comment: $('#DTE_Field_comment').val(),
                        is_viewed: 1,
                        approval_status: 1
                    }]
                };
                if (d.action === 'edit') {
                    $('.DTE_Form_Buttons:visible button').prop('disabled', true);
                    $.ajax({
                        type: 'PUT',
                        url: "<?php echo APP_URL ?>restapi/bundle/update_files_statuses?" + $.param(params),
                        dataType: "json",
                        success: function (json) {
                            $('.DTE_Processing_Indicator:visible').hide();
                            if (json.success) {
                                DT.ajax.reload(null, false);
                                initFilesTable();
                                editor.close();
                            } else {
                                $('.DTE_Form_Buttons:visible button').prop('disabled', false);
                                editor.field('bucket_id').error(json.message)
                            }
                        },
                        error: function (xhr, error, thrown) {
                            err(xhr, error, thrown);
                        }
                    });
                }
            },
            fields: [
                {
                    label: "Bucket:",
                    name: "bucket_id",
                    type: "select",
                    className: 'block'
                },
                {
                    label: "Comment:",
                    name: "comment",
                    type: "textarea",
                    'className': 'block'
                }
            ]
        });

        editor.on('submitComplete', editorErrorHandler);
        editor.on('onOpen', function () {
            var $select = $('#DTE_Field_bucket_id').select2({width: '100%'});
            var $request = $.ajax({
                url: "<?=APP_URL ?>restapi/bundle/bucket?" +
                "access_token=<?=Session::instance()->id();?>" +
                "&userType=" + fileData['ownertype']
            });

            $request.then(function (data) {
                for (var d = 0; d < data.data.length; d++) {
                    var item = data.data[d];
                    var option = new Option(item.name, item.id, true, true);
                    $select.append(option);
                }
                $select.trigger('change');
            });
        });
        editor
            .title('Approve file')
            .buttons([{
                label: "Approve", fn: function () {
                    $(this).submit();
                }
            }, {
                label: 'Cancel', fn: function () {
                    this.close();
                }
            }]).edit();

    }

    function fileApprove(element) {

        var fileData = $(element).data();
        var index;
        var data = {
            access_token: '',
            userType: fileData['ownertype']
        };
        var DT = $('#Tasks').DataTable();
        var editor = new $.fn.dataTable.Editor({
            ajax: function (method, url, d, callback, err) {
                var params = {
                    access_token: '<?php echo Session::instance()->id();?>',
                    bundle_id: <?php echo $Id; ?>,
                    files: [{
                        account_id: fileData['accountid'],
                        task_id: fileData['taskid'],
                        bucket: $('#DTE_Field_bucket_id').val(),
                        comment: $('#DTE_Field_comment').val(),
                        id: fileData['fileid'],
                        is_viewed: 1,
                        approval_status: 1
                    }]
                };
                if (d.action === 'edit') {
                    $('.DTE_Form_Buttons:visible button').prop('disabled', true);
                    $.ajax({
                        type: 'PUT',
                        url: "<?php echo APP_URL ?>restapi/bundle/update_files_statuses?" + $.param(params),
                        dataType: "json",
                        success: function (json) {
                            $('.DTE_Processing_Indicator:visible').hide();
                            if (json.success) {
                                editor.close();
                                initFilesTable();
                                DT.ajax.reload(null, false);
                                editor.close();
                            } else {
                                $('.DTE_Form_Buttons:visible button').prop('disabled', false);
                                editor.field('bucket_id').error(json.message)
                            }
                        },
                        error: function (xhr, error, thrown) {
                            err(xhr, error, thrown);
                        }
                    });
                }
            },
            fields: [
                {
                    label: "Bucket",
                    name: "bucket_id",
                    type: "select",
                    className: 'block'
                },
                {
                    label: "Comment:",
                    name: "comment",
                    type: "textarea",
                    'className': 'block'
                }
            ]
        });
        editor.on('submitComplete', editorErrorHandler);
        editor.on('onOpen', function () {
            var $select = $('#DTE_Field_bucket_id').select2({width: '100%'});
            var $request = $.ajax({
                url: "<?=APP_URL ?>restapi/bundle/bucket?" +
                "access_token=<?=Session::instance()->id();?>" +
                "&userType=" + fileData['ownertype']
            });

            $request.then(function (data) {
                for (var d = 0; d < data.data.length; d++) {
                    var item = data.data[d];
                    var option = new Option(item.name, item.id, true, true);
                    $select.append(option);
                }
                $select.trigger('change');
            });
        });
        editor
            .title('Approve file')
            .buttons([{
                label: "Approve", fn: function () {
                    $(this).submit();
                }
            }, {
                label: 'Cancel', fn: function () {
                    this.close();
                }
            }]).edit();

    }

    function taskFileDecline(element) {
        var filesTable = $('#Tasks').dataTable();
        var DT = $('#Tasks').DataTable();
        var fileData = $(element).data();
        var filesTableRow = filesTable.fnGetData($('tr#' + fileData['taskid']));
        var index;
        for (var i = 0; i < filesTableRow.files.length; i++) {
            if (fileData['fileid'] == filesTableRow.files[i].DT_RowID) {
                index = i;
            }
        }
        if (typeof index !== 'undefined') {
            filesTableRow.files[index].approval_state = '2';
            filesTableRow.files[index].is_new = '0';
        }

        var editor = new $.fn.dataTable.Editor({
            ajax: function (method, url, d, callback, err) {
                var params = {
                    access_token: '<?php echo Session::instance()->id();?>',
                    bundle_id: <?php echo $Id; ?>,
                    files: [{
                        account_id: fileData['accountid'],
                        task_id: fileData['taskid'],
                        id: fileData['fileid'],
                        is_viewed: 1,
                        approval_status: 2,
                        comment: d.data.keyless.comment
                    }]
                };
                if (d.action === 'edit') {
                    $.ajax({
                        type: 'PUT',
                        url: "<?php echo APP_URL ?>restapi/bundle/update_files_statuses?" + $.param(params),
                        dataType: "json",
                        success: function () {
                            editor.close();
                            DT.ajax.reload(null, false);
                            initFilesTable();
                        },
                        error: function (xhr, error, thrown) {
                            err(xhr, error, thrown);
                        }
                    });
                }
            },
            fields: [
                {
                    label: "Comment:",
                    name: "comment",
                    type: "textarea",
                    'className': 'block'
                }
            ]
        });
        editor.on('submitComplete', editorErrorHandler);
        editor.title('Decline file')
            .buttons([
                {
                    label: "Decline file",
                    fn: function () {
                        $(this).submit();
                    }
                },
                {
                    label: 'Cancel',
                    fn: function () {
                        this.close();
                    }
                }
            ]).edit();
    }

    function fileDecline(element) {
        var fileData = $(element).data();
        var editor = new $.fn.dataTable.Editor({
            ajax: function (method, url, d, callback, err) {
                var params = {
                    access_token: '<?php echo Session::instance()->id();?>',
                    bundle_id: <?php echo $Id; ?>,
                    files: [{
                        account_id: fileData['accountid'],
                        task_id: fileData['taskid'],
                        id: fileData['fileid'],
                        is_viewed: 1,
                        approval_status: 2,
                        comment: d.data.keyless.comment
                    }]
                };
                if (d.action === 'edit') {
                    $.ajax({
                        type: 'PUT',
                        url: "<?php echo APP_URL ?>restapi/bundle/update_files_statuses?" + $.param(params),
                        dataType: "json",
                        success: function () {
                            initFilesTable();
                            editor.close();
                            window.location.reload();
                        },
                        error: function (xhr, error, thrown) {
                            err(xhr, error, thrown);
                        }
                    });
                }
            },
            fields: [
                {
                    label: "Comment:",
                    name: "comment",
                    type: "textarea",
                    'className': 'block'
                }
            ]
        });
        editor.on('submitComplete', editorErrorHandler);
        editor.title('Decline file')
            .buttons([{
                label: "Decline file", fn: function () {
                    $(this).submit();
                }
            }, {
                label: 'Cancel', fn: function () {
                    this.close();
                }
            }]).edit();
    }

    function taskFileOpen(element) {
        var filesTable = $('#Tasks').dataTable();
        var DT = $('#Tasks').DataTable();
        var fileData = $(element).data();
        var filesTableRow = filesTable.fnGetData($('tr#' + fileData['taskid']));
        var index;
        for (var i = 0; i < filesTableRow.files.length; i++) {
            if (fileData['fileid'] == filesTableRow.files[i].DT_RowID) {
                index = i;
            }
        }
        if (typeof index !== 'undefined') {
            filesTableRow.files[index].is_new = '0';
        }

        var params = {
            access_token: '<?php echo Session::instance()->id();?>',
            bundle_id: <?php echo $Id; ?>,
            files: [{
                account_id: fileData['accountid'],
                task_id: fileData['taskid'],
                id: fileData['fileid'],
                is_viewed: 1
            }]
        };

        $.ajax({
            type: 'PUT',
            url: "<?php echo APP_URL ?>restapi/bundle/update_files_statuses?" + $.param(params),
            dataType: "json",
            success: function () {
                filesTable.fnUpdate(filesTableRow, $('tr#' + fileData['taskid']));
                var tr = $('tr#' + fileData['taskid']).closest('tr');
                var row = DT.row(tr);
                row.child(formatTasks(row.data().files)).show();
                var url =
                    "<?php echo APP_URL ?>components/mozilla/pdf.js/web/viewer.html?file=" +
                    encodeURIComponent("<?php echo APP_URL ?>partner/dashboard/bundlefile_download?link=" + fileData['link']);
                // var url = '<?php echo APP_URL ?>modules/pdf.js/web/viewer.html';
                window.open(url, 'PDF Viewer', 'directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,' +
                    'scrollbars=yes,resizable=yes,width=600,height=550');
            },
            error: function () {
                console.error('Cannot update file status');
            }
        });
    }

    function fileOpen(element) {
        var fileData = $(element).data();

        var params = {
            access_token: '<?php echo Session::instance()->id();?>',
            bundle_id: <?php echo $Id; ?>,
            files: [{
                account_id: fileData['accountid'],
                task_id: fileData['taskid'],
                id: fileData['fileid'],
                is_viewed: 1
            }]
        };

        $.ajax({
            type: 'PUT',
            url: "<?php echo APP_URL ?>restapi/bundle/update_files_statuses?" + $.param(params),
            dataType: "json",
            success: function (json) {
                var url =
                    "<?php echo APP_URL ?>components/mozilla/pdf.js/web/viewer.html?file=" +
                    encodeURIComponent("<?php echo APP_URL ?>partner/dashboard/bundlefile_download?link=" + fileData['link']);
                // var url = '<?php echo APP_URL ?>modules/pdf.js/web/viewer.html';
                window.open(url, 'PDF Viewer', 'directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,' +
                    'scrollbars=yes,resizable=yes,width=600,height=550');
                initFilesTable();
            },
            error: function (xhr, error, thrown) {
                console.error('Cannot update file status');
            },
            complete: function () {

            }
        });
    }

    function initFilesTable() {
        var $elem = $('#Files');
        $.ajax({
            type: 'GET',
            url: "<?php echo APP_URL ?>partner/ajax/bundle_files_queue?Id=<?php echo $Id?>",
            dataType: "json",
            success: function (files) {
                $elem.empty().append(formatFiles(files.data));
            },
            error: function (xhr, error, thrown) {
                err(xhr, error, thrown);
            }
        });
    }

    /* TASKS */
    function initTasksTable() {
        var $elem = $('#Tasks');
        var taskseditor = new $.fn.dataTable.Editor({
            ajax: "<?php echo APP_URL ?>partner/ajax/bundle_tasks_cdu?Id=<?php echo $Id?>",
            table: "#Tasks",
            fields: [
                {
                    label: "Task:",
                    name: "task"
                },
                {
                    label: "# of Documents:",
                    name: "req_files",
                    type: "select",
                    options: [
                        {label: "1", value: "1"},
                        {label: "2", value: "2"},
                        {label: "3", value: "3"},
                        {label: "4", value: "4"},
                        {label: "5", value: "5"},
                        {label: "6", value: "6"},
                        {label: "7", value: "7"},
                        {label: "8", value: "8"},
                        {label: "9", value: "9"},
                        {label: "10", value: "10"}
                    ]
                },
                {
                    label: "Group:",
                    name: "group",
                    type: "select",
                    options: [
                        {label: "Identity", value: "Identity"},
                        {label: "Assets", value: "Assets"},
                        {label: "Liability", value: "Liability"},
                        {label: "Insurance", value: "Insurance"},
                        {label: "Taxes", value: "Taxes"},
                        {label: "Income", value: "Income"},
                        {label: "Special Documents", value: "Special Documents"}
                    ]
                },
                {
                    label: "Create for:",
                    name: "owner_type",
                    type: "select",
                    options: [
                        {label: "Borrower", value: "Borrower"},
                        <?php if ($has_cbws): ?>
                        {label: "Co-borrower", value: "Co-borrower"},
                        {label: "Both", value: "Both"}
                        <?php endif; ?>
                    ]
                },
                {
                    label: "Created for:",
                    name: "owner_type_ro",
                    type : "select",
                    options: <?php echo json_encode(($bor_cobor)); ?>
                }
            ]
        });
        taskseditor.on('submitComplete', editorErrorHandler);
        taskseditor.on('onInitEdit', function () {
            taskseditor.hide('owner_type');
            taskseditor.show('owner_type_ro');
        });
        taskseditor.on('onInitCreate', function () {
            taskseditor.show('owner_type');
            taskseditor.hide('owner_type_ro');
        });

        var taskRowData;
        var taskUploaderParams;
        var taskUploadEditor =
            new $.fn.dataTable.Editor({
                ajax: "<?=APP_URL?>restapi/account/add_files_and_link?" +
                $.param({access_token: '<?=Session::instance()->id();?>'}),
                table: "#Tasks",
                fields: [
                    {
                        type: "upload",
                        label: "Document",
                        name: "document_file",
                        ajaxData: function (d) {
                            d.append('override_files', 0);
                            if (taskRowData.hasOwnProperty('DT_RowId')) d.append('task_id', taskRowData['DT_RowId']);
                            if (taskRowData.hasOwnProperty('task_owner_id'))  d.append('owner_id', taskRowData['task_owner_id']);
                            $('form').trigger("reset");
                            initFilesTable();
                        },
                        display: function (d) {
                            taskstable.ajax.reload(null, false);
                            taskUploadEditor.close();
                            initFilesTable();
                        }
                    }
                ]
            });

        var taskUploadApproveEditor =
            new $.fn.dataTable.Editor({
                ajax: "<?=APP_URL?>restapi/account/add_files_and_link?" +
                $.param({access_token: '<?=Session::instance()->id();?>'}),
                table: "#Tasks",
                fields: [
                    {
                        type: "upload",
                        label: "Document",
                        name: "document_file",
                        ajaxData: function (d) {
                            d.append('override_files', 0);
                            if (taskRowData.hasOwnProperty('DT_RowId')) d.append('task_id', taskRowData['DT_RowId']);
                            if (taskRowData.hasOwnProperty('task_owner_id'))  d.append('owner_id', taskRowData['task_owner_id']);
                            $('form').trigger("reset");
                            initFilesTable();
                        },
                        display: function (d) {
                            var account_id = taskstable.file('files', d).account_id;
                            taskUploadApproveEditor.field('bucket_id').enable();
                            taskUploaderParams = {
                                access_token: '<?php echo Session::instance()->id();?>',
                                bundle_id: <?php echo $Id; ?>,
                                files: [{
                                    account_id: account_id,
                                    task_id: taskRowData.DT_RowId,
                                    id: d,
                                    bucket: $('#DTE_Field_bucket_id').val(),
                                    comment: $('#DTE_Field_comment').val(),
                                    is_viewed: 1,
                                    approval_status: 1
                                }]
                            };
                            $('.DTE_Field_Name_document_file').hide();
                            $('.DTE_Form_Buttons button:eq(1)').prop('disabled', false);
                            initFilesTable();
                            return "File Uploaded";
                        }
                    },
                    {
                        label: "Bucket",
                        name: "bucket_id",
                        type: "select",
                        className: 'block'
                    },
                    {
                        label: "Comment:",
                        name: "comment",
                        type: "textarea",
                        'className': 'block'
                    }
                ]
            });

        taskUploadApproveEditor.on('onOpen', function () {
            //taskstable.button(1).disable();
            taskUploadApproveEditor.field('bucket_id').disable();
            $('.DTE_Field_Name_document_file').show();
            $('.DTE_Form_Buttons button:eq(0)').prop('disabled', false);
            $('.DTE_Form_Buttons button:eq(1)').prop('disabled', true);
            var $select = $('#DTE_Field_bucket_id').select2({width: '100%'});
            $.ajax({
                url: "<?=APP_URL ?>restapi/bundle/bucket?" +
                "access_token=<?=Session::instance()->id();?>" +
                "&userType=" + ((taskRowData.owner_type == 1) ? 'borrower' : 'co-borrower')
            }).then(function (data) {
                for (var d = 0; d < data.data.length; d++) {
                    var item = data.data[d];
                    var option = new Option(item.name, item.id, true, true);
                    $select.append(option);
                }
                $select.trigger('change');
            });
        });

        var taskstable = $elem.DataTable({
            processing: true,
            serverSide: false,
            lengthMenu: [10, 25, 50, 100],
            dom: 'Blrtip',
            ajax: {
                "url": "<?php echo APP_URL ?>partner/ajax/bundle_tasks_queue?Id=<?php echo $Id?>",
                "type": "POST"
            },
            select: {
                style: 'os',
                selector: 'td:not(:last-child)' // no row selection on last column
            },
            rowCallback: function (row, data) {
                // $('td:eq(0)', row).html('<span class="badge" data-toggle="tooltip" title="Number of Documents">' + data.files.length + '</span>');

                // Set the checked state of the checkbox in the table
                $('input.taskseditor-agent', row).prop('checked', data.agent == "1");
            },
            columns: [
                {
                    className: 'details-control',
                    orderable: false,
                    defaultContent: ''
                },
                {
                    data: function (data) {
                        return data['group'] + ' — ' + data['task'];
                    }
                },
                {
                    data: "req_files"
                },
                {
                    data: function (data) {
                        var success_files = 0;
                        data.files.forEach(function (file) {
                            if (file.approval_state == '1' && file.sync_status == '1') success_files++;
                        });
                        return (success_files >= parseInt(data.req_files))
                            ? '<i class="fa fa-check-circle-o text-success"></i> ' + data.files.length
                            : '<i style="opacity: .3" class="fa fa-circle-o text-muted"></i> ' + data.files.length;
                    }
                },
                {
                    data: function (data) {
                        var success_files = 0;
                        data.files.forEach(function (file) {
                            if (file.approval_state == '1' && file.sync_status == '1') success_files++;
                        });
                        return (success_files >= parseInt(data.req_files)) ? 'Done' : 'Incomplete';
                    }
                },
                {
                    data: function (data) {
                        return data['first_name'] + ' ' + data['last_name'];
                    }
                }
            ],
            buttons: [
                'excelHtml5',
                'print',
                {extend: "create", editor: taskseditor},
                {extend: "edit", editor: taskseditor},
                {extend: "remove", editor: taskseditor},
                {
                    text: 'Download files',
                    action: function (e, dt, node, config) {
                        var rows = $('tr.selected');
                        var index = dt.row(rows)[0][0];
                        var data = dt.data()[index];
                        var bundle_id = <?php echo $Id?>;
                        var url = "<?php echo APP_URL ?>partner/ajax/download_bundle_files?bundle_id=<?php echo $Id?>";
                        window.open(url, '_blank');
                    }
                },
                {
                    extend: "selectedSingle",
                    text: 'Upload',
                    action: function (e, dt, node, config) {
                        taskUploadEditor
                            .title('Upload')
                            .buttons([
                                {
                                    label: 'Close',
                                    fn: function () {
                                        this.close();
                                    }
                                }
                            ]).create();
                    }
                },
                {
                    extend: "selectedSingle",
                    text: 'Upload & Approve',
                    action: function (e, dt, node, config) {
                        taskUploadApproveEditor
                            .title('Upload & Approve')
                            .buttons([
                                {
                                    label: 'Close',
                                    fn: function () {
                                        this.close();
                                    }
                                },
                                {
                                    label: 'Approve',
                                    disabled: true,
                                    fn: function () {
                                        $('.DTE_Processing_Indicator').show();
                                        $('.DTE_Form_Buttons button').prop('disabled', true);
                                        taskUploaderParams.files[0].bucket = $('#DTE_Field_bucket_id').val();
                                        taskUploaderParams.files[0].comment = $('#DTE_Field_comment').val();
                                        $.ajax({
                                            type: 'PUT',
                                            url: "<?php echo APP_URL ?>restapi/bundle/update_files_statuses?" + $.param(taskUploaderParams),
                                            dataType: "json",
                                            success: function () {
                                                $('.DTE_Processing_Indicator').hide();
                                                taskstable.ajax.reload(null, false);
                                                initFilesTable();
                                                taskUploadApproveEditor.close();
                                            },
                                            error: function (xhr, error, thrown) {
                                                $('.DTE_Processing_Indicator').hide();
                                                return false;
                                                // error(xhr, error, thrown);
                                            }
                                        });
                                    }
                                }
                            ]).create();
                    }
                },
                {
                    text: 'Refresh',
                    action: function ( e, dt, node, config ) {
                        taskstable.ajax.reload(null, false);
                    }
                }
            ]
        });

        taskstable.on('click', 'tbody tr', function () {
            taskRowData = taskstable.row(this).data();
        });

        $elem.on('change', 'input.taskseditor-agent', function () {
            taskseditor
                .edit($(this).closest('tr'), false)
                .set('agent', $(this).prop('checked') ? "1" : "0")
                .submit();
        });

        // Add event listener for opening and closing details
        $('tbody', $elem).on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = taskstable.row(tr);
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(formatTasks(row.data().files)).show();
                tr.addClass('shown');
            }
        });

        addSearchFields(taskstable, $elem);
    }

    /* CHATS */
    function initChats() {
        if (typeof ChatTimer != 'undefined') {
            clearInterval(ChatTimer);
        }

        // move to body
        $('#Chat').appendTo(document.body);
        $('#Chat .chat__toggler-message, #Chat .chat__toggler-button').on('click', function () {
            $('#Chat').toggleClass('open');
        });

        ChatTimer = setInterval(getChatMessages, 5000);
        getChatMessages();

        $('#Chat form').on('submit', function (event) {
            event.preventDefault();
            var message = this.message.value;
            var chatPutURL = "<?php echo APP_URL ?>partner/ajax/bundle_chat_cdu?Id=<?php echo $Id?>";
            this.reset();
            var data = {
                action: 'create',
                data: [{'comment': message}]
            };
            $.ajax({
                url: chatPutURL,
                method: "POST",
                data: data,
                success: function (data) {
                    if (data) {
                        return getChatMessages();
                    }
                }
            });
        })
    }

    function getChatMessages() {
        var chatGetURL = "<?php echo APP_URL ?>partner/ajax/bundle_chat_queue?Id=<?php echo $Id?>";
        $.ajax({
            url: chatGetURL,
            method: "GET",
            dataType: "json",
            success: function (data) {
                return renderChats(data);
            }
        });
    }

    function renderChats(data) {
        var messages = data.data;
        var messagesWrapper = $("#Chat .chat__massages");
        var totalHeight = 0;
        if (data.recordsTotal && JSON.stringify(messages) != JSON.stringify(ChatMessages)) {
            messagesWrapper.empty();
            ChatMessages = messages;
            $.each(messages, function (index, message) {
                var m = $('<li class="left clearfix"><div class="chat-body clearfix"><div class="header">' +
                    '<strong>' + message.person + '</strong><small class="pull-right text-muted">' +
                    moment.utc(message.date).fromNow() +
                    '</small></div><p>' + message.comment + '</p></div></li>');
                m.prependTo(messagesWrapper);
                totalHeight += m.height();
            });
            $(messagesWrapper).scrollTop(totalHeight + 1000);
        }
    }

    function addSearchFields(table, $elem, ignoredFields) {
        // Setup - add a text input to each footer cell
        $('tfoot th', $elem).each(function (_, el) {
            if ($.inArray($(el).text(), ignoredFields) < 0) {
                $(this).html('<input type="text" placeholder="Search" />');
            } else {
                $(this).html('<b></b>');
            }
        });

        // Apply the search
        table.columns().every(function () {
            var that = this;
            $('input', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that
                        .search(this.value)
                        .draw();
                }
            });
        });
    }

    function editorErrorHandler(event, resp) {
        if (typeof resp.responseJSON != 'undefined') resp = resp.responseJSON;
        if (typeof resp != 'undefined' && typeof resp.status != 'undefined') {
            if (resp.status === 'error') {
                error = '';
                if (typeof resp.code != 'undefined') {
                    error += 'Error #' + resp.code;
                }
                if (typeof resp.message != 'undefined') {
                    error += ' ' + resp.message;
                }
                showError(error);
            }
        }
        else {
            showError('Unknown error');
        }
    }

    function initShareTable() {
        var $elem = $('#Sharing');
        $.ajax({
            type: 'GET',
            url: "<?php echo APP_URL ?>partner/ajax/bundle_sharing_queue?Id=<?php echo $Id?>",
            dataType: "json",
            success: function (persons) {
                $elem.empty();
                $.each(persons.data, function (index, person) {
                    btnOptions =
                        'type="button"' +
                        'class="btn btn-default btn-file btn-xs" ' +
                        'data-person_id="' + person.DT_RowId + '"' +
                        'data-email="' + person.email + '"' +
                        'data-uname="' + person.person + '"' +
                        'data-pid="' + person.DT_RowId + '"' +
                        'data-status="' + person.status + '"' +
                        'data-is_email_valid="' + person.is_email_valid + '"' +
                        '';
                    var status = (person.status === 'invited') ? (person.invitationCounter == 0 ? "<button " + btnOptions + "onClick='sendInvitation(this)' >Send Invitation</button>" : "<button " + btnOptions + " onClick='sendInvitation(this)' >Resend Invitation</button>") : "<b>Active</b>";
                    var card = $('<div class="col-sm-3">' +
                        '<div class="panel panel-default userlist">\n' +
                        '<div class="panel-body text-center">' +
                        '<div class="userprofile">' +
                        ((person.avatar) ?
                            '<div class="userpic"><img src="' + person.avatar + '" class="userpicimg"></div>' :
                            '<div class="userpic"><i class="fa fa-user"></i></div>') +
                            '<div class="user-name"><i class="fa"></i>' + person.person + '</div>'+
                            '<h6 class="status" >' + status + '</h6><p></p>' +
                            '<h6 class="user-email">' + person.email + '</h6><p></p>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>');
                    $elem.append(card);
                });

            },
            error: function (xhr, error, thrown) {

            }
        });
    }

    function sendInvitation(element) {
        var borrowerNotification = new $.fn.dataTable.Editor({
            ajax: {
                method: "POST",
                url: "<?php echo APP_URL ?>restapi/user/invite_user?" +
                $.param({access_token: '<?=Session::instance()->id();?>'}),
                data: function (d, e) {
                    return $.extend({}, d, {
                        role: <?=Helper_LcConst::USER_ROLE_USER?>,
                        current_lender_position_id: <?=Session::instance()->get('position_id')?>
                    });
                }
            }
        });

        var borrowerNewValidData = new $.fn.dataTable.Editor({
            ajax: {
                method: "POST",
                url: "<?php echo APP_URL ?>restapi/user/change_borrower_email?" +
                $.param({access_token: '<?=Session::instance()->id();?>'}),
                data: function (d) {
                    return $.extend({}, d, {
                        current_lender_position_id: <?=Session::instance()->get('position_id')?>
                    });
                }
            },
            fields: [
                {label: 'New Email', name: "new_email", type: 'text', attr: {required: 'required'}}
            ]
        });

        var rowData = $(element).data();
        if (rowData.is_email_valid === 1 && rowData.status === 'invited') {
            borrowerNotification
                .title(rowData.uname + ' has not set up a loan yet')
                .message('Would you like to send invitation to ' + rowData.email + '?')
                .on('preSubmit', function (e, o) {
                    o.name = rowData.uname;
                    o.email = rowData.email;
                })
                .buttons([{
                    label: "Send invitation & Continue", fn: function () {
                        this.submit(function () {
                            alert('Invitation has been sent successfully');
                        });
                    }
                }, {
                    label: 'Cancel', fn: function () {
                        this.close()
                    }
                }])
                .edit(this);
        } else if (rowData.is_email_valid === 0 && rowData.status === 'invited') {
            var newEmail = '';
            var newName = '';
            var newData;
            borrowerNewValidData
                .title('Not valid profile email detected')
                .message('You have to update borrower profile email <b>' + rowData.email + '</b> to send invitation.')
                .on('preSubmit', function (e, o) {
                    newData = o.data["[object Window]"];
                    newEmail = newData.new_email;
                    newName = rowData.uname;
                    o.new_email = newEmail;
                    o.borrower_id = rowData.pid;
                    o.current_email = rowData.email;
                })
                .on('submitSuccess', function () {
                    $.ajax({
                        url: "<?php echo APP_URL ?>restapi/user/invite_user?" +
                        $.param({access_token: '<?=Session::instance()->id();?>'}),
                        method: "post",
                        data: {
                            role: <?=Helper_LcConst::USER_ROLE_USER?>,
                            current_lender_position_id: <?=Session::instance()->get('position_id')?>,
                            name: newName,
                            email: newEmail
                        },
                        success: function () {
                            alert('Invitation has been sent successfully');
                            initShareTable();
                        }
                    });
                })
                .buttons([
                    {
                        label: "Send invitation & Continue",
                        fn: function () {
                            this.submit();
                        }
                    },
                    {
                        label: 'Cancel',
                        fn: function () {
                            this.close()
                        }
                    }
                ])
                .edit(this);
        }
    }

    /* AGENT FILES */
    function initAgentFilesTable() {
        var $elem = $('#Agentfiles');
        var afileseditor = new $.fn.dataTable.Editor({
            "ajax": "<?php echo APP_URL ?>partner/ajax/bundle_agentfiles_upload?Id=<?php echo $Id?>",
            "table": "#Agentfiles",
            "fields": [
                {
                    label: "Document:",
                    name: "image",
                    type: "upload",
                    display: function (file_id) {
                        return '<b>Click create to upload: "' + afilestable.file('files', file_id).filename + '"</b>';
                    }
                },
                {
                    label: "Ownership",
                    name: "ownership",
                    type: "select",
                    options: [
                        {
                            label: "Both",
                            value: "<?php echo Helper_LcConst::$LENDER_FILES_ACCESS_TYPE[Helper_LcConst::LENDER_FILES_ACCESS_TYPE_EVERYBODY]; ?>"
                        },
                        {
                            label: "Owner (Lender)",
                            value: "<?php echo Helper_LcConst::$LENDER_FILES_ACCESS_TYPE[Helper_LcConst::LENDER_FILES_ACCESS_TYPE_OWNER]; ?>"
                        },
                        {
                            label: "Borrower",
                            value: "<?php echo Helper_LcConst::$LENDER_FILES_ACCESS_TYPE[Helper_LcConst::LENDER_FILES_ACCESS_TYPE_BORROWER]; ?>"
                        },
                        {
                            label: "Co-borrowers",
                            value: "<?php echo Helper_LcConst::$LENDER_FILES_ACCESS_TYPE[Helper_LcConst::LENDER_FILES_ACCESS_TYPE_CO_BORROWER]; ?>"
                        }
                    ]
                }
            ]
        });
        var afilestable = $elem.DataTable({
            processing: true,
            serverSide: false,
            lengthMenu: [10, 25, 50],
            select: true,
            dom: 'Blrtip',
            ajax: {
                "url": "<?php echo APP_URL ?>partner/ajax/bundle_agentfiles_queue?Id=<?php echo $Id?>",
                "type": "POST"
            },
            rowCallback: function (row, data, index) {
                $('td:eq(0)', row).html('<a href="/partner/dashboard/bundleafile_download?link=' + data.link + '">' + data.file + '</a>');
            },

            columns: [
                {"data": "file"},
                {"data": "owner"}
            ],
            buttons: [
                'excelHtml5',
                'print',
                {extend: "create", editor: afileseditor},
                {extend: "remove", editor: afileseditor},
                {
                    text: 'Refresh',
                    action: function ( e, dt, node, config ) {
                        afilestable.ajax.reload(null, false);
                    }
                }
            ]

        });

        addSearchFields(afilestable, $elem);
    }


    jQuery(document).ready(function ($) {
        // Setup - tabs  controls
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
        });

        initChats();

        var tables = {
            tasks: initTasksTable,
            agent: initAgentFilesTable,
            files: initFilesTable,
            chats: initChats,
            share: initShareTable
        };
        window.initTable = function (tableName) {
            if (typeof tables[tableName] != 'undefined') {
                tables[tableName]();
                delete tables[tableName];
            }
        };
        // Init default table
        initTable('files');
    });
</script>
