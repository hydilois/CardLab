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
            // $('.panel .tools .fa').trigger('click');
            this.percentage();
        }


        /**
         * allow to set a whole bunch of listeners
         */
        Base.prototype.setListeners = function() {
        }

        Base.prototype.percentage  = function() {
            console.log("Yesyyyyyyyyyyyyyyyyyyyy");

            Morris.Donut({
                element: 'graph-donut',
                data: [
                    {value: 15, label: 'Sequence 1', formatted: 'at least 15%' },
                    {value: 15, label: 'Séquence 2', formatted: 'approx. 15%' },
                    {value: 15, label: 'Séquence 3', formatted: 'approx. 15%' },
                    {value: 15, label: 'Séquence 4', formatted: 'at most 15%' },
                    {value: 20, label: 'Séquence 5', formatted: 'at most 20%' },
                    {value: 20, label: 'Séquence 6', formatted: 'at most 20%' },
                ],
                backgroundColor: false,
                labelColor: '#fff',
                colors: [
                    '#4acacb','#6a8bc0','#5ab6df','#fe1076','#fe8676','#fe8676'
                ],
                formatter: function (x, data) { return data.formatted; }
            });
        }

        //this should be at the end
        base.initializeView();
        base.setListeners();
    });
});
