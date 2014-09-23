//============================================================================
//
// scripts.js
// ---------
//
// Jose Andres 04/2014
//
//============================================================================

/**
 * Shared Functions
 */
var shared = function() {
    return {
        /**
         * Check if the value
         * is empty
         *
         * @param {string} val
         * @returns {string}
         */
        isEmpty: function(val) {
            if (val === "" || val === null) {
                return '["EMPTY"]';
            } else {
                return JSON.stringify(val);
            }
        },
        /**
         * Creates a temporal
         * form to submit by
         * POST
         *
         * @param {string} url
         * @param {array} data
         * @param {string} method
         * @param {string} target
         * @returns {html}
         */
        form: function(url, data, method, target) {
            if (method == null) method = 'POST';
            if (data == null) data = {};
            if (target == null) target = '_self';

            var form = $('<form>').attr({
                method: method,
                action: url,
                target: target
             }).css({
                display: 'none'
             });

            var addData = function(name, data) {
                if ($.isArray(data)) {
                    for (var i = 0; i < data.length; i++) {
                        var value = data[i];
                        addData(name + '[]', value);
                    }
                } else if (typeof data === 'object') {
                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            addData(name + '[' + key + ']', data[key]);
                        }
                    }
                } else if (data != null) {
                    form.append($('<input>').attr({
                        type: 'hidden',
                        name: String(name),
                        value: String(data)
                    }));
                }
            };

            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    addData(key, data[key]);
                }
            }

            return form.appendTo('body');
        }
    };
}();

/**
 * Notifier Functions
 */
var notifier = function() {
    /**
     * [PRIVATE]
     * Creates a generic
     * noty instance
     *
     * @param {string} type
     * @param {string} text
     * @returns {noty}
     */
    function generic(type, text) {
        noty({
            layout: 'topRight',
            type: type,
            text: text,
            dismissQueue: true,
            timeout: false,
            maxVisible: 5
        });
    }

    return {
        /**
         * Upload Success
         *
         * @param {array} data
         * @param {string} redirectPath
         * @returns {noty}
         */
        sucessFileUpload: function(data, redirectPath) {
            noty({
                layout: 'topRight',
                text: 'The system finished processing '+data.count+' file(s). Do you want to see them now?',
                type: 'information',
                buttons:
                [{addClass: 'btn btn-primary', text: 'Yes', onClick: function($noty) {
                    $noty.close();
                    $(location).attr('href', redirectPath);
                }},{addClass: 'btn btn-danger', text: 'No', onClick: function($noty) {
                    $noty.close();
                }}],
                maxVisible: 1
            });
        },
        /**
         * Configuration Success
         *
         * @returns {noty}
         */
        configurationSuccess: function() {
            generic('success', 'Configurations saved successfully');
        },
        /**
         * Success
         *
         * @returns {noty}
         */
        deleteSuccess: function() {
            generic('success', 'Deleted successfully');
        },
        /**
         * Error
         *
         * @returns {noty}
         */
        error: function() {
            generic('error', 'Something went wrong');
        }
    };
}();

/**
 * Generic Sample Functions
 */
var sample = function() {
    var path,
        redirectPath,
        datePickerOptions = {
            pickTime: false,
            defaultDate: '',
            format: "MM/YYYY",
            viewMode: "months",
            minViewMode: "months"
        };

    /**
     * [PRIVATE]
     * Set Status Check Path
     *
     * @param {string} val
     */
    function setPath(val) {
        path = val;
    }

    /**
     * [PRIVATE]
     * Set Redirect Path
     *
     * @param {string} val
     */
    function setRedirectPath(val) {
        redirectPath = val;
    }

    /**
     * [PRIVATE]
     * Check Upload Status
     *
     * @returns {ajax}
     */
    function checkUploadStatus() {
        $.ajax({
            url: path,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.count > 0) {
                    notifier.successFileUpload(data, redirectPath);
                }
            }
        });
    }

    return {
        /**
         * Custom Settings Handle
         *
         * @returns {on.click}
         */
        customSettingsHandle: function() {
            $('#custom-settings-handle a').on('click', function(e){
                e.preventDefault();
                var customSettings = $('#custom-settings');
                var type = $(this).data('type');
                if (type == 'hide') {
                    customSettings.slideUp();
                    $(this).data('type', 'show').html('Show');
                } else {
                    customSettings.slideDown();
                    $(this).data('type', 'hide').html('Hide');
                }
            });
        },
        /**
         * Select2 on Custom Settings
         *
         * @returns {select2}
         */
        customSettings: function() {
            $(".select2").select2();
        },
        /**
         * Set Default Start Date
         *
         * @param {string} defaultDate
         * @param {string} maxDate
         * @returns {datePicker}
         */
        datePickerStart: function(defaultDate, maxDate) {
            var options = $.extend(datePickerOptions, {
                defaultDate: defaultDate,
                maxDate: maxDate
            });
            $('#period-start')
                .datetimepicker(options)
                .on("dp.change", function(e) {
                    $('#period-end').data("DateTimePicker").setMinDate(e.date);
                });
        },
        /**
         * Set Default End Date
         *
         * @param {string} defaultDate
         * @param {string} minDate
         * @param {string} maxDate
         * @returns {datePicker}
         */
        datePickerEnd: function(defaultDate, minDate, maxDate) {
            var options = $.extend(datePickerOptions, {
                defaultDate: defaultDate,
                minDate: minDate,
                maxDate: maxDate
            });
            $('#period-end')
                .datetimepicker(options)
                .on("dp.change",function(e) {
                    $('#period-start').data("DateTimePicker").setMaxDate(e.date);
                });
        },
        /**
         * Default Layout Scripts
         *
         * @param {string} path
         * @param {string} redirectPath
         */
        layout: function(path, redirectPath) {
            setPath(path);
            setRedirectPath(redirectPath);
            checkUploadStatus();

            // Check status every minute
            setInterval(function() {
                checkUploadStatus();
                return false;
            }, 1000 * 60 * 1);
        }
    };
}();

/**
 * Charts Functions
 */
var charts = function() {
    return {
        /**
         * Stacked Bar Chart Init
         *
         * @param {array} labels
         * @param {array} data
         * @param {integer} angle
         * @returns {jqPlot}
         */
        stacked: function(labels, data, series, angle) {
            $.jqplot('stacked-chart', data, {
                animate: true,
                animateReplot: true,
                stackSeries: true,
                seriesDefaults: {
                    renderer: $.jqplot.BarRenderer,
                    rendererOptions: {
                        barMargin: 50,
                        animation: {
                            speed: 2000
                        }
                    }
                },
                series: series,
                seriesColors: [
                    '#5DA5DA', '#FAA43A', '#60BD68', '#F17CB0',
                    '#B2912F', '#B276B2', '#DECF3F', '#F15854', '#4D4D4D'
                ],
                grid: {
                    borderWidth: 0,
                    shadow: false,
                    background: 'transparent'
                },
                axes: {
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer,
                        tickOptions: {
                            angle: angle,
                            fontSize: '10pt'
                        },
                        ticks: labels
                    },
                    yaxis: {
                        tickOptions: {
                            formatString: "$%'d"
                        },
                        min: 0
                    }
                },
                highlighter: {
                    show: true,
                    showLabel: true,
                    tooltipAxes: 'y',
                    sizeAdjust: 7.5,
                    tooltipLocation: 'e'
                }
            });
        }
    };
}();

/**
 * DataTable Functions
 */
var dtable = function() {
    /**
     * Default Settings
     */
    var defaults = {
        'pageLength': 100,
        'scrollX': true,
        'dom': 'Tlfrtip',
        'createdRow': function(row, data, dataIndex) {
            $(row).children('td').css('overflow', 'hidden');
            $(row).children('td').css('white-space', 'nowrap');
            $(row).children('td').css('text-overflow', 'ellipsis');
        },
        'language': {
            'search': '_INPUT_',
            'sZeroRecords': 'No records found'
        }
    };

    /**
     * [PRIVATE]
     * Column Type
     *
     * @returns {dataTableExt.oSort}
     */
    function columnType() {
        $.fn.dataTableExt.oSort['currency-asc'] = function(a,b) {
            var x = $(a).text() === "-" ? 0 : $(a).text().replace( /,/g, "" );
            var y = $(b).text() === "-" ? 0 : $(b).text().replace( /,/g, "" );

            x = x.substring( 1 );
            y = y.substring( 1 );

            x = parseFloat( x );
            y = parseFloat( y );
            return x - y;
        };

        $.fn.dataTableExt.oSort['currency-desc'] = function(a,b) {
            var x = $(a).text() === "-" ? 0 : $(a).text().replace( /,/g, "" );
            var y = $(b).text() === "-" ? 0 : $(b).text().replace( /,/g, "" );

            x = x.substring( 1 );
            y = y.substring( 1 );

            x = parseFloat( x );
            y = parseFloat( y );
            return y - x;
        };
    }

    /**
     * [PRIVATE]
     * DataTable Bootstrap
     *
     * @param {string} selector
     */
    function bootstrap(selector) {
        $(selector).each(function(){
            var datatable = $(this);

            datatable.closest('.dataTables_wrapper')
                .find('div[id$=_filter] input')
                .attr('placeholder', 'Search')
                .addClass('form-control input-small')
                .css('width', '250px');

            datatable.closest('.dataTables_wrapper')
                .find('div[id$=_filter] a')
                .html('<i class="icon-remove-circle icon-large"></i>')
                .css('margin-left', '5px');

            datatable.closest('.dataTables_wrapper')
                .find('div[id$=_length] select')
                .addClass('form-control input-small')
                .css('width', '75px');

            datatable.closest('.dataTables_wrapper')
                .find('div[id$=_info]')
                .css('margin-top', '18px');
        });
    }

    /**
     * [PRIVATE]
     * Creates a generic
     * DataTable instance
     *
     * @param {string} selector
     * @param {array} options
     * @returns {DataTable}
     */
    function generic(selector, options) {
        columnType();
        $(selector).DataTable(options);
        bootstrap(selector);
    }

    /**
     * [PRIVATE]
     * Replace All
     *
     * @param {string} find
     * @param {string} replace
     * @param {string} str
     * @returns {string}
     */
    function replaceAll(find, replace, str) {
        return str.replace(new RegExp(find, 'g'), replace);
    }

    return {
        /**
         * Graph
         *
         * @param {array} columns
         * @param {string} path
         * @param {string} exportPath
         * @param {string} loadingPath
         * @returns {DataTable}
         */
        graph: function(columns, path, exportPath, loadingPath) {
            var options = $.extend(defaults, {
                'ajax': path,
                'scrollY': 300,
                'order': [ 1, 'asc' ],
                'columns': columns,
                'language': {
                    'loadingRecords': '<img src="'+loadingPath+'" width="60" height="37" />',
                    'processing': '<img src="'+loadingPath+'" width="60" height="37" />'
                },
                'tableTools': {
                    'aButtons': [{
                        'sExtends': 'ajax',
                        'sFieldBoundary': '"',
                        'sFieldSeperator': ',',
                        'sNewLine': '],[',
                        'sButtonText': 'Export',
                        'fnClick': function( nButton, oConfig ) {
                            var data = this.fnGetTableData(oConfig);
                            shared.form(exportPath, { 'data': '[['+data+']]' }, 'POST', '_blank').submit();
                        },
                        'oSelectorOpts': {
                            page: 'all',
                            filter: 'applied'
                        }
                    }]
                }
            });
            generic('#datatable', options);
        },
        /**
         * Data
         *
         * @param {array} dataOptions
         * @param {array} columns
         * @param {string} path
         * @param {string} exportPath
         * @param {string} loadingPath
         * @returns {DataTable}
         */
        data: function(dataOptions, columns, path, exportPath, loadingPath) {
            var options = $.extend(defaults, {
                'ajax': {
                    'url': path,
                    'type': 'POST',
                    'data': {
                        'sourceId': dataOptions.sourceId,
                        'periodId': dataOptions.periodId,
                        'topicId': dataOptions.topicId
                    }
                },
                'serverSide': true,
                'deferRender': true,
                'processing': true,
                'scrollY': 400,
                'order': [],
                'columns': columns,
                'language': {
                    'loadingRecords': '<img src="'+loadingPath+'" width="60" height="37" />',
                    'processing': '<img src="'+loadingPath+'" width="60" height="37" />'
                },
                'tableTools': {
                    'aButtons': [{
                        'sExtends': 'ajax',
                        'sButtonText': 'Export',
                        'fnClick': function() {
                            var data = this.s.dt.oApi._fnAjaxParameters( this.s.dt );
                            shared.form(exportPath, {
                                'sourceId': dataOptions.sourceId,
                                'periodId': dataOptions.periodId,
                                'topicId': dataOptions.topicId,
                                'order': JSON.stringify(data.order),
                                'search': JSON.stringify(data.search)
                            }, 'POST', '_blank').submit();
                        }
                    }]
                }
            });
            generic('#datatable', options);
        },
        /**
         * Report
         *
         * @param {array} dataOptions
         * @param {array} columns
         * @param {string} path
         * @param {string} exportPath
         * @param {string} loadingPath
         * @returns {DataTable}
         */
        report: function(dataOptions, columns, path, exportPath, loadingPath) {
            var options = $.extend(defaults, {
                'ajax': {
                    'url': path,
                    'type': 'POST',
                    'data': {
                        'sourceId': dataOptions.sourceId,
                        'franchise': dataOptions.franchise,
                        'topicName': dataOptions.topicName,
                        'topicId': dataOptions.topicId,
                        'periods': dataOptions.periods
                    }
                },
                'serverSide': true,
                'deferRender': true,
                'processing': true,
                'scrollY': 300,
                'order': [],
                'columns': columns,
                'language': {
                    'loadingRecords': '<img src="'+loadingPath+'" width="60" height="37" />',
                    'processing': '<img src="'+loadingPath+'" width="60" height="37" />'
                },
                'tableTools': {
                    'aButtons': [{
                        'sExtends': 'ajax',
                        'sButtonText': 'Export',
                        'fnClick': function() {
                            var data = this.s.dt.oApi._fnAjaxParameters( this.s.dt );
                            shared.form(exportPath, {
                                'sourceId': dataOptions.sourceId,
                                'franchise': dataOptions.franchise,
                                'topicName': dataOptions.topicName,
                                'topicId': dataOptions.topicId,
                                'periods': dataOptions.periods,
                                'order': JSON.stringify(data.order),
                                'search': JSON.stringify(data.search)
                            }, 'POST', '_blank').submit();
                        }
                    }]
                }
            });
            generic('#datatable', options);
        }
    };
}();

/**
 * Graph Functions
 */
var sampleGraph = function() {
    return {
        /**
         * Submit Updated Settings
         *
         * @param {string} path
         */
        updateSettings: function(path) {
            $('#update-settings').on('click', function(){

                // Franchise
                var f = shared.isEmpty($('#franchises').val());

                // Topic Id
                var tId = shared.isEmpty($('#topic-ids').val());

                // Topic Name
                var tName = shared.isEmpty($('#topic-names').val());

                // Period Interval
                var m1 = $('#period-start').data("DateTimePicker").getDate().month() + 1;
                var y1 = $('#period-start').data("DateTimePicker").getDate().year();
                var m2 = $('#period-end').data("DateTimePicker").getDate().month() + 1;
                var y2 = $('#period-end').data("DateTimePicker").getDate().year();

                window.location = path+'?f='+f+'&t='+tId+'&n='+tName+'&m1='+m1+'&y1='+y1+'&m2='+m2+'&y2='+y2;
            });
        }
    };
}();

/**
 * Data Functions
 */
var sampleData = function() {
    return {
        /* No scripts at the moment */
    };
}();

/**
 * Report Functions
 */
var sampleReport = function() {
    return {
        /**
         * Submit Updated Settings
         *
         * @param {string} path
         */
        updateSettings: function(path) {
            $('#update-settings').on('click', function(){

                // Source
                var s = shared.isEmpty($('#sources').val());

                // Franchise
                var f = shared.isEmpty($('#franchises').val());

                // Topic Id
                var tId = shared.isEmpty($('#topic-ids').val());

                // Topic Name
                var tName = shared.isEmpty($('#topic-names').val());

                // Period Interval
                var m1 = $('#period-start').data("DateTimePicker").getDate().month() + 1;
                var y1 = $('#period-start').data("DateTimePicker").getDate().year();
                var m2 = $('#period-end').data("DateTimePicker").getDate().month() + 1;
                var y2 = $('#period-end').data("DateTimePicker").getDate().year();

                window.location = path+'?s='+s+'&f='+f+'&t='+tId+'&n='+tName+'&m1='+m1+'&y1='+y1+'&m2='+m2+'&y2='+y2;
            });
        }
    };
}();

/**
 * Upload Functions
 */
var sampleUpload = function() {
    return {
        /**
         * Tooltips Init
         *
         * @returns {tooltip}
         */
        tooltips: function() {
            $('#multiple-admin').tooltip();
            $('#sources-admin').tooltip();
        },
        /**
         * File Upload Init
         *
         * @param {string} url
         * @returns {fileupload, fileupload.events}
         */
        csv: function(url) {
            $('#fileupload').fileupload({
                url: url,
                dataType: 'json',
                autoUpload: false,
                maxChunkSize: 10000000, // 10 MB
                formData: {
                    type:      $('#avro_csv_import_type').val(),
                    delimiter: $('#avro_csv_import_delimiter').val(),
                    source:    $('#avro_csv_import_source').val(),
                    month:     $('#avro_csv_import_period_month').val(),
                    year:      $('#avro_csv_import_period_year').val()
                },
                acceptFileTypes: /(\.|\/)(csv|xls|xlsx)$/i,
                disableImageResize: /Android(?!.*Chrome)|Opera/
                    .test(window.navigator.userAgent)
            }).on('fileuploadadd', function (e, data) {
                data.formData = {
                    type:      $('#avro_csv_import_type').val(),
                    delimiter: $('#avro_csv_import_delimiter').val(),
                    source:    $('#avro_csv_import_source').val(),
                    month:     $('#avro_csv_import_period_month').val(),
                    year:      $('#avro_csv_import_period_year').val()
                };
                data.context = $('#files .row.hidden').clone().appendTo('#files');
                data.context.removeClass('hidden');
                data.context.find('.btn-upload').on('click', function(e){
                    e.preventDefault();
                    var $this = $(this), data = $this.data();
                    $this.off('click');
                    data.submit();
                });
                $.each(data.files, function (index, file) {
                    data.context.find('.file-name').html(file.name);
                    if (!index) {
                        data.context.find('.btn-upload').data(data);
                    }
                });
                $('#progress .progress-bar').css('width','0');
            }).on('fileuploadprogressall', function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }).on('fileuploaddone', function (e, data) {
                $.each(data.result.files, function (index, file) {
                    var link = data.context.find('.btn-upload');
                    var noti = data.context.find('.notification');
                    link.removeClass('btn-default');
                    if (file.error) {
                        if (file.error == 'error.whitelist') {
                            file.error = ' [File type not allowed]';
                        }
                        var error = $('<span class="text-danger"/>').text(file.error);
                        noti.removeClass('hidden').html(error);
                        link.addClass('btn-danger').html('<span class="glyphicon glyphicon glyphicon-remove"></span> Error');
                    } else {
                        link.addClass('btn-success').html('<span class="glyphicon glyphicon glyphicon-ok"></span> Success');
                    }
                });
            }).on('fileuploadfail', function (e, data) {
                $.each(data.files, function (index, file) {
                    var error = $('<span class="text-danger"/>').text(' [File upload failed]');
                    data.context.find('.notification').html(error);
                    data.context.find('.btn-upload').addClass('disabled');
                });
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        }
    };
}();

/**
 * Multiple Functions
 */
var sampleMultiple = function() {
    return {
        /**
         * Save Configurations
         * @param {string} path
         * @returns {on.click}
         */
        save: function(path) {
            $('.btn').on('click', function(){
                var form = $('#splits');
                var multipleId = form.find('#form_multipleId').val();
                var topicId = form.find('#form_topicId').val();
                var percentage = form.find('#form_percentage').val();

                $.get(path+'?m='+multipleId+'&t='+topicId+'&pg='+percentage, function(data){
                    notifier.configurationSuccess();
                }).fail(function(){
                    notifier.error();
                });
            });
        }
    };
}();

/**
 * Sources Functions
 */
var sampleSources = function() {
    return {
        /**
         * Tooltips Init
         *
         * @returns {tooltip}
         */
        tooltips: function() {
            $('.btn-tooltip').tooltip();
        },
        /**
         * Configuration Buttons Init
         *
         * @returns {on.click}
         */
        configureButtons: function() {
            $('.btn-configure').on('click', function(){
                var modal = $('#configure');
                modal.find('#configure-sourceName').html($(this).data('name'));
                modal.find('#form_topicId').val($(this).data('topic'));
                modal.find('#form_price').val($(this).data('price'));
                modal.find('#form_sourceId').val($(this).data('source'));
                modal.modal('show');
            });
        },
        /**
         * Configuration Modal: Yes Init
         *
         * @param {string} path
         * @returns {on.click}
         */
        configureYes: function(path) {
            $('#modal-configure-yes').on('click', function(){
                var form = $('#configure');
                var sourceId = form.find('#form_sourceId').val();
                var topicId = form.find('#form_topicId').val();
                var price = form.find('#form_price').val();

                $.get(path+'?s='+sourceId+'&t='+topicId+'&p='+price, function(data){
                    notifier.configurationSuccess();
                    $('#configure').modal('hide');
                    $('*[data-source='+data.sourceId+']')
                        .data('topic', data.topicId)
                        .data('price', data.price);
                }).fail(function(){
                    notifier.configurationFailed();
                });
            });
        },
        /**
         * Active/Disable Buttons Init
         *
         * @returns {on.click}
         */
        turnButtons: function() {
            $('.btn-turn').on('click', function(){
                var modal = $('#turn');
                modal.find('#turn-title').html($(this).data('title'));
                modal.find('#turn-option').html($(this).data('turn'));
                modal.find('#turn-sourceId').html($(this).data('sourceId'));
                modal.modal();
            });
        }
    };
}();

/**
 * Manager Functions
 */
var sampleManager = function() {
    return {
        /**
         * Tooltips Init
         *
         * @returns {tooltip}
         */
        tooltips: function() {
            $('.btn-tooltip').tooltip();
        },
        /**
         * Disable Button Init
         *
         * @returns {on.click}
         */
        disableButton: function() {
            $('.btn-disable').on('click', function(){
                var modal = $('#disable-modal');
                modal.find('#disable-name').html($(this).data('name'));
                modal.find('#disable-yes').data('id', $(this).data('id'));

                // Show Modal
                modal.modal();
            });
        },
        /**
         * Disable the submitted Id
         *
         * @param {string} path
         */
        disableEvent: function(path, loadingGif) {
            $('#disable-yes').on('click', function(){
                var id = $(this).data('id');
                $(this).html('<img src="'+loadingGif+'" width="20" height="20" alt="Yes" style="margin:0 2px"/>');
                $(this).unbind('click');
                $.get(path+'?id='+id, function(){
                    notifier.deleteSuccess();
                    $('#disable-modal').modal('hide');
                    location.reload();
                }).fail(function(){
                    notifier.error();
                });
            });
        },
        /**
         * Delete Button Init
         *
         * @returns {on.click}
         */
        deleteButton: function() {
            $('.btn-delete').on('click', function(){
                var modal = $('#delete-modal');
                modal.find('#delete-type').html($(this).data('type'));
                modal.find('#delete-source').html($(this).data('source'));
                modal.find('#delete-period').html($(this).data('period'));
                modal.find('#delete-yes').data('id', $(this).data('id'));

                // Show Modal
                modal.modal();
            });
        },
        /**
         * Delete the submitted Id
         *
         * @param {string} path
         */
        deleteEvent: function(path, loadingGif) {
            $('#delete-yes').on('click', function(){
                var id = $(this).data('id');
                $(this).html('<img src="'+loadingGif+'" width="20" height="20" alt="Yes" style="margin:0 2px"/>');
                $(this).unbind('click');
                $.get(path+'?id='+id, function(){
                    notifier.deleteSuccess();
                    $('#delete-modal').modal('hide');
                    location.reload();
                }).fail(function(){
                    notifier.error();
                });
            });
        }
    };
}();