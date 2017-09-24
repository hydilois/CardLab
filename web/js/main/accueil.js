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
        function Base() {
            this.feedbackHelper = new FeedbackHelper();
        }

        var base = new Base();

        /**
         * allow to initialize the view
         * @return {void} nothing
         */
        Base.prototype.initializeView = function() {
            console.log("Here stand the index file");
            $('.panel .tools .fa').trigger('click');
            this.percentage();
        }


        /**
         * allow to set a whole bunch of listeners
         */
        Base.prototype.setListeners = function() {
            this.setCreateFormModalShow();
            this.setEditFormModalShow();
        }

        Base.prototype.setCreateFormModalShow = function() {
            $('body').on('click','a[name="add-categorie"]', function() {
                console.log("Je sui déja la");

                $('#categorie-form').load(URL_ROOT+'/categorie/new', function(){
                    $('.modal-add-categorie').modal('show');
                });
            })
        }

        Base.prototype.percentage  = function() {
            Morris.Donut({
                element: 'graph-donut',
                data: [
                    {value: 50, label: 'New Visit', formatted: 'at least 70%' },
                    {value: 20, label: 'Unique Visits', formatted: 'approx. 15%' },
                    {value: 20, label: 'Bounce Rate', formatted: 'approx. 10%' },
                    {value: 10, label: 'Up Time', formatted: 'at most 99.99%' }
                ],
                backgroundColor: false,
                labelColor: '#fff',
                colors: [
                    '#4acacb','#6a8bc0','#5ab6df','#fe8676'
                ],
                formatter: function (x, data) { return data.formatted; }
            });
        }

        Base.prototype.setEditFormModalShow = function() {
            $('body').on('click','a[name="categorie-edit"]', function() {
                console.log("Je sui déja la");
                val = $(this).data('categorie');

                $('#categorie-editform').load(URL_ROOT+'/categorie/'+val+'/edit', function(){
                    $('.modal-edit-categorie').modal('show');
                });
            })
        }

        //this should be at the end
        base.initializeView();
        base.setListeners();
    });
});
