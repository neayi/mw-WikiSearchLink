/**
 * @class mw.PiwigoController
 * @singleton
 */
 ( function () {
	var piwigo_controller;

	piwigo_controller = {
		init: function () {
			var self = this;

            console.log("Piwigo Hello world");

            $('.showPiwigo').each(function () {
                var piwigoDiv = $(this);

                var tags = piwigoDiv.data( "tags" );

                self.getImages(tags, piwigoDiv);


            });

		},

        getImages: function (tags, piwigoDiv) {

            //            var piwigoURL = mw.config.get('NeayiInteractions').wgInsightsRootURL;
            var piwigoURL = 'https://galerie.dev.tripleperformance.fr/ws.php';

            $.ajax({
                    url: piwigoURL + "?format=json&method=pwg.tags.getImages&tag_id=" + tags,
                    dataType: 'json',
                    method: "GET",
            }).done(function (data) {

                    console.log(data);

                    var rowDiv = $('<div>').attr('class', 'row');
                    data.result.images.forEach(item => {

                        var large = item.element_url;
                        var thumb = item.derivatives.small.url;

                        rowDiv.append($(`<div class="col-sm-12 col-md-4">
                            <a class="lightbox" href="${large}">
                                <img src="${thumb}" alt="Bridge">
                            </a>
                        </div>`));
                    });

                    piwigoDiv.append(rowDiv);

                    baguetteBox.run('.showPiwigo');
            });

        }
	};

	module.exports = piwigo_controller;

	mw.PiwigoController = piwigo_controller;
}() );


(function () {
	$(document)
		.ready(function () {
            mw.PiwigoController.init();
		});
}());
