/**
 * @class mw.PiwigoController
 * @singleton
 */
 ( function () {
	var piwigo_controller;

	piwigo_controller = {
		init: function () {
			var self = this;

            $('.showPiwigo').each(function () {
                var piwigoDiv = $(this);

                // NB : Tags will take over category (album)
                var tags = piwigoDiv.data( "tags" );
                var tags_multiple = piwigoDiv.data( "tags_multiple" );
                var category = piwigoDiv.data( "category" );
                var count = piwigoDiv.data( "count" );

                self.getImages(tags, tags_multiple, category, count, piwigoDiv);
            });

		},

        getImages: function (tags, tags_multiple, category, count, piwigoDiv) {

            var piwigoRootURL = mw.config.get('Piwigo').wgPiwigoURL;
            var piwigoWSURL = piwigoRootURL + "/ws.php?format=json";
            var piwigoURL = piwigoRootURL + '/?/';

            if (tags !== undefined)
            {
                piwigoWSURL = piwigoWSURL + "&method=pwg.tags.getImages&tag_id=" + tags;
                piwigoURL = piwigoURL + 'tags/' + tags;
            }
            else if (tags_multiple !== undefined)
            {
                piwigoWSURL = piwigoWSURL + "&method=pwg.tags.getImages&tag_id[]=" + tags_multiple.split(',').join("&tag_id[]=");
                piwigoURL = piwigoURL + 'tags/' + tags_multiple.split(',').at(0); // Target the first tag only
            }
            else if (category !== undefined)
            {
                piwigoWSURL = piwigoWSURL + "&method=pwg.categories.getImages&cat_id=" + category;
                piwigoURL = piwigoURL + 'category/' + category;
            }

            if (count > 0)
                piwigoWSURL = piwigoWSURL + "&per_page=" + count;

            // Add a button with the URL to the gallery:
            $(`<div class="text-right">
                    <a  type="button" class="btn btn-primary btn-sm text-white" href="${piwigoURL}" target="_blank">Voir la galerie</a>
                </div><br style="clear:both"/>`).insertAfter(piwigoDiv);

            $.ajax({
                    url: piwigoWSURL,
                    dataType: 'json',
                    method: "GET",
            }).done(function (data) {

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
