!function ($) {
    $(document).ready(function() {

        $.hovernewsSection.start()
        
    })

    $.hovernewsSection = {

        start: function( opts ){
        
            var theContainers = $('.hovernews-container')
            var initToggle = function() {
                var theItems = $('.hovernews-item')
                theItems.each( function(){
                
                    var theItem = $(this)
                    theItem.unbind('mouseenter')
                    theItem.unbind('mouseleave')

                    theItem.on('mouseenter', function(){
                        var popOver = $(this).find('.hovernews-toggle')
                        ,   backDrop = $(this).find('.hovernews-backdrop')
                        if(popOver.is(':hidden')) {
                            popOver.slideDown(400)
                            backDrop.css('opacity', '0.6')
                        }
                    })

                    theItem.on('mouseleave', function(){
                        var popOver = $(this).find('.hovernews-toggle')
                        ,   backDrop = $(this).find('.hovernews-backdrop')
                        if(popOver.is(':visible')) {
                            popOver.slideUp(400)
                            backDrop.css('opacity', '1')
                        }
                    })

                })                

            }

            initToggle()
            theContainers.each( function(){
                
                var hovernewsContainer = $(this)
                ,   loadStyle = hovernewsContainer.data('loading')
                ,   theListID = hovernewsContainer.data('id')
                ,   theCol = hovernewsContainer.data('col')
                ,   containerWidth = hovernewsContainer.width()
                ,   pinsUrl = hovernewsContainer.data('url')
                
                var mod = containerWidth % (12 / theCol)
                if(mod != 0) {
                    while(mod != 0) {
                        containerWidth++;
                        mod = containerWidth % (12 / theCol);
                    }
                    hovernewsContainer.width(containerWidth);
                } 
                hovernewsContainer.imagesLoaded(function(){
                    if(hovernewsContainer.data('format') == 'grid') {
                            var width = hovernewsContainer.find('.hovernews-backdrop').width()
                            hovernewsContainer
                                .find('.hovernews-overlay')
                                .width(width);
                            hovernewsContainer
                                .masonry({
                                    itemSelector : '.hovernews-item'
                                    , layoutMode: 'fitRows'
                                });
                        } else {
                            hovernewsContainer
                                .masonry({
                                    itemSelector : '.hovernews-item'
                                    , layoutMode: 'masonry'
                                });
                        }
                    });

            

                if( loadStyle == 'infinite' ){
                    
                    hovernewsContainer.infinitescroll({
                        navSelector : '.iscroll',
                        nextSelector : '.iscroll a',
                        itemSelector : '#hovernews-'+theListID+' .hovernews-item',
                        loadingText : 'Loading...',
                        loadingImg :  pinsUrl+'/load.gif',
                        donetext : 'No more pages to load.',
                        debug : true,
                        loading: {
                            finishedMsg: 'No more pages to load.'
                        }
                        
                    }, function( arrayOfNewElems ) {
                        hovernewsContainer.masonry( 'appended', $( arrayOfNewElems ) );
                        // hovernewsContainer.masonry('appended', $( arrayOfNewElems ) );
                        // hovernewsContainer.layout();
                            if(hovernewsContainer.data('format') == 'grid') {
                                var width = hovernewsContainer.find('.hovernews-backdrop').width();
                                hovernewsContainer.find('.hovernews-overlay').width(width);
                            }
                            initToggle();
                    })

                } else {                    
                    var theLoadLink = hovernewsContainer.parent().find('.fetchpost a')

                    theLoadLink.on('click', function(e) {
                        
                        e.preventDefault();
                        
                        theLoadLink
                            .addClass('loading')
                            .html('<i class="icon icon-refresh icon-spin spin-fast"></i> &nbsp;  Loading...');
                            
                        $.ajax({
                            type: "GET",
                            url: theLoadLink.attr('href'),
                            dataType: "html",
                            success: function(out) {

                                var newContainer = $( out ).find( sprintf('[data-id="%s"]', theListID ) )
                                ,   result = newContainer.find( '.hovernews-item' )
                                ,   nextlink = newContainer.parent().find( '.fetchpost a' ).attr('href')
                                
                                
                                hovernewsContainer.append( result );
                                
                                hovernewsContainer.imagesLoaded(function(){
                                    
                                   hovernewsContainer.masonry( 'appended', result )
                                        
                                });

                                if(hovernewsContainer.data('format') == 'grid') {
                                    var width = hovernewsContainer.find('.hovernews-backdrop').width();
                                    hovernewsContainer.find('.hovernews-overlay').width(width);
                                }
                                initToggle();
                                theLoadLink
                                    .removeClass('loading')
                                    .text('Load More Posts');



                                if (nextlink != undefined)
                                    theLoadLink.attr('href', nextlink);
                                else
                                    theLoadLink.parent().remove();
                                
                            }
                        });
                    });
                }
                
            })

            
                
            

        }


    }
    
}(jQuery);