$(document).ready(function(){
    // Full featured editor
    $('.reply').each( function(index, element){
        $.fn.wysiwyg_ini(element);
    });

    // Raw editor
    var option = {
        element: $('#editor0').get(0),
        onkeypress: function( code, character, shiftKey, altKey, ctrlKey, metaKey ) {
            if( typeof console != 'undefined' )
                console.log( 'RAW: '+character+' key pressed' );
        },
        onselection: function( collapsed, rect, nodes, rightclick ) {
            if( typeof console != 'undefined' && rect )
                console.log( 'RAW: selection rect('+rect.left+','+rect.top+','+rect.width+','+rect.height+'), '+nodes.length+' nodes' );
        },
        onplaceholder: function( visible ) {
            if( typeof console != 'undefined' )
                console.log( 'RAW: placeholder ' + (visible ? 'visible' : 'hidden') );
        }
    };
});

$.fn.wysiwyg_ini = function (element) {
    $(element).wysiwyg({
        classes: 'some-more-classes',
        position: 'bottom',
        buttons: {
            bold: {
                title: 'Bold (Ctrl+B)',
                image: '<img src="/assets/img/icon-bold.png" alt="" />',
                hotkey: 'b'
            },
            italic: {
                title: 'Italic (Ctrl+I)',
                image: '<img src="/assets/img/icon-italic.png" alt="" />',
                hotkey: 'i'
            },
            underline: {
                title: 'Underline (Ctrl+U)',
                image: '<img src="/assets/img/icon-underline.png" alt="" />',
                hotkey: 'u'
            },
            fontsize: {
                title: 'Size',
                image: '<img src="/assets/img/icon-txt-size.png" alt="" />',
                popup: function( $popup, $button, $editor ) {
                    var list_fontsizes = {
                        // Name : Size
                        'Heading 1'  : 1,
                        'Heading 2'  : 2,
                        'Heading 3'  : 3,
                        'Heading 4'  : 4,
                        'Heading 5'  : 5,
                        'Heading 6'  : 6,
                        'Heading 7'  : 7
                    };
                    var $list = $('<div/>').addClass('wysiwyg-toolbar-list')
                    .attr('unselectable','on');
                    $.each( list_fontsizes, function( name, size ){
                        var $link = $('<a/>').attr('href','#')
                        .css( 'font-size', (8 + (size * 3)) + 'px' )
                        .html( name )
                        .click(function(event){
                            $(element).wysiwyg('fontsize',size);
                            $(element).wysiwyg('close-popup');
                            event.stopPropagation();
                            event.preventDefault();
                            return false;
                        });
                        $list.append( $link );
                    });
                    $popup.append( $list );
                }
            },
            forecolor: {
                title: 'Text color',
                image: '<img src="/assets/img/icon-color.png" alt="" />',
            },
            // Smiley-Plugin
            smilies: {
                title: 'Smilies',
                image: '<img src="/assets/img/icon-smile.png" alt="" />',
                popup: function( $popup, $button, $editor ) {
                    var list_smilies = [
                    '<img src="/assets/img/smiley/afraid.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/amorous.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/angel.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/angry.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/bored.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/cold.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/confused.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/cross.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/crying.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/devil.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/disappointed.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/dont-know.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/drool.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/embarrassed.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/excited.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/excruciating.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/eyeroll.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/happy.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/hot.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/hug-left.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/hug-right.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/hungry.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/invincible.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/kiss.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/lying.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/meeting.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/nerdy.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/neutral.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/party.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/pirate.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/pissed-off.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/question.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/sad.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/shame.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/shocked.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/shut-mouth.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/sick.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/silent.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/sleeping.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/sleepy.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/stressed.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/thinking.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/tongue.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/uhm-yeah.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/wink.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/working.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/bathing.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/beer.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/boy.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/camera.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/chilli.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/cigarette.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/cinema.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/coffee.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/girl.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/console.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/grumpy.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/in_love.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/internet.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/lamp.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/mobile.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/mrgreen.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/musical-note.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/music.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/phone.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/plate.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/restroom.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/rose.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/search.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/shopping.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/star.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/studying.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/suit.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/surfing.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/thunder.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/tv.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/typing.png" width="16" height="16" alt="" />',
                    '<img src="/assets/img/smiley/writing.png" width="16" height="16" alt="" />'
                    ];
                    var $smilies = $('<div/>').addClass('wysiwyg-toolbar-smilies')
                    .attr('unselectable','on');
                    $.each( list_smilies, function(index,smiley){
                        if( index != 0 )
                            $smilies.append(' ');
                        var $image = $(smiley).attr('unselectable','on');
                        // Append smiley
                        var imagehtml = ' '+$('<div/>').append($image.clone()).html()+' ';
                        $image
                        .css({
                            cursor: 'pointer'
                        })
                        .click(function(event){
                            $(element).wysiwyg('inserthtml',imagehtml);
                            // do not close popup
                            $(element).wysiwyg('close-popup');
                        })
                        .appendTo( $smilies );
                    });
                    $smilies.css({
                        maxWidth: parseInt($editor.width()*0.95)+'px'
                    });
                    $popup.append( $smilies );
                    // Smilies do not close on click, so force the popup-position to cover the toolbar
                    var $toolbar = $button.parents( '.wysiwyg-toolbar' );
                    if( ! $toolbar.length ) // selection toolbar?
                        return ;
                    var left = 0,
                    top = 0,
                    node = $toolbar.get(0);
                    while( node )
                    {
                        left += node.offsetLeft;
                        top += node.offsetTop;
                        node = node.offsetParent;
                    }
                    left += parseInt( ($toolbar.outerWidth() - $popup.outerWidth()) / 2 );
                    if( $toolbar.hasClass('wysiwyg-toolbar-top') )
                        top -= $popup.height() - parseInt($button.outerHeight() * 1/4);
                    else
                        top += parseInt($button.outerHeight() * 3/4);
                    $popup.css({
                        left: left + 'px',
                        top: top + 'px'
                    });
                    return false;
                }
            }
        },
        // Submit-Button
        submit: {
            title: 'Submit',
            image: '\uf00c'
        },
        // Other properties
        dropfileclick: 'Drop image or click',
        placeholderUrl: 'www.example.com',
        maxImageSize: [600,200]
    });
}