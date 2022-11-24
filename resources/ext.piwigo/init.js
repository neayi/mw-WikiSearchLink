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
                var search = piwigoDiv.data( "search" );
                var site = piwigoDiv.data( "site" );
                self.getImages(tags, tags_multiple, category, search, count, piwigoDiv, site);
            });

		},

        getImages: function (tags, tags_multiple, category, search, count, piwigoDiv, site) {

            var piwigoRootURL = '';
            if (site !== undefined)
                piwigoRootURL = site;
            else
                piwigoRootURL = mw.config.get('Piwigo').wgPiwigoURL;

            var piwigoURL = piwigoRootURL + '/?/';

            if (search !== undefined)
            {
                piwigoURL = piwigoRootURL + '/qsearch.php?q=' + search;
            }
            else if (tags !== undefined)
            {
                piwigoURL = piwigoURL + 'tags/' + tags;
            }
            else if (tags_multiple !== undefined)
            {
                piwigoURL = piwigoURL + 'tags/' + tags_multiple.split(',').at(0); // Target the first tag only
            }
            else if (category !== undefined)
            {
                piwigoURL = piwigoURL + 'category/' + category;
            }

            // Add a button with the URL to the gallery:
            $(`<div class="text-right">
                    <a  type="button" class="btn btn-primary btn-sm text-white" href="${piwigoURL}" target="_blank">Voir la galerie</a>
                </div><br style="clear:both"/>`).insertAfter(piwigoDiv);

            var api = new mw.Api();
            api.post( {
                'action': 'piwigosearch',
                'tags': tags,
                'tags_multiple': tags_multiple,
                'category': category,
                'search': search,
                'count': count,
                'site': site
            } )
            .done( function ( data ) {
                console.log("piwigosearch:");
                console.log(data);

                var rowDiv = $('<div>').attr('class', 'row');
                data.piwigosearch.result.result.images.forEach(item => {

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
            } );
        }
	};

	module.exports = piwigo_controller;

	mw.PiwigoController = piwigo_controller;
}() );


(function () {
	$(document)
		.ready(function () {
            mw.loader.using('mediawiki.api', function() {
                // Call to the function that uses mw.Api
                mw.PiwigoController.init();
              } );
		});
}());

