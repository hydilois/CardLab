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
        function Categorie() {
            this.feedbackHelper = new FeedbackHelper();
        }

        var categorie = new Categorie();

        /**
         * allow to initialize the view
         * @return {void} nothing
         */
        Categorie.prototype.initializeView = function() {
            console.log("Here stand Categorie");
        }


        /**
         * allow to set a whole bunch of listeners
         */
        Categorie.prototype.setListeners = function() {
            this.setCreateFormModalShow();
            this.setEditFormModalShow();
        }

        Categorie.prototype.setCreateFormModalShow = function() {
            $('body').on('click','a[name="add-categorie"]', function() {
                console.log("Je sui déja la");

                $('#categorie-form').load(URL_ROOT+'/categorie/new', function(){
                    $('.modal-add-categorie').modal('show');
                });
            })
        }

        Categorie.prototype.setEditFormModalShow = function() {
            $('body').on('click','a[name="categorie-edit"]', function() {
                console.log("Je sui déja la");
                val = $(this).data('categorie');

                $('#categorie-editform').load(URL_ROOT+'/categorie/'+val+'/edit', function(){
                    $('.modal-edit-categorie').modal('show');
                });
            })
        }

        //this should be at the end
        categorie.initializeView();
        categorie.setListeners();
    });
});
