// 
// 
//@author : Hydil Aicard Sokeing for GreenSoft-Team
//@creationDate : 22/09/2017


$(function() {

    // var APP_ROOT;

    (window.location.pathname.match(/(.*?)web/i)) ? (APP_ROOT = window.location.pathname.match(/(.*?)web/i)[1]) : (APP_ROOT = "");
    (APP_ROOT) ? (APP_ROOT += "web") : (APP_ROOT);

    var URL_ROOT = APP_ROOT;
    if(window.location.pathname.indexOf("app_dev.php") !== -1){
        URL_ROOT = APP_ROOT + "/app_dev.php";
    }else if(window.location.pathname.indexOf("app.php") !== -1){
        URL_ROOT = APP_ROOT + "/app.php";
    }

    head.load([
        APP_ROOT + "/js/main/helpers/FeedbackHelper.js",
    ], function() {
        /**
         * constructor
         */
        function Sequence() {
            this.feedbackHelper = new FeedbackHelper();
        }

        var sequence = new Sequence();

        /**
         * allow to initialize the view
         * @return {void} nothing
         */
        Sequence.prototype.initializeView = function() {
            console.log("Here stand Sequence");
            // $('.panel .tools .fa').trigger('click');
        }

        /**
         * allow to set a whole bunch of listeners
         */
        Sequence.prototype.setListeners = function() {
            this.setCreateFormModalShow();
            this.setEditFormModalShow();
        }

        Sequence.prototype.setCreateFormModalShow = function() {
            $('body').on('click','a[name="add-sequence"]', function() {
                console.log("Je sui déja la");

                $('#sequence-form').load(URL_ROOT+'/sequence/new', function(){
                    $('.modal-add-sequence').modal('show');
                });
            })
        }

        Sequence.prototype.setEditFormModalShow = function() {
            $('body').on('click','a[name="sequence-edit"]', function() {
                console.log("Je sui déja la");
                val = $(this).data('sequence');

                $('#sequence-editform').load(URL_ROOT+'/sequence/'+val+'/edit', function(){
                    $('.modal-edit-sequence').modal('show');
                });
            })
        }

        //this should be at the end
        sequence.initializeView();
        sequence.setListeners();
        // sequence.postActions();
    });
});
